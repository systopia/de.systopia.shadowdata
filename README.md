# SYSTOPIA Shawdow-Data Extension

## What does it do?

The extension will maintain certain database tables, not accessible to the CiviCRM user. These tables store data that can be unlocked by accessing it with the right code.

## Why?

The scenario this extension tries to cover is the purchase of one-off address data. You're allowed to contact them only once. Therefore, they cannot be part of the regular CiviCRM database, since CiviCRM doesn't really prevent anyone from contacting them. However, if they actively contact you, you are allowed to add them to your database. 

The solution is to contact the set using individual contact codes (advertising code), and store all contact data in the shadow database. Once they contact you, you can use the code to "unlock" the stored data, and copy the whole contact into CiviCRM.

## How?

The extension provides an additional API, that you can ask: "give me the contact_id for code XYZ". Then there's three options:
1. the contact is not in the shadow database. Return 'not found'
1. the contact is in the shadow database. Unlock the contact, create a new CiviCRM contact with the stashed data and return its ID.
1. the contact has already been unlocked. In this case, we can return the ID right away