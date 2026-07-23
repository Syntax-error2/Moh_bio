-- Personnel Income Assignment Table Schema
-- This table stores income amounts for individual personnel (many-to-many relationship)
-- Links personnels to pr_tbl_income with specific amounts

CREATE TABLE IF NOT EXISTS `pr_tbl_personnel_income` (
  `personnel_income_id` INT(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` VARCHAR(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `income_id` INT(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `amount_per_pay` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid per pay period',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `user_id` INT(11) NULL COMMENT 'User who created this record',
  
  PRIMARY KEY (`personnel_income_id`),
  UNIQUE KEY `unique_personnel_income` (`personnel_id`, `income_id`),
  KEY `idx_personnel_id` (`personnel_id`),
  KEY `idx_income_id` (`income_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Junction table: Links personnel to income types with amounts';

-- Sample Usage Queries:

-- 1. Get all active income for a specific personnel
-- SELECT
--     p.personnel_id,
--     CONCAT(p.fname, ' ', p.lname) AS personnel_name,
--     i.income_type,
--     i.income_title,
--     pi.amount_per_pay
-- FROM pr_tbl_personnel_income pi
-- INNER JOIN personnels p ON pi.personnel_id = p.personnel_id
-- INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
-- WHERE pi.is_active = 1
-- ORDER BY p.lname, p.fname, i.income_title;

-- 2. Get total income (gross pay) for a specific personnel
-- SELECT
--     SUM(amount_per_pay) AS gross_pay
-- FROM pr_tbl_personnel_income
-- WHERE personnel_id = 'P001' AND is_active = 1;

-- 3. Get income breakdown for payroll
-- SELECT
--     pi.personnel_id,
--     i.income_title,
--     pi.amount_per_pay
-- FROM pr_tbl_personnel_income pi
-- INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
-- WHERE pi.personnel_id IN ('P001', 'P002', 'P003')
--   AND pi.is_active = 1
-- ORDER BY pi.personnel_id, i.income_type, i.income_title;

-- 4. Get income by type for a personnel
-- SELECT
--     i.income_type,
--     SUM(pi.amount_per_pay) AS total
-- FROM pr_tbl_personnel_income pi
-- INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
-- WHERE pi.personnel_id = 'P001' AND pi.is_active = 1
-- GROUP BY i.income_type;
