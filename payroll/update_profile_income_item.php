<?php
/**
 * Update Profile Income Item Handler
 * Updates an income item in a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['profile_income_id'])) {
        throw new Exception('Income ID is required');
    }
    
    $profile_income_id = intval($_POST['profile_income_id']);
    $default_amount = !empty($_POST['default_amount']) ? floatval($_POST['default_amount']) : null;
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $is_mandatory = isset($_POST['is_mandatory']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Check if income item exists
    $check = $conn->prepare("SELECT profile_income_id FROM pr_tbl_payroll_profile_income WHERE profile_income_id = :id");
    $check->execute([':id' => $profile_income_id]);
    if (!$check->fetch()) {
        throw new Exception('Income item not found');
    }
    
    // Update the income item
    $update = $conn->prepare("
        UPDATE pr_tbl_payroll_profile_income 
        SET default_amount = :default_amount,
            is_mandatory = :is_mandatory,
            display_order = :display_order
        WHERE profile_income_id = :id
    ");
    
    $update->execute([
        ':default_amount' => $default_amount,
        ':is_mandatory' => $is_mandatory,
        ':display_order' => $sort_order,
        ':id' => $profile_income_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Income item updated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in update_profile_income_item.php: " . $e->getMessage());
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
