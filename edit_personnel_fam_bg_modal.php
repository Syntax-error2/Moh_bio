
<!-- ############################## PERSONNEL TABLES #########################################-->
    
    
                        <!-- EDIT SEMINAR Modal -->
                          <div id="editPersonnel_seminars<?php echo $ps_row['fm_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_personnel_tables.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                              <input name="fm_id" value="<?php echo $ps_row['fm_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">EDIT FAMILY MEMBER</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                                    <div class="form-group row">
                                    
                                        <div class="col-sm-12">
                                            <div class="row">
                                            
                                                <div class="col-md-12">
                                                <input value="<?php echo $ps_row['fullname']; ?>" name="fullname" type="text" class="form-control" placeholder="Enter Lastname, First Name, Middle Name" required="" />
                                                <small class="form-text">Family Member's Fullname</small>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                <select name="sex" class="form-control">
                                                <option><?php echo $ps_row['sex']; ?></option>
                                                <option>Male</option>
                                                <option>Female</option>
                                                </select>
                                                <small class="form-text">Sex</small>
                                                </div>
                                                
                                                <div class="col-md-8">
                                                <select name="relationship" class="form-control">
                                                <option><?php echo $ps_row['relationship']; ?></option>
                                                <option>Spouse</option>
                                                <option>Child</option>
                                                <option>Parents</option>
                                                <option>Siblings</option>
                                                </select>
                                                <small class="form-text">Relationship</small>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                <input value="<?php echo $ps_row['contact_num']; ?>" name="contact_num" type="text" class="form-control" placeholder="Enter contact number..." />
                                                <small class="form-text">Contact Number (Optional)</small>
                                                </div>
                                                  
                                            </div>
                                        </div>
                                    </div>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="update_fam_bg" type="submit" class="btn btn-success">Update</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end EDIT SEMINAR Modal -->
                          
                          
                          
                          <!-- delete SEMINAR Modal -->
                          <div id="deletePersonnel_seminars<?php echo $ps_row['fm_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                               <form action="save_add_personnel_tables.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                                <input name="fm_id" value="<?php echo $ps_row['fm_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">DELETE FAMILY MEMBER</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete family member <?php echo $ps_row['fullname']; ?>?
                                <br /><br />
                                <small class="form-text"><strong>Relationship:</strong> <?php echo $ps_row['relationship']; ?></small>
                                </h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="delete_fam_bg" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete SEMINAR Modal -->