<?php


 
include('session.php');

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

    
    if($_POST['user_rfid_type']==='With RFID'){
        
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
        
    }
    
    $personnel_id_code=$_POST['personnel_id_code'];
    
    
    $file = $RFTag_id."-".$_FILES['file']['name'];
    
    $file_loc = $_FILES['file']['tmp_name'];
 
	$folder="personnelImg/";
	
	// make file name in lower case
	$new_file_name = strtolower($file);
	// make file name in lower case
    
    
    $final_file=str_replace(' ','-',$new_file_name);
    
    
    $lname=strtoupper($_POST['lname']);
    $fname=strtoupper($_POST['fname']);
    $mname=strtoupper($_POST['mname']);
    $suffix=strtoupper($_POST['suffix']);
    
    $perDataCHK_query = $conn->query("SELECT * FROM personnels WHERE RFTag_id='$RFTag_id' OR personnel_id_code='$personnel_id_code' OR (fname='$fname' AND mname='$mname' AND lname='$lname')");
    if($perDataCHK_query->rowCount()>0){
        
         ?>
 
        <script>
        window.alert('Name / Biometric ID / ID of employee already exist...');
        window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>'; 
        </script>    
        
        <?php

    }else{
        
    
    
    
    if(move_uploaded_file($file_loc,$folder.$final_file)){
        
        $conn->query("INSERT INTO personnels(RFTag_id, personnel_id_code, img, lname, fname, mname, suffix, do_id)
        VALUES('$RFTag_id', '$personnel_id_code', '$final_file', '$lname', '$fname', '$mname', '$suffix', '$_GET[dept]')");

    }else{
        
        $conn->query("INSERT INTO personnels(RFTag_id, personnel_id_code, lname, fname, mname, suffix, do_id)
        VALUES('$RFTag_id', '$personnel_id_code', '$lname', '$fname', '$mname', '$suffix', '$_GET[dept]')");

    
    }
 
    $client_ip = get_client_ip();
    if ($client_ip == '::1' || $client_ip == '127.0.0.1') {
        $enr_path = __DIR__ . '/data.enr';
    } else {
        $enr_path = "\\\\" . $client_ip . "\\mob_bio\\data.enr";
    }
    $dataFile=@fopen($enr_path, "w");
    if($dataFile) { fwrite($dataFile, $blank); }
    if($dataFile) { fclose($dataFile); }
    
    
    $perData_query = $conn->query("SELECT personnel_id FROM personnels WHERE RFTag_id='$RFTag_id' OR personnel_id_code='$personnel_id_code'");
    $pd_row=$perData_query->fetch();
    
     ?>
 
    <script>
    window.alert('<?php echo $fname.' '.$mname.' '.$lname; ?> added successfully... you will be redirected another page to fill-up complete personnel data...');
    window.location='edit_completePersonnelData.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $pd_row['personnel_id']; ?>'; 
    </script>    
    
    <?php } } ?>

 

<?php

if(isset($_POST['updatePersonnelComplete']))
{
    
    $personnel_id=$_POST['personnel_id'];
    
    
    
    $RFTag_id=$_POST['RFTag_id'];
    
    $personnel_id_code=$_POST['personnel_id_code'];
    
    $shift_id=$_POST['shift_id'];
    
    
    $lname=strtoupper($_POST['lname']);
    $fname=strtoupper($_POST['fname']);
    $mname=strtoupper($_POST['mname']);
    $suffix=strtoupper($_POST['suffix']);
    
    
    
    $sex=$_POST['sex'];
    $marital_status=$_POST['marital_status'];
    
    //01/01/2019
    $bdMM=substr($_POST['birthdate'], 0,2);
    $bdDD=substr($_POST['birthdate'], 3,2);
    $bdYYYY=substr($_POST['birthdate'], 6,4);
        
    if($_POST['birthdate']=="  /  /    " OR $_POST['birthdate']=""){
        
        $age=0;
        
    }else{
        
        $dateOfBirth = $bdDD.'-'.$bdMM.'-'.$bdYYYY;
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age=$diff->format('%y');
        
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
    
    
    $sal_grade = $_POST['sal_grade'];
    $sal_step = $_POST['sal_step'];
    $sal_level = $_POST['sal_level'];
    $rate_per_day = $_POST['rate_per_day'];
    
    $empStat_id=$_POST['empStat_id'];
    
    
    $eligibility=$_POST['eligibility'];
    $plantilla_num=$_POST['plantilla_num'];
    
    $appointment_date=$_POST['appointment_date'];
    $separation_date=$_POST['separation_date'];
    
    if($appointment_date=="  /  /    " OR $appointment_date==""){
        $num_of_yrs=0;
    }else{
        
        $today2=date("Y-m-d");
        $diff2 = date_diff(date_create($appointment_date), date_create($today2));
       	$num_of_yrs=$diff2->format('%y');
        
    }
    
    $tin_num=$_POST['tin_num'];
    $gsis_num=$_POST['gsis_num'];
    $pagibig_num=$_POST['pagibig_num'];
    $philHealth_num=$_POST['philHealth_num'];
    
    $conn->query("UPDATE personnels SET
    
    RFTag_id='$RFTag_id',
    personnel_id_code='$personnel_id_code',
    shift_id='$shift_id',
    
    lname='$lname',
    fname='$fname',
    mname='$mname',
    suffix='$suffix',
    age='$age',
    sex='$sex',
    marital_status='$marital_status',
    
    bdMM='$bdMM',
    bdDD='$bdDD',
    bdYYYY='$bdYYYY',
    
    birth_place='$birth_place',
    address='$address',
    
    email='$email',
    personal_pnum='$personal_pnum',
    emergency_pnum='$emergency_pnum',
    
    conPerson_lname='$conPerson_lname',
    conPerson_fname='$conPerson_fname',
    conPerson_mname='$conPerson_mname',
    conPerson_relationship='$conPerson_relationship',
    
    do_id='$do_id',
    des_id='$des_id',
    
    sal_grade='$sal_grade',
    sal_step='$sal_step',
    sal_level='$sal_level',
    rate_per_day='$rate_per_day',
    
    empStat_id='$empStat_id',
    
    eligibility='$eligibility',
    plantilla_num='$plantilla_num',
    appointment_date='$appointment_date',
    separation_date='$separation_date',
    num_of_yrs='$num_of_yrs',
    
    tin_num='$tin_num',
    gsis_num='$gsis_num',
    pagibig_num='$pagibig_num',
    philHealth_num='$philHealth_num'
    
    WHERE personnel_id='$personnel_id'");
 
    //SEND EMAIL
    /*$to = $email;
    $subject = "BINALBAGAN - HR [ Registered successfully as MOB personnel ]";
    
    $message = "
    <html>
    <head>
    <title>BINALBAGAN - HR Email Push-Notifications</title>
    </head>
    <body>
    <p>Biometric ID is necessary to register a user account...</p>
    <table>
    <tr>
    <th>Biometric ID</th>
    </tr>
    <tr>
    <td>".$RFTag_id."</td>
    </tr>
    </table>
    </body>
    </html>
    ";
    
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
    // More headers
    $headers .= 'From: Binalbagan Catholic College - HR' . "\r\n";
    $headers .= 'Cc: emiloimagtolis@gmail.com' . "\r\n";
    
    mail($to,$subject,$message,$headers);
    
    //END SEND EMAIL */
  ?>
  
<script>

window.alert('<?php echo $fname.' '.$mname.' '.$lname; ?> Personal Information updated successfully...');
window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>'; </script>    

<?php } ?>


<?php

if(isset($_POST['updateStudentImg']))
{
    
    
    $personnel_id=$_POST['personnel_id'];
    
    $file = $_POST['RFTag_id']."-".$_FILES['file']['name'];
    
    $file_loc = $_FILES['file']['tmp_name'];
 
	$folder="personnelImg/";
	
	// make file name in lower case
	$new_file_name = strtolower($file);
	// make file name in lower case
    
    
    $final_file=str_replace(' ','-',$new_file_name);
        
        
     
    if(move_uploaded_file($file_loc,$folder.$final_file)){
        
        $conn->query("UPDATE personnels SET img='$final_file' WHERE personnel_id='$personnel_id'");

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
    
    $personnel_id = $_POST['personnel_id'];
    $target_dir = "personnel201Files/";
    $target_file = $target_dir.basename($_FILES["per_file"]["name"]);
    $FileType = pathinfo($target_file,PATHINFO_EXTENSION);

    if (move_uploaded_file($_FILES["per_file"]["tmp_name"], $target_file)){
        
        $conn->query("INSERT INTO files(personnel_id, file_name, file_type, date_time_uploaded)
        VALUES('$personnel_id', '$target_file', '$FileType', '$dateTimeSave')");
        
    }else{
        
        echo "Sorry, there was an error uploading your file.";
    }
    
 
?>

<script> window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>'; </script>    


<?php } ?>





<?php

if(isset($_POST['set_shift']))
{
    
    $personnel_id = $_POST['personnel_id'];
    $conn->query("UPDATE personnels SET shift_id='$_POST[shift_id]' WHERE personnel_id='$personnel_id'");
    
    $redirect_url = !empty($_POST['return_url']) ? $_POST['return_url'] : 'list_personnel.php?dept='.$_GET['dept'];
 
?>

<script>
window.alert('Shift updated successfully...');
window.location=<?php echo json_encode($redirect_url); ?>;
</script>    


<?php } ?>



 
<?php

if(isset($_POST['updateStudentRFIDTag']))
{
    
    $RFTag_id=$_POST['RFTag_id'];
    $currentRFIDTag=$_POST['currentRFIDTag'];
    
    
    $studDataCHK_query = $conn->query("SELECT * FROM personnels WHERE RFTag_id='$RFTag_id' ");
    if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('Biometric ID of employee already exist...');
        window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>&gradeLevel=<?php echo $getgradeLevel; ?>&section=<?php echo $getSection; ?>'; 
        </script>    
        
        <?php
    }else{
        $conn->query("UPDATE personnels SET RFTag_id='$RFTag_id' WHERE personnel_id='$_GET[personnel_id]'");
    
        $conn->query("UPDATE personnel_logs SET RFTag_id='$RFTag_id' WHERE RFTag_id='$currentRFIDTag'");
        
        ?>
          
        <script>
        window.alert('Biometric ID of employee updated successfully...');
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
    
    if($check_pass==$final_current_pass){
        
    $perDataCHK_query = $conn->query("SELECT * FROM useraccount WHERE username='$username' AND password='$final_pass'");
    if($perDataCHK_query->rowCount()>0){
        
         ?>
 
        <script>
        window.alert('Username and Password already exist...');
        window.location='user_profile.php?cw=UserProfile&dept=<?php echo $dept; ?>&personnel_id=<?php echo $personnel_id; ?>'; 
        </script>    
        
        <?php

    }else{  
         
         
    $conn->query("UPDATE useraccount SET do_id='$dept', username='$username', password='$final_pass' WHERE personnel_id='$personnel_id'");
    
    
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



        $studDataCHK_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$_POST[personnel_id]' AND course_details='$_POST[course_details]' AND year_grad='$_POST[year_grad]' AND school_name='$_POST[school_name]'");
        if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('Educational attainment already exist...');
        window.location='list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
        
        <?php
        
        }else{
            
        $conn->query("INSERT INTO personnel_educ_bg(personnel_id, degree, course_details, units, year_grad, school_name)VALUES
        
        ('$_POST[personnel_id]', '$_POST[degree]', '$_POST[course_details]', '$_POST[units]', '$_POST[year_grad]', '$_POST[school_name]')");
        
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


        $event_date=$_POST['sem_date_from'].' - '.$_POST['sem_date_to'];
        
        $studDataCHK_query = $conn->query("SELECT * FROM personnel_seminars WHERE personnel_id='$_POST[personnel_id]' AND seminar_title='$_POST[purpose_title]' AND event_date='$event_date'");
        if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('Seminar already exist...');
        window.location='list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_POST['personnel_id']; ?>';
        </script>    
        
        <?php
        
        }else{
        
        
        $conn->query("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, entry_type)
        VALUES ('$_POST[personnel_id]', '$_POST[purpose_title]', '$_POST[description]', '$_POST[location_venue]', '$event_date', 'Manual Encode')");
        
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
    $personnel_id = !empty($_POST['personnel_id']) ? $_POST['personnel_id'] : ($_GET['personnel_id'] ?? '');
    $dept = $_GET['dept'] ?? '';
    
    $maid_lname = trim($_POST['maid_lname'] ?? '');
    $maid_fname = trim($_POST['maid_fname'] ?? '');
    $maid_mname = trim($_POST['maid_mname'] ?? '');
    $serv_date_from = trim($_POST['serv_date_from'] ?? '');
    $serv_date_to = trim($_POST['serv_date_to'] ?? '');
    $roa_designation = trim($_POST['roa_designation'] ?? '');
    $roa_status = trim($_POST['roa_status'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $office_appointment = trim($_POST['office_appointment'] ?? '');
    $separate_date = trim($_POST['separate_date'] ?? '');
    $separate_cause = trim($_POST['separate_cause'] ?? '');

    $studDataCHK_query = $conn->prepare("SELECT sr_id FROM service_record WHERE personnel_id = ? AND serv_date_from = ? AND serv_date_to = ?");
    $studDataCHK_query->execute([$personnel_id, $serv_date_from, $serv_date_to]);
    if($studDataCHK_query->rowCount() > 0){
     ?>

    <script>
    window.alert('Service Record data already exist...');
    window.location='list_personnel_individual_details_SR.php?dept=<?php echo htmlspecialchars($dept); ?>&personnel_id=<?php echo htmlspecialchars($personnel_id); ?>';
    </script>    
    
    <?php
    
    }else{
    
    $ins_query = $conn->prepare("INSERT INTO service_record(personnel_id, maid_lname, maid_fname, maid_mname, appointDate_status, serv_date_from, serv_date_to, roa_designation, roa_status, salary, office_appointment, separate_date, separate_cause)
    VALUES (?, ?, ?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?)");
    $ins_query->execute([
        $personnel_id,
        $maid_lname,
        $maid_fname,
        $maid_mname,
        $serv_date_from,
        $serv_date_to,
        $roa_designation,
        $roa_status,
        $salary,
        $office_appointment,
        $separate_date,
        $separate_cause
    ]);
    
    ?>
      
    <script>
    window.alert('Service Record added successfully...');
    window.location='list_personnel_individual_details_SR.php?dept=<?php echo htmlspecialchars($dept); ?>&personnel_id=<?php echo htmlspecialchars($personnel_id); ?>';
    </script>    
     
    <?php } } ?>
        
        
        
        
        
<?php

if(isset($_POST['deleteStudent']))
{
    $personnel_id=$_POST['personnel_id'];
    
    $conn->query("DELETE FROM personnels WHERE personnel_id='$personnel_id'");
 
?>

<script>
window.alert('Personnel deleted successfully...');
window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
</script>    


<?php } ?>