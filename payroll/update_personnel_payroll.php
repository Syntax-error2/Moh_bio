<?php
/**
 * Update Personnel Payroll Details
 * Handles inline editing of individual personnel payroll amounts
 */

include('dbcon.php');
include('session.php');

header('Content-Type: application/json');

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$detail_id = isset($_POST['detail_id']) ? intval($_POST['detail_id']) : 0;
$income_items = isset($_POST['income_items']) ? json_decode($_POST['income_items'], true) : [];
$deduction_items = isset($_POST['deduction_items']) ? json_decode($_POST['deduction_items'], true) : [];

if (!$detail_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid detail ID']);
    exit();
}

try {
    $conn->beginTransaction();
    
    // Verify that this is a draft run
    $status_check = $conn->prepare("
        SELECT pr.run_status 
        FROM pr_tbl_payroll_run_details prd
        INNER JOIN pr_tbl_payroll_runs pr ON prd.run_id = pr.run_id
        WHERE prd.detail_id = :detail_id
    ");
    $status_check->execute([':detail_id' => $detail_id]);
    $run_status = $status_check->fetchColumn();
    
    if ($run_status !== 'draft') {
        throw new Exception('Only draft payroll runs can be edited');
    }
    
    // Update income items
    if (!empty($income_items)) {
        $income_update = $conn->prepare("
            UPDATE pr_tbl_payroll_run_income 
            SET amount = :amount
            WHERE run_income_id = :income_id
        ");
        
        foreach ($income_items as $item) {
            $income_update->execute([
                ':income_id' => $item['income_id'],
                ':amount' => $item['amount']
            ]);
        }
    }
    
    // Update deduction items
    if (!empty($deduction_items)) {
        $deduction_update = $conn->prepare("
            UPDATE pr_tbl_payroll_run_deductions 
            SET employee_amount = :employee_amount,
                employer_amount = :employer_amount
            WHERE run_deduction_id = :deduction_id
        ");
        
        foreach ($deduction_items as $item) {
            $deduction_update->execute([
                ':deduction_id' => $item['deduction_id'],
                ':employee_amount' => $item['employee_amount'],
                ':employer_amount' => $item['employer_amount']
            ]);
        }
    }
    
    // Recalculate totals for this personnel
    // Get updated income total
    $income_total_query = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM pr_tbl_payroll_run_income
        WHERE detail_id = :detail_id
    ");
    $income_total_query->execute([':detail_id' => $detail_id]);
    $new_gross_pay = $income_total_query->fetchColumn();
    
    // Get updated deduction totals
    $deduction_total_query = $conn->prepare("
        SELECT 
            COALESCE(SUM(employee_amount), 0) as employee_total,
            COALESCE(SUM(employer_amount), 0) as employer_total
        FROM pr_tbl_payroll_run_deductions
        WHERE detail_id = :detail_id
    ");
    $deduction_total_query->execute([':detail_id' => $detail_id]);
    $deduction_totals = $deduction_total_query->fetch(PDO::FETCH_ASSOC);
    
    $new_total_deductions = $deduction_totals['employee_total'];
    $new_employer_share = $deduction_totals['employer_total'];
    $new_net_pay = $new_gross_pay - $new_total_deductions;
    
    // Update personnel detail record
    $detail_update = $conn->prepare("
        UPDATE pr_tbl_payroll_run_details 
        SET gross_pay = :gross_pay,
            total_deductions = :total_deductions,
            total_employer_share = :total_employer_share,
            net_pay = :net_pay,
            updated_at = NOW()
        WHERE detail_id = :detail_id
    ");
    
    $detail_update->execute([
        ':detail_id' => $detail_id,
        ':gross_pay' => $new_gross_pay,
        ':total_deductions' => $new_total_deductions,
        ':total_employer_share' => $new_employer_share,
        ':net_pay' => $new_net_pay
    ]);
    
    // Update payroll run totals
    // Get run_id
    $run_query = $conn->prepare("SELECT run_id FROM pr_tbl_payroll_run_details WHERE detail_id = :detail_id");
    $run_query->execute([':detail_id' => $detail_id]);
    $run_id = $run_query->fetchColumn();
    
    // Recalculate run totals
    $run_totals_query = $conn->prepare("
        SELECT 
            COUNT(*) as total_personnel,
            COALESCE(SUM(gross_pay), 0) as total_gross,
            COALESCE(SUM(total_deductions), 0) as total_deductions,
            COALESCE(SUM(total_employer_share), 0) as total_employer_share,
            COALESCE(SUM(net_pay), 0) as total_net_pay
        FROM pr_tbl_payroll_run_details
        WHERE run_id = :run_id
    ");
    $run_totals_query->execute([':run_id' => $run_id]);
    $run_totals = $run_totals_query->fetch(PDO::FETCH_ASSOC);
    
    // Update run record
    $run_update = $conn->prepare("
        UPDATE pr_tbl_payroll_runs 
        SET total_personnel = :total_personnel,
            total_gross = :total_gross,
            total_deductions = :total_deductions,
            total_employer_share = :total_employer_share,
            total_net_pay = :total_net_pay,
            updated_at = NOW()
        WHERE run_id = :run_id
    ");
    
    $run_update->execute([
        ':run_id' => $run_id,
        ':total_personnel' => $run_totals['total_personnel'],
        ':total_gross' => $run_totals['total_gross'],
        ':total_deductions' => $run_totals['total_deductions'],
        ':total_employer_share' => $run_totals['total_employer_share'],
        ':total_net_pay' => $run_totals['total_net_pay']
    ]);
    
    // Log the update
    $audit_log = $conn->prepare("
        INSERT INTO pr_tbl_payroll_audit_log 
            (run_id, action_type, table_name, record_id, performed_by, action_details, performed_at)
        VALUES 
            (:run_id, 'update', 'pr_tbl_payroll_run_details', :record_id, :user_id, :details, NOW())
    ");
    
    $audit_log->execute([
        ':run_id' => $run_id,
        ':record_id' => $detail_id,
        ':user_id' => $session_id,
        ':details' => json_encode([
            'action' => 'inline_edit',
            'income_items_updated' => count($income_items),
            'deduction_items_updated' => count($deduction_items),
            'new_gross_pay' => $new_gross_pay,
            'new_net_pay' => $new_net_pay
        ])
    ]);
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Personnel payroll updated successfully',
        'data' => [
            'gross_pay' => $new_gross_pay,
            'total_deductions' => $new_total_deductions,
            'employer_share' => $new_employer_share,
            'net_pay' => $new_net_pay
        ]
    ]);
    
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Personnel payroll update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
