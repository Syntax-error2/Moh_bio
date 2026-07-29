<?php
require 'dbcon.php';
$sql = file_get_contents('db/rbac_migration.sql');
try {
    $conn->exec($sql);
    echo 'Migration successful!';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
