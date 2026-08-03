-- ============================================================================
-- MOH HRMS PROD SYNC DELTA (from dev migrations)
-- Generated: 2026-08-03
-- Purpose: Apply only missing migration pieces on production.
-- Safe to run multiple times where IF NOT EXISTS / idempotent guards are used.
-- ============================================================================

START TRANSACTION;

-- --------------------------------------------------------------------------
-- 1) RBAC migration deltas (missing in prod dump)
-- Source: db/rbac_migration.sql
-- --------------------------------------------------------------------------

ALTER TABLE `useraccount`
ADD COLUMN IF NOT EXISTS `module_access` VARCHAR(255) DEFAULT 'hris' AFTER `access`;

-- Normalize role values and module access.
-- Keeps behavior aligned with current dev migration policy.
UPDATE `useraccount`
SET `access` = 'Admin',
    `module_access` = 'hris,payroll'
WHERE `access` LIKE '%Admin%';

UPDATE `useraccount`
SET `access` = 'Staff',
    `module_access` = 'hris'
WHERE `access` NOT IN ('Admin');

CREATE TABLE IF NOT EXISTS `audit_trail` (
  `audit_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `user_name` VARCHAR(255) NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `module` VARCHAR(50) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_id`),
  KEY `user_id_idx` (`user_id`),
  KEY `module_idx` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------
-- 2) Payroll migration deltas that are missing in prod dump
-- Source: payroll/migrations_2026_08_02.sql
-- Missing objects: pr_tbl_signatory_templates, pr_tbl_signatory_items
-- --------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `pr_tbl_signatory_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `pr_tbl_signatory_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `role_title` varchar(150) DEFAULT NULL,
  `person_name` varchar(150) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `idx_template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Seed default official registry template once.
SET @existing_template := (
  SELECT COUNT(*)
  FROM `pr_tbl_signatory_templates`
  WHERE `template_name` = 'Standard Official Registry'
);

INSERT INTO `pr_tbl_signatory_templates` (`template_name`, `is_default`, `created_at`)
SELECT 'Standard Official Registry', 1, NOW()
WHERE @existing_template = 0;

SET @template_id := (
  SELECT `template_id`
  FROM `pr_tbl_signatory_templates`
  WHERE `template_name` = 'Standard Official Registry'
  ORDER BY `template_id` DESC
  LIMIT 1
);

INSERT INTO `pr_tbl_signatory_items` (`template_id`, `role_title`, `person_name`, `display_order`)
SELECT @template_id, 'Prepared By', 'HR Officer', 1
WHERE @template_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `pr_tbl_signatory_items`
    WHERE `template_id` = @template_id AND `display_order` = 1
  );

INSERT INTO `pr_tbl_signatory_items` (`template_id`, `role_title`, `person_name`, `display_order`)
SELECT @template_id, 'Certified: Service duly rendered as stated', 'Municipal Mayor', 2
WHERE @template_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `pr_tbl_signatory_items`
    WHERE `template_id` = @template_id AND `display_order` = 2
  );

INSERT INTO `pr_tbl_signatory_items` (`template_id`, `role_title`, `person_name`, `display_order`)
SELECT @template_id, 'Approved for Payment', 'Municipal Mayor', 3
WHERE @template_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `pr_tbl_signatory_items`
    WHERE `template_id` = @template_id AND `display_order` = 3
  );

INSERT INTO `pr_tbl_signatory_items` (`template_id`, `role_title`, `person_name`, `display_order`)
SELECT @template_id, 'Certified: Each employee whose name appears on the payroll has been paid the amount as indicated', '', 4
WHERE @template_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `pr_tbl_signatory_items`
    WHERE `template_id` = @template_id AND `display_order` = 4
  );

INSERT INTO `pr_tbl_signatory_items` (`template_id`, `role_title`, `person_name`, `display_order`)
SELECT @template_id, 'Accounting Entries', '', 5
WHERE @template_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `pr_tbl_signatory_items`
    WHERE `template_id` = @template_id AND `display_order` = 5
  );

INSERT INTO `pr_tbl_signatory_items` (`template_id`, `role_title`, `person_name`, `display_order`)
SELECT @template_id, 'Certified: Cash available for the purpose', 'Municipal Accountant', 6
WHERE @template_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `pr_tbl_signatory_items`
    WHERE `template_id` = @template_id AND `display_order` = 6
  );

INSERT INTO `pr_tbl_signatory_items` (`template_id`, `role_title`, `person_name`, `display_order`)
SELECT @template_id, 'Certified Correct:', 'Head of Treasury Division/Unit', 7
WHERE @template_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `pr_tbl_signatory_items`
    WHERE `template_id` = @template_id AND `display_order` = 7
  );

COMMIT;

-- --------------------------------------------------------------------------
-- Post-check queries
-- --------------------------------------------------------------------------
-- SELECT user_id, username, access, module_access FROM useraccount ORDER BY user_id;
-- SHOW TABLES LIKE 'audit_trail';
-- SHOW TABLES LIKE 'pr_tbl_signatory_templates';
-- SHOW TABLES LIKE 'pr_tbl_signatory_items';
-- SELECT template_id, template_name, is_default FROM pr_tbl_signatory_templates;
-- SELECT template_id, display_order, role_title, person_name FROM pr_tbl_signatory_items ORDER BY template_id, display_order;
