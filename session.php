<?php
include('dbcon.php');
include('auto_update_stats.php');
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
$user_query = $conn->prepare('SELECT * FROM useraccount WHERE user_id = :user_id');
$user_query->execute(['user_id' => $session_id]);
$user_row = $user_query->fetch();
if (!$user_row) {
    unset($_SESSION['id'], $_SESSION['useraccess'], $_SESSION['allowed_modules']);
    if (!headers_sent()) {
        header('Location: index.php');
    } else {
        echo '<script>window.location = "index.php";</script>';
    }
    exit();
}

// Use the current database role for access checks; the session value may be stale.
$session_access = $user_row['access'];
$_SESSION['useraccess'] = $session_access;
$breadcrumb_home = $session_access === 'User' ? 'home_user.php' : 'home.php';

if ($session_access === 'User') {
    $allowed_user_pages = [
        'home_user.php',
        'list_news_users.php',
        'user_profile.php',
        'list_personnel_individual_details.php',
        'list_personnel_individual_details_EB.php',
        'list_personnel_individual_details_SA.php',
        'list_personnel_individual_details_SR.php',
        'list_personnel_individual_details_files.php',
        'leave_application.php',
        'leave_card.php',
        'get_leave_application.php',
        'get_leave_application_print_data.php',
        'get_leave_card_balance.php',
        'get_signatories_settings.php',
        'save_add_personnel.php',
        'delete201Files.php',
        'download_201.php'
    ];

    $current_script = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($current_script !== '' && !in_array($current_script, $allowed_user_pages, true)) {
        echo '<!DOCTYPE html>';
        echo '<html><head><meta charset="utf-8"><title>Access Denied</title>';
        echo '<style>body{font-family:Arial,sans-serif;background:#f5f7fa;margin:0;padding:24px}.card{max-width:560px;margin:48px auto;background:#fff;border:1px solid #d9dee5;border-radius:8px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,.06)}.title{margin:0 0 10px;font-size:22px;color:#1f2937}.msg{margin:0 0 18px;color:#4b5563;line-height:1.5}.btn{display:inline-block;background:#0b5ed7;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px}</style>';
        echo '</head><body><div class="card">';
        echo '<h1 class="title">Access Denied</h1>';
        echo '<p class="msg">You do not have permission to access this page.</p>';
        echo '<a class="btn" href="home_user.php">Back to Home</a>';
        echo '</div></body></html>';
        exit();
    }
}
 
if ($session_access === 'Admin' || $session_access === 'Administrator') {
    $_SESSION['allowed_modules'] = ['hris', 'payroll']; // Admin gets everything
} else {
    $_SESSION['allowed_modules'] = explode(',', (string)($user_row['module_access'] ?? ''));
}

$user_personnel_id=$user_row['personnel_id'];
$user_dept=$user_row['do_id'];

 

$name = substr($user_row['fname'], 0,1).". ".$user_row['lname'];

$school_id = $user_row['school_id'];


if (!isset($_SESSION['sidebar_cache']) || (time() - ($_SESSION['sidebar_cache_time'] ?? 0)) > 60) {
    $_SESSION['sidebar_cache'] = [
        'do_TotalCtr' => $conn->query('SELECT COUNT(*) FROM dept_offices')->fetchColumn(),
        'desTotalCtr' => $conn->query('SELECT COUNT(*) FROM designation')->fetchColumn(),
        'gassTotalCtr' => $conn->query('SELECT COUNT(*) FROM gass')->fetchColumn(),
        'ES_TotalCtr' => $conn->query('SELECT COUNT(*) FROM emp_status')->fetchColumn(),
        'shiftTotalCtr' => $conn->query('SELECT COUNT(*) FROM shifts')->fetchColumn(),
        'client_computerTotalCtr' => $conn->query('SELECT COUNT(*) FROM client_computer')->fetchColumn(),
        'perCtr_all' => $conn->query("SELECT COUNT(*) FROM personnels WHERE (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')")->fetchColumn(),
        'perCtrM_all' => $conn->query("SELECT COUNT(*) FROM personnels WHERE sex='Male' AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')")->fetchColumn(),
        'perCtrF_all' => $conn->query("SELECT COUNT(*) FROM personnels WHERE sex='Female' AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')")->fetchColumn()
    ];
    $_SESSION['sidebar_cache_time'] = time();
}

$do_TotalCtr = $_SESSION['sidebar_cache']['do_TotalCtr'];
$desTotalCtr = $_SESSION['sidebar_cache']['desTotalCtr'];
$gassTotalCtr = $_SESSION['sidebar_cache']['gassTotalCtr'];
$ES_TotalCtr = $_SESSION['sidebar_cache']['ES_TotalCtr'];
$shiftTotalCtr = $_SESSION['sidebar_cache']['shiftTotalCtr'];
$client_computerTotalCtr = $_SESSION['sidebar_cache']['client_computerTotalCtr'];
$perCtr_all = $_SESSION['sidebar_cache']['perCtr_all'];
$perCtrM_all = $_SESSION['sidebar_cache']['perCtrM_all'];
$perCtrF_all = $_SESSION['sidebar_cache']['perCtrF_all'];

$check_pass = $user_row['password'];

// Auto-process monthly leave credits when Administrator logs in
if ($session_access === 'Admin' || $session_access === 'Administrator') {
    require_once('process_monthly_leave_credits.php');
    checkAndProcessMonthlyCredits($conn, $session_id);
}

?>
