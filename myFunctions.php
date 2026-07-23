<?php


$day=date('l'); //Mon-Sun
$currentDate=date('m/d/Y');
$logTime=date('h:i A');
$dateTime=$logTime.', '.$day.', '.$currentDate;


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

function get_client_ip(){
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
        
        
        if($ipaddress==='::1')
        {
            $machine_ip=gethostbyname(trim(`hostname`));  
        }else{
            $machine_ip=$ipaddress;
        }


    return $machine_ip;
}
 
 
function lastTagCode(){
    
    $tagFile=fopen("\\\\".get_client_ip()."\\rfid\\TEST\\data.enr", "r") or die ();
    $lastTag=fread($tagFile, filesize("\\\\".get_client_ip()."\\rfid\\TEST\\data.enr"));
    fclose($tagFile);
    return $lastTag;
}

 
function clearLastTag(){
    
    $blank='';
    
    $dataFile=fopen("\\\\".get_client_ip()."\\rfid\\TEST\\data.enr", "w") or die ();
    fwrite($dataFile, $blank);
    fclose($dataFile);
 
    
}


?> 