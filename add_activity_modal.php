            
            <!-- addSubjKinder Modal -->
                  <div id="addSubjKinder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                        
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Activity</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <form action="save_add_activity.php?mm=<?php echo $_GET['mm']; ?>&yyyy=<?php echo $_GET['yyyy']; ?>" method="POST">
                        <div class="modal-body">
                        
                        
                            <div class="form-group row">
                              <div class="col-sm-12">
                                <input name="event_title" type="text" class="form-control" style="text-transform: uppercase;">
                                 <small>Activity / Event Title</small>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <div class="col-sm-12">
                                <input name="event_description" type="text" class="form-control" style="text-transform: uppercase;">
                                 <small>Activity / Event Description</small>
                              </div>
                            </div>
                            
                            
                            <div class="form-group row">
                              <div class="col-sm-12">
                                <input type="date" name="activity_date" class="form-control" required="true" />
                                <small>Date of Activity</small>
                              </div>
                            </div>
                            
 
                            
                            <div class="form-group row">
                   
                              <div class="col-sm-12">
                                <div class="row">
                                   
                                  <div class="col-md-12">
                                    <select name="act_type" class="form-control">
                                    <option>-</option>
                                    <option>Regular Holiday</option>
                                    <option>Special Holiday</option>
                                    <option>Regular Working Holiday</option>
                                    <option>Special Working Holiday</option>
                                    <option>City/Municipal Activity</option>
                                    <option>Work Suspension</option>
                                    </select>
                                    <small class="form-text">Type</small>
                                  </div>
                                  
                                  <div class="col-md-12">
                                    <select name="status" class="form-control">
                                    <option>-</option>
                                    <option>Display to DTR</option>
                                    </select>
                                    <small class="form-text">Status</small>
                                  </div>
                         
                                  
                                </div>
                              </div>
                            </div>
                            
                            
                             
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addActivity" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end addSubjKinder Modal -->
                  
                  