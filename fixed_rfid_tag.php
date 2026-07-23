<h3>Fixed RFID Tag</h3>
<?php

include('dbcon.php');
include('session.php');  
/** @var PDO $conn */
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

$fixCtr=0;                  
$printAll_Data_query = $conn->query("SELECT personnel_id, lname, fname FROM personnels WHERE RFTag_id='' ORDER BY personnel_id ASC");
while($printALL_row=$printAll_Data_query->fetch()){

        
        $fixCtr+=1;
                              
        $RFTag_id='NRF'.substr(randomcode(), 0, 5);
        
        
        
        $updateStmt = $conn->prepare("UPDATE personnels SET RFTag_id = :RFTag_id WHERE personnel_id = :personnel_id");
        $updateStmt->execute([
            ':RFTag_id' => $RFTag_id,
            ':personnel_id' => $printALL_row['personnel_id']
        ]);
        
        
        echo $fixCtr.'. '.$RFTag_id.' - '.$printALL_row['lname'].', '.$printALL_row['fname']."<br />";

}

if($printAll_Data_query->rowCount()<=0){
    echo "No record found to be fixed...";
}

?>