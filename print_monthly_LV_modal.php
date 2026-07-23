                <!-- edit Class Modal -->
                  <div id="print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="checkPrintDetails.php?dept=<?php echo $_GET['dept']; ?>&RFTag_id=<?php echo $staff_row['RFTag_id']; ?>" method="POST">
                       
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">PRINT MONTHLY LOG VALIDATION</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        
                  
                  
                    
                        <div style="margin: 10px 10px 10px 12px;" class="form-group row">
                               
                              <div class="col-lg-12">
                                        <div class="row">
                                          <div class="col-md-12">
                                             <label>Select Date</label>
                                            <input type="month" name="dateFrom" class="form-control" />
                                          </div>
                                        </div>
                                </div> 
            
                              
                              
                            </div>
                    
             
                         
                          <div class="modal-footer">
                          <button name="checkPrintDetailsMonthly_log_validation" type="submit" class="btn btn-primary">Print Preview</button>
                          </div>  
                         
 
                            
                </form>     
                      </div>
                    </div>
                  </div>
                  <!-- end edit Class Modal -->