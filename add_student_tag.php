
<?php

function get_client_ip() {
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
    return $ipaddress;
} ?>


                            <div class="form-group row">
                            <label class="col-sm-2 form-control-label">RFID Tag</label>
                                <div class="col-sm-10">
                                      
                                    <div class="row">
                                        <div class="col-md-12">
                                        <?php if(get_client_ip()=="::1") { ?>
                                        
                                          <input name="RFTag_id" type="text" class="form-control" value="<?php echo file_get_contents("\\\\".gethostbyname(trim(`hostname`))."\\rfid\\TEST\\data.enr"); ?>" readonly="" />
                                         
                                        <?php }else{ ?>
                                        
                                          <input name="RFTag_id" type="text" class="form-control" value="<?php echo file_get_contents("\\\\".get_client_ip()."\\rfid\\TEST\\data.enr"); ?>" readonly="" />
                                          
                                        <?php } ?>
                                        <small>Tap RFID Card</small>                  
                                        </div>
                                            
                                         
                                    </div>
                                        
                                </div>
                        </div>
                        
                            
                         