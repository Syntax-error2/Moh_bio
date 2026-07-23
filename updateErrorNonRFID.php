<?php
include('session.php');

function randomcode() {
    $var = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $code = '';
    while ($i <= 9) {
    $num = rand() % 33;
    $tmp = substr($var, $num, 1);
    $code = $code . $tmp;
    $i++;
    }
    return $code;
    }
                                
                                
                                
$perData_query = $conn->query("SELECT personnel_id, RFTag_id FROM personnels") or die(mysql_error());
while($pd_row=$perData_query->fetch()){
    
$personnel_id=$pd_row['personnel_id'];

$str = $pd_row['RFTag_id'];

if(strlen($str)>8){

$RFTag_id='NRF'.substr(randomcode(), 0, 5);

$conn->query("UPDATE personnels SET RFTag_id='$RFTag_id' WHERE personnel_id='$personnel_id'");

}  
                                
                                

        
}        
?>