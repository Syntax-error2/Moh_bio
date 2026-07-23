-- ====================================
-- Migration: Add inclusive_dates_json column to leave_applications table
-- Purpose: Support multiple inclusive date ranges in a single leave application
-- Example: January 22-23, 2026 AND January 26-28, 2026 (skipping weekends)
-- Created: January 15, 2026
-- ====================================

-- Add new column to store multiple date ranges as JSON array
-- Format: [{"from": "2026-01-22", "to": "2026-01-23"}, {"from": "2026-01-26", "to": "2026-01-28"}]
ALTER TABLE `leave_applications` 
ADD COLUMN IF NOT EXISTS `inclusive_dates_json` JSON DEFAULT NULL 
COMMENT 'JSON array of date ranges: [{"from": "YYYY-MM-DD", "to": "YYYY-MM-DD"}, ...]' 
AFTER `inclusive_date_to`;

-- ====================================
-- Migration Script to populate existing data
-- Converts existing single-range dates to JSON format
-- ====================================
UPDATE `leave_applications` 
SET `inclusive_dates_json` = JSON_ARRAY(
    JSON_OBJECT('from', DATE_FORMAT(`inclusive_date_from`, '%Y-%m-%d'), 'to', DATE_FORMAT(`inclusive_date_to`, '%Y-%m-%d'))
)
WHERE `inclusive_dates_json` IS NULL 
AND `inclusive_date_from` IS NOT NULL 
AND `inclusive_date_to` IS NOT NULL;

-- ====================================
-- Verification query
-- ====================================
-- SELECT id, inclusive_date_from, inclusive_date_to, inclusive_dates_json 
-- FROM leave_applications 
-- ORDER BY id DESC LIMIT 10;
