
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
define('DB_NAME', 'moh_hrms');
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

// Fetch school preferences with specific columns for better performance
try {
    $sf_query = $conn->prepare("SELECT deped_id, region, division, schoolName, logo, address, contact FROM school_preferences LIMIT 1");
    $sf_query->execute();
    $sf_row = $sf_query->fetch();
    
    // Initialize default values if no preferences found
    if (!$sf_row) {
        $sf_row = [
            'deped_id' => '',
            'region' => '',
            'division' => '',
            'schoolName' => 'MOH HRMS',
            'logo' => 'favicon.ico',
            'address' => '',
            'contact' => ''
        ];
    }
} catch (PDOException $e) {
    error_log("Error fetching school preferences: " . $e->getMessage());
    
    // Set default values on error
    $sf_row = [
        'deped_id' => '',
        'region' => '',
        'division' => '',
        'schoolName' => 'MOH HRMS',
        'logo' => 'favicon.ico',
        'address' => '',
        'contact' => ''
    ];
}
?>

