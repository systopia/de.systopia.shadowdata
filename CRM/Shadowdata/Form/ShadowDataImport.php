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
class CRM_Shadowdata_Form_ShadowDataImport extends CRM_Core_Form {

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(E::ts("Shadow-Data Importer"));

    // add templates
    $this->assign('contact_template_link', $this->getDataLink('templates/Upload/Contact.csv'));

    // add form elements
    $this->addElement(
        'file',
        'contact_import_file',
        E::ts('Import More Data'));

    // add form elements
    $this->addElement(
        'text',
        'shadowdata_db',
        E::ts('Database for shadowdata tables'));
    $this->addRule('shadowdata_db', E::ts("invalid characters"), 'regex', '/^[a-zA-Z0-9_]*$/');
    $this->setDefaults(['shadowdata_db', CRM_Shadowdata_Config::getShadowdataTableDB()]);

    $this->addButtons(array(
        array(
            'type' => 'submit',
            'name' => E::ts('Save & Import'),
            'isDefault' => TRUE,
        ),
    ));

    // export form elements
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    // see if shadowdata_db has changed
    CRM_Shadowdata_Config::setShadowdataTableDB($values['shadowdata_db']);

    // import file, if there is one
    if (!empty($_FILES['contact_import_file']['tmp_name'])) {
      $file = $_FILES['contact_import_file'];
      if ($file['type'] == 'text/csv') {
        $transaction = new CRM_Core_Transaction();
        try {
          $result = CRM_Shadowdata_Contact::import($file['tmp_name']);
          if (is_string($result)) {
            CRM_Core_Session::setStatus($result, E::ts('Import Failed'), 'error');
            $transaction->rollback();
          } else {
            CRM_Core_Session::setStatus(E::ts("%1 records imported", [1 => $result]), E::ts('Import Completed'), 'info');
            $transaction->commit();
          }
        } catch (Exception $ex) {
          CRM_Core_Session::setStatus($ex->getMessage(), E::ts('Import Failed'), 'error');
          $transaction->rollback();
        }
      } else {
        CRM_Core_Session::setStatus(E::ts('Incorrect file type, expected CSV file.'), E::ts('Import Failed'), 'error');
      }
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/shadowdata', 'reset=1'));
    }

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

