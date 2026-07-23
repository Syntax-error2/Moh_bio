<?php

include('dbcon.php');

include('myFunctions.php');
 
$updClient = 'UPDATE client_computer SET RFID_tag = :RFID_tag  WHERE ipAddress = :ipAddress';
$conn->prepare($updClient)->execute(['RFID_tag' => $_POST['profile_id'], 'ipAddress' => get_client_ip()]);
 
?>