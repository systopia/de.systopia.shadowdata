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

require_once 'shadowdata.civix.php';
use CRM_Shadowdata_ExtensionUtil as E;


/**
 * Implements merge integration
 *
 * A contact merge should *always* move all submission records to the new contact
 */
function shadowdata_civicrm_merge($type, &$data, $mainId = NULL, $otherId = NULL, $tables = NULL) {
  if ($type == 'sqls') {
    // if an unlocked contact is merged, attach the record to the remaining contact
    if (is_numeric($mainId) && is_numeric($otherId)) {
      $data[] = "UPDATE `shadowdata_contact` SET `contact_id` = {$mainId} WHERE `contact_id` = {$otherId};";
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function shadowdata_civicrm_config(&$config) {
  _shadowdata_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function shadowdata_civicrm_install() {
  _shadowdata_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function shadowdata_civicrm_enable() {
  _shadowdata_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function shadowdata_civicrm_navigationMenu(&$menu) {
  _shadowdata_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _shadowdata_civix_navigationMenu($menu);
} // */
