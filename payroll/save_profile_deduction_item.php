<?php
/**
 * Save Profile Deduction Item Handler
 * Adds a deduction item to a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['profile_id']) || empty($_POST['deduction_id'])) {
        throw new Exception('Profile ID and Deduction ID are required');
    }
    
    $profile_id = intval($_POST['profile_id']);
    $deduction_id = intval($_POST['deduction_id']);
    $default_employee_amt = !empty($_POST['default_amount']) ? floatval($_POST['default_amount']) : null;
    $default_employer_amt = !empty($_POST['employer_amount']) ? floatval($_POST['employer_amount']) : null;
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
    
    // Check if deduction item exists
    $check_deduction = $conn->prepare("SELECT deduction_id FROM pr_tbl_deductions WHERE deduction_id = :deduction_id AND is_deleted = 0");
    $check_deduction->execute([':deduction_id' => $deduction_id]);
    if (!$check_deduction->fetch()) {
        throw new Exception('Deduction item not found');
    }
    
    // Check if deduction item is already in this profile
    $check_duplicate = $conn->prepare("
        SELECT profile_deduction_id 
        FROM pr_tbl_payroll_profile_deductions 
        WHERE profile_id = :profile_id AND deduction_id = :deduction_id
    ");
    $check_duplicate->execute([
        ':profile_id' => $profile_id,
        ':deduction_id' => $deduction_id
    ]);
    if ($check_duplicate->fetch()) {
        throw new Exception('This deduction item is already in the profile');
    }
    
    // Insert the deduction item
    $insert = $conn->prepare("
        INSERT INTO pr_tbl_payroll_profile_deductions 
        (profile_id, deduction_id, default_employee_amt, default_employer_amt, amount_calculation, 
         calculation_value, is_mandatory, display_order, notes, created_at)
        VALUES 
        (:profile_id, :deduction_id, :default_employee_amt, :default_employer_amt, :amount_calculation, 
         :calculation_value, :is_mandatory, :display_order, :notes, NOW())
    ");
    
    // Prepare calculation value based on method
    $calculation_value = null;
    $notes = null;
    
    if ($calculation_method === 'percentage' && $default_employee_amt !== null) {
        $calculation_value = $default_employee_amt / 100; // Store as decimal
    } elseif ($calculation_method === 'formula' && !empty($formula)) {
        $notes = 'Formula: ' . $formula;
        $calculation_value = null;
    }
    
    $insert->execute([
        ':profile_id' => $profile_id,
        ':deduction_id' => $deduction_id,
        ':default_employee_amt' => $default_employee_amt,
        ':default_employer_amt' => $default_employer_amt,
        ':amount_calculation' => $amount_calculation,
        ':calculation_value' => $calculation_value,
        ':is_mandatory' => $is_mandatory,
        ':display_order' => $sort_order,
        ':notes' => $notes
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Deduction item added successfully',
        'profile_deduction_id' => $conn->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in save_profile_deduction_item.php: " . $e->getMessage());
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
