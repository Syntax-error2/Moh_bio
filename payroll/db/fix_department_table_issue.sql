-- ============================================================================
-- FIX: Department Table Issue
-- ============================================================================
-- Date: October 20, 2025
-- Issue: Code references 'tbl_dept' but actual table is 'dept_offices'
-- Solution: Create a view to alias dept_offices as tbl_dept for compatibility
-- ============================================================================

-- Option 1: Create a VIEW (Recommended - Non-destructive)
-- This creates an alias so code can reference either name
-- ============================================================================

DROP VIEW IF EXISTS tbl_dept;

CREATE VIEW tbl_dept AS
SELECT 
    do_id as dept_id,
    dept_office_name as dept_title,
    dept_office_name as dept_name,
    officeHead_id as head_id,
    do_id,
    dept_office_name,
    officeHead_id
FROM dept_offices;

-- ============================================================================
-- Verify the view works
-- ============================================================================

-- Test query (should return all departments)
-- SELECT * FROM tbl_dept;

-- ============================================================================
-- Option 2: Create SYNONYM (MySQL doesn't support synonyms, use view above)
-- ============================================================================

-- ============================================================================
-- Option 3: Rename table (NOT RECOMMENDED - will break existing code)
-- ============================================================================

-- DO NOT RUN THIS - It will break existing code that uses dept_offices
-- RENAME TABLE dept_offices TO tbl_dept;

-- ============================================================================
-- DEPARTMENT TABLE STRUCTURE REFERENCE
-- ============================================================================

-- Actual dept_offices table structure:
CREATE TABLE `dept_offices` (
  `do_id` INT(11) NOT NULL AUTO_INCREMENT,
  `dept_office_name` VARCHAR(255) NOT NULL,
  `officeHead_id` INT(11) NOT NULL,
  PRIMARY KEY (`do_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- ============================================================================
-- USAGE EXAMPLES
-- ============================================================================

-- Old code (will now work):
-- SELECT * FROM tbl_dept ORDER BY dept_title ASC;

-- New code (still works):
-- SELECT * FROM dept_offices ORDER BY dept_office_name ASC;

-- Both reference the same data!

-- ============================================================================
-- COLUMN MAPPING
-- ============================================================================

-- tbl_dept (view)      →  dept_offices (actual table)
-- ----------------        ---------------------------
-- dept_id              →  do_id
-- dept_title           →  dept_office_name
-- dept_name            →  dept_office_name
-- head_id              →  officeHead_id
-- do_id                →  do_id (passthrough)
-- dept_office_name     →  dept_office_name (passthrough)
-- officeHead_id        →  officeHead_id (passthrough)

-- ============================================================================
-- NOTES
-- ============================================================================

-- 1. This view provides compatibility without breaking existing code
-- 2. Both tbl_dept and dept_offices can be used interchangeably
-- 3. Changes to dept_offices are immediately reflected in tbl_dept view
-- 4. The view is read-only by default (inserts/updates go to base table)
-- 5. Some columns are aliased, some are passthrough for maximum compatibility

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check if view was created successfully
-- SHOW FULL TABLES WHERE Table_type = 'VIEW';

-- Verify data is accessible
-- SELECT dept_id, dept_title FROM tbl_dept LIMIT 5;

-- Compare with original table
-- SELECT do_id, dept_office_name FROM dept_offices LIMIT 5;

-- ============================================================================
-- ROLLBACK (if needed)
-- ============================================================================

-- To remove the view:
-- DROP VIEW IF EXISTS tbl_dept;

-- ============================================================================
