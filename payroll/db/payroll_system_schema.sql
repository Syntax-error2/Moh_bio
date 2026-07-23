-- ============================================================================
-- PAYROLL TEMPLATE, HISTORY & SNAPSHOT SYSTEM
-- ============================================================================
-- This schema adds three major features:
-- 1. Payroll Profiles (Templates) - Reusable payroll configurations
-- 2. Payroll History - Track all payroll runs with detailed records
-- 3. Payroll Snapshots - Aggregate summaries and reporting
-- ============================================================================

-- ============================================================================
-- PART 1: PAYROLL PROFILES (TEMPLATES)
-- ============================================================================

-- Main profile/template definition table
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_profiles` (
  `profile_id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_name` VARCHAR(100) NOT NULL COMMENT 'Template name (e.g., "Regular Monthly Payroll", "13th Month Pay")',
  `profile_description` TEXT NULL COMMENT 'Detailed description of this template',
  `profile_type` ENUM('regular', 'special', '13th_month', 'bonus', 'custom') NOT NULL DEFAULT 'regular',
  `pay_frequency` ENUM('monthly', 'semi-monthly', 'bi-weekly', 'weekly', 'one-time') NOT NULL DEFAULT 'monthly',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is this the default profile for regular payroll?',
  `created_by` INT(11) NULL COMMENT 'User who created this profile',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `unique_profile_name` (`profile_name`),
  KEY `idx_profile_type` (`profile_type`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_is_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Payroll templates/profiles for easy cloning and reuse';

-- Profile income items (what income types are included in this template)
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_profile_income` (
  `profile_income_id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_profiles.profile_id',
  `income_id` INT(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `default_amount` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Default amount (NULL = use personnel-specific amount)',
  `amount_calculation` ENUM('fixed', 'percentage', 'formula', 'personnel_specific') NOT NULL DEFAULT 'personnel_specific',
  `calculation_base` VARCHAR(50) NULL COMMENT 'For percentage: what to base on (e.g., "basic_salary")',
  `calculation_value` DECIMAL(10,4) NULL COMMENT 'For percentage: the percentage value',
  `is_mandatory` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Must this income be included?',
  `display_order` INT(11) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`profile_income_id`),
  UNIQUE KEY `unique_profile_income` (`profile_id`, `income_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_income_id` (`income_id`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Income items included in payroll profiles';

-- Profile deduction items (what deduction types are included in this template)
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_profile_deductions` (
  `profile_deduction_id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_profiles.profile_id',
  `deduction_id` INT(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `default_employee_amt` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Default employee amount',
  `default_employer_amt` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Default employer amount',
  `amount_calculation` ENUM('fixed', 'percentage', 'formula', 'personnel_specific') NOT NULL DEFAULT 'personnel_specific',
  `calculation_base` VARCHAR(50) NULL COMMENT 'For percentage: what to base on',
  `calculation_value` DECIMAL(10,4) NULL COMMENT 'For percentage: the percentage value',
  `is_mandatory` TINYINT(1) NOT NULL DEFAULT 1,
  `display_order` INT(11) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`profile_deduction_id`),
  UNIQUE KEY `unique_profile_deduction` (`profile_id`, `deduction_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_deduction_id` (`deduction_id`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Deduction items included in payroll profiles';

-- Profile filters (which personnel are included in this template)
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_profile_filters` (
  `filter_id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL,
  `filter_type` ENUM('department', 'designation', 'emp_status', 'personnel', 'all') NOT NULL,
  `filter_value` VARCHAR(50) NOT NULL COMMENT 'ID or "all"',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`filter_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_filter_type` (`filter_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Define which personnel are included in profile';

-- ============================================================================
-- PART 2: PAYROLL HISTORY (PAYROLL RUNS)
-- ============================================================================

-- Main payroll run/execution table
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_runs` (
  `run_id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NULL COMMENT 'Profile used to generate this run',
  `run_name` VARCHAR(150) NOT NULL COMMENT 'Payroll run name (e.g., "October 2025 Regular Payroll")',
  `run_type` ENUM('regular', 'special', '13th_month', 'bonus', 'adjustment', 'custom') NOT NULL DEFAULT 'regular',
  `pay_period_start` DATE NOT NULL COMMENT 'Start of pay period',
  `pay_period_end` DATE NOT NULL COMMENT 'End of pay period',
  `payment_date` DATE NULL COMMENT 'Actual payment date',
  `run_status` ENUM('draft', 'pending', 'approved', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'draft',
  `total_personnel` INT(11) NOT NULL DEFAULT 0 COMMENT 'Total number of personnel in this run',
  `total_gross` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total gross pay for all personnel',
  `total_deductions` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total deductions (employee portion)',
  `total_employer_share` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total employer contributions',
  `total_net_pay` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total net pay for all personnel',
  `notes` TEXT NULL,
  `created_by` INT(11) NULL COMMENT 'User who created this run',
  `approved_by` INT(11) NULL COMMENT 'User who approved this run',
  `approved_at` DATETIME NULL,
  `completed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`run_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_run_status` (`run_status`),
  KEY `idx_run_type` (`run_type`),
  KEY `idx_pay_period` (`pay_period_start`, `pay_period_end`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Payroll execution history - each row is one payroll run';

-- Individual personnel payroll records for each run
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_run_details` (
  `detail_id` INT(11) NOT NULL AUTO_INCREMENT,
  `run_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` VARCHAR(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `gross_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_deductions` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_employer_share` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `net_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('pending', 'paid', 'hold', 'cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` ENUM('bank_transfer', 'check', 'cash', 'other') NULL,
  `payment_reference` VARCHAR(100) NULL COMMENT 'Check number, transaction ID, etc.',
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`detail_id`),
  UNIQUE KEY `unique_run_personnel` (`run_id`, `personnel_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Individual personnel records within each payroll run';

-- Income breakdown for each personnel in a run
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_run_income` (
  `run_income_id` INT(11) NOT NULL AUTO_INCREMENT,
  `detail_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_run_details.detail_id',
  `run_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` VARCHAR(50) NOT NULL,
  `income_id` INT(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `income_title` VARCHAR(100) NOT NULL COMMENT 'Snapshot of income name at time of run',
  `income_type` VARCHAR(50) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`run_income_id`),
  KEY `idx_detail_id` (`detail_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_income_id` (`income_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Income breakdown snapshot for each payroll run';

-- Deduction breakdown for each personnel in a run
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_run_deductions` (
  `run_deduction_id` INT(11) NOT NULL AUTO_INCREMENT,
  `detail_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_run_details.detail_id',
  `run_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` VARCHAR(50) NOT NULL,
  `deduction_id` INT(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `deduction_title` VARCHAR(100) NOT NULL COMMENT 'Snapshot of deduction name at time of run',
  `deduction_type` VARCHAR(50) NOT NULL,
  `employee_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `employer_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`run_deduction_id`),
  KEY `idx_detail_id` (`detail_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_deduction_id` (`deduction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Deduction breakdown snapshot for each payroll run';

-- ============================================================================
-- PART 3: PAYROLL SNAPSHOTS (AGGREGATE SUMMARIES)
-- ============================================================================

-- Overall payroll statistics and summaries
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_snapshots` (
  `snapshot_id` INT(11) NOT NULL AUTO_INCREMENT,
  `run_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `snapshot_date` DATE NOT NULL COMMENT 'Date snapshot was generated',
  `snapshot_type` ENUM('department', 'designation', 'emp_status', 'income_type', 'deduction_type', 'overall') NOT NULL,
  `group_by_value` VARCHAR(100) NULL COMMENT 'Department ID, Designation ID, etc.',
  `group_by_label` VARCHAR(150) NULL COMMENT 'Department Name, Designation Name, etc.',
  `personnel_count` INT(11) NOT NULL DEFAULT 0,
  `total_gross` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_deductions` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_employer_share` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_net_pay` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `average_gross` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `average_net_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_net_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_net_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`snapshot_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_snapshot_type` (`snapshot_type`),
  KEY `idx_snapshot_date` (`snapshot_date`),
  KEY `idx_group_by` (`snapshot_type`, `group_by_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Aggregate payroll statistics for reporting and analysis';

-- Detailed income/deduction summary by type
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_snapshot_items` (
  `snapshot_item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `snapshot_id` INT(11) NOT NULL COMMENT 'References pr_tbl_payroll_snapshots.snapshot_id',
  `run_id` INT(11) NOT NULL,
  `item_type` ENUM('income', 'deduction') NOT NULL,
  `item_id` INT(11) NOT NULL COMMENT 'income_id or deduction_id',
  `item_title` VARCHAR(100) NOT NULL,
  `item_category` VARCHAR(50) NOT NULL COMMENT 'income_type or deduction_type',
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `personnel_count` INT(11) NOT NULL DEFAULT 0 COMMENT 'How many personnel have this item',
  `average_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`snapshot_item_id`),
  KEY `idx_snapshot_id` (`snapshot_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_item_type` (`item_type`),
  KEY `idx_item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Detailed breakdown of income/deductions in snapshots';

-- ============================================================================
-- PART 4: AUDIT AND CHANGE TRACKING
-- ============================================================================

-- Track changes to payroll runs
CREATE TABLE IF NOT EXISTS `pr_tbl_payroll_audit_log` (
  `audit_id` INT(11) NOT NULL AUTO_INCREMENT,
  `run_id` INT(11) NULL,
  `detail_id` INT(11) NULL,
  `action_type` ENUM('create', 'update', 'delete', 'approve', 'cancel', 'complete') NOT NULL,
  `table_name` VARCHAR(100) NOT NULL,
  `record_id` INT(11) NULL,
  `field_name` VARCHAR(100) NULL,
  `old_value` TEXT NULL,
  `new_value` TEXT NULL,
  `reason` TEXT NULL,
  `performed_by` INT(11) NULL COMMENT 'User who performed the action',
  `performed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `ip_address` VARCHAR(45) NULL,
  
  PRIMARY KEY (`audit_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_detail_id` (`detail_id`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_table_name` (`table_name`),
  KEY `idx_performed_at` (`performed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Audit trail for all payroll changes';

-- ============================================================================
-- SAMPLE DATA INSERTS
-- ============================================================================

-- Insert a default regular payroll profile
INSERT INTO `pr_tbl_payroll_profiles` 
  (`profile_name`, `profile_description`, `profile_type`, `pay_frequency`, `is_active`, `is_default`, `created_by`)
VALUES
  ('Regular Monthly Payroll', 'Standard monthly payroll for all regular employees', 'regular', 'monthly', 1, 1, NULL),
  ('13th Month Pay', 'Annual 13th month payment', '13th_month', 'one-time', 1, 0, NULL),
  ('Special Bonus', 'Special performance or holiday bonus', 'bonus', 'one-time', 1, 0, NULL);

-- ============================================================================
-- USEFUL QUERIES AND VIEWS
-- ============================================================================

-- View: Complete payroll run summary
CREATE OR REPLACE VIEW `vw_payroll_run_summary` AS
SELECT 
    pr.run_id,
    pr.run_name,
    pr.run_type,
    pr.pay_period_start,
    pr.pay_period_end,
    pr.payment_date,
    pr.run_status,
    pr.total_personnel,
    pr.total_gross,
    pr.total_deductions,
    pr.total_employer_share,
    pr.total_net_pay,
    pp.profile_name,
    CONCAT(u1.fname, ' ', u1.lname) AS created_by_name,
    CONCAT(u2.fname, ' ', u2.lname) AS approved_by_name,
    pr.approved_at,
    pr.completed_at,
    pr.created_at
FROM pr_tbl_payroll_runs pr
LEFT JOIN pr_tbl_payroll_profiles pp ON pr.profile_id = pp.profile_id
LEFT JOIN useraccount u1 ON pr.created_by = u1.user_id
LEFT JOIN useraccount u2 ON pr.approved_by = u2.user_id;

-- View: Personnel payroll details with names
CREATE OR REPLACE VIEW `vw_payroll_personnel_details` AS
SELECT 
    prd.detail_id,
    prd.run_id,
    pr.run_name,
    pr.pay_period_start,
    pr.pay_period_end,
    prd.personnel_id,
    CONCAT(p.fname, ' ', IFNULL(CONCAT(SUBSTRING(p.mname, 1, 1), '. '), ''), p.lname) AS personnel_name,
    d.dept_office_name,
    des.des_name AS designation_name,
    prd.gross_pay,
    prd.total_deductions,
    prd.total_employer_share,
    prd.net_pay,
    prd.payment_status,
    prd.payment_method,
    prd.payment_reference
FROM pr_tbl_payroll_run_details prd
INNER JOIN pr_tbl_payroll_runs pr ON prd.run_id = pr.run_id
INNER JOIN personnels p ON prd.personnel_id = p.personnel_id
LEFT JOIN dept_offices d ON p.do_id = d.do_id
LEFT JOIN designation des ON p.des_id = des.des_id;

-- ============================================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================================

-- Additional composite indexes for common queries
CREATE INDEX idx_run_status_date ON pr_tbl_payroll_runs(run_status, pay_period_start, pay_period_end);
CREATE INDEX idx_detail_status ON pr_tbl_payroll_run_details(payment_status, run_id);
CREATE INDEX idx_snapshot_run_type ON pr_tbl_payroll_snapshots(run_id, snapshot_type);

-- ============================================================================
-- STORED PROCEDURES
-- ============================================================================

DELIMITER $$

-- Procedure to generate snapshot data after payroll completion
CREATE PROCEDURE `sp_generate_payroll_snapshot`(IN p_run_id INT)
BEGIN
    DECLARE v_snapshot_date DATE;
    DECLARE v_snapshot_id INT;
    
    SET v_snapshot_date = CURDATE();
    
    -- Generate overall snapshot
    INSERT INTO pr_tbl_payroll_snapshots 
        (run_id, snapshot_date, snapshot_type, group_by_value, group_by_label,
         personnel_count, total_gross, total_deductions, total_employer_share, total_net_pay,
         average_gross, average_net_pay, min_net_pay, max_net_pay)
    SELECT 
        p_run_id,
        v_snapshot_date,
        'overall',
        'ALL',
        'All Personnel',
        COUNT(*),
        SUM(gross_pay),
        SUM(total_deductions),
        SUM(total_employer_share),
        SUM(net_pay),
        AVG(gross_pay),
        AVG(net_pay),
        MIN(net_pay),
        MAX(net_pay)
    FROM pr_tbl_payroll_run_details
    WHERE run_id = p_run_id;
    
    SET v_snapshot_id = LAST_INSERT_ID();
    
    -- Generate department snapshots
    INSERT INTO pr_tbl_payroll_snapshots 
        (run_id, snapshot_date, snapshot_type, group_by_value, group_by_label,
         personnel_count, total_gross, total_deductions, total_employer_share, total_net_pay,
         average_gross, average_net_pay, min_net_pay, max_net_pay)
    SELECT 
        p_run_id,
        v_snapshot_date,
        'department',
        d.do_id,
        d.dept_office_name,
        COUNT(*),
        SUM(prd.gross_pay),
        SUM(prd.total_deductions),
        SUM(prd.total_employer_share),
        SUM(prd.net_pay),
        AVG(prd.gross_pay),
        AVG(prd.net_pay),
        MIN(prd.net_pay),
        MAX(prd.net_pay)
    FROM pr_tbl_payroll_run_details prd
    INNER JOIN personnels p ON prd.personnel_id = p.personnel_id
    INNER JOIN dept_offices d ON p.do_id = d.do_id
    WHERE prd.run_id = p_run_id
    GROUP BY d.do_id, d.dept_office_name;
    
    -- Generate income type summaries
    INSERT INTO pr_tbl_payroll_snapshot_items
        (snapshot_id, run_id, item_type, item_id, item_title, item_category,
         total_amount, personnel_count, average_amount, min_amount, max_amount)
    SELECT 
        v_snapshot_id,
        p_run_id,
        'income',
        income_id,
        income_title,
        income_type,
        SUM(amount),
        COUNT(DISTINCT personnel_id),
        AVG(amount),
        MIN(amount),
        MAX(amount)
    FROM pr_tbl_payroll_run_income
    WHERE run_id = p_run_id
    GROUP BY income_id, income_title, income_type;
    
    -- Generate deduction type summaries
    INSERT INTO pr_tbl_payroll_snapshot_items
        (snapshot_id, run_id, item_type, item_id, item_title, item_category,
         total_amount, personnel_count, average_amount, min_amount, max_amount)
    SELECT 
        v_snapshot_id,
        p_run_id,
        'deduction',
        deduction_id,
        deduction_title,
        deduction_type,
        SUM(employee_amount),
        COUNT(DISTINCT personnel_id),
        AVG(employee_amount),
        MIN(employee_amount),
        MAX(employee_amount)
    FROM pr_tbl_payroll_run_deductions
    WHERE run_id = p_run_id
    GROUP BY deduction_id, deduction_title, deduction_type;
    
END$$

DELIMITER ;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
