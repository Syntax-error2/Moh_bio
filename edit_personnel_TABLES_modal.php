

            <!-- ############################## PERSONNEL TABLES #########################################-->
    
    
                        <!-- EDIT editPersonnel_educ_bg Modal -->
                          <div id="editPersonnel_educ_bg<?php echo $peb_row['eb_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_personnel.php?dept=<?php //echo $editPer_row['do_id']; ?>" method="POST">
                              <input name="personnel_id" value="<?php //echo $personnel_id; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Edit Educational Background</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                          <div class="form-group row">
                                          
                                          <div class="col-sm-12">
                                            <div class="row">
                                            
                                             <div class="col-md-12">
                                                <select name="degree" class="form-control">
                                                <option>Bachelors</option>
                                                <option>Masters</option>
                                                <option>Doctors</option>
                                                <option>Others</option>
                                                </select>
                                                <small class="form-text">Degree</small>
                                              </div>
                                              
                                             <div class="col-md-4">
                                                <input name="units" type="number" class="form-control"/>
                                                <small class="form-text">Units</small>
                                              </div>
                                              
                                              <div class="col-md-8">
                                                <input name="year_grad" type="month" class="form-control" />
                                                <small class="form-text">Year Graduated</small>
                                              </div>
                                              
                                              <div class="col-md-12">
                                                <input name="course_details" type="text" class="form-control" />
                                                <small class="form-text">Course Description</small>
                                              </div>
                                              
                                              <div class="col-md-12">
                                                <input name="school_name" type="text" class="form-control" />
                                                <small class="form-text">School Graduated</small>
                                              </div>
                                              
                                            </div>
                                          </div>
                                        </div>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deleteStudent" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end EDIT editPersonnel_educ_bg Modal -->
                          
                          
                          
                          <!-- delete student Modal -->
                          <div id="deletePersonnel_educ_bg<?php echo $peb_row['eb_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_personnel.php?dept=<?php //echo $editPer_row['do_id']; ?>" method="POST">
                              <input name="personnel_id" value="<?php //echo $personnel_id; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Education Background</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete personnel:<br /><br /><?php //echo $editPer_row['lname'].", ".$editPer_row['fname']." ".$finalMName; ?>?
                                <br />
                                <small class="form-text"> </small>
                                </h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deleteStudent" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete student Modal -->