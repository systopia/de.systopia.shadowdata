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

{* stats section *}
<h3>{ts domain="de.systopia.shadowdata"}Statistics{/ts}</h3>

<table class="shadowdata-stats">
  <tbody>
    {foreach from=$stats key=label item=number}
      <tr>
        <td>{$label}</td>
        <td>{$number}</td>
      </tr>
    {/foreach}
  </tbody>
</table>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
