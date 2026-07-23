-- ============================================================================
-- MONTHLY SALARY SYNCHRONIZATION SYSTEM
-- Database: moh_hrms
-- Purpose: Keep monthly_salary in sync between personnels and service_record
-- ============================================================================

-- ============================================================================
-- STEP 1: CREATE TRIGGERS FOR AUTOMATIC SYNCHRONIZATION
-- ============================================================================

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS sync_salary_after_service_record_insert;
DROP TRIGGER IF EXISTS sync_salary_after_service_record_update;
DROP TRIGGER IF EXISTS sync_salary_after_personnel_update;

DELIMITER $$

-- Trigger 1: When a new service record is inserted, update personnels table
CREATE TRIGGER sync_salary_after_service_record_insert
AFTER INSERT ON service_record
FOR EACH ROW
BEGIN
    -- Update personnels table with the latest monthly_salary from the newest service record
    UPDATE personnels 
    SET monthly_salary = NEW.monthly_salary
    WHERE personnel_id = NEW.personnel_id;
END$$

-- Trigger 2: When a service record is updated, update personnels table
CREATE TRIGGER sync_salary_after_service_record_update
AFTER UPDATE ON service_record
FOR EACH ROW
BEGIN
    -- Get the most recent service record for this personnel
    DECLARE latest_salary DECIMAL(14,3);
    
    SELECT monthly_salary INTO latest_salary
    FROM service_record
    WHERE personnel_id = NEW.personnel_id
    ORDER BY 
        CASE 
            WHEN serv_date_to = '' OR serv_date_to IS NULL THEN '9999-12-31'
            ELSE serv_date_to
        END DESC,
        sr_id DESC
    LIMIT 1;
    
    -- Update personnels table with the latest salary
    UPDATE personnels 
    SET monthly_salary = latest_salary
    WHERE personnel_id = NEW.personnel_id;
END$$

-- Trigger 3: When personnels monthly_salary is updated, update the latest service record
CREATE TRIGGER sync_salary_after_personnel_update
AFTER UPDATE ON personnels
FOR EACH ROW
BEGIN
    -- Only proceed if monthly_salary actually changed
    IF NEW.monthly_salary != OLD.monthly_salary THEN
        -- Update the most recent service record
        UPDATE service_record
        SET monthly_salary = NEW.monthly_salary
        WHERE personnel_id = NEW.personnel_id
        AND sr_id = (
            SELECT sr_id FROM (
                SELECT sr_id
                FROM service_record
                WHERE personnel_id = NEW.personnel_id
                ORDER BY 
                    CASE 
                        WHEN serv_date_to = '' OR serv_date_to IS NULL THEN '9999-12-31'
                        ELSE serv_date_to
                    END DESC,
                    sr_id DESC
                LIMIT 1
            ) AS latest_sr
        );
    END IF;
END$$

DELIMITER ;

-- ============================================================================
-- STEP 2: SYNC EXISTING DATA
-- ============================================================================

-- Update personnels table with latest service record monthly_salary
UPDATE personnels p
INNER JOIN (
    SELECT 
        sr1.personnel_id,
        sr1.monthly_salary
    FROM service_record sr1
    INNER JOIN (
        SELECT 
            personnel_id,
            MAX(CASE 
                WHEN serv_date_to = '' OR serv_date_to IS NULL THEN '9999-12-31'
                ELSE serv_date_to
            END) as latest_date,
            MAX(sr_id) as latest_sr_id
        FROM service_record
        GROUP BY personnel_id
    ) sr2 ON sr1.personnel_id = sr2.personnel_id
    WHERE (
        CASE 
            WHEN sr1.serv_date_to = '' OR sr1.serv_date_to IS NULL THEN '9999-12-31'
            ELSE sr1.serv_date_to
        END = sr2.latest_date
        OR sr1.sr_id = sr2.latest_sr_id
    )
    GROUP BY sr1.personnel_id
) latest_sr ON p.personnel_id = latest_sr.personnel_id
SET p.monthly_salary = latest_sr.monthly_salary;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check for mismatches between personnels and service_record
SELECT 
    p.personnel_id,
    CONCAT(p.fname, ' ', p.lname) as name,
    p.monthly_salary as personnel_salary,
    sr.monthly_salary as service_record_salary,
    ABS(p.monthly_salary - sr.monthly_salary) as difference
FROM personnels p
INNER JOIN (
    SELECT 
        sr1.personnel_id,
        sr1.monthly_salary,
        sr1.sr_id
    FROM service_record sr1
    INNER JOIN (
        SELECT 
            personnel_id,
            MAX(sr_id) as latest_sr_id
        FROM service_record
        GROUP BY personnel_id
    ) sr2 ON sr1.personnel_id = sr2.personnel_id AND sr1.sr_id = sr2.latest_sr_id
) sr ON p.personnel_id = sr.personnel_id
WHERE ABS(COALESCE(p.monthly_salary, 0) - COALESCE(sr.monthly_salary, 0)) > 0.01
ORDER BY difference DESC;

-- Count total synchronized records
SELECT 
    COUNT(*) as total_personnels,
    SUM(CASE WHEN p.monthly_salary = sr.monthly_salary THEN 1 ELSE 0 END) as synced,
    SUM(CASE WHEN p.monthly_salary != sr.monthly_salary THEN 1 ELSE 0 END) as not_synced
FROM personnels p
INNER JOIN (
    SELECT 
        sr1.personnel_id,
        sr1.monthly_salary
    FROM service_record sr1
    INNER JOIN (
        SELECT 
            personnel_id,
            MAX(sr_id) as latest_sr_id
        FROM service_record
        GROUP BY personnel_id
    ) sr2 ON sr1.personnel_id = sr2.personnel_id AND sr1.sr_id = sr2.latest_sr_id
) sr ON p.personnel_id = sr.personnel_id;
