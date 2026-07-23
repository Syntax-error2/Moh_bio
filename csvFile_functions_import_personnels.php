<?php

//error_reporting(0);
 
include('dbcon3.php');

     function randomcode() {
     $var = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
     srand((double)microtime()*1000000);
     $i = 0;
     $code = '';
     while ($i <= 9) {
     $num = rand() % 33;
     $tmp = substr($var, $num, 1);
     $code = $code . $tmp;
     $i++;
     }
     return $code;
     }
                             
     


 if(isset($_POST["Import"])){
     
    $updateCtr=0;
    $insertCtr=0;
    
    $filename=$_FILES["file"]["tmp_name"];  
    
    
     if($_FILES["file"]["size"] > 0)
     {
            $conn3->beginTransaction();
        $file = fopen($filename, "r");
          while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
          {
               
               if($getData[0] == null){

                    $RFTag_id = randomcode();

               }else{

                    $RFTag_id = $getData[0];

               }
               
               $personnel_id_code = $getData[1];

               $checkStmt = $conn3->prepare("SELECT personnel_id FROM personnels WHERE personnel_id_code = :personnel_id_code");
               $checkStmt->execute([':personnel_id_code' => $personnel_id_code]);
             
               if ($checkStmt->fetchColumn()){
               
                    /*
                    $sql = "UPDATE personnels 
               
               SET 
               
               img='default_img.jpg', 
               RFTag_id = '$RFTag_id',
               lname='".$getData[1]."', 
               fname='".$getData[2]."', 
               do_id='".$getData[3]."' 
               
               WHERE 
               
               personnel_id_code='$personnel_id_code'";
               */
                  $updateStmt = $conn3->prepare("UPDATE personnels SET RFTag_id = :RFTag_id WHERE personnel_id_code = :personnel_id_code");
                  $updateStmt->execute([
                       ':RFTag_id' => $RFTag_id,
                       ':personnel_id_code' => $personnel_id_code,
                  ]);
               
               $updateCtr=$updateCtr+1;
               
               }else{
               /*
                    $insertCtr=$insertCtr+1;
                    
                    $sql = "INSERT INTO personnels
                    
                    (RFTag_id, personnel_id_code, img, lname, fname, do_id) 
                    
                    VALUES 
                    
                    ('$RFTag_id','$RFTag_id','default_img.jpg','".$getData[0]."','".$getData[1]."','".$getData[2]."')";
                    
                    $result = mysqli_query($conn3, $sql);
                    */
               } 
          }
          
          $insertCtr=$insertCtr-1;
          $conn3->commit();
          
          //if(!isset($result)){ ?>
          
          <script>
               /*
          alert("Invalid File: Please Upload CSV File...");
          window.location = 'csvFile_import_personnels.php';
          */
          </script>
          
          <?php //}else{ ?>
          
           <script>
           alert("CSV File has been successfully Imported... \n Inserted Rows: <?php echo $insertCtr; ?> \n Updated Rows: <?php echo $updateCtr; ?>");
           window.location = 'csvFile_import_personnels.php';
           </script>
                          
          <?php  //}
                          
                $sql_del = "DELETE FROM personnels WHERE RFTag_id='RFTag_id'";
                $conn3->exec($sql_del);
          fclose($file);  
     }
  }
  
  
  /* function get_all_records(){
    $conn3 = getdb();
    $Sql = "SELECT * FROM personnels";
    $result = mysqli_query($conn3, $Sql);  
    if (mysqli_num_rows($result) > 0) {
     echo "<table id='example' class='table table-striped table-bordered'>
             <thead><tr>  
             
                          <th>EMP ID</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                          <th>Registration Date</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                          <th>Registration Date</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                          <th>Registration Date</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                        
                        </tr></thead><tbody>";
     while($row = mysqli_fetch_assoc($result)) {
         echo "<tr><td>" . $row['log_id']."</td>
                   <td>" . $row['RFTag_id']."</td>
                   <td>" . $row['img']."</td>
                   <td>" . $row['captured_img']."</td>
                   <td>" . $row['lname']."</td>
                   <td>" . $row['fname']."</td>
                   <td>" . $row['mname']."</td>
                   <td>" . $row['suffix']."</td>
                   <td>" . $row['do_id']."</td>
                   <td>" . $row['shift_id']."</td>
                   <td>" . $row['logDate']."</td>
                   <td>" . $row['logTime']."</td>
                   <td>" . $row['late_status']."</td>
                   <td>" . $row['logFlow']."</td>
                   <td>" . $row['client_ip']."</td>
                   <td>" . $row['remarks']."</td></tr>";        
     }
    
     echo "</tbody></table>";
     
} else {
     echo "you have no records";
}
} */
 

 if(isset($_POST["Export"])){
    
      $logDateXportFrom=substr($_POST['logDateFrom'], 5,2).'/'.substr($_POST['logDateFrom'], 8,2).'/'.substr($_POST['logDateFrom'], 0,4);
      $logDateXportTo=substr($_POST['logDateTo'], 5,2).'/'.substr($_POST['logDateTo'], 8,2).'/'.substr($_POST['logDateTo'], 0,4);
      
      header('Content-Type: text/csv; charset=utf-8');  
      header('Content-Disposition: attachment; filename=logs_'.$logDateXportFrom.'_to_'.$logDateXportTo.'.csv');  
      $output = fopen("php://output", "w");  
  
      
      
      fputcsv($output, array('log_id', 'RFTag_id', 'img', 'captured_img', 'lname', 'fname', 'mname', 'suffix', 'do_id', 'shift_id', 'logDate', 'logTime', 'logTime_sec', 'late_status', 'logFlow', 'client_ip', 'remarks', 'travel_leave_code', 'ref_log_id'));  
      $queryStmt = $conn3->prepare("SELECT * FROM personnels WHERE logDate BETWEEN :date_from AND :date_to ORDER BY log_id DESC");
      $queryStmt->execute([
          ':date_from' => $logDateXportFrom,
          ':date_to' => $logDateXportTo,
      ]);
      while($row = $queryStmt->fetch(PDO::FETCH_ASSOC))  
      {  
           fputcsv($output, $row);  
      }  
      fclose($output);  
 }   
    
 ?>