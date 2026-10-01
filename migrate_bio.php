<?php
require 'dbcon.php';

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Alter personnels table
    $sql1 = "ALTER TABLE `personnels` 
             ADD COLUMN `biometric_id` VARCHAR(255) NULL AFTER `personnel_id`,
             ADD COLUMN `fingerprint_template` LONGTEXT NULL AFTER `biometric_id`";
    $conn->exec($sql1);
    echo "personnels table altered successfully.\n";

    // 2. Create bio_dtr table
    $sql2 = "CREATE TABLE IF NOT EXISTS `bio_dtr` (
              `bio_dtr_id` int(11) NOT NULL AUTO_INCREMENT,
              `personnel_id` varchar(255) NOT NULL,
              `log_date` date NOT NULL,
              `time_in` time DEFAULT NULL,
              `time_out` time DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`bio_dtr_id`),
              KEY `idx_personnel_date` (`personnel_id`,`log_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql2);
    echo "bio_dtr table created successfully.\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
