            
            <!-- addSubjKinder Modal -->
                  <div id="editClientComp<?php echo $client_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Client Computer</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <form action="save_add_client_comp.php?client_id=<?php echo $client_id; ?>" method="POST">
                        
                        <div class="modal-body">
                 
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Computer Name</label>
                              <div class="col-sm-8">
                              <input value="<?php echo $subjK_row['compName']; ?>" name="compName" type="text" class="form-control" readonly="">
                              <small class="form-text">Computer Name</small>
                              </div>
                                 
                        </div>
                        
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">IP Address</label>
                              <div class="col-sm-8">
                                <input value="<?php echo $subjK_row['ipAddress']; ?>" name="ipAddress" type="text" class="form-control" readonly="">
                              <small class="form-text">Network/IP Address</small>
                              </div>
                                 
                        </div>
                        
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Description</label>
                              <div class="col-sm-8">
                              <textarea class="form-control" style="resize: none;" rows="3" name="description"><?php echo $subjK_row['description']; ?></textarea>
                              <small class="form-text">Some client descriptions like, locations, etc.</small>
                              </div>
                                 
                        </div>
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Client Number</label>
                              <div class="col-sm-8">
                              <select name="clientNumber" class="form-control">
                              <option><?php echo $subjK_row['clientNumber']; ?></option>
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
                          <a style="color: white;" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="editClientComp" type="submit" class="btn btn-primary">Update</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end addSubjKinder Modal -->
                  
                  
                        <!-- delete student Modal -->
                          <div id="deleteClientComp<?php echo $client_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_client_comp.php?client_id=<?php echo $client_id; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Client Computer</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete client computer:<br /><br /><?php echo $subjK_row['ipAddress']." - ".$subjK_row['compName']; ?>?
                                 
                                </h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deleteClient" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete student Modal -->