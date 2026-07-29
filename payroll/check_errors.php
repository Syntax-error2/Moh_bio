<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$_SESSION['id'] = 1;
$_SESSION['useraccess'] = 'admin';

// create mock user for $user_row in session.php
require 'dbcon.php';
$conn->query("INSERT IGNORE INTO useraccount (user_id, fname, lname, personnel_id, do_id, school_id, useraccess, password) VALUES (1, 'Admin', 'User', 1, 1, 1, 'admin', 'password')");

$pages = [
    'home.php',
    'list_payroll_profiles.php',
    'list_payroll_history.php',
    'income.php',
    'deductions.php'
];

foreach ($pages as $page) {
    ob_start();
    include $page;
    $output = ob_get_clean();
    
    preg_match_all('/<b>(?:Notice|Warning|Fatal error|Parse error)<\/b>:.*?<br \/>/s', $output, $matches);
    if (!empty($matches[0])) {
        echo "\nErrors in $page:\n";
        print_r($matches[0]);
    }
}
echo "\nCheck completed.";
?>
