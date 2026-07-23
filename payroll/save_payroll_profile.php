<?php
/**
 * Save Payroll Profile
 * Handles creating and updating payroll profiles/templates
 */

// Start output buffering
ob_start();

include('session.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    try {
        // Get form data
        $profile_name = trim($_POST['profile_name'] ?? '');
        $profile_description = trim($_POST['profile_description'] ?? '');
        $profile_type = $_POST['profile_type'] ?? 'regular';
        $pay_frequency = $_POST['pay_frequency'] ?? 'monthly';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $is_default = isset($_POST['is_default']) ? 1 : 0;
        $profile_id = $_POST['profile_id'] ?? null;
        
        // Validate required fields
        if (empty($profile_name)) {
            throw new Exception('Profile name is required');
        }
        
        if (strlen($profile_name) < 3) {
            throw new Exception('Profile name must be at least 3 characters long');
        }
        
        // Validate allowed values
        $allowed_types = ['regular', 'special', '13th_month', 'bonus', 'custom'];
        if (!in_array($profile_type, $allowed_types)) {
            throw new Exception('Invalid profile type');
        }
        
        $allowed_frequencies = ['monthly', 'semi-monthly', 'bi-weekly', 'weekly', 'one-time'];
        if (!in_array($pay_frequency, $allowed_frequencies)) {
            throw new Exception('Invalid pay frequency');
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // If setting as default, unset other defaults first
        if ($is_default == 1) {
            $unset_default = $conn->prepare("UPDATE pr_tbl_payroll_profiles SET is_default = 0");
            $unset_default->execute();
        }
        
        if ($profile_id) {
            // UPDATE existing profile
            $update_query = $conn->prepare("
                UPDATE pr_tbl_payroll_profiles SET
                    profile_name = :profile_name,
                    profile_description = :profile_description,
                    profile_type = :profile_type,
                    pay_frequency = :pay_frequency,
                    is_active = :is_active,
                    is_default = :is_default,
                    updated_at = NOW()
                WHERE profile_id = :profile_id
            ");
            
            $update_query->execute([
                ':profile_name' => $profile_name,
                ':profile_description' => $profile_description,
                ':profile_type' => $profile_type,
                ':pay_frequency' => $pay_frequency,
                ':is_active' => $is_active,
                ':is_default' => $is_default,
                ':profile_id' => $profile_id
            ]);
            
            // Log audit trail
            $audit_log = $conn->prepare("
                INSERT INTO pr_tbl_payroll_audit_log 
                    (action_type, table_name, record_id, performed_by, performed_at)
                VALUES 
                    ('update', 'pr_tbl_payroll_profiles', :profile_id, :user_id, NOW())
            ");
            $audit_log->execute([
                ':profile_id' => $profile_id,
                ':user_id' => $_SESSION['user_id'] ?? null
            ]);
            
            $conn->commit();
            
            ob_end_clean();
            header('Location: view_payroll_profile.php?profile_id=' . $profile_id . '&success=' . urlencode('Profile updated successfully'));
            exit();
            
        } else {
            // INSERT new profile
            $insert_query = $conn->prepare("
                INSERT INTO pr_tbl_payroll_profiles 
                    (profile_name, profile_description, profile_type, pay_frequency, 
                     is_active, is_default, created_by, created_at)
                VALUES 
                    (:profile_name, :profile_description, :profile_type, :pay_frequency,
                     :is_active, :is_default, :created_by, NOW())
            ");
            
            $insert_query->execute([
                ':profile_name' => $profile_name,
                ':profile_description' => $profile_description,
                ':profile_type' => $profile_type,
                ':pay_frequency' => $pay_frequency,
                ':is_active' => $is_active,
                ':is_default' => $is_default,
                ':created_by' => $_SESSION['user_id'] ?? null
            ]);
            
            $new_profile_id = $conn->lastInsertId();
            
            // Log audit trail
            $audit_log = $conn->prepare("
                INSERT INTO pr_tbl_payroll_audit_log 
                    (action_type, table_name, record_id, performed_by, performed_at)
                VALUES 
                    ('create', 'pr_tbl_payroll_profiles', :profile_id, :user_id, NOW())
            ");
            $audit_log->execute([
                ':profile_id' => $new_profile_id,
                ':user_id' => $_SESSION['user_id'] ?? null
            ]);
            
            $conn->commit();
            
            ob_end_clean();
            header('Location: view_payroll_profile.php?profile_id=' . $new_profile_id . '&success=' . urlencode('Profile created successfully. Now add income and deduction items.'));
            exit();
        }
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        ob_end_clean();
        $error_msg = urlencode('Error saving profile: ' . $e->getMessage());
        
        if ($profile_id) {
            header('Location: view_payroll_profile.php?profile_id=' . $profile_id . '&error=' . $error_msg);
        } else {
            header('Location: list_payroll_profiles.php?error=' . $error_msg);
        }
        exit();
    }
}

// If not POST request, redirect back
ob_end_clean();
header('Location: list_payroll_profiles.php');
exit();
?>
