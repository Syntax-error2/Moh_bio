<?php

error_reporting(0);

include_once('dbcon.php');

include_once('myFunctions.php');
 

$current_time=date("h:i:s a");

$ct_query=$conn->prepare("SELECT RFID_tag FROM client_computer WHERE RFID_tag = :RFID_tag AND ipAddress = :ipAddress");
$ct_query->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
 
if($ct_query->rowCount()===1){ 

$currentTag=lastTagCode();

$updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag  WHERE ipAddress = :ipAddress';
$conn->prepare($updClient)->execute(['RFID_tag' => $currentTag, 'ipAddress' => get_client_ip()]);

clearLastTag();

}else{

$ct2_query=$conn->prepare("SELECT RFID_tag FROM client_computer WHERE ipAddress = :ipAddress");
$ct2_query->execute(['ipAddress' => get_client_ip()]);
$ct2_row=$ct2_query->fetch();

$currentTag=$ct2_row['RFID_tag']; 

$personnelData_query = $conn->prepare("SELECT p.personnel_id, p.img, p.lname, p.fname, p.mname, p.suffix, p.do_id, p.shift_id, p.RFTag_id, p.biometric_id FROM personnels p JOIN emp_status es ON es.empStat_id = p.empStat_id WHERE (p.RFTag_id = :RFTag_id OR p.biometric_id = :biometric_id) AND es.status = 'Active' AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ')");
$personnelData_query->execute(['RFTag_id' => $currentTag, 'biometric_id' => $currentTag]);

if($personnelData_query->rowCount()==0 && ctype_digit((string)$currentTag)) {
    $personnelData_query = $conn->prepare("SELECT p.personnel_id, p.img, p.lname, p.fname, p.mname, p.suffix, p.do_id, p.shift_id, p.RFTag_id, p.biometric_id FROM personnels p JOIN emp_status es ON es.empStat_id = p.empStat_id WHERE p.personnel_id = :pid AND es.status = 'Active' AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ')");
    $personnelData_query->execute(['pid' => (int)$currentTag]);
}
if($personnelData_query->rowCount()==0 && $currentTag != '') {
    $personnelData_query = $conn->prepare("SELECT p.personnel_id, p.img, p.lname, p.fname, p.mname, p.suffix, p.do_id, p.shift_id, p.RFTag_id, p.biometric_id FROM personnels p JOIN emp_status es ON es.empStat_id = p.empStat_id WHERE p.personnel_id_code = :pcode AND es.status = 'Active' AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ')");
    $personnelData_query->execute(['pcode' => $currentTag]);
}

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

<?php }

    clearLastTag();
    $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag  WHERE ipAddress = :ipAddress';
    $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
 
}else{
    

    $persData_row=$personnelData_query->fetch();
    $currentTag = $persData_row['RFTag_id'] ?: ($persData_row['biometric_id'] ?: $currentTag); // Ensure we use the real tag for insertions
    $rfTagList = array_values(array_unique(array_filter([$persData_row['RFTag_id'], $persData_row['biometric_id'], $currentTag])));
    $tagPlaceholders = implode(',', array_fill(0, count($rfTagList), '?'));
    $currentDateMdy = date('m/d/Y');

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

    // Card scans after the regular PM OUT belong to a separate OT pair.
    $pmOutExists = $conn->prepare("SELECT 1 FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND logFlow = 'PM OUT' LIMIT 1");
    $pmOutExists->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
    if ($pmOutExists->fetchColumn()) {
        try {
            $conn->beginTransaction();
            $lock = $conn->prepare('SELECT personnel_id FROM personnels WHERE personnel_id = ? FOR UPDATE');
            $lock->execute([$persData_row['personnel_id']]);
            $lastPunch = $conn->prepare("SELECT logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND logFlow IN ('PM OUT', 'OT IN', 'OT OUT') ORDER BY log_id DESC LIMIT 1");
            $lastPunch->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
            $previous = $lastPunch->fetchColumn();
            if ($previous && time() - strtotime($currentDate . ' ' . $previous) < 60) {
                $conn->rollBack();
                $otMessage = 'Scan too soon. Please wait one minute.';
                $otSuccess = false;
            } else {
                $openOt = $conn->prepare("SELECT i.log_id FROM personnel_logs i WHERE i.RFTag_id IN ($tagPlaceholders) AND i.logDate IN (?, ?) AND i.logFlow = 'OT IN' AND NOT EXISTS (SELECT 1 FROM personnel_logs o WHERE o.RFTag_id = i.RFTag_id AND o.logDate = i.logDate AND o.logFlow = 'OT OUT' AND o.log_id > i.log_id) ORDER BY i.log_id DESC LIMIT 1");
                $openOt->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
                $otFlow = $openOt->fetchColumn() ? 'OT OUT' : 'OT IN';
                $saveOt = $conn->prepare("INSERT INTO personnel_logs (RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, late_status, logFlow, client_ip, remarks, captured_img, travel_leave_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'off', ?, ?, '', '', '')");
                $saveOt->execute([$currentTag, $persData_row['img'], $lname, $fname, $mname, $suffix, $do_id, $shift_id, $currentDate, $current_time, $time_seconds_current_time, $otFlow, $_SERVER['REMOTE_ADDR'] ?? '']);
                $conn->commit();
                $otMessage = $otFlow . ' recorded. Pending OT approval.';
                $otSuccess = true;
            }
        } catch (PDOException $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            error_log('RFID overtime log error: ' . $e->getMessage());
            $otMessage = 'Overtime scan could not be saved.';
            $otSuccess = false;
        }
        clearLastTag();
        $conn->prepare('UPDATE client_computer SET RFID_tag = ? WHERE ipAddress = ?')->execute(['', get_client_ip()]);
        echo '<div style="padding:16px;text-align:center"><strong>' . htmlspecialchars($lname . ', ' . $fname, ENT_QUOTES, 'UTF-8') . '</strong><br>' . htmlspecialchars($otMessage, ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<script>if (typeof ' . ($otSuccess ? 'PopupCenterValid' : 'PopupCenterInvalid') . ' === "function") ' . ($otSuccess ? 'PopupCenterValid' : 'PopupCenterInvalid') . '();</script>';
        exit;
    }
                
    $studSchedDataQuery = $conn->prepare('SELECT am_IN, am_IN_co, am_OUT, pm_IN, pm_IN_co, pm_OUT, type FROM time_schedules WHERE day = :day AND do_id = :do_id AND shift_id = :shift_id');
    $studSchedDataQuery->execute(['day' => $day, 'do_id' => $do_id, 'shift_id' => $shift_id]);
    
    
    
    if($studSchedDataQuery->rowCount()>0)
    {
        
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
                
                
                $am_in_log_query = $conn->prepare("SELECT logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND logFlow = 'AM IN' ORDER BY log_id DESC LIMIT 1");
                $am_in_log_query->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
                $ail_row=$am_in_log_query->fetch();
                
                $am_out_log_query = $conn->prepare("SELECT logTime, logTime_sec FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND logFlow = 'AM OUT' ORDER BY log_id DESC LIMIT 1");
                $am_out_log_query->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
                $aol_row=$am_out_log_query->fetch();
                
                $pm_in_log_query = $conn->prepare("SELECT logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND logFlow = 'PM IN' ORDER BY log_id DESC LIMIT 1");
                $pm_in_log_query->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
                $pil_row=$pm_in_log_query->fetch();
                
                $pm_out_log_query = $conn->prepare("SELECT logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND logFlow = 'PM OUT' ORDER BY log_id DESC LIMIT 1");
                $pm_out_log_query->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
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
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                       
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
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                           
                            //end am out
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
                            
                            clearLastTag();
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
                            clearLastTag();
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
                            
                             //clear tag data
                            clearLastTag();
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
                            clearLastTag();
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
                            
                            clearLastTag();
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
                    clearLastTag();
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
                            clearLastTag();
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
                            clearLastTag();
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
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
                        }  //end time allowance
                    } //end check pm out false
                    
                    
                //end check pm in true
                }else{
                //start check pm in false
                
                 
                            if($am_in_log_query->rowCount()>0 AND $am_out_log_query->rowCount()>0){
                     
                            //5 MINUTES LOG ALLOWANCE
                            $LOG_AM_OUT_timeAllowance=$aol_row['logTime_sec']+300;
                        
                            if($time_seconds_current_time>$LOG_AM_OUT_timeAllowance){
    
                            if($time_seconds_current_time<$time_seconds_pm_in AND $time_seconds_current_time>$time_seconds_am_out){ ?>
                                
                            
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
                            
                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            //end pm in            
                            }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time<$time_seconds_pm_out){
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
                            clearLastTag();
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
                            
                            clearLastTag();
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
                            clearLastTag();
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
                             
                            
                             //clear tag data
                            clearLastTag();
                            $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag, display_time = :display_time WHERE ipAddress = :ipAddress';
                            $conn->prepare($updClient)->execute(['RFID_tag' => '', 'display_time' => 0, 'ipAddress' => get_client_ip()]);
                            
                            //end pm in            
                            }elseif($time_seconds_current_time>=$time_seconds_pm_in_late AND $time_seconds_current_time<$time_seconds_pm_out){
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
                            clearLastTag();
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
                            
                            clearLastTag();
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
 
                
                
                  
                }
                
    }elseif($shift_id===777){
                
                
                
                $date_yesterday = new DateTime($currentDate.'- 1 day');
                $yesterDate=$date_yesterday->format('m/d/Y');
                $yesterDateYmd=$date_yesterday->format('Y-m-d');
                
                $log_ctr_query = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?)");
                $log_ctr_query->execute(array_merge($rfTagList, [$yesterDate, $yesterDateYmd]));
                $log_ctr=$log_ctr_query->rowcount();
                
                $mod_val=$log_ctr % 2;
                
                if($mod_val>0){
                //OUT
                
                $yesterday_in_log_query = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND (logFlow='AM IN' OR logFlow='PM IN') ORDER BY log_id DESC LIMIT 1");
                $yesterday_in_log_query->execute(array_merge($rfTagList, [$yesterDate, $yesterDateYmd]));
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
               
                            
                            $in_log_query = $conn->query("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id='$currentTag' AND logDate='$currentDate' AND (logFlow='AM IN' OR logFlow='PM IN') ORDER BY log_id DESC");
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
                        
                        $current_log_ctr_query = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?)");
                        $current_log_ctr_query->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
                        $current_log_ctr=$current_log_ctr_query->rowcount();
                        
                        $current_mod_val=$current_log_ctr % 2;
                        
                        if($current_mod_val>0){
                        //CURRENT DAY OUT OPEN
                        
                        $in_log_query = $conn->prepare("SELECT log_id, logTime FROM personnel_logs WHERE RFTag_id IN ($tagPlaceholders) AND logDate IN (?, ?) AND (logFlow='AM IN' OR logFlow='PM IN') ORDER BY log_id DESC LIMIT 1");
                        $in_log_query->execute(array_merge($rfTagList, [$currentDate, $currentDateMdy]));
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
    $updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag WHERE ipAddress = :ipAddress';
    $conn->prepare($updClient)->execute(['RFID_tag' => '', 'ipAddress' => get_client_ip()]);
                            
            
} }

$studSchedDataQuery=null;

$am_in_log_query=null;
$am_out_log_query=null;
$pm_in_log_query=null;
$pm_out_log_query=null;

$ail_row=null;
$aol_row=null;
$pil_row=null;
$pol_row=null;
            
$personnelData_query=null;
$ct_query=null;

$ct2_query=null;
$ct2_row=null;

$in_log_query=null;

$conn=null;

}

if (isset($_POST['log_source']) && $_POST['log_source'] == 'BIO') {
    include('dbcon.php');
    $conn->prepare("UPDATE personnel_logs SET log_source = 'BIO' WHERE RFTag_id = :rfTag ORDER BY log_id DESC LIMIT 1")->execute(['rfTag' => $currentTag]);
    $conn=null;
}
?>
