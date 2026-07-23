<?php
/**
 * Database Connection Configuration (conn3 - PDO Version)
 * Optimized for CSV import/export operations
 */

date_default_timezone_set('Asia/Manila');

// Database configuration
define('DB_HOST3', 'localhost');
define('DB_NAME3', 'moh_hrms');
define('DB_USER3', 'root');
define('DB_PASS3', '');
define('DB_CHARSET3', 'utf8mb4');

// Build DSN string
$dsn3 = "mysql:host=" . DB_HOST3 . ";dbname=" . DB_NAME3 . ";charset=" . DB_CHARSET3;

// PDO options for security and performance
$options3 = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET3
];

try {
    // Create PDO connection
    $conn3 = new PDO($dsn3, DB_USER3, DB_PASS3, $options3);
} catch (PDOException $e) {
    // Log error securely
    error_log("Database Connection Error (conn3): " . $e->getMessage());
    
    // Display user-friendly error
    die("Database connection failed. Please contact the system administrator.");
}
?>
