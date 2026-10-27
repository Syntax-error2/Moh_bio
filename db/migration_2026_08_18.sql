-- ==============================================================================
-- DATABASE MIGRATION SCRIPT
-- Date: 2026-08-18
-- Purpose: Schema updates for the Automated LGU Leave Credit Management System
-- ==============================================================================

-- 1. ADD LEAVE BALANCES TO PERSONNELS
-- Purpose: The existing system did not store leave credits. These columns act 
-- as the main "ledger" to track the current available Vacation and Sick Leaves.
ALTER TABLE personnels 
ADD COLUMN vl_balance DECIMAL(10,3) NOT NULL DEFAULT 0.000,
ADD COLUMN sl_balance DECIMAL(10,3) NOT NULL DEFAULT 0.000;


-- 2. CREATE LEAVE CREDIT AUDIT LOGS TABLE
-- Purpose: Serves as an audit trail for CSC reporting. Every time a balance is 
-- changed (whether by manual HR adjustment, automated tardiness deduction, 
-- leave application, or monthly accrual), it is permanently logged here.
CREATE TABLE IF NOT EXISTS leave_credit_logs (
    log_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    personnel_id INT(11) NOT NULL,
    log_type VARCHAR(50) NOT NULL, -- 'Accrual', 'Tardiness Deduction', 'Manual Adjustment', 'Leave Application'
    vl_amount DECIMAL(10,3) NOT NULL DEFAULT 0.000,
    sl_amount DECIMAL(10,3) NOT NULL DEFAULT 0.000,
    remarks TEXT,
    date_logged DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- 3. CREATE AUTOMATED ACCRUAL TRACKER
-- Purpose: Since the 1.25 monthly accrual is triggered automatically when HR 
-- logs into the dashboard, this table prevents race conditions. It stores the 
-- month (e.g., '2026-08') so the system knows it has already distributed the 
-- credits and won't accidentally add them again.
CREATE TABLE IF NOT EXISTS leave_accrual_history (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    accrual_month VARCHAR(7) NOT NULL UNIQUE, -- format: YYYY-MM
    date_processed DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
