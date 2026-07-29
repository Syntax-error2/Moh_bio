<?php
$files = [
    'home.php',
    'leave_application.php',
    'log_validation_viewer.php',
    'log_validation_viewer_AM_OUT.php',
    'log_validation_viewer_PM_IN.php',
    'log_validation_viewer_PM_OUT.php',
    'navbar_header.php',
    'payroll/home.php',
    'payroll/list_personnel_deductions.php',
    'payroll/list_personnel_income.php',
    'payroll/list_personnel_individual_details.php',
    'payroll/navbar_header.php',
    'process_monthly_leave_credits.php',
    'session.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // We only want to replace 'Administrator' strings that refer to access level
        // Let's replace $session_access==='Administrator' and == 'Administrator'
        $content = str_replace(
            ["==='Administrator'", "== 'Administrator'", "=== 'Administrator'", '=="Administrator"', '== "Administrator"'],
            ["==='Admin'", "== 'Admin'", "=== 'Admin'", '=="Admin"', '== "Admin"'],
            $content
        );
        $content = str_replace(
            ["!=='Administrator'", "!= 'Administrator'", "!== 'Administrator'", '!="Administrator"', '!= "Administrator"'],
            ["!=='Admin'", "!= 'Admin'", "!== 'Admin'", '!="Admin"', '!= "Admin"'],
            $content
        );
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
?>
