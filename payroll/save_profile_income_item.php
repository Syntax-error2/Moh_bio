<?php
/**
 * Save Profile Income Item Handler
 * Adds an income item to a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['profile_id']) || empty($_POST['income_id'])) {
        throw new Exception('Profile ID and Income ID are required');
    }
    
    $profile_id = intval($_POST['profile_id']);
    $income_id = intval($_POST['income_id']);
    $default_amount = !empty($_POST['default_amount']) ? floatval($_POST['default_amount']) : null;
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $calculation_method = $_POST['calculation_method'] ?? 'personnel_specific';
    $formula = $_POST['formula'] ?? null;
    $is_mandatory = isset($_POST['is_mandatory']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Map calculation_method to amount_calculation enum
    $amount_calculation_map = [
        'fixed' => 'fixed',
        'percentage' => 'percentage',
        'formula' => 'formula',
        'manual' => 'personnel_specific'
    ];
    $amount_calculation = $amount_calculation_map[$calculation_method] ?? 'personnel_specific';
    
    // Check if profile exists
    $check_profile = $conn->prepare("SELECT profile_id FROM pr_tbl_payroll_profiles WHERE profile_id = :profile_id");
    $check_profile->execute([':profile_id' => $profile_id]);
    if (!$check_profile->fetch()) {
        throw new Exception('Profile not found');
    }
    
    // Check if income item exists
    $check_income = $conn->prepare("SELECT income_id FROM pr_tbl_income WHERE income_id = :income_id AND is_deleted = 0");
    $check_income->execute([':income_id' => $income_id]);
    if (!$check_income->fetch()) {
        throw new Exception('Income item not found');
    }
    
    // Check if income item is already in this profile
    $check_duplicate = $conn->prepare("
        SELECT profile_income_id 
        FROM pr_tbl_payroll_profile_income 
        WHERE profile_id = :profile_id AND income_id = :income_id
    ");
    $check_duplicate->execute([
        ':profile_id' => $profile_id,
        ':income_id' => $income_id
    ]);
    if ($check_duplicate->fetch()) {
        throw new Exception('This income item is already in the profile');
    }
    
    // Insert the income item
    $insert = $conn->prepare("
        INSERT INTO pr_tbl_payroll_profile_income 
        (profile_id, income_id, default_amount, amount_calculation, calculation_value, 
         is_mandatory, display_order, notes, created_at)
        VALUES 
        (:profile_id, :income_id, :default_amount, :amount_calculation, :calculation_value,
         :is_mandatory, :display_order, :notes, NOW())
    ");
    
    // Prepare calculation value based on method
    $calculation_value = null;
    $notes = null;
    
    if ($calculation_method === 'percentage' && $default_amount !== null) {
        $calculation_value = $default_amount / 100; // Store as decimal
    } elseif ($calculation_method === 'formula' && !empty($formula)) {
        $notes = 'Formula: ' . $formula;
        $calculation_value = null;
    }
    
    $insert->execute([
        ':profile_id' => $profile_id,
        ':income_id' => $income_id,
        ':default_amount' => $default_amount,
        ':amount_calculation' => $amount_calculation,
        ':calculation_value' => $calculation_value,
        ':is_mandatory' => $is_mandatory,
        ':display_order' => $sort_order,
        ':notes' => $notes
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Income item added successfully',
        'profile_income_id' => $conn->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in save_profile_income_item.php: " . $e->getMessage());
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
