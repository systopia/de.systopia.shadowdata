-- ***********************
--   CONTACT shadow data
-- ***********************
CREATE TABLE IF NOT EXISTS `shadowdata_contact`(
    -- metadata
     `id`                      int unsigned NOT NULL  AUTO_INCREMENT  COMMENT 'internal ID',
     `contact_id`              int unsigned           COMMENT 'FK to Contact ID that was generated by this data set',
     `code`                    varchar(64)            COMMENT 'unique code for the data set',
     `import_date`             datetime NOT NULL      COMMENT 'import timestamp',
     `unlock_date`             datetime               COMMENT 'unlock timestamp',
     `use_by`                  datetime               COMMENT 'if set, data should be cleared after this date - unless they have been unlocked',
     `source`                  varchar(64)            COMMENT 'simple source field',
    -- contact data
     `contact_type`            varchar(64)            COMMENT 'civicrm_contact field',
     `organization_name`       varchar(128)           COMMENT 'civicrm_contact field',
     `household_name`          varchar(128)           COMMENT 'civicrm_contact field',
     `first_name`              varchar(64)            COMMENT 'civicrm_contact field',
     `last_name`               varchar(64)            COMMENT 'civicrm_contact field',
     `formal_title`            varchar(64)            COMMENT 'civicrm_contact field',
     `prefix_id`               int(10) unsigned       COMMENT 'civicrm_contact field',
     `suffix_id`               int(10) unsigned       COMMENT 'civicrm_contact field',
     `gender_id`               int(10) unsigned       COMMENT 'civicrm_contact field',
     `birth_date`              date                   COMMENT 'civicrm_contact field',
     `job_title`               varchar(255)           COMMENT 'civicrm_contact field',
     `email`                   varchar(254)           COMMENT 'civicrm_email field',
     `phone`                   varchar(32)            COMMENT 'civicrm_phone field',
     `street_address`          varchar(96)            COMMENT 'civicrm_address field',
     `supplemental_address_1`  varchar(96)            COMMENT 'civicrm_address field',
     `supplemental_address_2`  varchar(96)            COMMENT 'civicrm_address field',
     `city`                    varchar(64)            COMMENT 'civicrm_address field',
     `postal_code`             varchar(64)            COMMENT 'civicrm_address field',
     `country_id`              int(10) unsigned       COMMENT 'civicrm_address field',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `code` (code),
    INDEX `contact_id` (contact_id),
    INDEX `use_by` (use_by),
    INDEX `import_date` (import_date),
    INDEX `source` (source),
    CONSTRAINT FK_civicrm_shadowdata_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;