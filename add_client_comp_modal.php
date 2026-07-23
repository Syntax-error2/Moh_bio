            
            <!-- addSubjKinder Modal -->
                  <div id="addSubjKinder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Client Computer</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <form action="save_add_client_comp.php" method="POST">
                        <div class="modal-body">
                 
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Computer Name</label>
                              <div class="col-sm-8">
                              <input value="<?php echo gethostbyaddr($_SERVER['REMOTE_ADDR']); ?>" name="compName" type="text" class="form-control" readonly="">
                              <small class="form-text">Computer Name</small>
                              </div>
                                 
                        </div>
                        
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">IP Address</label>
                              <div class="col-sm-8">
                              
                              
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
}



if(get_client_ip2()=="::1") { ?>

   <input value="<?php echo gethostbyname(trim(`hostname`)); ?>" name="ipAddress" type="text" class="form-control" readonly=""> 

<?php }else{ ?>
 
   <input value="<?php echo get_client_ip2(); ?>" name="ipAddress" type="text" class="form-control" readonly="">
<?php } ?>


                             
                              <small class="form-text">Network/IP Address</small>
                              </div>
                                 
                        </div>
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Description</label>
                              <div class="col-sm-8">
                              <textarea class="form-control" style="resize: none;" rows="3" name="description"></textarea>
                              <small class="form-text">Some client descriptions like, locations, etc.</small>
                              </div>
                                 
                        </div>
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Client Number</label>
                              <div class="col-sm-8">
                              <select name="clientNumber" class="form-control">
                              
                              <option>1</option>
                              <option>2</option>
                              <option>3</option>
                              <option>4</option>
                              <option>5</option>
                              <option>6</option>
                              <option>7</option>
                              
                              </select>
                              <small class="form-text">Client Number</small>
                              </div>
                                 
                        </div>
                        
                        </div>
                     
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addClientComp" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end addSubjKinder Modal -->
                  
                  