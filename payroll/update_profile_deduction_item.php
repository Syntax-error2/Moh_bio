<?php
/**
 * Update Profile Deduction Item Handler
 * Updates a deduction item in a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['profile_deduction_id'])) {
        throw new Exception('Deduction ID is required');
    }
    
    $profile_deduction_id = intval($_POST['profile_deduction_id']);
    $default_employee_amt = !empty($_POST['default_amount']) ? floatval($_POST['default_amount']) : null;
    $default_employer_amt = !empty($_POST['employer_amount']) ? floatval($_POST['employer_amount']) : null;
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $is_mandatory = isset($_POST['is_mandatory']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Check if deduction item exists
    $check = $conn->prepare("SELECT profile_deduction_id FROM pr_tbl_payroll_profile_deductions WHERE profile_deduction_id = :id");
    $check->execute([':id' => $profile_deduction_id]);
    if (!$check->fetch()) {
        throw new Exception('Deduction item not found');
    }
    
    // Update the deduction item
    $update = $conn->prepare("
        UPDATE pr_tbl_payroll_profile_deductions 
        SET default_employee_amt = :default_employee_amt,
            default_employer_amt = :default_employer_amt,
            is_mandatory = :is_mandatory,
            display_order = :display_order
        WHERE profile_deduction_id = :id
    ");
    
    $update->execute([
        ':default_employee_amt' => $default_employee_amt,
        ':default_employer_amt' => $default_employer_amt,
        ':is_mandatory' => $is_mandatory,
        ':display_order' => $sort_order,
        ':id' => $profile_deduction_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Deduction item updated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in update_profile_deduction_item.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
