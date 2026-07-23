 <?php

include('dbcon.php');

$dpCtr=0;

$currentDateDisplay=date('m/d/Y');
$bdChecker=date('m/d');

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


if(get_client_ip()=="::1")
{
    $machine_ip=gethostbyname(trim(`hostname`));  
}else{
    $machine_ip=get_client_ip();
}

$cc_query = $conn->query("select * from client_computer WHERE ipAddress='$machine_ip'");
$cc_row = $cc_query->fetch();

 

if($cc_row['display_time']==15){ 
    
if($cc_row['announcement_img']<=15){
$slide_img_query = $conn->query("select * from slides WHERE sequence=1");

}elseif($cc_row['announcement_img']<=30){
$slide_img_query = $conn->query("select * from slides WHERE sequence=2");

}elseif($cc_row['announcement_img']<=45){
$slide_img_query = $conn->query("select * from slides WHERE sequence=3");

}elseif($cc_row['announcement_img']<=60){
$slide_img_query = $conn->query("select * from slides WHERE sequence=4");

}elseif($cc_row['announcement_img']<=75){
$slide_img_query = $conn->query("select * from slides WHERE sequence=5");

}elseif($cc_row['announcement_img']<=90){
$slide_img_query = $conn->query("select * from slides WHERE sequence=6");

}elseif($cc_row['announcement_img']<=105){
$slide_img_query = $conn->query("select * from slides WHERE sequence=7");
   
}elseif($cc_row['announcement_img']<=120){
$slide_img_query = $conn->query("select * from slides WHERE sequence=8");

}elseif($cc_row['announcement_img']<=135){
$slide_img_query = $conn->query("select * from slides WHERE sequence=9");
   
}elseif($cc_row['announcement_img']<=150){
$slide_img_query = $conn->query("select * from slides WHERE sequence=10");
   
}elseif($cc_row['announcement_img']==150){
$conn->query("UPDATE client_computer SET announcement_img=0 WHERE ipAddress='$machine_ip'");

}

$si_row = $slide_img_query->fetch();
?> 

<div style="float: left; width: 85%; height: 61%; margin-top: 0px;" class="polaroid">
  <img src="announcement_img/<?php echo $si_row['img']; ?>" alt="announcement image..." style="width:100%; height:100%; padding: 5px 5px 5px 5px;">
</div> 

<?php

$conn->query("DELETE FROM personnel_logs WHERE logFlow=''") or die(mysql_error());

$cc_query=null;
$slide_img_query=null;

}else{
 
$tr1_img="";
$tr1_lname="";
$tr1_fmname="";
$tr1_gl="";
$logTime="";
 

$log_id_query = $conn->query("SELECT log_id FROM personnel_logs WHERE logDate='$currentDateDisplay' AND logFlow='' AND client_ip='$machine_ip' ORDER BY log_id DESC") or die(mysql_error());

if($log_id_query->rowCount()>0){

$liq_row=$log_id_query->fetch();

$displayLog_query = $conn->query("SELECT logTime, logFlow, log_id, RFTag_id, img, lname, fname, mname, suffix FROM personnel_logs WHERE log_id='$liq_row[log_id]'") or die(mysql_error());

}else{

$displayLog_query = $conn->query("SELECT logTime, logFlow, log_id, RFTag_id, img, lname, fname, mname, suffix FROM personnel_logs WHERE logDate='$currentDateDisplay' AND client_ip='$machine_ip' ORDER BY log_id DESC") or die(mysql_error());

}






while($dpLog_row=$displayLog_query->fetch())
{
    
    if(($dpLog_row['logTime']==='11:59 PM' AND $dpLog_row['logFlow']==='PM OUT') OR ($dpLog_row['logTime']==='12:00 AM' AND $dpLog_row['logFlow']==='AM IN')){
        
    }else{
        
    $dpCtr+=1;
     
    $log_id=$dpLog_row['log_id'];
    $RFTag_id=$dpLog_row['RFTag_id'];
 
    if($dpCtr==1)
    {
         
        $tr1_img=$dpLog_row['img'];
        $tr1_lname=$dpLog_row['lname'];
        
        if($dpLog_row['suffix']=="-")
        {
            $tr1_fmname=$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr1_fmname=$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
        //$tr1_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr1_gl="";
        
        
        $logTime=$dpLog_row['logTime'];
        $logFlow=$dpLog_row['logFlow'];
        
        $studData_query = $conn->query("SELECT * FROM personnels WHERE RFTag_id='$RFTag_id'") or die(mysql_error());
        $sd_row=$studData_query->fetch();
        
        $do_idxx=$sd_row['do_id'];
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting='Display';
        }else{
            $bdayGreeting='N/A';
        }
        
    }
  
} }
 
?>



<?php
if($tr1_img==""){ ?>
<div style="float: left;">
</div>
<?php }else{ ?>
<div style="float: left;" class="polaroid">
  <img src="<?php echo $tr1_img; ?>" alt="the last tapped" style="width:100%; height:100%; padding: 5px 5px 5px 5px;">
</div> 
<?php } ?>


<div style="width: 52%; height: 45%; background-color: transparent; float: right; margin: 4% 1% 0% 2%; ">

<?php
if($tr1_img==""){ ?>
 
 
<?php }else{ ?>

<p style="margin-bottom: 0px;"><strong style="font-size: 42px; margin-bottom: 0px;"><?php echo $tr1_lname; ?></strong>

<br />
<span style="font-size: 22px;"><?php echo $tr1_fmname; ?></span>
<br />
<br />
<br />
<span style="font-size: 22px;"><?php echo $tr1_gl; ?></span>
</p>

<?php if($bdayGreeting=='Display'){ ?>
<center><p style="margin-top: 12px;"><img style="width: 70%; height: 40%;" src="img/hbd.gif" /></p></center>
<?php }?>

<?php } ?>

</div>



<?php

if($log_id_query->rowCount()>0){

if($tr1_img==""){
    
}else{ ?>


            <?php
                            
                $am_in_log_query = $conn->query("SELECT * FROM personnel_logs WHERE logDate='$currentDateDisplay' AND client_ip='$machine_ip' AND RFTag_id='$RFTag_id' AND logFlow='AM IN'") or die(mysql_error());
                $ail_row=$am_in_log_query->fetch();
                
                $am_out_log_query = $conn->query("SELECT * FROM personnel_logs WHERE logDate='$currentDateDisplay' AND client_ip='$machine_ip' AND RFTag_id='$RFTag_id' AND logFlow='AM OUT'") or die(mysql_error());
                $aol_row=$am_out_log_query->fetch();
                
                $pm_in_log_query = $conn->query("SELECT * FROM personnel_logs WHERE logDate='$currentDateDisplay' AND client_ip='$machine_ip' AND RFTag_id='$RFTag_id' AND logFlow='PM IN'") or die(mysql_error());
                $pil_row=$pm_in_log_query->fetch();
                
                $pm_out_log_query = $conn->query("SELECT * FROM personnel_logs WHERE logDate='$currentDateDisplay' AND client_ip='$machine_ip' AND RFTag_id='$RFTag_id' AND logFlow='PM OUT'") or die(mysql_error());
                $pol_row=$pm_out_log_query->fetch();
             
                
    
    
                $day=date('l'); //Mon-Sun
                $current_time=date("h:i:s a"); 

                $str_current_time= date("H:i:s", strtotime($current_time));
                $str_current_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_current_time);
                sscanf($str_current_time, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_current_time = ($hours * 3600) + $minutes * 60 + $seconds;
                
                
                $studSchedDataQuery = $conn->query("SELECT * FROM time_schedules WHERE day='$day' AND do_id='$sd_row[do_id]' AND shift_id='$sd_row[shift_id]'") or die(mysql_error());
                $sSDQ_row = $studSchedDataQuery->fetch();
                
                //AM QUERY
                $amTimeIn=substr($sSDQ_row['am_IN'], 0, 5).":".date("s")." ".substr($sSDQ_row['am_IN'], 6, 2);
                $str_time_am_time_in= date("H:i:s", strtotime($amTimeIn));
                $str_time_am_time_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_time_in);
                sscanf($str_time_am_time_in, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_am_in = $hours * 3600 + $minutes * 60 + $seconds;
 
 
                $amTimeInLate=substr($sSDQ_row['am_IN_co'], 0, 5).":".date("s")." ".substr($sSDQ_row['am_IN_co'], 6, 2);
                $str_time_am_time_in_late= date("H:i:s", strtotime($amTimeInLate));
                $str_time_am_time_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_time_in_late);
                sscanf($str_time_am_time_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_am_in_late = $hours * 3600 + $minutes * 60 + $seconds;

                
                $amTimeOut=substr($sSDQ_row['am_OUT'], 0, 5).":".date("s")." ".substr($sSDQ_row['am_OUT'], 6, 2);
                $str_time_am_time_out= date("H:i:s", strtotime($amTimeOut));
                $str_time_am_time_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_time_out);
                sscanf($str_time_am_time_out, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_am_out = $hours * 3600 + $minutes * 60 + $seconds;
                //END AM QUERY
                
                
                
                //PM QUERY
                $pmTimeIn=substr($sSDQ_row['pm_IN'], 0, 5).":".date("s")." ".substr($sSDQ_row['pm_IN'], 6, 2);
                $str_time_pm_time_in= date("H:i:s", strtotime($pmTimeIn));
                $str_time_pm_time_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_time_in);
                sscanf($str_time_pm_time_in, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_pm_in = $hours * 3600 + $minutes * 60 + $seconds;


                $pmTimeInLate=substr($sSDQ_row['pm_IN_co'], 0, 5).":".date("s")." ".substr($sSDQ_row['pm_IN_co'], 6, 2);
                $str_time_pm_time_in_late= date("H:i:s", strtotime($pmTimeInLate));
                $str_time_pm_time_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_time_in_late);
                sscanf($str_time_pm_time_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_pm_in_late = $hours * 3600 + $minutes * 60 + $seconds;
 
 
                $pmTimeOut=substr($sSDQ_row['pm_OUT'], 0, 5).":".date("s")." ".substr($sSDQ_row['pm_OUT'], 6, 2);
                $str_time_pm_time_out= date("H:i:s", strtotime($pmTimeOut));
                $str_time_pm_time_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_time_out);
                sscanf($str_time_pm_time_out, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_pm_out = $hours * 3600 + $minutes * 60 + $seconds;
                //END PM QUERY
                
    ?>

        <div style="width: 52%; height: 8%; background-color: #008aff; color: white; float: right; margin: 0% 2.5% 0% 0%;">
        <p style="padding: 10px 16px 12px 12px; font-size: x-large; float: left;"> <strong><?php echo $logTime; ?></strong> </p>
        <p style="padding: 14px 16px 12px 12px; font-size: large; float: right;"> <strong>One moment please...</strong> </p>
                
                

 
 
    
 
  
                <?php if($am_in_log_query->rowCount()>0){
                    
                if($am_out_log_query->rowCount()>0){ }else{ ?>
               
                 
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_am_out){ ?>
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="AM OUT" />
                <input type="hidden" name="lateStat" value="off" />
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="AM OUT" /> 
                      
    
     
                <?php }elseif($time_seconds_current_time<$time_seconds_am_out){ ?>
                
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="AM OUT" />
                <input type="hidden" name="lateStat" value="on" />
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="AM OUT" /> 
               
                
                <?php }else{ } ?>
                <!-- button trapping -->
                
               <?php } } else {  if($pil_row['logFlow']!=""){ }else{ ?> 
 
                
               <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_am_in AND $time_seconds_current_time<$time_seconds_am_in_late AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="AM IN" />
                <input type="hidden" name="lateStat" value="off" />
                
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="AM IN" /> 
                
                
                
                
                
                <?php }elseif($time_seconds_current_time>=$time_seconds_am_in_late AND $time_seconds_current_time>$time_seconds_am_in AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                

                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="AM IN" />
                <input type="hidden" name="lateStat" value="on" />
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="AM IN" />
                
                
                 
                <?php }elseif($time_seconds_current_time<$time_seconds_am_in){ ?>
        
        
                <?php }else{ ?>
                
                 <!-- <a href="#" class="btn btn-default btn-sm" style="border: solid 2px white; color: gray;">AM IN</a> -->

                <?php } ?>
                <!-- button trapping -->
             
                <?php } } ?>
                
                <?php if($pm_in_log_query->rowCount()>0){
                    
                if($pm_out_log_query->rowCount()>0){ }else{ ?> 
                
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_pm_out){ ?>
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="PM OUT" />
                <input type="hidden" name="lateStat" value="off" />
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="PM OUT" />
                
                 
                
                 
                 
                <?php }elseif($time_seconds_current_time<$time_seconds_pm_out){ ?>
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="PM OUT" />
                <input type="hidden" name="lateStat" value="on" />
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="PM OUT" />
                
                 
                
                <?php }else{ ?>
                
                <!-- <a href="#" class="btn btn-default btn-sm" style="border: solid 2px white; color: gray;">PM OUT</a> -->      

                <?php } ?>
                <!-- button trapping -->
                
                
                 
                <?php } }else{ if($am_in_log_query->rowCount()>0){
                
                if($am_out_log_query->rowCount()>0){ 
                ?>
                
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_pm_in AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="PM IN" />
                <input type="hidden" name="lateStat" value="off" />
                
                <input type="hidden" name="type" value="<?php echo $sSDQ_row['type']; ?>" />
                <input type="hidden" name="pm_OUT" value="<?php echo $sSDQ_row['pm_OUT']; ?>" />
                <input type="hidden" name="am_IN" value="<?php echo $sSDQ_row['am_IN']; ?>" />
                 
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="PM IN" />
                
                 

                <?php }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time>$time_seconds_pm_in){ ?>
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="PM IN" />
                <input type="hidden" name="lateStat" value="on" />
                
                <input type="hidden" name="type" value="<?php echo $sSDQ_row['type']; ?>" />
                <input type="hidden" name="pm_OUT" value="<?php echo $sSDQ_row['pm_OUT']; ?>" />
                <input type="hidden" name="am_IN" value="<?php echo $sSDQ_row['am_IN']; ?>" />
                  
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="PM IN" />
                
                
                 <?php }elseif($time_seconds_current_time<$time_seconds_pm_in){ ?>
                        
                         
                <?php }else{ ?>
                
                 <!-- <a href="#" class="btn btn-default btn-sm" style="border: solid 2px white; color: gray;">PM IN</a> -->    

                <?php } ?>
                <!-- button trapping -->
                
                <?php  } }else{ ?>
                
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_pm_in AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                 
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="PM IN" />
                <input type="hidden" name="lateStat" value="off" />
                
                <input type="hidden" name="type" value="<?php echo $sSDQ_row['type']; ?>" />
                <input type="hidden" name="pm_OUT" value="<?php echo $sSDQ_row['pm_OUT']; ?>" />
                <input type="hidden" name="am_IN" value="<?php echo $sSDQ_row['am_IN']; ?>" />
                 
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="PM IN" />
                
                
                <?php }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time>$time_seconds_pm_in){ ?>
                
                <input type="hidden" name="RFTag_id" value="<?php echo $RFTag_id; ?>" />
                <input type="hidden" name="do_id" value="<?php echo $do_idxx; ?>" />
                <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
                <input type="hidden" name="logFlow" value="PM IN" />
                <input type="hidden" name="lateStat" value="on" />
                
                <input type="hidden" name="type" value="<?php echo $sSDQ_row['type']; ?>" />
                <input type="hidden" name="pm_OUT" value="<?php echo $sSDQ_row['pm_OUT']; ?>" />
                <input type="hidden" name="am_IN" value="<?php echo $sSDQ_row['am_IN']; ?>" />
                 
                
                <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="PM IN" />
                
                
                <?php }elseif($time_seconds_current_time<$time_seconds_pm_in AND $time_seconds_current_time>$time_seconds_am_out){ ?>
                        
                        <script type="text/javascript">
                        $(document).ready(function() {
                            $("input[name=submitInvalid]").click();
                            $("input[name=submitSB]").click();
                        });
                        </script>
                     
                        <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()" />
                        <input type="hidden" name="submitSB" onclick="myFunction2()" />
                        
                <?php }else{ } } } ?>
                
             
    
    </div> 
       
                <script>
                $(document).ready(function(){
               	 setInterval(function(){
             	      
                        $("input[name=saveBase64File]").click();
                       
                    }, 1000);
                });
                </script>
<?php }
        
}else{
    
if($tr1_img==""){ 
    
}else{ ?> 

    <div style="width: 52%; height: 8%; background-color: #008aff; color: white; float: right; margin: 0% 2.5% 0% 0%;">
      <p style="padding: 10px 16px 12px 12px; font-size: x-large; float: left;"> <strong><?php echo $logTime; ?></strong> </p>
      <p style="padding: 10px 16px 12px 12px; font-size: x-large; float: right;"> <strong><?php echo $logFlow; ?> SUCCESS...</strong> </p>
    </div>
 
 
<?php } } } ?>
