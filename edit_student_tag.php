
<?php

function get_client_ip2() {
    $ipaddress2 = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress2 = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress2 = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress2 = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress2 = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress2 = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress2 = getenv('REMOTE_ADDR');
    else
        $ipaddress2 = 'UNKNOWN';
    return $ipaddress2;
} ?>
                                       
<?php if(get_client_ip2()=="::1") { ?>

  <input required="true" name="RFTag_id" type="text" class="form-control" value="<?php echo file_get_contents("\\\\".gethostbyname(trim(`hostname`))."\\rfid\\TEST\\data.enr"); ?>">
   
<?php }else{ ?>

  <input required="true" name="RFTag_id" type="text" class="form-control" value="<?php echo file_get_contents("\\\\".get_client_ip2()."\\rfid\\TEST\\data.enr"); ?>">
  
<?php } ?> 


 