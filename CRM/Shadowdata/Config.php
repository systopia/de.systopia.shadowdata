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

class CRM_Shadowdata_Config {

  /**
   * Get the location type ID to be used
   *
   * @return integer location type to be used
   */
  public static function getLocationTypeID() {
    static $location_type_id = NULL;
    if ($location_type_id === NULL) {
      $location_type    = civicrm_api3('LocationType', 'getsingle', ['is_default' => 1]);
      $location_type_id = $location_type['id'];
    }
    return $location_type_id;
  }

  /**
   * Add source field to unlocked contact?
   *
   * @return boolean
   */
  public static function addSourceField() {
    return TRUE;
  }


  /**
   * Get the location type ID to be used for addresses
   *
   * @return integer location type to be used
   */
  public static function getAddressLocationTypeID() {
    return self::getLocationTypeID();
  }

  /**
   * Get the location type ID to be used for phones
   *
   * @return integer location type to be used
   */
  public static function getPhoneLocationTypeID() {
    return self::getLocationTypeID();
  }

  /**
   * Get the location type ID to be used for emails
   *
   * @return integer location type to be used
   */
  public static function getEmailLocationTypeID() {
    return self::getLocationTypeID();
  }

  /**
   * Is there enough address data to create an address?
   *
   * @param $data address data
   * @return boolean
   */
  public static function addressDataComplete($data) {
    return !empty($data['street_address']) && !empty($data['postal_code']) && !empty($data['city']);
  }

  /**
   * Is there enough email data to create an email?
   *
   * @param $data email data
   * @return boolean
   */
  public static function emailDataComplete($data) {
    return !empty($data['email']);
  }

  /**
   * Is there enough phone data to create an phone?
   *
   * @param $data phone data
   * @return boolean
   */
  public static function phoneDataComplete($data) {
    return !empty($data['phone']);
  }

}