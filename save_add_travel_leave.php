 <?php

include('dbcon.php'); 


if(isset($_POST['add_travel'])){

$remarks=$_POST['remarks'];
  

$p1=0;

while($p1<$_POST['no_of_personnels']) //40 of 75 personnels...
{
    
    $n1=0;
    $p1=$p1+1;
    
    if($p1==1){
         
        $RFTag_id=substr($_POST['jud1'], 0,8);
    }
    
    
    if($p1==2){
      
        $RFTag_id=substr($_POST['jud2'], 0,8);
    }
    
    
    if($p1==3){
       
        $RFTag_id=substr($_POST['jud3'], 0,8);
    }
    
    
    if($p1==4){
        //2019-01-21 
        $RFTag_id=substr($_POST['jud4'], 0,8);
    }
    
    
    if($p1==5){
        
        $RFTag_id=substr($_POST['jud5'], 0,8);
    }
    
    if($p1==6){
        
        $RFTag_id=substr($_POST['jud6'], 0,8);
    }
    
    if($p1==7){
        
        $RFTag_id=substr($_POST['jud7'], 0,8);
    }
    
    if($p1==8){
        
        $RFTag_id=substr($_POST['jud8'], 0,8);
    }
    
    if($p1==9){
        
        $RFTag_id=substr($_POST['jud9'], 0,8);
    }
    
    if($p1==10){
        
        $RFTag_id=substr($_POST['jud10'], 0,8);
    }
    
    if($p1==11){
        
        $RFTag_id=substr($_POST['jud11'], 0,8);
    }
    
    if($p1==12){
        
        $RFTag_id=substr($_POST['jud12'], 0,8);
    }
    
    if($p1==13){
        
        $RFTag_id=substr($_POST['jud13'], 0,8);
    }
    
    if($p1==14){
        
        $RFTag_id=substr($_POST['jud14'], 0,8);
    }
    
    if($p1==15){
        
        $RFTag_id=substr($_POST['jud15'], 0,8);
    }
    
    if($p1==16){
        
        $RFTag_id=substr($_POST['jud16'], 0,8);
    }
    
    if($p1==17){
        
        $RFTag_id=substr($_POST['jud17'], 0,8);
    }
    
    if($p1==18){
        
        $RFTag_id=substr($_POST['jud18'], 0,8);
    }
    
    if($p1==19){
        
        $RFTag_id=substr($_POST['jud19'], 0,8);
    }
    
    if($p1==20){
        
        $RFTag_id=substr($_POST['jud20'], 0,8);
    }
    
    if($p1==21){
        
        $RFTag_id=substr($_POST['jud21'], 0,8);
    }
    
    if($p1==22){
        
        $RFTag_id=substr($_POST['jud22'], 0,8);
    }
    
    if($p1==23){
        
        $RFTag_id=substr($_POST['jud23'], 0,8);
    }
    
    if($p1==24){
        
        $RFTag_id=substr($_POST['jud24'], 0,8);
    }
    
    if($p1==25){
        
        $RFTag_id=substr($_POST['jud25'], 0,8);
    }
    
    if($p1==26){
        
        $RFTag_id=substr($_POST['jud26'], 0,8);
    }
    
    if($p1==27){
        
        $RFTag_id=substr($_POST['jud27'], 0,8);
    }
    
    if($p1==28){
        
        $RFTag_id=substr($_POST['jud28'], 0,8);
    }
    
    
    if($p1==29){
        
        $RFTag_id=substr($_POST['jud29'], 0,8);
    }
    
    if($p1==30){
        
        $RFTag_id=substr($_POST['jud30'], 0,8);
    }
    
    if($p1==31){
        
        $RFTag_id=substr($_POST['jud31'], 0,8);
    }
    
    if($p1==32){
        
        $RFTag_id=substr($_POST['jud32'], 0,8);
    }
    
    if($p1==33){
        
        $RFTag_id=substr($_POST['jud33'], 0,8);
    }
    
    
    if($p1==34){
        
        $RFTag_id=substr($_POST['jud34'], 0,8);
    }
    
    
    if($p1==35){
        
        $RFTag_id=substr($_POST['jud35'], 0,8);
    }
    
    if($p1==36){
        
        $RFTag_id=substr($_POST['jud36'], 0,8);
    }
    
    if($p1==37){
        
        $RFTag_id=substr($_POST['jud37'], 0,8);
    }
    
    if($p1==38){
        
        $RFTag_id=substr($_POST['jud38'], 0,8);
    }
    
    
    if($p1==39){
        
        $RFTag_id=substr($_POST['jud39'], 0,8);
    }
    
    
    if($p1==40){
        
        $RFTag_id=substr($_POST['jud40'], 0,8);
    }
    


$perData1_query = $conn->query("SELECT personnel_id, img, lname, fname, mname, suffix, do_id, shift_id FROM personnels WHERE RFTag_id='$RFTag_id'");
$pd1_row=$perData1_query->fetch();
        
$img='personnelImg/'.$pd1_row['img'];
        
while($n1<$_POST['no_of_days']) //5 of 125 days...
{
    
    $n1=$n1+1;
    
    if($n1==1){
        //2019-01-21
        /*
        $selectedMM=substr($_POST['con1'], 5,2);
        $selectedDD=substr($_POST['con1'], 8,2);
        $selectedYYYY=substr($_POST['con1'], 0,4);
        $logDate1=$selectedMM.'/'.$selectedDD.'/'.$selectedYYYY;
        $final_logDate=$logDate1.' - '.$selectedMM.'/'.$selectedDD.'/'.$selectedYYYY;
        */
        $final_logDate = $_POST['con1']." - ".$_POST['con1'];
        $logDate=$_POST['con1'];
    }
    
    if($n1==2){
        /*
        $selectedMM=substr($_POST['con2'], 5,2);
        $selectedDD=substr($_POST['con2'], 8,2);
        $selectedYYYY=substr($_POST['con2'], 0,4);
        $final_logDate=$logDate1.' - '.$selectedMM.'/'.$selectedDD.'/'.$selectedYYYY;
        */
        $final_logDate = $_POST['con2']." - ".$_POST['con2'];
        $logDate=$_POST['con2'];
    }
    
    if($n1==3){
        /*
        $selectedMM=substr($_POST['con3'], 5,2);
        $selectedDD=substr($_POST['con3'], 8,2);
        $selectedYYYY=substr($_POST['con3'], 0,4);
        $final_logDate=$logDate1.' - '.$selectedMM.'/'.$selectedDD.'/'.$selectedYYYY;
        */
        $final_logDate = $_POST['con3']." - ".$_POST['con3'];
        $logDate=$_POST['con3'];
    }
    
    
    if($n1==4){
        /*
        $selectedMM=substr($_POST['con4'], 5,2);
        $selectedDD=substr($_POST['con4'], 8,2);
        $selectedYYYY=substr($_POST['con4'], 0,4);
        $final_logDate=$logDate1.' - '.$selectedMM.'/'.$selectedDD.'/'.$selectedYYYY;
        */
        $final_logDate = $_POST['con4']." - ".$_POST['con4'];
        $logDate=$_POST['con4'];
    }
    
    
    if($n1==5){
        /*
        $selectedMM=substr($_POST['con5'], 5,2);
        $selectedDD=substr($_POST['con5'], 8,2);
        $selectedYYYY=substr($_POST['con5'], 0,4);
        $final_logDate=$logDate1.' - '.$selectedMM.'/'.$selectedDD.'/'.$selectedYYYY;
        */
        $final_logDate = $_POST['con5']." - ".$_POST['con5'];
        $logDate=$_POST['con5'];
    }
        
        
        //save to student logs
        $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks, travel_leave_code)
        VALUES ('$RFTag_id', '$img', '$pd1_row[lname]', '$pd1_row[fname]', '$pd1_row[mname]', '$pd1_row[suffix]', '$pd1_row[do_id]', '$pd1_row[shift_id]', '$logDate', '$remarks', '$_POST[travel_code]')");
  
  }
        
    
        $purpose_title=addslashes($_POST['purpose_title']);
        $description=addslashes($_POST['description']);
        $location_venue=addslashes($_POST['location_venue']);
        
        //ADD TO TRAVEL ORDER
        if(isset($_POST['add_to']))
        {
        $conn->query("INSERT INTO personnel_official_travel_logs(personnel_id, travel_code, purpose, description, location, travel_date, travel_type, numDays)
        VALUES ('$pd1_row[personnel_id]', '$_POST[travel_code]', '$purpose_title', '$description', '$location_venue', '$final_logDate', '$remarks', '$_POST[no_of_days]')");
        
        $conn->query("UPDATE travel_num_generator SET mm='".date('m')."', sequence='$_POST[new_tng_sequence]' WHERE pot_id=1");
        }
        
        //ADD TO 201 SEMINAR RECORDS
        if(isset($_POST['add_201_sr']))
        {
        $conn->query("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, entry_type)
        VALUES ('$pd1_row[personnel_id]', '$purpose_title', '$description', '$location_venue', '$final_logDate', '$_POST[travel_code]')");
        }
       
}

$perData1_query=null;
$conn=null;

?>
 
<script>
window.alert('DTR log with date: <?php echo $final_logDate; ?> successfully updated with Travel Entry');
window.location='list_travel_order.php?cw=list_travel'; 
</script>

<?php } ?>
 



<?php
//DELETE TRAVEL         DELETE TRAVEL           DELETE TRAVEL           DELETE TRAVEL

if(isset($_POST['deleteTravel'])){

$travel_code=$_GET['travel_code'];

$conn->query("DELETE FROM personnel_logs WHERE travel_leave_code='$_GET[travel_code]'");

$conn->query("DELETE FROM personnel_seminars WHERE entry_type='$_GET[travel_code]'");

$conn->query("DELETE FROM personnel_official_travel_logs WHERE travel_code='$_GET[travel_code]'");

 
$conn=null;

?>
 
<script>
window.alert('Travel Order <?php echo $_GET['travel_code']; ?> successfully deleted...');
window.location='list_travel_order.php?cw=list_travel'; 
</script>

<?php } ?>



<?php
//UPDATE TRAVEL         UPDATE TRAVEL           UPDATE TRAVEL           UPDATE TRAVEL

if(isset($_POST['updateTravel'])){

        $travel_code=$_GET['travel_code'];
        $remarks=$_POST['remarks'];
        
        $purpose_title=addslashes($_POST['purpose_title']);
        $description=addslashes($_POST['description']);
        $location_venue=addslashes($_POST['location_venue']);
        
        if($remarks=='SEMINAR'){
        
            $conn->query("UPDATE personnel_official_travel_logs SET  purpose='$purpose_title', description='$description', location='$location_venue', travel_type='$remarks' WHERE travel_code='$travel_code'");

            $psDataCHK_query = $conn->query("SELECT ps_id FROM personnel_seminars WHERE entry_type='$travel_code'");
            
            if($psDataCHK_query->rowCount()>0){
                $conn->query("UPDATE personnel_seminars SET seminar_title='$purpose_title', seminar_desc='$description', seminar_venue='$location_venue' WHERE entry_type='$travel_code'");
            
            }else{
                //ADD TO 201 SEMINAR RECORDS
                if(isset($_POST['add_201_sr']))
                {
                
                $event_date=$_POST['event_date'];
                    $perData1_query = $conn->query("SELECT personnel_id FROM personnel_official_travel_logs WHERE travel_code='$travel_code'");
                    while($pd1_row=$perData1_query->fetch()){
                        
                        $conn->query("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, entry_type)
                        VALUES ('$pd1_row[personnel_id]', '$purpose_title', '$description', '$location_venue', '$event_date', '$travel_code')");
                    
                    }

                
                }
            }

            
            
        
        }else{
            
            $conn->query("UPDATE personnel_official_travel_logs SET  purpose='$purpose_title', description='$description', location='$location_venue', travel_type='$remarks' WHERE travel_code='$travel_code'");

            $conn->query("DELETE FROM personnel_seminars WHERE entry_type='$travel_code'");
        
        }
        
        $conn->query("UPDATE personnel_logs SET remarks='$remarks' WHERE travel_leave_code='$travel_code'");
        
$conn=null;

?>
 
<script>
window.alert('Travel Order <?php echo $_GET['travel_code']; ?> details successfully updated...');
window.location='list_travel_order_detailed.php?travel_code=<?php echo $_GET['travel_code']; ?>';
</script>

<?php } ?>


<?php //############################## END OF TRAVEL ####### START OF LEAVE ################################################# ?>

<?php

//SAVE LEAVE ENTRY          SAVE LEAVE ENTRY                SAVE LEAVE ENTRY
        
if(isset($_POST['add_leave'])){

$remarks = $_POST['remarks'];

if($remarks == 1){
    
    $remarks=$_POST['leave_spec'];

}


$n1=0;

$applicant_rfid=substr($_POST['jud1'], 0,8);
$substitute_rfid=substr($_POST['substitute_rfid'], 0,8);
    

        $perData1_query = $conn->query("SELECT personnel_id, img, lname, fname, mname, suffix, do_id, shift_id FROM personnels WHERE RFTag_id='$applicant_rfid'");
        $pd1_row=$perData1_query->fetch();
        
        $img='personnelImg/'.$pd1_row['img'];
        
        $perData2_query = $conn->query("SELECT personnel_id FROM personnels WHERE RFTag_id='$substitute_rfid'");
        $pd2_row=$perData2_query->fetch();
        
        while ($n1 < $_POST['no_of_days']) {
            $n1++;
        
            // Dynamically access the corresponding 'con' value
            $logDate = $_POST["con$n1"] ?? null;
        
            if ($logDate) {
                // Prepare the queries
                /*
                $personnelLogsQuery = $conn->prepare("
                    INSERT INTO personnel_logs (RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks, travel_leave_code)
                    VALUES (:RFTag_id, :img, :lname, :fname, :mname, :suffix, :do_id, :shift_id, :logDate, :remarks, :travel_leave_code)
                ");
                */

                $leaveApplicantsQuery = $conn->prepare("
                    INSERT INTO leave_applicants (leave_code, leave_date, leave_type, leave_type_desc, substitute_id, applicant_id, do_id, numDays)
                    VALUES (:leave_code, :leave_date, :leave_type, :leave_type_desc, :substitute_id, :applicant_id, :do_id, :numDays)
                ");
                
                
                // Bind parameters and execute the queries
                /*
                $personnelLogsQuery->execute([
                    ':RFTag_id' => $applicant_rfid,
                    ':img' => $img,
                    ':lname' => $pd1_row['lname'],
                    ':fname' => $pd1_row['fname'],
                    ':mname' => $pd1_row['mname'],
                    ':suffix' => $pd1_row['suffix'],
                    ':do_id' => $pd1_row['do_id'],
                    ':shift_id' => $pd1_row['shift_id'],
                    ':logDate' => $logDate,
                    ':remarks' => $remarks,
                    ':travel_leave_code' => $_POST['leave_code']
                ]);
                */
                $leaveApplicantsQuery->execute([
                    ':leave_code' => $_POST['leave_code'],
                    ':leave_date' => $logDate,
                    ':leave_type' => $remarks,
                    ':leave_type_desc' => $_POST['leave_type_desc'],
                    ':substitute_id' => $pd2_row['personnel_id'],
                    ':applicant_id' => $pd1_row['personnel_id'],
                    ':do_id' => $pd1_row['do_id'],
                    ':numDays' => $_POST['no_of_days']
                ]);
            }
        }
  
$conn=null; 
   
?>
 
<script>
window.alert('Leave application addedd successfully and subject for approval...');
window.location='list_leave.php?cw=list_leave'; 
</script>

<?php } ?>


<?php
//DELETE LEAVE         DELETE LEAVE           DELETE LEAVE           DELETE LEAVE

if(isset($_POST['deleteLeave'])){

$new_clearance_query = $conn->query("SELECT * FROM leave_applicants WHERE lap_id='$_GET[lap_id]'");
$nc_row = $new_clearance_query->fetch();
 

$conn->query("DELETE FROM personnel_logs WHERE travel_leave_code='$nc_row[leave_code]'"); 

$conn->query("DELETE FROM leave_applicants WHERE lap_id	='$_GET[lap_id]'");
               
                 
$new_clearance_query=null;
$perData1_query=null;
$conn=null;
      
 ?>
 
<script>
window.alert('Leave Entry successfully deleted...');
window.location='list_leave.php?cw=list_leave'; 
</script>

<?php } ?>

 
 