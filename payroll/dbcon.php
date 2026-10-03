
<?php
/**
 * Database Connection Configuration for Payroll Module
 * MOH HRMS - Payroll System
 */

// Prevent multiple inclusions
if (defined('DB_CONNECTION_LOADED')) {
    return;
}
define('DB_CONNECTION_LOADED', true);

// Set timezone for consistent datetime handling
date_default_timezone_set('Asia/Manila');

// Database configuration constants
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'moh_bio');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Build DSN string
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// PDO options for security and performance
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,     // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,            // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                       // Use real prepared statements
    PDO::ATTR_PERSISTENT         => false,                       // Don't use persistent connections
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET   // Set charset on connection
];

try {
    // Create PDO connection
    $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log error securely (don't expose details to users)
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Display user-friendly error
    die("Database connection failed. Please contact the system administrator.");
}

try {
    $sf_query = $conn->prepare("SELECT * FROM institution_preferences LIMIT 1");
    $sf_query->execute();
    $sf_row = $sf_query->fetch();
    
    $zip_code = $sf_row['zip_code'];
    $region = $sf_row['region'];
    $division = $sf_row['division'];
    $institution_name = $sf_row['institution_name'];
    
    // Legacy variable names for backward compatibility
    $deped_id = $zip_code;
    $schoolName = $institution_name;
    
} catch (PDOException $e) {
    error_log("Error fetching institution preferences: " . $e->getMessage());
}
?>

