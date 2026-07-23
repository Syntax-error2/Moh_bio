<?php
/**
 * Database Connection Configuration (Legacy - PDO Version)
 * Note: This file replaces deprecated mysql_* functions with PDO
 * Consider using dbcon.php instead for new development
 */

date_default_timezone_set('Asia/Manila');

// Database configuration
define('DB_HOST2', 'localhost');
define('DB_NAME2', 'moh_hrms');
define('DB_USER2', 'root');
define('DB_PASS2', '');
define('DB_CHARSET2', 'utf8mb4');

// Build DSN string
$dsn2 = "mysql:host=" . DB_HOST2 . ";dbname=" . DB_NAME2 . ";charset=" . DB_CHARSET2;

// PDO options for security and performance
$options2 = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET2
];

try {
    // Create PDO connection
    $conn2 = new PDO($dsn2, DB_USER2, DB_PASS2, $options2);
} catch (PDOException $e) {
    // Log error securely
    error_log("Database Connection Error (conn2): " . $e->getMessage());
    
    // Display user-friendly error
    die("Database connection failed. Please contact the system administrator.");
}
?>
