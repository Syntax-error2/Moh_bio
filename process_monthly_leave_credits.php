<?php
/**
 * Automatic Monthly Leave Credits Accrual System
 * 
 * This script automatically adds 1.25 VL and 1.25 SL earned credits
 * for all active personnel on the 1st of each month (or first admin login).
 * 
 * Triggered from session.php when an administrator logs in.
 */

require_once('dbcon.php');

/**
 * Process monthly leave credits for all active personnel
 * 
 * @param PDO $conn Database connection
 * @param int $admin_id ID of the admin triggering the process
 * @return array Result with success status and message
 */
function processMonthlyLeaveCredits($conn, $admin_id = null) {
    try {
        // Get the previous month and year to process
        // Credits for September are added in October
        $currentDate = new DateTime();
        $previousMonth = clone $currentDate;
        $previousMonth->modify('-1 month');
        
        $year = (int)$previousMonth->format('Y');
        $month = (int)$previousMonth->format('n'); // 1-12 without leading zeros
        $monthName = $previousMonth->format('F'); // Full month name
        
        // Calculate period dates (first and last day of the month)
        // Format as DATE for database (YYYY-MM-DD)
        $firstDay = new DateTime($previousMonth->format('Y-m-01'));
        $lastDay = new DateTime($previousMonth->format('Y-m-t'));
        
        $periodFrom = $firstDay->format('Y-m-d'); // First day: 2025-10-01
        $periodTo = $lastDay->format('Y-m-d'); // Last day: 2025-10-31
        
        // Date fields in DATE format for date_from and date_to
        $dateFrom = $firstDay->format('Y-m-d');
        $dateTo = $lastDay->format('Y-m-d');
        
        // Check if this month has already been processed
        $checkQuery = $conn->prepare("
            SELECT COUNT(*) as processed_count 
            FROM monthly_leave_credits_log 
            WHERE year = :year AND month = :month
        ");
        $checkQuery->execute([':year' => $year, ':month' => $month]);
        $result = $checkQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($result['processed_count'] > 0) {
            // Already processed
            return [
                'success' => true,
                'message' => "Monthly leave credits for {$monthName} {$year} already processed.",
                'already_processed' => true,
                'count' => 0
            ];
        }
        
        // Get all active personnel with Permanent or Casual status
        // (those without separation date and with employment status of Permanent or Casual)
        $personnelQuery = $conn->query("
            SELECT p.personnel_id, p.lname, p.fname, p.mname, es.emp_stat_name
            FROM personnels p
            LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id
            WHERE (p.separation_date IS NULL 
               OR p.separation_date = '' 
               OR p.separation_date = '  /  /    ')
            AND (es.emp_stat_name = 'Permanent' OR es.emp_stat_name = 'Casual')
            ORDER BY p.lname, p.fname
        ");
        
        $processedCount = 0;
        $errors = [];
        
        $conn->beginTransaction();
        
        while ($personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC)) {
            $personnelId = $personnel['personnel_id'];
            
            try {
                // Insert into leave_card table
                $particulars = "Month of {$monthName} {$year}";
                
                $insertLeaveCard = $conn->prepare("
                    INSERT INTO leave_card (
                        personnel_id,
                        period_from,
                        period_to,
                        particulars,
                        vl_earned,
                        vl_with_pay,
                        vl_without_pay,
                        sl_earned,
                        sl_with_pay,
                        sl_without_pay,
                        remarks,
                        is_special_leave,
                        created_from_application,
                        date_from,
                        date_to
                    ) VALUES (
                        :personnel_id,
                        :period_from,
                        :period_to,
                        :particulars,
                        1.25,
                        0.000,
                        0.000,
                        1.25,
                        0.000,
                        0.000,
                        'Monthly Leave Credits',
                        0,
                        0,
                        :date_from,
                        :date_to
                    )
                ");
                
                $insertLeaveCard->execute([
                    ':personnel_id' => $personnelId,
                    ':period_from' => $periodFrom,
                    ':period_to' => $periodTo,
                    ':particulars' => $particulars,
                    ':date_from' => $dateFrom,
                    ':date_to' => $dateTo
                ]);
                
                $leaveCardId = $conn->lastInsertId();
                
                // Log the processing in monthly_leave_credits_log
                $insertLog = $conn->prepare("
                    INSERT INTO monthly_leave_credits_log (
                        personnel_id,
                        year,
                        month,
                        vl_earned,
                        sl_earned,
                        leave_card_id,
                        processed_by
                    ) VALUES (
                        :personnel_id,
                        :year,
                        :month,
                        1.25,
                        1.25,
                        :leave_card_id,
                        :processed_by
                    )
                ");
                
                $insertLog->execute([
                    ':personnel_id' => $personnelId,
                    ':year' => $year,
                    ':month' => $month,
                    ':leave_card_id' => $leaveCardId,
                    ':processed_by' => $admin_id
                ]);
                
                $processedCount++;
                
            } catch (PDOException $e) {
                // Log individual errors but continue processing
                $errors[] = "Error processing personnel ID {$personnelId}: " . $e->getMessage();
                error_log("Monthly Leave Credits Error for personnel {$personnelId}: " . $e->getMessage());
            }
        }
        
        $conn->commit();
        
        return [
            'success' => true,
            'message' => "Successfully processed monthly leave credits for {$monthName} {$year}. {$processedCount} personnel records updated.",
            'already_processed' => false,
            'count' => $processedCount,
            'month' => $monthName,
            'year' => $year,
            'errors' => $errors
        ];
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        error_log("Monthly Leave Credits Processing Error: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => "Error processing monthly leave credits: " . $e->getMessage(),
            'already_processed' => false,
            'count' => 0
        ];
    }
}

/**
 * Check if monthly leave credits should be processed
 * This is called from session.php when admin logs in
 * 
 * @param PDO $conn Database connection
 * @param int $admin_id ID of the admin logging in
 * @return void
 */
function checkAndProcessMonthlyCredits($conn, $admin_id) {
    // Only process if it's a new month that hasn't been processed yet
    $result = processMonthlyLeaveCredits($conn, $admin_id);
    
    // Store result in session for display notification if needed
    if (!$result['already_processed'] && $result['success']) {
        $_SESSION['monthly_credits_processed'] = $result;
    }
}

// If called directly (for manual processing or testing)
if (basename($_SERVER['PHP_SELF']) == 'process_monthly_leave_credits.php') {
    session_start();
    
    // Check if user is admin
    if (!isset($_SESSION['id']) || $_SESSION['useraccess'] !== 'Administrator') {
        die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
    }
    
    $result = processMonthlyLeaveCredits($conn, $_SESSION['id']);
    
    // Return JSON for AJAX calls
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        // Display results for direct access
        echo "<h3>Monthly Leave Credits Processing Result</h3>";
        echo "<p><strong>Status:</strong> " . ($result['success'] ? 'Success' : 'Failed') . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($result['message']) . "</p>";
        if (!empty($result['errors'])) {
            echo "<p><strong>Errors:</strong></p><ul>";
            foreach ($result['errors'] as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
        }
        echo "<p><a href='home.php'>Return to Home</a></p>";
    }
}
?>
