<?php

 
include('session.php');
include('dbcon.php');
 
$do_id=$_GET['do_id'];
$shift_id=$_GET['shift_id'];
$shift=$_GET['shift'];
$type=$_GET['type'];
  
?>


<?php

if(isset($_POST['addSchedule']))
{
    
  
   
    $am_in_hr=$_POST['am_in_hr'];
    $am_in_min=$_POST['am_in_min'];
    $am_in_ampm=strtoupper($_POST['am_in_ampm']);
    $am_IN=$am_in_hr.":".$am_in_min." ".$am_in_ampm;
    
    $am_in_hr_late=$_POST['am_in_hr_late'];
    $am_in_min_late=$_POST['am_in_min_late'];
    $am_in_ampm_late=strtoupper($_POST['am_in_ampm_late']);
    $am_IN_co=$am_in_hr_late.":".$am_in_min_late." ".$am_in_ampm_late; 
    
    
    $am_out_hr=$_POST['am_out_hr'];
    $am_out_min=$_POST['am_out_min'];
    $am_out_ampm=strtoupper($_POST['am_out_ampm']);
    $am_OUT=$am_out_hr.":".$am_out_min." ".$am_out_ampm;
    
    //$am_out_hr_late=$_POST['am_out_hr_late'];
    //$am_out_min_late=$_POST['am_out_min_late'];
    //$am_out_ampm_late=$_POST['am_out_ampm_late'];
    //$am_OUT_co=$am_out_hr_late.":".$am_out_min_late." ".$am_out_ampm_late; 
     
    $pm_in_hr=$_POST['pm_in_hr'];
    $pm_in_min=$_POST['pm_in_min'];
    $pm_in_ampm=strtoupper($_POST['pm_in_ampm']);
    $pm_IN=$pm_in_hr.":".$pm_in_min." ".$pm_in_ampm;
    
    $pm_in_hr_late=$_POST['pm_in_hr_late'];
    $pm_in_min_late=$_POST['pm_in_min_late'];
    $pm_in_ampm_late=strtoupper($_POST['pm_in_ampm_late']);
    $pm_IN_co=$pm_in_hr_late.":".$pm_in_min_late." ".$pm_in_ampm_late; 
     
    
    
    $pm_out_hr=$_POST['pm_out_hr'];
    $pm_out_min=$_POST['pm_out_min'];
    $pm_out_ampm=strtoupper($_POST['pm_out_ampm']);
    $pm_OUT=$pm_out_hr.":".$pm_out_min." ".$pm_out_ampm;
    
    //$pm_out_hr_late=$_POST['pm_out_hr_late'];
    //$pm_out_min_late=$_POST['pm_out_min_late'];
    //$pm_out_ampm_late=$_POST['pm_out_ampm_late'];
    //$pm_OUT_co=$pm_out_hr_late.":".$pm_out_min_late." ".$pm_out_ampm_late; 
    
    
    $day = $_POST['checkbox2'];
    
    $conflictCtr=0;
    $conflictDaysCtr="";
    $conflictFound = false;
    $conn->beginTransaction();
    for($j=0;$j<count($day);$j++)
    {
        
    $dayz = $day[$j];
 
    $checkbox = $_POST['checkbox'];

    for($i=0;$i<count($checkbox);$i++)
    {
        
    $cb_do_id = $checkbox[$i];
     
     
    $checkStmt = $conn->prepare("SELECT 1 FROM time_schedules WHERE day = :day AND do_id = :do_id AND shift_id = :shift_id LIMIT 1");
    $checkStmt->execute([
        ':day' => $dayz,
        ':do_id' => $cb_do_id,
        ':shift_id' => $shift_id,
    ]);
    if($checkStmt->fetchColumn())
    {
        
    $conflictCtr=$conflictCtr+1;
    $conflictDaysCtr=$conflictDaysCtr."[". $dayz ."] ";
    $conflictFound = true;
    
    }else{
 
    $insertStmt = $conn->prepare("INSERT INTO time_schedules(school_id, day, am_IN, am_IN_co, am_OUT, pm_IN, pm_IN_co, pm_OUT, do_id, shift_id, type) VALUES(:school_id, :day, :am_IN, :am_IN_co, :am_OUT, :pm_IN, :pm_IN_co, :pm_OUT, :do_id, :shift_id, :type)");
    $insertStmt->execute([
        ':school_id' => $school_id,
        ':day' => $dayz,
        ':am_IN' => $am_IN,
        ':am_IN_co' => $am_IN_co,
        ':am_OUT' => $am_OUT,
        ':pm_IN' => $pm_IN,
        ':pm_IN_co' => $pm_IN_co,
        ':pm_OUT' => $pm_OUT,
        ':do_id' => $cb_do_id,
        ':shift_id' => $shift_id,
        ':type' => $type,
    ]);
    
    
    }
    
    }
    
    }
?>

<?php
if($conflictFound)
{ ?>
<script>
window.alert('There are (<?php echo $conflictCtr; ?>) schedule conflicts... <?php echo $conflictDaysCtr; ?>. Those data was not saved.');
window.location='schedule_preferences.php?do_id=<?php echo $do_id; ?>&shift_id=<?php echo $shift_id; ?>&shift=<?php echo $shift; ?>&type=<?php echo $type; ?>'; 
</script>
<?php $conn->rollBack(); }else{ $conn->commit(); ?>
<script>
window.location='schedule_preferences.php?do_id=<?php echo $do_id; ?>&shift_id=<?php echo $shift_id; ?>&shift=<?php echo $shift; ?>&type=<?php echo $type; ?>'; 
</script>
<?php } } ?>





<?php

if(isset($_POST['updateTimeSched']))
{
 
    $am_in_hr=$_POST['am_in_hr'];
    $am_in_min=$_POST['am_in_min'];
    $am_in_ampm=strtoupper($_POST['am_in_ampm']);
    $am_IN=$am_in_hr.":".$am_in_min." ".$am_in_ampm;
    
    $am_in_hr_late=$_POST['am_in_hr_late'];
    $am_in_min_late=$_POST['am_in_min_late'];
    $am_in_ampm_late=strtoupper($_POST['am_in_ampm_late']);
    $am_IN_co=$am_in_hr_late.":".$am_in_min_late." ".$am_in_ampm_late; 
    
    
    $am_out_hr=$_POST['am_out_hr'];
    $am_out_min=$_POST['am_out_min'];
    $am_out_ampm=strtoupper($_POST['am_out_ampm']);
    $am_OUT=$am_out_hr.":".$am_out_min." ".$am_out_ampm;
 
 
    $pm_in_hr=$_POST['pm_in_hr'];
    $pm_in_min=$_POST['pm_in_min'];
    $pm_in_ampm=strtoupper($_POST['pm_in_ampm']);
    $pm_IN=$pm_in_hr.":".$pm_in_min." ".$pm_in_ampm;
    
    $pm_in_hr_late=$_POST['pm_in_hr_late'];
    $pm_in_min_late=$_POST['pm_in_min_late'];
    $pm_in_ampm_late=strtoupper($_POST['pm_in_ampm_late']);
    $pm_IN_co=$pm_in_hr_late.":".$pm_in_min_late." ".$pm_in_ampm_late; 
     
    
    
    $pm_out_hr=$_POST['pm_out_hr'];
    $pm_out_min=$_POST['pm_out_min'];
    $pm_out_ampm=strtoupper($_POST['pm_out_ampm']);
    $pm_OUT=$pm_out_hr.":".$pm_out_min." ".$pm_out_ampm;


    $schedule_id = $_POST['schedule_id'] ?? '';
    $updateStmt = $conn->prepare("UPDATE time_schedules SET am_IN = :am_IN, am_IN_co = :am_IN_co, am_OUT = :am_OUT, pm_IN = :pm_IN, pm_IN_co = :pm_IN_co, pm_OUT = :pm_OUT WHERE schedule_id = :schedule_id");
    $updateStmt->execute([
        ':am_IN' => $am_IN,
        ':am_IN_co' => $am_IN_co,
        ':am_OUT' => $am_OUT,
        ':pm_IN' => $pm_IN,
        ':pm_IN_co' => $pm_IN_co,
        ':pm_OUT' => $pm_OUT,
        ':schedule_id' => $schedule_id,
    ]);
                                                                                                                              
?>
<script>
window.alert('<?php echo $_POST['day']; ?> schedule updated successfully.');
window.location='schedule_preferences.php?do_id=<?php echo $do_id; ?>&shift_id=<?php echo $shift_id; ?>&shift=<?php echo $shift; ?>&type=<?php echo $type; ?>'; 
</script>
<?php } ?>





<?php

if(isset($_POST['deleteSched']))
{
    
    $schedule_id = $_POST['schedule_id'] ?? '';
    $deleteStmt = $conn->prepare("DELETE FROM time_schedules WHERE schedule_id = :schedule_id");
    $deleteStmt->execute([':schedule_id' => $schedule_id]);
?>


<script>
window.alert('<?php echo $_POST['day']; ?> schedule deleted successfully.');
window.location='schedule_preferences.php?do_id=<?php echo $do_id; ?>&shift_id=<?php echo $shift_id; ?>&shift=<?php echo $shift; ?>&type=<?php echo $type; ?>';  

</script>

<?php } ?>
 
