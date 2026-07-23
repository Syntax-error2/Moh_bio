<?php
/**
 * Fix Leave Card Sync Script
 * 
 * This script fixes existing leave applications that have mismatched leave_card entries
 * where vl_with_pay/sl_with_pay don't match the application's less_application_* values.
 * 
 * Run this script once to sync all existing records, then delete or disable it.
 * 
 * Usage: Access via browser http://localhost/moh_hrms/fix_leave_card_sync.php
 *        Or run from command line: php fix_leave_card_sync.php
 */

session_start();
require_once 'dbcon.php';

// Check if user is logged in (skip for CLI execution)
$is_cli = php_sapi_name() === 'cli';
if (!$is_cli && !isset($_SESSION['id'])) {
    die('<div style="color: red; padding: 20px;">Access denied. Please log in first.</div>');
}

// Set execution time limit for large datasets
set_time_limit(300);

echo "<!DOCTYPE html><html><head><title>Fix Leave Card Sync</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .btn { padding: 10px 20px; margin: 5px; cursor: pointer; }
    .btn-primary { background-color: #007bff; color: white; border: none; }
    .btn-danger { background-color: #dc3545; color: white; border: none; }
    pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
</style></head><body>";

echo "<h1>🔧 Leave Card Sync Fix Tool</h1>";
echo "<p>This tool identifies and fixes mismatched leave card entries.</p>";

// Check for action parameter
$action = $_GET['action'] ?? 'preview';

try {
    // ====================================
    // STEP 1: Find mismatched records
    // ====================================
    echo "<h2>📋 Analysis Results</h2>";
    
    // Query to find leave applications with linked leave card entries that have mismatched values
    $mismatch_query = $conn->prepare("
        SELECT 
            la.id as app_id,
            la.personnel_id,
            la.leave_type,
            la.number_of_days,
            la.inclusive_date_from,
            la.inclusive_date_to,
            la.less_application_vl as app_vl_with_pay,
            la.less_application_sl as app_sl_with_pay,
            la.less_application_vl_without_pay as app_vl_without_pay,
            la.less_application_sl_without_pay as app_sl_without_pay,
            la.leave_card_entry_id,
            la.status,
            lc.id as lc_id,
            lc.vl_with_pay as lc_vl_with_pay,
            lc.sl_with_pay as lc_sl_with_pay,
            lc.vl_without_pay as lc_vl_without_pay,
            lc.sl_without_pay as lc_sl_without_pay,
            lc.particulars,
            p.fname, p.lname
        FROM leave_applications la
        LEFT JOIN leave_card lc ON la.leave_card_entry_id = lc.id
        LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
        WHERE la.leave_card_entry_id IS NOT NULL
        AND (
            ROUND(COALESCE(la.less_application_vl, 0), 3) != ROUND(COALESCE(lc.vl_with_pay, 0), 3)
            OR ROUND(COALESCE(la.less_application_sl, 0), 3) != ROUND(COALESCE(lc.sl_with_pay, 0), 3)
            OR ROUND(COALESCE(la.less_application_vl_without_pay, 0), 3) != ROUND(COALESCE(lc.vl_without_pay, 0), 3)
            OR ROUND(COALESCE(la.less_application_sl_without_pay, 0), 3) != ROUND(COALESCE(lc.sl_without_pay, 0), 3)
        )
        ORDER BY la.id DESC
    ");
    $mismatch_query->execute();
    $mismatched = $mismatch_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Also find approved applications WITHOUT leave card entries
    $missing_query = $conn->prepare("
        SELECT 
            la.id as app_id,
            la.personnel_id,
            la.leave_type,
            la.number_of_days,
            la.inclusive_date_from,
            la.inclusive_date_to,
            la.less_application_vl as app_vl_with_pay,
            la.less_application_sl as app_sl_with_pay,
            la.less_application_vl_without_pay as app_vl_without_pay,
            la.less_application_sl_without_pay as app_sl_without_pay,
            la.status,
            p.fname, p.lname
        FROM leave_applications la
        LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
        WHERE la.leave_card_entry_id IS NULL
        AND la.status = 'approved'
        ORDER BY la.id DESC
    ");
    $missing_query->execute();
    $missing = $missing_query->fetchAll(PDO::FETCH_ASSOC);
    
    $total_mismatched = count($mismatched);
    $total_missing = count($missing);
    
    echo "<div class='info'>";
    echo "<p><strong>📊 Summary:</strong></p>";
    echo "<ul>";
    echo "<li>Mismatched leave card entries: <strong>$total_mismatched</strong></li>";
    echo "<li>Approved applications without leave card: <strong>$total_missing</strong></li>";
    echo "</ul>";
    echo "</div>";
    
    // ====================================
    // STEP 2: Display mismatched records
    // ====================================
    if ($total_mismatched > 0) {
        echo "<h3>⚠️ Mismatched Records (Leave Card vs Application)</h3>";
        echo "<table>";
        echo "<tr>
            <th>App ID</th>
            <th>Personnel</th>
            <th>Leave Type</th>
            <th>Days</th>
            <th>App VL w/Pay</th>
            <th>LC VL w/Pay</th>
            <th>App SL w/Pay</th>
            <th>LC SL w/Pay</th>
            <th>Status</th>
        </tr>";
        
        foreach ($mismatched as $row) {
            $vl_mismatch = floatval($row['app_vl_with_pay']) != floatval($row['lc_vl_with_pay']);
            $sl_mismatch = floatval($row['app_sl_with_pay']) != floatval($row['lc_sl_with_pay']);
            
            echo "<tr>";
            echo "<td>{$row['app_id']}</td>";
            echo "<td>{$row['fname']} {$row['lname']}</td>";
            echo "<td>{$row['leave_type']}</td>";
            echo "<td>{$row['number_of_days']}</td>";
            echo "<td" . ($vl_mismatch ? " class='warning'" : "") . ">" . number_format($row['app_vl_with_pay'], 3) . "</td>";
            echo "<td" . ($vl_mismatch ? " class='error'" : "") . ">" . number_format($row['lc_vl_with_pay'], 3) . "</td>";
            echo "<td" . ($sl_mismatch ? " class='warning'" : "") . ">" . number_format($row['app_sl_with_pay'], 3) . "</td>";
            echo "<td" . ($sl_mismatch ? " class='error'" : "") . ">" . number_format($row['lc_sl_with_pay'], 3) . "</td>";
            echo "<td>{$row['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // ====================================
    // STEP 3: Display missing records
    // ====================================
    if ($total_missing > 0) {
        echo "<h3>❌ Approved Applications Missing Leave Card Entry</h3>";
        echo "<table>";
        echo "<tr>
            <th>App ID</th>
            <th>Personnel</th>
            <th>Leave Type</th>
            <th>Days</th>
            <th>Dates</th>
            <th>VL w/Pay</th>
            <th>SL w/Pay</th>
        </tr>";
        
        foreach ($missing as $row) {
            echo "<tr>";
            echo "<td>{$row['app_id']}</td>";
            echo "<td>{$row['fname']} {$row['lname']}</td>";
            echo "<td>{$row['leave_type']}</td>";
            echo "<td>{$row['number_of_days']}</td>";
            echo "<td>{$row['inclusive_date_from']} to {$row['inclusive_date_to']}</td>";
            echo "<td>" . number_format($row['app_vl_with_pay'], 3) . "</td>";
            echo "<td>" . number_format($row['app_sl_with_pay'], 3) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // ====================================
    // STEP 4: Fix action
    // ====================================
    if ($action === 'fix' && ($total_mismatched > 0 || $total_missing > 0)) {
        echo "<h2>🔄 Applying Fixes...</h2>";
        
        $fixed_count = 0;
        $created_count = 0;
        $errors = [];
        
        // Fix mismatched records
        foreach ($mismatched as $row) {
            try {
                $update_lc = $conn->prepare("UPDATE leave_card SET
                    vl_with_pay = :vl_with_pay,
                    vl_without_pay = :vl_without_pay,
                    sl_with_pay = :sl_with_pay,
                    sl_without_pay = :sl_without_pay
                WHERE id = :id");
                
                $update_lc->execute([
                    ':vl_with_pay' => floatval($row['app_vl_with_pay']),
                    ':vl_without_pay' => floatval($row['app_vl_without_pay']),
                    ':sl_with_pay' => floatval($row['app_sl_with_pay']),
                    ':sl_without_pay' => floatval($row['app_sl_without_pay']),
                    ':id' => $row['lc_id']
                ]);
                
                $fixed_count++;
                echo "<p class='success'>✓ Fixed leave card ID {$row['lc_id']} for application ID {$row['app_id']}</p>";
            } catch (PDOException $e) {
                $errors[] = "Error fixing leave card {$row['lc_id']}: " . $e->getMessage();
            }
        }
        
        // Create missing leave card entries for approved applications
        // Include the createLeaveCardEntry function
        require_once 'save_leave_application.php';
        
        foreach ($missing as $row) {
            try {
                // Use the existing createLeaveCardEntry function if available, otherwise create inline
                $period_date = new DateTime($row['inclusive_date_from']);
                $period_from = $period_date->format('Y-m-01');
                $period_to = $period_date->format('Y-m-t');
                
                // Determine particulars
                $particulars = $row['leave_type'];
                if (strpos($row['leave_type'], 'Vacation') !== false) {
                    $particulars = 'Vacation Leave';
                } elseif (strpos($row['leave_type'], 'Sick') !== false) {
                    $particulars = 'Sick Leave';
                }
                
                // Determine if special leave
                $special_leave_types = [
                    'Maternity Leave', 'Paternity Leave', 'Special Privilege Leave',
                    'Solo Parent Leave', 'Study Leave', '10-Day VAWC Leave',
                    'Rehabilitation Privilege', 'Special Leave Benefits for Women',
                    'Special Emergency (Calamity) Leave', 'Adoption Leave'
                ];
                $is_special = in_array($row['leave_type'], $special_leave_types) ? 1 : 0;
                
                $insert_lc = $conn->prepare("INSERT INTO leave_card (
                    personnel_id, period_from, period_to, particulars,
                    vl_earned, vl_with_pay, vl_without_pay,
                    sl_earned, sl_with_pay, sl_without_pay,
                    is_special_leave, date_from, date_to, number_of_days, created_from_application
                ) VALUES (
                    :personnel_id, :period_from, :period_to, :particulars,
                    0, :vl_with_pay, :vl_without_pay,
                    0, :sl_with_pay, :sl_without_pay,
                    :is_special_leave, :date_from, :date_to, :number_of_days, 1
                )");
                
                $insert_lc->execute([
                    ':personnel_id' => $row['personnel_id'],
                    ':period_from' => $period_from,
                    ':period_to' => $period_to,
                    ':particulars' => $particulars,
                    ':vl_with_pay' => floatval($row['app_vl_with_pay']),
                    ':vl_without_pay' => floatval($row['app_vl_without_pay']),
                    ':sl_with_pay' => floatval($row['app_sl_with_pay']),
                    ':sl_without_pay' => floatval($row['app_sl_without_pay']),
                    ':is_special_leave' => $is_special,
                    ':date_from' => $row['inclusive_date_from'],
                    ':date_to' => $row['inclusive_date_to'],
                    ':number_of_days' => $row['number_of_days']
                ]);
                
                $new_lc_id = $conn->lastInsertId();
                
                // Link to application
                $link_stmt = $conn->prepare("UPDATE leave_applications SET leave_card_entry_id = :lc_id WHERE id = :app_id");
                $link_stmt->execute([':lc_id' => $new_lc_id, ':app_id' => $row['app_id']]);
                
                $created_count++;
                echo "<p class='success'>✓ Created leave card ID $new_lc_id for application ID {$row['app_id']}</p>";
            } catch (PDOException $e) {
                $errors[] = "Error creating leave card for app {$row['app_id']}: " . $e->getMessage();
            }
        }
        
        // Summary
        echo "<h3>📊 Fix Summary</h3>";
        echo "<p class='success'><strong>Fixed mismatched entries:</strong> $fixed_count</p>";
        echo "<p class='success'><strong>Created missing entries:</strong> $created_count</p>";
        
        if (count($errors) > 0) {
            echo "<h4 class='error'>Errors:</h4>";
            foreach ($errors as $err) {
                echo "<p class='error'>$err</p>";
            }
        }
        
        echo "<p><a href='fix_leave_card_sync.php'>← Run analysis again</a></p>";
        
    } else if ($total_mismatched > 0 || $total_missing > 0) {
        // Show fix button
        echo "<h2>🚀 Ready to Fix?</h2>";
        echo "<p>Click the button below to sync all leave card entries with their corresponding leave applications.</p>";
        echo "<p class='warning'><strong>⚠️ Warning:</strong> This will modify database records. Make sure you have a backup!</p>";
        echo "<form method='GET'>";
        echo "<input type='hidden' name='action' value='fix'>";
        echo "<button type='submit' class='btn btn-primary'>🔧 Apply Fixes ($total_mismatched mismatched, $total_missing missing)</button>";
        echo "</form>";
    } else {
        echo "<div class='success'>";
        echo "<h3>✅ All Good!</h3>";
        echo "<p>No mismatched or missing leave card entries found. All records are in sync.</p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<h3>Database Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Script executed at: " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p><a href='home.php'>← Back to Home</a></p>";
echo "</body></html>";
?>
