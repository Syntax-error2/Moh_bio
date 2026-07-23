<?php

 
error_reporting(0);
include('dbcon.php');
include('myFunctions.php');

$current_time=date("h:i:s a");

$ct_query = $conn->query("select RFID_tag FROM client_computer WHERE RFID_tag='' AND ipAddress='".get_client_ip()."'") or die(mysql_error());

if($ct_query->rowCount()===1){ 

$currentTag=lastTagCode();
    
$conn->query("UPDATE client_computer SET RFID_tag='$currentTag' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());

clearLastTag();

}else{
    
clearLastTag();

$ct2_query = $conn->query("SELECT RFID_tag FROM client_computer WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
$ct2_row=$ct2_query->fetch();

$currentTag=$ct2_row['RFID_tag']; 

$personnelData_query = $conn->query("SELECT img, lname, fname, mname, suffix, do_id, shift_id FROM personnels WHERE RFTag_id='$currentTag'") or die(mysql_error());

if($personnelData_query->rowCount()==0)
{ 
    if($currentTag=="")
    {
        
    }else{
 
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("input[name=submitInvalid]").click();
        $("input[name=submitSB]").click();
    });
</script> 
 
<input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
<input type="hidden" name="submitSB" onclick="myFunction()" />

<?php
    
    }

    clearLastTag();
    $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
    
}else{
    

    $persData_row=$personnelData_query->fetch();
     
    $img='personnelImg/'.$persData_row['img'];
    
    $lname=$persData_row['lname'];
    $fname=$persData_row['fname'];
    $mname=$persData_row['mname'];
    $suffix=$persData_row['suffix'];
    
    //$gender=$persData_row['gender'];
    
    //$mobileNumber=$persData_row['mobileNumber'];
    
    $do_id=$persData_row['do_id'];
    $shift_id=$persData_row['shift_id'];
    
    
$studSchedDataQuery = $conn->query("SELECT * FROM time_schedules WHERE day='$day' AND do_id='$do_id' AND shift_id='$shift_id'") or die(mysql_error());
 
if($studSchedDataQuery->rowCount()>0)
{
    
                $sSDQ_row = $studSchedDataQuery->fetch();
    
                $str_current_time= date("H:i:s", strtotime($current_time));
                $str_current_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_current_time);
                sscanf($str_current_time, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds_current_time = ($hours * 3600) + $minutes * 60 + $seconds;
                
                
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
                
                
                $am_in_log_query = $conn->query("SELECT * FROM personnel_logs WHERE RFTag_id='$currentTag' AND logDate='$currentDate' AND logFlow='AM IN'") or die(mysql_error());
                $ail_row=$am_in_log_query->fetch();
                
                $am_out_log_query = $conn->query("SELECT * FROM personnel_logs WHERE RFTag_id='$currentTag' AND logDate='$currentDate' AND logFlow='AM OUT'") or die(mysql_error());
                $aol_row=$am_out_log_query->fetch();
                
                $pm_in_log_query = $conn->query("SELECT * FROM personnel_logs WHERE RFTag_id='$currentTag' AND logDate='$currentDate' AND logFlow='PM IN'") or die(mysql_error());
                $pil_row=$pm_in_log_query->fetch();
                
                $pm_out_log_query = $conn->query("SELECT * FROM personnel_logs WHERE RFTag_id='$currentTag' AND logDate='$currentDate' AND logFlow='PM OUT'") or die(mysql_error());
                $pol_row=$pm_out_log_query->fetch();
                
        if($mname=='')
        {
            $finalMName='';
            
        }else{
            
            if($suffix == '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';
        }
        
  
                
                //AM LOGS   AM LOGS     AM LOGS     AM LOGS     AM LOGS
                
                
                if($time_seconds_current_time>=$time_seconds_am_in AND $time_seconds_current_time<$time_seconds_pm_in){
                
                if($am_in_log_query->rowCount()>0)
                {
                        
                        
                        $AM_IN_log_checker=substr($ail_row['logTime'], 0, 5).":".date("s")." ".substr($ail_row['logTime'], 6, 2);
                        $str_time_AM_IN_logCHK= date("H:i:s", strtotime($AM_IN_log_checker));
                        $str_time_AM_IN_logCHK = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_AM_IN_logCHK);
                        sscanf($str_time_AM_IN_logCHK, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_AM_IN_log = $hours * 3600 + $minutes * 60 + $seconds;
                        
                        //5 MINUTES LOG ALLOWANCE
                        $LOG_timeAllowance=$time_seconds_AM_IN_log+300;
                        
                        
                        if($time_seconds_current_time>$LOG_timeAllowance){
                        
                        if($am_out_log_query->rowCount()<=0)
                        {
                        
                        
                        //LOG AM OUT ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             //SAVE AM OUT
                            
                            //save to student logs
                            $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, client_ip)
                            
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', '".get_client_ip()."')");  
                            
                            //end save to student logs 
                            
                             //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='', display_time=0 WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                            
                            
                        }else{  ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submitInvalid]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
                            <input type="hidden" name="submitSB" onclick="myFunction2()" />
                            
                             <?php
                             
                            //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                            
                            
                        } } else{
                            
                            //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                            
                        } 
                    
   
                }else{ //LOG AM IN ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                            
                            //save to student logs
                            $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, client_ip)
                            
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', '".get_client_ip()."')");  
                            
                            //end save to student logs 
                            
                             //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='', display_time=0 WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                   
                 }
                
                $AM_IN_logCHK_query=null;
                
                }else{
                            
                            if($time_seconds_current_time<$time_seconds_am_in){ ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submitInvalid]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
                            <input type="hidden" name="submitSB" onclick="myFunction2()" />
                            
                             <?php
                             
                             //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                            
                            }else{
                  //END AM LOGS           END AM LOGS           END AM LOGS           END AM LOGS           END AM LOGS
                       
                       
                    
                    
                //PM LOGS   PM LOGS     PM LOGS     PM LOGS     PM LOGS
                
                if($time_seconds_current_time>=$time_seconds_am_in AND $time_seconds_current_time>=$time_seconds_pm_in){
                
                $logCHK_PM_IN_query = $conn->query("SELECT * FROM personnel_logs WHERE RFTag_id='$currentTag' AND logDate='$currentDate' AND logFlow='PM IN'") or die(mysql_error());
                if($logCHK_PM_IN_query->rowCount()>0)
                {
                        $lcq_PM_IN_row=$logCHK_PM_IN_query->fetch();
                        
                        $PM_IN_log_checker=substr($lcq_PM_IN_row['logTime'], 0, 5).":".date("s")." ".substr($lcq_PM_IN_row['logTime'], 6, 2);
                        $str_time_PM_IN_logCHK= date("H:i:s", strtotime($PM_IN_log_checker));
                        $str_time_PM_IN_logCHK = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_PM_IN_logCHK);
                        sscanf($str_time_PM_IN_logCHK, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_PM_IN_log = $hours * 3600 + $minutes * 60 + $seconds;
                        
                        //5 MINUTES LOG ALLOWANCE
                        $LOG_timeAllowance=$time_seconds_PM_IN_log+300;
                        
                         
                        
                        if($time_seconds_current_time>$LOG_timeAllowance){
                        
                        $PM_OUT_logCHK_query = $conn->query("SELECT logTime FROM personnel_logs WHERE RFTag_id='$currentTag' AND logDate='$currentDate' AND logFlow='PM OUT'") or die(mysql_error());
                        if($PM_OUT_logCHK_query->rowCount()<=0)
                        { 
                        ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             //SAVE AM OUT
                            
                            //save to student logs
                            $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, client_ip)
                            
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', '".get_client_ip()."')");  
                            
                            //end save to student logs 
                            
                             //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='', display_time=0 WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                            
                            
                            
                        }else{
                            
                            //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                         
                         
                        } } else{
                            //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                         
                        } }else{ //PM IN LOG
                        
                        ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             //SAVE PM IN
                            
                            //save to student logs
                            $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, client_ip)
                            
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', '".get_client_ip()."')");  
                            
                            //end save to student logs 
                            
                             //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='', display_time=0 WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                            
                            
                        }
                        
                        $logCHK_PM_IN_query=null;
                        
                        }else{ ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submitInvalid]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
                            <input type="hidden" name="submitSB" onclick="myFunction2()" />
                            
                             <?php
                             
                             //clear tag data
                            clearLastTag();
                            $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
                            
                                    
                        }
                
                
                
                //END PM LOGS       END PM LOGS          END PM LOGS         END PM LOGS
                
                
                
                } }
                
                
                
                
    
             }else{
     
    ?>

    <script type="text/javascript">
        $(document).ready(function() {
            $("input[name=submitInvalid]").click();
            $("input[name=submitSB]").click();
        });
    </script>
     
    <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
    <input type="hidden" name="submitSB" onclick="myFunction4()" />
    
    <?php
    
    clearLastTag();
    $conn->query("UPDATE client_computer SET RFID_tag='' WHERE ipAddress='".get_client_ip()."'") or die(mysql_error());
            
} }


$studSchedDataQuery=null;
$personnelData_query=null;
$ct_query=null;
$ct2_query=null;
$conn=null;

}  ?>