<?php
/*-------------------------------------------------------+
| SYSTOPIA SHADOW DATA EXTENSION                         |
| Copyright (C) 2019 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Shadowdata_ExtensionUtil as E;

class CRM_Shadowdata_Contact {

  public static $table_name      = 'shadowdata_contact';
  public static $fields_metadata = ['id','contact_id','code','import_date','unlock_date','use_by','source'];
  public static $fields_contact  = ['contact_type','organization_name','household_name','first_name','last_name','formal_title','prefix_id','suffix_id','gender_id','birth_date','job_title'];
  public static $fields_address  = ['street_address','supplemental_address_1','supplemental_address_2','city','postal_code','country_id'];
  public static $fields_email    = ['email'];
  public static $fields_phone    = ['phone'];
  public static $file_header     = ['code','use_by','source','contact_type','organization_name','household_name','first_name','last_name','formal_title','prefix_id','suffix_id','gender_id','birth_date','job_title','email','phone','street_address','supplemental_address_1','supplemental_address_2','city','postal_code','country_id'];

  /**
   * Unlock/get the contact with the given code
   *
   * @param $code string code
   * @return int|null contact ID or NULL
   */
  public static function getContactID($code) {
    $code = substr($code, 0, 64);

    // query the table for an entry that has either been already booked (contact_id IS NOT NULL)
    //   or still valid for unlocking (use_by > NOW() OR use_by IS NULL)
    $table_name = self::$table_name;
    $record     = CRM_Core_DAO::executeQuery("
        SELECT id AS record_id, contact_id AS contact_id 
        FROM `{$table_name}` WHERE code = %1 
          AND (contact_id IS NOT NULL OR use_by > NOW() OR use_by IS NULL);", [1 => [$code, 'String']]);
    if (!$record->fetch()) {
      // there is no such record
      return NULL;
    }

    // now investigate the record
    if (empty($record->contact_id)) {
      // this is an unlock process
      return self::unlockContact($record->record_id);
    } else {
      // this contact has already been unlocked
      return $record->contact_id;
    }
  }

  /**
   * Unlock the contact in the given row
   * @param $record_id
   */
  public static function unlockContact($record_id) {
    $record_id = (int) $record_id;
    // first, get the full contact data
    $all_fields = array_merge(self::$fields_metadata, self::$fields_contact, self::$fields_address, self::$fields_email, self::$fields_phone);
    $record = CRM_Core_DAO::executeQuery("SELECT " . implode(',', $all_fields) . " FROM " . self::$table_name . " WHERE id = {$record_id}");
    if (!$record->fetch()) {
      throw new Exception("Shadow contact record {$record_id} could not be found");
    }

    $transaction = new CRM_Core_Transaction();
    try {
      // create contact
      $contact_data = self::getData(self::$fields_contact, $record);
      if (CRM_Shadowdata_Config::addSourceField()) {
        $contact_data['source'] = $record->source;
      }
      $contact = civicrm_api3('Contact', 'create', $contact_data);
      $contact_id = (int) $contact['id'];

      // create address
      $address_data = self::getData(self::$fields_address, $record);
      if (CRM_Shadowdata_Config::addressDataComplete($address_data)) {
        $address_data['contact_id'] = $contact_id;
        $address_data['location_type_id'] = CRM_Shadowdata_Config::getAddressLocationTypeID();
        civicrm_api3('Address', 'create', $address_data);
      }

      // create email
      $email_data = self::getData(self::$fields_email, $record);
      if (CRM_Shadowdata_Config::emailDataComplete($email_data)) {
        $email_data['contact_id'] = $contact_id;
        $email_data['location_type_id'] = CRM_Shadowdata_Config::getEmailLocationTypeID();
        civicrm_api3('Email', 'create', $email_data);
      }

      // create phone
      $phone_data = self::getData(self::$fields_phone, $record);
      if (CRM_Shadowdata_Config::phoneDataComplete($phone_data)) {
        $phone_data['contact_id'] = $contact_id;
        $phone_data['location_type_id'] = CRM_Shadowdata_Config::getPhoneLocationTypeID();
        civicrm_api3('Phone', 'create', $phone_data);
      }

      // if we get here, all is well
      $transaction->commit();
      $transaction = NULL;

      // update record (set contact ID and unlock timestamp)
      CRM_Core_DAO::executeQuery("UPDATE " . self::$table_name . " SET contact_id ={$contact_id}, unlock_date = NOW() WHERE id = {$record_id}");

    } catch (Exception $ex) {
      if ($transaction) {
        $transaction->rollback();
      }
      throw new Exception("Record [{$record_id}] could not be restored: " . $ex->getMessage());
    }

    return $contact_id;
  }

  /**
   * Import the given CSV file
   *
   * @param $file_name string file name
   * @return int|string returns int (number of records imported) or string in case of an error
   * @throws Exception
   */
  public static function import($file_name) {
    $import_count = 0;
    $data = fopen($file_name, 'r');
    $header = fgetcsv($data, 0, ';');
    if (count($header) != count(self::$file_header)) {
      return E::ts("Wrong number of columns, or possibly wrong separator. Use the template.");
    }
    if ($header != self::$file_header) {
      return E::ts("Unexpected headers. Use the template.");
    }
    while ($raw_row = fgetcsv($data, 0, ';')) {
      // convert row in to row_data
      $row = [];
      for ($i = 0; $i < count($header); $i++) {
        $row[$header[$i]] = $raw_row[$i];
      }

      if (self::importRow($row)) {
        $import_count += 1;
      }
    }
    return $import_count;
  }

  /**
   * Import the given CSV file
   *
   * @param $row array data
   * @return boolean
   * @throws Exception
   */
  protected static function importRow($row) {
    // check if code is free
    $table_name = self::$table_name;
    if (empty($row['code'])) {
      throw new Exception(E::ts("Missing code(s)"));
    }
    $code_exists = CRM_Core_DAO::singleValueQuery("SELECT id FROM {$table_name} WHERE code = %1;", [1 => [$row['code'], 'String']]);
    if ($code_exists) {
      throw new Exception(E::ts("Duplicate code '%1'", [1 => $row['code']]));
    }

    // build insert query
    $sql_params = [];
    $sql_vars   = [];
    foreach ($row as $name => $value) {
      $sql_params[] = [$value, 'String'];
      $sql_var = "%" . count($sql_vars);
      if ($name == 'country_id') {
        $sql_vars[]   = "(SELECT id FROM civicrm_country WHERE iso_code = {$sql_var})";
      } else {
        $sql_vars[] = $sql_var;
      }
    }
    // run the query
    CRM_Core_DAO::executeQuery("INSERT INTO {$table_name} (import_date, " . implode(',', array_keys($row)) . ") VALUES (NOW(), " . implode(',', $sql_vars) . ");", $sql_params);
    return TRUE;
  }

  /**
   * Get some statistics on contacts
   * @param $stats
   */
  public static function addStatistics(&$stats) {
    $table_name = self::$table_name;
    $query = "SELECT
        source                                                                      AS source,
        COUNT(id)                                                                   AS total,
        SUM(CASE WHEN contact_id IS NULL THEN 0 ELSE 1 END)                         AS unlocked,
        SUM(CASE WHEN unlock_date > NOW() AND contact_id IS NULL THEN 1 ELSE 0 END) AS outdated
    FROM {$table_name}";

    // run for all
    $general_result = CRM_Core_DAO::executeQuery($query);
    $general_result->fetch();
    $stats[E::ts("Total datasets")]          = (int) $general_result->total;
    $stats[E::ts("Total datasets unlocked")] = self::percentage($general_result->unlocked, $general_result->total);
    $stats[E::ts("Total datasets outdated")] = self::percentage($general_result->outdated, $general_result->total);

    // run by source
    $source_result = CRM_Core_DAO::executeQuery($query);
    while ($source_result->fetch()) {
      $source_name = $source_result->source;
      $stats[E::ts("'%1' datasets", [1 => $source_name])]          = (int) $source_result->total;
      $stats[E::ts("'%1' datasets unlocked", [1 => $source_name])] = self::percentage($source_result->unlocked, $source_result->total);
      $stats[E::ts("'%1' datasets outdated", [1 => $source_name])] = self::percentage($source_result->outdated, $source_result->total);
    }
  }

  /**
   * Render a nice percentage string
   *
   * @param $count float
   * @param $total float
   * @return string
   */
  protected static function percentage($count, $total) {
    if ($total == 0) {
      return "0 (0%)";
    } else {
      $count = (float) $count;
      $total = (float) $total;
      return sprintf("%d (%.2f%%)", $count, ($count / $total * 100.0));
    }
  }

  /**
   * Helper function to copy all
   * @param array        $fields
   * @param CRM_Core_DAO $record
   *
   * @return array extracted data
   */
  protected static function getData($fields, $record) {
    $data = [];
    foreach ($fields as $field) {
      if (isset($record->$field)) {
        $data[$field] = $record->$field;
      } else {
        $data[$field] = NULL;
      }
    }
    return $data;
  }
}


