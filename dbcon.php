<?php

date_default_timezone_set('Asia/Manila');

$host = 'localhost';
$db   = 'moh_hrms';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     throw new PDOException($e->getMessage(), (int)$e->getCode());
}

$sf_query = $conn->prepare("SELECT * FROM institution_preferences");
$sf_query->execute();
$sf_row = $sf_query->fetch();

$zip_code = $sf_row['zip_code'];
$region = $sf_row['region'];
$division = $sf_row['division'];
$institution_name = $sf_row['institution_name'];

// Legacy variable names for backward compatibility
$deped_id = $zip_code;
$schoolName = $institution_name;

?>
