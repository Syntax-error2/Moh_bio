          
            <!-- addSubjKinder Modal -->
                  <div id="addPersonnel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="save_add_personnel.php?dept=<?php echo $_GET['dept']; ?>" method="POST" enctype="multipart/form-data">
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Personnel</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                        
                        <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Personnel ID</label>
                              <div class="col-sm-10">
                              
                              <div class="row">
                                <div class="col-md-12">
                                <input name="personnel_id_code" type="text" class="form-control" required="" />
                                
                                </div>
    
                              </div>
                                
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Personnel Image</label>
                              <div class="col-sm-10">
                              
                              
                              <div class="row">
                              
                                <div class="col-md-8">
                                <input class="form-control" type="file" name="file" id="imgInp" />
                                <small class="form-text pull-right">Image preview <i class="fa fa-arrow-right"></i></small>
                                </div>
                                
                                <div class="col-md-4">
                                <img width="100%" height="100%" class="img-fluid rounded" id="blah" src="#" alt="your image" />
                                </div>
                                
                              </div>
                              
                              </div>
                            </div>
                        
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Fullname</label>
                              <div class="col-sm-10">
                              
                              <div class="row">
    
                                <div class="col-md-6">
                                <input name="fname" type="text" class="form-control" required="true" />
                                <small class="form-text">First Name</small>
                                </div>
                                
     
                                 
                                <div class="col-md-6">
                                <input name="mname" type="text" class="form-control" />
                                <small class="form-text">Middle Name</small>
                                </div>
                                
                              </div>
                                
                              </div>
                            </div>
                            
                            
                            
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label"></label>
                              <div class="col-sm-10">
                              
                              <div class="row">
     
                                 
                                <div class="col-md-8">
                                <input name="lname" type="text" class="form-control" required="true" />
                                <small class="form-text">Last Name</small>
                                </div>
                       
                             
                                  <div class="col-md-4">
                                     
                                    <select name="suffix" class="form-control">
                                    <option>-</option>
                                    <option>JR.</option>
                                    <option>SR.</option>
                                    <option>III</option>
                                    <option>IV</option>
                                    </select>
                                    <small class="form-text">Suffix</small>
                                  </div>
                              </div>
                                
                              </div>
                            </div>
 
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="saveAddPersonnel" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end addSubjKinder Modal -->
                  
                  