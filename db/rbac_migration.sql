-- 1. Add module_access to useraccount
ALTER TABLE `useraccount` 
ADD COLUMN `module_access` VARCHAR(255) DEFAULT 'hris' AFTER `access`;

-- 2. Update existing 'Administrator' / 'Admin' roles to just 'Admin' and grant all
UPDATE `useraccount` SET `access` = 'Admin', `module_access` = 'hris,payroll' WHERE `access` LIKE '%Admin%';

-- 3. Update existing 'User' / 'HR Staff' / 'Payroll Staff' to 'Staff'
UPDATE `useraccount` SET `access` = 'Staff', `module_access` = 'hris' WHERE `access` NOT IN ('Admin');

-- 4. Create Audit Trail table
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
