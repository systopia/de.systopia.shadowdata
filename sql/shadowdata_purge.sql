-- SQL command to purge the shadow contacts that haven't been activated
DELETE FROM `shadowdata_contact`
WHERE use_by < NOW()
  AND contact_id IS NULL
;