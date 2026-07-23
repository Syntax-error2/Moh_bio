 <?php


    include('dbcon.php');

    $img = $_POST['image'];
    $folderPath = "upload/";
  
    $image_parts = explode(";base64,", $img);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
  
    $image_base64 = base64_decode($image_parts[1]);
    $fileName = uniqid() . '.png';
  
    $file = $folderPath . $fileName;
    file_put_contents($file, $image_base64);
    
    
    $log_id = $_GET['log_id'] ?? '';
    $updateStmt = $conn->prepare("UPDATE personnel_logs SET captured_img = :captured_img WHERE log_id = :log_id");
    $updateStmt->execute([
        ':captured_img' => $fileName,
        ':log_id' => $log_id,
    ]);


?>

<script>
window.location='updateBlankLogFlow.php?toWindow=rlt1';
</script>
