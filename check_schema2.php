<?php
require 'dbcon.php';
$stmt = $conn->query("DESCRIBE personnels");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($columns as $col) {
    echo $col['Field'] . "\n";
}
?>
