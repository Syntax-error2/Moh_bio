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

if ($user_row) {
    $session_access = $user_row['access'];
    $_SESSION['useraccess'] = $user_row['access'];
    
    if ($user_row['access'] === 'Admin') {
        $_SESSION['allowed_modules'] = ['hris', 'payroll'];
    } else {
        $_SESSION['allowed_modules'] = explode(',', (string)($user_row['module_access'] ?? ''));
    }
} else {
    $_SESSION['allowed_modules'] = [];
}

if (!in_array('payroll', $_SESSION['allowed_modules'], true)) {
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Access Denied</title>';
    echo '<style>body{font-family:Arial,sans-serif;background:#f5f7fa;margin:0;padding:24px}.card{max-width:560px;margin:48px auto;background:#fff;border:1px solid #d9dee5;border-radius:8px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,.06)}.title{margin:0 0 10px;font-size:22px;color:#1f2937}.msg{margin:0 0 18px;color:#4b5563;line-height:1.5}.btn{display:inline-block;background:#0b5ed7;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px}</style>';
    echo '</head><body><div class="card"><h1 class="title">Access Denied</h1>';
    echo '<p class="msg">You do not have permission to access the Payroll Module.</p>';
    echo '<a class="btn" href="../index.php">Back to Login</a></div></body></html>';
    exit();
}


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


$check_pass = $user_row['password'];

?>