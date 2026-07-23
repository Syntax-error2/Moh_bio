
<script src="js/3.3.1_jquery.min.js"></script>
<script src="js/1.0.25_webcam.min.js"></script>
 
<div id="my_camera" style="display: none;"></div>
<div id="results" style="display: none;">Your captured image will appear here...</div>


                <script>
                
                var timeleft = 2;
                var downloadTimer = setInterval( function(){
                    
                    
                    document.getElementById("snapProgress").value = 2 - timeleft;
                    document.getElementById("countdown").innerHTML = "Capturing image in " + timeleft + "...";
                    
                    timeleft-=1;
                    
                    if(timeleft <= 0){
                        clearInterval(downloadTimer);
                        document.getElementById("countdown").innerHTML = "Snapshot Captured...";
                        }
                }, 1000)
                
                $(document).ready(function(){
                	setInterval(function(){
                	 
                        $("button[name=submit_log]").click();
                 
                    }, 2000);
                });
                </script>
                
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
 
 
$tr1_img="";
$tr1_lname="";
$tr1_fmname="";
$tr1_gl="";
$logTime="";
 

$displayLog_query = $conn->query("SELECT * FROM personnel_logs WHERE logDate='$currentDateDisplay' AND logFlow='' AND client_ip='$machine_ip' ORDER BY log_id DESC") or die(mysql_error());
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
        
        //$tr1_gl=$dpLog_row['shift_id']." - ".$dpLog_row['section'];
        $tr1_gl="";
        
        $logTime=$dpLog_row['logTime'];
        
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
        <p style="padding: 14px 8px 12px 12px; font-size: large; float: left;"> <strong><?php echo $logTime; ?></strong> </p>
        <p style="padding: 10px 16px 12px 12px; font-size: medium; float: right;">
                
                

 
 
    
 
  
                <?php if($am_in_log_query->rowCount()>0){
                    
                if($am_out_log_query->rowCount()>0){ }else{ ?>
               
                 
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_am_out){ ?>
                
           
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=AM OUT&lateStat=off" enctype="multipart/form-data">
      
                        
                        <input type="hidden" name="image" class="image-tag" />
                        
                        
                        
                         
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">AM OUT</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
                         
                </form>
    
     
                <?php }elseif($time_seconds_current_time<$time_seconds_am_out){ ?>
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=AM OUT&lateStat=on" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                        
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">AM OUT</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table> 
                        
                </form>
                
                
                <?php }else{ } ?>
                <!-- button trapping -->
                
               <?php } } else {  if($pil_row['logFlow']!=""){ }else{ ?> 
 
                
               <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_am_in AND $time_seconds_current_time<$time_seconds_am_in_late AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=AM IN&lateStat=off" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                        
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">AM IN</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
                        
           
 
                         
                        
         
                </form>
                 
                
                <?php }elseif($time_seconds_current_time>=$time_seconds_am_in_late AND $time_seconds_current_time>$time_seconds_am_in AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=AM IN&lateStat=on" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                         
                        
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">AM IN</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
                         
                </form>
                
                <?php }elseif($time_seconds_current_time<$time_seconds_am_in  AND $time_seconds_current_time>$time_seconds_am_out){ ?>
                
                        <a href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                       
                <?php }else{ ?>
                
                 <!-- <a href="#" class="btn btn-default btn-sm" style="border: solid 2px white; color: gray;">AM IN</a> -->

                <?php } ?>
                <!-- button trapping -->
             
                <?php } } ?>
                
                <?php if($pm_in_log_query->rowCount()>0){
                    
                if($pm_out_log_query->rowCount()>0){ }else{ ?> 
                
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_pm_out){ ?>
                
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=PM OUT&lateStat=off" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                        
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">PM OUT</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
           
                </form>
                 
                <?php }elseif($time_seconds_current_time<$time_seconds_pm_out){ ?>
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=PM OUT&lateStat=on" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                        
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">PM OUT</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
               
                </form>
              
                
                <?php }else{ ?>
                
                <!-- <a href="#" class="btn btn-default btn-sm" style="border: solid 2px white; color: gray;">PM OUT</a> -->      

                <?php } ?>
                <!-- button trapping -->
                
                
                 
                <?php } }else{ if($am_in_log_query->rowCount()>0){
                
                if($am_out_log_query->rowCount()>0){ 
                ?>
                
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_pm_in AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=PM IN&lateStat=off&type=<?php echo $sSDQ_row['type']; ?>&pm_OUT=<?php echo $sSDQ_row['pm_OUT']; ?>&am_IN=<?php echo $sSDQ_row['am_IN']; ?>" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">PM IN</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table> 
                       
                </form>
                
              
                 
                
                <?php }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time>$time_seconds_pm_in){ ?>
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=PM IN&lateStat=on&type=<?php echo $sSDQ_row['type']; ?>&pm_OUT=<?php echo $sSDQ_row['pm_OUT']; ?>&am_IN=<?php echo $sSDQ_row['am_IN']; ?>" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                       
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-warning btn-sm" style="border: solid 2px white; color: blue; display: none;">PM IN</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
       
                   
                </form>
                
                 <?php }elseif($time_seconds_current_time<$time_seconds_pm_in){ ?>
                        
                        <a href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
  
                <?php }else{ ?>
                
                 <!-- <a href="#" class="btn btn-default btn-sm" style="border: solid 2px white; color: gray;">PM IN</a> -->    

                <?php } ?>
                <!-- button trapping -->
                
                <?php  } }else{ ?>
                
                <!-- button trapping -->
                <?php if($time_seconds_current_time>=$time_seconds_pm_in AND $time_seconds_current_time<$time_seconds_pm_in_late){ ?>
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=PM IN&lateStat=off&type=<?php echo $sSDQ_row['type']; ?>&pm_OUT=<?php echo $sSDQ_row['pm_OUT']; ?>&am_IN=<?php echo $sSDQ_row['am_IN']; ?>" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                     
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-info btn-sm" style="border: solid 2px white; display: none;">PM IN</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
                      
                </form>
                
                
                <?php }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time>$time_seconds_pm_in){ ?>
                
                <form method="POST" action="updateTap.php?RFTag_id=<?php echo $RFTag_id; ?>&do_idxx=<?php echo $do_idxx; ?>&log_id=<?php echo $log_id; ?>&logFlow=PM IN&lateStat=on&type=<?php echo $sSDQ_row['type']; ?>&pm_OUT=<?php echo $sSDQ_row['pm_OUT']; ?>&am_IN=<?php echo $sSDQ_row['am_IN']; ?>" enctype="multipart/form-data">
      
                        <input type="hidden" name="image" class="image-tag" />
                        
                       
                        <table style="margin-top: 8px; width: 70%;" align="right">
                        <tr>
                        
                        <td>
                        <i class="fa fa-camera" style="color: white;"></i>
                        <progress value="0" max="1" id="snapProgress" style="height: 5px; width: 210px;"></progress>
                        <button name="submit_log" onclick="take_snapshot()" class="btn btn-warning btn-sm" style="border: solid 2px white; color: blue; display: none;">PM IN</button>
                        </td>
                        
                        <td>&nbsp;</td>
                        
                        <td rowspan="2">
                        <a id="hide" href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        </td>
                        
                        </tr>
                        <tr>
                        <td colspan="2">
                        <small style="color: white;"><div id="countdown">&nbsp;</div></small>
                        </td>
                        </tr>
                        </table>
                      
                </form>
                
                <?php }elseif($time_seconds_current_time<$time_seconds_pm_in AND $time_seconds_current_time>$time_seconds_am_out){ ?>
                        
                        <a href="updateBlankLogFlow.php?toWindow=rlt1" class="btn btn-default btn-sm" style="border: solid 2px white; color: black;">Cancel</a>
                        
                <?php }else{ } } } ?>
                
             
      </p>
    </div>
  
  


<?php } ?>

<!-- Configure a few settings and attach camera -->
<script language="JavaScript">
    Webcam.set({
        width: 490,
        height: 390,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
  
    Webcam.attach( '#my_camera' );
  
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );
    }
</script>
 
<script>
setTimeout(function(){
    $('#show').show()
}, 2000);
</script>

<script>
setTimeout(function(){
    $('#hide').hide()
}, 2000);
</script>


