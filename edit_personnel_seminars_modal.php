




<!-- ############################## PERSONNEL TABLES #########################################-->
    
    
                        <!-- EDIT SEMINAR Modal -->
                          <div id="editPersonnel_seminars<?php echo $ps_row['ps_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_personnel_tables.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                              <input name="ps_id" value="<?php echo $ps_row['ps_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Edit Seminar</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                          <div class="form-group row">
                              
                                              <div class="col-sm-12">
                                                <div class="row">
                                  
                                                  <div class="col-md-12">
                                                    <input value="<?php echo $ps_row['seminar_title']; ?>" name="purpose_title" type="text" class="form-control" />
                                                    <small class="form-text">Title</small>
                                                  </div>
                                                  
                                                  <div class="col-md-12">
                                                    <input value="<?php echo $ps_row['seminar_desc']; ?>" name="description" type="text" class="form-control" />
                                                    <small class="form-text">Description</small>
                                                  </div>
                                                  
                                                  <div class="col-md-12">
                                                    <input value="<?php echo $ps_row['seminar_venue']; ?>" name="location_venue" type="text" class="form-control" />
                                                    <small class="form-text">Venue</small>
                                                  </div>
                                                  
                                                  <div class="col-md-6">
                                                    <input value="<?php echo substr($ps_row['event_date'], 0, 10); ?>" name="sem_date_from" type="text" class="form-control" />
                                                    <small class="form-text">From</small>
                                                  </div>
                                                  
                                                  <div class="col-md-6">
                                                    <input value="<?php echo substr($ps_row['event_date'], 13, 10); ?>" name="sem_date_to" type="text" class="form-control" />
                                                    <small class="form-text">To</small>
                                                  </div> 
                                                  
                                                </div>
                                              </div>
                                            </div>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="update_seminar" type="submit" class="btn btn-success">Update</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end EDIT SEMINAR Modal -->
                          
                          
                          
                          <!-- delete SEMINAR Modal -->
                          <div id="deletePersonnel_seminars<?php echo $ps_row['ps_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                               <form action="save_add_personnel_tables.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                                <input name="ps_id" value="<?php echo $ps_row['ps_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Seminar</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete Seminar: <br /><br /><?php echo $ps_row['seminar_title']; ?>?
                                <br />
                                <small class="form-text"><strong>Venue:</strong> <?php echo $ps_row['seminar_venue']; ?> | <strong>Date:</strong> <?php echo $ps_row['event_date']; ?></small>
                                </h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="delete_seminar" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete SEMINAR Modal -->