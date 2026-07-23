                <!-- edit Class Modal -->
                  <div id="print_DTRSummary<?php echo $staff_row['RFTag_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="checkPrintDetails.php?dept=<?php echo $_GET['dept']; ?>" method="POST">
                      <input name="class_id" value="<?php echo $staff_row['class_id']; ?>" type="hidden" />
                      <input name="RFTag_id" value="<?php echo $staff_row['RFTag_id']; ?>" type="hidden" />
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">DTR Summary - <?php echo $staff_row['fname']." ".$staff_row['mname']." ".$staff_row['lname']; ?></h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        
                  
                  
                    
                        <div style="margin: 10px 10px 10px 12px;" class="form-group row">
                               
                              <div class="col-lg-12">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <label>Select Date From</label>
                                            <input type="date" name="dateFrom" class="form-control" value="<?php echo date('Y-m-01'); ?>" />
                                          </div>
                                          <div class="col-md-6">
                                            <label>Select Date To</label>
                                            <input type="date" name="dateTo" class="form-control" value="<?php echo date('Y-m-d'); ?>" />
                                          
                                          </div>
                                        </div>
                                </div> 
            
                              
                              
                            </div>
                    
             
                         
                          <div class="modal-footer">
                          <button name="checkPrintDetailsMonthly" type="submit" class="btn btn-primary">Print Preview</button>
                          </div>  
                         
 
                            
                        </form>     
                      </div>
                    </div>
                  </div>
                  <!-- end edit Class Modal -->