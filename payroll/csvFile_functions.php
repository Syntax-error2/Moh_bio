<?php
/**
 * CSV Import/Export Functions for Personnel Logs
 * Refactored with PDO prepared statements for security
 */

include('dbcon3.php');

if(isset($_POST["Import"])){
    
    try {
        $updateCtr = 0;
        $insertCtr = 0;
        
        $filename = $_FILES["file"]["tmp_name"];
        
        if($_FILES["file"]["size"] > 0) {
            $file = fopen($filename, "r");
            
            // Prepare statements outside the loop for better performance
            $checkStmt = $conn3->prepare("SELECT RFTag_id FROM personnel_logs 
                WHERE RFTag_id = :rftag_id AND logDate = :log_date AND logFlow = :log_flow");
            
            $updateStmt = $conn3->prepare("UPDATE personnel_logs SET 
                RFTag_id = :rftag_id,
                img = :img,
                captured_img = :captured_img,
                lname = :lname,
                fname = :fname,
                mname = :mname,
                suffix = :suffix,
                do_id = :do_id,
                shift_id = :shift_id,
                logDate = :log_date,
                logTime = :log_time,
                logTime_sec = :log_time_sec,
                late_status = :late_status,
                logFlow = :log_flow,
                client_ip = :client_ip,
                remarks = :remarks,
                travel_leave_code = :travel_leave_code
            WHERE RFTag_id = :rftag_id_where AND logDate = :log_date_where AND logFlow = :log_flow_where");
            
            $insertStmt = $conn3->prepare("INSERT INTO personnel_logs
                (RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, 
                 logDate, logTime, logTime_sec, late_status, logFlow, client_ip, remarks, travel_leave_code)
            VALUES
                (:rftag_id, :img, :captured_img, :lname, :fname, :mname, :suffix, :do_id, :shift_id,
                 :log_date, :log_time, :log_time_sec, :late_status, :log_flow, :client_ip, :remarks, :travel_leave_code)");
            
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                
                // Skip header row if it exists
                if($getData[1] == 'RFTag_id' || empty($getData[1])) {
                    continue;
                }
                
                // Format date properly
                $old_date = date($getData[10]);
                $old_date_timestamp = strtotime($old_date);
                $new_date = date('m/d/Y', $old_date_timestamp);
                
                // Check if record exists
                $checkStmt->execute([
                    ':rftag_id' => $getData[1],
                    ':log_date' => $new_date,
                    ':log_flow' => $getData[14]
                ]);
                
                if ($checkStmt->rowCount() > 0) {
                    // Update existing record
                    $updateStmt->execute([
                        ':rftag_id' => $getData[1],
                        ':img' => $getData[2],
                        ':captured_img' => $getData[3],
                        ':lname' => $getData[4],
                        ':fname' => $getData[5],
                        ':mname' => $getData[6],
                        ':suffix' => $getData[7],
                        ':do_id' => $getData[8],
                        ':shift_id' => $getData[9],
                        ':log_date' => $new_date,
                        ':log_time' => $getData[11],
                        ':log_time_sec' => $getData[12],
                        ':late_status' => $getData[13],
                        ':log_flow' => $getData[14],
                        ':client_ip' => $getData[15],
                        ':remarks' => $getData[16],
                        ':travel_leave_code' => $getData[17],
                        ':rftag_id_where' => $getData[1],
                        ':log_date_where' => $new_date,
                        ':log_flow_where' => $getData[14]
                    ]);
                    
                    $updateCtr++;
                    
                } else {
                    // Insert new record
                    $insertStmt->execute([
                        ':rftag_id' => $getData[1],
                        ':img' => $getData[2],
                        ':captured_img' => $getData[3],
                        ':lname' => $getData[4],
                        ':fname' => $getData[5],
                        ':mname' => $getData[6],
                        ':suffix' => $getData[7],
                        ':do_id' => $getData[8],
                        ':shift_id' => $getData[9],
                        ':log_date' => $new_date,
                        ':log_time' => $getData[11],
                        ':log_time_sec' => $getData[12],
                        ':late_status' => $getData[13],
                        ':log_flow' => $getData[14],
                        ':client_ip' => $getData[15],
                        ':remarks' => $getData[16],
                        ':travel_leave_code' => $getData[17]
                    ]);
                    
                    $insertCtr++;
                }
            }
            
            // Delete header rows that might have been imported
            $deleteStmt = $conn3->prepare("DELETE FROM personnel_logs WHERE fname = :fname AND lname = :lname");
            $deleteStmt->execute([
                ':fname' => 'fname',
                ':lname' => 'lname'
            ]);
            
            fclose($file);
            
            ?>
            <script>
            alert("CSV File has been successfully Imported... \n Inserted Rows: <?php echo $insertCtr; ?> \n Updated Rows: <?php echo $updateCtr; ?>");
            window.location = 'csvFile_import.php';
            </script>
            <?php
            
        } else {
            ?>
            <script>
            alert("File is empty. Please upload a valid CSV file.");
            window.location = 'csvFile_import.php';
            </script>
            <?php
        }
        
    } catch (PDOException $e) {
        error_log("CSV Import Error: " . $e->getMessage());
        ?>
        <script>
        alert("An error occurred during import. Please check the file format and try again.");
        window.location = 'csvFile_import.php';
        </script>
        <?php
    } catch (Exception $e) {
        error_log("File Error: " . $e->getMessage());
        ?>
        <script>
        alert("Invalid File: Please Upload CSV File.");
        window.location = 'csvFile_import.php';
        </script>
        <?php
    }
}
  
  
  /* function get_all_records(){
    $conn3 = getdb();
    $Sql = "SELECT * FROM personnel_logs";
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
      $query = $conn3->prepare("SELECT * FROM personnel_logs 
            WHERE logDate BETWEEN :date_from AND :date_to 
            ORDER BY log_id DESC");
        
      $query->execute([
            ':date_from' => $logDateXportFrom,
            ':date_to' => $logDateXportTo
      ]);
        
      while($row = $query->fetch(PDO::FETCH_ASSOC))  
      {  
           fputcsv($output, $row);  
      }  
      fclose($output);  
 }   
    
 ?>