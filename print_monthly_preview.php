<!DOCTYPE html>
<html>

<?php

include('session.php');  
//error_reporting(0);

  $get_RFTag_id=$_GET['RFTag_id'];
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
  $grandTotalTRHr=0;
  $grandTotalTRMin=0;
  
  $grandTotalamLateMin=0;
  $grandTotalpmLateMin=0;
  
  $grandTotalamUTimeMin=0;
  $grandTotalpmUTimeMin=0;
 
                 
                if($selectedMM=="01")
                {
                    
                    $mmWords="January";
                    $MMmaxDay=32;
                }
                
                if($selectedMM=="02")
                {
                    $mmWords="February";
                    
                    $leap = date('L', mktime(0, 0, 0, 1, 1, $selectedYYYY));
            
                    if($leap==0)
                    {
                    $MMmaxDay=29;    
                    }else{
                    $MMmaxDay=30;        
                    }
                    
                }
                
                
                if($selectedMM=="03")
                {
                    $mmWords="March";
                    $MMmaxDay=32;    
                }
                
                
                if($selectedMM=="04")
                {
                    $mmWords="April";
                    $MMmaxDay=31;    
                }
                
                
                if($selectedMM=="05")
                {
                    $mmWords="May";
                    $MMmaxDay=32;

                }
                
                
                if($selectedMM=="06")
                {
                    $mmWords="June";
                    $MMmaxDay=31;
                }
                
                
                
                if($selectedMM=="07")
                {
                    $mmWords="July";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="08")
                {
                    $mmWords="August";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="09")
                {
                    $mmWords="September";
                    $MMmaxDay=31;
                }
                
                
                if($selectedMM=="10")
                {
                    $mmWords="October";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="11")
                {
                    $mmWords="November";
                    $MMmaxDay=31;
                }
                
                
                if($selectedMM=="12")
                {
                    $mmWords="December";
                    $MMmaxDay=32;
                }
  
            
        
include('header_print.php');

?>

<body>
 

<?php include('header_print_letterHead.php'); ?>

<hr />

<?php
$studData_query = $conn->query("select * FROM personnels WHERE RFTag_id='$get_RFTag_id'");
$studData_row=$studData_query->fetch();



?>

 


<table style="width: 100%;">

  <tr style="font-size: large;" style="border: none;">
    
    
    <td style="width: 40%; border: none;" colspan="2">
    <small>Employment Status</small><br />
    <strong><?php
    $emp_stat_query = $conn->query("select * from emp_status WHERE empStat_id='$studData_row[empStat_id]'");
    $es_row=$emp_stat_query->fetch();
    echo strtoupper($es_row['emp_stat_name']);?></strong>
    
    </td>
    
    <td style="width: 40%; border: none;" colspan="2">
    <small>Department / Office</small><br />
    <strong style="font-size: small;"><?php
    $emp_stat_query = $conn->query("select * from dept_offices WHERE do_id='$studData_row[do_id]'");
    $es_row=$emp_stat_query->fetch();
    echo strtoupper($es_row['dept_office_name']); ?></strong> 
    
    </td>
     
  </tr>
  
  <tr>
  <td style="width: 70%; border: none;" colspan="2">
    <small>Employee</small><br />
    <strong><?php
    $mname=$studData_row['mname'];
    
    $suffix=$studData_row['suffix'];
    if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
    if($mname=='')
    {
            $finalMName=$suffix;
            
            echo strtoupper($studData_row['lname'].", ".$studData_row['fname']." ".$finalMName);
            
    }else{
            
            
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';

            echo strtoupper($studData_row['lname'].", ".$studData_row['fname']." ".$finalMName);

    }
    ?></strong> 
    
   
  </td>
  
  <td style="width: 30%; border: none;">
  <small>Month Covered</small><br />
  <strong><?php echo strtoupper($mmWords); ?></strong>
  
  
  </td>
  </tr>
</table>
 <hr />

<table id="myTable">

  <tr style="font-weight: light; font-size: 14px">
    
    <td style="width:8%;"><center><strong>DATE</strong></center></td>
    
    <td style="width:15%;"><center><strong>AM IN</strong></center></td>
    <td style="width:15%;"><center><strong>AM OUT</strong></center></td>
    <td style="width:15%;"><center><strong>PM IN</strong></center></td>
    <td style="width:15%;"><center><strong>PM OUT</strong></center></td>
    <td style="width:10%;"><center><strong>TARDINESS</strong></center></td>
    <td style="width:10%;"><center><strong>UNDERTIME</strong></center></td>
    <td style="width:12%;"><center><strong>OVERTIME</strong></center></td>
  </tr>
 
<?php
 
    $RFTag_id = !empty($studData_row['RFTag_id']) ? $studData_row['RFTag_id'] : $studData_row['biometric_id'];
 
    $amPresentCtr=0;
    $pmPresentCtr=0;
    
    $amLateCtr=0;
    $pmLateCtr=0;
    
    $amUTimeCtr=0;
    $pmUTimeCtr=0;
    
    $amAbsentCtr=0;
    $pmAbsentCtr=0;
    
    $leaveCtr=0;
    
    $grandTotalamLateMin = 0;
    $grandTotalpmLateMin = 0;
    $grandTotalamUTimeMin = 0;
    $grandTotalpmUTimeMin = 0;
    $grandTotalLateMin = 0;
    $grandTotalUTimeMin = 0;
    $grandTotalOvertimeMin = 0;
    
    // --- BULK FETCH OPTIMIZATION ---
    // Fetch all logs for the month
    $startDate = sprintf("%04d-%02d-01", $selectedYYYY, $selectedMM);
    $endDate = sprintf("%04d-%02d-%02d", $selectedYYYY, $selectedMM, $MMmaxDay - 1);
    
    $allBioLogs = [];
    $bio_stmt = $conn->prepare("SELECT log_date, time_in, time_out FROM bio_dtr WHERE personnel_id = ? AND log_date BETWEEN ? AND ? ORDER BY time_in ASC");
    $bio_stmt->execute([$studData_row['personnel_id'], $startDate, $endDate]);
    while ($r = $bio_stmt->fetch(PDO::FETCH_ASSOC)) {
        $allBioLogs[$r['log_date']][] = $r;
    }
    
    // Fetch schedule
    $allSchedules = [];
    $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = ? AND shift_id = ?");
    $sched_stmt->execute([$studData_row['do_id'], $studData_row['shift_id']]);
    while ($r = $sched_stmt->fetch(PDO::FETCH_ASSOC)) {
        $allSchedules[$r['day']] = $r;
    }
    
    // Fetch activity calendar
    $allHolidays = [];
    $hol_stmt = $conn->prepare("SELECT completeDate, event_title, status FROM activity_calendar WHERE completeDate BETWEEN ? AND ?");
    $hol_stmt->execute([$startDate, $endDate]);
    while ($r = $hol_stmt->fetch(PDO::FETCH_ASSOC)) {
        $allHolidays[$r['completeDate']][] = $r;
    }
    
    // Fetch approved leaves
        // Fetch approved overtime requests
    $allOvertime = [];
    $startDateDB = $startDate;
    $endDateDB = $endDate;
    $ot_stmt = $conn->prepare("SELECT ot_date FROM personnel_overtime_requests WHERE personnel_id = ? AND ot_date BETWEEN ? AND ?");
    $ot_stmt->execute([$studData_row['personnel_id'], $startDateDB, $endDateDB]);
    while ($r = $ot_stmt->fetch(PDO::FETCH_ASSOC)) {
        $formattedDate = date('m/d/Y', strtotime($r['ot_date']));
        $allOvertime[$formattedDate] = true;
    }
    
    $allLeaves = [];
    $leave_stmt = $conn->prepare("SELECT logDate, remarks FROM personnel_logs WHERE RFTag_id = ? AND STR_TO_DATE(logDate, '%m/%d/%Y') BETWEEN ? AND ? AND (remarks='Vacation Leave' OR remarks='Sick Leave' OR travel_leave_code != '')");
    $leave_stmt->execute([$studData_row['RFTag_id'], $startDate, $endDate]);
    while ($r = $leave_stmt->fetch(PDO::FETCH_ASSOC)) {
        $allLeaves[$r['logDate']] = $r['remarks'];
    }
    // Fetch personnel_logs
    $allPersonnelLogs = [];
    $p_stmt = $conn->prepare("
        SELECT logFlow, logTime, late_status, logDate 
        FROM personnel_logs 
        WHERE (RFTag_id = ? OR RFTag_id = ?) 
          AND (
            (logDate LIKE '%-%' AND logDate BETWEEN ? AND ?) OR 
            (logDate NOT LIKE '%-%' AND STR_TO_DATE(logDate, '%m/%d/%Y') BETWEEN ? AND ?)
          )
          AND (client_ip = '' OR logDate LIKE '%-%')
    ");
    $p_stmt->execute([
        $studData_row['RFTag_id'], 
        $studData_row['biometric_id'], 
        $startDate, 
        $endDate, 
        $startDate, 
        $endDate
    ]);
    while ($r = $p_stmt->fetch(PDO::FETCH_ASSOC)) {
        // Normalize logDate to YYYY-MM-DD for array key
        $r_date = strpos($r['logDate'], '-') !== false ? $r['logDate'] : date('Y-m-d', strtotime($r['logDate']));
        $allPersonnelLogs[$r_date][$r['logFlow']] = $r;
    }
    // -------------------------------
    
    for($d=1; $d<$MMmaxDay; $d++){
        
        $dailyLate=0;
        $dailyUTime=0;
        $dailyOvertime=0;
        $dayPad = str_pad($d, 2, '0', STR_PAD_LEFT);
        $logDateCtr = $selectedYYYY . '-' . $selectedMM . '-' . $dayPad;
        $displayDateCtr = $selectedMM . '/' . $dayPad . '/' . substr($selectedYYYY, 2, 2);
        
        $is_biometric = !empty($allBioLogs[$logDateCtr]);
        $bio_am_in = false;
        $bio_am_out = false;
        $bio_pm_in = false;
        $bio_pm_out = false;
        
        if ($is_biometric) {
            $bio_logs = $allBioLogs[$logDateCtr] ?? [];
            
            foreach($bio_logs as $log) {
                if (!empty($log['time_in']) && $log['time_in'] != '00:00:00') {
                    if ($log['time_in'] < '12:00:00' && !$bio_am_in) {
                        $bio_am_in = ['logTime' => $log['time_in'], 'late_status' => 'on'];
                    } else if ($log['time_in'] >= '12:00:00' && !$bio_pm_in) {
                        $bio_pm_in = ['logTime' => $log['time_in'], 'late_status' => 'on'];
                    }
                }
                if (!empty($log['time_out']) && $log['time_out'] != '00:00:00') {
                    if ($log['time_out'] < '13:00:00' && !$bio_am_out) {
                        $bio_am_out = ['logTime' => $log['time_out'], 'late_status' => 'on'];
                    } else if ($log['time_out'] >= '13:00:00' && !$bio_pm_out) {
                        $bio_pm_out = ['logTime' => $log['time_out'], 'late_status' => 'on'];
                    }
                }
            }
        }
 
    ?>
    
  <tr>
  
  <?php
  $isWorkingDayHoliday = false;
  $sc_row3 = null;
  if (isset($allHolidays[$logDateCtr])) {
      foreach ($allHolidays[$logDateCtr] as $hol) {
          if ($hol['status'] == 'Add as working day') {
              $isWorkingDayHoliday = true;
              $sc_row3 = $hol;
              break;
          }
      }
  }
  
  if ($isWorkingDayHoliday) {
      
  ?>
  <td rowspan="2">
    <?php
    
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    echo $displayDateCtr." <sup>".$dayName2."</sup>";
    
    ?>
    </td>
  <?php }else{?>
  <td>
    <?php
    
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    echo $displayDateCtr." <sup>".$dayName2."</sup>";
    
    ?>
    </td>
  <?php } ?>
  
     
    
    
    <?php
    $hasLeave = isset($allLeaves[$logDateCtr]);
    if($hasLeave){ 
    $leaveRemarks = $allLeaves[$logDateCtr];
    $leaveCtr=$leaveCtr+1;
    
    ?> 
    <td colspan="7" style="background-color: #b8ffd9;"><center><strong><?php echo $leaveRemarks; ?></strong></center></td>
     
      <?php }else{
    $hasAnyLogs = false;
    if ($is_biometric) {
        $hasAnyLogs = !empty($allPersonnelLogs[$logDateCtr]) || !empty($bio_am_in) || !empty($bio_am_out) || !empty($bio_pm_in) || !empty($bio_pm_out);
    } else {
        $hasAnyLogs = !empty($allPersonnelLogs[$logDateCtr]);
    }
    
    if(!$hasAnyLogs AND ($dayName2=='Sat' OR $dayName2=='Sun')){ ?> 
    
    <td colspan="6" style="background-color: #ececec;"><center><strong><?php if($dayName2=='Sat'){ echo "S A T U R D A Y"; } if($dayName2=='Sun'){ echo "S U N D A Y"; } ?></strong></center></td>
     
      <?php }else{
     
      $isRegularHoliday = false;
      $sc_row = null;
      if (isset($allHolidays[$logDateCtr])) {
          foreach ($allHolidays[$logDateCtr] as $hol) {
              if ($hol['status'] != 'Add as working day') {
                  $isRegularHoliday = true;
                  $sc_row = $hol;
                  break;
              }
          }
      }
      
      if($isRegularHoliday){
      ?>
        
      <td colspan="6" style="background-color: #ffbac5;"><center><strong><?php echo $sc_row['event_title'].'</strong> [ '.$sc_row['status'].' ]'; ?></strong></center></td>
      
      <?php }else{ ?> 
 
    
    
    <!-- AM IN -->
    <td>
    <?php
    $studLogs_AM_IN_row = $allPersonnelLogs[$logDateCtr]['AM IN'] ?? null;
    $has_am_in = !empty($studLogs_AM_IN_row);
    if (!$has_am_in && $is_biometric && !empty($bio_am_in)) {
        $studLogs_AM_IN_row = $bio_am_in;
        $has_am_in = true;
    }
    ?>
    
    <?php
    if($has_am_in){
    
    $str_time_am_in= date("H:i:s", strtotime($studLogs_AM_IN_row['logTime']));
    $str_time_am_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_in);
    sscanf($str_time_am_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_in = ($hours * 3600) + $minutes * 60 + $seconds;
        
    ?>
    
    
    <?php
    if($studLogs_AM_IN_row['late_status']==='on'){
        
        $sq_row = $allSchedules[$dayName] ?? null;
 
        if ($sq_row && !empty($sq_row['am_IN'])) {
            $str_time_sched_am_in_late= date("H:i:s", strtotime($sq_row['am_IN']));
            $str_time_sched_am_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_in_late);
            sscanf($str_time_sched_am_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds_time_am_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
            
            $am_in_late_min=($time_seconds_time_am_in-$time_seconds_time_am_in_late)/60;
            
            if ($am_in_late_min <= 15) {
                $dailyLate=$dailyLate+0;
                $amPresentCtr=$amPresentCtr+1;
                ?>
                <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo date('h:i:s A', strtotime($studLogs_AM_IN_row['logTime'])); ?> ]</p>
                <?php
            } else {
                $grandTotalamLateMin=$grandTotalamLateMin+$am_in_late_min;
                
                $amLateCtr=$amLateCtr+1;
                $amPresentCtr=$amPresentCtr+1;
                
                $dailyLate=$dailyLate+$am_in_late_min;
                ?>
                <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo date('h:i:s A', strtotime($studLogs_AM_IN_row['logTime'])); ?> ]</p>
            <?php } ?>
        <?php } else {
            $dailyLate=$dailyLate+0;
            $amPresentCtr=$amPresentCtr+1;
            ?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo date('h:i:s A', strtotime($studLogs_AM_IN_row['logTime'])); ?> ]</p>
        <?php } ?>
    <?php }else{ 
        
        $dailyLate=$dailyLate+0;
        $amPresentCtr=$amPresentCtr+1;
        
        ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo date('h:i:s A', strtotime($studLogs_AM_IN_row['logTime'])); ?> ]</p>
    <?php } ?>

    <!-- time in seconds AM_IN -->
    <?php
    
    
    
    
    
    ?>
    
    <?php }else{ $time_seconds_time_am_in=0;  
    
       $amAbsentCtr=$amAbsentCtr+1; ?>
       
        <p style="margin: 0px;">--:--</p>   
    <?php } ?>
    
    </td>
    
    
    <!-- AM OUT -->
    <td>
    <?php
    $studLogs_AM_OUT_row = $allPersonnelLogs[$logDateCtr]['AM OUT'] ?? null;
    $has_am_out = !empty($studLogs_AM_OUT_row);
    if (!$has_am_out && $is_biometric && !empty($bio_am_out)) {
        $studLogs_AM_OUT_row = $bio_am_out;
        $has_am_out = true;
    }
    ?>
    
    <?php
    if($has_am_out){ 
        
    $str_time_am_out= date("H:i:s", strtotime($studLogs_AM_OUT_row['logTime']));
    $str_time_am_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_out);
    sscanf($str_time_am_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_out = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
    <?php
    if($studLogs_AM_OUT_row['late_status']==='on'){
        
        $sq_row = $allSchedules[$dayName] ?? null;
        
        if ($sq_row && !empty($sq_row['am_OUT'])) {
            $str_time_sched_am_out_utime= date("H:i:s", strtotime($sq_row['am_OUT']));
            $str_time_sched_am_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_out_utime);
            sscanf($str_time_sched_am_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds_time_am_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
            
            $am_out_utime_min=($time_seconds_time_am_out_utime-$time_seconds_time_am_out)/60;
            
            $grandTotalamUTimeMin=$grandTotalamUTimeMin+$am_out_utime_min;
            
            $amUTimeCtr=$amUTimeCtr+1;
            
            $dailyUTime=$dailyUTime+$am_out_utime_min;
                
        ?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo date('h:i:s A', strtotime($studLogs_AM_OUT_row['logTime'])); ?> ]</p>
        <?php } else {
            $dailyUTime=$dailyUTime+0;
        ?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo date('h:i:s A', strtotime($studLogs_AM_OUT_row['logTime'])); ?> ]</p>
        <?php } ?>
    <?php }else{
        
        $dailyUTime=$dailyUTime+0; ?>
        
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo date('h:i:s A', strtotime($studLogs_AM_OUT_row['logTime'])); ?> ]</p>
    
    <?php } ?>

    <!-- time in seconds AM_OUT -->
    
    
    <?php }else{ $time_seconds_time_am_out=0; 
    
    $chk_has_pm_out = false;
    if ($is_biometric) {
        $chk_has_pm_out = !empty($bio_pm_out);
    } else {
        $chk_has_pm_out = isset($allPersonnelLogs[$logDateCtr]['PM OUT']);
    }
    
    if($chk_has_pm_out AND $has_am_in){ }else{ ?>
    
    <p style="margin: 0px;">--:--</p>   
    
    <?php } } ?>
    
    
    </td>
    
    
    <!-- PM IN -->
    <td>
    <?php
    $studLogs_PM_IN_row = $allPersonnelLogs[$logDateCtr]['PM IN'] ?? null;
    $has_pm_in = !empty($studLogs_PM_IN_row);
    if (!$has_pm_in && $is_biometric && !empty($bio_pm_in)) {
        $studLogs_PM_IN_row = $bio_pm_in;
        $has_pm_in = true;
    }
    ?>
    
    <?php
    if($has_pm_in){ ?>
    
    <!-- time in seconds PM_IN -->
    <?php
    
    $str_time_pm_in= date("H:i:s", strtotime($studLogs_PM_IN_row['logTime']));
    $str_time_pm_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_in);
    sscanf($str_time_pm_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_in = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
    <?php
    if($studLogs_PM_IN_row['late_status']==='on'){
        
        $sq_row = $allSchedules[$dayName] ?? null;
 
        if ($sq_row && !empty($sq_row['pm_IN'])) {
            $str_time_sched_pm_in_late= date("H:i:s", strtotime($sq_row['pm_IN']));
            $str_time_sched_pm_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_in_late);
            sscanf($str_time_sched_pm_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds_time_pm_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
            
            $pm_in_late_min=($time_seconds_time_pm_in-$time_seconds_time_pm_in_late)/60;
            
            if ($pm_in_late_min <= 15) {
                $dailyLate=$dailyLate+0;
                $pmPresentCtr=$pmPresentCtr+1;
                ?>
                <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo date('h:i:s A', strtotime($studLogs_PM_IN_row['logTime'])); ?> ]</p>
                <?php
            } else {
                $grandTotalpmLateMin=$grandTotalpmLateMin+$pm_in_late_min;
                
                $pmLateCtr=$pmLateCtr+1;
                $pmPresentCtr=$pmPresentCtr+1;
                
                $dailyLate=$dailyLate+$pm_in_late_min;
                ?>
                <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo date('h:i:s A', strtotime($studLogs_PM_IN_row['logTime'])); ?> ]</p>
            <?php } ?>
        <?php } else {
            $dailyLate=$dailyLate+0;
            $pmPresentCtr=$pmPresentCtr+1;
            ?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo date('h:i:s A', strtotime($studLogs_PM_IN_row['logTime'])); ?> ]</p>
        <?php } ?>
    <?php }else{ 
        $dailyLate=$dailyLate+0;
        $pmPresentCtr=$pmPresentCtr+1;
        
        ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo date('h:i:s A', strtotime($studLogs_PM_IN_row['logTime'])); ?> ]</p>
    <?php } ?>
 
    <?php }else{ $time_seconds_time_pm_in=0; ?>
    
    <?php
    
    $chk_has_pm_out = false;
    if ($is_biometric) {
        $chk_has_pm_out = !empty($bio_pm_out);
    } else {
        $chk_has_pm_out = isset($allPersonnelLogs[$logDateCtr]['PM OUT']);
    }
    
    if($chk_has_pm_out AND $has_am_in){ $pmPresentCtr=$pmPresentCtr+1; }else{ 
        
        $pmAbsentCtr=$pmAbsentCtr+1;
        
        ?>
    
    <p style="margin: 0px;">--:--</p>   
    
    <?php } } ?>
    
    </td>
    
    
    <!-- PM OUT -->
    <td>
    <?php
    $studLogs_PM_OUT_row = $allPersonnelLogs[$logDateCtr]['PM OUT'] ?? null;
    $has_pm_out = !empty($studLogs_PM_OUT_row);
    if (!$has_pm_out && $is_biometric && !empty($bio_pm_out)) {
        $studLogs_PM_OUT_row = $bio_pm_out;
        $has_pm_out = true;
    }
    ?>
    
    <?php
    if($has_pm_out){ 
    
    $str_time_pm_out= date("H:i:s", strtotime($studLogs_PM_OUT_row['logTime']));
    $str_time_pm_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_out);
    sscanf($str_time_pm_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_out = ($hours * 3600) + $minutes * 60 + $seconds;
    
    // Always fetch schedule to calculate Overtime even if not 'on' for undertime
    $sq_row = $allSchedules[$dayName] ?? null;
    
    if ($sq_row && !empty($sq_row['pm_OUT'])) {
        $str_time_sched_pm_out_utime= date("H:i:s", strtotime($sq_row['pm_OUT']));
        $str_time_sched_pm_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_out_utime);
        sscanf($str_time_sched_pm_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
    } else {
        $time_seconds_time_pm_out_utime = 0;
    }
    
    // Check for Overtime
        $overtime_sec = 0; 
        $checkDate = date('m/d/Y', strtotime($logDateCtr));
    if (isset($allOvertime[$checkDate])) {
        if ($is_biometric && !empty($bio_logs)) {
            foreach($bio_logs as $log) {
                if (!empty($log['time_in']) && !empty($log['time_out'])) {
                    sscanf($log['time_in'], "%d:%d:%d", $h, $m, $s);
                    $segment_in_sec = ($h * 3600) + ($m * 60) + $s;
                    
                    sscanf($log['time_out'], "%d:%d:%d", $h, $m, $s);
                    $segment_out_sec = ($h * 3600) + ($m * 60) + $s;
                    
                    if ($segment_out_sec > $time_seconds_time_pm_out_utime) {
                        $start = max($segment_in_sec, $time_seconds_time_pm_out_utime);
                        $overtime_sec += ($segment_out_sec - $start);
                    }
                }
            }
        } else {
            $overtime_sec = $time_seconds_time_pm_out - $time_seconds_time_pm_out_utime;
        }
    }
    if ($overtime_sec > 0) {
        $dailyOvertime = $overtime_sec / 60;
        $grandTotalOvertimeMin = (isset($grandTotalOvertimeMin) ? $grandTotalOvertimeMin : 0) + $dailyOvertime;
    }
     
    if($studLogs_PM_OUT_row['late_status']=="on"){
        $pm_out_utime_min=($time_seconds_time_pm_out_utime-$time_seconds_time_pm_out)/60;
        $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+$pm_out_utime_min;
        $pmUTimeCtr=$pmUTimeCtr+1;
        $dailyUTime=$dailyUTime+$pm_out_utime_min;
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo date('h:i:s A', strtotime($studLogs_PM_OUT_row['logTime'])); ?> ]</p>
    <?php }else{
        $dailyUTime=$dailyUTime+0;
    ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo date('h:i:s A', strtotime($studLogs_PM_OUT_row['logTime'])); ?> ]</p>
    <?php } ?>
    
    
    <!-- time in seconds PM_OUT -->
    <?php }else{ $time_seconds_time_pm_out=0; ?>
    
        <p style="margin: 0px;">--:--</p>  
         
    <?php } ?>
    
    </td>
    
    
    <?php
    $SC_query3 = $conn->query("SELECT activity_id FROM activity_calendar WHERE completeDate='$logDateCtr' AND status='Add as working day'");
    if($SC_query3->rowCount()>0){
        
    ?>
    <!-- Late -->
    <td rowspan="2">
    
    <?php echo round($dailyLate).' minute(s)'; ?>

    </td>
    <!-- end Late -->
    
    
    <!-- Undertime -->
    <td rowspan="2">
    <?php echo round($dailyUTime).' minute(s)'; ?>
    </td>
    <!-- end Undertime -->
  <?php }else{?>
    <!-- Late -->
    <td>
    
    <?php echo round($dailyLate).' minute(s)'; ?>

    </td>
    <!-- end Late -->
    
    
    <!-- Undertime -->
    <td>
    <?php echo round($dailyUTime).' minute(s)'; ?>
    </td>
    <!-- end Undertime -->
    
    <!-- Overtime -->
    <td>
    <?php echo (isset($dailyOvertime) ? $dailyOvertime : 0) .' minute(s)'; ?>
    </td>
    <!-- end Overtime -->
  <?php } ?>
  
    
    <?php } } } ?>
    
  <?php
  $SC_query4 = $conn->query("select * FROM activity_calendar WHERE completeDate='$logDateCtr' AND status='Add as working day'");
  
  if($SC_query4->rowCount()>0){ ?>
  
  <?php }else{ ?>
  
  <?php } ?>
  </tr>
  
  <?php
  $SC_query2 = $conn->query("select * FROM activity_calendar WHERE completeDate='$logDateCtr' AND status='Add as working day'");
      
      if($SC_query2->rowCount()>0){
      $SC_row2=$SC_query2->fetch();
  ?>
  <tr>
   
    <td colspan="4" style="background-color: #b8ffd9;"><center><small><?php echo strtoupper($SC_row2['event_title']); ?></small></center></td>
 
  </tr>
  <?php } ?>
<?php } ?>


<?php
// Suppress warnings if parts are missing, default to 0
$amLate = isset($grandTotalamLateMin) ? (float)$grandTotalamLateMin : 0;
$pmLate = isset($grandTotalpmLateMin) ? (float)$grandTotalpmLateMin : 0;
$grandTotalLateMin = $amLate + $pmLate;
$final_lateHr = floor($grandTotalLateMin / 60);
$final_lateMin = $grandTotalLateMin % 60;
 


$amUTime = isset($grandTotalamUTimeMin) ? (float)$grandTotalamUTimeMin : 0;
$pmUTime = isset($grandTotalpmUTimeMin) ? (float)$grandTotalpmUTimeMin : 0;
$grandTotalUTimeMin = $amUTime + $pmUTime;
$final_uTimeHr = floor($grandTotalUTimeMin / 60);
$final_uTimeMin = $grandTotalUTimeMin % 60;
 


?>


  <tr>
  <td colspan="5"><strong class="pull-right">TOTAL</strong></td>
  <td style="background-color: lightgoldenrodyellow;"><strong><?php echo round($grandTotalLateMin); ?> minute(s)</strong></td>
  <td style="background-color: lightgoldenrodyellow;"><strong><?php echo round($grandTotalUTimeMin); ?> minute(s)</strong></td>
  <td style="background-color: lightgoldenrodyellow;"><strong><?php echo (isset($grandTotalOvertimeMin) ? $grandTotalOvertimeMin : 0); ?> minute(s)</strong></td>
  </tr>
  </table>





 
<table id="myTable" style="margin-top: 12px;">
<thead>
<tr>
<th colspan="15"><center>M O N T H L Y &nbsp;&nbsp;&nbsp; S U M M A R Y</center></th>
</tr>
</thead>
 

 

<tbody>

<tr>
<td colspan="3"><strong>Days Present</strong></td>
<td colspan="4"><strong>Late</strong></td>
<td colspan="4"><strong>Undertime</strong></td>
<td colspan="3"><strong>Days Absent</strong></td>
<td><strong><center>Leave / OB / RD</center></strong></td>
</tr>


<tr>
<td style="width: 7%; font-size: small;">AM</td>
<td style="width: 7%; font-size: small;">PM</td>
<td style="width: 7%; font-size: small;">Total</td>

<td style="width: 4%; font-size: small;">AM</td>
<td style="width: 4%; font-size: small;">PM</td>
<td style="width: 4%; font-size: small;">Total #</td>
<td style="width: 12%; font-size: small;">Total Time</td>

<td style="width: 4%; font-size: small;">AM</td>
<td style="width: 4%; font-size: small;">PM</td>
<td style="width: 4%; font-size: small;">Total #</td>
<td style="width: 12%; font-size: small;">Total Time</td>

<td style="width: 7%; font-size: small;">AM</td>
<td style="width: 7%; font-size: small;">PM</td>
<td style="width: 7%; font-size: small;">Total</td>

<td rowspan="2" style="width: 10%; font-size: 24px;"><center><strong><?php if($leaveCtr<=1){ echo $leaveCtr.' <small style="font-size: 12px;">day</small>'; }else{ echo $leaveCtr.' <small style="font-size: 12px;">day</small>'; } ?> </strong></center></td>
</tr>


<tr>
<td><?php echo $amPresentCtr; ?></td>
<td><?php echo $pmPresentCtr; ?></td>
<td><?php echo ($amPresentCtr+$pmPresentCtr)/2 ?></td>

<td><?php echo $amLateCtr; ?></td>
<td><?php echo $pmLateCtr; ?></td>
<td><?php echo ($amLateCtr+$pmLateCtr); ?></td>
<td><small><?php echo  $grandTotalLateMin.' min(s) | '.$final_lateHr.':'.$final_lateMin; ?> hr(s)</small></td>

<td><?php echo $amUTimeCtr; ?></td>
<td><?php echo $pmUTimeCtr; ?></td>
<td><?php echo ($amUTimeCtr+$pmUTimeCtr); ?></td>
<td><small><?php echo  $grandTotalUTimeMin.' min(s) | '.$final_uTimeHr.':'.$final_uTimeMin; ?> hr(s)</small></td>

<td><?php echo $amAbsentCtr; ?></td>
<td><?php echo $pmAbsentCtr; ?></td>
<td><?php echo ($amAbsentCtr+$pmAbsentCtr)/2; ?></td>





 
 
</tr>



 
<tr>
<td colspan="15">
<br />
<center>***THIS IS A SYSTEM GENERATED REPORT***</center>
<br />
</td>
</tr>
 
</tbody>
</table>

<?php include('footer_print.php'); ?>

</div>
</body>
</html>
       
            

