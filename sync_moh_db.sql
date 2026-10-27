-- ==============================================================================
-- DATABASE MIGRATION SCRIPT FOR MOH_BIO
-- Target Database: moh_bio (Municipal Government of Hinoba-an)
-- Purpose: Schema updates for Leave Balances, Special Leaves w/ Pay, and Accruals
-- ==============================================================================

USE `moh_bio`;

-- 1. ADD RUNNING LEAVE BALANCES TO PERSONNELS TABLE
-- Enables personnel records to store and quickly reference current VL and SL totals
ALTER TABLE `personnels`
ADD COLUMN `vl_balance` DECIMAL(10,3) NOT NULL DEFAULT 0.000 AFTER `monthly_salary`,
ADD COLUMN `sl_balance` DECIMAL(10,3) NOT NULL DEFAULT 0.000 AFTER `vl_balance`;

-- 2. ADD COLUMNS TO LEAVE CREDIT LOGS TABLE
-- Supports period, particulars, Absence w/ Pay (vl_w_pay, sl_w_pay), and LWOP (vl_wout_pay, sl_wout_pay)
ALTER TABLE `leave_credit_logs`
ADD COLUMN `period` VARCHAR(100) DEFAULT NULL AFTER `date_logged`,
ADD COLUMN `particulars` VARCHAR(100) DEFAULT NULL AFTER `period`,
ADD COLUMN `vl_w_pay` DECIMAL(10,3) DEFAULT 0.000 AFTER `sl_amount`,
ADD COLUMN `sl_w_pay` DECIMAL(10,3) DEFAULT 0.000 AFTER `vl_w_pay`,
ADD COLUMN `vl_wout_pay` DECIMAL(10,3) DEFAULT 0.000 AFTER `sl_w_pay`,
ADD COLUMN `sl_wout_pay` DECIMAL(10,3) DEFAULT 0.000 AFTER `vl_wout_pay`;

-- 3. CREATE AUTOMATED ACCRUAL TRACKER TABLE
-- Prevents duplicate monthly 1.250 VL / SL accrual runs
CREATE TABLE IF NOT EXISTS `leave_accrual_history` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `accrual_month` VARCHAR(7) NOT NULL UNIQUE, -- format: YYYY-MM
    `date_processed` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. OVERTIME STATUS AND REVIEW TRACKING (OPTIONAL / RECOMMENDED)
ALTER TABLE `personnel_overtime`
ADD COLUMN `status` VARCHAR(20) DEFAULT 'Pending' AFTER `ot_reason`,
ADD COLUMN `reviewed_by` INT(11) DEFAULT NULL AFTER `status`,
ADD COLUMN `reviewed_at` DATETIME DEFAULT NULL AFTER `reviewed_by`;
