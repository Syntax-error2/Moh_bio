
<?php include('session.php'); ?>
<?php require_once('official_duty.php'); ?>
 
<?php 

    if(isset($_POST['updateDailyLog'])){
        
    $logDate=$_POST['logDate'];
    $parsedLogDate = DateTime::createFromFormat('!Y-m-d', $logDate);
    if (!$parsedLogDate || $parsedLogDate->format('Y-m-d') !== $logDate) {
        http_response_code(400);
        exit('Invalid log date.');
    }
    $shift_id=$_POST['shift_id'];
    
    $studData_query = $conn->prepare('SELECT * FROM personnels WHERE personnel_id = :personnel_id');
    $studData_query->execute(['personnel_id' => $_GET['personnel_id']]);
    $sd_row = $studData_query->fetch();
    if (!$sd_row || (empty($sd_row['RFTag_id']) && empty($sd_row['biometric_id']))) {
        http_response_code(400);
        exit('This employee needs an RFID or biometric ID before a daily log can be saved.');
    }
    $RFTag_id = !empty($sd_row['RFTag_id']) ? $sd_row['RFTag_id'] : $sd_row['biometric_id'];
    $legacyLogDate = $parsedLogDate->format('m/d/Y');
    
    $am_in_lq=$_GET['am_in_lq'];

    if (!isset($_POST['am_in_late'])){ $late_status1="off"; }else{ $late_status1="on"; }
    
    $am_in_hr=$_POST['am_in_hr'];
    $am_in_min=$_POST['am_in_min'];
    $am_in_ampm=$_POST['am_in_ampm'];
    
    $am_IN=$am_in_hr.":".$am_in_min." ".$am_in_ampm;
    
    if($am_in_hr=='-' OR $am_in_min=='-' OR $am_in_ampm=='-'){
        $am_in_stat="No Log";
        if($am_in_lq=="Update"){
            $conn->query("DELETE FROM personnel_logs WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='AM IN'");
            $am_in_stat="Deleted";
        }
    }else{
        $am_in_stat="Updated";
        $am_in_sec = strtotime($am_IN) - strtotime("today");
        
        if($am_in_lq=="Update"){
            $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$am_IN', logTime_sec='$am_in_sec', late_status='$late_status1', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='AM IN'");
        }else{
            $conn->query("INSERT INTO personnel_logs (RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, logFlow, late_status, remarks) VALUES ('$RFTag_id', '$sd_row[img]', '', '$sd_row[lname]', '$sd_row[fname]', '$sd_row[mname]', '$sd_row[suffix]', '$_GET[dept]', '$shift_id', '$logDate', '$am_IN', '$am_in_sec', 'AM IN', '$late_status1', 'Inserted')");    
        }
    }
    
    
    
    $am_out_lq=$_GET['am_out_lq'];
    
    if (!isset($_POST['am_out_undertime'])){ $late_status2="off"; }else{ $late_status2="on"; }
 
    $am_out_hr=$_POST['am_out_hr'];
    $am_out_min=$_POST['am_out_min'];
    $am_out_ampm=$_POST['am_out_ampm'];
    $am_OUT=$am_out_hr.":".$am_out_min." ".$am_out_ampm;
    
    if($am_out_hr=='-' OR $am_out_min=='-' OR $am_out_ampm=='-'){
        $am_out_stat="No Log";
        if($am_out_lq=="Update"){
            $conn->query("DELETE FROM personnel_logs WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='AM OUT'");
            $am_out_stat="Deleted";
        }
    }else{
        $am_out_stat="Updated";
        $am_out_sec = strtotime($am_OUT) - strtotime("today");
        
        if($am_out_lq=="Update"){
            $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$am_OUT', logTime_sec='$am_out_sec', late_status='$late_status2', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='AM OUT'");
        }else{
            $conn->query("INSERT INTO personnel_logs (RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, logFlow, late_status, remarks) VALUES ('$RFTag_id', '$sd_row[img]', '', '$sd_row[lname]', '$sd_row[fname]', '$sd_row[mname]', '$sd_row[suffix]', '$_GET[dept]', '$shift_id', '$logDate', '$am_OUT', '$am_out_sec', 'AM OUT', '$late_status2', 'Inserted')");    
        }
    }
        
    
    
    
    
    $pm_in_lq=$_GET['pm_in_lq'];
    
    if (!isset($_POST['pm_in_late'])){ $late_status3="off"; }else{ $late_status3="on"; }
    
    $pm_in_hr=$_POST['pm_in_hr'];
    $pm_in_min=$_POST['pm_in_min'];
    $pm_in_ampm=$_POST['pm_in_ampm'];
    $pm_IN=$pm_in_hr.":".$pm_in_min." ".$pm_in_ampm;
    
    if($pm_in_hr=='-' OR $pm_in_min=='-' OR $pm_in_ampm=='-'){
        $pm_in_stat="No Log";
        if($pm_in_lq=="Update"){
            $conn->query("DELETE FROM personnel_logs WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='PM IN'");
            $pm_in_stat="Deleted";
        }
    }else{
        $pm_in_stat="Updated";
        $pm_in_sec = strtotime($pm_IN) - strtotime("today");
        
        if($pm_in_lq=="Update"){
            $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$pm_IN', logTime_sec='$pm_in_sec', late_status='$late_status3', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='PM IN'");
        }else{
            $conn->query("INSERT INTO personnel_logs (RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, logFlow, late_status, remarks) VALUES ('$RFTag_id', '$sd_row[img]', '', '$sd_row[lname]', '$sd_row[fname]', '$sd_row[mname]', '$sd_row[suffix]', '$_GET[dept]', '$shift_id', '$logDate', '$pm_IN', '$pm_in_sec', 'PM IN', '$late_status3', 'Inserted')");    
        }
    }
    
    
    
    
    $pm_out_lq=$_GET['pm_out_lq'];
 
    if (!isset($_POST['pm_out_undertime'])){ $late_status4="off"; }else{ $late_status4="on"; }
    
    $pm_out_hr=$_POST['pm_out_hr'];
    $pm_out_min=$_POST['pm_out_min'];
    $pm_out_ampm=$_POST['pm_out_ampm'];
    $pm_OUT=$pm_out_hr.":".$pm_out_min." ".$pm_out_ampm;
    
    if($pm_out_hr=='-' OR $pm_out_min=='-' OR $pm_out_ampm=='-'){
        $pm_out_stat="No Log";
        if($pm_out_lq=="Update"){
            $conn->query("DELETE FROM personnel_logs WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='PM OUT'");
            $pm_out_stat="Deleted";
        }
    }else{
        $pm_out_stat="Updated";
        $pm_out_sec = strtotime($pm_OUT) - strtotime("today");
        
        if($pm_out_lq=="Update"){
            $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$pm_OUT', logTime_sec='$pm_out_sec', late_status='$late_status4', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate IN ('$logDate', '$legacyLogDate') AND logFlow='PM OUT'");
        }else{
            $conn->query("INSERT INTO personnel_logs (RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, logFlow, late_status, remarks) VALUES ('$RFTag_id', '$sd_row[img]', '', '$sd_row[lname]', '$sd_row[fname]', '$sd_row[mname]', '$sd_row[suffix]', '$_GET[dept]', '$shift_id', '$logDate', '$pm_OUT', '$pm_out_sec', 'PM OUT', '$late_status4', 'Inserted')");    
        }
    }
    
    
    ?>
    
    <script>
    window.alert('AM IN: <?php echo $am_in_stat.' | AM OUT: '.$am_out_stat.' | PM IN: '.$pm_in_stat.' | PM OUT: '.$pm_out_stat; ?> successfully logged for date: <?php echo $logDate; ?>');
    window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
    </script>
    
    <?php } ?>
    
    
<?php if(isset($_POST['saveRD'])){
    
    $studData_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'");
    $sd_row=$studData_query->fetch();
    $logRFTagId = !empty($sd_row['RFTag_id']) ? $sd_row['RFTag_id'] : $sd_row['biometric_id'];
 
    $logDate=$_POST['selectedMM'].'/'.$_POST['selectedDD'].'/'.$_POST['selectedYYYY'];
    
    $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks)
    VALUES ('$logRFTagId', '$sd_row[img]', '$sd_row[lname]', '$sd_row[fname]', '$sd_row[mname]', '$sd_row[suffix]', '$sd_row[do_id]', '$sd_row[shift_id]', '$logDate', 'REST DAY')");
        
    ?>
    
    <script>
    window.alert('Rest day successfully logged for date: <?php echo $logDate; ?>');
    window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
    </script>
    
<?php } ?>


<?php if(isset($_POST['delRD'])){

    $conn->query("DELETE FROM personnel_logs WHERE log_id='$_POST[log_id]'");
        
    ?>
    
    <script>
    window.alert('Rest day successfully deleted for date: <?php echo $_GET['logDate']; ?>');
    window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
    </script>
    
<?php } ?>

<?php if(isset($_POST['updateDTRLog'])){

    $remarks = $_POST['remarks'];
    $personnel_id = $_GET['personnel_id'];
    
    $studData_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$personnel_id'");
    $sd_row = $studData_query->fetch();
    $logRFTagId = !empty($sd_row['RFTag_id']) ? $sd_row['RFTag_id'] : $sd_row['biometric_id'];
    
    $actual_dates_count = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'con') === 0 && !empty($value)) {
            $actual_dates_count++;
        }
    }
    $no_of_days = (int)$_POST['no_of_days'];
    if ($actual_dates_count > 0) {
        $no_of_days = max($no_of_days, $actual_dates_count);
    }

    $is_forced_leave = ($remarks === 'Mandatory/Forced Leave' || $remarks === 'Forced Leave' || $remarks === 'Force Leave' || stripos($remarks, 'forced leave') !== false || stripos($remarks, 'force leave') !== false);
    if ($is_forced_leave) {
        $remarks = 'Mandatory/Forced Leave';
        if ($no_of_days > 5 || $actual_dates_count > 5) {
            echo "<script>alert('Error: Mandatory/Forced Leave is strictly limited to a maximum of 5 days only!'); window.history.back();</script>";
            exit;
        }

        // Check cumulative Forced Leave applied in the same calendar year
        $first_input_date = isset($_POST['con1']) ? $_POST['con1'] : date('Y-m-d');
        $app_year = date('Y', strtotime($first_input_date));

        $stmt_fl = $conn->prepare("SELECT numDays, leave_date FROM leave_applicants WHERE applicant_id = ? AND (leave_type = 'Mandatory/Forced Leave' OR leave_type = 'Forced Leave' OR leave_type LIKE '%Forced Leave%')");
        $stmt_fl->execute([$personnel_id]);
        $used_fl_days = 0;
        while($fl_row = $stmt_fl->fetch(PDO::FETCH_ASSOC)) {
            $ld_parts = explode(' - ', $fl_row['leave_date']);
            $rec_year = date('Y', strtotime(trim($ld_parts[0])));
            if ($rec_year === $app_year) {
                $used_fl_days += (float)$fl_row['numDays'];
            }
        }

        if (($used_fl_days + (float)$no_of_days) > 5) {
            $remaining = max(0, 5 - $used_fl_days);
            $emp_name = trim($sd_row['fname'] . ' ' . $sd_row['lname']);
            echo "<script>alert('Error: {$emp_name} has already applied/used {$used_fl_days} day(s) of Mandatory/Forced Leave for {$app_year}. Maximum allowed is 5 days per calendar year (Remaining available: {$remaining} day(s)).'); window.history.back();</script>";
            exit;
        }
    }
    
    $leave_code = isset($_POST['add_to']) ? $_POST['travel_code'] : '';
    
    $n1 = 0;
    $first_date = '';
    $last_date = '';
    
    while($n1 < $no_of_days) {
        $n1++;
        $input_name = 'con' . $n1;
        if(isset($_POST[$input_name])) {
            $logDate = $_POST[$input_name];
            
            if($n1 == 1) {
                $first_date = $logDate;
            }
            $last_date = $logDate;
            
            $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks, travel_leave_code)
            VALUES ('$logRFTagId', '$sd_row[img]', '$sd_row[lname]', '$sd_row[fname]', '$sd_row[mname]', '$sd_row[suffix]', '$sd_row[do_id]', '$sd_row[shift_id]', '$logDate', '$remarks', '$leave_code')");
        }
    }
    
    // IF Leave Type, deduct from balances (or record non-cumulative special leaves with pay)
    $leave_types = ['VACATION LEAVE', 'SICK LEAVE', 'MATERNITY LEAVE (RA 8282)', 'PATERNITY LEAVE (RA 8187)', 'PARENTAL LEAVE for SOLO PARENTS (RA 8972)', 'Mandatory/Forced Leave', 'Forced Leave', 'Force Leave', 'WELLNESS LEAVE', 'Wellness Leave', 'EMERGENCY LEAVE', 'Emergency Leave', 'TERMINAL LEAVE', 'Terminal Leave'];
    $leave_type_desc = isset($_POST['purpose_title']) ? $_POST['purpose_title'] : '';
    $is_special_leave = isNonDeductibleSpecialLeave($remarks, $leave_type_desc);
    
    if (in_array($remarks, $leave_types) || $is_special_leave) {
        
        $final_logDate = $first_date . " - " . $last_date;
        
        $conn->query("INSERT INTO leave_applicants(leave_code, leave_date, leave_type, leave_type_desc, applicant_id, do_id, numDays)
        VALUES ('$leave_code', '$final_logDate', '$remarks', '$leave_type_desc', '$personnel_id', '$sd_row[do_id]', '$no_of_days')");
        
        $numDays = (float)$no_of_days;
        $vl_deduct = 0;
        $sl_deduct = 0;
        $vl_wout_pay = 0;
        $sl_wout_pay = 0;
        $vl_w_pay = 0.000;
        $sl_w_pay = 0.000;
        
        $curr_vl = (float)$sd_row['vl_balance'];
        $curr_sl = (float)$sd_row['sl_balance'];
        
        if ($is_special_leave) {
            // Special non-cumulative leaves (Wellness, Maternity, Paternity, Solo Parent, Rehab, Emergency)
            // have no deduction from balances (wala buhin sa balance)
            $vl_deduct = 0;
            $sl_deduct = 0;
            $vl_wout_pay = 0;
            $sl_wout_pay = 0;
            $vl_w_pay = $numDays;
        } else if ($remarks == 'TERMINAL LEAVE' || $remarks == 'Terminal Leave') {
            // Terminal Leave default auto cascade: VL first, then SL
            if ($curr_vl >= $numDays) {
                $vl_deduct = $numDays;
                $curr_vl -= $numDays;
            } else {
                $vl_deduct = $curr_vl;
                $rem = $numDays - $curr_vl;
                $curr_vl = 0;
                if ($curr_sl >= $rem) {
                    $sl_deduct = $rem;
                    $curr_sl -= $rem;
                } else {
                    $sl_deduct = $curr_sl;
                    $vl_wout_pay = $rem - $curr_sl;
                    $curr_vl = 0;
                }
            }
        } else if ($remarks == 'VACATION LEAVE' || $remarks == 'Mandatory/Forced Leave' || $remarks == 'Forced Leave' || $remarks == 'Force Leave') {
            if ($curr_vl >= $numDays) {
                $vl_deduct = $numDays;
                $curr_vl -= $numDays;
            } else {
                $vl_deduct = $curr_vl;
                $vl_wout_pay = $numDays - $curr_vl;
                $curr_vl = 0;
            }
        } else if ($remarks == 'SICK LEAVE') {
            if ($curr_sl >= $numDays) {
                $sl_deduct = $numDays;
                $curr_sl -= $numDays;
            } else {
                $sl_deduct = $curr_sl;
                $rem = $numDays - $curr_sl;
                $curr_sl = 0;
                if ($curr_vl >= $rem) {
                    $vl_deduct = $rem;
                    $curr_vl -= $rem;
                } else {
                    $vl_deduct = $curr_vl;
                    $sl_wout_pay = $rem - $curr_vl;
                    $curr_vl = 0;
                }
            }
        }
        
        if (!$is_special_leave) {
            if ($vl_deduct > 0) $vl_w_pay = $vl_deduct;
            if ($sl_deduct > 0) $sl_w_pay = $sl_deduct;
        }
        
        $update = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
        $update->execute([$curr_vl, $curr_sl, $personnel_id]);
        
        $p_from = date('m-d-y', strtotime($first_date));
        $p_to = date('m-d-y', strtotime($last_date));
        $period_str = $p_from . " - " . $p_to;
        
        $particulars = $leave_type_desc;
        if(empty(trim($particulars))) {
            $particulars = $remarks;
        }
        
        $logRemarks = "Encoded $remarks" . ($leave_code ? " [Code: $leave_code]" : "");
        if ($vl_wout_pay > 0 || $sl_wout_pay > 0) {
            $lwop_tot = $vl_wout_pay + $sl_wout_pay;
            $logRemarks .= " (Plus $lwop_tot LWOP)";
        }
        
        $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, period, particulars, vl_amount, sl_amount, vl_w_pay, sl_w_pay, vl_wout_pay, sl_wout_pay, remarks) VALUES (?, 'Leave Application', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$personnel_id, $period_str, $particulars, -$vl_deduct, -$sl_deduct, $vl_w_pay, $sl_w_pay, $vl_wout_pay, $sl_wout_pay, $logRemarks]);
    }
    
    if (isset($_POST['add_to'])) {
        $conn->query("UPDATE travel_num_generator SET sequence = '$_POST[new_tng_sequence]'");
    }
    
    if (isset($_POST['add_201_sr'])) {
        $dateFrom = date('m/d/Y', strtotime($first_date));
        $dateTo = date('m/d/Y', strtotime($last_date));
        
        $conn->query("INSERT INTO personnel_seminars (personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, event_date_to) 
        VALUES ('$personnel_id', '$_POST[purpose_title]', '$_POST[description]', '$_POST[location_venue]', '$dateFrom', '$dateTo')");
    }
    
?>
    <script>
    window.alert('DTR Note successfully logged for selected dates.');
    window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
    </script>
<?php } ?>
