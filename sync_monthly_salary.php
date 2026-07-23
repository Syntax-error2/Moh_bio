<?php
/**
 * Monthly Salary Synchronization Manager
 * Keeps monthly_salary in sync between personnels and service_record tables
 */

require_once 'dbcon.php';

class MonthlySalarySync {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Sync monthly_salary from service_record to personnels table
     * Uses the latest service record for each personnel
     */
    public function syncFromServiceRecordToPersonnels() {
        try {
            $sql = "UPDATE personnels p
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
                    SET p.monthly_salary = latest_sr.monthly_salary";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            
            return [
                'success' => true,
                'message' => "Successfully synced $rowCount personnel records",
                'count' => $rowCount
            ];
        } catch (PDOException $e) {
            error_log("Error syncing salary from service_record to personnels: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Error: " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Sync monthly_salary for a specific personnel
     */
    public function syncPersonnel($personnel_id) {
        try {
            // Get latest service record salary
            $sql = "SELECT monthly_salary 
                    FROM service_record 
                    WHERE personnel_id = :personnel_id
                    ORDER BY 
                        CASE 
                            WHEN serv_date_to = '' OR serv_date_to IS NULL THEN '9999-12-31'
                            ELSE serv_date_to
                        END DESC,
                        sr_id DESC
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':personnel_id' => $personnel_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $monthly_salary = $result['monthly_salary'];
                
                // Update personnels table
                $update_sql = "UPDATE personnels 
                              SET monthly_salary = :monthly_salary 
                              WHERE personnel_id = :personnel_id";
                
                $update_stmt = $this->conn->prepare($update_sql);
                $update_stmt->execute([
                    ':monthly_salary' => $monthly_salary,
                    ':personnel_id' => $personnel_id
                ]);
                
                return [
                    'success' => true,
                    'message' => "Synced personnel $personnel_id with salary $monthly_salary"
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "No service record found for personnel $personnel_id"
                ];
            }
        } catch (PDOException $e) {
            error_log("Error syncing personnel $personnel_id: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Error: " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get mismatched records between personnels and service_record
     */
    public function getMismatchedRecords() {
        try {
            $sql = "SELECT 
                        p.personnel_id,
                        CONCAT(p.fname, ' ', p.lname) as name,
                        p.monthly_salary as personnel_salary,
                        sr.monthly_salary as service_record_salary,
                        ABS(COALESCE(p.monthly_salary, 0) - COALESCE(sr.monthly_salary, 0)) as difference
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
                    ORDER BY difference DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error getting mismatched records: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Error: " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get sync statistics
     */
    public function getSyncStats() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_personnels,
                        SUM(CASE WHEN ABS(COALESCE(p.monthly_salary, 0) - COALESCE(sr.monthly_salary, 0)) <= 0.01 THEN 1 ELSE 0 END) as synced,
                        SUM(CASE WHEN ABS(COALESCE(p.monthly_salary, 0) - COALESCE(sr.monthly_salary, 0)) > 0.01 THEN 1 ELSE 0 END) as not_synced
                    FROM personnels p
                    LEFT JOIN (
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
                    ) sr ON p.personnel_id = sr.personnel_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return [
                'success' => true,
                'data' => $stmt->fetch(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error getting sync stats: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Error: " . $e->getMessage()
            ];
        }
    }
}

// If this script is run directly
if (php_sapi_name() === 'cli' || basename($_SERVER['PHP_SELF']) === 'sync_monthly_salary.php') {
    $sync = new MonthlySalarySync($conn);
    
    echo "=== Monthly Salary Synchronization ===\n\n";
    
    // Get current stats
    echo "Current Status:\n";
    $stats = $sync->getSyncStats();
    if ($stats['success']) {
        $data = $stats['data'];
        echo "Total Personnel: {$data['total_personnels']}\n";
        echo "Synced: {$data['synced']}\n";
        echo "Not Synced: {$data['not_synced']}\n\n";
    }
    
    // Show mismatched records
    $mismatched = $sync->getMismatchedRecords();
    if ($mismatched['success'] && count($mismatched['data']) > 0) {
        echo "Mismatched Records:\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-10s %-30s %-15s %-15s %-10s\n", "ID", "Name", "Personnel", "Service Rec", "Diff");
        echo str_repeat("-", 80) . "\n";
        foreach ($mismatched['data'] as $row) {
            printf("%-10s %-30s %-15s %-15s %-10s\n", 
                $row['personnel_id'],
                substr($row['name'], 0, 28),
                number_format($row['personnel_salary'], 2),
                number_format($row['service_record_salary'], 2),
                number_format($row['difference'], 2)
            );
        }
        echo str_repeat("-", 80) . "\n\n";
    }
    
    // Perform sync
    echo "Performing synchronization...\n";
    $result = $sync->syncFromServiceRecordToPersonnels();
    echo $result['message'] . "\n\n";
    
    // Get updated stats
    echo "After Synchronization:\n";
    $stats = $sync->getSyncStats();
    if ($stats['success']) {
        $data = $stats['data'];
        echo "Total Personnel: {$data['total_personnels']}\n";
        echo "Synced: {$data['synced']}\n";
        echo "Not Synced: {$data['not_synced']}\n\n";
    }
    
    echo "=== Synchronization Complete ===\n";
}
?>
