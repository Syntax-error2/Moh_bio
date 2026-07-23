<?php
// Start output buffering to prevent header issues
ob_start();

// DEBUG: Enable error display temporarily
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DEBUG: Log that we reached this file
error_log("=== save_personnel_income.php accessed ===");
error_log("POST data: " . print_r($_POST, true));
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);

include('session.php');

// Check if this is a POST request (form submission)
if($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['save_personnel_income']) || isset($_POST['personnel_id']))) {
    
    try {
        // Get POST data
        $personnel_id = $_POST['personnel_id'] ?? '';
        $dept = $_POST['dept'] ?? '';
        $table_exists = isset($_POST['table_exists']) && $_POST['table_exists'] === '1';
        
        // Validate required fields
        if (empty($personnel_id)) {
            ob_end_clean(); // Clear output buffer
            header('Location: list_personnel.php?dept=' . urlencode($dept) . '&error=' . urlencode('Personnel ID is required'));
            exit();
        }
        
        // Check if table exists
        if (!$table_exists) {
            ob_end_clean(); // Clear output buffer
            header('Location: list_personnel_income.php?dept=' . urlencode($dept) . '&personnel_id=' . urlencode($personnel_id) . '&error=' . urlencode('Database table not created yet'));
            exit();
        }
        
        // Get income data arrays
        $income_ids = $_POST['income_id'] ?? [];
        $amounts = $_POST['amount_per_pay'] ?? [];
        
        // Validate arrays have same length
        if (count($income_ids) !== count($amounts)) {
            throw new Exception('Data mismatch: income IDs and amounts count do not match');
        }
        
        // Begin transaction
        $conn->beginTransaction();
        
        // First, deactivate all existing income entries for this personnel
        $deactivate_query = $conn->prepare("UPDATE pr_tbl_personnel_income 
                                           SET is_active = 0, 
                                               updated_at = NOW() 
                                           WHERE personnel_id = :personnel_id");
        $deactivate_query->execute([':personnel_id' => $personnel_id]);
        
        $inserted_count = 0;
        $updated_count = 0;
        
        // Process each income item
        for ($i = 0; $i < count($income_ids); $i++) {
            $income_id = $income_ids[$i];
            $amount = floatval($amounts[$i]);
            
            // Skip if amount is 0 or empty
            if ($amount <= 0) {
                continue;
            }
            
            // Check if record exists
            $check_query = $conn->prepare("SELECT personnel_income_id, is_active 
                                          FROM pr_tbl_personnel_income 
                                          WHERE personnel_id = :personnel_id 
                                            AND income_id = :income_id");
            $check_query->execute([
                ':personnel_id' => $personnel_id,
                ':income_id' => $income_id
            ]);
            
            $existing = $check_query->fetch();
            
            if ($existing) {
                // Update existing record
                $update_query = $conn->prepare("UPDATE pr_tbl_personnel_income 
                                               SET amount_per_pay = :amount,
                                                   is_active = 1,
                                                   updated_at = NOW(),
                                                   user_id = :user_id
                                               WHERE personnel_income_id = :personnel_income_id");
                $update_query->execute([
                    ':amount' => $amount,
                    ':user_id' => $session_id,
                    ':personnel_income_id' => $existing['personnel_income_id']
                ]);
                $updated_count++;
            } else {
                // Insert new record
                $insert_query = $conn->prepare("INSERT INTO pr_tbl_personnel_income 
                                               (personnel_id, income_id, amount_per_pay, is_active, user_id, created_at) 
                                               VALUES 
                                               (:personnel_id, :income_id, :amount, 1, :user_id, NOW())");
                $insert_query->execute([
                    ':personnel_id' => $personnel_id,
                    ':income_id' => $income_id,
                    ':amount' => $amount,
                    ':user_id' => $session_id
                ]);
                $inserted_count++;
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Clear output buffer before redirect
        ob_end_clean();
        
        // Build success URL in PHP
        $redirect_url = 'list_personnel_income.php?dept=' . urlencode($dept) . 
                       '&personnel_id=' . urlencode($personnel_id) . 
                       '&success=1';
        
        // Redirect with success message
        header('Location: ' . $redirect_url);
        exit();
        
    } catch (PDOException $e) {
        // Rollback on database error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        error_log("Database error saving personnel income: " . $e->getMessage());
        
        // Clear output buffer before redirect
        ob_end_clean();
        
        // Build error URL in PHP
        $redirect_url = 'list_personnel_income.php?dept=' . urlencode($dept) . 
                       '&personnel_id=' . urlencode($personnel_id) . 
                       '&error=' . urlencode('Database error occurred');
        
        header('Location: ' . $redirect_url);
        exit();
        
    } catch (Exception $e) {
        // Rollback on general error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        error_log("Error saving personnel income: " . $e->getMessage());
        
        // Clear output buffer before redirect
        ob_end_clean();
        
        // Build error URL in PHP
        $redirect_url = 'list_personnel_income.php?dept=' . urlencode($dept) . 
                       '&personnel_id=' . urlencode($personnel_id) . 
                       '&error=' . urlencode($e->getMessage());
        
        header('Location: ' . $redirect_url);
        exit();
    }
}

// If no POST data or fell through, redirect back with parameters
$dept = $_POST['dept'] ?? $_GET['dept'] ?? '';
$personnel_id = $_POST['personnel_id'] ?? $_GET['personnel_id'] ?? '';

ob_end_clean();

if ($personnel_id && $dept) {
    header('Location: list_personnel_income.php?dept=' . urlencode($dept) . '&personnel_id=' . urlencode($personnel_id) . '&error=' . urlencode('Invalid form submission'));
} else {
    header('Location: list_personnel.php?error=' . urlencode('Invalid form submission'));
}
exit();
?>
