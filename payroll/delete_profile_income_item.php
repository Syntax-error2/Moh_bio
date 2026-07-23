<?php
/**
 * Delete Profile Income Item Handler
 * Removes an income item from a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['profile_income_id'])) {
        throw new Exception('Income ID is required');
    }
    
    $profile_income_id = intval($_POST['profile_income_id']);
    
    // Check if income item exists
    $check = $conn->prepare("SELECT profile_income_id FROM pr_tbl_payroll_profile_income WHERE profile_income_id = :id");
    $check->execute([':id' => $profile_income_id]);
    if (!$check->fetch()) {
        throw new Exception('Income item not found');
    }
    
    // Delete the income item
    $delete = $conn->prepare("DELETE FROM pr_tbl_payroll_profile_income WHERE profile_income_id = :id");
    $delete->execute([':id' => $profile_income_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Income item removed successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in delete_profile_income_item.php: " . $e->getMessage());
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
