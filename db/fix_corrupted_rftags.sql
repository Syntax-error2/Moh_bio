-- This script cleans up the corrupted RFTag_ids ("<br /><b") caused by an import error.
-- Please run this on the server's MySQL database to apply the data fix.

UPDATE personnels SET RFTag_id = '' WHERE RFTag_id = '<br /><b';
UPDATE personnel_logs SET RFTag_id = '' WHERE RFTag_id = '<br /><b';
