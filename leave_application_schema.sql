-- ====================================
-- CS FORM NO. 6 - LEAVE APPLICATION SYSTEM
-- Database Schema
-- Created: October 24, 2025
-- ====================================

-- Table: leave_applications
-- Stores all leave application records based on CS Form No. 6
CREATE TABLE IF NOT EXISTS `leave_applications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` INT(11) NOT NULL,
  `office_agency` VARCHAR(255) NOT NULL COMMENT 'Office/Agency/Department',
  `application_date` DATE NOT NULL COMMENT 'Date of filing',
  `leave_type` VARCHAR(100) NOT NULL COMMENT 'Type of leave (Vacation, Sick, Maternity, etc.)',
  `other_leave_specification` VARCHAR(255) DEFAULT NULL COMMENT 'Specification for "Others" leave type',
  `vacation_details` TEXT DEFAULT NULL COMMENT 'Where vacation will be spent (within/abroad Philippines)',
  `sick_details` TEXT DEFAULT NULL COMMENT 'Illness details or hospital name (in/out patient)',
  `study_details` TEXT DEFAULT NULL COMMENT 'Study leave details (degree, university)',
  `inclusive_date_from` DATE NOT NULL COMMENT 'Start date of leave',
  `inclusive_date_to` DATE NOT NULL COMMENT 'End date of leave',
  `number_of_days` DECIMAL(5,2) NOT NULL COMMENT 'Number of working days applied for',
  `commutation` ENUM('requested','not_requested') DEFAULT 'not_requested' COMMENT 'Commutation request status',
  `as_of_date` DATE DEFAULT NULL COMMENT 'Date for leave credits certification',
  `total_earned_vl` DECIMAL(10,3) DEFAULT 0.000 COMMENT 'Total Vacation Leave earned',
  `total_earned_sl` DECIMAL(10,3) DEFAULT 0.000 COMMENT 'Total Sick Leave earned',
  `less_application_vl` DECIMAL(10,3) DEFAULT 0.000 COMMENT 'VL deduction for this application',
  `less_application_sl` DECIMAL(10,3) DEFAULT 0.000 COMMENT 'SL deduction for this application',
  `balance_vl` DECIMAL(10,3) DEFAULT 0.000 COMMENT 'VL balance after application',
  `balance_sl` DECIMAL(10,3) DEFAULT 0.000 COMMENT 'SL balance after application',
  `status` ENUM('pending','approved','disapproved') DEFAULT 'pending' COMMENT 'Application status',
  `recommendation` TEXT DEFAULT NULL COMMENT 'Recommendation or remarks from authorized officer',
  `approved_by` INT(11) DEFAULT NULL COMMENT 'User ID who approved/disapproved',
  `approved_date` DATETIME DEFAULT NULL COMMENT 'Date and time of approval/disapproval',
  `leave_card_entry_id` INT(11) DEFAULT NULL COMMENT 'Linked leave_card entry ID (auto-created on approval)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_application_date` (`application_date`),
  KEY `idx_status` (`status`),
  KEY `idx_leave_card_entry` (`leave_card_entry_id`),
  CONSTRAINT `fk_leave_app_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`personnel_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='CS Form No. 6 - Leave Applications';

-- ====================================
-- Add columns to leave_card table to track applications and store date range
-- This column indicates if the leave card entry was auto-created from an approved application
-- ====================================
ALTER TABLE `leave_card` 
ADD COLUMN IF NOT EXISTS `created_from_application` TINYINT(1) DEFAULT 0 COMMENT 'Auto-created from approved leave application' AFTER `is_special_leave`;

ALTER TABLE `leave_card` 
ADD COLUMN IF NOT EXISTS `date_from` DATE DEFAULT NULL COMMENT 'Leave start date' AFTER `created_from_application`;

ALTER TABLE `leave_card` 
ADD COLUMN IF NOT EXISTS `date_to` DATE DEFAULT NULL COMMENT 'Leave end date' AFTER `date_from`;

ALTER TABLE `leave_card` 
ADD COLUMN IF NOT EXISTS `number_of_days` DECIMAL(5,2) DEFAULT NULL COMMENT 'Number of leave days' AFTER `date_to`;

-- ====================================
-- Indexes for better performance
-- ====================================
CREATE INDEX IF NOT EXISTS `idx_created_from_app` ON `leave_card` (`created_from_application`);

-- ====================================
-- Sample query to view leave applications with personnel info
-- ====================================
-- SELECT 
--     la.id,
--     la.application_date,
--     la.leave_type,
--     la.inclusive_date_from,
--     la.inclusive_date_to,
--     la.number_of_days,
--     la.status,
--     CONCAT(p.lastname, ', ', p.firstname, ' ', COALESCE(p.middlename, '')) as full_name,
--     d.designation_name,
--     la.leave_card_entry_id
-- FROM leave_applications la
-- LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
-- LEFT JOIN designation d ON p.designation_id = d.designation_id
-- ORDER BY la.application_date DESC, la.created_at DESC;

-- ====================================
-- Sample query to check leave applications with linked leave card entries
-- ====================================
-- SELECT 
--     la.id as app_id,
--     la.application_date,
--     la.leave_type,
--     la.status,
--     la.leave_card_entry_id,
--     lc.id as leave_card_id,
--     lc.period_from,
--     lc.period_to,
--     lc.particulars,
--     lc.vl_with_pay,
--     lc.sl_with_pay,
--     lc.created_from_application
-- FROM leave_applications la
-- LEFT JOIN leave_card lc ON la.leave_card_entry_id = lc.id
-- WHERE la.status = 'approved'
-- ORDER BY la.application_date DESC;
