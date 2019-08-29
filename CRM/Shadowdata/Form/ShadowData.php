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

    // add templates
    $this->assign('contact_template_link', $this->getDataLink('templates/Upload/Contact.csv'));

    // add form elements
    $this->addElement(
        'file',
        'contact_import_file',
        E::ts('Import More Data'));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Import'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
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

  /**
   * Generate a download file link
   * @param $file string
   * @return string
   */
  protected function getDataLink($file) {
    $data = file_get_contents(E::path($file));
    return 'data:text/csv;filename=test.csv;base64,' . base64_encode($data);
  }
}

