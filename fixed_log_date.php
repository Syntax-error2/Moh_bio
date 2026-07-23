<?php include('session.php');
include('dbcon.php');
/*
$upd_ctr = 0;
$transList_stmt = $conn->prepare("SELECT * FROM personnel_logs");
$transList_stmt->execute();
$transList_query = $transList_stmt;
while($tList_row=$transList_query->fetch()){ 
    
    $newDate = date('Y-m-d', strtotime($tList_row['logDate']));
    
    $update_stmt = $conn->prepare("UPDATE personnel_logs SET logDate = :logDate WHERE log_id = :log_id");
    $update_stmt->execute([':logDate' => $newDate, ':log_id' => $tList_row['log_id']]);
    
    $upd_ctr += 1;
    
    echo $tList_row['logDate']." changed to ".$newDate;
    echo "( ".$upd_ctr." )";
    echo "<br />";
}


$upd_ctr = 0;
$transList_stmt = $conn->prepare("SELECT * FROM activity_calendar");
$transList_stmt->execute();
$transList_query = $transList_stmt;
while($tList_row=$transList_query->fetch()){ 
    
    $newDate = date('Y-m-d', strtotime($tList_row['completeDate']));
    
    $update_stmt = $conn->prepare("UPDATE activity_calendar SET completeDate = :completeDate WHERE activity_id = :activity_id");
    $update_stmt->execute([':completeDate' => $newDate, ':activity_id' => $tList_row['activity_id']]);
    
    $upd_ctr += 1;
    
    echo $tList_row['completeDate']." changed to ".$newDate;
    echo "( ".$upd_ctr." )";
    echo "<br />";
}

*/

$upd_ctr = 0;
$transList_stmt = $conn->prepare("SELECT * FROM leave_applicants");
$transList_stmt->execute();
$transList_query = $transList_stmt;
while($tList_row=$transList_query->fetch()){ 
    
    $newDate = date('Y-m-d', strtotime($tList_row['leave_date']));
    
    $update_stmt = $conn->prepare("UPDATE leave_applicants SET leave_date = :leave_date WHERE lap_id = :lap_id");
    $update_stmt->execute([':leave_date' => $newDate, ':lap_id' => $tList_row['lap_id']]);
    
    $upd_ctr += 1;
    
    echo $tList_row['leave_date']." changed to ".$newDate;
    echo "( ".$upd_ctr." )";
    echo "<br />";
}

$conn=null;
?>
