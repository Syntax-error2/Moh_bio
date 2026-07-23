<!DOCTYPE html>
<html>

<?php

include('session.php');  
//error_reporting(0);

// Sanitize and validate GET parameters
$get_RFTag_id = isset($_GET['RFTag_id']) ? $_GET['RFTag_id'] : '';
$dateFromInput = isset($_GET['dateFrom']) ? trim($_GET['dateFrom']) : date('Y-m-01');
$dateToInput = isset($_GET['dateTo']) ? trim($_GET['dateTo']) : '';

if ($dateFromInput === '') {
    $dateFromInput = date('Y-m-01');
}

if ($dateToInput === '') {
    $dateToInput = $dateFromInput;
}

function normalizeReportDateValue($value, $isEndDate = false) {
    $value = trim((string)$value);

    if ($value === '') {
        return $isEndDate ? date('Y-m-d') : date('Y-m-01');
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return $value;
    }

    if (preg_match('/^\d{4}-\d{2}$/', $value)) {
        return $isEndDate ? date('Y-m-t', strtotime($value . '-01')) : $value . '-01';
    }

    if (preg_match('/^\d{4}$/', $value)) {
        return $isEndDate ? $value . '-12-31' : $value . '-01-01';
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return $isEndDate ? date('Y-m-d') : date('Y-m-01');
    }

    return date('Y-m-d', $timestamp);
}

$startDate = new DateTime(normalizeReportDateValue($dateFromInput, false));
$endDate = new DateTime(normalizeReportDateValue($dateToInput, true));

if ($endDate < $startDate) {
    $temporaryDate = clone $startDate;
    $startDate = clone $endDate;
    $endDate = $temporaryDate;
}

$selectedMM = $startDate->format('m');
$selectedYYYY = $startDate->format('Y');
$mmWords = $startDate->format('F');
$dateRangeLabel = strtoupper($startDate->format('M. j, Y')) . ' TO ' . strtoupper($endDate->format('M. j, Y'));

$grandTotalTRHr = 0;
$grandTotalTRMin = 0;

$grandTotalamLateMin = 0;
$grandTotalpmLateMin = 0;

$grandTotalamUTimeMin = 0;
$grandTotalpmUTimeMin = 0;

/**
 * Helper function to get personnel logs
 */
function getPersonnelLogs($conn, $RFTag_id, $logDate, $logFlow = null, $remarksFilter = false) {
    try {
        if ($remarksFilter) {
            $stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (remarks != '' AND remarks != 'Updated' AND remarks != 'Inserted')");
            $stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDate]);
        } elseif ($logFlow) {
            $stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND logDate = :logDate");
            $stmt->execute([':RFTag_id' => $RFTag_id, ':logFlow' => $logFlow, ':logDate' => $logDate]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
            $stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDate]);
        }
        return $stmt;
    } catch (PDOException $e) {
        error_log("Error in getPersonnelLogs: " . $e->getMessage());
        return false;
    }
}

/**
 * Helper function to get schedule
 */
function getSchedule($conn, $school_id, $do_id, $shift_id, $day, $field) {
    try {
        $stmt = $conn->prepare("SELECT $field FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $stmt->execute([
            ':school_id' => $school_id,
            ':do_id' => $do_id,
            ':shift_id' => $shift_id,
            ':day' => $day
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getSchedule: " . $e->getMessage());
        return false;
    }
}

/**
 * Helper function to check activity calendar
 */
function getActivityCalendar($conn, $completeDate, $status = null) {
    try {
        if ($status === null) {
            $stmt = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate");
            $stmt->execute([':completeDate' => $completeDate]);
        } elseif ($status === 'NOT_WORKING_DAY') {
            $stmt = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != 'Add as working day'");
            $stmt->execute([':completeDate' => $completeDate]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = :status");
            $stmt->execute([':completeDate' => $completeDate, ':status' => $status]);
        }
        return $stmt;
    } catch (PDOException $e) {
        error_log("Error in getActivityCalendar: " . $e->getMessage());
        return false;
    }
}
 
                 
include('header_print.php');

?>

<body>
 

<table style="width: 100%;">
<tr>
<td align="left" style="width: 100%; border: none;">
<?php include('header_print_letterHead.php'); ?>
</td>
 
</tr>
</table>

<hr />

<?php
try {
    $studData_query = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id");
    $studData_query->execute([':RFTag_id' => $get_RFTag_id]);
    $studData_row = $studData_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$studData_row) {
        die("Personnel not found with RFTag_id: " . htmlspecialchars($get_RFTag_id));
    }
} catch (PDOException $e) {
    error_log("Error fetching personnel data: " . $e->getMessage());
    die("Error loading personnel data. Please try again.");
}

?>

 


<table style="width: 100%;">

  <tr style="font-size: large;" style="border: none;">
    
    
    <td style="width: 40%; border: none;" colspan="2">
    <small>Employment Status</small><br />
    <strong><?php
    try {
        $emp_stat_query = $conn->prepare("SELECT * FROM emp_status WHERE empStat_id = :empStat_id");
        $emp_stat_query->execute([':empStat_id' => $studData_row['empStat_id']]);
        $es_row = $emp_stat_query->fetch(PDO::FETCH_ASSOC);
        echo strtoupper($es_row['emp_stat_name'] ?? 'N/A');
    } catch (PDOException $e) {
        error_log("Error fetching employment status: " . $e->getMessage());
        echo "N/A";
    }
    ?></strong>
    
    </td>
    
    <td style="width: 40%; border: none;" colspan="2">
    <small>Department / Office</small><br />
    <strong style="font-size: small;"><?php
    try {
        $dept_query = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
        $dept_query->execute([':do_id' => $studData_row['do_id']]);
        $dept_row = $dept_query->fetch(PDO::FETCH_ASSOC);
        echo strtoupper($dept_row['dept_office_name'] ?? 'N/A');
    } catch (PDOException $e) {
        error_log("Error fetching department: " . $e->getMessage());
        echo "N/A";
    }
    ?></strong> 
    
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
    <small>Date Covered</small><br />
    <strong><?php echo htmlspecialchars($dateRangeLabel); ?></strong>
  
  
  </td>
  </tr>
</table>
 <hr />

<table id="myTable">

  <tr style="font-weight: light; font-size: 14px">
    
    <td style="width:8%;"><center><strong>DATE</strong></center></td>
    
    <td style="width:18%;"><center><strong>AM IN</strong></center></td>
    <td style="width:18%;"><center><strong>AM OUT</strong></center></td>
    <td style="width:18%;"><center><strong>PM IN</strong></center></td>
    <td style="width:18%;"><center><strong>PM OUT</strong></center></td>
    <td style="width:10%;"><center><strong>TARDINESS</strong></center></td>
    <td style="width:10%;"><center><strong>UNDERTIME</strong></center></td>
  </tr>
 
<?php
 
    $RFTag_id=$studData_row['RFTag_id'];
 
    $amPresentCtr=0;
    $pmPresentCtr=0;
    
    $amLateCtr=0;
    $pmLateCtr=0;
    
    $amUTimeCtr=0;
    $pmUTimeCtr=0;
    
    $amAbsentCtr=0;
    $pmAbsentCtr=0;
    
    $leaveCtr=0;
    
    
    $datePeriodEnd = (clone $endDate)->modify('+1 day');
    $datePeriod = new DatePeriod($startDate, new DateInterval('P1D'), $datePeriodEnd);

    foreach ($datePeriod as $currentDate) {
        $logDateCtr = $currentDate->format('m/d/Y');
        $dailyLate=0;
        $dailyUTime=0;
        $dayName = $currentDate->format('l');
        $dayName2 = substr($dayName, 0, 3);
 
    ?>
    
  <tr>
  
  <?php
  try {
      $SC_query3 = $conn->prepare("SELECT activity_id FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Add as working day'");
      $SC_query3->execute([':completeDate' => $logDateCtr]);
      
      if($SC_query3->rowCount() > 0) {
  ?>
  <td rowspan="2">
        <?php echo substr($logDateCtr, 0, 6) . substr($logDateCtr, 8, 2) . " <sup>" . htmlspecialchars($dayName2) . "</sup>"; ?>
    </td>
  <?php } else { ?>
  <td>
        <?php echo substr($logDateCtr, 0, 6) . substr($logDateCtr, 8, 2) . " <sup>" . htmlspecialchars($dayName2) . "</sup>"; ?>
    </td>
  <?php }
  } catch (PDOException $e) {
      error_log("Error checking activity calendar: " . $e->getMessage());
      echo "<td>ERR</td>";
  }
  ?>
  
     
    
    
    <?php
    $studLogs_remarks_query = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (remarks != '' AND remarks != 'Updated' AND remarks != 'Inserted')");
    $studLogs_remarks_query->execute([
        ':RFTag_id' => $RFTag_id,
        ':logDate' => $logDateCtr
    ]);
    
    if($studLogs_remarks_query->rowCount() > 0) { 
        $SRQ_row = $studLogs_remarks_query->fetch(PDO::FETCH_ASSOC);
        $leaveCtr = $leaveCtr + 1;
    ?> 
    <td colspan="7" style="background-color: #b8ffd9;"><center><strong><?php echo htmlspecialchars($SRQ_row['remarks']); ?></strong></center></td>
     
      <?php } else {
        
        $studLogs_sat_query = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
        $studLogs_sat_query->execute([
            ':RFTag_id' => $RFTag_id,
            ':logDate' => $logDateCtr
        ]);
        
        if($studLogs_sat_query->rowCount() == 0 AND ($dayName2 == 'Sat' OR $dayName2 == 'Sun')) { ?>
    
    <td colspan="6" style="background-color: #ececec;"><center><strong><?php if($dayName2=='Sat'){ echo "S A T U R D A Y"; } if($dayName2=='Sun'){ echo "S U N D A Y"; } ?></strong></center></td>
     
      <?php } else {
     
        $SC_query = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != 'Add as working day'");
        $SC_query->execute([':completeDate' => $logDateCtr]);
      
        if($SC_query->rowCount() > 0) {
            $SC_row = $SC_query->fetch(PDO::FETCH_ASSOC);
      ?>
        
      <td colspan="6" style="background-color: #ffbac5;"><center><strong><?php echo htmlspecialchars($SC_row['event_title']).'</strong> [ '.htmlspecialchars($SC_row['act_type']).' ]'; ?></strong></center></td>
      
      <?php } else { ?>
    
    <!-- AM IN -->
    <td>
    <?php
    $studLogs_query_AM_IN = getPersonnelLogs($conn, $RFTag_id, $logDateCtr, 'AM IN');
    $studLogs_AM_IN_row = $studLogs_query_AM_IN ? $studLogs_query_AM_IN->fetch(PDO::FETCH_ASSOC) : null;
    ?>
    
    <?php
    if($studLogs_query_AM_IN && $studLogs_query_AM_IN->rowCount() > 0 && $studLogs_AM_IN_row){
    
    $str_time_am_in = date("H:i:s", strtotime($studLogs_AM_IN_row['logTime']));
    $str_time_am_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_in);
    sscanf($str_time_am_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_in = ($hours * 3600) + $minutes * 60 + $seconds;
        
    ?>
    
    
    <?php
    if($studLogs_AM_IN_row['late_status'] === 'on'){
        
        $sq_row = getSchedule($conn, $school_id, $studData_row['do_id'], $studData_row['shift_id'], $dayName, 'am_IN_co');
 
        if ($sq_row) {
            $str_time_sched_am_in_late = date("H:i:s", strtotime($sq_row['am_IN_co']));
            $str_time_sched_am_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_in_late);
            sscanf($str_time_sched_am_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds_time_am_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
            
            $am_in_late_min = ($time_seconds_time_am_in - $time_seconds_time_am_in_late) / 60;
            
            $grandTotalamLateMin = $grandTotalamLateMin + $am_in_late_min;
            
            $amLateCtr = $amLateCtr + 1;
            $amPresentCtr = $amPresentCtr + 1;
            
            $dailyLate = $dailyLate + $am_in_late_min;
        } else {
            $dailyLate = $dailyLate + 0;
            $amPresentCtr = $amPresentCtr + 1;
        }
        ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo htmlspecialchars($studLogs_AM_IN_row['logTime']); ?> ]</p>
    <?php } else { 
        
        $dailyLate = $dailyLate + 0;
        $amPresentCtr = $amPresentCtr + 1;
        
        ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo htmlspecialchars($studLogs_AM_IN_row['logTime']); ?> ]</p>
    <?php } ?>

    <!-- time in seconds AM_IN -->
    <?php
    
    
    
    
    
    ?>
    
    <?php } else { $time_seconds_time_am_in = 0;  
    
       $amAbsentCtr = $amAbsentCtr + 1; ?>
       
        <p style="margin: 0px;">--:--</p>   
    <?php } ?>
    
    </td>
    
    
    <!-- AM OUT -->
    <td>
    <?php
    $studLogs_query_AM_OUT = getPersonnelLogs($conn, $RFTag_id, $logDateCtr, 'AM OUT');
    $studLogs_AM_OUT_row = $studLogs_query_AM_OUT ? $studLogs_query_AM_OUT->fetch(PDO::FETCH_ASSOC) : null;
    ?>
    
    <?php
    if($studLogs_query_AM_OUT && $studLogs_query_AM_OUT->rowCount() > 0 && $studLogs_AM_OUT_row){ 
        
    $str_time_am_out = date("H:i:s", strtotime($studLogs_AM_OUT_row['logTime']));
    $str_time_am_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_out);
    sscanf($str_time_am_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_out = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
    <?php
    if($studLogs_AM_OUT_row['late_status'] === 'on'){
        $am_out_utime_min = 0;
        
        $sq_row = getSchedule($conn, $school_id, $studData_row['do_id'], $studData_row['shift_id'], $dayName, 'am_OUT');
        
        if ($sq_row) {
            $str_time_sched_am_out_utime = date("H:i:s", strtotime($sq_row['am_OUT']));
            $str_time_sched_am_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_out_utime);
            sscanf($str_time_sched_am_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds_time_am_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
            
            $am_out_utime_min = ($time_seconds_time_am_out_utime - $time_seconds_time_am_out) / 60;
            
            $grandTotalamUTimeMin = $grandTotalamUTimeMin + $am_out_utime_min;
        
        $amUTimeCtr=$amUTimeCtr+1;
        }
        
        $dailyUTime = $dailyUTime + $am_out_utime_min;
            
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo htmlspecialchars($studLogs_AM_OUT_row['logTime']); ?> ]</p>
    <?php } else {
        
        $dailyUTime = $dailyUTime + 0; ?>
        
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo htmlspecialchars($studLogs_AM_OUT_row['logTime']); ?> ]</p>
    
    <?php } ?>

    <!-- time in seconds AM_OUT -->
    
    
    <?php } else { $time_seconds_time_am_out = 0; 
    
    $studLogs_query_PM_OUT_chk = getPersonnelLogs($conn, $RFTag_id, $logDateCtr, 'PM OUT');
    
    if($studLogs_query_PM_OUT_chk && $studLogs_query_PM_OUT_chk->rowCount() > 0 && $studLogs_query_AM_IN && $studLogs_query_AM_IN->rowCount() > 0){ } else { ?>
    
    <p style="margin: 0px;">--:--</p>   
    
    <?php } } ?>
    
    
    </td>
    
    
    <!-- PM IN -->
    <td>
    <?php
    $studLogs_query_PM_IN = getPersonnelLogs($conn, $RFTag_id, $logDateCtr, 'PM IN');
    $studLogs_PM_IN_row = $studLogs_query_PM_IN ? $studLogs_query_PM_IN->fetch(PDO::FETCH_ASSOC) : null;
    ?>
    
    <?php
    if($studLogs_query_PM_IN && $studLogs_query_PM_IN->rowCount() > 0 && $studLogs_PM_IN_row){ ?>
    
    <!-- time in seconds PM_IN -->
    <?php
    
    $str_time_pm_in = date("H:i:s", strtotime($studLogs_PM_IN_row['logTime']));
    $str_time_pm_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_in);
    sscanf($str_time_pm_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_in = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
    <?php
    if($studLogs_PM_IN_row['late_status'] === 'on'){
        
        $sq_row = getSchedule($conn, $school_id, $studData_row['do_id'], $studData_row['shift_id'], $dayName, 'pm_IN_co');
        
        if ($sq_row) {
            $str_time_sched_pm_in_late = date("H:i:s", strtotime($sq_row['pm_IN_co']));
            $str_time_sched_pm_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_in_late);
            sscanf($str_time_sched_pm_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds_time_pm_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $pm_in_late_min=($time_seconds_time_pm_in-$time_seconds_time_pm_in_late)/60;
        
        $grandTotalpmLateMin=$grandTotalpmLateMin+$pm_in_late_min;
        
        $pmLateCtr=$pmLateCtr+1;
        $pmPresentCtr=$pmPresentCtr+1;
        
        
        $dailyLate=$dailyLate+$pm_in_late_min;
        ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo $studLogs_PM_IN_row['logTime']; ?> ]</p>
    <?php }else{ 
        $dailyLate=$dailyLate+0;
        $pmPresentCtr=$pmPresentCtr+1;
        
        ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_PM_IN_row['logTime']; ?> ]</p>
    <?php } ?>
 
    <?php } else { $time_seconds_time_pm_in = 0; ?>
    
    <?php
    
    $studLogs_query_PM_OUT_chk = getPersonnelLogs($conn, $RFTag_id, $logDateCtr, 'PM OUT');
    
    if($studLogs_query_PM_OUT_chk && $studLogs_query_PM_OUT_chk->rowCount() > 0 && $studLogs_query_AM_IN && $studLogs_query_AM_IN->rowCount() > 0){ 
        $pmPresentCtr = $pmPresentCtr + 1; 
    } else { 
        
        $pmAbsentCtr = $pmAbsentCtr + 1;
        
        ?>
    
    <p style="margin: 0px;">--:--</p>   
    
    <?php } } ?>
    
    </td>
    
    
    <!-- PM OUT -->
    <td>
    <?php
    $studLogs_query_PM_OUT = getPersonnelLogs($conn, $RFTag_id, $logDateCtr, 'PM OUT');
    $studLogs_PM_OUT_row = $studLogs_query_PM_OUT ? $studLogs_query_PM_OUT->fetch(PDO::FETCH_ASSOC) : null;
    ?>
    
    <?php
    if($studLogs_query_PM_OUT && $studLogs_query_PM_OUT->rowCount() > 0 && $studLogs_PM_OUT_row){ 
    
    $str_time_pm_out = date("H:i:s", strtotime($studLogs_PM_OUT_row['logTime']));
    $str_time_pm_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_out);
    sscanf($str_time_pm_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_out = ($hours * 3600) + $minutes * 60 + $seconds;
     
    if($studLogs_PM_OUT_row['late_status'] == "on"){
        
        $sq_row = getSchedule($conn, $school_id, $studData_row['do_id'], $studData_row['shift_id'], $dayName, 'pm_OUT');
        
        if ($sq_row) {
            $str_time_sched_pm_out_utime = date("H:i:s", strtotime($sq_row['pm_OUT']));
            $str_time_sched_pm_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_out_utime);
            sscanf($str_time_sched_pm_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds_time_pm_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
            
            $pm_out_utime_min = ($time_seconds_time_pm_out_utime - $time_seconds_time_pm_out) / 60;
            
            $grandTotalpmUTimeMin = $grandTotalpmUTimeMin + $pm_out_utime_min;
            
            $pmUTimeCtr = $pmUTimeCtr + 1;
        
        $dailyUTime=$dailyUTime+$pm_out_utime_min;
            
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo $studLogs_PM_OUT_row['logTime']; ?> ]</p>
    <?php }else{
        
    $dailyUTime=$dailyUTime+0;
    
    ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_PM_OUT_row['logTime']; ?> ]</p>
    <?php } ?>
    
    
    <!-- time in seconds PM_OUT -->
    <?php }else{ $time_seconds_time_pm_out=0; ?>
    
        <p style="margin: 0px;">--:--</p>  
         
    <?php }
    }
    ?>
    
    </td>
    
    
    <?php
    $SC_query3 = getActivityCalendar($conn, $logDateCtr, 'Add as working day');
    if($SC_query3 && $SC_query3->rowCount() > 0){
        
    ?>
    <!-- Late -->
    <td rowspan="2">
    
    <?php echo number_format($dailyLate, 2).' minute(s)'; ?>

    </td>
    <!-- end Late -->
    
    
    <!-- Undertime -->
    <td rowspan="2">
    <?php echo number_format($dailyUTime, 2).' minute(s)'; ?>
    </td>
    <!-- end Undertime -->
  <?php } else { ?>
    <!-- Late -->
    <td>
    
    <?php echo number_format($dailyLate, 2).' minute(s)'; ?>

    </td>
    <!-- end Late -->
    
    
    <!-- Undertime -->
    <td>
    <?php echo number_format($dailyUTime, 2).' minute(s)'; ?>
    </td>
    <!-- end Undertime -->
  <?php } ?>
  
    
    <?php } } } } ?>
    
  <?php
  $SC_query4 = getActivityCalendar($conn, $logDateCtr, 'Add as working day');
  
  if($SC_query4 && $SC_query4->rowCount() > 0){ ?>
  
  <?php } else { ?>
  
  <?php } ?>
  </tr>
  
  <?php
  $SC_query2 = getActivityCalendar($conn, $logDateCtr, 'Add as working day');
      
      if($SC_query2 && $SC_query2->rowCount() > 0){
      $SC_row2=$SC_query2->fetch();
  ?>
  <tr>
   
    <td colspan="4" style="background-color: #b8ffd9;"><center><small><?php echo strtoupper($SC_row2['event_title']); ?></small></center></td>
 
  </tr>
  <?php } ?>
<?php } ?>


<?php

// Calculate total lateness in minutes
$grandTotalLateMin = (int)$grandTotalamLateMin + (int)$grandTotalpmLateMin;

// Convert lateness to hours and minutes properly
$final_lateHr = floor($grandTotalLateMin / 60);
$final_lateMin = $grandTotalLateMin % 60;

// Calculate total undertime in minutes
$grandTotalUTimeMin = (int)$grandTotalamUTimeMin + (int)$grandTotalpmUTimeMin;

// Convert undertime to hours and minutes properly
$final_uTimeHr = floor($grandTotalUTimeMin / 60);
$final_uTimeMin = $grandTotalUTimeMin % 60;

?>


<tr>
<td colspan="5"><strong class="pull-right">TOTAL</strong></td>
<td style="background-color: lightgoldenrodyellow;"><strong><?php echo $grandTotalLateMin; ?> minute(s)</strong></td>
<td style="background-color: lightgoldenrodyellow;"><strong><?php echo $grandTotalUTimeMin; ?> minute(s)</strong></td>
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
       
            