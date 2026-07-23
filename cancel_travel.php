 <?php include('session.php'); ?>
<?php include('dbcon.php'); ?>

<?php
if(isset($_POST['deletePersonnel'])){
    

$personnel_id=$_GET['personnel_id'];
$travel_code=$_GET['travel_code'];

$perData1_stmt = $conn->prepare("SELECT RFTag_id FROM personnels WHERE personnel_id = :personnel_id");
$perData1_stmt->execute([':personnel_id' => $personnel_id]);
$perData1_query = $perData1_stmt;
$pd1_row=$perData1_query->fetch();

   
                //save to student logs
                $delete_logs_stmt = $conn->prepare("DELETE FROM personnel_logs WHERE RFTag_id = :RFTag_id AND travel_leave_code = :travel_leave_code");
                $delete_logs_stmt->execute([':RFTag_id' => $pd1_row['RFTag_id'], ':travel_leave_code' => $travel_code]);
     
                //save to student logs
                $delete_travel_stmt = $conn->prepare("DELETE FROM personnel_official_travel_logs WHERE personnel_id = :personnel_id AND travel_code = :travel_code");
                $delete_travel_stmt->execute([':personnel_id' => $personnel_id, ':travel_code' => $travel_code]);
     
                //save to student logs
                $delete_seminar_stmt = $conn->prepare("DELETE FROM personnel_seminars WHERE personnel_id = :personnel_id AND entry_type = :entry_type");
                $delete_seminar_stmt->execute([':personnel_id' => $personnel_id, ':entry_type' => $travel_code]);
      
?>
 
<script>
window.alert('Personnel successfully removed from Travel Order <?php echo $travel_code; ?>...');
window.location='list_travel_order_detailed.php?travel_code=<?php echo $travel_code; ?>'; 
</script> 


<?php } ?>



<?php
if(isset($_POST['addPersonnel'])){

$travel_code=$_GET['travel_code'];
$RFTag_id=substr($_POST['personnel_RFTag_id'], 0, 8);
$logStat="Ok";

                    
                    $perData1_stmt = $conn->prepare("SELECT DISTINCT logDate, remarks FROM personnel_logs WHERE travel_leave_code = :travel_leave_code");
                    $perData1_stmt->execute([':travel_leave_code' => $travel_code]);
                    $perData1_query = $perData1_stmt;
                    while($pd1_row=$perData1_query->fetch()){
                    
                    $perData1CHK_stmt = $conn->prepare("SELECT log_id FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
                    $perData1CHK_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $pd1_row['logDate']]);
                    $perData1CHK_query = $perData1CHK_stmt;
                    if($perData1CHK_query->rowCount()>0){
                        $logStat="Exist";
                    }else{
                        
                        $perData2_stmt = $conn->prepare("SELECT personnel_id, img, lname, fname, mname, suffix, do_id, shift_id FROM personnels WHERE RFTag_id = :RFTag_id");
                        $perData2_stmt->execute([':RFTag_id' => $RFTag_id]);
                        $perData2_query = $perData2_stmt;
                        $pd2_row=$perData2_query->fetch();
                                
                        $img='personnelImg/'.$pd2_row['img'];
                        
                        $insert_log_stmt = $conn->prepare("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks, travel_leave_code)
                        VALUES (:RFTag_id, :img, :lname, :fname, :mname, :suffix, :do_id, :shift_id, :logDate, :remarks, :travel_leave_code)");
                        $insert_log_stmt->execute([
                            ':RFTag_id' => $RFTag_id,
                            ':img' => $img,
                            ':lname' => $pd2_row['lname'],
                            ':fname' => $pd2_row['fname'],
                            ':mname' => $pd2_row['mname'],
                            ':suffix' => $pd2_row['suffix'],
                            ':do_id' => $pd2_row['do_id'],
                            ':shift_id' => $pd2_row['shift_id'],
                            ':logDate' => $pd1_row['logDate'],
                            ':remarks' => $pd1_row['remarks'],
                            ':travel_leave_code' => $travel_code
                        ]);
                        
                        $logStat="Ok";
                        
                    }
                    
                    }
                
                        
                        $perData2_stmt = $conn->prepare("SELECT personnel_id FROM personnels WHERE RFTag_id = :RFTag_id");
                        $perData2_stmt->execute([':RFTag_id' => $RFTag_id]);
                        $perData2_query = $perData2_stmt;
                        $pd2_row=$perData2_query->fetch();
                        
                        $potLogsCHK_stmt = $conn->prepare("SELECT travel_log_id FROM personnel_official_travel_logs WHERE travel_code = :travel_code AND personnel_id = :personnel_id");
                        $potLogsCHK_stmt->execute([':travel_code' => $travel_code, ':personnel_id' => $pd2_row['personnel_id']]);
                        $potLogsCHK_query = $potLogsCHK_stmt;
                        if($potLogsCHK_query->rowCount()>0){
                          
                        }else{
                        $potLogs_stmt = $conn->prepare("SELECT travel_code, purpose, description, location, travel_date, travel_type, numDays FROM personnel_official_travel_logs WHERE travel_code = :travel_code");
                        $potLogs_stmt->execute([':travel_code' => $travel_code]);
                        $potLogs_query = $potLogs_stmt;
                        $pLogs_row=$potLogs_query->fetch();
                        
                        //ADD TO TRAVEL ORDER
                        $insert_travel_stmt = $conn->prepare("INSERT INTO personnel_official_travel_logs(personnel_id, travel_code, purpose, description, location, travel_date, travel_type, numDays)
                        VALUES (:personnel_id, :travel_code, :purpose, :description, :location, :travel_date, :travel_type, :numDays)");
                        $insert_travel_stmt->execute([
                            ':personnel_id' => $pd2_row['personnel_id'],
                            ':travel_code' => $travel_code,
                            ':purpose' => $pLogs_row['purpose'],
                            ':description' => $pLogs_row['description'],
                            ':location' => $pLogs_row['location'],
                            ':travel_date' => $pLogs_row['travel_date'],
                            ':travel_type' => $pLogs_row['travel_type'],
                            ':numDays' => $pLogs_row['numDays']
                        ]);

                        }
                                            
                        $pSemCHK_stmt = $conn->prepare("SELECT ps_id FROM personnel_seminars WHERE entry_type = :entry_type AND personnel_id = :personnel_id");
                        $pSemCHK_stmt->execute([':entry_type' => $travel_code, ':personnel_id' => $pd2_row['personnel_id']]);
                        $pSemCHK_query = $pSemCHK_stmt;
                        
                        if($pSemCHK_query->rowCount()>0){
                         
                        }else{
                            
                        $pSem_stmt = $conn->prepare("SELECT seminar_title, seminar_desc, seminar_venue, event_date FROM personnel_seminars WHERE entry_type = :entry_type");
                        $pSem_stmt->execute([':entry_type' => $travel_code]);
                        $pSem_query = $pSem_stmt;
                        $pSem_row=$pSem_query->fetch();
                        
                        //ADD TO 201 SEMINAR RECORDS
                        $insert_seminar_stmt = $conn->prepare("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, entry_type)
                        VALUES (:personnel_id, :seminar_title, :seminar_desc, :seminar_venue, :event_date, :entry_type)");
                        $insert_seminar_stmt->execute([
                            ':personnel_id' => $pd2_row['personnel_id'],
                            ':seminar_title' => $pSem_row['seminar_title'],
                            ':seminar_desc' => $pSem_row['seminar_desc'],
                            ':seminar_venue' => $pSem_row['seminar_venue'],
                            ':event_date' => $pSem_row['event_date'],
                            ':entry_type' => $travel_code
                        ]);
                        
                        
                        
                        }
                
                
                
                
                


if($logStat=="Ok"){
?>
 
<script>
window.alert('Personnel successfully added from Travel Order <?php echo $travel_code; ?>...');
window.location='list_travel_order_detailed.php?travel_code=<?php echo $travel_code; ?>'; 
</script> 


<?php 
}else{

?>
 
<script>
window.alert('Personnel has existing logs...');
window.location='list_travel_order_detailed.php?travel_code=<?php echo $travel_code; ?>'; 
</script> 


<?php } } ?>


 