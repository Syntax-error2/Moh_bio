<?php
include('dbcon.php');

$queries = [
    "CREATE TABLE IF NOT EXISTS personnel_overtime_requests (
        ot_id INT AUTO_INCREMENT PRIMARY KEY,
        personnel_id INT NOT NULL,
        ot_date DATE NOT NULL,
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(16) NOT NULL DEFAULT 'pending',
        reviewed_by INT NULL,
        reviewed_at DATETIME NULL,
        KEY idx_ot_person_date_status (personnel_id, ot_date, status)
    )"
];

$success = false;
foreach ($queries as $q) {
    try {
        $conn->query($q);
        echo "<p style='color: green;'>Executed: $q</p>";
        $success = true;
    } catch(PDOException $e) {
        echo "<p style='color: orange;'>Error: " . $e->getMessage() . "</p>";
    }
}
echo "<h3>Overtime Table Sync Complete! You can close this tab!</h3>";
?>
