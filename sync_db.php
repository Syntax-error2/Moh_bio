<?php
require_once('dbcon.php');

$queries = [
    // 1. Personnels balance columns
    "ALTER TABLE personnels ADD COLUMN vl_balance DECIMAL(10,3) NOT NULL DEFAULT 0.000 AFTER monthly_salary",
    "ALTER TABLE personnels ADD COLUMN sl_balance DECIMAL(10,3) NOT NULL DEFAULT 0.000 AFTER vl_balance",
    
    // 2. Leave credit logs columns
    "ALTER TABLE leave_credit_logs ADD COLUMN period VARCHAR(100) DEFAULT NULL AFTER date_logged",
    "ALTER TABLE leave_credit_logs ADD COLUMN particulars VARCHAR(100) DEFAULT NULL AFTER period",
    "ALTER TABLE leave_credit_logs ADD COLUMN vl_w_pay DECIMAL(10,3) DEFAULT 0.000 AFTER sl_amount",
    "ALTER TABLE leave_credit_logs ADD COLUMN sl_w_pay DECIMAL(10,3) DEFAULT 0.000 AFTER vl_w_pay",
    "ALTER TABLE leave_credit_logs ADD COLUMN vl_wout_pay DECIMAL(10,3) DEFAULT 0.000 AFTER sl_w_pay",
    "ALTER TABLE leave_credit_logs ADD COLUMN sl_wout_pay DECIMAL(10,3) DEFAULT 0.000 AFTER vl_wout_pay",

    // 3. Automated accrual tracker
    "CREATE TABLE IF NOT EXISTS leave_accrual_history (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        accrual_month VARCHAR(7) NOT NULL UNIQUE,
        date_processed DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // 4. Overtime review tracking
    "ALTER TABLE personnel_overtime ADD COLUMN status VARCHAR(20) DEFAULT 'Pending' AFTER ot_reason",
    "ALTER TABLE personnel_overtime ADD COLUMN reviewed_by INT(11) DEFAULT NULL AFTER status",
    "ALTER TABLE personnel_overtime ADD COLUMN reviewed_at DATETIME DEFAULT NULL AFTER reviewed_by"
];

$is_cli = (php_sapi_name() === 'cli');

foreach ($queries as $q) {
    try {
        $conn->query($q);
        if ($is_cli) {
            echo "[SUCCESS] Executed: $q\n";
        } else {
            echo "<p style='color: green;'>[SUCCESS] Executed: " . htmlspecialchars($q) . "</p>";
        }
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if ($is_cli) {
            echo "[NOTICE] Skipped / Exists: $q (Message: $msg)\n";
        } else {
            echo "<p style='color: orange;'>[NOTICE] Skipped / Exists: " . htmlspecialchars($q) . " (<em>" . htmlspecialchars($msg) . "</em>)</p>";
        }
    }
}

if ($is_cli) {
    echo "\nDatabase Sync Complete for moh_bio!\n";
} else {
    echo "<h3>Database Sync Complete for moh_bio!</h3>";
}
?>
