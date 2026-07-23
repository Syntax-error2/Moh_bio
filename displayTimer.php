<?php

include('dbcon.php');
include('myFunctions.php');

$client_ip = get_client_ip();

$cc_stmt = $conn->prepare("SELECT * FROM client_computer WHERE ipAddress = :ipAddress");
$cc_stmt->execute([':ipAddress' => $client_ip]);
$cc_query = $cc_stmt;
$cc_row = $cc_query->fetch();

if($cc_row['display_time']<15){
    
$display_time=$cc_row['display_time']+1;
$display_stmt = $conn->prepare("UPDATE client_computer SET display_time = :display_time, announcement_img = 0 WHERE ipAddress = :ipAddress");
$display_stmt->execute([':display_time' => $display_time, ':ipAddress' => $client_ip]);

}

if($cc_row['display_time']==15){


if($cc_row['announcement_img']<150){
    
$announcement_img_time=$cc_row['announcement_img']+1;

$announcement_stmt = $conn->prepare("UPDATE client_computer SET announcement_img = :announcement_img WHERE ipAddress = :ipAddress");
$announcement_stmt->execute([':announcement_img' => $announcement_img_time, ':ipAddress' => $client_ip]);
 
}

}

if($cc_row['announcement_img']==150)
    {
    $reset_stmt = $conn->prepare("UPDATE client_computer SET announcement_img = 0 WHERE ipAddress = :ipAddress");
    $reset_stmt->execute([':ipAddress' => $client_ip]);
    }

$cc_query=null;
$cc_row=null;

$conn=null;

?>