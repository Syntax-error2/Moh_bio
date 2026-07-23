<?php
/**
 * Fix Leave Application Balances
 * 
 * This script recalculates and updates the balance_vl and balance_sl values
 * for existing leave applications to fix the 7.A CERTIFICATION OF LEAVE CREDITS data.
 * 
 * IMPORTANT: Only run this once to fix existing data.
 * New applications will automatically calculate correct values.
 */

session_start();
require_once 'dbcon.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    die("Access denied. Please login first.");
}

echo "<html><head><title>Fix Leave Application Balances</title>";
echo "<link href='css/style.css' rel='stylesheet'>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; } table { border-collapse: collapse; margin-top: 20px; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }</style>";
echo "</head><body>";
echo "<h2>Fix Leave Application Balances</h2>";

if (!isset($_GET['confirm'])) {
    // Show preview of what will be updated
    echo "<p class='info'>This will recalculate balance values for all leave applications.</p>";
    echo "<p class='info'>The formula is: <strong>Balance = Total Earned - Less This Application</strong></p>";
    
    // Get all applications with their current values
    $stmt = $conn->prepare("SELECT id, personnel_id, leave_type, number_of_days, 
                                   total_earned_vl, total_earned_sl, 
                                   less_application_vl, less_application_sl,
                                   balance_vl, balance_sl, status
                            FROM leave_applications 
                            ORDER BY id DESC LIMIT 50");
    $stmt->execute();
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Preview (Last 50 applications):</h3>";
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Leave Type</th>
            <th>Days</th>
            <th>Total Earned VL</th>
            <th>Less VL</th>
            <th>Current Balance VL</th>
            <th>Correct Balance VL</th>
            <th>Status</th>
          </tr>";
    
    foreach ($applications as $app) {
        $correct_balance_vl = round(floatval($app['total_earned_vl']) - floatval($app['less_application_vl']), 3);
        $correct_balance_sl = round(floatval($app['total_earned_sl']) - floatval($app['less_application_sl']), 3);
        
        $needs_fix = ($app['balance_vl'] != $correct_balance_vl || $app['balance_sl'] != $correct_balance_sl);
        $row_class = $needs_fix ? 'error' : 'success';
        
        echo "<tr class='{$row_class}'>";
        echo "<td>{$app['id']}</td>";
        echo "<td>{$app['leave_type']}</td>";
        echo "<td>{$app['number_of_days']}</td>";
        echo "<td>{$app['total_earned_vl']}</td>";
        echo "<td>{$app['less_application_vl']}</td>";
        echo "<td>{$app['balance_vl']}</td>";
        echo "<td><strong>{$correct_balance_vl}</strong></td>";
        echo "<td>{$app['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><br>";
    echo "<p><strong>Red rows</strong> need balance fix. <strong>Green rows</strong> are correct.</p>";
    echo "<p><a href='?confirm=1' onclick='return confirm(\"Are you sure you want to update all leave application balances?\")' class='btn btn-warning'>Click here to fix all balances</a></p>";
    echo "<p><a href='leave_application.php' class='btn btn-secondary'>Cancel and go back</a></p>";
    
} else {
    // Perform the fix
    echo "<h3>Updating balances...</h3>";
    
    try {
        $conn->beginTransaction();
        
        // Get all applications
        $stmt = $conn->prepare("SELECT id, total_earned_vl, total_earned_sl, 
                                       less_application_vl, less_application_sl
                                FROM leave_applications");
        $stmt->execute();
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $update_stmt = $conn->prepare("UPDATE leave_applications 
                                       SET balance_vl = :balance_vl, balance_sl = :balance_sl 
                                       WHERE id = :id");
        
        $updated_count = 0;
        foreach ($applications as $app) {
            // Calculate correct balances
            $balance_vl = round(floatval($app['total_earned_vl']) - floatval($app['less_application_vl']), 3);
            $balance_sl = round(floatval($app['total_earned_sl']) - floatval($app['less_application_sl']), 3);
            
            $update_stmt->execute([
                ':balance_vl' => $balance_vl,
                ':balance_sl' => $balance_sl,
                ':id' => $app['id']
            ]);
            
            $updated_count++;
        }
        
        $conn->commit();
        
        echo "<p class='success'>✓ Successfully updated {$updated_count} leave applications!</p>";
        echo "<p>All balance values have been recalculated using the formula:</p>";
        echo "<p><code>Balance = Total Earned - Less This Application</code></p>";
        echo "<br>";
        echo "<p><a href='leave_application.php' class='btn btn-primary'>Go back to Leave Applications</a></p>";
        
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
}

echo "</body></html>";
?>
