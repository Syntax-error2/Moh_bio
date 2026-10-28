<?php

date_default_timezone_set('Asia/Manila');

$host = '127.0.0.1';
$db   = 'moh_bio';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     throw new PDOException($e->getMessage(), (int)$e->getCode());
}

if (!defined('FAST_DB_INIT')) {
    try {
        $sf_query = $conn->prepare("SELECT * FROM institution_preferences");
        $sf_query->execute();
        $sf_row = $sf_query->fetch();
    } catch (PDOException $e) {
        $sf_query = $conn->prepare("SELECT * FROM school_preferences");
        $sf_query->execute();
        $sf_row = $sf_query->fetch();
    }
} else {
    $sf_row = null;
}

// Normalize sql_mode across MySQL/MariaDB environments to avoid legacy NOT NULL strict traps
if (!defined('FAST_DB_INIT')) {
    try {
        $conn->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
    } catch (PDOException $ignored) {}
}

if ($sf_row) {
    $zip_code = $sf_row['zip_code'] ?? ($sf_row['deped_id'] ?? '');
    $region = $sf_row['region'] ?? '';
    $division = $sf_row['division'] ?? '';
    $institution_name = $sf_row['institution_name'] ?? ($sf_row['schoolName'] ?? '');

    // Backward compatibility variable aliases
    $deped_id = $zip_code;
    $schoolName = $institution_name;
}

// One-time self-healing schema migration (runs once per installation/update, cached in db/.schema_synced)
$schema_marker = __DIR__ . '/db/.schema_synced';
if (!file_exists($schema_marker)) {
    // Ensure leave_credit_logs schema is always up to date
    try {
        $conn->query("SELECT vl_w_pay, sl_w_pay, vl_wout_pay, sl_wout_pay, period, particulars FROM leave_credit_logs LIMIT 0");
    } catch (PDOException $e) {
        $log_cols = [
            "period VARCHAR(100) DEFAULT NULL",
            "particulars VARCHAR(100) DEFAULT NULL",
            "vl_w_pay DECIMAL(10,3) DEFAULT 0.000",
            "sl_w_pay DECIMAL(10,3) DEFAULT 0.000",
            "vl_wout_pay DECIMAL(10,3) DEFAULT 0.000",
            "sl_wout_pay DECIMAL(10,3) DEFAULT 0.000"
        ];
        foreach ($log_cols as $c) {
            try {
                $conn->query("ALTER TABLE leave_credit_logs ADD COLUMN $c");
            } catch (PDOException $ignored) {}
        }
    }

    // Ensure service_record schema has default for appointDate_status
    try {
        $conn->query("ALTER TABLE service_record MODIFY COLUMN appointDate_status VARCHAR(55) NOT NULL DEFAULT ''");
    } catch (PDOException $ignored) {}

    // Ensure personnel_logs has proper defaults for strict SQL modes
    try {
        $conn->exec("ALTER TABLE personnel_logs MODIFY COLUMN ref_log_id INT(11) NOT NULL DEFAULT 0");
        $conn->exec("ALTER TABLE personnel_logs MODIFY COLUMN captured_img VARCHAR(255) NOT NULL DEFAULT ''");
        $conn->exec("ALTER TABLE personnel_logs MODIFY COLUMN remarks VARCHAR(55) NOT NULL DEFAULT ''");
        $conn->exec("ALTER TABLE personnel_logs MODIFY COLUMN travel_leave_code VARCHAR(55) NOT NULL DEFAULT ''");
        $conn->exec("ALTER TABLE personnel_logs MODIFY COLUMN logTime_sec INT(11) NOT NULL DEFAULT 0");
        $conn->exec("ALTER TABLE personnel_logs MODIFY COLUMN late_status VARCHAR(3) NOT NULL DEFAULT 'off'");
    } catch (PDOException $ignored) {}

    // Ensure sync export tracking columns exist on bio_dtr and personnel_logs
    try {
        $conn->query("SELECT is_exported FROM bio_dtr LIMIT 0");
    } catch (PDOException $e) {
        try { $conn->exec("ALTER TABLE bio_dtr ADD COLUMN is_exported TINYINT(1) NOT NULL DEFAULT 0"); } catch (PDOException $ignored) {}
        try { $conn->exec("ALTER TABLE bio_dtr ADD COLUMN exported_at DATETIME NULL DEFAULT NULL"); } catch (PDOException $ignored) {}
        try { $conn->exec("ALTER TABLE bio_dtr ADD INDEX idx_bio_exported (is_exported, log_date)"); } catch (PDOException $ignored) {}
    }

    try {
        $conn->query("SELECT is_exported FROM personnel_logs LIMIT 0");
    } catch (PDOException $e) {
        try { $conn->exec("ALTER TABLE personnel_logs ADD COLUMN is_exported TINYINT(1) NOT NULL DEFAULT 0"); } catch (PDOException $ignored) {}
        try { $conn->exec("ALTER TABLE personnel_logs ADD COLUMN exported_at DATETIME NULL DEFAULT NULL"); } catch (PDOException $ignored) {}
        try { $conn->exec("ALTER TABLE personnel_logs ADD INDEX idx_logs_exported (is_exported, logDate)"); } catch (PDOException $ignored) {}
    }

    @file_put_contents($schema_marker, date('Y-m-d H:i:s'));
}
?>