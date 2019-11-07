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
 * The only UI: Import / Statistics page
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Shadowdata_Form_ShadowData extends CRM_Core_Form {

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(E::ts("Shadow-Data Overview"));
    // add stats
    $this->assign('stats', $this->getStats());

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Refresh'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    parent::buildQuickForm();
  }

  public function postProcess() {
    parent::postProcess();
  }


  /**
   * Get some stats on the current data
   */
  protected function getStats() {
    $stats = [];

    // add contact stats
    CRM_Shadowdata_Contact::addStatistics($stats);

    return $stats;
  }
}

