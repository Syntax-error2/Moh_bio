<!DOCTYPE html>
<html>
<?php

include('session.php');
require_once __DIR__ . '/dtr_day_status.php';
//error_reporting(0);

  $get_RFTag_id=$_GET['RFTag_id'];
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
 
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
 
<?php
 
$studData_query = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id OR biometric_id = :bio_id");
$studData_query->execute(['RFTag_id' => $_GET['RFTag_id'], 'bio_id' => $_GET['RFTag_id']]);
$studData_row=$studData_query->fetch();

  $grandTotalamLateMin=0;
  $grandTotalpmLateMin=0;
  
  $grandTotalamUTimeMin=0;
  $grandTotalpmUTimeMin=0;
  
?>



<table style="width: 45%;">
<tr>
<td style="width: 100%; border: none; padding: 0px; text-align: left;">
<table style="width: 100%;"  >

<tr>
<td style="border: none; background-color: #fff; padding: 0px;">CIVIL SERVICE FORM No. <strong>48</strong></td>
</tr>

<tr>
<td colspan="2" style="font-size: x-large; border: none; background-color: #fff; padding: 0px;"><center>DAILY TIME RECORD</center></td>
</tr>

<tr>
<td style="border: none; padding: 0px; text-align: center;" colspan="2">

     
    <p style="font-size: 14px; padding: 0px;">(Name)</p>
    <p style="font-size: 24px; font-variant-caps: all-petite-caps; padding: 0px;">
    <?php
    $mname=$studData_row['mname'];
            
    if($mname=='')
    {
 
            echo $studData_row['lname'].", ".$studData_row['fname'];
            
    }else{
            
            $suffix=$studData_row['suffix'];
            
            if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';
            
            echo $studData_row['lname'].", ".$studData_row['fname']." ".$finalMName;
            
    }
    ?></p>
</td>
</tr>

<tr>
<td style="border: none; background-color: #fff;  padding: 0px;">For the month of <strong><?php echo $mmWords; ?> 1, <?php echo $selectedYYYY; ?> - <?php echo $mmWords.' '.($MMmaxDay-1).', '.$selectedYYYY; ?></strong></td>
</tr>

<tr>
<td style="border: none; padding: 0px;">Official hours for arrival
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
( Regular days.......</td>
</tr>

<tr>
<td style="border: none; background-color: #fff; padding: 0px;">and departure
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
( Saturdays.......</td>
</tr>

</table>
</td>
 
</tr>
</table>

<br />
 
<table id="myTable" style="width: 45%;">
    
  <tr style="font-weight: light; font-size: 14px">
    <td rowspan="2" style="width:14%; text-align: center;"><strong>DAY</strong></td>
    <td colspan="2" style="width:32%; text-align: center;"><strong>AM</strong></td>
    <td colspan="2" style="width:32%; text-align: center;"><strong>PM</strong></td>
    <td colspan="2" style="width:12%; text-align: center;"><strong><small>LATE &amp; UNDERTIME</small></strong></td>
    <td rowspan="2" style="width:10%; text-align: center;"><strong><small>OVERTIME</small></strong></td>
  </tr>
  
  <tr style="font-weight: light; font-size: 14px">
    <td style="width:16%; text-align: center;">&nbsp;&nbsp;Arrival&nbsp;&nbsp;</td>
    <td style="width:16%; text-align: center;">Departure</td>
    <td style="width:16%; text-align: center;">&nbsp;&nbsp;Arrival&nbsp;&nbsp;</td>
    <td style="width:16%; text-align: center;">Departure</td>
    <td style="width:6%; text-align: center;">Hours</td>
    <td style="width:6%; text-align: center;">Min.</td>
  </tr>
 
<?php

$RFTag_id = !empty($studData_row['RFTag_id']) ? $studData_row['RFTag_id'] : $studData_row['biometric_id'];
require_once __DIR__ . '/overtime_dtr.php';
$otMonthStart = sprintf('%04d-%02d-01', $selectedYYYY, $selectedMM);
$otMonthEnd = date('Y-m-t', strtotime($otMonthStart));
$overtimeData = overtimeDtrLoad($conn, $studData_row['personnel_id'], $RFTag_id, $otMonthStart, $otMonthEnd);

// Use the same day/shift schedule for each punch and handle days with no schedule.
$scheduleByDay = [];
$scheduleQuery = $conn->prepare("SELECT day, am_IN, am_OUT, pm_IN, pm_OUT FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id ORDER BY schedule_id");
$scheduleQuery->execute(['do_id' => $studData_row['do_id'], 'shift_id' => $studData_row['shift_id']]);
while ($scheduleRow = $scheduleQuery->fetch(PDO::FETCH_ASSOC)) {
    $scheduleDay = ucfirst(strtolower(trim($scheduleRow['day'])));
    if (!isset($scheduleByDay[$scheduleDay])) {
        $scheduleByDay[$scheduleDay] = $scheduleRow;
    }
}

    $grandTotalamLateMin = 0;
    $grandTotalpmLateMin = 0;
    $grandTotalamUTimeMin = 0;
    $grandTotalpmUTimeMin = 0;
    $grandTotalLateMin = 0;
    $grandTotalUTimeMin = 0;
    $grandTotalOvertimeMin = 0;
    $amLateCtr = 0;
    $amPresentCtr = 0;
    $pmLateCtr = 0;
    $pmPresentCtr = 0;

    for($d=1; $d<$MMmaxDay; $d++){
        
        $dailyLate=0;
        $dailyUTime=0;
        /*
        if($d<10){
        $logDateCtr=$selectedMM.'/0'.$d.'/'.$selectedYYYY;
        }else{
        $logDateCtr=$selectedMM.'/'.$d.'/'.$selectedYYYY;
        }
        */
        if($d<10){
            
            $logDateCtr=$_GET['dateFrom'].'-0'.$d; $logDateLog = date('m/d/Y', strtotime($logDateCtr));
            
        }else{
            
            $logDateCtr=$_GET['dateFrom'].'-'.$d; $logDateLog = date('m/d/Y', strtotime($logDateCtr));
        
        }
        
        $dailyOvertime = 0;
        $dailyOvertimeLabel = '';
        
        $is_biometric = !empty($studData_row['biometric_id']);
        $bio_am_in = false;
        $bio_am_out = false;
        $bio_pm_in = false;
        $bio_pm_out = false;
        
        if ($is_biometric) {
            $bio_query = $conn->prepare("SELECT * FROM bio_dtr WHERE personnel_id = :pid AND log_date = :ldate ORDER BY time_in ASC");
            $bio_query->execute(['pid' => $studData_row['personnel_id'], 'ldate' => $logDateCtr]);
            $bio_logs = $bio_query->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($bio_logs as $log) {
                if (!empty($log['time_in']) && $log['time_in'] != '00:00:00') {
                    if ($log['time_in'] < '12:00:00' && !$bio_am_in) {
                        $bio_am_in = ['logTime' => $log['time_in'], 'late_status' => 'on'];
                    } else if ($log['time_in'] >= '12:00:00' && !$bio_pm_in) {
                        $bio_pm_in = ['logTime' => $log['time_in'], 'late_status' => 'on'];
                    }
                }
            }
            foreach($bio_logs as $log) {
                if (!empty($log['time_out']) && $log['time_out'] != '00:00:00') {
                    $tOut = $log['time_out'];
                    $tIn = $log['time_in'] ?? '';
                    if (!empty($tIn) && $tIn != '00:00:00' && $tIn < '12:00:00') {
                        if ($tOut < '14:00:00' || ($bio_pm_in && $tOut <= $bio_pm_in['logTime'])) {
                            if (!$bio_am_out) {
                                $bio_am_out = ['logTime' => $tOut, 'late_status' => 'on'];
                            }
                        } else if ($tOut >= '15:00:00') {
                            if (!$bio_pm_out) {
                                $bio_pm_out = ['logTime' => $tOut, 'late_status' => 'on'];
                            }
                        } else {
                            if (!$bio_am_out && !$bio_pm_in) {
                                $bio_am_out = ['logTime' => $tOut, 'late_status' => 'on'];
                            } else if (!$bio_pm_out) {
                                $bio_pm_out = ['logTime' => $tOut, 'late_status' => 'on'];
                            }
                        }
                    } else {
                        if ($tOut < '14:00:00' && (!$bio_pm_in || $tOut <= $bio_pm_in['logTime']) && !$bio_am_out) {
                            $bio_am_out = ['logTime' => $tOut, 'late_status' => 'on'];
                        } else if (!$bio_pm_out) {
                            $bio_pm_out = ['logTime' => $tOut, 'late_status' => 'on'];
                        }
                    }
                }
            }
            if ($bio_pm_out && $bio_pm_in && strtotime($bio_pm_out['logTime']) <= strtotime($bio_pm_in['logTime'])) {
                $bio_pm_out = false;
            }
            if ($bio_pm_out && $bio_am_out && abs(strtotime($bio_pm_out['logTime']) - strtotime($bio_am_out['logTime'])) < 120) {
                $bio_pm_out = false;
            }
        }
        
    ?>
    
  <tr>
 
    
 
    <?php
    $SC_query3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = :status");
    $SC_query3->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    $daySchedule = $scheduleByDay[$dayName] ?? null;
    $isRestDay = $dayName2 == 'Sat' || $dayName2 == 'Sun' || $dayName2 == 'Fri';

    $studLogs_sat_query = $conn->prepare("SELECT 1 FROM personnel_logs WHERE RFTag_id = :RFTag_id AND (logDate = :logDate OR logDate = :logDateCtr) AND logFlow IN ('AM IN', 'AM OUT', 'PM IN', 'PM OUT') LIMIT 1");
    $studLogs_sat_query->execute(['RFTag_id' => $RFTag_id, 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $hasDayAttendance = $studLogs_sat_query->rowCount() > 0 || $bio_am_in || $bio_am_out || $bio_pm_in || $bio_pm_out;

    $studLogs_remarks_query = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND (logDate = :logDate OR logDate = :logDateCtr) AND (COALESCE(travel_leave_code, '') != '' OR (COALESCE(remarks, '') != '' AND remarks NOT IN ('Updated', 'Inserted', '24hrs'))) ORDER BY log_id DESC LIMIT 1");
    $studLogs_remarks_query->execute(['RFTag_id' => $RFTag_id, 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $SRQ_row = $studLogs_remarks_query->fetch();
    $hasRemark = !empty($SRQ_row);
    $remarkText = $hasRemark ? trim($SRQ_row['remarks'] ?? '') : '';
    $isLeaveRemark = $hasRemark && (stripos($remarkText, 'leave') !== false || stripos($remarkText, 'vacation') !== false || stripos($remarkText, 'sick') !== false || stripos($SRQ_row['travel_leave_code'] ?? '', 'LV') !== false);
    $isWeekendLeave = $isRestDay && $isLeaveRemark;
    $ps_info = $hasRemark ? dtrCheckPassSlip($conn, $remarkText, $SRQ_row['travel_leave_code'] ?? null) : ['is_pass_slip' => false, 'time_info' => ''];
    $isPassSlip = $ps_info['is_pass_slip'];
    $passSlipTimes = $ps_info['time_info'];
    ?>
    
    <td <?php if($SC_query3->rowCount()>0){?> rowspan="2" <?php } ?> style="text-align: center; white-space: nowrap; padding: 4px 1px; font-size: 10px; <?php if($isWeekendLeave){ ?>background-color: #b8ffd9; font-weight: bold;<?php } ?>" <?php if($isWeekendLeave && $remarkText !== ''){ ?>title="<?php echo htmlspecialchars($remarkText, ENT_QUOTES, 'UTF-8'); ?>"<?php } ?>>
    
    <?php
    echo date('d/m/y', $timestamp);
    $holidayRow = dtrHolidayForDate($conn, $logDateCtr);
    $isNonWorkingHoliday = dtrIsNonWorkingHoliday($holidayRow);
    if ($holidayRow) {
        echo '<br><small>HOLIDAY</small>';
    }
    if ($isWeekendLeave) {
        echo '<br><small style="color: #0b6623; font-weight: bold; font-size: 8px; display: block; line-height: 1.1;">LEAVE</small>';
    }
    if ($isPassSlip) {
        $ps_tip = $remarkText !== '' ? $remarkText : 'PASS SLIP';
        if (!empty($passSlipTimes)) {
            $ps_tip .= ' ('.$passSlipTimes.')';
        }
        echo '<br><small style="color: #0056b3; font-weight: bold; font-size: 8px; display: block; line-height: 1.1;" title="'.htmlspecialchars($ps_tip, ENT_QUOTES, 'UTF-8').'">PASS SLIP'.(!empty($passSlipTimes) ? '<br>'.$passSlipTimes : '').'</small>';
    }
    ?>
    </td>
    <?php
    if(!$hasDayAttendance && !$isNonWorkingHoliday && $isRestDay){ ?>
    <td colspan="7" style="background-color: #ececec; text-align: center;">
    <strong>
    <?php echo strtoupper(implode(' ', str_split($dayName))); ?>
    </strong>
    </td>
    <?php }else if($hasRemark && (!$isRestDay || !$isLeaveRemark) && !($isPassSlip && $hasDayAttendance)){ ?> 
    
    <td colspan="7" style="background-color: #b8ffd9; text-align: center;">
    <strong><?php echo htmlspecialchars($remarkText !== '' ? $remarkText : 'Leave/Travel Record', ENT_QUOTES, 'UTF-8'); ?></strong>
    </td>
    
    <?php }else{
     
      $SC_query = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != :status");
      $SC_query->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
      
      $SC_row = $SC_query->fetch(PDO::FETCH_ASSOC);
      if ($isNonWorkingHoliday && !$hasDayAttendance) { ?>
      <td colspan="7" style="background-color: #ffbac5; text-align: center;"><strong>HOLIDAY — <?php echo htmlspecialchars($holidayRow['event_title'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
      <?php } elseif ($SC_row && !$hasDayAttendance && !$holidayRow) { ?>
      <td colspan="7" style="background-color: #ffbac5; text-align: center;"><strong><?php echo htmlspecialchars($SC_row['event_title'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
      <?php } elseif (!$hasDayAttendance && $logDateCtr < date('Y-m-d') && !$isRestDay) {
          $grandTotalamUTimeMin += 240;
          $grandTotalpmUTimeMin += 240;
      ?>
      <td colspan="7" style="background-color: #fff3cd; text-align: center;"><strong>ABSENT</strong></td>
      <?php } else { ?>

    <!-- AM IN -->
    <td>
    <?php
    $studLogs_query_AM_IN = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr)");
    $studLogs_query_AM_IN->execute(['RFTag_id' => $RFTag_id, 'logFlow' => "AM IN", 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $has_am_in = $studLogs_query_AM_IN->rowCount() > 0;
    $studLogs_AM_IN_row = $studLogs_query_AM_IN->fetch();
    
    if (!$has_am_in && $is_biometric && !empty($bio_am_in)) {
        $studLogs_AM_IN_row = $bio_am_in;
        $has_am_in = true;
    }
    
    if($has_am_in){
    
    $str_time_am_in= date("H:i:s", strtotime($studLogs_AM_IN_row['logTime']));
    $str_time_am_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_in);
    sscanf($str_time_am_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_in = ($hours * 3600) + $minutes * 60 + $seconds;
        
    ?>
    
    
    <?php
    if(!$isRestDay && $studLogs_AM_IN_row['late_status']=="on" && !empty($daySchedule['am_IN']) && strtotime($daySchedule['am_IN']) !== false){

        $str_time_sched_am_in_late= date("H:i:s", strtotime($daySchedule['am_IN']));
        $str_time_sched_am_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_in_late);
        sscanf($str_time_sched_am_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_am_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $am_in_late_min=(($time_seconds_time_am_in-$time_seconds_time_am_in_late)/60);
        
        if ($am_in_late_min <= 15) {
            $am_in_late_min = 0;
            $dailyLate+=$am_in_late_min;
            $amPresentCtr=$amPresentCtr+1;
            ?>
            <p style="background-color: white; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_AM_IN_row['logTime']); ?></p>
            <?php
        } else {
            $grandTotalamLateMin+=$am_in_late_min;
            $dailyLate+=$am_in_late_min;
            $amLateCtr=$amLateCtr+1;
            $amPresentCtr=$amPresentCtr+1;
            ?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_AM_IN_row['logTime']); ?></p>
        <?php } ?>
    <?php }else{ 
        
        $dailyLate=$dailyLate+0;
        
        ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_AM_IN_row['logTime']); ?></p>
    <?php } ?>

    <!-- time in seconds AM_IN -->
    <?php
    
    
    
    
    
    ?>
    
    <?php }else{ $time_seconds_time_am_in=0; ?>
       
        <p style="margin: 0px;">--:--</p>   
    <?php } ?>
    
    </td>
    
    
    <!-- AM OUT -->
    <td>
    <?php
    $studLogs_query_AM_OUT = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr)");
    $studLogs_query_AM_OUT->execute(['RFTag_id' => $RFTag_id, 'logFlow' => "AM OUT", 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $has_am_out = $studLogs_query_AM_OUT->rowCount() > 0;
    $studLogs_AM_OUT_row = $studLogs_query_AM_OUT->fetch();

    if (!$has_am_out && $is_biometric && !empty($bio_am_out)) {
        $studLogs_AM_OUT_row = $bio_am_out;
        $has_am_out = true;
    }
    
    if($has_am_out){ 
    
    $str_time_am_out= date("H:i:s", strtotime($studLogs_AM_OUT_row['logTime']));
    $str_time_am_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_out);
    sscanf($str_time_am_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_out = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
     
    <?php
    if(!$isRestDay && $studLogs_AM_OUT_row['late_status'] == "on" && !empty($daySchedule['am_OUT']) && strtotime($daySchedule['am_OUT']) !== false){

        $str_time_sched_am_out_utime= date("H:i:s", strtotime($daySchedule['am_OUT']));
        $str_time_sched_am_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_out_utime);
        sscanf($str_time_sched_am_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_am_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $am_out_utime_min=($time_seconds_time_am_out_utime-$time_seconds_time_am_out)/60;
        
        $grandTotalamUTimeMin=$grandTotalamUTimeMin+$am_out_utime_min;
 
        $dailyUTime=$dailyUTime+$am_out_utime_min;
        
    
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_AM_OUT_row['logTime']); ?></p>
    <?php }else{
        
        $dailyUTime=$dailyUTime+0;
    
    ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_AM_OUT_row['logTime']); ?></p>
    <?php } ?>

    <!-- time in seconds AM_OUT -->
 
    <?php }else{ $time_seconds_time_am_out=0; 
    
    $studLogs_query_PM_OUT_chk = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr)");
    $studLogs_query_PM_OUT_chk->execute(['RFTag_id' => $RFTag_id, 'logFlow' => "PM OUT", 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $chk_has_pm_out = $studLogs_query_PM_OUT_chk->rowCount() > 0;
    if (!$chk_has_pm_out && $is_biometric && !empty($bio_pm_out)) {
        $chk_has_pm_out = true;
    }
    
    if($chk_has_pm_out AND $has_am_in){ }else{ ?>
    
    <p style="margin: 0px;">--:--</p>   
    
    <?php } } ?>
    
    
    </td>
    
    <?php
    //ADD AM NO LOGS | 4 HOURS AS ABSENT
    if(!$isRestDay AND !$has_am_in AND !$has_am_out AND !$isNonWorkingHoliday AND $logDateCtr < date('Y-m-d') AND !$isPassSlip){
        
        $grandTotalamUTimeMin=$grandTotalamUTimeMin+240;
        $dailyUTime=$dailyUTime+240;
        
    } ?>
    
    
    <!-- PM IN -->
    <td>
    <?php
    $studLogs_query_PM_IN = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr)");
    $studLogs_query_PM_IN->execute(['RFTag_id' => $RFTag_id, 'logFlow' => "PM IN", 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $has_pm_in = $studLogs_query_PM_IN->rowCount() > 0;
    $studLogs_PM_IN_row = $studLogs_query_PM_IN->fetch();

    if (!$has_pm_in && $is_biometric && !empty($bio_pm_in)) {
        $studLogs_PM_IN_row = $bio_pm_in;
        $has_pm_in = true;
    }
    
    if($has_pm_in){
     
    // time in seconds PM_IN //
    
    $str_time_pm_in= date("H:i:s", strtotime($studLogs_PM_IN_row['logTime']));
    $str_time_pm_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_in);
    sscanf($str_time_pm_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_in = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
    <?php
    if(!$isRestDay && $studLogs_PM_IN_row['late_status'] == "on" && !empty($daySchedule['pm_IN']) && strtotime($daySchedule['pm_IN']) !== false){

        $str_time_sched_pm_in_late= date("H:i:s", strtotime($daySchedule['pm_IN']));
        $str_time_sched_pm_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_in_late);
        sscanf($str_time_sched_pm_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $pm_in_late_min=(($time_seconds_time_pm_in-$time_seconds_time_pm_in_late)/60);
        
        if ($pm_in_late_min <= 15) {
            $pm_in_late_min = 0;
            $dailyLate+=$pm_in_late_min;
            $pmPresentCtr=$pmPresentCtr+1;
            ?>
            <p style="background-color: white; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_PM_IN_row['logTime']); ?></p>
            <?php
        } else {
            $grandTotalpmLateMin+=$pm_in_late_min;
            $dailyLate+=$pm_in_late_min; 
            $pmLateCtr=$pmLateCtr+1;
            $pmPresentCtr=$pmPresentCtr+1;?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_PM_IN_row['logTime']); ?></p>
        <?php } ?>
    <?php }else{ 
        
        $dailyLate=$dailyLate+0; 
        
        ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_PM_IN_row['logTime']); ?></p>
    <?php } ?>
 
    <?php }else{ $time_seconds_time_pm_in=0; ?>
    
    <?php
    
    $studLogs_query_PM_OUT_chk = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr)");
    $studLogs_query_PM_OUT_chk->execute(['RFTag_id' => $RFTag_id, 'logFlow' => "PM OUT", 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $chk_has_pm_out = $studLogs_query_PM_OUT_chk->rowCount() > 0;
    if (!$chk_has_pm_out && $is_biometric && !empty($bio_pm_out)) {
        $chk_has_pm_out = true;
    }
    
    if($chk_has_pm_out AND $has_am_in){ }else{ ?>
    
    <p style="margin: 0px;">--:--</p> 
    
    <?php } } ?>
    
    </td>
    
    
    <!-- PM OUT -->
    <td>
    <?php
    $studLogs_query_PM_OUT = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr)");
    $studLogs_query_PM_OUT->execute(['RFTag_id' => $RFTag_id, 'logFlow' => "PM OUT", 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    $has_pm_out = $studLogs_query_PM_OUT->rowCount() > 0;
    $studLogs_PM_OUT_row = $studLogs_query_PM_OUT->fetch();

    if (!$has_pm_out && $is_biometric && !empty($bio_pm_out)) {
        $studLogs_PM_OUT_row = $bio_pm_out;
        $has_pm_out = true;
    }
    
    if($has_pm_out){
        
    $str_time_pm_out= date("H:i:s", strtotime($studLogs_PM_OUT_row['logTime']));
    $str_time_pm_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_out);
    sscanf($str_time_pm_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_out = ($hours * 3600) + $minutes * 60 + $seconds;
        
    $time_seconds_time_pm_out_utime = null;
    if (!empty($daySchedule['pm_OUT']) && strtotime($daySchedule['pm_OUT']) !== false) {
        $str_time_sched_pm_out_utime = date("H:i:s", strtotime($daySchedule['pm_OUT']));
        sscanf($str_time_sched_pm_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
    }
    
    // Check for Overtime
        $overtime_sec = 0; 
        $checkDate = date('m/d/Y', strtotime($logDateCtr));
    if ($time_seconds_time_pm_out_utime !== null && ($overtimeData['requests'][$logDateCtr] ?? null) === 'approved') {
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
    $overtimeState = overtimeDtrDay($overtimeData, $logDateCtr, max(0, $overtime_sec / 60));
    $dailyOvertime = $overtimeState['minutes'];
    $dailyOvertimeLabel = $overtimeState['label'];
    $grandTotalOvertimeMin += $dailyOvertime;
        
    if(!$isRestDay && $studLogs_PM_OUT_row['late_status'] == "on" && $time_seconds_time_pm_out_utime !== null){
        $pm_out_utime_min=($time_seconds_time_pm_out_utime-$time_seconds_time_pm_out)/60;
        $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+$pm_out_utime_min;
        $dailyUTime=$dailyUTime+$pm_out_utime_min;
    
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_PM_OUT_row['logTime']); ?></p>
    <?php }else{
        $dailyUTime=$dailyUTime+0;
    ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo formatCsf48Time($studLogs_PM_OUT_row['logTime']); ?></p>
    <?php } ?>
    
 
    <?php }else{ $time_seconds_time_pm_out=0; ?>
        <p style="margin: 0px;">--:--</p>
    <?php } ?>
    
    </td>
    
    <?php
    //ADD PM NO LOGS | 4 HOURS AS ABSENT
    if(!$isRestDay AND !$has_pm_in AND !$has_pm_out AND !$isNonWorkingHoliday AND $logDateCtr < date('Y-m-d') AND !$isPassSlip){
        
        $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+240;
        $dailyUTime=$dailyUTime+240;
        
    }
    
    $dailyTotalMins = $dailyLate + $dailyUTime;
    $dailyFinalHR = $dailyTotalMins > 0 ? floor($dailyTotalMins / 60) : 0;
    $dailyFinalMIN = $dailyTotalMins > 0 ? round(fmod($dailyTotalMins, 60)) : 0;

    $SC_query3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = :status");
    $SC_query3->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
    if($SC_query3->rowCount()>0){
    ?>
    
    <td rowspan="2"><?php echo $dailyFinalHR; ?></td>
    
    <td rowspan="2"><?php echo $dailyFinalMIN; ?></td>
    
    <td rowspan="2"><?php echo $dailyOvertime; ?><?php if ($dailyOvertimeLabel !== '') echo '<br><small>' . htmlspecialchars($dailyOvertimeLabel, ENT_QUOTES, 'UTF-8') . '</small>'; ?></td>
  <?php }else{?>
    <td><?php echo $dailyFinalHR; ?></td>
    
    <td><?php echo $dailyFinalMIN; ?></td>
    
    <td><?php echo $dailyOvertime; ?><?php if ($dailyOvertimeLabel !== '') echo '<br><small>' . htmlspecialchars($dailyOvertimeLabel, ENT_QUOTES, 'UTF-8') . '</small>'; ?></td>
    <?php } ?>
  
    
    
   
    <?php } } ?>
    
 
  
  
  <?php
  $SC_query4 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = :status");
  $SC_query4->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
  if($SC_query4->rowCount()>0){ }else{ }
  ?>
  
  </tr>
  
  <?php
  $SC_query2 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = :status");
  $SC_query2->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
      if($SC_query2->rowCount()>0){
      $SC_row2=$SC_query2->fetch();
  ?>
  <tr>
   
    <td colspan="4" style="background-color: #b8ffd9; text-align: center;"><small><?php echo strtoupper($SC_row2['event_title']); ?></small></td>
 
  </tr>
  <?php } ?>
 
<?php } ?>


<?php

$grandTotalLateMin = $grandTotalamLateMin + $grandTotalpmLateMin;
$grandTotalUTimeMin = $grandTotalamUTimeMin + $grandTotalpmUTimeMin;
$totalLateUTimeMin = $grandTotalLateMin + $grandTotalUTimeMin;

$final_hr = floor($totalLateUTimeMin / 60);
$final_min = round(fmod($totalLateUTimeMin, 60));
$format_final_hr = $final_hr . ($final_hr == 1 ? ' hr' : ' hrs');
$format_final_min = $final_min . ($final_min == 1 ? ' min.' : ' mins.');
?>

<tr>

<td colspan="4" style="padding: 8px 8px 8px 8px; text-align: right;">
<strong style="font-size: larger; "> TOTAL LATE &amp; UNDERTIME:</strong>
</td>
 
<td style="background-color: lightgoldenrodyellow;"><strong><?php echo $format_final_hr; ?></strong></td>
<td style="background-color: lightgoldenrodyellow;"><strong><?php echo $format_final_min; ?></strong></td>

<td colspan="2" style="background-color: lightgoldenrodyellow; text-align: center;">
<strong style="font-size: larger; ">OT: </strong>
<strong><?php echo (isset($grandTotalOvertimeMin) ? $grandTotalOvertimeMin : 0); ?> min.</strong>
</td>

</tr>


<tr>
<td colspan="7" style="padding: 8px 8px 8px 8px;">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY on my honor that the above is true and correct
 report of the hours of work performed, record of which was made daily at the time of
  arrival at and departure from office. <br /> 
  
  <p style="float: right; text-decoration-line: underline; font-size: 18px; font-variant: all-petite-caps;">
  <?php
    $mname=$studData_row['mname'];
            
    if($mname=='')
    {
 
            echo $studData_row['lname'].", ".$studData_row['fname'];
            
    }else{
            
            $suffix=$studData_row['suffix'];
            
            if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';
            
            echo $studData_row['lname'].", ".$studData_row['fname']." ".$finalMName;
            
    }
    ?>
  </p> 
  
</td>
</tr>


<tr>
<td colspan="7" style="padding: 8px 8px 8px 8px;">
 
Verified as to the prescribed office hours. <br /> 

  <p style="float: right; text-decoration-line: underline; font-size: 18px; font-variant: all-petite-caps; margin: 0px;">
    
    <?php
  
    $dept_off_query = $conn->prepare("SELECT officeHead_id FROM dept_offices WHERE do_id = :do_id");
    $dept_off_query->execute(['do_id' => $studData_row['do_id']]);
    $do_row = $dept_off_query->fetch(); 
    
    
    $officeHead_query = $conn->prepare("SELECT lname, fname, mname, suffix FROM personnels WHERE personnel_id = :personnel_id");
    $officeHead_query->execute(['personnel_id' => $do_row['officeHead_id']]);
    $oh_row=$officeHead_query->fetch();
             
    if($oh_row['suffix']=="-")
    {
        
    echo $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname'];
    
    }else{
        
    echo $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname']." ".$oh_row['suffix'];
    
    }  
                                    
                                    
                                    
    ?>
  
  </p>
  <br /> <br /> 
  <i style="text-decoration-line: none !important; font-variant-caps: normal !important; float: right;">In-charge</i> 
  
</td>
</tr>
 
 
</table>


<?php $conn=null; ?>

<?php include_once('universal_excel_export.php'); ?>
</body>
</html>

