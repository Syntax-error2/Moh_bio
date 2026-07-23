<?php
/**
 * Process Payroll Generation
 * Creates actual payroll run records from a template/profile
 */

// Start output buffering
ob_start();

// Increase execution time for large payroll processing
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');

include('session.php');

// DEBUG: Log all POST data
error_log("=== PROCESS PAYROLL GENERATION DEBUG ===");
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("generate_payroll isset: " . (isset($_POST['generate_payroll']) ? 'YES' : 'NO'));

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_payroll'])) {
    error_log("Form validation passed, starting processing...");
    try {
        // Get form data
        $profile_id = $_POST['profile_id'] ?? null;
        error_log("Profile ID: " . $profile_id);
        $run_name = trim($_POST['run_name'] ?? '');
        $pay_period_start = $_POST['pay_period_start'] ?? '';
        $pay_period_end = $_POST['pay_period_end'] ?? '';
        $payment_date = $_POST['payment_date'] ?? null;
        $notes = trim($_POST['notes'] ?? '');
        $personnel_selection = $_POST['personnel_selection'] ?? 'all';
        
        // Validate required fields
        if (empty($profile_id) || empty($run_name) || empty($pay_period_start) || empty($pay_period_end)) {
            throw new Exception('Missing required fields');
        }
        
        // Validate dates
        if (strtotime($pay_period_start) > strtotime($pay_period_end)) {
            throw new Exception('Pay period start date must be before or equal to end date');
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // Get profile details
        $profile_query = $conn->prepare("SELECT * FROM pr_tbl_payroll_profiles WHERE profile_id = :profile_id AND is_active = 1");
        $profile_query->execute([':profile_id' => $profile_id]);
        $profile = $profile_query->fetch(PDO::FETCH_ASSOC);
        
        if (!$profile) {
            throw new Exception('Profile not found or inactive');
        }
        
        // Build personnel query based on selection criteria
        $personnel_where = [];
        $personnel_params = [];
        
        switch ($personnel_selection) {
            case 'department':
                if (!empty($_POST['departments'])) {
                    $dept_placeholders = [];
                    foreach ($_POST['departments'] as $index => $dept_id) {
                        $param_name = ':dept_' . $index;
                        $dept_placeholders[] = $param_name;
                        $personnel_params[$param_name] = $dept_id;
                    }
                    $personnel_where[] = "p.do_id IN (" . implode(',', $dept_placeholders) . ")";
                } else {
                    throw new Exception('Please select at least one department');
                }
                break;
                
            case 'designation':
                if (!empty($_POST['designations'])) {
                    $des_placeholders = [];
                    foreach ($_POST['designations'] as $index => $des_id) {
                        $param_name = ':des_' . $index;
                        $des_placeholders[] = $param_name;
                        $personnel_params[$param_name] = $des_id;
                    }
                    $personnel_where[] = "p.des_id IN (" . implode(',', $des_placeholders) . ")";
                } else {
                    throw new Exception('Please select at least one designation');
                }
                break;
                
            case 'emp_status':
                if (!empty($_POST['emp_statuses'])) {
                    $status_placeholders = [];
                    foreach ($_POST['emp_statuses'] as $index => $status_id) {
                        $param_name = ':status_' . $index;
                        $status_placeholders[] = $param_name;
                        $personnel_params[$param_name] = $status_id;
                    }
                    $personnel_where[] = "p.empStat_id IN (" . implode(',', $status_placeholders) . ")";
                } else {
                    throw new Exception('Please select at least one employment status');
                }
                break;
                
            case 'custom':
                if (!empty($_POST['personnel_ids'])) {
                    $person_placeholders = [];
                    foreach ($_POST['personnel_ids'] as $index => $person_id) {
                        $param_name = ':person_' . $index;
                        $person_placeholders[] = $param_name;
                        $personnel_params[$param_name] = $person_id;
                    }
                    $personnel_where[] = "p.personnel_id IN (" . implode(',', $person_placeholders) . ")";
                } else {
                    throw new Exception('Please select at least one personnel');
                }
                break;
                
            case 'all':
            default:
                // No additional filters, include all active personnel
                break;
        }
        
        // Get all eligible personnel
        $where_sql = !empty($personnel_where) ? "AND " . implode(" AND ", $personnel_where) : "";
        
        $personnel_query = $conn->prepare("
            SELECT p.personnel_id
            FROM personnels p
            WHERE 1=1 $where_sql
            ORDER BY p.lname, p.fname
        ");
        $personnel_query->execute($personnel_params);
        $personnel_list = $personnel_query->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($personnel_list)) {
            throw new Exception('No personnel found matching the selected criteria');
        }
        
        $total_personnel = count($personnel_list);
        
        // Get income items from profile
        $income_query = $conn->prepare("
            SELECT ppi.*, i.income_title, i.income_type
            FROM pr_tbl_payroll_profile_income ppi
            INNER JOIN pr_tbl_income i ON ppi.income_id = i.income_id
            WHERE ppi.profile_id = :profile_id
            ORDER BY ppi.display_order ASC
        ");
        $income_query->execute([':profile_id' => $profile_id]);
        $income_items = $income_query->fetchAll(PDO::FETCH_ASSOC);
        
        // Get deduction items from profile
        $deduction_query = $conn->prepare("
            SELECT ppd.*, d.deduction_title, d.deduction_type
            FROM pr_tbl_payroll_profile_deductions ppd
            INNER JOIN pr_tbl_deductions d ON ppd.deduction_id = d.deduction_id
            WHERE ppd.profile_id = :profile_id
            ORDER BY ppd.display_order ASC
        ");
        $deduction_query->execute([':profile_id' => $profile_id]);
        $deduction_items = $deduction_query->fetchAll(PDO::FETCH_ASSOC);
        
        // Create payroll run header
        $create_run = $conn->prepare("
            INSERT INTO pr_tbl_payroll_runs 
                (profile_id, run_name, run_type, pay_period_start, pay_period_end, payment_date,
                 run_status, total_personnel, notes, created_by, created_at)
            VALUES 
                (:profile_id, :run_name, :run_type, :pay_period_start, :pay_period_end, :payment_date,
                 'draft', :total_personnel, :notes, :created_by, NOW())
        ");
        
        $create_run->execute([
            ':profile_id' => $profile_id,
            ':run_name' => $run_name,
            ':run_type' => $profile['profile_type'],
            ':pay_period_start' => $pay_period_start,
            ':pay_period_end' => $pay_period_end,
            ':payment_date' => $payment_date ?: null,
            ':total_personnel' => $total_personnel,
            ':notes' => $notes,
            ':created_by' => $session_id
        ]);
        
        $run_id = $conn->lastInsertId();
        
        // Initialize totals
        $run_total_gross = 0;
        $run_total_deductions = 0;
        $run_total_employer_share = 0;
        $run_total_net_pay = 0;
        
        // Process each personnel
        $detail_insert = $conn->prepare("
            INSERT INTO pr_tbl_payroll_run_details 
                (run_id, personnel_id, gross_pay, total_deductions, total_employer_share, net_pay,
                 payment_status, created_at)
            VALUES 
                (:run_id, :personnel_id, :gross_pay, :total_deductions, :total_employer_share, :net_pay,
                 'pending', NOW())
        ");
        
        $income_insert = $conn->prepare("
            INSERT INTO pr_tbl_payroll_run_income 
                (detail_id, run_id, personnel_id, income_id, income_title, income_type, amount, created_at)
            VALUES 
                (:detail_id, :run_id, :personnel_id, :income_id, :income_title, :income_type, :amount, NOW())
        ");
        
        $deduction_insert = $conn->prepare("
            INSERT INTO pr_tbl_payroll_run_deductions 
                (detail_id, run_id, personnel_id, deduction_id, deduction_title, deduction_type,
                 employee_amount, employer_amount, created_at)
            VALUES 
                (:detail_id, :run_id, :personnel_id, :deduction_id, :deduction_title, :deduction_type,
                 :employee_amount, :employer_amount, NOW())
        ");
        
        foreach ($personnel_list as $personnel_id) {
            $personnel_gross = 0;
            $personnel_deductions = 0;
            $personnel_employer_share = 0;
            
            // Calculate income for this personnel
            $personnel_income_data = [];
            
            foreach ($income_items as $income_item) {
                // Determine amount to use
                if ($income_item['amount_calculation'] === 'personnel_specific') {
                    // Get personnel-specific amount from pr_tbl_personnel_income
                    $get_amount = $conn->prepare("
                        SELECT amount_per_pay 
                        FROM pr_tbl_personnel_income 
                        WHERE personnel_id = :personnel_id 
                          AND income_id = :income_id 
                          AND is_active = 1
                    ");
                    $get_amount->execute([
                        ':personnel_id' => $personnel_id,
                        ':income_id' => $income_item['income_id']
                    ]);
                    $amount_row = $get_amount->fetch(PDO::FETCH_ASSOC);
                    
                    if ($amount_row) {
                        // Use personnel-specific amount
                        $amount = floatval($amount_row['amount_per_pay']);
                    } else {
                        // Fall back to profile default amount if no personnel-specific data
                        $amount = floatval($income_item['default_amount'] ?? 0);
                    }
                } elseif ($income_item['amount_calculation'] === 'fixed') {
                    // Use default fixed amount
                    $amount = floatval($income_item['default_amount'] ?? 0);
                } elseif ($income_item['amount_calculation'] === 'percentage') {
                    // Calculate based on percentage (e.g., 10% of basic salary)
                    // TODO: Implement percentage calculation based on calculation_base
                    $amount = floatval($income_item['default_amount'] ?? 0);
                } else {
                    $amount = floatval($income_item['default_amount'] ?? 0);
                }
                
                // Only include if amount > 0 or if mandatory
                if ($amount > 0 || $income_item['is_mandatory']) {
                    $personnel_gross += $amount;
                    $personnel_income_data[] = [
                        'income_id' => $income_item['income_id'],
                        'income_title' => $income_item['income_title'],
                        'income_type' => $income_item['income_type'],
                        'amount' => $amount
                    ];
                }
            }
            
            // Calculate deductions for this personnel
            $personnel_deduction_data = [];
            
            foreach ($deduction_items as $deduction_item) {
                // Determine amounts to use
                if ($deduction_item['amount_calculation'] === 'personnel_specific') {
                    // Get personnel-specific amounts from pr_tbl_personnel_deductions
                    $get_amounts = $conn->prepare("
                        SELECT employee_amt_per_pay, employer_amt_per_pay
                        FROM pr_tbl_personnel_deductions 
                        WHERE personnel_id = :personnel_id 
                          AND deduction_id = :deduction_id 
                          AND is_active = 1
                    ");
                    $get_amounts->execute([
                        ':personnel_id' => $personnel_id,
                        ':deduction_id' => $deduction_item['deduction_id']
                    ]);
                    $amounts_row = $get_amounts->fetch(PDO::FETCH_ASSOC);
                    
                    if ($amounts_row) {
                        // Use personnel-specific amounts
                        $employee_amt = floatval($amounts_row['employee_amt_per_pay']);
                        $employer_amt = floatval($amounts_row['employer_amt_per_pay']);
                    } else {
                        // Fall back to profile default amounts if no personnel-specific data
                        $employee_amt = floatval($deduction_item['default_employee_amt'] ?? 0);
                        $employer_amt = floatval($deduction_item['default_employer_amt'] ?? 0);
                    }
                } elseif ($deduction_item['amount_calculation'] === 'fixed') {
                    // Use default fixed amounts
                    $employee_amt = floatval($deduction_item['default_employee_amt'] ?? 0);
                    $employer_amt = floatval($deduction_item['default_employer_amt'] ?? 0);
                } else {
                    $employee_amt = floatval($deduction_item['default_employee_amt'] ?? 0);
                    $employer_amt = floatval($deduction_item['default_employer_amt'] ?? 0);
                }
                
                // Only include if amounts > 0 or if mandatory
                if (($employee_amt > 0 || $employer_amt > 0) || $deduction_item['is_mandatory']) {
                    $personnel_deductions += $employee_amt;
                    $personnel_employer_share += $employer_amt;
                    $personnel_deduction_data[] = [
                        'deduction_id' => $deduction_item['deduction_id'],
                        'deduction_title' => $deduction_item['deduction_title'],
                        'deduction_type' => $deduction_item['deduction_type'],
                        'employee_amount' => $employee_amt,
                        'employer_amount' => $employer_amt
                    ];
                }
            }
            
            // Calculate net pay
            $personnel_net_pay = $personnel_gross - $personnel_deductions;
            
            // Insert personnel payroll detail
            $detail_insert->execute([
                ':run_id' => $run_id,
                ':personnel_id' => $personnel_id,
                ':gross_pay' => $personnel_gross,
                ':total_deductions' => $personnel_deductions,
                ':total_employer_share' => $personnel_employer_share,
                ':net_pay' => $personnel_net_pay
            ]);
            
            $detail_id = $conn->lastInsertId();
            
            // Insert income items
            foreach ($personnel_income_data as $income_data) {
                $income_insert->execute([
                    ':detail_id' => $detail_id,
                    ':run_id' => $run_id,
                    ':personnel_id' => $personnel_id,
                    ':income_id' => $income_data['income_id'],
                    ':income_title' => $income_data['income_title'],
                    ':income_type' => $income_data['income_type'],
                    ':amount' => $income_data['amount']
                ]);
            }
            
            // Insert deduction items
            foreach ($personnel_deduction_data as $deduction_data) {
                $deduction_insert->execute([
                    ':detail_id' => $detail_id,
                    ':run_id' => $run_id,
                    ':personnel_id' => $personnel_id,
                    ':deduction_id' => $deduction_data['deduction_id'],
                    ':deduction_title' => $deduction_data['deduction_title'],
                    ':deduction_type' => $deduction_data['deduction_type'],
                    ':employee_amount' => $deduction_data['employee_amount'],
                    ':employer_amount' => $deduction_data['employer_amount']
                ]);
            }
            
            // Add to run totals
            $run_total_gross += $personnel_gross;
            $run_total_deductions += $personnel_deductions;
            $run_total_employer_share += $personnel_employer_share;
            $run_total_net_pay += $personnel_net_pay;
        }
        
        // Update payroll run with totals
        $update_run = $conn->prepare("
            UPDATE pr_tbl_payroll_runs SET
                total_gross = :total_gross,
                total_deductions = :total_deductions,
                total_employer_share = :total_employer_share,
                total_net_pay = :total_net_pay,
                updated_at = NOW()
            WHERE run_id = :run_id
        ");
        
        $update_run->execute([
            ':total_gross' => $run_total_gross,
            ':total_deductions' => $run_total_deductions,
            ':total_employer_share' => $run_total_employer_share,
            ':total_net_pay' => $run_total_net_pay,
            ':run_id' => $run_id
        ]);
        
        // Generate snapshot (call stored procedure if it exists, or create snapshot manually)
        try {
            $conn->query("CALL sp_generate_payroll_snapshot($run_id)");
        } catch (Exception $e) {
            // Stored procedure may not exist yet, skip for now
        }
        
        // Log audit trail
        $audit_log = $conn->prepare("
            INSERT INTO pr_tbl_payroll_audit_log 
                (run_id, action_type, table_name, record_id, performed_by, performed_at)
            VALUES 
                (:run_id, 'create', 'pr_tbl_payroll_runs', :record_id, :user_id, NOW())
        ");
        $audit_log->execute([
            ':run_id' => $run_id,
            ':record_id' => $run_id,
            ':user_id' => $session_id
        ]);
        
        // Commit transaction
        $conn->commit();
        error_log("Transaction committed successfully! Run ID: " . $run_id . ", Total personnel: " . $total_personnel);
        
        // Redirect to view payroll run
        ob_end_clean();
        error_log("Redirecting to view_payroll_run.php?run_id=" . $run_id);
        header('Location: view_payroll_run.php?run_id=' . $run_id . '&success=' . urlencode('Payroll run generated successfully! Processed ' . $total_personnel . ' personnel.'));
        exit();
        
    } catch (Exception $e) {
        error_log("ERROR in payroll generation: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        if ($conn->inTransaction()) {
            $conn->rollBack();
            error_log("Transaction rolled back");
        }
        
        ob_end_clean();
        $error_msg = urlencode('Error generating payroll: ' . $e->getMessage());
        $profile_id = $_POST['profile_id'] ?? '';
        error_log("Redirecting with error: " . $error_msg);
        
        if ($profile_id) {
            header('Location: generate_payroll_from_profile.php?profile_id=' . $profile_id . '&error=' . $error_msg);
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
