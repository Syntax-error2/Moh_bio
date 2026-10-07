<!DOCTYPE html>
<html>
<?php

include('session.php');
require_once __DIR__ . '/attendance_report_bio.php';


 
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
 
                 
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
 
    $printAll_Data_query = $conn->prepare("SELECT * FROM personnels WHERE do_id = :do_id AND (separation_date = '' OR separation_date = '  /  /    ') ORDER BY lname, fname ASC");
    $printAll_Data_query->execute(['do_id' => $_GET['do_id']]);
    while($printALL_row=$printAll_Data_query->fetch()){
    
    $grandTotalamLateMin=0;
    $grandTotalpmLateMin=0;
    
    $grandTotalamUTimeMin=0;
    $grandTotalpmUTimeMin=0;
  $amLateCtr = 0;
  $amPresentCtr = 0;
  $pmLateCtr = 0;
  $pmPresentCtr = 0;
  
?>


<table style="width: 45%;">
<tr>
<td style="width: 100%; border: none; padding: 0px; text-align: left;">
<table style="width: 100%;"  >

<tr>
<td style="border: none; background-color: #fff; padding: 0px;">CIVIL SERVICE FORM No. <strong>48</strong></td>
</tr>

<tr>
<td colspan="2" style="font-size: x-large; border: none; background-color: #fff; padding: 0px; text-align: center;">DAILY TIME RECORD</td>
</tr>

<tr>
<td style="border: none; padding: 0px; text-align: center;" colspan="2">

     
    <p style="font-size: 14px; padding: 0px;">(Name)</p>
    <p style="font-size: 24px; font-variant-caps: all-petite-caps; padding: 0px;">
    <?php
    $mname=$printALL_row['mname'];
            
    if($mname=='')
    {
 
            echo $printALL_row['lname'].", ".$printALL_row['fname'];
            
    }else{
            
            $suffix=$printALL_row['suffix'];
            
            if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';
            
            echo $printALL_row['lname'].", ".$printALL_row['fname']." ".$finalMName;
            
    }
    ?></p>
</td>
</tr>

<tr>
<td style="border: none; background-color: #fff; padding: 0px;">For the month of <strong><?php echo $mmWords; ?> 16, <?php echo $selectedYYYY; ?> - <?php echo $mmWords.' '.($MMmaxDay-1).', '.$selectedYYYY; ?></strong></td>
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
    <td rowspan="2" style="width:6%; text-align: center;"><strong>DAY</strong></td>
    <td colspan="2" style="width:36%; text-align: center;"><strong>AM</strong></td>
    <td colspan="2" style="width:36%; text-align: center;"><strong>PM</strong></td>
    <td colspan="2" style="width:12%; text-align: center;"><strong><small>LATE &amp; UNDERTIME</small></strong></td>
    <td rowspan="2" style="width:10%; text-align: center;"><strong><small>OVERTIME</small></strong></td>
  </tr>
  
  <tr style="font-weight: light; font-size: 14px">
    <td style="width:18%; text-align: center;">&nbsp;&nbsp;Arrival&nbsp;&nbsp;</td>
    <td style="width:18%; text-align: center;">Departure</td>
    <td style="width:18%; text-align: center;">&nbsp;&nbsp;Arrival&nbsp;&nbsp;</td>
    <td style="width:18%; text-align: center;">Departure</td>
    <td style="width:6%; text-align: center;">Hours</td>
    <td style="width:6%; text-align: center;">Min.</td>
  </tr>
 
<?php

$RFTag_id = !empty($printALL_row['RFTag_id']) ? $printALL_row['RFTag_id'] : $printALL_row['biometric_id'];

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

    for($d=16; $d<$MMmaxDay; $d++){
        
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
        
        $is_biometric = attendance_has_bio($conn, (int)$printALL_row['personnel_id']);
        $bio_am_in = false;
        $bio_am_out = false;
        $bio_pm_in = false;
        $bio_pm_out = false;
        
        if ($is_biometric) {
            $bio_query = $conn->prepare("SELECT * FROM bio_dtr WHERE personnel_id = :pid AND log_date = :ldate ORDER BY time_in ASC");
            $bio_query->execute(['pid' => $printALL_row['personnel_id'], 'ldate' => $logDateCtr]);
            $bio_logs = $bio_query->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($bio_logs as $log) {
                if (!empty($log['time_in']) && $log['time_in'] != '00:00:00') {
                    if ($log['time_in'] < '12:00:00' && !$bio_am_in) {
                        $bio_am_in = ['logTime' => date('h:i A', strtotime($log['time_in'])), 'late_status' => 'on'];
                    } else if ($log['time_in'] >= '12:00:00' && !$bio_pm_in) {
                        $bio_pm_in = ['logTime' => date('h:i A', strtotime($log['time_in'])), 'late_status' => 'on'];
                    }
                }
                if (!empty($log['time_out']) && $log['time_out'] != '00:00:00') {
                    if ($log['time_out'] < '13:00:00' && !$bio_am_out) {
                        $bio_am_out = ['logTime' => date('h:i A', strtotime($log['time_out'])), 'late_status' => 'on'];
                    } else if ($log['time_out'] >= '13:00:00' && !$bio_pm_out) {
                        $bio_pm_out = ['logTime' => date('h:i A', strtotime($log['time_out'])), 'late_status' => 'on'];
                    }
                }
            }
        }
        
    ?>
    
  <tr>
 
    
 
    <?php
    $SC_query3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = :status");
    $SC_query3->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
    ?>
    
    <td <?php if($SC_query3->rowCount()>0){?> rowspan="2" <?php } ?> style="text-align: center;">
    
    <?php
    
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    //echo $d_str;
    
    ?>
    </td>
  
 
    
    <?php
    
    $studLogs_remarks_query = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '') AND (remarks!='' AND remarks!='Updated' AND remarks!='Inserted' AND remarks!='24hrs')");
    $studLogs_remarks_query->execute(['RFTag_id' => $RFTag_id, 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    if($studLogs_remarks_query->rowCount()>0){
    $SRQ_row=$studLogs_remarks_query->fetch(); ?> 
    
    <td colspan="6" style="background-color: #b8ffd9; text-align: center;">
    <strong><?php echo $SRQ_row['remarks']; ?></strong>
    </td>
    
      <?php }else{
        
    $studLogs_sat_query = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '')");
    $studLogs_sat_query->execute(['RFTag_id' => $RFTag_id, 'logDate' => $logDateLog, 'logDateCtr' => $logDateCtr]);
    if($studLogs_sat_query->rowCount()==0 AND ($dayName2=='Sat' OR $dayName2=='Sun')){ ?> 
    <td colspan="6" style="background-color: #ececec; text-align: center;">
    <strong>
    <?php if($dayName2=='Sat'){ echo "S A T U R D A Y"; } if($dayName2=='Sun'){ echo "S U N D A Y"; }?>
    </strong>
    </td>
     
      <?php }else{
     
      $SC_query = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != :status");
      $SC_query->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
      
      if($SC_query->rowCount()>0){
      $SC_row=$SC_query->fetch();
      ?>
        
      <td colspan="6" style="background-color: #ffbac5; text-align: center;">
      <strong><?php echo $SC_row['event_title'].'</strong> [ '.$SC_row['act_type'].' ]'; ?></strong>
      </td>
       
      <?php }else{ ?> 
 
    
    
    <!-- AM IN -->
    <td>
    <?php
    $studLogs_query_AM_IN = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '')");
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
    if($studLogs_AM_IN_row['late_status']=="on"){
        
        $sched_query = $conn->prepare("SELECT am_IN FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_query->execute(['do_id' => $printALL_row['do_id'], 'shift_id' => $printALL_row['shift_id'], 'day' => $dayName]);
        $sq_row=$sched_query->fetch();
 
        $str_time_sched_am_in_late= date("H:i:s", strtotime($sq_row['am_IN']));
        $str_time_sched_am_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_in_late);
        sscanf($str_time_sched_am_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_am_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $am_in_late_min=(($time_seconds_time_am_in-$time_seconds_time_am_in_late)/60);
        
        if ($am_in_late_min <= 15) {
            $am_in_late_min = 0;
            $dailyLate+=$am_in_late_min;
            $amPresentCtr=$amPresentCtr+1;
            ?>
            <p style="background-color: white; margin: 0px;">&nbsp;<?php echo date("H:i", strtotime($studLogs_AM_IN_row['logTime'])); ?></p>
            <?php
        } else {
            $grandTotalamLateMin+=$am_in_late_min;
            $dailyLate+=$am_in_late_min;
            $amLateCtr=$amLateCtr+1;
            $amPresentCtr=$amPresentCtr+1;
            ?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo date("H:i", strtotime($studLogs_AM_IN_row['logTime'])); ?></p>
        <?php } ?>
    <?php }else{ 
        
        $dailyLate=$dailyLate+0;
        
        ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo substr($studLogs_AM_IN_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_AM_IN_row['logTime'], -2); ?></sup></p>
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
    $studLogs_query_AM_OUT = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '')");
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
    if($studLogs_AM_OUT_row['late_status'] == "on"){
        
        $sched_query = $conn->prepare("SELECT am_OUT FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_query->execute(['do_id' => $printALL_row['do_id'], 'shift_id' => $printALL_row['shift_id'], 'day' => $dayName]);
        $sq_row=$sched_query->fetch();
        
        $str_time_sched_am_out_utime= date("H:i:s", strtotime($sq_row['am_OUT']));
        $str_time_sched_am_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_out_utime);
        sscanf($str_time_sched_am_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_am_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $am_out_utime_min=($time_seconds_time_am_out_utime-$time_seconds_time_am_out)/60;
        
        $grandTotalamUTimeMin=$grandTotalamUTimeMin+$am_out_utime_min;
 
        $dailyUTime=$dailyUTime+$am_out_utime_min;
        
    
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo substr($studLogs_AM_OUT_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_AM_OUT_row['logTime'], -2); ?></sup></p>
    <?php }else{
        
        $dailyUTime=$dailyUTime+0;
    
    ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo substr($studLogs_AM_OUT_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_AM_OUT_row['logTime'], -2); ?></sup></p>
    <?php } ?>

    <!-- time in seconds AM_OUT -->
 
    <?php }else{ $time_seconds_time_am_out=0; 
    
    $studLogs_query_PM_OUT_chk = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '')");
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
    if(!$has_am_in AND !$has_am_out){
        
        $grandTotalamUTimeMin=$grandTotalamUTimeMin+240;
        $dailyUTime=$dailyUTime+240;
        
    } ?>
    
    
    <!-- PM IN -->
    <td>
    <?php
    $studLogs_query_PM_IN = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '')");
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
    if($studLogs_PM_IN_row['late_status'] == "on"){
        
        $sched_query = $conn->prepare("SELECT pm_IN FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_query->execute(['do_id' => $printALL_row['do_id'], 'shift_id' => $printALL_row['shift_id'], 'day' => $dayName]);
        $sq_row=$sched_query->fetch();
 
        $str_time_sched_pm_in_late= date("H:i:s", strtotime($sq_row['pm_IN']));
        $str_time_sched_pm_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_in_late);
        sscanf($str_time_sched_pm_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $pm_in_late_min=(($time_seconds_time_pm_in-$time_seconds_time_pm_in_late)/60);
        
        if ($pm_in_late_min <= 15) {
            $pm_in_late_min = 0;
            $dailyLate+=$pm_in_late_min;
            $pmPresentCtr=$pmPresentCtr+1;
            ?>
            <p style="background-color: white; margin: 0px;">&nbsp;<?php echo date("H:i", strtotime($studLogs_PM_IN_row['logTime'])); ?></p>
            <?php
        } else {
            $grandTotalpmLateMin+=$pm_in_late_min;
            $dailyLate+=$pm_in_late_min; 
            $pmLateCtr=$pmLateCtr+1;
            $pmPresentCtr=$pmPresentCtr+1;?>
            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo date("H:i", strtotime($studLogs_PM_IN_row['logTime'])); ?></p>
        <?php } ?>
    <?php }else{ 
        
        $dailyLate=$dailyLate+0; 
        
        ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo substr($studLogs_PM_IN_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_PM_IN_row['logTime'], -2); ?></sup></p>
    <?php } ?>
 
    <?php }else{ $time_seconds_time_pm_in=0; ?>
    
    <?php
    
    $studLogs_query_PM_OUT_chk = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '')");
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
    $studLogs_query_PM_OUT = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = :logFlow AND (logDate = :logDate OR logDate = :logDateCtr) AND (captured_img != '' OR logDate LIKE '%-%' OR client_ip = '')");
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
        
    // Always fetch schedule to calculate Overtime even if not 'on' for undertime
    $sched_query = $conn->prepare("SELECT pm_OUT FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
    $sched_query->execute(['do_id' => $printALL_row['do_id'], 'shift_id' => $printALL_row['shift_id'], 'day' => $dayName]);
    $sq_row=$sched_query->fetch();
    
    $str_time_sched_pm_out_utime= date("H:i:s", strtotime($sq_row['pm_OUT']));
    $str_time_sched_pm_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_out_utime);
    sscanf($str_time_sched_pm_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
    
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
        
    if($studLogs_PM_OUT_row['late_status'] == "on"){
        $pm_out_utime_min=($time_seconds_time_pm_out_utime-$time_seconds_time_pm_out)/60;
        $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+$pm_out_utime_min;
        $dailyUTime=$dailyUTime+$pm_out_utime_min;
    
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo substr($studLogs_PM_OUT_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_PM_OUT_row['logTime'], -2); ?></sup></p>
    <?php }else{
        $dailyUTime=$dailyUTime+0;
    ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo substr($studLogs_PM_OUT_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_PM_OUT_row['logTime'], -2); ?></sup></p>
    <?php } ?>
    
 
    <?php }else{ $time_seconds_time_pm_out=0; ?>
        <p style="margin: 0px;">--:--</p>
    <?php } ?>
    
    </td>
    
    <?php
    //ADD PM NO LOGS | 4 HOURS AS ABSENT
    if(!$has_pm_in AND !$has_pm_out){
        
        $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+240;
        $dailyUTime=$dailyUTime+240;
        
    }
    
    $SC_query3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = :status");
    $SC_query3->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
    if($SC_query3->rowCount()>0){
    ?>
    
    <td rowspan="2"><?php echo $dailyFinalHR=substr(($dailyLate+$dailyUTime)/60, 0, 1); ?></td>
    
    <td rowspan="2"><?php echo ($dailyLate+$dailyUTime)-($dailyFinalHR*60); ?></td>
    
    <td rowspan="2"><?php echo $dailyOvertime; ?></td>
  <?php }else{?>
    <td><?php echo $dailyFinalHR=substr(($dailyLate+$dailyUTime)/60, 0, 1); ?></td>
    
    <td><?php echo ($dailyLate+$dailyUTime)-($dailyFinalHR*60); ?></td>
    
    <td><?php echo $dailyOvertime; ?></td>
    <?php } ?>
  
    
    
   
    <?php } } } ?>
    
 
  
  
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

$grandTotalLateMin=$grandTotalamLateMin+$grandTotalpmLateMin;
$grandTotalUTimeMin=$grandTotalamUTimeMin+$grandTotalpmUTimeMin;

$grandTotalLateUnderTime_hr=($grandTotalLateMin+$grandTotalUTimeMin)/60;
$grandTotalLateUnderTime_min=$grandTotalLateMin+$grandTotalUTimeMin;

if($grandTotalLateUnderTime_hr<9){
    
    $final_hr=substr($grandTotalLateUnderTime_hr, 0, 1);
    
    if($grandTotalLateUnderTime_hr>1){
        $format_final_hr=substr($grandTotalLateUnderTime_hr, 0, 1)." hrs";
    }else{
        $format_final_hr=substr($grandTotalLateUnderTime_hr, 0, 1)." hr";
    }
    
}elseif($grandTotalLateUnderTime_hr < 99 AND $grandTotalLateUnderTime_hr > 9){
    
    $final_hr=substr($grandTotalLateUnderTime_hr, 0, 2);
    $format_final_hr=substr($grandTotalLateUnderTime_hr, 0, 2)." hrs";
    
}elseif($grandTotalLateUnderTime_hr < 999 AND $grandTotalLateUnderTime_hr > 99){
    
    $final_hr=substr($grandTotalLateUnderTime_hr, 0, 3);
    $format_final_hr=substr($grandTotalLateUnderTime_hr, 0, 3)." hrs";
    
}


$grandTotalLateUnderTime_min = isset($grandTotalLateUnderTime_min) ? (float)$grandTotalLateUnderTime_min : 0;
$final_hr = isset($final_hr) ? (float)$final_hr : 0;
$final_min = $grandTotalLateUnderTime_min - ($final_hr * 60);

?>

<tr>

<td colspan="4" style="padding: 8px 8px 8px 8px; text-align: right;">
<strong style="font-size: larger; "> TOTAL LATE &amp; UNDERTIME:</strong>
</td>
 
<td style="background-color: lightgoldenrodyellow;"><strong><?php echo $format_final_hr; ?></strong></td>
<td style="background-color: lightgoldenrodyellow;"><strong><?php if($final_min>1){ echo $final_min." mins."; }else{ echo $final_min." min."; }  ?></strong></td>

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
    $mname=$printALL_row['mname'];
            
    if($mname=='')
    {
 
            echo $printALL_row['lname'].", ".$printALL_row['fname'];
            
    }else{
            
            $suffix=$printALL_row['suffix'];
            
            if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';
            
            echo $printALL_row['lname'].", ".$printALL_row['fname']." ".$finalMName;
            
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
    $dept_off_query->execute(['do_id' => $printALL_row['do_id']]);
    $do_row = $dept_off_query->fetch(); 
    
    
    $officeHead_query = $conn->prepare("SELECT lname, fname, mname, suffix FROM personnels WHERE personnel_id = :personnel_id");
    $officeHead_query->execute(['personnel_id' => ($do_row['officeHead_id'] ?? 0)]);
    $oh_row=$officeHead_query->fetch();
    if ($oh_row) {
        if($oh_row['suffix']=="-") {
            echo $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname'];
        } else {
            echo $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname']." ".$oh_row['suffix'];
        }
    }
                                    
                                    
    ?>
  
  </p>
  <br /> <br /> 
  <i style="text-decoration-line: none !important; font-variant-caps: normal !important; float: right;">In-charge</i> 
  
</td>
</tr>
 
 
</table>


<?php } $conn=null; ?>

<?php include_once('universal_excel_export.php'); ?>
</body>
</html>
            

