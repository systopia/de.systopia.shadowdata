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

  /**
   * Unlock/get the contact with the given code
   *
   * @param $code string code
   * @return int|null contact ID or NULL
   */
  public static function getContactID($code) {
    $code = substr($code, 0, 64);
    // TODO
  }

}