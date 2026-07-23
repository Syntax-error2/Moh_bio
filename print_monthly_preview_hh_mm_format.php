<!DOCTYPE html>
<html>

<?php
include('session.php');  
//error_reporting(0);

include('dbcon.php');

  $get_RFTag_id=$_GET['RFTag_id'];
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
  $grandTotalTRHr=0;
  $grandTotalTRMin=0;
  
  $grandTotalamLateMin=0;
  $grandTotalpmLateMin=0;
  
  $grandTotalamUTimeMin=0;
  $grandTotalpmUTimeMin=0;

$hours=0;
$minutes=0;
$seconds=0;
 
                 
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
 
<table style="width: 100%;">
<tr>
<td align="left" style="width: 100%; border: none;">
<?php include('header_print_letterHead.php'); ?>
</td>
 
</tr>
</table>


<?php
$studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id");
$studData_stmt->execute([':RFTag_id' => $get_RFTag_id]);
$studData_query = $studData_stmt;
$studData_row=$studData_query->fetch();



?>

<br />


<table style="width: 100%;">

  <tr style="font-size: large;" style="border: none;">
    
    
    <td style="width: 40%; border: none;" colspan="2">
    <strong><?php
    $emp_stat_stmt = $conn->prepare("SELECT * FROM emp_status WHERE empStat_id = :empStat_id");
    $emp_stat_stmt->execute([':empStat_id' => $studData_row['empStat_id']]);
    $emp_stat_query = $emp_stat_stmt;
    $es_row=$emp_stat_query->fetch();
    echo strtoupper($es_row['emp_stat_name']);?></strong><br />
    <small>Employment Status</small>
    </td>
    
    <td style="width: 40%; border: none;" colspan="2">
    <strong><?php
    $dept_off_stmt = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
    $dept_off_stmt->execute([':do_id' => $studData_row['do_id']]);
    $emp_stat_query = $dept_off_stmt;
    $es_row=$emp_stat_query->fetch();
    echo strtoupper($es_row['dept_office_name']); ?></strong><br />
    <small>Department / Office</small>
    </td>
     
  </tr>
  
  <tr>
  <td style="width: 70%; border: none;" colspan="2">
    
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
    ?></strong><br />
    <small>Employee</small>
   
  </td>
  
  <td style="width: 30%; border: none;">
  <strong><?php echo strtoupper($mmWords); ?></strong><br />
  <small>Month Covered</small>
  
  </td>
  </tr>
</table>
 

<table id="myTable">

  <tr style="font-weight: light; font-size: 14px">
    
    <td style="width:8%;"><center><strong>DATE</strong></center></td>
    
    <td style="width:16%;"><center><strong>AM IN</strong></center></td>
    <td style="width:16%;"><center><strong>AM OUT</strong></center></td>
    <td style="width:16%;"><center><strong>PM IN</strong></center></td>
    <td style="width:16%;"><center><strong>PM OUT</strong></center></td>
    <td style="width:6%;"><center><strong>Time Rendered</strong></center></td>
    <td style="width:8%;"><center><strong>OT IN</strong></center></td>
    <td style="width:8%;"><center><strong>OT OUT</strong></center></td>
    <td style="width:6%;"><center><strong>OT Time Rendered</strong></center></td>
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
    
    
    for($d=1; $d<$MMmaxDay; $d++){
        
        
        
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
      
      if($SC_query3->rowCount()>0){
      
  ?>
  <td rowspan="2">
    <?php
    
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    echo substr($logDateCtr, 0, 6).substr($logDateCtr, 8, 2)." <sup>".$dayName2."</sup>";
    
    ?>
    </td>
  <?php }else{?>
  <td>
    <?php
    
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    echo substr($logDateCtr, 0, 6).substr($logDateCtr, 8, 2)." <sup>".$dayName2."</sup>";
    
    ?>
    </td>
  <?php } ?>
  
     
    
    
    <?php
    
    $studLogs_remarks_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (remarks!='' AND remarks!='Updated' AND remarks!='Inserted')");
    $studLogs_remarks_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_remarks_query = $studLogs_remarks_stmt;
    if($studLogs_remarks_query->rowCount()>0){ 
    $SRQ_row=$studLogs_remarks_query->fetch(); ?> 
    <td colspan="5" style="background-color: #b8ffd9;"><center><strong><?php echo $SRQ_row['remarks']; ?></strong></center></td>
     
      <?php }else{
        
    $studLogs_sat_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
    $studLogs_sat_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_sat_query = $studLogs_sat_stmt;
    if($studLogs_sat_query->rowCount()==0 AND ($dayName2=='Sat' OR $dayName2=='Sun')){ ?> 
    
    <td colspan="5" style="background-color: #ececec;"><center><strong><?php if($dayName2=='Sat'){ echo "S A T U R D A Y"; } if($dayName2=='Sun'){ echo "S U N D A Y"; } ?></strong></center></td>
     
      <?php }else{
     
    $SC_stmt = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != 'Display to DTR'");
    $SC_stmt->execute([':completeDate' => $logDateCtr]);
    $SC_query = $SC_stmt;
      
      if($SC_query->rowCount()>0){
      $SC_row=$SC_query->fetch();
      ?>
        
      <td colspan="5" style="background-color: #ffbac5;"><center><strong><?php echo $SC_row['event_title'].'</strong> [ '.$SC_row['act_type'].' ]'; ?></strong></center></td>
      
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
        
        $sched_stmt = $conn->prepare("SELECT am_IN_co FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':school_id' => $school_id, ':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
 
        $str_time_sched_am_in_late= date("H:i:s", strtotime($sq_row['am_IN_co']));
        $str_time_sched_am_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_in_late);
        sscanf($str_time_sched_am_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_am_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $am_in_late_min=($time_seconds_time_am_in-$time_seconds_time_am_in_late)/60;
        
        $grandTotalamLateMin=$grandTotalamLateMin+$am_in_late_min;
        
        $amLateCtr=$amLateCtr+1;
        $amPresentCtr=$amPresentCtr+1;
        
        ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo $studLogs_AM_IN_row['logTime']; ?> ]</p>
    <?php }else{ 
        
        $amPresentCtr=$amPresentCtr+1;
        
        ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_AM_IN_row['logTime']; ?> ]</p>
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
        
        $sched_stmt = $conn->prepare("SELECT am_OUT FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':school_id' => $school_id, ':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
        
        $str_time_sched_am_out_utime= date("H:i:s", strtotime($sq_row['am_OUT']));
        $str_time_sched_am_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_out_utime);
        sscanf($str_time_sched_am_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_am_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $am_out_utime_min=($time_seconds_time_am_out_utime-$time_seconds_time_am_out)/60;
        
        $grandTotalamUTimeMin=$grandTotalamUTimeMin+$am_out_utime_min;
        
        $amUTimeCtr=$amUTimeCtr+1;
            
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo $studLogs_AM_OUT_row['logTime']; ?> ]</p>
    <?php }else{ ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_AM_OUT_row['logTime']; ?> ]</p>
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
        
        $sched_stmt = $conn->prepare("SELECT pm_IN_co FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':school_id' => $school_id, ':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
 
        $str_time_sched_pm_in_late= date("H:i:s", strtotime($sq_row['pm_IN_co']));
        $str_time_sched_pm_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_in_late);
        sscanf($str_time_sched_pm_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $pm_in_late_min=($time_seconds_time_pm_in-$time_seconds_time_pm_in_late)/60;
        
        $grandTotalpmLateMin=$grandTotalpmLateMin+$pm_in_late_min;
        
        $pmLateCtr=$pmLateCtr+1;
        $pmPresentCtr=$pmPresentCtr+1;
        
        ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo $studLogs_PM_IN_row['logTime']; ?> ]</p>
    <?php }else{ 
        
        $pmPresentCtr=$pmPresentCtr+1;
        
        ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_PM_IN_row['logTime']; ?> ]</p>
    <?php } ?>
 
    <?php }else{ $time_seconds_time_pm_in=0; ?>
    
    <?php
    
    $studLogs_PM_OUT_chk_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM OUT' AND logDate = :logDate");
    $studLogs_PM_OUT_chk_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
    $studLogs_query_PM_OUT_chk = $studLogs_PM_OUT_chk_stmt;
    
    if($studLogs_query_PM_OUT_chk->rowCount()>0 AND $studLogs_query_AM_IN->rowCount()>0){ $pmPresentCtr=$pmPresentCtr+1; }else{ 
        
        $pmAbsentCtr=$pmAbsentCtr+1;
        
        ?>
    
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
        
        $sched_stmt = $conn->prepare("SELECT pm_OUT FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
        $sched_stmt->execute([':school_id' => $school_id, ':do_id' => $studData_row['do_id'], ':shift_id' => $studData_row['shift_id'], ':day' => $dayName]);
        $sq_row=$sched_stmt->fetch();
        
        $str_time_sched_pm_out_utime= date("H:i:s", strtotime($sq_row['pm_OUT']));
        $str_time_sched_pm_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_out_utime);
        sscanf($str_time_sched_pm_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds_time_pm_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
        
        $pm_out_utime_min=($time_seconds_time_pm_out_utime-$time_seconds_time_pm_out)/60;
        
        $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+$pm_out_utime_min;
        
        $pmUTimeCtr=$pmUTimeCtr+1;
            
    ?>
        <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo $studLogs_PM_OUT_row['logTime']; ?> ]</p>
    <?php }else{ ?>
        <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_PM_OUT_row['logTime']; ?> ]</p>
    <?php } ?>
    
    
    <!-- time in seconds PM_OUT -->
    <?php }else{ $time_seconds_time_pm_out=0; ?>
    
        <p style="margin: 0px;">--:--</p>  
         
    <?php } ?>
    
    </td>
    
    <td>
    
    
    <?php
    
   
    $finalAM=(($time_seconds_time_am_out-$time_seconds_time_am_in)/60)/60;    
  
    $finalPM=(($time_seconds_time_pm_out-$time_seconds_time_pm_in)/60)/60;  
   
    
    
    
    
            $finalTimeRendered=$finalAM+$finalPM;
    
    
            $trHr=number_format((float)$finalTimeRendered, 2, ':', '');
            $trMin=(substr(number_format((float)$finalTimeRendered, 2, ':', ''), -2,2)/100)*60;
            
            if($finalTimeRendered>=10){
                $trHrFinal=substr($trHr, 0,2);
            }else{
                $trHrFinal=substr($trHr, 0,1);
            }
            
            
            if($trMin>=10){
                $trMinFinal=substr($trMin, 0,2);
            }else{
                $trMinFinal='0'.substr($trMin, 0,1);
            }
            
            if($trHrFinal==0){
            echo $finalTimeRendered='00:00 hr';    
            }else{
            echo $finalTimeRendered='0'.$trHrFinal.':'.$trMinFinal.' hrs';    
            }
            
            
            
            $grandTotalTRHr=$grandTotalTRHr+$trHrFinal;
            $grandTotalTRMin=$grandTotalTRMin+$trMinFinal;

    ?>
    
    </td>
    
    <?php } } } ?>
    
  <?php
    $SC_stmt4 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
    $SC_stmt4->execute([':completeDate' => $logDateCtr]);
    $SC_query4 = $SC_stmt4;
      
      if($SC_query4->rowCount()>0){
     
  ?>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
  <?php }else{ ?>
    <td></td>
    <td></td>
    <td></td> 
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
   
    <td colspan="5" style="background-color: #b8ffd9;"><center><small><?php echo strtoupper($SC_row2['event_title']); ?></small></center></td>
 
  </tr>
  <?php } ?>
<?php } ?>

<tr>
<td colspan="9">
<h2>Attendance Summary:</h2>
<table id="myTable">
<thead>
<tr>
<th colspan="2">Days Present</th>
<th colspan="2">Late</th>
<th colspan="2">Undertime</th>
<th colspan="2">Days Absent</th>
<th colspan="2">Time Rendered</th>
</tr>
</thead>

<tbody>
<tr>
<td style="width: 10%;">AM</td>
<td style="width: 10%;">PM</td>
<td style="width: 10%;">Late AM</td>
<td style="width: 10%;">Late PM</td>
<td style="width: 10%;">Undertime AM</td>
<td style="width: 10%;">Undertime PM</td>
<td style="width: 10%;">AM</td>
<td style="width: 10%;">PM</td>
<td style="width: 10%;">Regular Hours</td>
<td style="width: 10%;">Over Time</td>
</tr>


<tr>
<td><?php echo $amPresentCtr; ?></td>
<td><?php echo $pmPresentCtr; ?></td>

<td><?php echo $amLateCtr; ?></td>
<td><?php echo $pmLateCtr; ?></td>

<td><?php echo $amUTimeCtr; ?></td>
<td><?php echo $pmUTimeCtr; ?></td>

<td><?php echo $amAbsentCtr; ?></td>
<td><?php echo $pmAbsentCtr; ?></td>





<td>
           <?php 
           
            $grandTotalTRMin;
            $finalGTTRMin=$grandTotalTRMin/60;
         
            $finalGTTRHr=$grandTotalTRHr+$finalGTTRMin;
          
          
            if($finalGTTRMin>=1){
                
            $finalGTTRMin2=substr($finalGTTRMin, 0,1);
            $finalGTTRHr=$grandTotalTRHr+$finalGTTRMin2;
            
            }else{
                
            $finalGTTRHr=$grandTotalTRHr;
            
            }
            
           $finalGTTRMinXX=(substr(number_format((float)$finalGTTRMin, 2, ':', ''), -2,2)/100)*60;
            
           
            if($finalGTTRMinXX>=10){
                $finalDisplayGTTRMin=substr($finalGTTRMinXX, 0,2);
            }else{
                $finalDisplayGTTRMin='0'.substr($finalGTTRMinXX, 0,1);
            }
            
            
           echo $finalGTTRHr.':'.$finalDisplayGTTRMin." hrs"; 
           ?>
</td>

<td> </td>
</tr>

<tr>
<td colspan="2"><strong>Total Days Present:</strong> <?php echo ($amPresentCtr+$pmPresentCtr)/2 ?></td>


<td colspan="2"><strong>Total Late [ Total Time ]:</strong> <?php echo ($amLateCtr+$pmLateCtr); ?> [ 

<?php

$grandTotalLateMin=$grandTotalamLateMin+$grandTotalpmLateMin;
$final_lateHr=$grandTotalLateMin/60;
$final_lateHr=substr($grandTotalLateMin/60, 0,1);

$final_lateMin=substr($grandTotalLateMin/60, 1)/100*60;
$final_lateMin=number_format($final_lateMin, 2, '.', '');

$final_lateMin=substr($final_lateMin, 2);


echo  $final_lateHr.':'.$final_lateMin;

?> hrs ]</td>

<td colspan="2"><strong>Total Undertime [ Total Time ]:</strong> <?php echo ($amUTimeCtr+$pmUTimeCtr); ?> [ 

<?php

$grandTotalUTimeMin=$grandTotalamUTimeMin+$grandTotalpmUTimeMin;
$final_uTimeHr=$grandTotalUTimeMin/60;
$final_uTimeHr=substr($grandTotalUTimeMin/60, 0,1);
$final_uTimeMin=substr($grandTotalUTimeMin/60, 1)/100*60;
$final_uTimeMin=number_format($final_uTimeMin, 2, '.', '');
$final_uTimeMin=substr($final_uTimeMin, 2);

echo  $final_uTimeHr.':'.$final_uTimeMin;

?> hrs ]</td>

<td colspan="2"><strong>Total Days Absent:</strong> <?php echo ($amAbsentCtr+$pmAbsentCtr)/2; ?></td>
<td colspan="2"><strong>Total Time Rendered:</strong> <?php echo $finalGTTRHr.':'.$finalDisplayGTTRMin." hrs"; ; ?></td>
</tr>


 
<tr>
<td colspan="10">
<br />
<center>***THIS IS A SYSTEM GENERATED REPORT***</center>
<br />
</td>
</tr>
 
</tbody>
</table>

</td>
</tr>
</table>
 
<?php include('footer_print.php'); ?>


</body>
</html>
       
            