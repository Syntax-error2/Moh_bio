<?php
/**
 * Update Payroll Run
 * Process form submission from edit_payroll_run.php
 */

include('session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_run_info'])) {
    try {
        $run_id = intval($_POST['run_id']);
        $run_name = trim($_POST['run_name']);
        $run_type = $_POST['run_type'];
        $pay_period_start = $_POST['pay_period_start'];
        $pay_period_end = $_POST['pay_period_end'];
        $payment_date = $_POST['payment_date'] ?? null;
        $notes = trim($_POST['notes'] ?? '');
        
        // Validate
        if (empty($run_name) || empty($pay_period_start) || empty($pay_period_end)) {
            throw new Exception('All required fields must be filled');
        }
        
        if (strtotime($pay_period_start) > strtotime($pay_period_end)) {
            throw new Exception('Pay period start must be before or equal to end date');
        }
        
        // Check if run is still in draft status
        $status_check = $conn->prepare("SELECT run_status FROM pr_tbl_payroll_runs WHERE run_id = :run_id");
        $status_check->execute([':run_id' => $run_id]);
        $current_status = $status_check->fetchColumn();
        
        if ($current_status !== 'draft') {
            throw new Exception('Cannot edit payroll run with status: ' . $current_status);
        }
        
        // Update run info
        $update_query = $conn->prepare("
            UPDATE pr_tbl_payroll_runs
            SET run_name = :run_name,
                run_type = :run_type,
                pay_period_start = :pay_period_start,
                pay_period_end = :pay_period_end,
                payment_date = :payment_date,
                notes = :notes,
                updated_at = NOW()
            WHERE run_id = :run_id
        ");
        
        $update_query->execute([
            ':run_name' => $run_name,
            ':run_type' => $run_type,
            ':pay_period_start' => $pay_period_start,
            ':pay_period_end' => $pay_period_end,
            ':payment_date' => $payment_date,
            ':notes' => $notes,
            ':run_id' => $run_id
        ]);
        
        header('Location: edit_payroll_run.php?run_id=' . $run_id . '&success=' . urlencode('Payroll run updated successfully'));
        exit();
        
    } catch (Exception $e) {
        $run_id = $_POST['run_id'] ?? '';
        header('Location: edit_payroll_run.php?run_id=' . $run_id . '&error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: list_payroll_history.php');
    exit();
}
