 
 
<?php

include('dbcon.php');

 
    if($_GET['logFlow']==="AM IN" OR $_GET['logFlow']==="PM IN")
    {
        $lateStat=$_GET['lateStat'];
    }else{
        $lateStat="off";
    }

    $img = $_POST['image'];
    $folderPath = "upload/";
  
    $image_parts = explode(";base64,", $img);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
  
    $image_base64 = base64_decode($image_parts[1]);
    $fileName = uniqid() . '.png';
  
    $file = $folderPath . $fileName;
    file_put_contents($file, $image_base64);
    
    
    $conn->query("UPDATE personnel_logs SET captured_img='$fileName', logFlow='$_GET[logFlow]', late_status='$lateStat' WHERE RFTag_id='$_GET[RFTag_id]' AND log_id='$_GET[log_id]'") or die(mysql_error());

 
            if($_GET['logFlow']==='PM IN'){
                
            
            if($_GET['type']==='Night Shift'){
           
            $logCHK_query = $conn->query("SELECT * FROM personnel_logs WHERE log_id='$_GET[log_id]'") or die(mysql_error());
            $pl_row=$logCHK_query->fetch();

            $pm_in_date=strtotime($pl_row['logDate']); 
            $am_in_date=strtotime("+1 day", $pm_in_date);
            $am_in_logDate=date('m/d/Y', $am_in_date);
            
            //save to student logs
            $conn->query("INSERT INTO personnel_logs
            
            (RFTag_id, img, lname, fname, mname, suffix, gender, do_id, shift_id, logDate, logTime, logFlow, client_ip)
            
            VALUES
            
            ('$_GET[RFTag_id]', '$pl_row[img]', '$pl_row[lname]', '$pl_row[fname]', '$pl_row[mname]', '$pl_row[suffix]', '$pl_row[gender]', '$pl_row[do_id]', '$pl_row[shift_id]', '$pl_row[logDate]', '$_GET[pm_OUT]', 'PM OUT', '$pl_row[client_ip]'),
            ('$_GET[RFTag_id]', '$pl_row[img]', '$pl_row[lname]', '$pl_row[fname]', '$pl_row[mname]', '$pl_row[suffix]', '$pl_row[gender]', '$pl_row[do_id]', '$pl_row[shift_id]', '$am_in_logDate', '$_GET[am_IN]', 'AM IN', '$pl_row[client_ip]')"); 
 
            } }
            


 
?>

<script>
window.location='updateBlankLogFlow.php?toWindow=rlt1';
</script>
            
<?php include('scripts_files.php'); ?>