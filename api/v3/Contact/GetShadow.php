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

/**
 * Contact.get_shadow command
 */
function civicrm_api3_contact_get_shadow($params) {
  $contact_id = CRM_Shadowdata_Contact::getContactID($params['code']);
  if ($contact_id) {
    unset($params['code']);
    $params['id'] = $contact_id;
    if (empty($params['is_deleted'])) {
      $params['is_deleted'] = 0;
    }
    return civicrm_api3('Contact', 'getsingle', $params);
  } else {
    return civicrm_api3_create_error(E::ts("Contact not found"));
  }
}

/**
 * Contact.get_shadow metadata
 */
function _civicrm_api3_contact_get_shadow_spec(&$params) {
  $params['code'] = array(
      'name'         => 'code',
      'api.required' => 1,
      'type'         => CRM_Utils_Type::T_STRING,
      'title'        => E::ts("Code"),
      'description'  => E::ts("Provide the code to unlock or identify the contact"),
  );
}
