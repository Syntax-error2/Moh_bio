-- ==============================================================================
-- DATABASE MIGRATION SCRIPT
-- Date: 2026-08-17
-- Purpose: Schema updates for the Biometric Integration System
-- ==============================================================================

-- 1. CREATE BIOMETRIC DTR LOGS TABLE
-- Purpose: The existing system only tracked RFID logs in `personnel_logs`. 
-- To prevent the ZKTeco biometric device from mixing up raw fingerprint logs 
-- with RFID logs (and to prevent UI bugs), this standalone table was created 
-- strictly to hold the biometric device data. The DTR print engine dynamically 
-- stitches data from this table together with RFID logs.
CREATE TABLE IF NOT EXISTS bio_dtr (
  bio_id int(11) NOT NULL AUTO_INCREMENT,
  personnel_id int(11) NOT NULL,
  biometric_id varchar(100) NOT NULL,
  log_date date NOT NULL,
  time_in time NOT NULL,
  time_out time NOT NULL,
  PRIMARY KEY (bio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. ADD BIOMETRIC COLUMNS TO PERSONNELS
-- Purpose: Maps the employee in our system to their ID and fingerprint template stored inside the ZKTeco device.
ALTER TABLE personnels 
ADD COLUMN IF NOT EXISTS biometric_id varchar(55) DEFAULT NULL AFTER RFTag_id,
ADD COLUMN IF NOT EXISTS fingerprint_template longtext DEFAULT NULL AFTER biometric_id;
