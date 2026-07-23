<?php
include('dbcon.php');
//Start session
 session_start();
//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['id']) || ($_SESSION['id'] == '')) { ?>


<script>
window.location = 'index.php';
</script>

<?php
    exit();
}

$session_id=$_SESSION['id'];
$session_access=$_SESSION['useraccess'];
 
$user_query = $conn->prepare('SELECT * FROM useraccount WHERE user_id = :user_id');
$user_query->execute(['user_id' => $session_id]);
$user_row = $user_query->fetch();


$user_personnel_id=$user_row['personnel_id'];
$user_dept=$user_row['do_id'];

$name = substr($user_row['fname'], 0,1).". ".$user_row['lname'];

$school_id = $user_row['school_id'];

$do_TotalCtr = $conn->query('SELECT COUNT(*) FROM dept_offices')->fetchColumn(); 
$desTotalCtr = $conn->query('SELECT COUNT(*) FROM designation')->fetchColumn(); 
$gassTotalCtr = $conn->query('SELECT COUNT(*) FROM gass')->fetchColumn(); 
$ES_TotalCtr = $conn->query('SELECT COUNT(*) FROM emp_status')->fetchColumn(); 
$shiftTotalCtr = $conn->query('SELECT COUNT(*) FROM shifts')->fetchColumn(); 
$client_computerTotalCtr = $conn->query('SELECT COUNT(*) FROM client_computer')->fetchColumn();


$perCtr_query = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN sex='Male' THEN 1 ELSE 0 END) as male_count,
    SUM(CASE WHEN sex='Female' THEN 1 ELSE 0 END) as female_count
FROM personnels 
WHERE separation_date = '' OR separation_date = '  /  /    '");

$perCtr_query->execute();
$perCtr_result = $perCtr_query->fetch();

$perCtr_all = $perCtr_result['total'];
$perCtrM_all = $perCtr_result['male_count'];
$perCtrF_all = $perCtr_result['female_count'];

$deped_id = $sf_row['deped_id'];
$region = $sf_row['region'];
$division = $sf_row['division'];
$schoolName = $sf_row['schoolName'];

$check_pass = $user_row['password'];

?>