-- SQL Migration: Create signatories_settings table for CS Form No. 6
-- Run this SQL in your phpMyAdmin or MySQL client

CREATE TABLE IF NOT EXISTS `signatories_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hrmo_name` varchar(255) DEFAULT NULL COMMENT 'Full name of HRMO/Certifying Officer',
  `hrmo_position` varchar(255) DEFAULT 'Human Resource Management Officer' COMMENT 'Position of HRMO',
  `recommending_name` varchar(255) DEFAULT NULL COMMENT 'Full name of Recommending Officer/Immediate Supervisor',
  `recommending_position` varchar(255) DEFAULT 'Immediate Supervisor' COMMENT 'Position of Recommending Officer',
  `approving_name` varchar(255) DEFAULT NULL COMMENT 'Full name of Approving Officer/Head of Agency',
  `approving_position` varchar(255) DEFAULT 'Regional Director' COMMENT 'Position of Approving Officer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Signatories for CS Form No. 6 (Application for Leave)';

-- Insert default values (optional)
INSERT INTO `signatories_settings` (`hrmo_name`, `hrmo_position`, `recommending_name`, `recommending_position`, `approving_name`, `approving_position`)
VALUES (NULL, 'Human Resource Management Officer', NULL, 'Immediate Supervisor', NULL, 'Regional Director')
ON DUPLICATE KEY UPDATE `id` = `id`;
