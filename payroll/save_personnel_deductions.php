<?php
/**
 * Save Personnel Deductions
 * Handles CRUD operations for personnel-specific deductions
 */

// Start output buffering to prevent header issues
ob_start();

include('session.php');

if(isset($_POST['save_personnel_deductions'])) {
    
    try {
        $personnel_id = $_POST['personnel_id'] ?? '';
        $dept = $_POST['dept'] ?? '';
        $deduction_ids = $_POST['deduction_id'] ?? [];
        $employer_amounts = $_POST['employer_amtPP'] ?? [];
        $employee_amounts = $_POST['employee_amtPP'] ?? [];
        
        // Validate required fields
        if (empty($personnel_id)) {
            ?>
            <script>
            alert('Personnel ID is required.');
            window.history.back();
            </script>
            <?php
            exit();
        }
        
        // Verify personnel exists
        $verify_personnel = $conn->prepare("SELECT personnel_id FROM personnels WHERE personnel_id = :personnel_id LIMIT 1");
        $verify_personnel->execute([':personnel_id' => $personnel_id]);
        
        if ($verify_personnel->rowCount() == 0) {
            $redirect_url = 'list_personnel_deductions.php?dept=' . urlencode($dept) . '&personnel_id=' . urlencode($personnel_id) . '&error=' . urlencode('Invalid personnel ID');
            ?>
            <script>
            alert('Invalid personnel ID.');
            window.location = '<?php echo $redirect_url; ?>';
            </script>
            <?php
            exit();
        }
        
        // Check if the personnel_deductions table exists
        $table_check = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_deductions'");
        if ($table_check->rowCount() == 0) {
            ?>
            <script>
            alert('Database table not found.\n\nPlease create the pr_tbl_personnel_deductions table first by running:\npayroll/db/personnel_deductions_schema.sql');
            window.history.back();
            </script>
            <?php
            exit();
        }
        
        // Begin transaction for data integrity
        $conn->beginTransaction();
        
        // Delete existing deductions for this personnel
        $delete_stmt = $conn->prepare("DELETE FROM pr_tbl_personnel_deductions WHERE personnel_id = :personnel_id");
        $delete_stmt->execute([':personnel_id' => $personnel_id]);
        
        // Prepare insert statement (using user_id instead of created_by to match schema)
        $insert_stmt = $conn->prepare("INSERT INTO pr_tbl_personnel_deductions 
            (personnel_id, deduction_id, employer_amt_per_pay, employee_amt_per_pay, user_id, created_at) 
            VALUES 
            (:personnel_id, :deduction_id, :employer_amt, :employee_amt, :user_id, NOW())");
        
        $inserted_count = 0;
        
        // Insert new deductions
        for ($i = 0; $i < count($deduction_ids); $i++) {
            $deduction_id = $deduction_ids[$i];
            $employer_amt = floatval($employer_amounts[$i] ?? 0);
            $employee_amt = floatval($employee_amounts[$i] ?? 0);
            
            // Only insert if at least one amount is greater than 0
            if ($employer_amt > 0 || $employee_amt > 0) {
                $insert_stmt->execute([
                    ':personnel_id' => $personnel_id,
                    ':deduction_id' => $deduction_id,
                    ':employer_amt' => $employer_amt,
                    ':employee_amt' => $employee_amt,
                    ':user_id' => $session_id
                ]);
                
                $inserted_count++;
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Prepare redirect URL with success parameter
        $redirect_url = 'list_personnel_deductions.php?dept=' . urlencode($dept) . '&personnel_id=' . urlencode($personnel_id) . '&success=1';
        $success_message = 'Personnel deductions updated successfully.\\n' . $inserted_count . ' deduction(s) saved.';
        ?>
        <script>
        alert('<?php echo $success_message; ?>');
        window.location = '<?php echo $redirect_url; ?>';
        </script>
        <?php
        
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        error_log("Error saving personnel deductions: " . $e->getMessage());
        
        // Prepare error redirect URL
        $error_message = urlencode('An error occurred while saving deductions. Please try again.');
        $redirect_url = 'list_personnel_deductions.php?dept=' . urlencode($dept) . '&personnel_id=' . urlencode($personnel_id) . '&error=' . $error_message;
        ?>
        <script>
        alert('An error occurred while saving deductions. Please try again.\n\nError: <?php echo htmlspecialchars($e->getMessage(), ENT_QUOTES); ?>');
        window.location = '<?php echo $redirect_url; ?>';
        </script>
        <?php
        exit();
    }
    
} else {
    // Direct access not allowed - redirect to deductions list with error
    ob_end_clean(); // Clear output buffer
    $error_message = urlencode('Direct access not allowed. Please use the form to save deductions.');
    header('Location: list_personnel_deductions.php?error=' . $error_message);
    exit();
}
?>
