<?php


 
include('session.php');
require_once('personnel_files_lib.php');

pfm_ensure_schema($conn);

$currentDate=date('m/d/Y');
$logTime=date('h:i:s A');
$dateTimeSave=$currentDate.' | '.$logTime;
$blank='';

?>


<?php


        function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
     
    
    if(get_client_ip()=="::1")
    {
        $machine_ip=gethostbyname(trim(`hostname`));  
    }else{
        $machine_ip=get_client_ip();
    }
    
    
if(isset($_POST['saveAddPersonnel']))
{

    $dept = $_GET['dept'] ?? 'All';

    function generateRandomRFTag($length = 10) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    
    /*if($_POST['user_rfid_type']==='With RFID'){
        
        $RFTag_id=$_POST['RFTag_id'];
        
    }else{
        
        
        
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
                                
        $RFTag_id='NRF'.substr(randomcode(), 0, 5);
        
    } */
    
    $personnel_id_code=trim($_POST['personnel_id_code']);
    $RFTag_id = '';

    for ($attempt = 0; $attempt < 50; $attempt++) {
        $candidateRFTag = generateRandomRFTag(10);

        if (strcasecmp($candidateRFTag, $personnel_id_code) === 0) {
            continue;
        }

        $rfidCheckStmt = $conn->prepare("SELECT personnel_id FROM personnels WHERE RFTag_id = :RFTag_id LIMIT 1");
        $rfidCheckStmt->execute([':RFTag_id' => $candidateRFTag]);

        if ($rfidCheckStmt->rowCount() === 0) {
            $RFTag_id = $candidateRFTag;
            break;
        }
    }

    if ($RFTag_id === '') {
        ?>
        <script>
        window.alert('Unable to generate RFID Tag. Please try again.');
        window.location='list_personnel.php?dept=<?php echo $dept; ?>';
        </script>
        <?php
        exit;
    }
    
    $folder="personnelImg/";
    $file_loc = '';
    $final_file = '';
    $hasUpload = isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']);

    if ($hasUpload) {
        $originalFileName = basename($_FILES['file']['name']);
        $file = $RFTag_id."-".$originalFileName;
        $file_loc = $_FILES['file']['tmp_name'];

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions, true)) {
            ?>
            <script>
            window.alert('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');
            window.location='list_personnel.php?dept=<?php echo $dept; ?>';
            </script>
            <?php
            exit;
        }

        $new_file_name = strtolower($file);
        $final_file = str_replace(' ','-',$new_file_name);
    }
    
    
    $lname=strtoupper($_POST['lname']);
    $fname=strtoupper($_POST['fname']);
    $mname=strtoupper($_POST['mname']);
    $suffix=strtoupper($_POST['suffix']);
    
    $perDataCHK_query = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id OR personnel_id_code = :personnel_id_code OR (fname = :fname AND mname = :mname AND lname = :lname)");
    $perDataCHK_query->execute([
        ':RFTag_id' => $RFTag_id,
        ':personnel_id_code' => $personnel_id_code,
        ':fname' => $fname,
        ':mname' => $mname,
        ':lname' => $lname
    ]);
    if($perDataCHK_query->rowCount()>0){
        
         ?>
 
        <script>
        window.alert('Name / RFID Tag / ID of employee already exist...');
        window.location='list_personnel.php?dept=<?php echo $dept; ?>'; 
        </script>    
        
        <?php

    }else{
        
    
    
    
    if($hasUpload && move_uploaded_file($file_loc,$folder.$final_file)){
        
        $insertPersonnel = $conn->prepare("INSERT INTO personnels(RFTag_id, personnel_id_code, img, lname, fname, mname, suffix, do_id)
        VALUES(:RFTag_id, :personnel_id_code, :img, :lname, :fname, :mname, :suffix, :do_id)");
        $insertPersonnel->execute([
            ':RFTag_id' => $RFTag_id,
            ':personnel_id_code' => $personnel_id_code,
            ':img' => $final_file,
            ':lname' => $lname,
            ':fname' => $fname,
            ':mname' => $mname,
            ':suffix' => $suffix,
            ':do_id' => $dept
        ]);

    }else{
        
        $insertPersonnel = $conn->prepare("INSERT INTO personnels(RFTag_id, personnel_id_code, lname, fname, mname, suffix, do_id)
        VALUES(:RFTag_id, :personnel_id_code, :lname, :fname, :mname, :suffix, :do_id)");
        $insertPersonnel->execute([
            ':RFTag_id' => $RFTag_id,
            ':personnel_id_code' => $personnel_id_code,
            ':lname' => $lname,
            ':fname' => $fname,
            ':mname' => $mname,
            ':suffix' => $suffix,
            ':do_id' => $dept
        ]);

    
    }
    
    $perData_query = $conn->prepare("SELECT personnel_id FROM personnels WHERE RFTag_id = :RFTag_id OR personnel_id_code = :personnel_id_code");
    $perData_query->execute([
        ':RFTag_id' => $RFTag_id,
        ':personnel_id_code' => $personnel_id_code
    ]);
    $pd_row=$perData_query->fetch();
    
     ?>
 
    <script>
    window.alert('<?php echo $fname.' '.$mname.' '.$lname; ?> added successfully... you will be redirected another page to fill-up complete personnel data...');
    window.location='edit_completePersonnelData.php?dept=<?php echo $dept; ?>&personnel_id=<?php echo $pd_row['personnel_id']; ?>'; 
    </script>    
    
    <?php } } ?>

 

<?php

if(isset($_POST['updatePersonnelComplete']))
{
    
    $personnel_id=$_POST['personnel_id'];
    
    $personnel_id_code=$_POST['personnel_id_code'];
    
    $RFTag_id=$_POST['RFTag_id'];
    
    $shift_id=$_POST['shift_id'];
    
    
    $lname=strtoupper($_POST['lname']);
    $fname=strtoupper($_POST['fname']);
    $mname=strtoupper($_POST['mname']);
    $suffix=strtoupper($_POST['suffix']);
    
    
    
    $sex=$_POST['sex'];
    $marital_status=$_POST['marital_status'];
    
    $birthdate_input = trim($_POST['birthdate'] ?? '');
    $bdMM='';
    $bdDD='';
    $bdYYYY='';
    $age=0;
    if($birthdate_input!=="" && $birthdate_input!=="  /  /    "){
        $birthdate_dt = DateTime::createFromFormat('m/d/Y', $birthdate_input);
        if($birthdate_dt && $birthdate_dt->format('m/d/Y') === $birthdate_input){
            $bdMM = $birthdate_dt->format('m');
            $bdDD = $birthdate_dt->format('d');
            $bdYYYY = $birthdate_dt->format('Y');

            $today = new DateTime();
            $age = $birthdate_dt->diff($today)->y;
        }
    }
    
                            
    $birth_place=$_POST['birth_place'];
    $address=$_POST['address'];
    
    
    $email=$_POST['email'];
    $personal_pnum=$_POST['personal_pnum'];
    
    $emergency_pnum=$_POST['emergency_pnum'];
    $conPerson_lname=strtoupper($_POST['conPerson_lname']);
    $conPerson_fname=strtoupper($_POST['conPerson_fname']);
    $conPerson_mname=strtoupper($_POST['conPerson_mname']);
    $conPerson_relationship=$_POST['conPerson_relationship'];


    $do_id=$_POST['do_id'];
    $des_id=$_POST['des_id'];
    $gass_id=$_POST['gass_id'];
    $empStat_id=$_POST['empStat_id'];
    
    
    $eligibility=$_POST['eligibility'];
    $plantilla_num=$_POST['plantilla_num'];
    
    $appointment_input = trim($_POST['appointment_date'] ?? '');
    $separation_input = trim($_POST['separation_date'] ?? '');

    $appointment_date = null;
    $separation_date = null;
    $num_of_yrs=0;

    if($appointment_input!=="" && $appointment_input!=="  /  /    "){
        $appointment_dt = DateTime::createFromFormat('m/d/Y', $appointment_input);
        if($appointment_dt && $appointment_dt->format('m/d/Y') === $appointment_input){
            $appointment_date = $appointment_dt->format('m/d/Y');
            $num_of_yrs = $appointment_dt->diff(new DateTime())->y;
        }
    }

    if($separation_input!=="" && $separation_input!=="  /  /    "){
        $separation_dt = DateTime::createFromFormat('m/d/Y', $separation_input);
        if($separation_dt && $separation_dt->format('m/d/Y') === $separation_input){
            $separation_date = $separation_dt->format('m/d/Y');
        }
    }
    
    $tin_num=$_POST['tin_num'];
    $gsis_num=$_POST['gsis_num'];
    $pagibig_num=$_POST['pagibig_num'];
    $philHealth_num=$_POST['philHealth_num'];
    
    try {
        $update_stmt = $conn->prepare("UPDATE personnels SET
            RFTag_id = :RFTag_id,
            personnel_id_code = :personnel_id_code,
            shift_id = :shift_id,
            lname = :lname,
            fname = :fname,
            mname = :mname,
            suffix = :suffix,
            age = :age,
            sex = :sex,
            marital_status = :marital_status,
            bdMM = :bdMM,
            bdDD = :bdDD,
            bdYYYY = :bdYYYY,
            birth_place = :birth_place,
            address = :address,
            email = :email,
            personal_pnum = :personal_pnum,
            emergency_pnum = :emergency_pnum,
            conPerson_lname = :conPerson_lname,
            conPerson_fname = :conPerson_fname,
            conPerson_mname = :conPerson_mname,
            conPerson_relationship = :conPerson_relationship,
            do_id = :do_id,
            des_id = :des_id,
            gass_id = :gass_id,
            empStat_id = :empStat_id,
            eligibility = :eligibility,
            plantilla_num = :plantilla_num,
            appointment_date = :appointment_date,
            separation_date = :separation_date,
            num_of_yrs = :num_of_yrs,
            tin_num = :tin_num,
            gsis_num = :gsis_num,
            pagibig_num = :pagibig_num,
            philHealth_num = :philHealth_num
        WHERE personnel_id = :personnel_id");
        
        $update_stmt->execute([
            ':RFTag_id' => $RFTag_id,
            ':personnel_id_code' => $personnel_id_code,
            ':shift_id' => $shift_id,
            ':lname' => $lname,
            ':fname' => $fname,
            ':mname' => $mname,
            ':suffix' => $suffix,
            ':age' => $age,
            ':sex' => $sex,
            ':marital_status' => $marital_status,
            ':bdMM' => $bdMM,
            ':bdDD' => $bdDD,
            ':bdYYYY' => $bdYYYY,
            ':birth_place' => $birth_place,
            ':address' => $address,
            ':email' => $email,
            ':personal_pnum' => $personal_pnum,
            ':emergency_pnum' => $emergency_pnum,
            ':conPerson_lname' => $conPerson_lname,
            ':conPerson_fname' => $conPerson_fname,
            ':conPerson_mname' => $conPerson_mname,
            ':conPerson_relationship' => $conPerson_relationship,
            ':do_id' => $do_id,
            ':des_id' => $des_id,
            ':gass_id' => $gass_id,
            ':empStat_id' => $empStat_id,
            ':eligibility' => $eligibility,
            ':plantilla_num' => $plantilla_num,
            ':appointment_date' => $appointment_date,
            ':separation_date' => $separation_date,
            ':num_of_yrs' => $num_of_yrs,
            ':tin_num' => $tin_num,
            ':gsis_num' => $gsis_num,
            ':pagibig_num' => $pagibig_num,
            ':philHealth_num' => $philHealth_num,
            ':personnel_id' => $personnel_id
        ]);
    } catch (PDOException $e) {
        error_log("Error updating personnel complete data: " . $e->getMessage());
        die("Error updating personnel information. Please try again.");
    }
    
  ?>
  
<script>

window.alert('<?php echo $fname.' '.$mname.' '.$lname; ?> Personal Information updated successfully...');
window.location='list_personnel_individual_details.php?dept=<?php echo $do_id; ?>&personnel_id=<?php echo $personnel_id; ?>'; </script>    

<?php } ?>


<?php

if(isset($_POST['updateStudentImg']))
{
    
    
    $personnel_id=$_POST['personnel_id'];
    
    // Validate personnel_id
    if (empty($personnel_id) || !is_numeric($personnel_id)) {
        die("Invalid personnel ID");
    }
    
    $file = $_POST['RFTag_id']."-".$_FILES['file']['name'];
    
    $file_loc = $_FILES['file']['tmp_name'];
 
	$folder="personnelImg/";
	
	// make file name in lower case
	$new_file_name = strtolower($file);
	// make file name in lower case
    
    // Validate file extension
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($new_file_name, PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        die("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
    }
    
    $final_file=str_replace(' ','-',$new_file_name);
        
        
     
    if(move_uploaded_file($file_loc,$folder.$final_file)){
        
        try {
            $update_img_stmt = $conn->prepare("UPDATE personnels SET img = :img WHERE personnel_id = :personnel_id");
            $update_img_stmt->execute([
                ':img' => $final_file,
                ':personnel_id' => $personnel_id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating personnel image: " . $e->getMessage());
            die("Error updating image. Please try again.");
        }

?>
<script> window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>'; </script>    

    <?php }else{ ?>
        
        <script>
        window.alert("Error uploading image. Please try again.");
        window.location='updateStudentImg.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $personnel_id; ?>';
        </script> 
    
    <?php } } ?>


<?php

if(isset($_POST['save_add201File']))
{
    $personnel_id = (int)($_POST['personnel_id'] ?? 0);
    $folder_id = (int)($_POST['folder_id'] ?? 0);
    $files_backlink = 'list_personnel_individual_details_files.php?dept=' . urlencode((string)($_GET['dept'] ?? '')) . '&personnel_id=' . $personnel_id;

    if (!pfm_can_manage_personnel_files($session_access, $user_personnel_id, $personnel_id)) {
        ?>
        <script>
        window.alert('You are not allowed to manage files for this personnel.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $folderStmt = $conn->prepare("SELECT folder_id, folder_name, folder_slug, is_system_201 FROM personnel_file_folders WHERE folder_id = :folder_id AND personnel_id = :personnel_id LIMIT 1");
    $folderStmt->execute([
        ':folder_id' => $folder_id,
        ':personnel_id' => $personnel_id,
    ]);
    $folderRow = $folderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$folderRow) {
        ?>
        <script>
        window.alert('Selected folder not found.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    if ((int)$folderRow['is_system_201'] === 1 && !pfm_is_admin($session_access)) {
        ?>
        <script>
        window.alert('Only Administrator / HR Head can upload files to 201-files.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    if (!isset($_FILES['per_file'])) {
        ?>
        <script>
        window.alert('No file uploaded.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $uploadError = (int)($_FILES['per_file']['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadError !== UPLOAD_ERR_OK) {
        ?>
        <script>
        window.alert('Upload failed. Please try again.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    if (!is_uploaded_file($_FILES['per_file']['tmp_name'])) {
        ?>
        <script>
        window.alert('Invalid upload source.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $maxUploadBytes = 10 * 1024 * 1024;
    $uploadedSize = (int)($_FILES['per_file']['size'] ?? 0);
    if ($uploadedSize <= 0 || $uploadedSize > $maxUploadBytes) {
        ?>
        <script>
        window.alert('File must be greater than 0 bytes and up to 10MB only.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $originalName = basename($_FILES['per_file']['name']);
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
    $ext = strtolower((string)pathinfo($safeName, PATHINFO_EXTENSION));
    $allowed = pfm_allowed_extensions();
    if (!in_array($ext, $allowed, true)) {
        ?>
        <script>
        window.alert('Invalid file type. Allowed: PDF, Office docs, images, and TXT.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $mimeType = pfm_detect_mime_type($_FILES['per_file']['tmp_name']);
    if (!pfm_is_valid_extension_mime($ext, $mimeType)) {
        ?>
        <script>
        window.alert('Invalid file content type for the selected extension.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $target_dir = 'personnelFiles/' . $personnel_id . '/' . $folderRow['folder_slug'] . '/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0775, true);
    }

    $storedName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $safeName;
    $target_file = $target_dir . $storedName;

    if (move_uploaded_file($_FILES['per_file']['tmp_name'], $target_file)) {
        $insertStmt = $conn->prepare("INSERT INTO files(personnel_id, folder_id, uploaded_by_personnel_id, uploaded_by_access, file_name, file_type, date_time_uploaded)
                                      VALUES(:personnel_id, :folder_id, :uploaded_by_personnel_id, :uploaded_by_access, :file_name, :file_type, :date_time_uploaded)");
        $insertStmt->execute([
            ':personnel_id' => $personnel_id,
            ':folder_id' => (int)$folderRow['folder_id'],
            ':uploaded_by_personnel_id' => (int)$user_personnel_id,
            ':uploaded_by_access' => $session_access,
            ':file_name' => $target_file,
            ':file_type' => $ext,
            ':date_time_uploaded' => $dateTimeSave,
        ]);

        $newFileId = (int)$conn->lastInsertId();
        pfm_log_action(
            $conn,
            'upload_file',
            (int)$user_personnel_id,
            $session_access,
            $personnel_id,
            (int)$folderRow['folder_id'],
            $newFileId,
            'file_name=' . basename($target_file) . '; mime=' . $mimeType . '; size=' . $uploadedSize
        );
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    
 
?>

<script> window.location='<?php echo $files_backlink; ?>'; </script>    


<?php } ?>





<?php

if(isset($_POST['set_shift']))
{
    
    $personnel_id = $_POST['personnel_id'];
    $shift_id = $_POST['shift_id'];
    
    $updateShift = $conn->prepare("UPDATE personnels SET shift_id = :shift_id WHERE personnel_id = :personnel_id");
    $updateShift->execute([
        ':shift_id' => $shift_id,
        ':personnel_id' => $personnel_id
    ]);
    
 
?>

<script>
window.alert('Shift updated successfully...');
window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
</script>    


<?php } ?>


<?php

if(isset($_POST['updateStudentRFIDTag']))
{
    
    $RFTag_id=$_POST['RFTag_id'];
    $currentRFIDTag=$_POST['currentRFIDTag'];
    $personnel_id = $_GET['personnel_id'] ?? '';
    
    
    $studDataCHK_query = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id");
    $studDataCHK_query->execute([':RFTag_id' => $RFTag_id]);
    if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('RFID Tag of employee already exist...');
        window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>'; 
        </script>    
        
        <?php
    }else{
        $updateRFID = $conn->prepare("UPDATE personnels SET RFTag_id = :RFTag_id WHERE personnel_id = :personnel_id");
        $updateRFID->execute([
            ':RFTag_id' => $RFTag_id,
            ':personnel_id' => $personnel_id
        ]);
    
        $updateLogs = $conn->prepare("UPDATE personnel_logs SET RFTag_id = :new_RFTag_id WHERE RFTag_id = :old_RFTag_id");
        $updateLogs->execute([
            ':new_RFTag_id' => $RFTag_id,
            ':old_RFTag_id' => $currentRFIDTag
        ]);
        
        ?>
          
        <script>
        window.alert('RFID Tag of employee updated successfully...');
        window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
        </script>    
         
        <?php } } ?>



<?php
//UPDATE USER LOGIN SETTINGS
if(isset($_POST['updateLoginSettings']))
{
    
    
    $dept=$_GET['dept'];
    $personnel_id=$_GET['personnel_id'];
    $username=$_POST['username'];
    $password=$_POST['password'];
    
    $safe_pass=md5($password);
    $salt="a1Bz20ydqelm8m1wql";
    $final_pass=$salt.$safe_pass;
    
    $safe_pass_check_pass=md5($_POST['current_password']);
    $salt="a1Bz20ydqelm8m1wql";
    $final_current_pass=$salt.$safe_pass_check_pass;
    
    $currentPassStmt = $conn->prepare("SELECT password FROM useraccount WHERE personnel_id = :personnel_id LIMIT 1");
    $currentPassStmt->execute([':personnel_id' => $personnel_id]);
    $currentPassRow = $currentPassStmt->fetch();
    $check_pass = $currentPassRow['password'] ?? '';

    if($check_pass===$final_current_pass){
        
    $perDataCHK_query = $conn->prepare("SELECT * FROM useraccount WHERE username = :username AND password = :password");
    $perDataCHK_query->execute([
        ':username' => $username,
        ':password' => $final_pass
    ]);
    if($perDataCHK_query->rowCount()>0){
        
         ?>
 
        <script>
        window.alert('Username and Password already exist...');
        window.location='user_profile.php?cw=UserProfile&dept=<?php echo $dept; ?>&personnel_id=<?php echo $personnel_id; ?>'; 
        </script>    
        
        <?php

    }else{  
         
         
    $updateUserAccount = $conn->prepare("UPDATE useraccount SET do_id = :do_id, username = :username, password = :password WHERE personnel_id = :personnel_id");
    $updateUserAccount->execute([
        ':do_id' => $dept,
        ':username' => $username,
        ':password' => $final_pass,
        ':personnel_id' => $personnel_id
    ]);
    
    
    ?>
    
        <script>
        window.alert('Login settings successfully updated...');
        window.location='user_profile.php?cw=UserProfile&dept=<?php echo $dept; ?>&personnel_id=<?php echo $personnel_id; ?>'; 
        </script>    
    
    
    
    <?php } }else{ ?>
 
        <script>
        window.alert('Yuor old password did not matched...');
        window.location='user_profile.php?cw=UserProfile&dept=<?php echo $dept; ?>&personnel_id=<?php echo $personnel_id; ?>'; 
        </script>    
        
 <?php } } ?>
        
        
<?php

//ADD EMPLOYEE EDUCATIONAL BG
if(isset($_POST['add_educ_bg']))
{

        $personnel_id = $_POST['personnel_id'];
        $course_details = $_POST['course_details'];
        $year_grad = $_POST['year_grad'];
        $school_name = $_POST['school_name'];
        $degree = $_POST['degree'];
        $units = $_POST['units'];

        $studDataCHK_query = $conn->prepare("SELECT * FROM personnel_educ_bg WHERE personnel_id = :personnel_id AND course_details = :course_details AND year_grad = :year_grad AND school_name = :school_name");
        $studDataCHK_query->execute([
            ':personnel_id' => $personnel_id,
            ':course_details' => $course_details,
            ':year_grad' => $year_grad,
            ':school_name' => $school_name
        ]);
        if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('Educational attainment already exist...');
        window.location='list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
        
        <?php
        
        }else{
            
        $insertEducBg = $conn->prepare("INSERT INTO personnel_educ_bg(personnel_id, degree, course_details, units, year_grad, school_name) VALUES (:personnel_id, :degree, :course_details, :units, :year_grad, :school_name)");
        $insertEducBg->execute([
            ':personnel_id' => $personnel_id,
            ':degree' => $degree,
            ':course_details' => $course_details,
            ':units' => $units,
            ':year_grad' => $year_grad,
            ':school_name' => $school_name
        ]);
        
        ?>
          
        <script>
        window.alert('Educational attainment added successfully...');
        window.location='list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
         
        <?php } } ?>
        
 

<?php

//ADD TO 201 SEMINAR RECORDS
if(isset($_POST['add_seminar']))
{

        $personnel_id = $_POST['personnel_id'];
        $purpose_title = $_POST['purpose_title'];
        $description = $_POST['description'];
        $location_venue = $_POST['location_venue'];
        $event_date=$_POST['sem_date_from'].' - '.$_POST['sem_date_to'];
        
        $studDataCHK_query = $conn->prepare("SELECT * FROM personnel_seminars WHERE personnel_id = :personnel_id AND seminar_title = :seminar_title AND event_date = :event_date");
        $studDataCHK_query->execute([
            ':personnel_id' => $personnel_id,
            ':seminar_title' => $purpose_title,
            ':event_date' => $event_date
        ]);
        if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('Seminar already exist...');
        window.location='list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
        
        <?php
        
        }else{
        
        
        $insertSeminar = $conn->prepare("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, entry_type) VALUES (:personnel_id, :seminar_title, :seminar_desc, :seminar_venue, :event_date, 'Manual Encode')");
        $insertSeminar->execute([
            ':personnel_id' => $personnel_id,
            ':seminar_title' => $purpose_title,
            ':seminar_desc' => $description,
            ':seminar_venue' => $location_venue,
            ':event_date' => $event_date
        ]);
        
        ?>
          
        <script>
        window.alert('Seminar added successfully...');
        window.location='list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
         
        <?php } } ?>
        
        


<?php

//ADD SERVICE RECORDS
if(isset($_POST['add_servRecord']))
{
 
        $personnel_id = $_POST['personnel_id'];
        $serv_date_from = $_POST['serv_date_from'];
        $serv_date_to = $_POST['serv_date_to'];
 
        $studDataCHK_query = $conn->prepare("SELECT * FROM service_record WHERE personnel_id = :personnel_id AND serv_date_from = :serv_date_from AND serv_date_to = :serv_date_to");
        $studDataCHK_query->execute([
            ':personnel_id' => $personnel_id,
            ':serv_date_from' => $serv_date_from,
            ':serv_date_to' => $serv_date_to
        ]);
        if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('Service Record data already exist...');
        window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
        
        <?php
        
        }else{
        
        $maid_lname = $_POST['maid_lname'];
        $maid_fname = $_POST['maid_fname'];
        $maid_mname = $_POST['maid_mname'];
        $roa_designation = $_POST['roa_designation'];
        $roa_status = $_POST['roa_status'];
        $office_appointment = addslashes($_POST['office_appointment']);
        $separate_date = $_POST['separate_date'];
        $separate_cause = $_POST['separate_cause'];
        $monthly_salary = isset($_POST['monthly_salary']) ? $_POST['monthly_salary'] : 0;
        $annual_salary = isset($_POST['annual_salary']) ? $_POST['annual_salary'] : 0;
        
        $insertServRecord = $conn->prepare("INSERT INTO service_record(personnel_id, maid_lname, maid_fname, maid_mname, serv_date_from, serv_date_to, roa_designation, roa_status, monthly_salary, annual_salary, office_appointment, separate_date, separate_cause) VALUES (:personnel_id, :maid_lname, :maid_fname, :maid_mname, :serv_date_from, :serv_date_to, :roa_designation, :roa_status, :monthly_salary, :annual_salary, :office_appointment, :separate_date, :separate_cause)");
        $insertServRecord->execute([
            ':personnel_id' => $personnel_id,
            ':maid_lname' => $maid_lname,
            ':maid_fname' => $maid_fname,
            ':maid_mname' => $maid_mname,
            ':serv_date_from' => $serv_date_from,
            ':serv_date_to' => $serv_date_to,
            ':roa_designation' => $roa_designation,
            ':roa_status' => $roa_status,
            ':monthly_salary' => $monthly_salary,
            ':annual_salary' => $annual_salary,
            ':office_appointment' => $office_appointment,
            ':separate_date' => $separate_date,
            ':separate_cause' => $separate_cause
        ]);
        
        // Sync monthly_salary to personnels table
        require_once 'sync_monthly_salary.php';
        $sync = new MonthlySalarySync($conn);
        $sync->syncPersonnel($personnel_id);
        
        ?>
          
        <script>
        window.alert('Service Record added successfully...');
        window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
         
        <?php } } ?>
        
        
        
        
        
<?php

if(isset($_POST['deleteStudent']))
{
    $personnel_id=$_POST['personnel_id'];

    $archiveDate = date('m/d/Y');
    $archive_stmt = $conn->prepare("UPDATE personnels SET separation_date = :separation_date WHERE personnel_id = :personnel_id");
    $archive_stmt->execute([
        ':separation_date' => $archiveDate,
        ':personnel_id' => $personnel_id
    ]);
 
?>

<script>
window.alert('Personnel archived successfully...');
window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
</script>    


<?php } ?>


<?php

if(isset($_POST['save_createFileFolder']))
{
    $personnel_id = (int)($_POST['personnel_id'] ?? 0);
    $folder_name = trim($_POST['folder_name'] ?? '');
    $files_backlink = 'list_personnel_individual_details_files.php?dept=' . urlencode((string)($_GET['dept'] ?? '')) . '&personnel_id=' . $personnel_id;

    if (!pfm_can_manage_personnel_files($session_access, $user_personnel_id, $personnel_id)) {
        ?>
        <script>
        window.alert('You are not allowed to create folders for this personnel.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    if ($folder_name === '') {
        ?>
        <script>
        window.alert('Folder name is required.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $folder_slug = pfm_slugify($folder_name);
    if ($folder_slug === '201-files') {
        ?>
        <script>
        window.alert('Folder name 201-files is reserved.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $chk = $conn->prepare("SELECT folder_id FROM personnel_file_folders WHERE personnel_id = :personnel_id AND folder_slug = :folder_slug LIMIT 1");
    $chk->execute([
        ':personnel_id' => $personnel_id,
        ':folder_slug' => $folder_slug,
    ]);

    if ($chk->fetch()) {
        ?>
        <script>
        window.alert('Folder already exists.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $insert = $conn->prepare("INSERT INTO personnel_file_folders (personnel_id, folder_name, folder_slug, is_system_201)
                              VALUES (:personnel_id, :folder_name, :folder_slug, 0)");
    $insert->execute([
        ':personnel_id' => $personnel_id,
        ':folder_name' => $folder_name,
        ':folder_slug' => $folder_slug,
    ]);

    $newFolderId = (int)$conn->lastInsertId();
    pfm_log_action(
        $conn,
        'create_folder',
        (int)$user_personnel_id,
        $session_access,
        $personnel_id,
        $newFolderId,
        null,
        'folder_name=' . $folder_name
    );
?>

<script>
window.alert('Folder created successfully...');
window.location='<?php echo $files_backlink; ?>';
</script>


<?php } ?>


<?php

if(isset($_POST['save_renameFileFolder']))
{
    $personnel_id = (int)($_POST['personnel_id'] ?? 0);
    $folder_id = (int)($_POST['folder_id'] ?? 0);
    $folder_name = trim($_POST['folder_name'] ?? '');
    $files_backlink = 'list_personnel_individual_details_files.php?dept=' . urlencode((string)($_GET['dept'] ?? '')) . '&personnel_id=' . $personnel_id;

    if (!pfm_can_manage_personnel_files($session_access, $user_personnel_id, $personnel_id)) {
        ?>
        <script>
        window.alert('You are not allowed to rename folders for this personnel.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    if ($folder_name === '') {
        ?>
        <script>
        window.alert('Folder name is required.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $folderStmt = $conn->prepare("SELECT folder_id, folder_slug, is_system_201 FROM personnel_file_folders WHERE folder_id = :folder_id AND personnel_id = :personnel_id LIMIT 1");
    $folderStmt->execute([
        ':folder_id' => $folder_id,
        ':personnel_id' => $personnel_id,
    ]);
    $folderRow = $folderStmt->fetch(PDO::FETCH_ASSOC);
    $oldFolderSlug = (string)($folderRow['folder_slug'] ?? '');

    if (!$folderRow) {
        ?>
        <script>
        window.alert('Folder not found.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    if ((int)$folderRow['is_system_201'] === 1) {
        ?>
        <script>
        window.alert('201-files folder is protected and cannot be renamed.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $folder_slug = pfm_slugify($folder_name);
    if ($folder_slug === '201-files') {
        ?>
        <script>
        window.alert('Folder name 201-files is reserved.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $dupStmt = $conn->prepare("SELECT folder_id FROM personnel_file_folders WHERE personnel_id = :personnel_id AND folder_slug = :folder_slug AND folder_id <> :folder_id LIMIT 1");
    $dupStmt->execute([
        ':personnel_id' => $personnel_id,
        ':folder_slug' => $folder_slug,
        ':folder_id' => $folder_id,
    ]);

    if ($dupStmt->fetch()) {
        ?>
        <script>
        window.alert('Another folder already uses this name.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $updateStmt = $conn->prepare("UPDATE personnel_file_folders SET folder_name = :folder_name, folder_slug = :folder_slug WHERE folder_id = :folder_id AND personnel_id = :personnel_id");
    $updateStmt->execute([
        ':folder_name' => $folder_name,
        ':folder_slug' => $folder_slug,
        ':folder_id' => $folder_id,
        ':personnel_id' => $personnel_id,
    ]);

    pfm_log_action(
        $conn,
        'rename_folder',
        (int)$user_personnel_id,
        $session_access,
        $personnel_id,
        $folder_id,
        null,
        'from=' . $oldFolderSlug . ';to=' . $folder_slug
    );
?>

<script>
window.alert('Folder renamed successfully...');
window.location='<?php echo $files_backlink; ?>';
</script>


<?php } ?>


<?php

if(isset($_POST['save_deleteFileFolder']))
{
    $personnel_id = (int)($_POST['personnel_id'] ?? 0);
    $folder_id = (int)($_POST['folder_id'] ?? 0);
    $files_backlink = 'list_personnel_individual_details_files.php?dept=' . urlencode((string)($_GET['dept'] ?? '')) . '&personnel_id=' . $personnel_id;

    if (!pfm_can_manage_personnel_files($session_access, $user_personnel_id, $personnel_id)) {
        ?>
        <script>
        window.alert('You are not allowed to delete folders for this personnel.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $folderStmt = $conn->prepare("SELECT folder_id, is_system_201 FROM personnel_file_folders WHERE folder_id = :folder_id AND personnel_id = :personnel_id LIMIT 1");
    $folderStmt->execute([
        ':folder_id' => $folder_id,
        ':personnel_id' => $personnel_id,
    ]);
    $folderRow = $folderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$folderRow) {
        ?>
        <script>
        window.alert('Folder not found.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    if ((int)$folderRow['is_system_201'] === 1) {
        ?>
        <script>
        window.alert('201-files folder is protected and cannot be deleted.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $countStmt = $conn->prepare("SELECT COUNT(*) FROM files WHERE folder_id = :folder_id");
    $countStmt->execute([':folder_id' => $folder_id]);
    $fileCount = (int)$countStmt->fetchColumn();

    if ($fileCount > 0) {
        ?>
        <script>
        window.alert('Folder is not empty. Delete files first.');
        window.location='<?php echo $files_backlink; ?>';
        </script>
        <?php
        exit;
    }

    $deleteStmt = $conn->prepare("DELETE FROM personnel_file_folders WHERE folder_id = :folder_id AND personnel_id = :personnel_id");
    $deleteStmt->execute([
        ':folder_id' => $folder_id,
        ':personnel_id' => $personnel_id,
    ]);

    pfm_log_action(
        $conn,
        'delete_folder',
        (int)$user_personnel_id,
        $session_access,
        $personnel_id,
        $folder_id,
        null,
        'folder_deleted=1'
    );
?>

<script>
window.alert('Folder deleted successfully...');
window.location='<?php echo $files_backlink; ?>';
</script>


<?php } ?>