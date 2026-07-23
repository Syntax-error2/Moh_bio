<?php
session_start();
include('dbcon.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Accept both GET and POST, with parameter 'id' or 'leave_application_id'
$leave_application_id = null;
if (isset($_GET['id'])) {
    $leave_application_id = $_GET['id'];
} elseif (isset($_POST['leave_application_id'])) {
    $leave_application_id = $_POST['leave_application_id'];
} elseif (isset($_POST['id'])) {
    $leave_application_id = $_POST['id'];
}

if (!$leave_application_id) {
    echo json_encode(['success' => false, 'message' => 'Leave application ID is required']);
    exit();
}

try {
    // Fetch leave application with personnel details and active service record salary
    $query = $conn->prepare("SELECT 
        la.*,
        p.lname, p.fname, p.mname, p.suffix,
        des.des_name as position,
        sr.monthly_salary
    FROM leave_applications la
    LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
    LEFT JOIN designation des ON p.des_id = des.des_id
    LEFT JOIN service_record sr ON p.personnel_id = sr.personnel_id AND sr.appointDate_status = 'Active'
    WHERE la.id = :leave_application_id
    LIMIT 1");
    
    $query->execute([':leave_application_id' => $leave_application_id]);
    $leave_app = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$leave_app) {
        echo json_encode(['success' => false, 'message' => 'Leave application not found']);
        exit();
    }
    
    // Build full name
    $full_name = $leave_app['fname'] . ' ';
    if ($leave_app['mname']) {
        $full_name .= substr($leave_app['mname'], 0, 1) . '. ';
    }
    $full_name .= $leave_app['lname'];
    if ($leave_app['suffix'] && $leave_app['suffix'] != '-') {
        $full_name .= ' ' . $leave_app['suffix'];
    }
    
    $personnel_id = $leave_app['personnel_id'];
    
    // ====================================
    // USE STORED SNAPSHOT VALUES FROM leave_applications TABLE
    // These values were captured when the application was created/approved
    // This ensures reprinting shows the SAME values as the original print
    // ====================================
    
    // Get stored deduction values
    $vl_this_app = floatval($leave_app['less_application_vl'] ?? 0);
    $sl_this_app = floatval($leave_app['less_application_sl'] ?? 0);
    $vl_without_pay_app = floatval($leave_app['less_application_vl_without_pay'] ?? 0);
    $sl_without_pay_app = floatval($leave_app['less_application_sl_without_pay'] ?? 0);
    
    // Get stored balance values (snapshot at time of application)
    $stored_total_earned_vl = $leave_app['total_earned_vl'] ?? null;
    $stored_total_earned_sl = $leave_app['total_earned_sl'] ?? null;
    $stored_balance_vl = $leave_app['balance_vl'] ?? null;
    $stored_balance_sl = $leave_app['balance_sl'] ?? null;
    
    // ====================================
    // CHECK IF STORED VALUES EXIST
    // If stored values exist (non-null and non-zero), use them for consistency
    // Otherwise fall back to current calculation for backward compatibility
    // ====================================
    $has_stored_values = ($stored_total_earned_vl !== null && $stored_total_earned_vl !== '' && 
                          $stored_balance_vl !== null && $stored_balance_vl !== '') ||
                         ($stored_total_earned_sl !== null && $stored_total_earned_sl !== '' && 
                          $stored_balance_sl !== null && $stored_balance_sl !== '');
    
    if ($has_stored_values) {
        // Use stored snapshot values - this ensures reprinting shows consistent data
        $vl_balance_before = floatval($stored_total_earned_vl);
        $sl_balance_before = floatval($stored_total_earned_sl);
        $vl_balance_after = floatval($stored_balance_vl);
        $sl_balance_after = floatval($stored_balance_sl);
    } else {
        // Fall back to calculation for old records without stored values
        // Get all leave card entries for this personnel, ordered by date
        $leave_card_query = $conn->prepare("SELECT 
            vl_earned, vl_with_pay, vl_without_pay,
            sl_earned, sl_with_pay, sl_without_pay
        FROM leave_card 
        WHERE personnel_id = :personnel_id 
        ORDER BY period_from ASC, id ASC");
        $leave_card_query->execute([':personnel_id' => $personnel_id]);
        $leave_entries = $leave_card_query->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate running totals
        $vl_total_earned = 0;
        $vl_total_used = 0;
        $sl_total_earned = 0;
        $sl_total_used = 0;
        
        foreach ($leave_entries as $entry) {
            $vl_total_earned += floatval($entry['vl_earned']);
            $vl_total_used += floatval($entry['vl_with_pay']) + floatval($entry['vl_without_pay']);
            $sl_total_earned += floatval($entry['sl_earned']);
            $sl_total_used += floatval($entry['sl_with_pay']) + floatval($entry['sl_without_pay']);
        }
        
        // Calculate current balances
        $vl_balance_current = $vl_total_earned - $vl_total_used;
        $sl_balance_current = $sl_total_earned - $sl_total_used;
        
        // If stored deduction values are empty (old records), fall back to calculation based on leave type
        if ($vl_this_app == 0 && $sl_this_app == 0 && $vl_without_pay_app == 0 && $sl_without_pay_app == 0) {
            $leave_days = floatval($leave_app['number_of_days'] ?? 0);
            $leave_type = $leave_app['leave_type'] ?? '';
            
            // Determine how many days are being deducted from VL or SL
            if (stripos($leave_type, 'vacation') !== false || stripos($leave_type, 'mandatory') !== false || stripos($leave_type, 'forced') !== false) {
                $vl_this_app = $leave_days;
            } elseif (stripos($leave_type, 'sick') !== false) {
                $sl_this_app = $leave_days;
            }
        }
        
        $leave_card_entry_id = $leave_app['leave_card_entry_id'] ?? null;
        
        // Calculate balance BEFORE this application
        if ($leave_card_entry_id) {
            $vl_balance_before = $vl_balance_current + $vl_this_app;
            $sl_balance_before = $sl_balance_current + $sl_this_app;
        } else {
            $vl_balance_before = $vl_balance_current;
            $sl_balance_before = $sl_balance_current;
        }
        
        // Balance AFTER this application
        $vl_balance_after = $vl_balance_before - $vl_this_app;
        $sl_balance_after = $sl_balance_before - $sl_this_app;
    }
    
    // Fetch signatories settings
    $signatories_query = $conn->prepare("SELECT * FROM signatories_settings LIMIT 1");
    $signatories_query->execute();
    $signatories = $signatories_query->fetch(PDO::FETCH_ASSOC);
    
    // Default signatories if not set
    if (!$signatories) {
        $signatories = [
            'hrmo_name' => '',
            'hrmo_position' => 'Human Resource Management Officer',
            'recommending_name' => '',
            'recommending_position' => 'Municipal Administrator',
            'approving_name' => '',
            'approving_position' => 'Regional Director',
            'monetization_constant' => '0.0481927',
            'budget_officer_name' => '',
            'budget_officer_position' => 'Municipal Budget Officer',
            'treasurer_name' => '',
            'treasurer_position' => 'Municipal Treasurer',
            'accountant_name' => '',
            'accountant_position' => 'Municipal Accountant',
            'mayor_name' => '',
            'mayor_position' => 'Municipal Mayor'
        ];
    }
    
    // Prepare response with proper structure
    // NOTE: For CS Form 6, "Total Earned" field shows the balance BEFORE this application
    echo json_encode([
        'success' => true,
        'leave_application' => $leave_app,
        'personnel' => [
            'full_name' => $full_name,
            'monthly_salary' => $leave_app['monthly_salary'] ?? 0,
            'position' => $leave_app['position'] ?? ''
        ],
        'leave_credits' => [
            'vl_total_earned' => $vl_balance_before,  // Balance BEFORE this application
            'vl_this_application' => $vl_this_app,
            'vl_without_pay' => $vl_without_pay_app,
            'vl_balance' => $vl_balance_after,        // Balance AFTER this application
            'sl_total_earned' => $sl_balance_before,  // Balance BEFORE this application
            'sl_this_application' => $sl_this_app,
            'sl_without_pay' => $sl_without_pay_app,
            'sl_balance' => $sl_balance_after         // Balance AFTER this application
        ],
        'signatories' => $signatories
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching leave application for print: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
