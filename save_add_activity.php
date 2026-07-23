<?php

 
include('session.php');
include('dbcon.php');

?>


<?php

if(isset($_POST['addActivity']))
{
    
    $completeDate=substr($_POST['activity_date'], 5,2).'/'.substr($_POST['activity_date'], 8,2).'/'.substr($_POST['activity_date'], 0,4);
    
    $actMM=substr($_POST['activity_date'], 5,2);
    $actDD=substr($_POST['activity_date'], 8,2);
    $actYYYY=substr($_POST['activity_date'], 0,4);
    
    
    $title=$_POST['event_title'];
    $description=$_POST['event_description'];
    
    $act_type=$_POST['act_type'];
    $status=$_POST['status'];
  
 
    $insertStmt = $conn->prepare("INSERT INTO activity_calendar(actMM, actDD, actYYYY, completeDate, event_title, event_description, act_type, status) VALUES(:actMM, :actDD, :actYYYY, :completeDate, :title, :description, :act_type, :status)");
    $insertStmt->execute([
        ':actMM' => $actMM,
        ':actDD' => $actDD,
        ':actYYYY' => $actYYYY,
        ':completeDate' => $completeDate,
        ':title' => $title,
        ':description' => $description,
        ':act_type' => $act_type,
        ':status' => $status,
    ]);
    
?>

<script>
window.alert('Activity added successfully...');
window.location='institutional_calendar.php?mm=<?php echo $actMM; ?>&yyyy=<?php echo $actYYYY; ?>';
</script>

<?php } ?>

 

<?php

if(isset($_POST['editActivity']))
{

    $completeDate=$_POST['actMM'].'/'.$_POST['actDD'].'/'.$_POST['actYYYY'];
    
    $actMM=$_POST['actMM'];
    $actDD=$_POST['actDD'];
    $actYYYY=$_POST['actYYYY'];
    
    $title=$_POST['event_title'];
    $description=$_POST['event_description'];
    
    $act_type=$_POST['act_type'];
    $status=$_POST['status'];
  
 
    $activity_id = $_GET['activity_id'] ?? '';
    $updateStmt = $conn->prepare("UPDATE activity_calendar SET actMM = :actMM, actDD = :actDD, actYYYY = :actYYYY, completeDate = :completeDate, event_title = :title, event_description = :description, act_type = :act_type, status = :status WHERE activity_id = :activity_id");
    $updateStmt->execute([
        ':actMM' => $actMM,
        ':actDD' => $actDD,
        ':actYYYY' => $actYYYY,
        ':completeDate' => $completeDate,
        ':title' => $title,
        ':description' => $description,
        ':act_type' => $act_type,
        ':status' => $status,
        ':activity_id' => $activity_id,
    ]);
?>

<script>
window.alert('Activity updated successfully...');
window.location='institutional_calendar.php?mm=<?php echo $actMM; ?>&yyyy=<?php echo $actYYYY; ?>';
</script>

<?php } ?>


<?php

if(isset($_POST['deleteActivity']))
{   
    $activity_id=$_POST['activity_id'];
    
    $deleteStmt = $conn->prepare("DELETE FROM activity_calendar WHERE activity_id = :activity_id");
    $deleteStmt->execute([':activity_id' => $activity_id]);
?>

<script>
window.alert('Activity deleted successfully...');
window.location='institutional_calendar.php?mm=<?php echo $_GET['mm']; ?>&yyyy=<?php echo $_GET['yyyy']; ?>';
</script>

<?php } ?>
 
 


