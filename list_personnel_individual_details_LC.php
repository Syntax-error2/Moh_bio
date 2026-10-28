<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   require_once('official_duty.php');
   
   ?>

  <?php
  
 
  $get_dept=$_GET['dept'];
  
  if(isset($_POST['filterPosition'])){
  $filterPosition=$_POST['filter'];
  }else{
  $filterPosition='All';
  } ?>
    
    
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <?php
    $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'");
    $staff_row = $staff_query->fetch();
    
    $emp_stat_query5 = $conn->query("SELECT * from shifts WHERE shift_id='$staff_row[shift_id]'");
    $es_row5=$emp_stat_query5->fetch();
                  
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo $_GET['dept']; ?>">List of Personnel</a></li>
            <li class="breadcrumb-item active"><?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {

                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } ?> - Service Record</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
            
            
            <?php include('encode_daily_log_modal.php'); ?>
            <?php include('restDay_modal.php'); ?>
            <?php include('update_shift_single_modal.php'); ?>
            <?php include('updateMonthlyLog_modal.php'); ?>
            <?php include('print_monthly_attendance_modal_csf48.php'); ?>
            <?php include('print_monthly_attendance_modal.php'); ?>
            <?php include('print_monthly_LV_modal.php'); ?>
             
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">
                  <h4>
                  <img src="personnelImg/<?php echo $staff_row['img']; ?>" width="50" height="50" class="img-fluid" style="margin-bottom: 8px; border: 2px solid green;" />
                                    <?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } ?> <a href="javascript:void(0);" data-toggle="modal" data-target="#updateShiftSingleModal" onclick="event.stopPropagation();" title="Click to change shift / schedule..." style="cursor: pointer; text-decoration: underline; <?php if($es_row5['type'] == 'Regular Shift'){ ?> color: green; <?php }elseif($es_row5['type'] == 'Night Shift'){ ?> color: blue; <?php }elseif($es_row5['type'] == '24 Hours Shift'){ ?> color: brown; <?php }elseif($es_row5['type'] == 'Open Time'){ ?> color: purple; <?php }else{ ?> color: red; <?php } ?>"><small><i class="fa fa-pencil-square-o"></i> ( <?php echo $es_row5 ? htmlspecialchars($es_row5['shift_name']) : 'N/A'; ?> - <?php echo $es_row5 ? htmlspecialchars($es_row5['type']) : 'N/A'; ?> )</small></a>
                                    
                  </h4>
                  </a>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                <div class="col-lg-12" style="margin-top: 12px;">
                          <!--Reports Buttons-->

                            <a title="Encode log..." style="color: black !important; margin-top: 3px;" data-toggle="modal" data-target="#encodeDL<?php echo $staff_row['RFTag_id']; ?>" href="#" class="btn btn-warning btn-sm"><i class="fa fa-clock-o"></i> ENCODE DAILY LOG</a> 
                
                            <a title="Rest day settings..." style="color: black !important; margin-top: 3px;" data-toggle="modal" data-target="#restDaySetup<?php echo $staff_row['RFTag_id']; ?>" href="#" class="btn btn-info btn-sm"><i class="fa fa-heartbeat"></i> SET REST DAY</a> 
                
                            <!-- REPORTS -->
                            <button data-toggle="dropdown" type="button" class="btn btn-outline-primary dropdown-toggle btn-sm" style="margin-top: 3px;"><i class="fa fa-print"></i>  REPORTS <i class="caret"></i></button>
                            
                            <div class="dropdown-menu">
                            
                            <a title="Print Civil Service Form 48..." data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> CSForm 48</a>
                            <a title="Print detailed DTR..." data-toggle="modal" data-target="#print_monthly_attendance<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> Detailed DTR <small>(Monthly)</small></a>
                            <a title="Print Log Validations history..." data-toggle="modal" data-target="#print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-image"></i> Log Validation History <small>(Monthly)</small></a>
                            <!--a title="Print DTR notes history..." data-toggle="modal" data-target="#print_monthly_DTRNotes<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-list"></i> DTR Notes History <small>(Monthly)</small></a>
                            <a title="Print detailed DTR..." data-toggle="modal" data-target="#print_DTRSummary<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-list"></i> DTR Summary <small>(Yearly)</small></a-->
                           
                             
                            </div>
                            <!-- END REPORTS -->
                </div>
                
                <div class="col-lg-12" style="margin-top: 12px;">
                <a class="btn btn-outline-primary" href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> PERSONAL INFORMATION</a>
                <a class="btn btn-outline-primary" href="list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> EDUCATIONAL BACKGROUND</a>
                <a class="btn btn-outline-primary" href="list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> SEMINARS ATTENDED</a>                  <a class="btn btn-outline-primary" href="list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> SERVICE RECORD</a>
                  <?php
                  $has_leave_credits = personnelHasLeaveCredits($conn, $staff_row);
                  if($has_leave_credits) { ?>
                    <a class="btn btn-primary" style="color: white; font-weight: bold;" href="list_personnel_individual_details_LC.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> LEAVE CREDITS</a>
                    <a href="javascript:void(0);" id="btnOpenCertModal" class="btn btn-info" style="color: white; margin-left: 2px;" title="Leave Credit Certification" data-toggle="modal" data-target="#certModal" onclick="$('#certModal').modal('show'); return false;"><i class="fa fa-certificate"></i></a>
                  <?php } ?>
                <a class="btn btn-info" style="color: white;" title="Print personnel data sheet..." href="printPersonnelDataSheet_detailed.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a>
                </div>
 
                    
                   <?php
                   // Leave Credit Certification Values
                   $cert_vl = floatval($staff_row['vl_balance']);
                   $cert_sl = floatval($staff_row['sl_balance']);
                   $cert_total = $cert_vl + $cert_sl;
                   
                   $cert_monthly_salary = floatval($staff_row['rate_per_day']);
                   if ($cert_monthly_salary <= 0) {
                       $sr_cert_q = $conn->query("SELECT salary FROM service_record WHERE personnel_id='".$staff_row['personnel_id']."' ORDER BY sr_id DESC LIMIT 1");
                       if ($sr_cert_q && $sr_cert_r = $sr_cert_q->fetch()) {
                           $raw_sal = preg_replace('/[^0-9.]/', '', $sr_cert_r['salary']);
                           if (stripos($sr_cert_r['salary'], 'annum') !== false || stripos($sr_cert_r['salary'], '/pa') !== false) {
                               $cert_monthly_salary = floatval($raw_sal) / 12;
                           } else {
                               $cert_monthly_salary = floatval($raw_sal);
                           }
                       }
                   }
                   $cert_amount_due = $cert_total * 0.0481927 * $cert_monthly_salary;
                   
                   $cert_des_name = '';
                   $des_cert_q = $conn->query("SELECT des_name FROM designation WHERE des_id='".$staff_row['des_id']."'");
                   if ($des_cert_q && $des_cert_r = $des_cert_q->fetch()) {
                       $cert_des_name = $des_cert_r['des_name'];
                   }
                   $cert_emp_stat = '';
                   $stat_cert_q = $conn->query("SELECT emp_stat_name FROM emp_status WHERE empStat_id='".$staff_row['empStat_id']."'");
                   if ($stat_cert_q && $stat_cert_r = $stat_cert_q->fetch()) {
                       $cert_emp_stat = strtolower($stat_cert_r['emp_stat_name']);
                   }
                   ?>
                   <!-- LEAVE CREDITS -->
                   <div class="col-lg-12" style="margin-top: 12px;">
                     <h4>Leave Credits</h4>
                     <div style="margin-top: 10px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; font-size: 1.15em;">
                         <div>
                             <strong>Vacation Leave (VL):</strong> <?php echo number_format($staff_row['vl_balance'], 3); ?> &nbsp;&nbsp;|&nbsp;&nbsp;
                             <strong>Sick Leave (SL):</strong> <?php echo number_format($staff_row['sl_balance'], 3); ?>
                         </div>
                         <div style="margin-top: 15px;">
                             <a href="#" data-toggle="modal" data-target="#adjustLeave<?php echo $_GET['personnel_id']; ?>" title="Adjust Leave Balances" class="btn btn-warning btn-sm" style="color: white;"><i class="fa fa-pencil"></i> Adjust Credits</a>
                             <form action="save_earn_monthly_leave.php" method="POST" style="display:inline-block; margin-left: 8px;">
                                 <input type="hidden" name="personnel_id" value="<?php echo $_GET['personnel_id']; ?>">
                                 <input type="hidden" name="dept" value="<?php echo $_GET['dept']; ?>">
                                 <input type="hidden" name="month_year" value="<?php echo date('F Y'); ?>">
                                 <button type="submit" name="earn_monthly_leave" class="btn btn-success btn-sm" onclick="return confirm('Add 1.25 VL and 1.25 SL for <?php echo date('F Y'); ?>?');"><i class="fa fa-plus"></i> Earn Auto 1.25</button>
                             </form>
                         </div>
                     </div>
                   </div>
                   
                   <div class="col-lg-12" style="margin-top: 30px;">
    <?php $lc_order = isset($_GET['lc_order']) && strtolower(trim($_GET['lc_order'])) === 'asc' ? 'asc' : 'desc'; ?>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="m-0" style="letter-spacing: 2px;"><strong>LEAVE CARD</strong></h5>
        <div>
            <a href="print_leave_card.php?personnel_id=<?php echo $_GET['personnel_id']; ?>&order=asc" target="_blank" class="btn btn-secondary btn-sm" title="Print Leave Card in Ascending order (Start to Last)"><i class="fa fa-print"></i> Print Card</a>
            <?php if ($lc_order === 'desc') { ?>
                <a href="list_personnel_individual_details_LC.php?personnel_id=<?php echo $_GET['personnel_id']; ?>&dept=<?php echo urlencode($_GET['dept'] ?? ''); ?>&lc_order=asc" class="btn btn-outline-secondary btn-sm" title="View Ascending (From date start to last)"><i class="fa fa-sort-amount-asc"></i> Ascending</a>
            <?php } else { ?>
                <a href="list_personnel_individual_details_LC.php?personnel_id=<?php echo $_GET['personnel_id']; ?>&dept=<?php echo urlencode($_GET['dept'] ?? ''); ?>&lc_order=desc" class="btn btn-outline-secondary btn-sm" title="View Descending (Newest first)"><i class="fa fa-sort-amount-desc"></i> Descending</a>
            <?php } ?>
            <a href="#" data-toggle="modal" data-target="#absenceModal" class="btn btn-warning btn-sm"><i class="fa fa-minus-circle"></i> Manual Absence/Undertime</a>
            <a href="#" data-toggle="modal" data-target="#adjustLeave<?php echo $_GET['personnel_id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Adjust Balance</a>
            <a href="list_personnel_individual_details_LC.php?personnel_id=<?php echo $_GET['personnel_id']; ?>&dept=<?php echo urlencode($_GET['dept'] ?? ''); ?>&sync_leaves=1" class="btn btn-outline-info btn-sm" title="Check and sync approved leave applications into Leave Card"><i class="fa fa-refresh"></i> Sync Leaves</a>
        </div>
    </div>
    
    <div style="overflow-x: auto; background: #fff; padding: 10px; border: 1px solid #ccc;">
        <style>
            .lc-table {
                width: 100%;
                border-collapse: collapse;
                font-family: Arial, sans-serif;
                font-size: 11px;
                color: #000;
                text-align: center;
                white-space: nowrap;
            }
            .lc-table th, .lc-table td {
                border: 1px solid #000;
                padding: 4px 6px;
            }
            .lc-table th {
                background-color: #f8f9fa;
                font-weight: bold;
                text-transform: uppercase;
                vertical-align: middle;
            }
            .lc-sub-header {
                display: flex;
                flex-direction: column;
                height: 100%;
            }
            .lc-sub-top {
                border-bottom: 1px solid #000;
                padding-bottom: 3px;
                margin-bottom: 3px;
            }
        </style>
        <table class="lc-table">
            <thead>
                <tr>
                    <th colspan="2" rowspan="2">PERIOD</th>
                    <th rowspan="2">PARTICULARS</th>
                    <th colspan="4">VACATION LEAVE</th>
                    <th colspan="4">SICK LEAVE</th>
                    <th rowspan="2">REMARKS</th><th rowspan="2" class="no-print">ACT</th>
                </tr>
                <tr>
                    <th>EARNED</th>
                    <th style="padding:0;"><div class="lc-sub-header"><div class="lc-sub-top">Absence<br>Undertime</div><div>w/ pay</div></div></th>
                    <th>BALANCE</th>
                    <th style="padding:0;"><div class="lc-sub-header"><div class="lc-sub-top">Absence<br>Undertime</div><div>w/out pay</div></div></th>
                    
                    <th>EARNED</th>
                    <th style="padding:0;"><div class="lc-sub-header"><div class="lc-sub-top">Absence<br>Undertime</div><div>w/ pay</div></div></th>
                    <th>BALANCE</th>
                    <th style="padding:0;"><div class="lc-sub-header"><div class="lc-sub-top">Absence<br>Undertime</div><div>w/out pay</div></div></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (function_exists('syncPersonnelUnloggedLeaves')) {
                    syncPersonnelUnloggedLeaves($conn, $_GET['personnel_id']);
                }
                $log_query = $conn->prepare("SELECT * FROM leave_credit_logs WHERE personnel_id=? ORDER BY log_id ASC");
                $log_query->execute([$_GET['personnel_id']]);
                $logs = $log_query->fetchAll(PDO::FETCH_ASSOC);
                $logs = array_values(array_filter($logs, function ($log) {
                    if (isZeroValueOfficialDutyLeaveLog($log)) {
                        return false;
                    }
                    // Exclude legacy pre-2026 RFID leave applications (keep records encoded in the new system)
                    if ($log['log_type'] === 'Leave Application' && stripos($log['remarks'], 'Applied for') !== false && stripos($log['remarks'], '[HR Encoded]') === false) {
                        $p = trim(explode(' - ', $log['period'])[0]);
                        $ts = strtotime(str_replace('-', '/', $p));
                        $y = $ts ? (int)date('Y', $ts) : 0;
                        if ($y > 0 && $y < 2026) {
                            return false;
                        }
                    }
                    return true;
                }));

usort($logs, function($a, $b) {
    // Get start date of period
    $a_p = trim(explode(' - ', $a['period'])[0]);
    $b_p = trim(explode(' - ', $b['period'])[0]);
    $tsA = strtotime(str_replace('-', '/', $a_p));
    $tsB = strtotime(str_replace('-', '/', $b_p));
    
    // If invalid date, fallback to 0
    if(!$tsA) $tsA = 0;
    if(!$tsB) $tsB = 0;
    
    // Primary sort by date, secondary sort by log_id (so manual adjustments on same day go after accruals)
    if ($tsA == $tsB) {
        return $a['log_id'] <=> $b['log_id'];
    }
    return $tsA <=> $tsB;
});

                $vl_run = 0.000;
                $sl_run = 0.000;
                foreach ($logs as $key => $log) {
                    $prev_vl = $vl_run;
                    $prev_sl = $sl_run;

                    $vl_run += (float)$log['vl_amount'];
                    if ($vl_run < 0) $vl_run = 0;
                    $sl_run += (float)$log['sl_amount'];
                    if ($sl_run < 0) $sl_run = 0;
                    
                    $logs[$key]['prev_vl'] = $prev_vl;
                    $logs[$key]['prev_sl'] = $prev_sl;
                    $logs[$key]['vl_run_calc'] = $vl_run;
                    $logs[$key]['sl_run_calc'] = $sl_run;
                }

                $has_terminal_leave = false;
                foreach ($logs as $l) {
                    if (stripos($l['particulars'] ?? '', 'terminal') !== false ||
                        stripos($l['log_type'] ?? '', 'terminal') !== false ||
                        stripos($l['remarks'] ?? '', 'terminal') !== false) {
                        $has_terminal_leave = true;
                        break;
                    }
                }
                if (!$has_terminal_leave) {
                    $chk_tl_log = $conn->prepare("SELECT log_id FROM leave_credit_logs WHERE personnel_id = ? AND (particulars LIKE '%terminal%' OR log_type LIKE '%terminal%' OR remarks LIKE '%terminal%') LIMIT 1");
                    $chk_tl_log->execute([$_GET['personnel_id']]);
                    if ($chk_tl_log->fetch()) {
                        $has_terminal_leave = true;
                    }
                }
                if (!$has_terminal_leave) {
                    $chk_tl_app = $conn->prepare("SELECT lap_id FROM leave_applicants WHERE applicant_id = ? AND (leave_type LIKE '%terminal%' OR leave_type_desc LIKE '%terminal%') LIMIT 1");
                    $chk_tl_app->execute([$_GET['personnel_id']]);
                    if ($chk_tl_app->fetch()) {
                        $has_terminal_leave = true;
                    }
                }

                // Order: Reverse to show descending only if requested, otherwise show ascending
                if ($lc_order === 'desc') {
                    $logs = array_reverse($logs);
                }

                if(count($logs) == 0) {
                    echo "<tr><td colspan='11' style='padding: 20px;'>No leave records found.</td></tr>";
                }

                foreach($logs as $log) {
                    $log_type_clean = strtolower(trim((string)$log['log_type']));
                    $particulars_clean = strtolower(trim((string)$log['particulars']));
                    $is_monthly_earn = ($log_type_clean === 'monthly earn' || $particulars_clean === 'accrual' || stripos((string)$log['remarks'], 'auto-accrual') !== false);

                    $period = !empty($log['period']) ? $log['period'] : date('m-d-y', strtotime($log['date_logged']));
                    $particulars = !empty($log['particulars']) ? $log['particulars'] : $log['log_type'];

                    $is_special_leave = isNonDeductibleSpecialLeave($particulars, $log['remarks']);

                    $vl_earned = ($is_monthly_earn && $log['vl_amount'] > 0) ? $log['vl_amount'] : '';
                    $vl_w_pay = (!empty($log['vl_w_pay']) && (float)$log['vl_w_pay'] > 0) ? (float)$log['vl_w_pay'] : (($log['vl_amount'] < 0) ? abs($log['vl_amount']) : '');
                    $vl_wout_pay = (!empty($log["vl_wout_pay"]) && (float)$log["vl_wout_pay"] > 0) ? $log["vl_wout_pay"] : "";

                    $sl_earned = ($is_monthly_earn && $log['sl_amount'] > 0) ? $log['sl_amount'] : '';
                    $sl_w_pay = (!empty($log['sl_w_pay']) && (float)$log['sl_w_pay'] > 0) ? (float)$log['sl_w_pay'] : (($log['sl_amount'] < 0) ? abs($log['sl_amount']) : '');
                    $sl_wout_pay = (!empty($log["sl_wout_pay"]) && (float)$log["sl_wout_pay"] > 0) ? $log["sl_wout_pay"] : "";

                    // Approved leave applications are paid leaves: tanan sa absence with pay, zero sa without pay
                    if ($log_type_clean === 'leave application' || stripos($particulars_clean, 'leave') !== false) {
                        if (!empty($vl_wout_pay) && (float)$vl_wout_pay > 0) {
                            $vl_w_pay = ((float)$vl_w_pay > 0 ? (float)$vl_w_pay : 0) + (float)$vl_wout_pay;
                            $vl_wout_pay = '';
                        }
                        if (!empty($sl_wout_pay) && (float)$sl_wout_pay > 0) {
                            $sl_w_pay = ((float)$sl_w_pay > 0 ? (float)$sl_w_pay : 0) + (float)$sl_wout_pay;
                            $sl_wout_pay = '';
                        }
                    }

                    // Non-deductible special leaves (Wellness, Maternity, Paternity, Solo Parent, Rehab, Emergency)
                    // reflect their days with pay without deducting from running balances
                    if ($is_special_leave && (float)$log['vl_amount'] == 0 && (float)$log['sl_amount'] == 0) {
                        $days = getSpecialLeaveDays($log, $conn);
                        if (!empty($log['sl_w_pay']) && (float)$log['sl_w_pay'] > 0) {
                            $sl_w_pay = (float)$log['sl_w_pay'];
                        } else {
                            $vl_w_pay = $days;
                        }
                    }

                    $vl_run = $log['vl_run_calc'];
                    $sl_run = $log['sl_run_calc'];

                    $vl_manual_balance = (!$is_monthly_earn && $vl_w_pay === '' && !$is_special_leave) ? (float)$vl_run : '';
                    $sl_manual_balance = (!$is_monthly_earn && $sl_w_pay === '' && !$is_special_leave) ? (float)$sl_run : '';
                ?>
                <tr>
                    <?php 
    $period_from = $period;
    $period_to = '';
    if(strpos($period, ' - ') !== false) {
        $p_parts = explode(' - ', $period);
        $period_from = trim($p_parts[0]);
        $period_to = trim($p_parts[1]);
    }
    
    // If period_to is empty, automatically compute appropriate end date
    if ($period_to === '' && $period_from !== '') {
        if(strtolower($log['log_type']) == 'leave application' || stripos($log['particulars'], 'leave') !== false) {
            $period_to = $period_from;
        } else {
            $clean_from = str_replace('-', '/', $period_from);
            $ts_from = strtotime($clean_from);
            if ($ts_from) {
                $sep = (strpos($period_from, '/') !== false) ? '/' : '-';
                $p_parts = explode($sep, $period_from);
                if (isset($p_parts[0]) && strlen(trim($p_parts[0])) == 4) {
                    $period_to = date("Y{$sep}m{$sep}t", $ts_from);
                } else {
                    $m_len = isset($p_parts[0]) ? strlen(trim($p_parts[0])) : 2;
                    $y_len = isset($p_parts[2]) ? strlen(trim($p_parts[2])) : (isset($p_parts[1]) ? strlen(trim($p_parts[1])) : 2);
                    $m_fmt = ($m_len == 1) ? 'n' : 'm';
                    $y_fmt = ($y_len == 4) ? 'Y' : 'y';
                    $period_to = date("{$m_fmt}{$sep}t{$sep}{$y_fmt}", $ts_from);
                }
            }
        }
    }
    
    $remarks_display = htmlspecialchars($log['remarks']);
    // Remove "Manual adjustment by HR" from remarks case-insensitively
    $remarks_display = trim(str_ireplace('Manual adjustment by HR', '', $remarks_display));
    // Remove LWOP text from remarks for leave applications
    $remarks_display = trim(preg_replace('/\s*\(Plus\s+[\d\.]+\s+LWOP\)/i', '', $remarks_display));
    
    // Automatically display Certified Correct for Terminal Leave if empty or generic
    if (stripos($particulars, 'terminal') !== false || stripos($log['log_type'], 'terminal') !== false || stripos($remarks_display, 'terminal') !== false) {
        if ($remarks_display === '' || stripos($remarks_display, 'applied for') !== false || stripos($remarks_display, 'encoded') !== false || stripos($remarks_display, 'certified correct') !== false) {
            $remarks_display = 'Certified Correct';
        }
    }
    
    // Remove "Balance Forwarded" from particulars if it's a manual entry
    $particulars_display = htmlspecialchars($particulars);
    if (strtolower($log['log_type']) == 'manual adjustment') {
        $particulars_display = trim(str_ireplace('Balance Forwarded', '', $particulars_display));
    }
?>
            <td><?php echo htmlspecialchars($period_from); ?></td>
            <td><?php echo htmlspecialchars($period_to); ?></td>
            <td><?php echo $particulars_display; ?></td>
            
            <td><?php echo $vl_earned ? number_format($vl_earned, 3) : ''; ?></td>
            <td><?php echo $vl_w_pay ? number_format($vl_w_pay, 3) : ''; ?></td>
            <td style="font-weight: bold; background: #f9f9f9;"><?php echo number_format($vl_run, 3); ?></td>
            <td><?php echo $vl_wout_pay ? number_format($vl_wout_pay, 3) : ''; ?></td>
            
            <td><?php echo $sl_earned ? number_format($sl_earned, 3) : ''; ?></td>
            <td><?php echo $sl_w_pay ? number_format($sl_w_pay, 3) : ''; ?></td>
            <td style="font-weight: bold; background: #f9f9f9;"><?php echo number_format($sl_run, 3); ?></td>
            <td><?php echo $sl_wout_pay ? number_format($sl_wout_pay, 3) : ''; ?></td>
            
            <td style="text-align: left; max-width: 300px; white-space: normal;"><small><?php echo $remarks_display; ?></small></td>
            <td class="no-print">
                <button class="btn btn-sm btn-info edit-log-btn" 
                    data-id="<?php echo $log['log_id']; ?>"
                    data-period_from="<?php echo htmlspecialchars($period_from); ?>"
                    data-period_to="<?php echo htmlspecialchars($period_to); ?>"
                    data-particulars="<?php echo htmlspecialchars($particulars); ?>"
                    data-vl_earned="<?php echo $vl_earned ? number_format($vl_earned, 3) : ''; ?>"
                    data-vl_balance="<?php echo ($vl_manual_balance !== '') ? number_format($vl_manual_balance, 3) : ''; ?>"
                    data-prev_vl="<?php echo number_format($log['prev_vl'], 3); ?>"
                    data-vl_w_pay="<?php echo $vl_w_pay ? number_format($vl_w_pay, 3) : ''; ?>"
                    data-vl_wout_pay="<?php echo $vl_wout_pay ? number_format($vl_wout_pay, 3) : ''; ?>"
                    data-sl_earned="<?php echo $sl_earned ? number_format($sl_earned, 3) : ''; ?>"
                    data-sl_balance="<?php echo ($sl_manual_balance !== '') ? number_format($sl_manual_balance, 3) : ''; ?>"
                    data-prev_sl="<?php echo number_format($log['prev_sl'], 3); ?>"
                    data-sl_w_pay="<?php echo $sl_w_pay ? number_format($sl_w_pay, 3) : ''; ?>"
                    data-sl_wout_pay="<?php echo $sl_wout_pay ? number_format($sl_wout_pay, 3) : ''; ?>"
                    data-remarks="<?php echo htmlspecialchars($log['remarks']); ?>"
                    data-toggle="modal" data-target="#editLogModal"
                    title="Edit Row" style="margin-bottom: 2px;"><i class="fa fa-edit"></i></button>
                <button class="btn btn-sm btn-danger delete-log-btn" data-id="<?php echo $log['log_id']; ?>" title="Delete Row"><i class="fa fa-trash"></i></button>
            </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if ($has_terminal_leave) { ?>
    <div style="display: flex; justify-content: flex-end; margin-top: 15px; margin-bottom: 25px; padding-right: 15px;">
        <div style="width: 290px; border: 3.5px solid #0f2b48; border-radius: 10px; padding: 10px 14px 12px 14px; color: #0f2b48; font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="text-align: center; font-size: 13px; font-weight: 900; letter-spacing: 0.5px; line-height: 1.25;">CERTIFIED TRUE COPY OF</div>
            <div style="text-align: center; font-size: 13px; font-weight: 900; letter-spacing: 0.5px; line-height: 1.25;">AN ORIGINAL DOCUMENT</div>
            <div style="display: flex; align-items: flex-end; margin-top: 12px; font-size: 12px; font-weight: 800; line-height: 1;">
                <span style="white-space: nowrap; padding-right: 4px; letter-spacing: 0.5px;">SIGNED</span>
                <span style="flex-grow: 1; border-bottom: 2px dotted #0f2b48; height: 12px; margin-bottom: 1px;"></span>
            </div>
            <div style="display: flex; align-items: flex-end; margin-top: 8px; font-size: 12px; font-weight: 800; line-height: 1;">
                <span style="white-space: nowrap; padding-right: 4px; letter-spacing: 0.5px;">DATE</span>
                <span style="flex-grow: 1; border-bottom: 2px dotted #0f2b48; height: 12px; margin-bottom: 1px;"></span>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
                          
                          <?php $personnel_id = $_GET['personnel_id']; include('adjust_leave_modal.php'); ?>

<!-- Edit Log Modal -->
<div class="modal fade text-left" id="editLogModal" tabindex="-1" role="dialog" aria-labelledby="editLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="save_edit_leave_log.php?personnel_id=<?php echo $_GET['personnel_id']; ?>&dept=<?php echo isset($_GET['dept']) ? $_GET['dept'] : ''; ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLogModalLabel">Edit Leave Card Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="log_id" id="edit_log_id">
                    <input type="hidden" name="prev_vl" id="edit_prev_vl">
                    <input type="hidden" name="prev_sl" id="edit_prev_sl">
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Period From</label>
                            <input type="text" name="period_from" id="edit_period_from" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Period To</label>
                            <input type="text" name="period_to" id="edit_period_to" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Particulars</label>
                        <input type="text" name="particulars" id="edit_particulars" class="form-control">
                    </div>
                    
                    <h6 class="mt-3">Vacation Leave</h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Earned</label>
                            <input type="number" step="0.001" min="0" name="vl_earned" id="edit_vl_earned" class="form-control">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Balance</label>
                            <input type="number" step="0.001" name="vl_balance" id="edit_vl_balance" class="form-control">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Absence/Undertime w/ Pay</label>
                            <input type="number" step="0.001" min="0" name="vl_w_pay" id="edit_vl_w_pay" class="form-control">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Absence/Undertime w/out Pay</label>
                            <input type="number" step="0.001" min="0" name="vl_wout_pay" id="edit_vl_wout_pay" class="form-control">
                        </div>
                    </div>
                    
                    <h6 class="mt-3">Sick Leave</h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Earned</label>
                            <input type="number" step="0.001" min="0" name="sl_earned" id="edit_sl_earned" class="form-control">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Balance</label>
                            <input type="number" step="0.001" name="sl_balance" id="edit_sl_balance" class="form-control">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Absence/Undertime w/ Pay</label>
                            <input type="number" step="0.001" min="0" name="sl_w_pay" id="edit_sl_w_pay" class="form-control">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Absence/Undertime w/out Pay</label>
                            <input type="number" step="0.001" min="0" name="sl_wout_pay" id="edit_sl_wout_pay" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">Remarks</label>
                            <a href="javascript:void(0);" onclick="$('#edit_remarks').val('Certified Correct');" class="small text-primary font-weight-bold" style="text-decoration: none;"><i class="fa fa-certificate"></i> Set 'Certified Correct'</a>
                        </div>
                        <textarea name="remarks" id="edit_remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_log" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
if (typeof autoCalculatePeriodTo !== 'function') {
    function autoCalculatePeriodTo(dateStr) {
        if (!dateStr) return '';
        dateStr = dateStr.trim();
        var sep = dateStr.indexOf('/') !== -1 ? '/' : (dateStr.indexOf('-') !== -1 ? '-' : '');
        if (!sep) return '';
        var parts = dateStr.split(sep);
        if (parts.length < 3) return '';

        var m, d, y, mStr, yStr;
        if (parts[0].length === 4) {
            y = parseInt(parts[0], 10);
            m = parseInt(parts[1], 10);
            d = parseInt(parts[2], 10);
            yStr = parts[0];
            mStr = parts[1];
            if (isNaN(y) || isNaN(m)) return '';
            var lastDay = new Date(y, m, 0).getDate();
            var ldStr = (lastDay < 10 ? '0' : '') + lastDay;
            return yStr + sep + mStr + sep + ldStr;
        } else {
            m = parseInt(parts[0], 10);
            d = parseInt(parts[1], 10);
            y = parseInt(parts[2], 10);
            mStr = parts[0];
            yStr = parts[2];
            if (isNaN(y) || isNaN(m)) return '';
            var fullYear = (y < 100) ? (2000 + y) : y;
            var lastDay = new Date(fullYear, m, 0).getDate();
            var ldStr = (mStr.length === 2 && lastDay < 10) ? ('0' + lastDay) : lastDay;
            return mStr + sep + ldStr + sep + yStr;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    let editPeriodFrom = document.getElementById('edit_period_from');
    if (editPeriodFrom) {
        ['input', 'change', 'blur', 'keyup'].forEach(evt => {
            editPeriodFrom.addEventListener(evt, function() {
                let calc = autoCalculatePeriodTo(this.value);
                if (calc) {
                    document.getElementById('edit_period_to').value = calc;
                }
            });
        });
    }

    let editBtns = document.querySelectorAll('.edit-log-btn');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_log_id').value = this.dataset.id;
            document.getElementById('edit_period_from').value = this.dataset.period_from;
            let pTo = this.dataset.period_to || '';
            if (!pTo && this.dataset.period_from) {
                pTo = autoCalculatePeriodTo(this.dataset.period_from) || '';
            }
            document.getElementById('edit_period_to').value = pTo;
            document.getElementById('edit_particulars').value = this.dataset.particulars;
            document.getElementById('edit_vl_earned').value = this.dataset.vl_earned;
            document.getElementById('edit_vl_balance').value = this.dataset.vl_balance;
            document.getElementById('edit_prev_vl').value = this.dataset.prev_vl || '0';
            document.getElementById('edit_vl_w_pay').value = this.dataset.vl_w_pay;
            document.getElementById('edit_vl_wout_pay').value = this.dataset.vl_wout_pay;
            document.getElementById('edit_sl_earned').value = this.dataset.sl_earned;
            document.getElementById('edit_sl_balance').value = this.dataset.sl_balance;
            document.getElementById('edit_prev_sl').value = this.dataset.prev_sl || '0';
            document.getElementById('edit_sl_w_pay').value = this.dataset.sl_w_pay;
            document.getElementById('edit_sl_wout_pay').value = this.dataset.sl_wout_pay;
            document.getElementById('edit_remarks').value = this.dataset.remarks;
        });
    });

    let deleteBtns = document.querySelectorAll('.delete-log-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Are you sure you want to delete this leave card entry? This will also recalculate the balances.')) {
                let log_id = this.dataset.id;
                let personnel_id = '<?php echo $_GET['personnel_id']; ?>';
                let dept = '<?php echo isset($_GET['dept']) ? $_GET['dept'] : ''; ?>';
                
                let formData = new FormData();
                formData.append('log_id', log_id);
                formData.append('personnel_id', personnel_id);
                
                fetch('delete_leave_log.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert('Entry deleted successfully!');
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting entry.');
                });
            }
        });
    });
});
</script>
                          <?php include('add_absence_modal.php'); ?>
                          
              </div>
            </div>
              <!-- kinder End-->
             
            </div>
            
          </div>
        </div>
     
        
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>

    <!-- Certification Print Preview Modal at root body level -->
    <?php if ($has_leave_credits) { include('leave_certification_modal.php'); } ?>

    <?php include('scripts_files.php'); ?>
    <script>
    $(document).on('click', '#btnOpenCertModal', function(e) {
        e.preventDefault();
        $('#certModal').modal('show');
    });
    </script>
  
  </body>
</html>
