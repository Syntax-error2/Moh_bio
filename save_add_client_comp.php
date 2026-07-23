<?php

 
include('session.php');
include('dbcon.php');
 
if(isset($_POST['addClientComp']))
{
    $ipAddress=$_POST['ipAddress'];
    $compName=$_POST['compName'];
    
    $description=$_POST['description'];
    $clientNumber=$_POST['clientNumber'];
    
    $insertStmt = $conn->prepare("INSERT INTO client_computer(ipAddress, compName, description, clientNumber) VALUES(:ipAddress, :compName, :description, :clientNumber)");
    $insertStmt->execute([
        ':ipAddress' => $ipAddress,
        ':compName' => $compName,
        ':description' => $description,
        ':clientNumber' => $clientNumber,
    ]);

?>

<script> window.location='list_client_comp.php'; </script>

<?php } ?>
 
<?php
 
if(isset($_POST['editClientComp']))
{
 
    $client_id = $_GET['client_id'] ?? '';
    $updateStmt = $conn->prepare("UPDATE client_computer SET description = :description, clientNumber = :clientNumber WHERE client_id = :client_id");
    $updateStmt->execute([
        ':description' => $_POST['description'],
        ':clientNumber' => $_POST['clientNumber'],
        ':client_id' => $client_id,
    ]);

?>

<script> window.location='list_client_comp.php'; </script>

<?php } ?>

 
 
<?php
 
if(isset($_POST['deleteClient']))
{

    $client_id = $_GET['client_id'] ?? '';
    $deleteStmt = $conn->prepare("DELETE FROM client_computer WHERE client_id = :client_id");
    $deleteStmt->execute([':client_id' => $client_id]);

?>

<script> window.location='list_client_comp.php'; </script>

<?php } ?>


