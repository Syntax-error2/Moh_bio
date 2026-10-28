-- Fix for missing manual logs and incorrect DTR views
-- 1. Identify biometric-only users who suffered from the `<br /><b` corruption
-- 2. Give them unique placeholder RFTag_ids (T{personnel_id})
--    Note: RFTag_id is varchar(8), so we use a very short prefix to avoid truncation.
-- 3. Restore their manual logs by matching their names

-- Fix corrupted personnels by giving them a unique placeholder tag based on their ID
UPDATE personnels SET RFTag_id = CONCAT('T', personnel_id) WHERE RFTag_id LIKE '<br%' OR RFTag_id = '' OR RFTag_id LIKE 'TEMP_%';

-- Fix personnel_logs that suffered the same corruption or are empty by matching names
UPDATE personnel_logs pl INNER JOIN personnels p ON pl.lname = p.lname AND pl.fname = p.fname SET pl.RFTag_id = p.RFTag_id WHERE (pl.RFTag_id LIKE '<br%' OR pl.RFTag_id = '' OR pl.RFTag_id LIKE 'TEMP_%') AND p.RFTag_id LIKE 'T%';

-- Fix logs that were flipped from "AM IN" to "AM OUT" because of the old truncated tag bug.
-- This happens when someone scans in the morning but they share a tag with someone who already scanned earlier.
UPDATE personnel_logs pl
SET pl.logFlow = 'AM IN'
WHERE pl.logFlow = 'AM OUT' 
AND pl.logTime_sec < 43200 
AND NOT EXISTS (
    SELECT 1 FROM (SELECT * FROM personnel_logs) AS pl2 
    WHERE pl2.logDate = pl.logDate AND pl2.RFTag_id = pl.RFTag_id AND pl2.logFlow = 'AM IN'
);

UPDATE bio_dtr 
SET time_in = time_out, time_out = '00:00:00' 
WHERE time_in = '00:00:00' AND time_out != '00:00:00' AND time_out < '12:00:00';
