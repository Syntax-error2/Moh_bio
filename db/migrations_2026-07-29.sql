-- Migration generated on 2026-07-29 13:01:29
-- This file contains all the payroll module additions made to mob_rfid_dtr

-- Table structure for `pr_tbl_deductions`
CREATE TABLE `pr_tbl_deductions` (
  `deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `deduction_type` varchar(55) DEFAULT NULL,
  `deduction_title` varchar(55) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`deduction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `pr_tbl_income`
CREATE TABLE `pr_tbl_income` (
  `income_id` int(11) NOT NULL AUTO_INCREMENT,
  `income_type` varchar(55) DEFAULT NULL,
  `income_title` varchar(55) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`income_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `pr_tbl_pay_pro_personnels`
CREATE TABLE `pr_tbl_pay_pro_personnels` (
  `ppp_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) NOT NULL,
  `payprofile_id` int(11) NOT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`ppp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `pr_tbl_payroll_audit_log`
CREATE TABLE `pr_tbl_payroll_audit_log` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `run_id` int(11) DEFAULT NULL,
  `detail_id` int(11) DEFAULT NULL,
  `action_type` enum('create','update','delete','approve','cancel','complete') NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `field_name` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL COMMENT 'User who performed the action',
  `performed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`audit_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_detail_id` (`detail_id`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_table_name` (`table_name`),
  KEY `idx_performed_at` (`performed_at`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Audit trail for all payroll changes';

-- Table structure for `pr_tbl_payroll_profile_deductions`
CREATE TABLE `pr_tbl_payroll_profile_deductions` (
  `profile_deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_profiles.profile_id',
  `deduction_id` int(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `default_employee_amt` decimal(10,2) DEFAULT NULL COMMENT 'Default employee amount',
  `default_employer_amt` decimal(10,2) DEFAULT NULL COMMENT 'Default employer amount',
  `amount_calculation` enum('fixed','percentage','formula','personnel_specific') NOT NULL DEFAULT 'personnel_specific',
  `calculation_base` varchar(50) DEFAULT NULL COMMENT 'For percentage: what to base on',
  `calculation_value` decimal(10,4) DEFAULT NULL COMMENT 'For percentage: the percentage value',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`profile_deduction_id`),
  UNIQUE KEY `unique_profile_deduction` (`profile_id`,`deduction_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_deduction_id` (`deduction_id`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Deduction items included in payroll profiles';

-- Table structure for `pr_tbl_payroll_profile_filters`
CREATE TABLE `pr_tbl_payroll_profile_filters` (
  `filter_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL,
  `filter_type` enum('department','designation','emp_status','personnel','all') NOT NULL,
  `filter_value` varchar(50) NOT NULL COMMENT 'ID or "all"',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`filter_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_filter_type` (`filter_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Define which personnel are included in profile';

-- Table structure for `pr_tbl_payroll_profile_income`
CREATE TABLE `pr_tbl_payroll_profile_income` (
  `profile_income_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_profiles.profile_id',
  `income_id` int(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `default_amount` decimal(10,2) DEFAULT NULL COMMENT 'Default amount (NULL = use personnel-specific amount)',
  `amount_calculation` enum('fixed','percentage','formula','personnel_specific') NOT NULL DEFAULT 'personnel_specific',
  `calculation_base` varchar(50) DEFAULT NULL COMMENT 'For percentage: what to base on (e.g., "basic_salary")',
  `calculation_value` decimal(10,4) DEFAULT NULL COMMENT 'For percentage: the percentage value',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Must this income be included?',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`profile_income_id`),
  UNIQUE KEY `unique_profile_income` (`profile_id`,`income_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_income_id` (`income_id`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Income items included in payroll profiles';

-- Table structure for `pr_tbl_payroll_profiles`
CREATE TABLE `pr_tbl_payroll_profiles` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(100) NOT NULL COMMENT 'Template name (e.g., "Regular Monthly Payroll", "13th Month Pay")',
  `profile_description` text DEFAULT NULL COMMENT 'Detailed description of this template',
  `profile_type` enum('regular','special','13th_month','bonus','custom') NOT NULL DEFAULT 'regular',
  `pay_frequency` enum('monthly','semi-monthly','bi-weekly','weekly','one-time') NOT NULL DEFAULT 'monthly',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Is this the default profile for regular payroll?',
  `created_by` int(11) DEFAULT NULL COMMENT 'User who created this profile',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `unique_profile_name` (`profile_name`),
  KEY `idx_profile_type` (`profile_type`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_is_default` (`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Payroll templates/profiles for easy cloning and reuse';

-- Table structure for `pr_tbl_payroll_run_deductions`
CREATE TABLE `pr_tbl_payroll_run_deductions` (
  `run_deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `detail_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_run_details.detail_id',
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` varchar(50) NOT NULL,
  `deduction_id` int(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `deduction_title` varchar(100) NOT NULL COMMENT 'Snapshot of deduction name at time of run',
  `deduction_type` varchar(50) NOT NULL,
  `employee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `employer_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`run_deduction_id`),
  KEY `idx_detail_id` (`detail_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_deduction_id` (`deduction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Deduction breakdown snapshot for each payroll run';

-- Table structure for `pr_tbl_payroll_run_details`
CREATE TABLE `pr_tbl_payroll_run_details` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` varchar(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `gross_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_employer_share` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','paid','hold','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` enum('bank_transfer','check','cash','other') DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL COMMENT 'Check number, transaction ID, etc.',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`detail_id`),
  UNIQUE KEY `unique_run_personnel` (`run_id`,`personnel_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_detail_status` (`payment_status`,`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5655 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Individual personnel records within each payroll run';

-- Table structure for `pr_tbl_payroll_run_income`
CREATE TABLE `pr_tbl_payroll_run_income` (
  `run_income_id` int(11) NOT NULL AUTO_INCREMENT,
  `detail_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_run_details.detail_id',
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` varchar(50) NOT NULL,
  `income_id` int(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `income_title` varchar(100) NOT NULL COMMENT 'Snapshot of income name at time of run',
  `income_type` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`run_income_id`),
  KEY `idx_detail_id` (`detail_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_income_id` (`income_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6987 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Income breakdown snapshot for each payroll run';

-- Table structure for `pr_tbl_payroll_runs`
CREATE TABLE `pr_tbl_payroll_runs` (
  `run_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) DEFAULT NULL COMMENT 'Profile used to generate this run',
  `run_name` varchar(150) NOT NULL COMMENT 'Payroll run name (e.g., "October 2025 Regular Payroll")',
  `run_type` enum('regular','special','13th_month','bonus','adjustment','custom') NOT NULL DEFAULT 'regular',
  `pay_period_start` date NOT NULL COMMENT 'Start of pay period',
  `pay_period_end` date NOT NULL COMMENT 'End of pay period',
  `payment_date` date DEFAULT NULL COMMENT 'Actual payment date',
  `run_status` enum('draft','pending','approved','processing','completed','cancelled') NOT NULL DEFAULT 'draft',
  `total_personnel` int(11) NOT NULL DEFAULT 0 COMMENT 'Total number of personnel in this run',
  `total_gross` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total gross pay for all personnel',
  `total_deductions` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total deductions (employee portion)',
  `total_employer_share` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total employer contributions',
  `total_net_pay` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total net pay for all personnel',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'User who created this run',
  `approved_by` int(11) DEFAULT NULL COMMENT 'User who approved this run',
  `approved_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`run_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_run_status` (`run_status`),
  KEY `idx_run_type` (`run_type`),
  KEY `idx_pay_period` (`pay_period_start`,`pay_period_end`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_run_status_date` (`run_status`,`pay_period_start`,`pay_period_end`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Payroll execution history - each row is one payroll run';

-- Table structure for `pr_tbl_payroll_snapshot_items`
CREATE TABLE `pr_tbl_payroll_snapshot_items` (
  `snapshot_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `snapshot_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_snapshots.snapshot_id',
  `run_id` int(11) NOT NULL,
  `item_type` enum('income','deduction') NOT NULL,
  `item_id` int(11) NOT NULL COMMENT 'income_id or deduction_id',
  `item_title` varchar(100) NOT NULL,
  `item_category` varchar(50) NOT NULL COMMENT 'income_type or deduction_type',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `personnel_count` int(11) NOT NULL DEFAULT 0 COMMENT 'How many personnel have this item',
  `average_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`snapshot_item_id`),
  KEY `idx_snapshot_id` (`snapshot_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_item_type` (`item_type`),
  KEY `idx_item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Detailed breakdown of income/deductions in snapshots';

-- Table structure for `pr_tbl_payroll_snapshots`
CREATE TABLE `pr_tbl_payroll_snapshots` (
  `snapshot_id` int(11) NOT NULL AUTO_INCREMENT,
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `snapshot_date` date NOT NULL COMMENT 'Date snapshot was generated',
  `snapshot_type` enum('department','designation','emp_status','income_type','deduction_type','overall') NOT NULL,
  `group_by_value` varchar(100) DEFAULT NULL COMMENT 'Department ID, Designation ID, etc.',
  `group_by_label` varchar(150) DEFAULT NULL COMMENT 'Department Name, Designation Name, etc.',
  `personnel_count` int(11) NOT NULL DEFAULT 0,
  `total_gross` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_employer_share` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_net_pay` decimal(15,2) NOT NULL DEFAULT 0.00,
  `average_gross` decimal(10,2) NOT NULL DEFAULT 0.00,
  `average_net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`snapshot_id`),
  KEY `idx_run_id` (`run_id`),
  KEY `idx_snapshot_type` (`snapshot_type`),
  KEY `idx_snapshot_date` (`snapshot_date`),
  KEY `idx_group_by` (`snapshot_type`,`group_by_value`),
  KEY `idx_snapshot_run_type` (`run_id`,`snapshot_type`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Aggregate payroll statistics for reporting and analysis';

-- Table structure for `pr_tbl_personnel_deductions`
CREATE TABLE `pr_tbl_personnel_deductions` (
  `personnel_deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` varchar(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `deduction_id` int(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `employer_amt_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid by employer per pay period',
  `employee_amt_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount deducted from employee per pay period',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'User who created this record',
  PRIMARY KEY (`personnel_deduction_id`),
  UNIQUE KEY `unique_personnel_deduction` (`personnel_id`,`deduction_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_deduction_id` (`deduction_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `pr_tbl_personnel_income`
CREATE TABLE `pr_tbl_personnel_income` (
  `personnel_income_id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` varchar(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `income_id` int(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `amount_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid per pay period',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'User who created this record',
  PRIMARY KEY (`personnel_income_id`),
  UNIQUE KEY `unique_personnel_income` (`personnel_id`,`income_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_income_id` (`income_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Junction table: Links personnel to income types with amounts';

-- View structure for `vw_payroll_personnel_details`
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_payroll_personnel_details` AS select `prd`.`detail_id` AS `detail_id`,`prd`.`run_id` AS `run_id`,`pr`.`run_name` AS `run_name`,`pr`.`pay_period_start` AS `pay_period_start`,`pr`.`pay_period_end` AS `pay_period_end`,`prd`.`personnel_id` AS `personnel_id`,concat(`p`.`fname`,' ',ifnull(concat(substr(`p`.`mname`,1,1),'. '),''),`p`.`lname`) AS `personnel_name`,`d`.`dept_office_name` AS `dept_office_name`,`des`.`des_name` AS `designation_name`,`prd`.`gross_pay` AS `gross_pay`,`prd`.`total_deductions` AS `total_deductions`,`prd`.`total_employer_share` AS `total_employer_share`,`prd`.`net_pay` AS `net_pay`,`prd`.`payment_status` AS `payment_status`,`prd`.`payment_method` AS `payment_method`,`prd`.`payment_reference` AS `payment_reference` from ((((`pr_tbl_payroll_run_details` `prd` join `pr_tbl_payroll_runs` `pr` on(`prd`.`run_id` = `pr`.`run_id`)) join `personnels` `p` on(`prd`.`personnel_id` = `p`.`personnel_id`)) left join `dept_offices` `d` on(`p`.`do_id` = `d`.`do_id`)) left join `designation` `des` on(`p`.`des_id` = `des`.`des_id`));

-- View structure for `vw_payroll_run_summary`
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_payroll_run_summary` AS select `pr`.`run_id` AS `run_id`,`pr`.`run_name` AS `run_name`,`pr`.`run_type` AS `run_type`,`pr`.`pay_period_start` AS `pay_period_start`,`pr`.`pay_period_end` AS `pay_period_end`,`pr`.`payment_date` AS `payment_date`,`pr`.`run_status` AS `run_status`,`pr`.`total_personnel` AS `total_personnel`,`pr`.`total_gross` AS `total_gross`,`pr`.`total_deductions` AS `total_deductions`,`pr`.`total_employer_share` AS `total_employer_share`,`pr`.`total_net_pay` AS `total_net_pay`,`pp`.`profile_name` AS `profile_name`,concat(`u1`.`fname`,' ',`u1`.`lname`) AS `created_by_name`,concat(`u2`.`fname`,' ',`u2`.`lname`) AS `approved_by_name`,`pr`.`approved_at` AS `approved_at`,`pr`.`completed_at` AS `completed_at`,`pr`.`created_at` AS `created_at` from (((`pr_tbl_payroll_runs` `pr` left join `pr_tbl_payroll_profiles` `pp` on(`pr`.`profile_id` = `pp`.`profile_id`)) left join `useraccount` `u1` on(`pr`.`created_by` = `u1`.`user_id`)) left join `useraccount` `u2` on(`pr`.`approved_by` = `u2`.`user_id`));




-- Official Registry Signatory Templates
CREATE TABLE IF NOT EXISTS pr_tbl_signatory_templates (
    template_id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100),
    is_default TINYINT(1) DEFAULT 0,
    created_at DATETIME
);

CREATE TABLE IF NOT EXISTS pr_tbl_signatory_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT,
    role_title VARCHAR(150),
    person_name VARCHAR(150),
    display_order INT
);

-- Check if default template exists to avoid duplicates
SET @existing = (SELECT COUNT(*) FROM pr_tbl_signatory_templates WHERE template_name = 'Standard Official Registry');

-- Only insert if it doesn't exist
INSERT INTO pr_tbl_signatory_templates (template_name, is_default, created_at)
SELECT 'Standard Official Registry', 1, NOW()
WHERE @existing = 0;

SET @last_id = LAST_INSERT_ID();

INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, display_order)
SELECT @last_id, 'Prepared By', 'HR Officer', 1
WHERE @existing = 0 AND @last_id > 0;

INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, display_order)
SELECT @last_id, 'Certified: Service duly rendered as stated', 'Municipal Mayor', 2
WHERE @existing = 0 AND @last_id > 0;

INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, display_order)
SELECT @last_id, 'Approved for Payment', 'Municipal Mayor', 3
WHERE @existing = 0 AND @last_id > 0;

INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, display_order)
SELECT @last_id, 'Certified: Each employee whose name appears on the payroll has been paid the amount as indicated', '', 4
WHERE @existing = 0 AND @last_id > 0;

INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, display_order)
SELECT @last_id, 'Accounting Entries', '', 5
WHERE @existing = 0 AND @last_id > 0;

INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, display_order)
SELECT @last_id, 'Certified: Cash available for the purpose', 'Municipal Accountant', 6
WHERE @existing = 0 AND @last_id > 0;

INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, display_order)
SELECT @last_id, 'Certified Correct:', 'Head of Treasury Division/Unit', 7
WHERE @existing = 0 AND @last_id > 0;
