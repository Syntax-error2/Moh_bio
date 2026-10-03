<?php


$day=date('l'); //Mon-Sun
$currentDate=date('Y-m-d');
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
 
 
function get_enr_path() {
    $client_ip = get_client_ip();
    if ($client_ip == '::1' || $client_ip == '127.0.0.1') {
        return __DIR__ . '/data.enr';
    } else {
        return "\\\\" . $client_ip . "\\moh_hris\\data.enr";
    }
}

function lastTagCode(){
    $enr_path = get_enr_path();
    $tagFile = @fopen($enr_path, "r");
    if ($tagFile) {
        $lastTag = fread($tagFile, filesize($enr_path));
        fclose($tagFile);
        return $lastTag;
    }
    return '';
}

function clearLastTag(){
    $blank='';
    $enr_path = get_enr_path();
    $dataFile = @fopen($enr_path, "w");
    if ($dataFile) {
        fwrite($dataFile, $blank);
        fclose($dataFile);
    }
}