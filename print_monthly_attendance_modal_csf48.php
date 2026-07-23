                <!-- edit Class Modal -->
                  <div id="print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="checkPrintDetails.php?dept=<?php echo $_GET['dept']; ?>" method="POST">
                      <input name="RFTag_id" value="<?php echo $staff_row['RFTag_id']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Civil Service Form 48 - <?php echo $staff_row['fname']." ".$staff_row['mname']." ".$staff_row['lname']; ?></h5>
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
                                        
                                        <div class="row">
                                          <div class="col-md-12">
                                            <label>Type of Docs</label>
                                            <select name="doc_type" class="form-control">
                                            <option>CS Form 48 (1-15)</option>
                                            <option>CS Form 48 (16-31)</option>
                                            <option>CS Form 48</option>
                                            </select>
                                          </div>
                                        </div>
                                        
                                </div> 
            
                              
                              
                            </div>
                    
             
                         
                          <div class="modal-footer">
                          <button name="checkPrintDetailsMonthly_csf48" type="submit" class="btn btn-primary">Print Preview</button>
                          </div>  
                         
 
                            
                </form>     
                      </div>
                    </div>
                  </div>
                  <!-- end edit Class Modal -->
                  
                  
                  
                  
                  
                  
                  