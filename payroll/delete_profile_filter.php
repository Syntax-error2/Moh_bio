<?php
/**
 * Delete Profile Filter Handler
 * Removes a personnel filter from a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['filter_id'])) {
        throw new Exception('Filter ID is required');
    }
    
    $filter_id = intval($_POST['filter_id']);
    
    // Check if filter exists
    $check = $conn->prepare("SELECT filter_id FROM pr_tbl_payroll_profile_filters WHERE filter_id = :id");
    $check->execute([':id' => $filter_id]);
    if (!$check->fetch()) {
        throw new Exception('Filter not found');
    }
    
    // Delete the filter
    $delete = $conn->prepare("DELETE FROM pr_tbl_payroll_profile_filters WHERE filter_id = :id");
    $delete->execute([':id' => $filter_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Filter removed successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in delete_profile_filter.php: " . $e->getMessage());
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
