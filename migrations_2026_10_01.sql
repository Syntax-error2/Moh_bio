-- Consolidated Migrations for MOH HRIS
-- Date: 2026-10-01

-- 1. Create Profile Management Tables (If missing on boss's laptop)
CREATE TABLE IF NOT EXISTS `dept_offices` (
  `do_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_office_name` varchar(255) NOT NULL,
  `officeHead_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`do_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `designation` (
  `designation_id` int(11) NOT NULL AUTO_INCREMENT,
  `designation_name` varchar(255) NOT NULL,
  PRIMARY KEY (`designation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `emp_status` (
  `es_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_status_name` varchar(255) NOT NULL,
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Payroll Module Schema
CREATE TABLE IF NOT EXISTS `pr_tbl_personnel_deductions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` varchar(255) NOT NULL,
  `deduction_name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_profile_deductions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL,
  `deduction_name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Biometric Integration
ALTER TABLE `personnels` 
ADD COLUMN IF NOT EXISTS `biometric_id` VARCHAR(255) NULL AFTER `personnel_id`,
ADD COLUMN IF NOT EXISTS `fingerprint_template` LONGTEXT NULL AFTER `biometric_id`;

CREATE TABLE IF NOT EXISTS `bio_dtr` (
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

-- 4. Role Update for Administrator
UPDATE useraccount SET access = 'Admin' WHERE access = 'Administrator';
