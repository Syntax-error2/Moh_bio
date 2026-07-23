-- ================================================================
-- PAYROLL PROFILE MANAGEMENT TABLES
-- Database schema for Personnel Filters, Income Items, and Deduction Items
-- ================================================================

-- 1. Personnel Filters Table
-- Stores filter criteria for determining which personnel are included in a payroll profile
CREATE TABLE IF NOT EXISTS pr_tbl_payroll_profile_filters (
    filter_id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    filter_type ENUM('department', 'employment_status', 'position', 'salary_grade', 'gender', 'age_range', 'custom') NOT NULL,
    filter_operator VARCHAR(20) DEFAULT 'equals',
    filter_value TEXT,
    filter_description VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES pr_tbl_payroll_profiles(profile_id) ON DELETE CASCADE,
    INDEX idx_profile (profile_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Payroll Profile Income Items Table
-- Links income items from master list to specific payroll profiles
CREATE TABLE IF NOT EXISTS pr_tbl_payroll_profile_income (
    profile_income_id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    income_id INT NOT NULL,
    default_amount DECIMAL(12,2) DEFAULT 0.00,
    sort_order INT DEFAULT 0,
    calculation_method ENUM('fixed', 'percentage', 'formula', 'manual') DEFAULT 'fixed',
    formula VARCHAR(500),
    is_mandatory TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES pr_tbl_payroll_profiles(profile_id) ON DELETE CASCADE,
    FOREIGN KEY (income_id) REFERENCES pr_tbl_income(income_id) ON DELETE CASCADE,
    UNIQUE KEY unique_profile_income (profile_id, income_id),
    INDEX idx_profile (profile_id),
    INDEX idx_income (income_id),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Payroll Profile Deduction Items Table
-- Links deduction items from master list to specific payroll profiles
CREATE TABLE IF NOT EXISTS pr_tbl_payroll_profile_deductions (
    profile_deduction_id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    deduction_id INT NOT NULL,
    default_amount DECIMAL(12,2) DEFAULT 0.00,
    sort_order INT DEFAULT 0,
    calculation_method ENUM('fixed', 'percentage', 'formula', 'manual') DEFAULT 'fixed',
    formula VARCHAR(500),
    priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    is_mandatory TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES pr_tbl_payroll_profiles(profile_id) ON DELETE CASCADE,
    FOREIGN KEY (deduction_id) REFERENCES pr_tbl_deductions(deduction_id) ON DELETE CASCADE,
    UNIQUE KEY unique_profile_deduction (profile_id, deduction_id),
    INDEX idx_profile (profile_id),
    INDEX idx_deduction (deduction_id),
    INDEX idx_active (is_active),
    INDEX idx_priority (priority),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- SAMPLE DATA (Optional - for testing)
-- ================================================================

-- Sample: Add filters to profile ID 1
/*
INSERT INTO pr_tbl_payroll_profile_filters 
(profile_id, filter_type, filter_operator, filter_value, filter_description, is_active) 
VALUES
(1, 'employment_status', 'in', 'Permanent,Casual', 'Permanent and Casual employees only', 1),
(1, 'salary_grade', 'between', '15,30', 'Salary Grade 15 to 30', 1);
*/

-- Sample: Add income items to profile ID 1
/*
INSERT INTO pr_tbl_payroll_profile_income 
(profile_id, income_id, default_amount, sort_order, calculation_method, is_mandatory, is_active)
VALUES
(1, 1, 0.00, 1, 'manual', 1, 1),  -- Basic Salary (manual entry per personnel)
(1, 2, 2000.00, 2, 'fixed', 1, 1),  -- PERA (fixed ₱2,000)
(1, 3, 0.00, 3, 'manual', 0, 1);  -- Overtime (variable, optional)
*/

-- Sample: Add deduction items to profile ID 1
/*
INSERT INTO pr_tbl_payroll_profile_deductions 
(profile_id, deduction_id, default_amount, sort_order, calculation_method, priority, is_mandatory, is_active)
VALUES
(1, 1, 0.00, 1, 'manual', 'high', 1, 1),  -- SSS (manual, high priority, mandatory)
(1, 2, 0.00, 2, 'manual', 'high', 1, 1),  -- PhilHealth (manual, high priority)
(1, 3, 200.00, 3, 'fixed', 'high', 1, 1),  -- Pag-IBIG (fixed ₱200)
(1, 4, 0.00, 4, 'formula', 'high', 1, 1);  -- Withholding Tax (formula-based)
*/

-- ================================================================
-- VERIFICATION QUERIES
-- ================================================================

-- Check if tables were created successfully
/*
SHOW TABLES LIKE 'pr_tbl_payroll_profile%';
*/

-- View table structures
/*
DESCRIBE pr_tbl_payroll_profile_filters;
DESCRIBE pr_tbl_payroll_profile_income;
DESCRIBE pr_tbl_payroll_profile_deductions;
*/

-- Count records in each table
/*
SELECT 
    (SELECT COUNT(*) FROM pr_tbl_payroll_profile_filters) as filters_count,
    (SELECT COUNT(*) FROM pr_tbl_payroll_profile_income) as income_count,
    (SELECT COUNT(*) FROM pr_tbl_payroll_profile_deductions) as deduction_count;
*/

-- ================================================================
-- NOTES
-- ================================================================
-- 1. Foreign keys ensure referential integrity
-- 2. ON DELETE CASCADE removes child records when profile is deleted
-- 3. Unique constraints prevent duplicate income/deduction in same profile
-- 4. Indexes improve query performance
-- 5. ENUM fields restrict values to predefined options
-- 6. created_by should reference users table if available
-- ================================================================
