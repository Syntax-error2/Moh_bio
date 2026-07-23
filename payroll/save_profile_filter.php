<?php
/**
 * Save Profile Filter Handler
 * Adds a personnel filter to a payroll profile
 */

include('session.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['profile_id']) || empty($_POST['filter_type'])) {
        throw new Exception('Profile ID and Filter Type are required');
    }
    
    $profile_id = intval($_POST['profile_id']);
    $filter_type = $_POST['filter_type'];
    $filter_value = '';
    
    // Check if profile exists
    $check_profile = $conn->prepare("SELECT profile_id FROM pr_tbl_payroll_profiles WHERE profile_id = :profile_id");
    $check_profile->execute([':profile_id' => $profile_id]);
    if (!$check_profile->fetch()) {
        throw new Exception('Profile not found');
    }
    
    // Map filter types from form to database enum
    $filter_type_map = [
        'department' => 'department',
        'employment_status' => 'emp_status',
        'position' => 'designation',
        'salary_grade' => 'personnel',
        'gender' => 'personnel',
        'age_range' => 'personnel',
        'custom' => 'personnel'
    ];
    
    $db_filter_type = $filter_type_map[$filter_type] ?? 'personnel';
    
    // Process filter values based on type
    switch ($filter_type) {
        case 'department':
            if (!empty($_POST['department_ids']) && is_array($_POST['department_ids'])) {
                $filter_value = implode(',', array_map('intval', $_POST['department_ids']));
            } else {
                throw new Exception('Please select at least one department');
            }
            break;
            
        case 'employment_status':
            if (isset($_POST['employment_status']) && is_array($_POST['employment_status']) && count($_POST['employment_status']) > 0) {
                $filter_value = implode(',', array_map('intval', $_POST['employment_status']));
            } else {
                throw new Exception('Please select at least one employment status (Hold Ctrl/Cmd to select multiple items)');
            }
            break;
            
        case 'position':
            if (!empty($_POST['position_value'])) {
                $filter_value = $_POST['position_value'];
            } else {
                throw new Exception('Please enter a position value');
            }
            break;
            
        case 'salary_grade':
            $from = !empty($_POST['salary_grade_from']) ? intval($_POST['salary_grade_from']) : null;
            $to = !empty($_POST['salary_grade_to']) ? intval($_POST['salary_grade_to']) : null;
            if ($from !== null && $to !== null) {
                $filter_value = "SG:{$from}-{$to}";
            } elseif ($from !== null) {
                $filter_value = "SG:>={$from}";
            } elseif ($to !== null) {
                $filter_value = "SG:<={$to}";
            } else {
                throw new Exception('Please specify salary grade range');
            }
            break;
            
        case 'gender':
            if (!empty($_POST['gender_value'])) {
                $filter_value = "GENDER:" . $_POST['gender_value'];
            } else {
                throw new Exception('Please select a gender');
            }
            break;
            
        case 'age_range':
            $from = !empty($_POST['age_from']) ? intval($_POST['age_from']) : null;
            $to = !empty($_POST['age_to']) ? intval($_POST['age_to']) : null;
            if ($from !== null && $to !== null) {
                $filter_value = "AGE:{$from}-{$to}";
            } elseif ($from !== null) {
                $filter_value = "AGE:>={$from}";
            } elseif ($to !== null) {
                $filter_value = "AGE:<={$to}";
            } else {
                throw new Exception('Please specify age range');
            }
            break;
            
        case 'custom':
            if (!empty($_POST['custom_condition'])) {
                $filter_value = "SQL:" . $_POST['custom_condition'];
            } else {
                throw new Exception('Please enter a custom SQL condition');
            }
            break;
            
        default:
            throw new Exception('Invalid filter type');
    }
    
    if (empty($filter_value)) {
        throw new Exception('Filter value cannot be empty');
    }
    
    // Insert the filter
    $insert = $conn->prepare("
        INSERT INTO pr_tbl_payroll_profile_filters 
        (profile_id, filter_type, filter_value, created_at)
        VALUES 
        (:profile_id, :filter_type, :filter_value, NOW())
    ");
    
    $insert->execute([
        ':profile_id' => $profile_id,
        ':filter_type' => $db_filter_type,
        ':filter_value' => $filter_value
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Filter added successfully',
        'filter_id' => $conn->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in save_profile_filter.php: " . $e->getMessage());
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
