<?php
require 'dbcon.php';
$stmt=$conn->query("SELECT fname, lname, access, module_access FROM useraccount WHERE lname = 'HERRADURA'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>
