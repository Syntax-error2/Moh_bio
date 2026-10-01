<?php
require 'dbcon.php';
$stmt1 = $conn->query("SHOW COLUMNS FROM personnels LIKE 'biometric_id'");
print_r($stmt1->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $conn->query("SHOW TABLES LIKE 'bio_dtr'");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
?>
