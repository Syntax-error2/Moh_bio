-- --------------------------------------------------------
-- Migration: Biometric System Integration
-- Date: October 2026
-- Database: moh_hrms
-- --------------------------------------------------------

-- 1. Create the `bio_dtr` table if it doesn't exist to store biometric time logs.
CREATE TABLE IF NOT EXISTS `bio_dtr` (
  `bio_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) NOT NULL,
  `biometric_id` varchar(255) DEFAULT NULL,
  `log_date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`bio_id`),
  KEY `idx_personnel_date` (`personnel_id`, `log_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Add biometric tracking columns to the `personnels` table
--    (We use a stored procedure to safely add columns without raising errors if they already exist)

DELIMITER //

CREATE PROCEDURE AddBiometricColumns()
BEGIN
    -- Check if `biometric_id` exists
    IF NOT EXISTS (
        SELECT 1
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'personnels'
          AND COLUMN_NAME = 'biometric_id'
    ) THEN
        ALTER TABLE `personnels` ADD COLUMN `biometric_id` varchar(255) DEFAULT NULL AFTER `personnel_id`;
    END IF;

    -- Check if `fingerprint_template` exists
    IF NOT EXISTS (
        SELECT 1
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'personnels'
          AND COLUMN_NAME = 'fingerprint_template'
    ) THEN
        ALTER TABLE `personnels` ADD COLUMN `fingerprint_template` longtext DEFAULT NULL AFTER `biometric_id`;
    END IF;
END//

DELIMITER ;

-- Execute the procedure to safely alter the table
CALL AddBiometricColumns();

-- Clean up the temporary procedure
DROP PROCEDURE AddBiometricColumns;

-- --------------------------------------------------------
-- End of Migration
-- --------------------------------------------------------

-- Create missing tables from mob_bio required by print files

CREATE TABLE IF NOT EXISTS cron_monthly_leave (
  id int(11) NOT NULL AUTO_INCREMENT,
  month_year varchar(7) DEFAULT NULL,
  date_executed datetime DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY month_year (month_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS leave_accrual_history (
  id int(11) NOT NULL AUTO_INCREMENT,
  accrual_month varchar(7) NOT NULL,
  date_processed datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY accrual_month (accrual_month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS leave_credit_logs (
  log_id int(11) NOT NULL AUTO_INCREMENT,
  personnel_id int(11) NOT NULL,
  log_type varchar(50) NOT NULL,
  vl_amount decimal(10,3) NOT NULL DEFAULT 0.000,
  sl_amount decimal(10,3) NOT NULL DEFAULT 0.000,
  remarks text DEFAULT NULL,
  date_logged datetime NOT NULL DEFAULT current_timestamp(),
  period varchar(100) DEFAULT NULL,
  particulars varchar(100) DEFAULT NULL,
  vl_wout_pay decimal(10,3) DEFAULT 0.000,
  sl_wout_pay decimal(10,3) DEFAULT 0.000,
  PRIMARY KEY (log_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS personnel_overtime_requests (
  ot_id int(11) NOT NULL AUTO_INCREMENT,
  personnel_id int(11) NOT NULL,
  ot_date date NOT NULL,
  remarks text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (ot_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Archive active Job Order personnel in moh_bio without deleting their records.
-- The verification query should return 0 after the update.
UPDATE moh_bio.personnels
SET separation_date = DATE_FORMAT(CURDATE(), '%m/%d/%Y')
WHERE empStat_id = 4
  AND separation_date IS NULL;

SELECT COUNT(*) AS active_job_orders
FROM moh_bio.personnels
WHERE empStat_id = 4
  AND (separation_date IS NULL
       OR separation_date = ''
       OR separation_date = '  /  /    ');
