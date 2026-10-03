                        <!-- edit Time Sched Modal -->
                          <div id="encodeDL<?php echo $staff_row['RFTag_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="encode_daily_log.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Encode Daily Log</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                                    <div class="col-lg-12">
                                        <div class="row">
                                        <h3>Set Date</h3>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                           
                                      <div class="col-lg-12">
                                        <input name="logDate" value="<?php echo date('Y-m-d'); ?>" type="date" class="form-control" required="" />
                                        <small class="form-text">Select Date</small>
                                      </div> 
                                    </div>
                             
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="updateTimeSched" type="submit" class="btn btn-primary">Proceed</button>
                                </div>
                                </form>

                              </div>
                            </div>
                          </div>
                          <!-- end Edit Time Sched Modal -->  
                          