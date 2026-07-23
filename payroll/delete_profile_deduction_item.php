<?php
/**
 * Delete Profile Deduction Item Handler
 * Removes a deduction item from a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['profile_deduction_id'])) {
        throw new Exception('Deduction ID is required');
    }
    
    $profile_deduction_id = intval($_POST['profile_deduction_id']);
    
    // Check if deduction item exists
    $check = $conn->prepare("SELECT profile_deduction_id FROM pr_tbl_payroll_profile_deductions WHERE profile_deduction_id = :id");
    $check->execute([':id' => $profile_deduction_id]);
    if (!$check->fetch()) {
        throw new Exception('Deduction item not found');
    }
    
    // Delete the deduction item
    $delete = $conn->prepare("DELETE FROM pr_tbl_payroll_profile_deductions WHERE profile_deduction_id = :id");
    $delete->execute([':id' => $profile_deduction_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Deduction item removed successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in delete_profile_deduction_item.php: " . $e->getMessage());
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
