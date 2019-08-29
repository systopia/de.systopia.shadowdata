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
   * Get some statistics on contacts
   * @param $stats
   */
  public static function addStatistics(&$stats) {
    $stats['TEST'] = "TEST";
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


