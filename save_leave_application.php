<?php
session_start();
require_once 'dbcon.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

// ====================================
// HELPER: Fetch current leave card balances for a personnel
// ====================================
function getLeaveCardBalances($conn, $personnel_id) {
    // Check if is_special_leave column exists
    $column_check = $conn->query("SHOW COLUMNS FROM leave_card LIKE 'is_special_leave'");
    $has_special_leave_column = $column_check->rowCount() > 0;
    
    if ($has_special_leave_column) {
        // Calculate balances excluding special leave "with pay" deductions
        $query = $conn->prepare("SELECT 
            COALESCE(SUM(vl_earned), 0) as total_vl_earned,
            COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN vl_with_pay ELSE 0 END), 0) as total_vl_with_pay,
            COALESCE(SUM(sl_earned), 0) as total_sl_earned,
            COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN sl_with_pay ELSE 0 END), 0) as total_sl_with_pay
        FROM leave_card 
        WHERE personnel_id = :personnel_id");
    } else {
        // Fallback query without is_special_leave column
        $query = $conn->prepare("SELECT 
            COALESCE(SUM(vl_earned), 0) as total_vl_earned,
            COALESCE(SUM(vl_with_pay), 0) as total_vl_with_pay,
            COALESCE(SUM(sl_earned), 0) as total_sl_earned,
            COALESCE(SUM(sl_with_pay), 0) as total_sl_with_pay
        FROM leave_card 
        WHERE personnel_id = :personnel_id");
    }
    
    $query->execute([':personnel_id' => $personnel_id]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $vl_earned = floatval($result['total_vl_earned']);
        $vl_used = floatval($result['total_vl_with_pay']);
        $sl_earned = floatval($result['total_sl_earned']);
        $sl_used = floatval($result['total_sl_with_pay']);
        
        return [
            'vl_balance' => round($vl_earned - $vl_used, 3),
            'sl_balance' => round($sl_earned - $sl_used, 3)
        ];
    }
    
    return ['vl_balance' => 0, 'sl_balance' => 0];
}

// ====================================
// SAVE NEW LEAVE APPLICATION
// ====================================
if (isset($_POST['save_leave_application'])) {
    try {
        $personnel_id = $_POST['personnel_id'];
        $office_agency = $_POST['office_agency'];
        $application_date = $_POST['application_date'];
        $leave_type = $_POST['leave_type'];
        $other_leave_specification = $_POST['other_leave_specification'] ?? null;
        $vacation_details = $_POST['vacation_details'] ?? null;
        $sick_details = $_POST['sick_details'] ?? null;
        $study_details = $_POST['study_details'] ?? null;
        $inclusive_date_from = $_POST['inclusive_date_from'];
        $inclusive_date_to = $_POST['inclusive_date_to'];
        $inclusive_dates_json = $_POST['inclusive_dates_json'] ?? null;
        $number_of_days = $_POST['number_of_days'];
        $commutation = $_POST['commutation'];
        $as_of_date = $_POST['as_of_date'] ?? null;
        
        // Fetch ACTUAL current leave balances from leave_card
        $current_balances = getLeaveCardBalances($conn, $personnel_id);
        
        // Use actual leave card balance as "total earned" (this is the balance BEFORE this application)
        $total_earned_vl = $current_balances['vl_balance'];
        $total_earned_sl = $current_balances['sl_balance'];
        
        // Get less_application values from form (the days being applied for)
        $less_application_vl = floatval($_POST['less_application_vl'] ?? 0);
        $less_application_vl_without_pay = floatval($_POST['less_application_vl_without_pay'] ?? 0);
        $less_application_sl = floatval($_POST['less_application_sl'] ?? 0);
        $less_application_sl_without_pay = floatval($_POST['less_application_sl_without_pay'] ?? 0);
        
        // Calculate balance: total_earned - less_application = balance (AFTER this application)
        $balance_vl = round($total_earned_vl - $less_application_vl, 3);
        $balance_sl = round($total_earned_sl - $less_application_sl, 3);
        
        // Parse and validate inclusive_dates_json
        $date_ranges = [];
        if (!empty($inclusive_dates_json)) {
            $date_ranges = json_decode($inclusive_dates_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $date_ranges = [];
            }
        }
        
        // Fallback: if no JSON or parsing failed, use legacy single date range
        if (empty($date_ranges) && !empty($inclusive_date_from) && !empty($inclusive_date_to)) {
            $date_ranges = [['from' => $inclusive_date_from, 'to' => $inclusive_date_to]];
            $inclusive_dates_json = json_encode($date_ranges);
        }

        // Insert into leave_applications table
        $stmt = $conn->prepare("INSERT INTO leave_applications (
            personnel_id,
            office_agency,
            application_date,
            leave_type,
            other_leave_specification,
            vacation_details,
            sick_details,
            study_details,
            inclusive_date_from,
            inclusive_date_to,
            inclusive_dates_json,
            number_of_days,
            commutation,
            as_of_date,
            total_earned_vl,
            total_earned_sl,
            less_application_vl,
            less_application_vl_without_pay,
            less_application_sl,
            less_application_sl_without_pay,
            balance_vl,
            balance_sl,
            status,
            created_at
        ) VALUES (
            :personnel_id,
            :office_agency,
            :application_date,
            :leave_type,
            :other_leave_specification,
            :vacation_details,
            :sick_details,
            :study_details,
            :inclusive_date_from,
            :inclusive_date_to,
            :inclusive_dates_json,
            :number_of_days,
            :commutation,
            :as_of_date,
            :total_earned_vl,
            :total_earned_sl,
            :less_application_vl,
            :less_application_vl_without_pay,
            :less_application_sl,
            :less_application_sl_without_pay,
            :balance_vl,
            :balance_sl,
            'pending',
            NOW()
        )");

        $stmt->bindParam(':personnel_id', $personnel_id);
        $stmt->bindParam(':office_agency', $office_agency);
        $stmt->bindParam(':application_date', $application_date);
        $stmt->bindParam(':leave_type', $leave_type);
        $stmt->bindParam(':other_leave_specification', $other_leave_specification);
        $stmt->bindParam(':vacation_details', $vacation_details);
        $stmt->bindParam(':sick_details', $sick_details);
        $stmt->bindParam(':study_details', $study_details);
        $stmt->bindParam(':inclusive_date_from', $inclusive_date_from);
        $stmt->bindParam(':inclusive_date_to', $inclusive_date_to);
        $stmt->bindParam(':inclusive_dates_json', $inclusive_dates_json);
        $stmt->bindParam(':number_of_days', $number_of_days);
        $stmt->bindParam(':commutation', $commutation);
        $stmt->bindParam(':as_of_date', $as_of_date);
        $stmt->bindParam(':total_earned_vl', $total_earned_vl);
        $stmt->bindParam(':total_earned_sl', $total_earned_sl);
        $stmt->bindParam(':less_application_vl', $less_application_vl);
        $stmt->bindParam(':less_application_vl_without_pay', $less_application_vl_without_pay);
        $stmt->bindParam(':less_application_sl', $less_application_sl);
        $stmt->bindParam(':less_application_sl_without_pay', $less_application_sl_without_pay);
        $stmt->bindParam(':balance_vl', $balance_vl);
        $stmt->bindParam(':balance_sl', $balance_sl);

        $stmt->execute();

        // Get the leave application ID for linking
        $leave_application_id = $conn->lastInsertId();
        
        // ====================================
        // DTR INTEGRATION: Create entries in leave_applicants table
        // This integrates with the daily time record system
        // ====================================
        
        // Generate unique leave code for DTR tracking
        function generateLeaveCode() {
            $var = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $code = '';
            for ($i = 0; $i < 10; $i++) {
                $code .= $var[rand(0, strlen($var) - 1)];
            }
            return $code;
        }
        
        $leave_code = generateLeaveCode();
        
        // Get personnel department/office
        $personnel_query = $conn->prepare("SELECT do_id FROM personnels WHERE personnel_id = :personnel_id LIMIT 1");
        $personnel_query->execute([':personnel_id' => $personnel_id]);
        $personnel_data = $personnel_query->fetch();
        $do_id = $personnel_data['do_id'] ?? 0;
        
        // Determine if this is a special leave (no deductions from credits)
        $special_leave_types = [
            'Maternity Leave',
            'Paternity Leave',
            'Special Privilege Leave',
            'Solo Parent Leave',
            'Study Leave',
            'Study Leave - Completion of Master\'s Degree',
            'Study Leave - BAR/Board Examination Review',
            '10-Day VAWC Leave',
            'Rehabilitation Privilege',
            'Special Leave Benefits for Women',
            'Special Emergency (Calamity) Leave',
            'Adoption Leave'
        ];
        $is_special = in_array($leave_type, $special_leave_types) ? 1 : 0;
        
        // Get substitute personnel ID if specified in do_id field (for compatibility)
        $substitute_id = 0; // Default no substitute
        
        // Prepare statement for DTR entries
        $dtr_stmt = $conn->prepare("INSERT INTO leave_applicants (
            leave_code,
            leave_date,
            leave_type,
            leave_type_desc,
            substitute_id,
            applicant_id,
            do_id,
            numDays,
            is_special,
            status,
            date_created,
            approved_by,
            leave_application_id
        ) VALUES (
            :leave_code,
            :leave_date,
            :leave_type,
            :leave_type_desc,
            :substitute_id,
            :applicant_id,
            :do_id,
            :numDays,
            :is_special,
            'Pending',
            NOW(),
            0,
            :leave_application_id
        )");
        
        // Create description
        $leave_desc = $other_leave_specification ?? $vacation_details ?? $sick_details ?? $study_details ?? '';
        
        // ====================================
        // DTR ENTRIES FOR MULTIPLE DATE RANGES
        // Create entries for each date in each range
        // ====================================
        foreach ($date_ranges as $range) {
            $start_date = new DateTime($range['from']);
            $end_date = new DateTime($range['to']);
            $end_date->modify('+1 day'); // Include end date
            
            $interval = new DateInterval('P1D');
            $date_period = new DatePeriod($start_date, $interval, $end_date);
            
            // Insert entry for each date in this range
            foreach ($date_period as $date) {
                $leave_date = $date->format('Y-m-d');
            
                $dtr_stmt->execute([
                    ':leave_code' => $leave_code,
                    ':leave_date' => $leave_date,
                    ':leave_type' => $leave_type,
                    ':leave_type_desc' => $leave_desc,
                    ':substitute_id' => $substitute_id,
                    ':applicant_id' => $personnel_id,
                    ':do_id' => $do_id,
                    ':numDays' => $number_of_days,
                    ':is_special' => $is_special,
                    ':leave_application_id' => $leave_application_id
                ]);
            }
        }
        
        // Update leave_applications with the leave_code for reference
        $update_code = $conn->prepare("UPDATE leave_applications SET leave_code = :leave_code WHERE id = :id");
        $update_code->execute([':leave_code' => $leave_code, ':id' => $leave_application_id]);
        
        // NOTE: Leave card entry is NOT created here.
        // It will be created automatically when the application status is changed to 'approved'
        // See the UPDATE section below for the createLeaveCardEntry() call

        $_SESSION['success'] = "Leave application submitted successfully! Leave card entry will be created upon approval.";
        
        // Check if redirect_url is provided (coming from leave_card page)
        $redirect_url = $_POST['redirect_url'] ?? '';
        if (!empty($redirect_url)) {
            header("Location: " . $redirect_url);
        } else {
            // Check if do_id is provided (coming from personnel list)
            $do_id = $_POST['do_id'] ?? null;
            $session_access = $_SESSION['access'] ?? '';
            if (!empty($do_id) && $session_access !== 'User') {
                // Redirect to personnel list
                header("Location: list_personnel.php?dept=" . $do_id);
            } else {
                // Redirect to leave application page
                $dept = $_POST['dept'] ?? '';
                header("Location: leave_application.php?dept=" . urlencode($dept) . "&personnel_id=" . $personnel_id);
            }
        }
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error saving leave application: " . $e->getMessage();
        
        // Check redirect location
        $redirect_url = $_POST['redirect_url'] ?? '';
        if (!empty($redirect_url)) {
            header("Location: " . $redirect_url);
        } else {
            $do_id = $_POST['do_id'] ?? null;
            $session_access = $_SESSION['access'] ?? '';
            if (!empty($do_id) && $session_access !== 'User') {
                header("Location: list_personnel.php?dept=" . $do_id);
            } else {
                $dept = $_POST['dept'] ?? '';
                header("Location: leave_application.php?dept=" . urlencode($dept) . "&personnel_id=" . $_POST['personnel_id']);
            }
        }
        exit();
    }
}

// ====================================
// UPDATE LEAVE APPLICATION
// ====================================
if (isset($_POST['update_leave_application'])) {
    try {
        $leave_application_id = $_POST['leave_application_id'];
        $personnel_id = $_POST['personnel_id'];
        $office_agency = $_POST['office_agency'];
        $application_date = $_POST['application_date'];
        $leave_type = $_POST['leave_type'];
        $other_leave_specification = $_POST['other_leave_specification'] ?? null;
        $vacation_details = $_POST['vacation_details'] ?? null;
        $sick_details = $_POST['sick_details'] ?? null;
        $study_details = $_POST['study_details'] ?? null;
        $inclusive_date_from = $_POST['inclusive_date_from'];
        $inclusive_date_to = $_POST['inclusive_date_to'];
        $inclusive_dates_json = $_POST['inclusive_dates_json'] ?? null;
        $number_of_days = $_POST['number_of_days'];
        $commutation = $_POST['commutation'];
        $as_of_date = $_POST['as_of_date'] ?? null;
        
        // Get less_application values from form
        $less_application_vl = floatval($_POST['less_application_vl'] ?? 0);
        $less_application_vl_without_pay = floatval($_POST['less_application_vl_without_pay'] ?? 0);
        $less_application_sl = floatval($_POST['less_application_sl'] ?? 0);
        $less_application_sl_without_pay = floatval($_POST['less_application_sl_without_pay'] ?? 0);
        
        $status = $_POST['status'];
        $recommendation = $_POST['recommendation'] ?? null;
        
        // Get previous status and stored values
        $prev_stmt = $conn->prepare("SELECT status, leave_card_entry_id, total_earned_vl, total_earned_sl, balance_vl, balance_sl 
                                     FROM leave_applications WHERE id = :id");
        $prev_stmt->bindParam(':id', $leave_application_id);
        $prev_stmt->execute();
        $prev_data = $prev_stmt->fetch(PDO::FETCH_ASSOC);
        $previous_status = $prev_data['status'];
        $existing_leave_card_id = $prev_data['leave_card_entry_id'];
        
        // Determine total_earned and balance values
        // If already approved, preserve the stored snapshot values
        // If pending and now being approved, calculate fresh values
        // If still pending, recalculate from current leave card
        
        if ($previous_status === 'approved') {
            // Already approved - preserve the snapshot values for 7.A
            $total_earned_vl = floatval($prev_data['total_earned_vl']);
            $total_earned_sl = floatval($prev_data['total_earned_sl']);
            $balance_vl = floatval($prev_data['balance_vl']);
            $balance_sl = floatval($prev_data['balance_sl']);
        } else {
            // Not yet approved - calculate fresh values from current leave card
            $current_balances = getLeaveCardBalances($conn, $personnel_id);
            $total_earned_vl = $current_balances['vl_balance'];
            $total_earned_sl = $current_balances['sl_balance'];
            
            // Calculate balance: total_earned - less_application = balance (AFTER this application)
            $balance_vl = round($total_earned_vl - $less_application_vl, 3);
            $balance_sl = round($total_earned_sl - $less_application_sl, 3);
        }
        
        // Parse and validate inclusive_dates_json
        $date_ranges = [];
        if (!empty($inclusive_dates_json)) {
            $date_ranges = json_decode($inclusive_dates_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $date_ranges = [];
            }
        }
        
        // Fallback: if no JSON or parsing failed, use legacy single date range
        if (empty($date_ranges) && !empty($inclusive_date_from) && !empty($inclusive_date_to)) {
            $date_ranges = [['from' => $inclusive_date_from, 'to' => $inclusive_date_to]];
            $inclusive_dates_json = json_encode($date_ranges);
        }

        // Update leave_applications table
        $stmt = $conn->prepare("UPDATE leave_applications SET
            office_agency = :office_agency,
            application_date = :application_date,
            leave_type = :leave_type,
            other_leave_specification = :other_leave_specification,
            vacation_details = :vacation_details,
            sick_details = :sick_details,
            study_details = :study_details,
            inclusive_date_from = :inclusive_date_from,
            inclusive_date_to = :inclusive_date_to,
            inclusive_dates_json = :inclusive_dates_json,
            number_of_days = :number_of_days,
            commutation = :commutation,
            as_of_date = :as_of_date,
            total_earned_vl = :total_earned_vl,
            total_earned_sl = :total_earned_sl,
            less_application_vl = :less_application_vl,
            less_application_vl_without_pay = :less_application_vl_without_pay,
            less_application_sl = :less_application_sl,
            less_application_sl_without_pay = :less_application_sl_without_pay,
            balance_vl = :balance_vl,
            balance_sl = :balance_sl,
            status = :status,
            recommendation = :recommendation,
            updated_at = NOW()
        WHERE id = :id");

        $stmt->bindParam(':id', $leave_application_id);
        $stmt->bindParam(':office_agency', $office_agency);
        $stmt->bindParam(':application_date', $application_date);
        $stmt->bindParam(':leave_type', $leave_type);
        $stmt->bindParam(':other_leave_specification', $other_leave_specification);
        $stmt->bindParam(':vacation_details', $vacation_details);
        $stmt->bindParam(':sick_details', $sick_details);
        $stmt->bindParam(':study_details', $study_details);
        $stmt->bindParam(':inclusive_date_from', $inclusive_date_from);
        $stmt->bindParam(':inclusive_date_to', $inclusive_date_to);
        $stmt->bindParam(':inclusive_dates_json', $inclusive_dates_json);
        $stmt->bindParam(':number_of_days', $number_of_days);
        $stmt->bindParam(':commutation', $commutation);
        $stmt->bindParam(':as_of_date', $as_of_date);
        $stmt->bindParam(':total_earned_vl', $total_earned_vl);
        $stmt->bindParam(':total_earned_sl', $total_earned_sl);
        $stmt->bindParam(':less_application_vl', $less_application_vl);
        $stmt->bindParam(':less_application_vl_without_pay', $less_application_vl_without_pay);
        $stmt->bindParam(':less_application_sl', $less_application_sl);
        $stmt->bindParam(':less_application_sl_without_pay', $less_application_sl_without_pay);
        $stmt->bindParam(':balance_vl', $balance_vl);
        $stmt->bindParam(':balance_sl', $balance_sl);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':recommendation', $recommendation);

        $stmt->execute();

        // ====================================
        // AUTOMATIC LEAVE CARD ENTRY SYNC
        // ====================================
        
        // If leave card entry exists, update it with the new values
        if (!empty($existing_leave_card_id)) {
            try {
                // Calculate period dates from leave dates (use month of leave start)
                $period_date = new DateTime($inclusive_date_from);
                $period_from = $period_date->format('Y-m-01');
                $period_to = $period_date->format('Y-m-t');
                
                // Determine particulars based on leave type
                $particulars = $leave_type;
                if (strpos($leave_type, 'Vacation') !== false) {
                    $particulars = 'Vacation Leave';
                } elseif (strpos($leave_type, 'Sick') !== false) {
                    $particulars = 'Sick Leave';
                } elseif (strpos($leave_type, 'Mandatory') !== false || strpos($leave_type, 'Forced') !== false) {
                    $particulars = 'Mandatory/Forced Leave';
                }
                
                // Determine if special leave
                $special_leave_types = [
                    'Maternity Leave', 'Paternity Leave', 'Special Privilege Leave',
                    'Solo Parent Leave', 'Study Leave', 'Study Leave - Completion of Master\'s Degree',
                    'Study Leave - BAR/Board Examination Review', '10-Day VAWC Leave',
                    'Rehabilitation Privilege', 'Special Leave Benefits for Women',
                    'Special Emergency (Calamity) Leave', 'Adoption Leave'
                ];
                $is_special_leave = in_array($leave_type, $special_leave_types) ? 1 : 0;
                
                // Update leave card entry
                $update_lc = $conn->prepare("UPDATE leave_card SET
                    period_from = :period_from,
                    period_to = :period_to,
                    particulars = :particulars,
                    vl_with_pay = :vl_with_pay,
                    vl_without_pay = :vl_without_pay,
                    sl_with_pay = :sl_with_pay,
                    sl_without_pay = :sl_without_pay,
                    is_special_leave = :is_special_leave,
                    date_from = :date_from,
                    date_to = :date_to,
                    number_of_days = :number_of_days
                WHERE id = :id");
                
                $update_lc->execute([
                    ':period_from' => $period_from,
                    ':period_to' => $period_to,
                    ':particulars' => $particulars,
                    ':vl_with_pay' => floatval($less_application_vl),
                    ':vl_without_pay' => floatval($less_application_vl_without_pay),
                    ':sl_with_pay' => floatval($less_application_sl),
                    ':sl_without_pay' => floatval($less_application_sl_without_pay),
                    ':is_special_leave' => $is_special_leave,
                    ':date_from' => $inclusive_date_from,
                    ':date_to' => $inclusive_date_to,
                    ':number_of_days' => $number_of_days,
                    ':id' => $existing_leave_card_id
                ]);
            } catch (PDOException $e) {
                error_log("Error updating linked leave card entry: " . $e->getMessage());
            }
        }
        // If status changed to 'approved' and no leave card entry exists yet, create one
        elseif ($status === 'approved' && $previous_status !== 'approved') {
            $leave_card_id = createLeaveCardEntry($conn, $leave_application_id, $personnel_id, $leave_type, $inclusive_date_from, $inclusive_date_to, $number_of_days, $less_application_vl, $less_application_vl_without_pay, $less_application_sl, $less_application_sl_without_pay);
            
            // Link the new leave card entry to the application
            if ($leave_card_id) {
                $link_stmt = $conn->prepare("UPDATE leave_applications SET leave_card_entry_id = :leave_card_entry_id WHERE id = :id");
                $link_stmt->execute([':leave_card_entry_id' => $leave_card_id, ':id' => $leave_application_id]);
            }
        }

        $_SESSION['success'] = "Leave application updated successfully!";
        $dept = isset($_POST['dept']) ? $_POST['dept'] : '';
        header("Location: leave_application.php?dept=" . urlencode($dept) . "&personnel_id=" . $personnel_id);
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating leave application: " . $e->getMessage();
        $dept = isset($_POST['dept']) ? $_POST['dept'] : '';
        header("Location: leave_application.php?dept=" . urlencode($dept) . "&personnel_id=" . $_POST['personnel_id']);
        exit();
    }
}

// ====================================
// DELETE LEAVE APPLICATION
// ====================================
if (isset($_POST['delete_leave_application'])) {
    try {
        $leave_application_id = $_POST['leave_application_id'];
        $personnel_id = $_POST['personnel_id'];

        // Check if there's a linked leave card entry
        $check_stmt = $conn->prepare("SELECT leave_card_entry_id FROM leave_applications WHERE id = :id");
        $check_stmt->bindParam(':id', $leave_application_id);
        $check_stmt->execute();
        $check_data = $check_stmt->fetch(PDO::FETCH_ASSOC);
        $leave_card_entry_id = $check_data['leave_card_entry_id'];

        // Delete the leave application
        $stmt = $conn->prepare("DELETE FROM leave_applications WHERE id = :id");
        $stmt->bindParam(':id', $leave_application_id);
        $stmt->execute();

        // Also delete the linked leave card entry if exists
        if (!empty($leave_card_entry_id)) {
            $delete_lc = $conn->prepare("DELETE FROM leave_card WHERE id = :id");
            $delete_lc->bindParam(':id', $leave_card_entry_id);
            $delete_lc->execute();
        }

        $_SESSION['success'] = "Leave application deleted successfully!";
        
        // Check if redirect_to is set, otherwise use default
        if (isset($_POST['redirect_to']) && $_POST['redirect_to'] == 'list_leave') {
            header("Location: list_leave.php");
        } else {
            $dept = isset($_POST['dept']) ? $_POST['dept'] : '';
            header("Location: leave_application.php?dept=" . urlencode($dept) . "&personnel_id=" . $personnel_id);
        }
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting leave application: " . $e->getMessage();
        
        // Check if redirect_to is set, otherwise use default
        if (isset($_POST['redirect_to']) && $_POST['redirect_to'] == 'list_leave') {
            header("Location: list_leave.php");
        } else {
            $dept = isset($_POST['dept']) ? $_POST['dept'] : '';
            header("Location: leave_application.php?dept=" . urlencode($dept) . "&personnel_id=" . $_POST['personnel_id']);
        }
        exit();
    }
}

// ====================================
// FUNCTION: CREATE LEAVE CARD ENTRY FROM APPROVED APPLICATION
// ====================================
function createLeaveCardEntry($conn, $leave_application_id, $personnel_id, $leave_type, $date_from, $date_to, $number_of_days, $vl_with_pay_deduction, $vl_without_pay_deduction, $sl_with_pay_deduction, $sl_without_pay_deduction) {
    try {
        error_log("DEBUG: createLeaveCardEntry called with personnel_id=$personnel_id, leave_type=$leave_type, date_from=$date_from, date_to=$date_to");
        
        // Determine if this is a special leave (no deductions)
        $special_leave_types = [
            'Maternity Leave',
            'Paternity Leave',
            'Special Privilege Leave',
            'Solo Parent Leave',
            'Study Leave',
            'Study Leave - Completion of Master\'s Degree',
            'Study Leave - BAR/Board Examination Review',
            '10-Day VAWC Leave',
            'Rehabilitation Privilege',
            'Special Leave Benefits for Women',
            'Special Emergency (Calamity) Leave',
            'Adoption Leave'
        ];

        $is_special_leave = in_array($leave_type, $special_leave_types) ? 1 : 0;

        // Map leave type to simple category
        $particulars = $leave_type;
        if (strpos($leave_type, 'Vacation') !== false) {
            $particulars = 'Vacation Leave';
        } elseif (strpos($leave_type, 'Sick') !== false) {
            $particulars = 'Sick Leave';
        } elseif (strpos($leave_type, 'Mandatory') !== false || strpos($leave_type, 'Forced') !== false) {
            $particulars = 'Mandatory/Forced Leave';
        }

        // Get the period from application date - use the MONTH of the leave start date
        $period_date = new DateTime($date_from);
        // Set period_from to first day of the month
        $period_from = $period_date->format('Y-m-01');
        // Set period_to to last day of the month
        $period_to = $period_date->format('Y-m-t');

        // Prepare leave card values
        // For leave applications, we create a "charge" entry with the deductions
        // earned values are 0 since this is a consumption of existing credits
        $vl_earned = 0;
        $sl_earned = 0;
        $vl_with_pay = 0;
        $sl_with_pay = 0;
        $vl_without_pay = 0;
        $sl_without_pay = 0;

        // Assign deductions based on leave type and WITH PAY / WITHOUT PAY choice
        if (strpos($leave_type, 'Vacation') !== false || strpos($leave_type, 'Mandatory') !== false || strpos($leave_type, 'Forced') !== false) {
            // Vacation Leave, Mandatory/Forced Leave
            $vl_with_pay = floatval($vl_with_pay_deduction ?? 0);
            $vl_without_pay = floatval($vl_without_pay_deduction ?? 0);
        } elseif (strpos($leave_type, 'Sick') !== false) {
            // Sick Leave
            $sl_with_pay = floatval($sl_with_pay_deduction ?? 0);
            $sl_without_pay = floatval($sl_without_pay_deduction ?? 0);
        } elseif ($is_special_leave) {
            // Special leave - save values but mark as special (doesn't deduct from earned)
            $vl_with_pay = floatval($vl_with_pay_deduction ?? 0);
            $vl_without_pay = floatval($vl_without_pay_deduction ?? 0);
            $sl_with_pay = floatval($sl_with_pay_deduction ?? 0);
            $sl_without_pay = floatval($sl_without_pay_deduction ?? 0);
        } else {
            // Default for "Others" and any unmatched types - use whatever was submitted
            // The frontend should have already determined which type to deduct
            $vl_with_pay = floatval($vl_with_pay_deduction ?? 0);
            $vl_without_pay = floatval($vl_without_pay_deduction ?? 0);
            $sl_with_pay = floatval($sl_with_pay_deduction ?? 0);
            $sl_without_pay = floatval($sl_without_pay_deduction ?? 0);
        }
        
        error_log("DEBUG: Deductions calculated - vl_with_pay=$vl_with_pay, vl_without_pay=$vl_without_pay, sl_with_pay=$sl_with_pay, sl_without_pay=$sl_without_pay");

        // Insert into leave_card table using correct column names
        $lc_stmt = $conn->prepare("INSERT INTO leave_card (
            personnel_id,
            period_from,
            period_to,
            particulars,
            vl_earned,
            vl_with_pay,
            vl_without_pay,
            sl_earned,
            sl_with_pay,
            sl_without_pay,
            is_special_leave,
            date_from,
            date_to,
            number_of_days,
            created_from_application
        ) VALUES (
            :personnel_id,
            :period_from,
            :period_to,
            :particulars,
            :vl_earned,
            :vl_with_pay,
            :vl_without_pay,
            :sl_earned,
            :sl_with_pay,
            :sl_without_pay,
            :is_special_leave,
            :date_from,
            :date_to,
            :number_of_days,
            1
        )");

        $lc_stmt->bindParam(':personnel_id', $personnel_id);
        $lc_stmt->bindParam(':period_from', $period_from);
        $lc_stmt->bindParam(':period_to', $period_to);
        $lc_stmt->bindParam(':particulars', $particulars);
        $lc_stmt->bindParam(':vl_earned', $vl_earned);
        $vl_with_pay = floatval($vl_with_pay);
        $lc_stmt->bindParam(':vl_with_pay', $vl_with_pay);
        $vl_without_pay = floatval($vl_without_pay);
        $lc_stmt->bindParam(':vl_without_pay', $vl_without_pay);
        $lc_stmt->bindParam(':sl_earned', $sl_earned);
        $sl_with_pay = floatval($sl_with_pay);
        $lc_stmt->bindParam(':sl_with_pay', $sl_with_pay);
        $sl_without_pay = floatval($sl_without_pay);
        $lc_stmt->bindParam(':sl_without_pay', $sl_without_pay);
        $lc_stmt->bindParam(':is_special_leave', $is_special_leave);
        $lc_stmt->bindParam(':date_from', $date_from);
        $lc_stmt->bindParam(':date_to', $date_to);
        $lc_stmt->bindParam(':number_of_days', $number_of_days);

        error_log("DEBUG: About to execute insert with period_from=$period_from, period_to=$period_to, vl_with_pay=$vl_with_pay, sl_with_pay=$sl_with_pay");
        $lc_stmt->execute();
        error_log("DEBUG: Insert executed successfully");

        // Get the inserted leave card entry ID
        $leave_card_entry_id = $conn->lastInsertId();

        // Return the leave card entry ID for linking
        return $leave_card_entry_id;

    } catch (PDOException $e) {
        // Log error but don't stop the main process
        error_log("Error creating leave card entry: " . $e->getMessage());
        return false;
    }
}

// Redirect if accessed directly
header("Location: leave_application.php");
exit();
?>
