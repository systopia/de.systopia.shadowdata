{*-------------------------------------------------------+
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
+-------------------------------------------------------*}

{* Database Link *}
<br/>
<h3>{ts domain="de.systopia.shadowdata"}Database for shadow data{/ts}</h3>
<div id="help">
  {ts domain="de.systopia.shadowdata"}You can set a database name for the <code>shadowdata_*</code> tables, in case you don't want to store the data with the rest of the CiviCRM data.{/ts}
  {ts}Changing this will make sure the (prefixed) tables exist, but will not delete the old ones.{/ts}
  {ts}If empty, the CiviCRM database will be used.{/ts}
</div>
<div class="crm-section">
  <div class="label">{$form.shadowdata_db.label}</div>
  <div class="content">{$form.shadowdata_db.html}</div>
  <div class="clear"></div>
</div>

{* upload section *}
<br/>
<h3>{ts domain="de.systopia.shadowdata"}Upload More Contact Records{/ts}</h3>
<div id="help">
  {ts domain="de.systopia.shadowdata"}To import more data you need to upload an ISO 8859-1 encoded CSV file with ';' separators.{/ts}
  {ts 1=$contact_template_link domain="de.systopia.shadowdata"}You can download a template <a download='ShadowContacts.csv' href="%1">HERE</a>{/ts}
</div>
<div class="crm-section">
  <div class="label">{$form.contact_import_file.label}</div>
  <div class="content">{$form.contact_import_file.html}</div>
  <div class="clear"></div>
</div>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
