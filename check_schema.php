<?php
require 'dbcon.php';
$stmt=$conn->query('DESCRIBE useraccount');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
