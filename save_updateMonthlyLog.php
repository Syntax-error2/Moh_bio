
<?php include('session.php'); ?>
 
<?php 

    if(isset($_POST['updateDailyLog'])){
        
    $logDate=$_POST['logDate'];
    $RFTag_id=$_POST['RFTag_id'];
    $shift_id=$_POST['shift_id'];
    
    $am_in_lq=$_GET['am_in_lq'];

    if (!isset($_POST['am_in_late'])){ $late_status1="off"; }else{ $late_status1="on"; }
    
    $am_in_hr=$_POST['am_in_hr'];
    $am_in_min=$_POST['am_in_min'];
    $am_in_ampm=$_POST['am_in_ampm'];
    
    $am_IN=$am_in_hr.":".$am_in_min." ".$am_in_ampm;
    
    if($am_in_hr=='-' OR $am_in_min=='-' OR $am_in_ampm=='-'){
    
    $am_in_stat="No Log";
    
    }else{
    
    $am_in_stat="Updated";
    
        if($am_in_lq=="Update"){
            
        $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$am_IN', late_status='$late_status1', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate='$logDate' AND logFlow='AM IN'");    
        
        }else{
            
        $conn->query("INSERT INTO personnel_logs (RFTag_id, do_id, shift_id, logDate, logTime, logFlow, late_status, remarks)VALUES('$RFTag_id', '$_GET[dept]', '$shift_id', '$logDate', '$am_IN', 'AM IN', '$late_status1', 'Inserted')");    
        
        }
    
    }
    
    
    
    $am_out_lq=$_GET['am_out_lq'];
    
    if (!isset($_POST['am_out_undertime'])){ $late_status2="off"; }else{ $late_status2="on"; }
 
    $am_out_hr=$_POST['am_out_hr'];
    $am_out_min=$_POST['am_out_min'];
    $am_out_ampm=$_POST['am_out_ampm'];
    $am_OUT=$am_out_hr.":".$am_out_min." ".$am_out_ampm;
    
    if($am_out_hr=='-' OR $am_out_min=='-' OR $am_out_ampm=='-'){
        
    $am_out_stat="No Log";
    
    }else{
    
    $am_out_stat="Updated";
    
        if($am_out_lq=="Update"){
        
        $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$am_OUT', late_status='$late_status2', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate='$logDate' AND logFlow='AM OUT'");    
        
        }else{
        
        $conn->query("INSERT INTO personnel_logs (RFTag_id, do_id, shift_id, logDate, logTime, logFlow, late_status, remarks)VALUES('$RFTag_id', '$_GET[dept]', '$shift_id', '$logDate', '$am_OUT', 'AM OUT', '$late_status2', 'Inserted')");    
        
        }
        
    }
        
    
    
    
    
    $pm_in_lq=$_GET['pm_in_lq'];
    
    if (!isset($_POST['pm_in_late'])){ $late_status3="off"; }else{ $late_status3="on"; }
    
    $pm_in_hr=$_POST['pm_in_hr'];
    $pm_in_min=$_POST['pm_in_min'];
    $pm_in_ampm=$_POST['pm_in_ampm'];
    $pm_IN=$pm_in_hr.":".$pm_in_min." ".$pm_in_ampm;
    
    if($pm_in_hr=='-' OR $pm_in_min=='-' OR $pm_in_ampm=='-'){
        
    $pm_in_stat="No Log";
    
    }else{
    
    $pm_in_stat="Updated";
    
        if($pm_in_lq=="Update"){
        
        $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$pm_IN', late_status='$late_status3', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate='$logDate' AND logFlow='PM IN'");    
        
        }else{
            
        $conn->query("INSERT INTO personnel_logs (RFTag_id, do_id, shift_id, logDate, logTime, logFlow, late_status, remarks)VALUES('$RFTag_id', '$_GET[dept]', '$shift_id', '$logDate', '$pm_IN', 'PM IN', '$late_status3', 'Inserted')");    
        
        }
        
    }
    
    
    
    
    $pm_out_lq=$_GET['pm_out_lq'];
 
    if (!isset($_POST['pm_out_undertime'])){ $late_status4="off"; }else{ $late_status4="on"; }
    
    $pm_out_hr=$_POST['pm_out_hr'];
    $pm_out_min=$_POST['pm_out_min'];
    $pm_out_ampm=$_POST['pm_out_ampm'];
    $pm_OUT=$pm_out_hr.":".$pm_out_min." ".$pm_out_ampm;
    
    if($pm_out_hr=='-' OR $pm_out_min=='-' OR $pm_out_ampm=='-'){
        
    $pm_out_stat="No Log";
    
    }else{
    
    $pm_out_stat="Updated";
    
        if($pm_out_lq=="Update"){
            
        $conn->query("UPDATE personnel_logs SET do_id='$_GET[dept]', shift_id='$shift_id', logTime='$pm_OUT', late_status='$late_status4', remarks='Updated' WHERE RFTag_id='$RFTag_id' AND logDate='$logDate' AND logFlow='PM OUT'");    
        
        }else{
            
        $conn->query("INSERT INTO personnel_logs (RFTag_id, do_id, shift_id, logDate, logTime, logFlow, late_status, remarks)VALUES('$RFTag_id', '$_GET[dept]', '$shift_id', '$logDate', '$pm_OUT', 'PM OUT', '$late_status4', 'Inserted')");    
        
        }
    }
    
    
    ?>
    
    <script>
    window.alert('AM IN: <?php echo $am_in_stat.' | AM OUT: '.$am_out_stat.' | PM IN: '.$pm_in_stat.' | PM OUT: '.$pm_out_stat; ?> successfully logged for date: <?php echo $logDate; ?>');
    window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
    </script>
    
    <?php } ?>
    
    
<?php if(isset($_POST['saveRD'])){
    
    $studData_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'") or die(mysql_error());
    $sd_row=$studData_query->fetch();
 
    $logDate=$_POST['selectedMM'].'/'.$_POST['selectedDD'].'/'.$_POST['selectedYYYY'];
    
    $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks)
    VALUES ('$sd_row[RFTag_id]', '$sd_row[img]', '$sd_row[lname]', '$sd_row[fname]', '$sd_row[mname]', '$sd_row[suffix]', '$sd_row[do_id]', '$sd_row[shift_id]', '$logDate', 'REST DAY')");
        
    ?>
    
    <script>
    window.alert('Rest day successfully logged for date: <?php echo $logDate; ?>');
    window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
    </script>
    
<?php } ?>


<?php if(isset($_POST['delRD'])){

    $conn->query("DELETE FROM personnel_logs WHERE log_id='$_POST[log_id]'");
        
    ?>
    
    <script>
    window.alert('Rest day successfully deleted for date: <?php echo $_GET['logDate']; ?>');
    window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
    </script>
    
<?php } ?>
