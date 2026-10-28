<?php

include('dbcon.php');
require_once('session.php'); 
require_once('official_duty.php');


if(isset($_POST['add_travel']) || isset($_POST['add_leave'])){

    if (!isset($_POST['no_of_days'])) {
        $_POST['no_of_days'] = 0;
    }
    
    // Clean and auto-generate dates if user typed Total Days but didn't click Add Date
    $actual_dates = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'con') === 0 && !empty($value)) {
            $actual_dates[] = $value;
        }
    }
    
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
    if ($remarks == '1') {
        $remarks = isset($_POST['leave_spec']) && trim($_POST['leave_spec']) !== '' ? trim($_POST['leave_spec']) : 'Others';
    }
    
    $ps_time_out = !empty($_POST['pass_slip_time_out']) ? date('h:i A', strtotime($_POST['pass_slip_time_out'])) : '';
    $ps_time_in = !empty($_POST['pass_slip_time_in']) ? date('h:i A', strtotime($_POST['pass_slip_time_in'])) : '';
    $ps_time_range = '';
    if (!empty($ps_time_out) && !empty($ps_time_in)) {
        $ps_time_range = $ps_time_out . ' - ' . $ps_time_in;
    } elseif (!empty($ps_time_out)) {
        $ps_time_range = 'Out: ' . $ps_time_out;
    } elseif (!empty($ps_time_in)) {
        $ps_time_range = 'In: ' . $ps_time_in;
    }

    $is_forced_leave = ($remarks === 'Mandatory/Forced Leave' || $remarks === 'Forced Leave' || $remarks === 'Force Leave' || stripos($remarks, 'forced leave') !== false || stripos($remarks, 'force leave') !== false);
    if ($is_forced_leave) {
        $remarks = 'Mandatory/Forced Leave';
    }
    $is_official_duty = isOfficialDutyNotation($remarks) || isOfficialDutyNotation($_POST['leave_type_desc'] ?? '');
    if (isset($_POST['add_leave']) && $is_official_duty) {
        // The leave form does not issue a travel-order number. Use the travel workflow.
        echo "<script>alert('Official business or travel is not leave. Use Add Travel Order so it appears in the DTR without changing leave credits.'); window.location='list_travel_order.php?cw=list_travel';</script>";
        exit;
    }
    if (isset($_POST['add_leave']) && $is_forced_leave) {
        $check_days = isset($_POST['no_of_days']) ? (float)$_POST['no_of_days'] : (float)count($actual_dates);
        if ($check_days > 5 || count($actual_dates) > 5) {
            echo "<script>alert('Error: Mandatory/Forced Leave is strictly limited to a maximum of 5 days only!'); window.history.back();</script>";
            exit;
        }
    }
    $is_monetization_or_terminal = (strpos(strtolower($remarks), 'monetization') !== false || strpos(strtolower($remarks), 'terminal') !== false);

    if (count($actual_dates) > 0) {
        sort($actual_dates);
        $no_of_days = isset($_POST['no_of_days']) ? (int)$_POST['no_of_days'] : count($actual_dates);
        
        if (count($actual_dates) < $no_of_days && !$is_monetization_or_terminal) {
            $last_date = end($actual_dates);
            $missing = $no_of_days - count($actual_dates);
            $current = $last_date;
            for ($i = 0; $i < $missing; $i++) {
                $current = date('Y-m-d', strtotime($current . ' +1 weekday'));
                $actual_dates[] = $current;
            }
        }
        
        if (!$is_monetization_or_terminal) {
            $_POST['no_of_days'] = count($actual_dates);
        }
        
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'con') === 0) {
                unset($_POST[$key]);
            }
        }
        for ($i = 1; $i <= count($actual_dates); $i++) {
            $_POST['con'.$i] = $actual_dates[$i-1];
        }
    }

$p1=0;

$no_of_personnels = isset($_POST['no_of_personnels']) ? $_POST['no_of_personnels'] : 1;

while($p1 < $no_of_personnels) //40 of 75 personnels...
{
    
    $n1=0;
    $p1=$p1+1;
    
    if($p1==1){
         
        $parts = explode(" | ", $_POST["jud1"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==2){
      
        $parts = explode(" | ", $_POST["jud2"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==3){
       
        $parts = explode(" | ", $_POST["jud3"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==4){
        //2019-01-21 
        $parts = explode(" | ", $_POST["jud4"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==5){
        
        $parts = explode(" | ", $_POST["jud5"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==6){
        
        $parts = explode(" | ", $_POST["jud6"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==7){
        
        $parts = explode(" | ", $_POST["jud7"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==8){
        
        $parts = explode(" | ", $_POST["jud8"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==9){
        
        $parts = explode(" | ", $_POST["jud9"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==10){
        
        $parts = explode(" | ", $_POST["jud10"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==11){
        
        $parts = explode(" | ", $_POST["jud11"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==12){
        
        $parts = explode(" | ", $_POST["jud12"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==13){
        
        $parts = explode(" | ", $_POST["jud13"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==14){
        
        $parts = explode(" | ", $_POST["jud14"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==15){
        
        $parts = explode(" | ", $_POST["jud15"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==16){
        
        $parts = explode(" | ", $_POST["jud16"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==17){
        
        $parts = explode(" | ", $_POST["jud17"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==18){
        
        $parts = explode(" | ", $_POST["jud18"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==19){
        
        $parts = explode(" | ", $_POST["jud19"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==20){
        
        $parts = explode(" | ", $_POST["jud20"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==21){
        
        $parts = explode(" | ", $_POST["jud21"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==22){
        
        $parts = explode(" | ", $_POST["jud22"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==23){
        
        $parts = explode(" | ", $_POST["jud23"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==24){
        
        $parts = explode(" | ", $_POST["jud24"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==25){
        
        $parts = explode(" | ", $_POST["jud25"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==26){
        
        $parts = explode(" | ", $_POST["jud26"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==27){
        
        $parts = explode(" | ", $_POST["jud27"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==28){
        
        $parts = explode(" | ", $_POST["jud28"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==29){
        
        $parts = explode(" | ", $_POST["jud29"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==30){
        
        $parts = explode(" | ", $_POST["jud30"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==31){
        
        $parts = explode(" | ", $_POST["jud31"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==32){
        
        $parts = explode(" | ", $_POST["jud32"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==33){
        
        $parts = explode(" | ", $_POST["jud33"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==34){
        
        $parts = explode(" | ", $_POST["jud34"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==35){
        
        $parts = explode(" | ", $_POST["jud35"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==36){
        
        $parts = explode(" | ", $_POST["jud36"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==37){
        
        $parts = explode(" | ", $_POST["jud37"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==38){
        
        $parts = explode(" | ", $_POST["jud38"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
     if($p1==39){
        
        $parts = explode(" | ", $_POST["jud39"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==40){
        
        $parts = explode(" | ", $_POST["jud40"]); $personnel_id_extracted = trim($parts[0]);
    }
    


$perData1_query = $conn->query("SELECT RFTag_id, biometric_id, personnel_id, img, lname, fname, mname, suffix, do_id, shift_id FROM personnels WHERE personnel_id='$personnel_id_extracted'");
$pd1_row=$perData1_query->fetch();
$logRFTagId = !empty($pd1_row['RFTag_id']) ? $pd1_row['RFTag_id'] : $pd1_row['biometric_id'];
        
$img='personnelImg/'.$pd1_row['img'];

        $substitute_pid = 0;
        if (isset($_POST['substitute_rfid']) && !empty(trim($_POST['substitute_rfid']))) {
            $parts2 = explode(" | ", $_POST['substitute_rfid']);
            $sub_id_ext = trim($parts2[0]);
            $perData2_query = $conn->query("SELECT personnel_id FROM personnels WHERE personnel_id='$sub_id_ext'");
            $pd2_row = $perData2_query->fetch();
            if ($pd2_row) {
                $substitute_pid = $pd2_row['personnel_id'];
            }
        }
        
        $leave_code = isset($_POST['leave_code']) ? $_POST['leave_code'] : (isset($_POST['travel_code']) ? $_POST['travel_code'] : '');
        $leave_type_desc = isset($_POST['leave_type_desc']) ? $_POST['leave_type_desc'] : '';
        
        if (isset($_POST['add_leave']) && $is_forced_leave) {
            $requested_days = (float)$_POST['no_of_days'];
            if ($requested_days > 5) {
                echo "<script>alert('Error: Mandatory/Forced Leave is strictly limited to a maximum of 5 days only!'); window.history.back();</script>";
                exit;
            }
            
            // Check cumulative Forced Leave applied in the same calendar year
            $target_date = !empty($actual_dates) ? $actual_dates[0] : (isset($_POST['con1']) ? $_POST['con1'] : date('Y-m-d'));
            $app_year = date('Y', strtotime($target_date));
            
            $stmt_fl = $conn->prepare("SELECT numDays, leave_date FROM leave_applicants WHERE applicant_id = ? AND (leave_type = 'Mandatory/Forced Leave' OR leave_type = 'Forced Leave' OR leave_type LIKE '%Forced Leave%')");
            $stmt_fl->execute([$pd1_row['personnel_id']]);
            $used_fl_days = 0;
            while($fl_row = $stmt_fl->fetch(PDO::FETCH_ASSOC)) {
                $ld_parts = explode(' - ', $fl_row['leave_date']);
                $rec_year = date('Y', strtotime(trim($ld_parts[0])));
                if ($rec_year === $app_year) {
                    $used_fl_days += (float)$fl_row['numDays'];
                }
            }
            
            if (($used_fl_days + $requested_days) > 5) {
                $remaining = max(0, 5 - $used_fl_days);
                $emp_name = trim($pd1_row['fname'] . ' ' . $pd1_row['lname']);
                echo "<script>alert('Error: {$emp_name} has already applied/used {$used_fl_days} day(s) of Mandatory/Forced Leave for {$app_year}. Maximum allowed is 5 days per calendar year (Remaining available: {$remaining} day(s)).'); window.history.back();</script>";
                exit;
            }
        }
        
        $dates_array = [];
        $first_date = '';
        $last_date = '';
        
        while($n1<$_POST['no_of_days'])
        {
            $n1=$n1+1;
            
            $input_name = 'con' . $n1;
            if(isset($_POST[$input_name])) {
                $logDate = $_POST[$input_name];
                
                if($n1 == 1) {
                    $first_date = $logDate;
                }
                $last_date = $logDate;
                
                $dates_array[] = $logDate;
                
                if (!$is_monetization_or_terminal) {
                    $log_remarks = $remarks;
                    if ($remarks === 'PASS SLIP' && !empty($ps_time_range)) {
                        $log_remarks = 'PASS SLIP (' . $ps_time_range . ')';
                    }
                    $log_remarks = substr($log_remarks, 0, 55);
                    $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks, travel_leave_code)
                    VALUES ('$logRFTagId', '$img', '$pd1_row[lname]', '$pd1_row[fname]', '$pd1_row[mname]', '$pd1_row[suffix]', '$pd1_row[do_id]', '$pd1_row[shift_id]', '$logDate', '$log_remarks', '$leave_code')");
                }
            }
        }
        
        if (empty($first_date)) {
            $first_date = date('Y-m-d');
        }
        if (empty($last_date)) {
            $last_date = $first_date;
        }
  
        // Define final_logDate for leave_applicants (e.g. "YYYY-MM-DD - YYYY-MM-DD")
        $final_logDate = $first_date . " - " . $last_date;
  
        if(isset($_POST['add_leave'])) {
            $conn->query("INSERT INTO leave_applicants(leave_code, leave_date, leave_type, leave_type_desc, substitute_id, applicant_id, do_id, numDays)
            VALUES ('$leave_code', '$final_logDate', '$remarks', '$leave_type_desc', '$substitute_pid', '$pd1_row[personnel_id]', '$pd1_row[do_id]', '$_POST[no_of_days]')");
        
        // --- DEDUCT FROM LEAVE BALANCES AND LOG TO LEAVE CARD ---
        $numDays = (float)$_POST['no_of_days'];
        $vl_deduct = 0;
        $sl_deduct = 0;
        $vl_wout_pay = 0;
        $sl_wout_pay = 0;
        $vl_w_pay = 0.000;
        $sl_w_pay = 0.000;
        
        $stmt_bal = $conn->prepare("SELECT vl_balance, sl_balance FROM personnels WHERE personnel_id = ?");
        $stmt_bal->execute([$pd1_row['personnel_id']]);
        $bal_row = $stmt_bal->fetch(PDO::FETCH_ASSOC);
        $curr_vl = (float)$bal_row['vl_balance'];
        $curr_sl = (float)$bal_row['sl_balance'];
        
        $is_non_deductible = isNonDeductibleSpecialLeave($remarks, $leave_type_desc);

        if($is_non_deductible) {
            // Special non-cumulative leaves (Wellness, Maternity, Paternity, Solo Parent, Rehab, Emergency)
            // have no deduction from balances (wala buhin sa balance)
            $vl_deduct = 0;
            $sl_deduct = 0;
            $vl_wout_pay = 0;
            $sl_wout_pay = 0;
            $vl_w_pay = $numDays;
        } else if(stripos($remarks, 'monetization') !== false || stripos($remarks, 'terminal') !== false) {
            $monetizeSource = $_POST['monetization_source'] ?? 'auto';
            if ($monetizeSource === 'sl' || $remarks == 'Monetization (Sick Leave)' || $remarks == 'Terminal Leave (Sick Leave)') {
                $sl_deduct = $numDays;
                $curr_sl -= $numDays;
                $sl_w_pay = $numDays;
            } else if ($monetizeSource === 'vl' || $remarks == 'Monetization (Vacation Leave)' || $remarks == 'Terminal Leave (Vacation Leave)') {
                $vl_deduct = $numDays;
                $curr_vl -= $numDays;
                $vl_w_pay = $numDays;
            } else {
                // Auto: Deduct from VL first, then SL
                if ($curr_vl >= $numDays) {
                    $vl_deduct = $numDays;
                    $curr_vl -= $numDays;
                    $vl_w_pay = $numDays;
                } else {
                    $vl_deduct = $curr_vl;
                    $rem = $numDays - $curr_vl;
                    $curr_vl = 0;
                    $sl_deduct = $rem;
                    $curr_sl -= $rem;
                    $vl_w_pay = $vl_deduct;
                    $sl_w_pay = $rem;
                }
            }
            $vl_wout_pay = 0;
            $sl_wout_pay = 0;
        } else if($remarks == 'Vacation Leave' || $remarks == 'Mandatory/Forced Leave' || $remarks == 'Forced Leave' || $remarks == 'Force Leave') {
            // Tanan sa Absence with pay (VL w/ pay), deducted 1:1 against VL
            $vl_deduct = $numDays;
            $curr_vl -= $numDays;
            $vl_w_pay = $numDays;
            $vl_wout_pay = 0;
            $sl_deduct = 0;
            $sl_w_pay = 0;
            $sl_wout_pay = 0;
        } else if($remarks == 'Sick Leave') {
            // Tanan sa Absence with pay (SL w/ pay), deducted 1:1 against SL
            $sl_deduct = $numDays;
            $curr_sl -= $numDays;
            $sl_w_pay = $numDays;
            $sl_wout_pay = 0;
            $vl_deduct = 0;
            $vl_w_pay = 0;
            $vl_wout_pay = 0;
        }
        
        if (!$is_non_deductible) {
            if ($vl_deduct > 0) $vl_w_pay = $vl_deduct;
            if ($sl_deduct > 0) $sl_w_pay = $sl_deduct;
        }

        $update = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
        $update->execute([$curr_vl, $curr_sl, $pd1_row['personnel_id']]);
        
        // Format period for Leave Card (e.g. "06-13-25" or "06-13-25 - 06-15-25")
        $p_from = date('m-d-y', strtotime($first_date));
        $p_to = date('m-d-y', strtotime($last_date));
        $period_str = $p_from . " - " . $p_to;
        
        $particulars = $_POST['leave_type_desc'];
        if(empty(trim($particulars))) {
            $particulars = $remarks;
            if(stripos($remarks, 'monetization') !== false && !empty($_POST['monetization_source'])) {
                if($_POST['monetization_source'] === 'vl') {
                    $particulars = 'Monetization (VL)';
                } else if($_POST['monetization_source'] === 'sl') {
                    $particulars = 'Monetization (SL)';
                } else {
                    $particulars = 'Monetization';
                }
            } else if(stripos($remarks, 'terminal') !== false && !empty($_POST['monetization_source'])) {
                if($_POST['monetization_source'] === 'vl') {
                    $particulars = 'Terminal Leave (VL)';
                } else if($_POST['monetization_source'] === 'sl') {
                    $particulars = 'Terminal Leave (SL)';
                } else {
                    $particulars = 'Terminal Leave';
                }
            }
        }
        
        if (stripos($remarks, 'terminal') !== false || stripos($particulars, 'terminal') !== false) {
            $logRemarks = "Certified Correct" . (!empty($_POST['leave_code']) ? " [Code: $_POST[leave_code]]" : "");
        } else {
            $logRemarks = "Applied for $remarks [Code: $_POST[leave_code]]";
            $start_yr = !empty($first_date) ? (int)date('Y', strtotime($first_date)) : 0;
            if ($start_yr > 0 && $start_yr < 2026) {
                $logRemarks .= " [HR Encoded]";
            }
            if ($vl_wout_pay > 0 || $sl_wout_pay > 0) {
                $lwop_tot = $vl_wout_pay + $sl_wout_pay;
                $logRemarks .= " (Plus $lwop_tot LWOP)";
            }
        }
        
        try {
            $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, period, particulars, vl_amount, sl_amount, vl_w_pay, sl_w_pay, vl_wout_pay, sl_wout_pay, remarks) VALUES (?, 'Leave Application', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $log->execute([$pd1_row['personnel_id'], $period_str, $particulars, -$vl_deduct, -$sl_deduct, $vl_w_pay, $sl_w_pay, $vl_wout_pay, $sl_wout_pay, $logRemarks]);
        } catch (PDOException $e) {
            if ($e->getCode() == '42S22' || strpos($e->getMessage(), '1054') !== false) {
                $repair_cols = [
                    "period VARCHAR(100) DEFAULT NULL",
                    "particulars VARCHAR(100) DEFAULT NULL",
                    "vl_w_pay DECIMAL(10,3) DEFAULT 0.000",
                    "sl_w_pay DECIMAL(10,3) DEFAULT 0.000",
                    "vl_wout_pay DECIMAL(10,3) DEFAULT 0.000",
                    "sl_wout_pay DECIMAL(10,3) DEFAULT 0.000"
                ];
                foreach ($repair_cols as $c) {
                    try { $conn->query("ALTER TABLE leave_credit_logs ADD COLUMN $c"); } catch (PDOException $ex) {}
                }
                $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, period, particulars, vl_amount, sl_amount, vl_w_pay, sl_w_pay, vl_wout_pay, sl_wout_pay, remarks) VALUES (?, 'Leave Application', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $log->execute([$pd1_row['personnel_id'], $period_str, $particulars, -$vl_deduct, -$sl_deduct, $vl_w_pay, $sl_w_pay, $vl_wout_pay, $sl_wout_pay, $logRemarks]);
            } else {
                throw $e;
            }
        }
        // ----------------------------------
        } // End of if(isset($_POST['add_leave']))

        // --- ADD TRAVEL LOGIC ---
        if(isset($_POST['add_travel'])) {
            if(isset($_POST['add_to'])) {
                $purpose = $_POST['purpose_title'];
                $description = $_POST['description'];
                $location = $_POST['location_venue'];
                
                if ($remarks === 'PASS SLIP') {
                    if (empty(trim($description))) {
                        $description = !empty($ps_time_range) ? "PASS SLIP ($ps_time_range)" : "PASS SLIP";
                    } elseif (!empty($ps_time_range) && stripos($description, $ps_time_range) === false) {
                        $description .= " ($ps_time_range)";
                    }
                }
                
                try {
                    $stmt_to = $conn->prepare("INSERT INTO personnel_official_travel_logs(personnel_id, travel_code, purpose, description, location, travel_date, travel_type, numDays, time_out, time_in) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt_to->execute([$pd1_row['personnel_id'], $leave_code, $purpose, $description, $location, $final_logDate, $remarks, $_POST['no_of_days'], $ps_time_out, $ps_time_in]);
                } catch (PDOException $e) {
                    try {
                        $conn->query("ALTER TABLE personnel_official_travel_logs ADD COLUMN time_out VARCHAR(20) DEFAULT NULL, ADD COLUMN time_in VARCHAR(20) DEFAULT NULL");
                        $stmt_to = $conn->prepare("INSERT INTO personnel_official_travel_logs(personnel_id, travel_code, purpose, description, location, travel_date, travel_type, numDays, time_out, time_in) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt_to->execute([$pd1_row['personnel_id'], $leave_code, $purpose, $description, $location, $final_logDate, $remarks, $_POST['no_of_days'], $ps_time_out, $ps_time_in]);
                    } catch (PDOException $e2) {
                        $conn->query("INSERT INTO personnel_official_travel_logs(personnel_id, travel_code, purpose, description, location, travel_date, travel_type, numDays)
                        VALUES ('$pd1_row[personnel_id]', '$leave_code', '$purpose', '$description', '$location', '$final_logDate', '$remarks', '$_POST[no_of_days]')");
                    }
                }
            }
            
            if(isset($_POST['add_201_sr'])) {
                $purpose = $_POST['purpose_title'];
                $description = $_POST['description'];
                $location = $_POST['location_venue'];
                $dFrom = date('m/d/Y', strtotime($first_date));
                $dTo = date('m/d/Y', strtotime($last_date));
                
                $conn->query("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, event_date_to, entry_type)
                VALUES ('$pd1_row[personnel_id]', '$purpose', '$description', '$location', '$dFrom', '$dTo', '$leave_code')");
            }
        }
        
    }

$perData1_query=null;
$perData2_query=null;

    if(isset($_POST['add_travel']) && isset($_POST['add_to'])) {
        $new_tng_sequence = $_POST['new_tng_sequence'];
        $new_tng_mm = isset($_POST['new_tng_mm']) ? $_POST['new_tng_mm'] : date('m');
        $conn->query("UPDATE travel_num_generator SET sequence='$new_tng_sequence', mm='$new_tng_mm'");
    }

$conn=null; 
   
?>
 
<?php if(isset($_POST['add_leave'])){ ?>
<script>
window.alert('DTR log with date: <?php echo $final_logDate; ?> successfully updated with Leave Entry');
window.location='list_leave.php?cw=list_leave'; 
</script>
<?php } else if(isset($_POST['add_travel'])){ ?>
<script>
window.alert('Travel Order successfully added!');
window.location='list_travel_order.php?cw=list_travel'; 
</script>
<?php } ?>

<?php } ?>


<?php
//DELETE LEAVE         DELETE LEAVE           DELETE LEAVE           DELETE LEAVE

if(isset($_POST['deleteLeave'])){

$new_clearance_query = $conn->query("SELECT * FROM leave_applicants WHERE lap_id='$_GET[lap_id]'");
$nc_row = $new_clearance_query->fetch();
 

$conn->query("DELETE FROM personnel_logs WHERE travel_leave_code='$nc_row[leave_code]'"); 

// --- REFUND LEAVE BALANCES ---
$remarks = $nc_row['leave_type'];

// Try to find the exact deduction log for this leave application
$stmt_log = $conn->prepare("SELECT vl_amount, sl_amount, vl_wout_pay, sl_wout_pay FROM leave_credit_logs WHERE personnel_id = ? AND remarks LIKE ? LIMIT 1");
$stmt_log->execute([$nc_row['applicant_id'], "%[Code: $nc_row[leave_code]]%"]);
$log_row = $stmt_log->fetch(PDO::FETCH_ASSOC);

if ($log_row) {
    // Refund based on the exact amounts that were originally deducted (amounts are stored as negative)
    $vl_refund = abs((float)$log_row['vl_amount']);
    $sl_refund = abs((float)$log_row['sl_amount']);
    
    $update = $conn->prepare("UPDATE personnels SET vl_balance = vl_balance + ?, sl_balance = sl_balance + ? WHERE personnel_id = ?");
    $update->execute([$vl_refund, $sl_refund, $nc_row['applicant_id']]);
    
    $logRemarks = "Refunded deleted $remarks";
    $conn->query("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks) VALUES ('$nc_row[applicant_id]', 'Manual Adjustment', '$vl_refund', '$sl_refund', '$logRemarks')");
} else {
    // Fallback if log is not found (for old leaves before this fix)
    if($remarks == 'Vacation Leave' || $remarks == 'Mandatory/Forced Leave' || $remarks == 'Forced Leave' || $remarks == 'Force Leave' || $remarks == 'Monetization' || $remarks == 'Monetization (Vacation Leave)' || $remarks == 'Terminal Leave (Vacation Leave)') {
        $numDays = (float)$nc_row['numDays'];
        $conn->query("UPDATE personnels SET vl_balance = vl_balance + $numDays WHERE personnel_id = '$nc_row[applicant_id]'");
        $logRemarks = "Refunded deleted $remarks (Fallback)";
        $conn->query("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks) VALUES ('$nc_row[applicant_id]', 'Manual Adjustment', '$numDays', '0', '$logRemarks')");
    } else if($remarks == 'Sick Leave' || $remarks == 'Monetization (Sick Leave)' || $remarks == 'Terminal Leave (Sick Leave)') {
        $numDays = (float)$nc_row['numDays'];
        $conn->query("UPDATE personnels SET sl_balance = sl_balance + $numDays WHERE personnel_id = '$nc_row[applicant_id]'");
        $logRemarks = "Refunded deleted $remarks (Fallback)";
        $conn->query("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks) VALUES ('$nc_row[applicant_id]', 'Manual Adjustment', '0', '$numDays', '$logRemarks')");
    }
}
// -----------------------------

$conn->query("DELETE FROM leave_applicants WHERE lap_id='$_GET[lap_id]'");

$new_clearance_query=null;
$perData1_query=null;
$conn=null;
      
 ?>
 
<script>
window.alert('Leave Entry successfully deleted...');
window.location='list_leave.php?cw=list_leave'; 
</script>

<?php } ?>


<?php
// DELETE TRAVEL
if(isset($_POST['deleteTravel'])){

    $travel_code = $_GET['travel_code'];
    
    // Delete from personnel logs (DTR entries)
    $stmt1 = $conn->prepare("DELETE FROM personnel_logs WHERE travel_leave_code=?");
    $stmt1->execute([$travel_code]);
    
    // Delete from official travel logs
    $stmt2 = $conn->prepare("DELETE FROM personnel_official_travel_logs WHERE travel_code=?");
    $stmt2->execute([$travel_code]);
    
    // Delete from personnel seminars (201 File SR) if added
    $stmt3 = $conn->prepare("DELETE FROM personnel_seminars WHERE entry_type=?");
    $stmt3->execute([$travel_code]);

    $conn=null;
?>
<script>
window.alert('Travel Order successfully deleted...');
window.location='list_travel_order.php?cw=list_travel'; 
</script>
<?php } ?>

<?php
// UPDATE TRAVEL
if(isset($_POST['updateTravel'])){
    $travel_code = $_GET['travel_code'];
    $remarks = $_POST['remarks'] ?? 'OFFICIAL BUSINESS TRIP';
    $purpose = $_POST['purpose_title'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location_venue'] ?? '';
    
    $raw_time_out = $_POST['pass_slip_time_out'] ?? '';
    $raw_time_in = $_POST['pass_slip_time_in'] ?? '';
    $ps_time_out = '';
    if (!empty($raw_time_out)) {
        $ps_time_out = (strlen($raw_time_out) <= 8 && strpos($raw_time_out, ':') !== false) ? date('h:i A', strtotime($raw_time_out)) : $raw_time_out;
    }
    $ps_time_in = '';
    if (!empty($raw_time_in)) {
        $ps_time_in = (strlen($raw_time_in) <= 8 && strpos($raw_time_in, ':') !== false) ? date('h:i A', strtotime($raw_time_in)) : $raw_time_in;
    }
    
    $ps_time_range = '';
    if (!empty($ps_time_out) && !empty($ps_time_in)) {
        $ps_time_range = $ps_time_out . ' - ' . $ps_time_in;
    } elseif (!empty($ps_time_out)) {
        $ps_time_range = 'Out: ' . $ps_time_out;
    } elseif (!empty($ps_time_in)) {
        $ps_time_range = 'In: ' . $ps_time_in;
    }
    
    if ($remarks === 'PASS SLIP') {
        if (empty(trim($description))) {
            $description = !empty($ps_time_range) ? "PASS SLIP ($ps_time_range)" : "PASS SLIP";
        } elseif (!empty($ps_time_range) && stripos($description, $ps_time_range) === false) {
            $description .= " ($ps_time_range)";
        }
    }

    try {
        $conn->query("ALTER TABLE personnel_official_travel_logs ADD COLUMN time_out VARCHAR(20) DEFAULT NULL, ADD COLUMN time_in VARCHAR(20) DEFAULT NULL");
    } catch (Exception $e) {}

    try {
        $stmt_up = $conn->prepare("UPDATE personnel_official_travel_logs SET travel_type=?, purpose=?, description=?, location=?, time_out=?, time_in=? WHERE travel_code=?");
        $stmt_up->execute([$remarks, $purpose, $description, $location, $ps_time_out, $ps_time_in, $travel_code]);
    } catch (PDOException $e) {
        $stmt_up = $conn->prepare("UPDATE personnel_official_travel_logs SET travel_type=?, purpose=?, description=?, location=? WHERE travel_code=?");
        $stmt_up->execute([$remarks, $purpose, $description, $location, $travel_code]);
    }

    $log_remarks = $remarks;
    if ($remarks === 'PASS SLIP' && !empty($ps_time_range)) {
        $log_remarks = 'PASS SLIP (' . $ps_time_range . ')';
    }
    $log_remarks = substr($log_remarks, 0, 55);
    $stmt_pl = $conn->prepare("UPDATE personnel_logs SET remarks=? WHERE travel_leave_code=?");
    $stmt_pl->execute([$log_remarks, $travel_code]);

    if(isset($_POST['add_201_sr'])) {
        $chk_sem = $conn->prepare("SELECT seminar_id FROM personnel_seminars WHERE entry_type=?");
        $chk_sem->execute([$travel_code]);
        if(!$chk_sem->fetch()) {
            $pot = $conn->prepare("SELECT personnel_id, travel_date FROM personnel_official_travel_logs WHERE travel_code=?");
            $pot->execute([$travel_code]);
            while($p_row = $pot->fetch()) {
                $dParts = explode(' - ', $p_row['travel_date']);
                $dFrom = date('m/d/Y', strtotime(trim($dParts[0])));
                $dTo = isset($dParts[1]) ? date('m/d/Y', strtotime(trim($dParts[1]))) : $dFrom;
                $conn->query("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, event_date_to, entry_type)
                VALUES ('$p_row[personnel_id]', '$purpose', '$description', '$location', '$dFrom', '$dTo', '$travel_code')");
            }
        }
    }

    $conn = null;
?>
<script>
window.alert('Travel Order successfully updated!');
window.location='list_travel_order_detailed.php?travel_code=<?php echo urlencode($travel_code); ?>';
</script>
<?php } ?>
