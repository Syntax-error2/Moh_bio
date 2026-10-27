-- Database Migration: Biometric DTR Integration
-- Date: 2026-08-17
-- Description: Adds biometric support to the HRMS by linking personnel to biometric IDs and creating a dedicated DTR table for biometric logs.

-- 1. Add biometric_id and fingerprint_template to personnels table to link employees with their scanner templates
ALTER TABLE `personnels` 
ADD COLUMN `biometric_id` VARCHAR(255) NULL AFTER `personnel_id`,
ADD COLUMN `fingerprint_template` LONGTEXT NULL AFTER `biometric_id`;

-- 2. Create bio_dtr table to store biometric attendance logs
-- This table separates biometric data from legacy RFID personnel_logs
CREATE TABLE `bio_dtr` (
  `bio_dtr_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` varchar(255) NOT NULL,
  `log_date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`bio_dtr_id`),
  KEY `idx_personnel_date` (`personnel_id`,`log_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
