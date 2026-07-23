-- Personnel Deductions Assignment Table Schema
-- This table stores deduction amounts for individual personnel (many-to-many relationship)
-- Links personnels to pr_tbl_deductions with specific amounts

CREATE TABLE IF NOT EXISTS `pr_tbl_personnel_deductions` (
  `personnel_deduction_id` INT(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` VARCHAR(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `deduction_id` INT(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `employer_amt_per_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid by employer per pay period',
  `employee_amt_per_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount deducted from employee per pay period',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `user_id` INT(11) NULL COMMENT 'User who created this record',
  PRIMARY KEY (`personnel_deduction_id`),
  INDEX `idx_personnel_id` (`personnel_id`),
  INDEX `idx_deduction_id` (`deduction_id`),
  INDEX `idx_is_active` (`is_active`),
  UNIQUE KEY `unique_personnel_deduction` (`personnel_id`, `deduction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Note: Foreign key constraints are optional. Uncomment if you want referential integrity:
-- ALTER TABLE `pr_tbl_personnel_deductions` 
--   ADD CONSTRAINT `fk_personnel_ded_personnel` 
--     FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`personnel_id`) 
--     ON DELETE CASCADE ON UPDATE CASCADE,
--   ADD CONSTRAINT `fk_personnel_ded_deduction` 
--     FOREIGN KEY (`deduction_id`) REFERENCES `pr_tbl_deductions` (`deduction_id`) 
--     ON DELETE CASCADE ON UPDATE CASCADE;

-- Sample queries after table creation:

-- 1. View all personnel deductions
-- SELECT 
--     p.personnel_id,
--     CONCAT(p.fname, ' ', p.lname) AS personnel_name,
--     d.deduction_type,
--     d.deduction_title,
--     pd.employer_amt_per_pay,
--     pd.employee_amt_per_pay,
--     (pd.employer_amt_per_pay + pd.employee_amt_per_pay) AS total_deduction
-- FROM pr_tbl_personnel_deductions pd
-- INNER JOIN personnels p ON pd.personnel_id = p.personnel_id
-- INNER JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
-- WHERE pd.is_active = 1
-- ORDER BY p.lname, p.fname, d.deduction_title;

-- 2. Get total deductions for a specific personnel
-- SELECT 
--     SUM(employer_amt_per_pay) AS total_employer,
--     SUM(employee_amt_per_pay) AS total_employee,
--     SUM(employer_amt_per_pay + employee_amt_per_pay) AS grand_total
-- FROM pr_tbl_personnel_deductions
-- WHERE personnel_id = 'P001' AND is_active = 1;

-- 3. Get deduction breakdown for payroll
-- SELECT 
--     pd.personnel_id,
--     d.deduction_title,
--     d.deduction_type,
--     pd.employer_amt_per_pay,
--     pd.employee_amt_per_pay
-- FROM pr_tbl_personnel_deductions pd
-- INNER JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
-- WHERE pd.is_active = 1
-- ORDER BY pd.personnel_id, d.deduction_type;

