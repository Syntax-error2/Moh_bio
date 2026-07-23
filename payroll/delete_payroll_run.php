<?php
/**
 * Delete Payroll Run
 * Permanently deletes a draft payroll run and all related data
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    if (empty($_POST['run_id'])) {
        throw new Exception('Run ID is required');
    }
    
    $run_id = intval($_POST['run_id']);
    
    // Check if run exists and is in draft status
    $check_query = $conn->prepare("
        SELECT run_id, run_name, run_status, total_personnel
        FROM pr_tbl_payroll_runs
        WHERE run_id = :run_id
    ");
    $check_query->execute([':run_id' => $run_id]);
    $run = $check_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$run) {
        throw new Exception('Payroll run not found');
    }
    
    // Only allow deletion of draft runs
    if ($run['run_status'] !== 'draft') {
        throw new Exception('Only draft payroll runs can be deleted. Current status: ' . $run['run_status']);
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    // Delete related records in correct order (child tables first)
    
    // 1. Delete payroll run deductions
    $delete_deductions = $conn->prepare("
        DELETE FROM pr_tbl_payroll_run_deductions
        WHERE run_id = :run_id
    ");
    $delete_deductions->execute([':run_id' => $run_id]);
    $deleted_deductions = $delete_deductions->rowCount();
    
    // 2. Delete payroll run income
    $delete_income = $conn->prepare("
        DELETE FROM pr_tbl_payroll_run_income
        WHERE run_id = :run_id
    ");
    $delete_income->execute([':run_id' => $run_id]);
    $deleted_income = $delete_income->rowCount();
    
    // 3. Delete payroll run details (personnel records)
    $delete_details = $conn->prepare("
        DELETE FROM pr_tbl_payroll_run_details
        WHERE run_id = :run_id
    ");
    $delete_details->execute([':run_id' => $run_id]);
    $deleted_details = $delete_details->rowCount();
    
    // 4. Delete audit logs (optional, for clean history)
    $delete_audit = $conn->prepare("
        DELETE FROM pr_tbl_payroll_audit_log
        WHERE run_id = :run_id
    ");
    $delete_audit->execute([':run_id' => $run_id]);
    $deleted_audit = $delete_audit->rowCount();
    
    // 5. Finally, delete the main payroll run record
    $delete_run = $conn->prepare("
        DELETE FROM pr_tbl_payroll_runs
        WHERE run_id = :run_id
    ");
    $delete_run->execute([':run_id' => $run_id]);
    
    // Commit transaction
    $conn->commit();
    
    // Log the deletion
    error_log(sprintf(
        "Payroll run deleted: ID=%d, Name='%s', Personnel=%d, User=%d, Details=%d, Income=%d, Deductions=%d, Audit=%d",
        $run_id,
        $run['run_name'],
        $run['total_personnel'],
        $session_id ?? 0,
        $deleted_details,
        $deleted_income,
        $deleted_deductions,
        $deleted_audit
    ));
    
    echo json_encode([
        'success' => true,
        'message' => 'Payroll run deleted successfully',
        'details' => [
            'run_id' => $run_id,
            'run_name' => $run['run_name'],
            'deleted_personnel' => $deleted_details,
            'deleted_income_records' => $deleted_income,
            'deleted_deduction_records' => $deleted_deductions,
            'deleted_audit_logs' => $deleted_audit
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Error deleting payroll run: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
