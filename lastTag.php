<?php


error_reporting(0);

include('dbcon.php');

include('myFunctions.php');
 

$current_time=date("h:i:s a");
 
$ct2_query=$conn->prepare("SELECT RFID_tag FROM client_computer WHERE ipAddress = :ipAddress");
$ct2_query->execute(['ipAddress' => get_client_ip()]);
$ct2_row=$ct2_query->fetch();

$currentTag=$ct2_row['RFID_tag']; 

$personnelData_query = $conn->prepare('SELECT img, lname, fname, mname, suffix, do_id, shift_id FROM personnels WHERE RFTag_id = :RFTag_id');
$personnelData_query->execute(['RFTag_id' => $currentTag]);

if($personnelData_query->rowCount()<=0)
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

<?php }

    
    $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag  WHERE ipAddress = :ipAddress';
    $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
 
}else{
    

    $persData_row=$personnelData_query->fetch();

    if($persData_row['img']===''){
        $img='personnelImg/default_img.jpg';
    }else{
        $img='personnelImg/'.$persData_row['img'];
    }
    
    
    $lname=$persData_row['lname'];
    $fname=$persData_row['fname'];
    $mname=$persData_row['mname'];
    $suffix=$persData_row['suffix'];
    
    //$gender=$persData_row['gender'];
    
    //$mobileNumber=$persData_row['mobileNumber'];
    
    $do_id=$persData_row['do_id'];
    $shift_id=$persData_row['shift_id'];
    
    $str_current_time= date("H:i:s", strtotime($current_time));
    $str_current_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_current_time);
    sscanf($str_current_time, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds_current_time = ($hours * 3600) + $minutes * 60 + $seconds;
                
    $studSchedDataQuery = $conn->prepare('SELECT am_IN, am_IN_co, am_OUT, pm_IN, pm_IN_co, pm_OUT, type FROM time_schedules WHERE day = :day AND do_id = :do_id AND shift_id = :shift_id');
    $studSchedDataQuery->execute(['day' => $day, 'do_id' => $do_id, 'shift_id' => $shift_id]);
    
    
    
if($studSchedDataQuery->rowCount()>0){
        
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
                
                
                $am_in_log_query = $conn->prepare('SELECT logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND logFlow = :logFlow');
                $am_in_log_query->execute(['RFTag_id' => $currentTag, 'logDate' => $currentDate, 'logFlow' => 'AM IN']);
                $ail_row=$am_in_log_query->fetch();
                
                $am_out_log_query = $conn->prepare('SELECT logTime, logTime_sec FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND logFlow = :logFlow');
                $am_out_log_query->execute(['RFTag_id' => $currentTag, 'logDate' => $currentDate, 'logFlow' => 'AM OUT']);
                $aol_row=$am_out_log_query->fetch();
                
                $pm_in_log_query = $conn->prepare('SELECT logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND logFlow = :logFlow');
                $pm_in_log_query->execute(['RFTag_id' => $currentTag, 'logDate' => $currentDate, 'logFlow' => 'PM IN']);
                $pil_row=$pm_in_log_query->fetch();
                
                $pm_out_log_query = $conn->prepare('SELECT logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND logFlow = :logFlow');
                $pm_out_log_query->execute(['RFTag_id' => $currentTag, 'logDate' => $currentDate, 'logFlow' => 'PM OUT']);
                $pol_row=$pm_out_log_query->fetch();
                 
        if($mname=='')
        {
            $finalMName='';
            
        }else{
            
            if($suffix == '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';
        }
        
                
//START REGULAR SHIFT// //START REGULAR SHIFT//     //START REGULAR SHIFT//     //START REGULAR SHIFT//     //START REGULAR SHIFT// 
if($sSDQ_row['type']==='Regular Shift'){
 
                //check am in 
                if($am_in_log_query->rowCount()>0){
                    
                    
                        $AM_IN_log_checker=substr($ail_row['logTime'], 0, 5).":".date("s")." ".substr($ail_row['logTime'], 6, 2);
                        $str_time_AM_IN_logCHK= date("H:i:s", strtotime($AM_IN_log_checker));
                        $str_time_AM_IN_logCHK = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_AM_IN_logCHK);
                        sscanf($str_time_AM_IN_logCHK, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_AM_IN_log = $hours * 3600 + $minutes * 60 + $seconds;
                        
                    //5 MINUTES LOG ALLOWANCE
                    $LOG_timeAllowance=$time_seconds_AM_IN_log+180;

                    //start check am out false
                    if($time_seconds_current_time>$LOG_timeAllowance){
                    
                    if($am_out_log_query->rowCount()<=0){
                        
                    if($time_seconds_current_time<$time_seconds_pm_in_late){
                        
                    if($time_seconds_current_time<$time_seconds_am_out){
   
                        //am out undertime
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
                             
                             //SAVE am out undertime
                            
                            //save to personnel logs
                            $save_AM_OUT_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', '$time_seconds_current_time', 'on', 'AM OUT', '".get_client_ip()."')";
                            $conn->exec($save_AM_OUT_co_Log);
                            //end save to personnel logs 
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            $currentTag="";
                            
                    //end am out undertime
                    }elseif($time_seconds_current_time>=$time_seconds_am_out AND $time_seconds_current_time<=$time_seconds_pm_in_late){
                        //am out
                    
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
                             
                             //SAVE am out
                            
                            //save to personnel logs
                            $save_AM_OUT_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', '$time_seconds_current_time', 'off', 'AM OUT', '".get_client_ip()."')";
                            $conn->exec($save_AM_OUT_Log);
                            //end save to personnel logs
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            //end am out
                            
                            $currentTag="";
                            
                            }
                        
                        }else{
                            //no am out, pass pm in late trap
                            ?>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submitInvalid]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
                            <input type="hidden" name="submitSB" onclick="myFunction5()" />
                            
                            <?php
                            
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag  WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                        //end no am out, pass pm in late trap
                        }
                    }
                           
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
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                             
                            
                        }  //end time allowance
                     
                    
                    
                //end check am in true
                }else{
                //start check am in false
                
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
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                        
                }elseif($time_seconds_current_time>=$time_seconds_am_in AND $time_seconds_current_time<$time_seconds_am_in_late){
                //am in
                
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
                             
                             //SAVE am in
                            
                            //save to personnel logs
                            $save_AM_IN_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'AM IN', '".get_client_ip()."')";
                            $conn->exec($save_AM_IN_Log);
                            //end save to personnel logs
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                          
                           
                //end am in            
                }elseif($time_seconds_current_time>=$time_seconds_am_in_late AND $time_seconds_current_time<$time_seconds_am_out){
      
                //am in late ?>
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             
                             //save to personnel logs
                             $save_AM_IN_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                             VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'on', 'AM IN', '".get_client_ip()."')";
                             $conn->exec($save_AM_IN_co_Log);
                             //end save to personnel logs
                                 
                                 
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            }elseif($time_seconds_current_time<$time_seconds_pm_in AND $time_seconds_current_time>$time_seconds_am_out){
                            
                            ?>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submitInvalid]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
                            <input type="hidden" name="submitSB" onclick="myFunction5()" />
                            
                            <?php
                            
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag  WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
                            }
                
                //end check am in false
                }
                
                
                
                //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS
        

//===========//==============//====================//===================
                
                
                //PM IN LOGS ******* //PM IN LOGS******* //PM IN LOGS******* //PM IN LOGS******* //PM IN LOGS******* //PM IN LOGS
 
                if($pm_in_log_query->rowCount()>0){
                    
                    
                        $PM_IN_log_checker=substr($pil_row['logTime'], 0, 5).":".date("s")." ".substr($pil_row['logTime'], 6, 2);
                        $str_time_PM_IN_logCHK= date("H:i:s", strtotime($PM_IN_log_checker));
                        $str_time_PM_IN_logCHK = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_PM_IN_logCHK);
                        sscanf($str_time_PM_IN_logCHK, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_PM_IN_log = $hours * 3600 + $minutes * 60 + $seconds;
                        
                        //5 MINUTES LOG ALLOWANCE
                        $LOG_timeAllowance=$time_seconds_PM_IN_log+300;
                        
                    //check pm out
                    if($pm_out_log_query->rowCount()>0){
                    
                    //clear tag data
                    
                    $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                    $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                   
                           
                    //end check pm out true
                    }else{
                    //start check pm out false
                    if($time_seconds_current_time>$LOG_timeAllowance){
                        
                    if($time_seconds_current_time<$time_seconds_pm_out){
                        //pm out undertime
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
                             
                             //SAVE pm out undertime
                            
                            //save to personnel logs
                            $save_PM_OUT_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'on', 'PM OUT', '".get_client_ip()."')";
                            $conn->exec($save_PM_OUT_co_Log);
                            //end save to personnel logs
                            
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                    
                    //end pm out undertime
                    }elseif($time_seconds_current_time>=$time_seconds_pm_out){
                        //pm out
                    
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
                             
                             //SAVE pm out
                            
                            //save to personnel logs
                            $save_PM_OUT_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'PM OUT', '".get_client_ip()."')";
                            $conn->exec($save_PM_OUT_Log);
                            //end save to personnel logs
                            
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                      
                            }  //end pm out
                            
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
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
                        }  //end time allowance
                    } //end check pm out false
                    
                    
                //end check pm in true
                }else{
                //start check pm in false
                
                 
                            if($am_in_log_query->rowCount()>0 AND $am_out_log_query->rowCount()>0){
                            
                            //echo "1st level passed <br />";
                            
                            $LOG_AM_OUT_timeAllowance=$aol_row['logTime_sec']+300;
                            
                            //if($time_seconds_current_time>$LOG_AM_OUT_timeAllowance AND $currentTag!=""){
                            if($time_seconds_current_time>$LOG_AM_OUT_timeAllowance){
    
                            //if($time_seconds_current_time<$time_seconds_pm_in AND $time_seconds_current_time>$time_seconds_am_out){ 
                            if($time_seconds_current_time<$time_seconds_pm_in){
                            ?>
                                
                            
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
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                
                            }elseif($time_seconds_current_time>=$time_seconds_pm_in AND $time_seconds_current_time<$time_seconds_pm_in_late AND $currentTag!=""){
                            //pm in
                            
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
                             
                            //SAVE pm in
                            
                            //save to personnel logs
                            $save_PM_IN_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'PM IN', '".get_client_ip()."')";
                            $conn->exec($save_PM_IN_Log);
                            //end save to personnel logs
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            //end pm in            
                            }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time<$time_seconds_pm_out AND $currentTag!=""){
                            //pm in late ?>
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             //SAVE pm in late
                            
                            //save to personnel logs
                            $save_PM_IN_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'on', 'PM IN', '".get_client_ip()."')";
                            $conn->exec($save_PM_IN_co_Log);
                            //end save to personnel logs 
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            }elseif($time_seconds_current_time>$time_seconds_pm_out){
                    
                            ?>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submitInvalid]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
                            <input type="hidden" name="submitSB" onclick="myFunction5()" />
                            
                            <?php
                            
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
                            }
                            
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
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                     
                            }
                        
                        
                        
                            }elseif($am_in_log_query->rowCount()>=0 AND $am_out_log_query->rowCount()<=0){
                                
                            
             
                            if($time_seconds_current_time<$time_seconds_pm_in){ ?>
                            
                            
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
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                
                            }elseif($time_seconds_current_time>=$time_seconds_pm_in AND $time_seconds_current_time<$time_seconds_pm_in_late AND $currentTag!=""){
                            //pm in
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
                             
                             //SAVE pm in
                            
                            //save to personnel logs
                            $save_PM_IN_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'PM IN', '".get_client_ip()."')";
                            $conn->exec($save_PM_IN_Log);
                            //end save to personnel logs
                             
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            //end pm in            
                            }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time<$time_seconds_pm_out AND $currentTag!=""){
                            //pm in late ?>
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             //SAVE pm in late
                            
                            //save to personnel logs
                            $save_PM_IN_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'on', 'PM IN', '".get_client_ip()."')";
                            $conn->exec($save_PM_IN_co_Log);
                            //end save to personnel logs 
                            
                             //clear tag data
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            }elseif($time_seconds_current_time>$time_seconds_pm_out){
                    
                            ?>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submitInvalid]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submitInvalid" onclick="PopupCenterInvalid()">
                            <input type="hidden" name="submitSB" onclick="myFunction5()" />
                            
                            <?php
                            
                            
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
                            }
                            
                        }
                        //end check pm in false
                    }
                
                //END PM LOGS       END PM LOGS     END PM LOGS     END PM LOGS     END PM LOGS     END PM LOGS     END PM LOGS
        
                //END REGULAR SHIFT//   //END REGULAR SHIFT//   //END REGULAR SHIFT//   //END REGULAR SHIFT//   //END REGULAR SHIFT//

               
}elseif($sSDQ_row['type']==='Night Shift'){

                //check am in 
                if($am_in_log_query->rowCount()>0){
                    
                    
                        $AM_IN_log_checker=substr($ail_row['logTime'], 0, 5).":".date("s")." ".substr($ail_row['logTime'], 6, 2);
                        $str_time_AM_IN_logCHK= date("H:i:s", strtotime($AM_IN_log_checker));
                        $str_time_AM_IN_logCHK = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_AM_IN_logCHK);
                        sscanf($str_time_AM_IN_logCHK, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_AM_IN_log = $hours * 3600 + $minutes * 60 + $seconds;
                        
                    //5 MINUTES LOG ALLOWANCE
                    $LOG_timeAllowance=$time_seconds_AM_IN_log+180;

                    //start check am out false
                    if($time_seconds_current_time>$LOG_timeAllowance){
                        
                    if($time_seconds_current_time<$time_seconds_am_out){
                        //am out undertime
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
                             
                             //SAVE am out undertime
                            
                            //save to personnel logs
                            $save_AM_OUT_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'on', 'AM OUT', '".get_client_ip()."')";
                            $conn->exec($save_AM_OUT_co_Log);
                            //end save to personnel logs 
                            
                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                       
                    //end am out undertime
                    }elseif($time_seconds_current_time>=$time_seconds_am_out){
                        //am out
                    
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
                             
                             //SAVE am out
                            
                            //save to personnel logs
                            $save_AM_OUT_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'AM OUT', '".get_client_ip()."')";
                            $conn->exec($save_AM_OUT_Log);
                            //end save to personnel logs
                            
                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                           
                            //end am out
                            }
                            
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
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                             
                            
                        }  //end time allowance
                     
                    
                    
                //end check am in true
                }
                
                //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS
        

//===========//==============//====================//===================
                
                
                //PM IN LOGS ******* //PM IN LOGS******* //PM IN LOGS******* //PM IN LOGS******* //PM IN LOGS******* //PM IN LOGS
                
                if($pm_in_log_query->rowCount()>0){
               
                //clear tag data
                clearLastTag();
                $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
                }else{
                    
                if($time_seconds_current_time<$time_seconds_pm_in){
                    
                    //clear tag data nedd trapping for alert
                    clearLastTag();
                    $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                    $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                
                }elseif($time_seconds_current_time>=$time_seconds_pm_in AND $time_seconds_current_time<$time_seconds_pm_in_late){
                //pm in

                
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
                             
                             //SAVE pm in
                            
                            //save to personnel logs
                            $save_PM_IN_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'PM IN', '".get_client_ip()."')";
                            $conn->exec($save_PM_IN_Log);
                            //end save to personnel logs
                 
                            
                            $date_AM_IN = new DateTime($currentDate.'+ 1 day');
                            $finalDate_AM_IN=$date_AM_IN->format('m/d/Y');
                            
                            
                            // begin the transaction
                            $conn->beginTransaction();
                            
                            // our SQL statements
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '11:59 PM', 'off', 'PM OUT', '".get_client_ip()."')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$finalDate_AM_IN', '12:00 AM', 'off', 'AM IN', '".get_client_ip()."')");
                            
                            // commit the transaction
                            $conn->commit();
                            
                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                //end pm in            
                }elseif($time_seconds_current_time>=$time_seconds_pm_in_late){
                //pm in late ?>
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             //SAVE pm in late
                            
                            //save to personnel logs
                            $save_PM_IN_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'on', 'PM IN', '".get_client_ip()."')";
                            $conn->exec($save_PM_IN_co_Log);
                            //end save to personnel logs 
                            
                            $date_AM_IN = new DateTime($currentDate.'+ 1 day');
                            $finalDate_AM_IN=$date_AM_IN->format('m/d/Y');
                            
                            
                            // begin the transaction
                            $conn->beginTransaction();
                            
                            // our SQL statements
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '11:59 PM', 'off', 'PM OUT', '".get_client_ip()."')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$finalDate_AM_IN', '12:00 AM', 'off', 'AM IN', '".get_client_ip()."')");
                            
                            // commit the transaction
                            $conn->commit();

                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            }
                }
                
                //END PM LOGS       END PM LOGS     END PM LOGS     END PM LOGS     END PM LOGS     END PM LOGS     END PM LOGS
    
}elseif($sSDQ_row['type']==='24 Hours Shift'){
                
                if($am_in_log_query->rowCount()>0 AND $am_out_log_query->rowCount()>0){

                //clear tag data
                clearLastTag();
                $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
                }else{
                
                if($am_in_log_query->rowCount()>0 AND $am_out_log_query->rowCount()<=0){
                    
                //save to personnel logs
                $save_AM_OUT_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'AM OUT', '".get_client_ip()."')";
                $conn->exec($save_AM_OUT_Log);
                //end save to personnel logs
                            
                //clear tag data
                clearLastTag();
                $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                
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
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                        
                }elseif($time_seconds_current_time>=$time_seconds_am_in AND $time_seconds_current_time<$time_seconds_am_in_late){
                //am in
                
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
                             
                             //SAVE am in
                            
                            //save to personnel logs
                            $save_AM_IN_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', 'AM IN', '".get_client_ip()."')";
                            $conn->exec($save_AM_IN_Log);
                            //end save to personnel logs
                     
                            $date_AM_IN = new DateTime($currentDate.'+ 1 day');
                            $finalDate_AM_IN=$date_AM_IN->format('m/d/Y');
                            
                            // begin the transaction
                            $conn->beginTransaction();
                            
                            // our SQL statements
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '11:59 AM', 'off', 'AM OUT', '".get_client_ip()."', '24hrs')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '12:00 PM', 'off', 'PM IN', '".get_client_ip()."', '24hrs')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '11:59 PM', 'off', 'PM OUT', '".get_client_ip()."', '24hrs')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$finalDate_AM_IN', '12:00 AM', 'off', 'AM IN', '".get_client_ip()."', '24hrs')");
           
                            // commit the transaction
                            $conn->commit();
                        
                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                          
                           
                //end am in            
                }elseif($time_seconds_current_time>=$time_seconds_am_in_late){
      
                //am in late ?>
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                             <?php
                             
                             //SAVE am in late
              
                            //save to personnel logs
                            $save_AM_IN_co_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'on', 'AM IN', '".get_client_ip()."')";
                            $conn->exec($save_AM_IN_co_Log);
                            //end save to personnel logs
                            
                            $date_AM_IN = new DateTime($currentDate.'+ 1 day');
                            $finalDate_AM_IN=$date_AM_IN->format('m/d/Y');
                            
                            // begin the transaction
                            $conn->beginTransaction();
                            
                            // our SQL statements
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '11:59 AM', 'off', 'AM OUT', '".get_client_ip()."', '24hrs')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '12:00 PM', 'off', 'PM IN', '".get_client_ip()."', '24hrs')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '11:59 PM', 'off', 'PM OUT', '".get_client_ip()."', '24hrs')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, remarks)
                            VALUES ('$currentTag', '$img', 'nss/nss.jpg', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$finalDate_AM_IN', '12:00 AM', 'off', 'AM IN', '".get_client_ip()."', '24hrs')");
           
                            // commit the transaction
                            $conn->commit();
               
                            
                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            } 
                        }
                    }
              
                
                //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS ******* //AM IN LOGS


}elseif($shift_id===777){
    
    
                $date_yesterday = new DateTime($currentDate.'- 1 day');
                $yesterDate=$date_yesterday->format('m/d/Y');
                
                $log_ctr_stmt = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
                $log_ctr_stmt->execute([':RFTag_id' => $currentTag, ':logDate' => $yesterDate]);
                $log_ctr_query = $log_ctr_stmt;
                $log_ctr=$log_ctr_query->rowcount();
                
                $mod_val=$log_ctr % 2;
                
                if($mod_val>0){
                //OUT
                
                $yesterday_in_log_stmt = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (logFlow = 'AM IN' OR logFlow = 'PM IN') ORDER BY log_id DESC");
                $yesterday_in_log_stmt->execute([':RFTag_id' => $currentTag, ':logDate' => $yesterDate]);
                $yesterday_in_log_query = $yesterday_in_log_stmt;
                $yd_il_row=$yesterday_in_log_query->fetch();
                $ref_log_id=$yd_il_row['log_id'];
                
                if(date('A')==='AM' OR date('A')==='am'){
                    $logFlow="AM OUT";
                }elseif(date('A')==='PM' OR date('A')==='pm'){
                    $logFlow="PM OUT";
                }
              
                
                            // begin the transaction
                            $conn->beginTransaction();
                            
                            // our SQL statements
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, ref_log_id)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$yesterDate', '11:59 PM', 'off', 'PM OUT', '".get_client_ip()."', '$ref_log_id')");
                            
                            $conn->exec("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '12:00 AM', 'off', 'AM IN', '".get_client_ip()."')");
                            
                            // commit the transaction
                            $conn->commit();
               
                            
                            $in_log_stmt = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (logFlow = 'AM IN' OR logFlow = 'PM IN') ORDER BY log_id DESC");
                            $in_log_stmt->execute([':RFTag_id' => $currentTag, ':logDate' => $currentDate]);
                            $in_log_query = $in_log_stmt;
                            $il_row=$in_log_query->fetch();
                            $ref_log_id=$il_row['log_id'];
                
                            //save to personnel logs
                            $save_OPEN_OUT_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, ref_log_id)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', '$logFlow', '".get_client_ip()."', '$ref_log_id')";
                            $conn->exec($save_OPEN_OUT_Log);
                            //end save to personnel logs
                            
                            //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                        ?>
    
                        <script type="text/javascript">
                            $(document).ready(function() {
                            $("input[name=submit]").click();
                            $("input[name=submitSB]").click();
                            });
                        </script>
                             
                        <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                        <input type="hidden" name="submitSB" onclick="myFunction6()" />
                            
                        <?php }else{
                        
                        //IN
                        
                        $current_log_ctr_stmt = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
                        $current_log_ctr_stmt->execute([':RFTag_id' => $currentTag, ':logDate' => $currentDate]);
                        $current_log_ctr_query = $current_log_ctr_stmt;
                        $current_log_ctr=$current_log_ctr_query->rowcount();
                        
                        $current_mod_val=$current_log_ctr % 2;
                        
                        if($current_mod_val>0){
                        //CURRENT DAY OUT OPEN
                        
                        $in_log_stmt = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (logFlow = 'AM IN' OR logFlow = 'PM IN') ORDER BY log_id DESC");
                        $in_log_stmt->execute([':RFTag_id' => $currentTag, ':logDate' => $currentDate]);
                        $in_log_query = $in_log_stmt;
                        $il_row=$in_log_query->fetch();
                        $ref_log_id=$il_row['log_id'];
                        
                        //LOG IN TIME CTR
                        $TimeIn=substr($il_row['logTime'], 0, 5).":".date("s")." ".substr($il_row['logTime'], 6, 2);
                        $str_time_time_in= date("H:i:s", strtotime($TimeIn));
                        $str_time_time_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_time_in);
                        sscanf($str_time_time_in, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_in = $hours * 3600 + $minutes * 60 + $seconds;
                        
                        //FOR OPEN TIME
                        $timeAllowance=$time_seconds_in+28800;
                        if($time_seconds_current_time>=$timeAllowance){
                            
                            
                            if(date('A')==='AM' OR date('A')==='am'){
                                $logFlow="AM OUT";
                            }elseif(date('A')==='PM' OR date('A')==='pm'){
                                $logFlow="PM OUT";
                            }
              
                            //save to personnel logs
                            $save_OPEN_OUT_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip, ref_log_id)
                            VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', '$logFlow', '".get_client_ip()."', '$ref_log_id')";
                            $conn->exec($save_OPEN_OUT_Log);
                            //end save to personnel logs
                            
                                                                                    
                            //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
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
                            //END AM OUT >= 8 HOURS
                            }else{
                            //LESS 8 HOURS
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);''
                            ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterInvalid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction2()" />
                            
                             <?php
                             
                        } }else{
                        
                        
                        
                        
                        if($current_log_ctr_query->rowCount()<=0){
                            
                            
                        if(date('A')==='AM' OR date('A')==='am'){
                            $logFlow="AM IN";
                        }elseif(date('A')==='PM' OR date('A')==='pm'){
                            $logFlow="PM IN";
                        }
                            
                        //save to personnel logs
                        $save_OPEN_IN_Log = "INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, late_status, logFlow, client_ip)
                        VALUES ('$currentTag', '$img', '$lname', '$fname', '$mname', '$suffix', '$do_id', '$shift_id', '$currentDate', '$logTime', 'off', '$logFlow', '".get_client_ip()."')";
                        $conn->exec($save_OPEN_IN_Log);
                        //end save to personnel logs
                            
                                                                                    
                            
                        //clear tag data
                        clearLastTag();
                        $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                        $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            ?>
    
                            <script type="text/javascript">
                                $(document).ready(function() {
                                $("input[name=submit]").click();
                                $("input[name=submitSB]").click();
                                });
                            </script>
                                 
                            <input type="hidden" name="submit" onclick="PopupCenterValid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction6()" />
                                
                        <?php }else{
                            
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);''
                            ?>
                            
                            
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("input[name=submit]").click();
                                    $("input[name=submitSB]").click();
                                });
                            </script>
                             
                            <input type="hidden" name="submit" onclick="PopupCenterInvalid()" />
                            <input type="hidden" name="submitSB" onclick="myFunction2()" />
                            
                             <?php
                        }
                    }
                }
                 
    } }else{
    
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
    
    
    $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
    $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
            
} }

$conn=null;

?>