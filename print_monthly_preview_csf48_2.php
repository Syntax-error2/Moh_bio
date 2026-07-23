<!DOCTYPE html>
<html>

<?php

include('session.php');  
//error_reporting(0);

include('dbcon.php');

 
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
  
            $classData="<strong>Class - Type:</strong> ";
        
include('header_print.php');

?>
 
<body>
 
<?php
 
$hours=0;
$minutes=0;
$seconds=0;
$printAll_Data_stmt = $conn->prepare("SELECT * FROM personnels WHERE do_id = :do_id AND (separation_date IS NULL) ORDER BY lname, fname ASC");
$printAll_Data_stmt->execute([':do_id' => $_GET['do_id'] ?? '']);
$printAll_Data_query = $printAll_Data_stmt;
while($printALL_row=$printAll_Data_query->fetch()){


$studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id AND do_id = :do_id");
$studData_stmt->execute([':RFTag_id' => $printALL_row['RFTag_id'], ':do_id' => $_GET['do_id'] ?? '']);
$studData_query = $studData_stmt;
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
    
    <td rowspan="2" style="width:6%;"><center><strong>DAY</strong></center></td>
    <td colspan="2" style="width:42%;"><center><strong>AM</strong></center></td>
    <td colspan="2" style="width:42%;"><center><strong>PM</strong></center></td>
    <td colspan="2" style="width:10%;"><center><strong><small>LATE &amp; UNDERTIME</small></strong></center></td>
    
  </tr>
  
  <tr style="font-weight: light; font-size: 14px">
    
     
    <td style="width:16%;"><center>&nbsp;&nbsp;Arrival&nbsp;&nbsp;</center></td>
    <td style="width:16%;"><center>Departure</center></td>
    <td style="width:16%;"><center>&nbsp;&nbsp;Arrival&nbsp;&nbsp;</center></td>
    <td style="width:16%;"><center>Departure</center></td>
    <td style="width:8%;"><center>Hours</center></td>
    <td style="width:8%;"><center>Min.</center></td>
  </tr>
 
<?php
 
    $RFTag_id=$studData_row['RFTag_id'];
 
    
    
    for($d=1; $d<$MMmaxDay; $d++){
        
        $dailyLate=0;
        $dailyUTime=0;
        
        if($d<10){
        $logDateCtr=$selectedMM.'/0'.$d.'/'.$selectedYYYY;
        }else{
        $logDateCtr=$selectedMM.'/'.$d.'/'.$selectedYYYY;
        }
 
    ?>
    
  <tr>
 
    
 
     <?php
    $SC_stmt3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
    $SC_stmt3->execute([':completeDate' => $logDateCtr]);
    $SC_query3 = $SC_stmt3;
      
      
      
  ?>
  <td <?php if($SC_query3->rowCount()>0){?> rowspan="2" <?php } ?> >
    <?php
    
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    echo substr($logDateCtr, 3, 2);
    
    ?>
    </td>
  
 
    
    <?php
    
    $studLogs_remarks_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (remarks != '' AND remarks != 'Updated' AND remarks != 'Inserted' AND remarks != '24hrs')");
    $studLogs_remarks_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_remarks_query = $studLogs_remarks_stmt;
    if($studLogs_remarks_query->rowCount()>0){ 
    $SRQ_row=$studLogs_remarks_query->fetch(); ?> 
    
    <td colspan="6" style="background-color: #b8ffd9;">
    <center><strong><?php echo $SRQ_row['remarks']; ?></strong></center>
    </td>
    
      <?php }else{
        
    $studLogs_sat_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
    $studLogs_sat_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_sat_query = $studLogs_sat_stmt;
    if($studLogs_sat_query->rowCount()==0 AND ($dayName2=='Sat' OR $dayName2=='Sun')){ ?> 
    <td colspan="6" style="background-color: #ececec;">
    <center><strong>
    <?php if($dayName2=='Sat'){ echo "S A T U R D A Y"; } if($dayName2=='Sun'){ echo "S U N D A Y"; }?>
    </strong></center>
    </td>
     
      <?php }else{
     
    $SC_stmt = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != 'Display to DTR'");
    $SC_stmt->execute([':completeDate' => $logDateCtr]);
    $SC_query = $SC_stmt;
      
      if($SC_query->rowCount()>0){
      $SC_row=$SC_query->fetch();
      ?>
        
      <td colspan="6" style="background-color: #ffbac5;">
      <center><strong><?php echo $SC_row['event_title'].'</strong> [ '.$SC_row['act_type'].' ]'; ?></strong></center>
      </td>
       
      <?php }else{ ?> 
 
    
    
    <!-- AM IN -->
    <td>
    <?php
    $studLogs_AM_IN_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'AM IN' AND logDate = :logDate");
    $studLogs_AM_IN_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_query_AM_IN = $studLogs_AM_IN_stmt;
    $studLogs_AM_IN_row=$studLogs_query_AM_IN->fetch();
    ?>
    
    <?php
    if($studLogs_query_AM_IN->rowCount()>0){
    
    $str_time_am_in= date("H:i:s", strtotime($studLogs_AM_IN_row['logTime']));
    $str_time_am_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_in);
    sscanf($str_time_am_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_in = ($hours * 3600) + $minutes * 60 + $seconds;
        
    ?>
    
    
    <?php
    if($studLogs_AM_IN_row['late_status']==='on'){
        
        
        $sched_stmt = $conn->prepare("SELECT am_IN_co FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
 
        $str_time_sched_am_in_late= date("H:i:s", strtotime($sq_row['am_IN_co']));
        $str_time_sched_am_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_in_late);
        sscanf($str_time_sched_am_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_am_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $am_in_late_min=(($time_seconds_time_am_in-$time_seconds_time_am_in_late)/60)+15;
        
        $grandTotalamLateMin+=$am_in_late_min;
        
        $dailyLate+=$am_in_late_min;
        
        ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo substr($studLogs_AM_IN_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_AM_IN_row['logTime'], -2); ?></sup></p>
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
    $studLogs_AM_OUT_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'AM OUT' AND logDate = :logDate");
    $studLogs_AM_OUT_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_query_AM_OUT = $studLogs_AM_OUT_stmt;
    $studLogs_AM_OUT_row=$studLogs_query_AM_OUT->fetch();
    ?>
    
    
    <?php
    if($studLogs_query_AM_OUT->rowCount()>0){ 
        
    $str_time_am_out= date("H:i:s", strtotime($studLogs_AM_OUT_row['logTime']));
    $str_time_am_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_out);
    sscanf($str_time_am_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_am_out = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
     
    <?php
    if($studLogs_AM_OUT_row['late_status']==='on'){
        
        $sched_stmt = $conn->prepare("SELECT am_OUT FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
        
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
    
    $studLogs_PM_OUT_chk_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM OUT' AND logDate = :logDate");
    $studLogs_PM_OUT_chk_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_query_PM_OUT_chk = $studLogs_PM_OUT_chk_stmt;
    
    if($studLogs_query_PM_OUT_chk->rowCount()>0 AND $studLogs_query_AM_IN->rowCount()>0){ }else{ ?>
    
    <p style="margin: 0px;">--:--</p>   
    
    <?php } } ?>
    
    
    </td>
    
    <?php
    //ADD AM NO LOGS | 4 HOURS AS ABSENT
    if($studLogs_query_AM_IN->rowCount()<=0 AND $studLogs_query_AM_OUT->rowCount()<=0){
        
        $grandTotalamUTimeMin=$grandTotalamUTimeMin+240;
        $dailyUTime=$dailyUTime+240;
        
    } ?>
    
    
    <!-- PM IN -->
    <td>
    <?php
    $studLogs_PM_IN_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM IN' AND logDate = :logDate");
    $studLogs_PM_IN_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_query_PM_IN = $studLogs_PM_IN_stmt;
    $studLogs_PM_IN_row=$studLogs_query_PM_IN->fetch();
    ?>
    
    <?php
    if($studLogs_query_PM_IN->rowCount()>0){ ?>
    
    <!-- time in seconds PM_IN -->
    <?php
    
    $str_time_pm_in= date("H:i:s", strtotime($studLogs_PM_IN_row['logTime']));
    $str_time_pm_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_in);
    sscanf($str_time_pm_in, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_in = ($hours * 3600) + $minutes * 60 + $seconds;
    
    ?>
    
    <?php
    if($studLogs_PM_IN_row['late_status']==='on'){
        
        $sched_stmt = $conn->prepare("SELECT pm_IN_co FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
 
        $str_time_sched_pm_in_late= date("H:i:s", strtotime($sq_row['pm_IN_co']));
        $str_time_sched_pm_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_in_late);
        sscanf($str_time_sched_pm_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $pm_in_late_min=(($time_seconds_time_pm_in-$time_seconds_time_pm_in_late)/60)+15;
        
        $grandTotalpmLateMin+=$pm_in_late_min;
        
        $dailyLate+=$pm_in_late_min; 
     
        
        ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<?php echo substr($studLogs_PM_IN_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_PM_IN_row['logTime'], -2); ?></sup></p>
    <?php }else{ 
        
        $dailyLate=$dailyLate+0; 
        
        ?>
        <p style="background-color: white; margin: 0px;">&nbsp;<?php echo substr($studLogs_PM_IN_row['logTime'], 0, 5); ?> <sup><?php echo substr($studLogs_PM_IN_row['logTime'], -2); ?></sup></p>
    <?php } ?>
 
    <?php }else{ $time_seconds_time_pm_in=0; ?>
    
    <?php
    
    $studLogs_PM_OUT_chk_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM OUT' AND logDate = :logDate");
    $studLogs_PM_OUT_chk_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_query_PM_OUT_chk = $studLogs_PM_OUT_chk_stmt;
    
    if($studLogs_query_PM_OUT_chk->rowCount()>0 AND $studLogs_query_AM_IN->rowCount()>0){ }else{ ?>
    
    <p style="margin: 0px;">--:--</p> 
    
    <?php } } ?>
    
    </td>
    
    
    <!-- PM OUT -->
    <td>
    <?php
    $studLogs_PM_OUT_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM OUT' AND logDate = :logDate");
    $studLogs_PM_OUT_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_query_PM_OUT = $studLogs_PM_OUT_stmt;
    $studLogs_PM_OUT_row=$studLogs_query_PM_OUT->fetch();
    ?>
    
    <?php
    if($studLogs_query_PM_OUT->rowCount()>0){ 
    
    $str_time_pm_out= date("H:i:s", strtotime($studLogs_PM_OUT_row['logTime']));
    $str_time_pm_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_out);
    sscanf($str_time_pm_out, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_time_pm_out = ($hours * 3600) + $minutes * 60 + $seconds;
        
    if($studLogs_PM_OUT_row['late_status']=="on"){

        $sched_stmt = $conn->prepare("SELECT pm_OUT FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
        
        $str_time_sched_pm_out_utime= date("H:i:s", strtotime($sq_row['pm_OUT']));
        $str_time_sched_pm_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_out_utime);
        sscanf($str_time_sched_pm_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
        
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
    if($studLogs_query_PM_IN->rowCount()<=0 AND $studLogs_query_PM_OUT->rowCount()<=0){
        
        $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+240;
        $dailyUTime=$dailyUTime+240;
        
    } ?>
    
    <?php
    $SC_stmt3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
    $SC_stmt3->execute([':completeDate' => $logDateCtr]);
    $SC_query3 = $SC_stmt3;
    if($SC_query3->rowCount()>0){
        
    ?>
    <td rowspan="2"><?php echo $dailyFinalHR=substr(($dailyLate+$dailyUTime)/60, 0, 1); ?></td>
    
    <td rowspan="2"><?php echo ($dailyLate+$dailyUTime)-($dailyFinalHR*60); ?></td>
  <?php }else{?>
    <td><?php echo $dailyFinalHR=substr(($dailyLate+$dailyUTime)/60, 0, 1); ?></td>
    
    <td><?php echo ($dailyLate+$dailyUTime)-($dailyFinalHR*60); ?></td>
    <?php } ?>
  
    
    
   
    <?php } } } ?>
    
 
  
  
  <?php
    $SC_stmt4 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
    $SC_stmt4->execute([':completeDate' => $logDateCtr]);
    $SC_query4 = $SC_stmt4;
      
      if($SC_query4->rowCount()>0){
     
  ?>
 
     
  <?php }else{ ?>
 
  <?php } ?>
  </tr>
  
  <?php
    $SC_stmt2 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
    $SC_stmt2->execute([':completeDate' => $logDateCtr]);
    $SC_query2 = $SC_stmt2;
      
      if($SC_query2->rowCount()>0){
      $SC_row2=$SC_query2->fetch();
  ?>
  <tr>
   
    <td colspan="4" style="background-color: #b8ffd9;"><center><small><?php echo strtoupper($SC_row2['event_title']); ?></small></center></td>
 
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


$final_min=$grandTotalLateUnderTime_min-($final_hr*60);

?>

<tr>

<td colspan="4" style="padding: 8px 8px 8px 8px; text-align: right;">
<strong style="font-size: larger; "> TOTAL LATE &amp; UNDERTIME:</strong>
</td>
 
<td style="background-color: lightgoldenrodyellow;"><strong><?php echo $format_final_hr; ?></strong></td>
<td colspan="2" style="background-color: lightgoldenrodyellow;"><strong><?php if($final_min>1){ echo $final_min." mins."; }else{ echo $final_min." min."; }  ?></strong></td>

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
  
    $adminData_stmt = $conn->prepare("SELECT do_id FROM personnels WHERE RFTag_id = :RFTag_id");
    $adminData_stmt->execute([':RFTag_id' => $printALL_row['RFTag_id']]);
    $adminData_row=$adminData_stmt->fetch();
    
    
    $dept_off_stmt = $conn->prepare("SELECT officeHead_id FROM dept_offices WHERE do_id = :do_id");
    $dept_off_stmt->execute([':do_id' => $adminData_row['do_id']]);
    $do_row = $dept_off_stmt->fetch(); 
    
    
    $officeHead_stmt = $conn->prepare("SELECT lname, fname, mname, suffix FROM personnels WHERE personnel_id = :personnel_id");
    $officeHead_stmt->execute([':personnel_id' => $do_row['officeHead_id']]);
    $oh_row=$officeHead_stmt->fetch();
                 
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

<h1 class="pb"></h1>

<?php } $conn=null; ?>
 
</body>
</html>
       
            