
<!-- ############################## PERSONNEL TABLES #########################################-->
    
    
                        <!-- EDIT editPersonnel_educ_bg Modal -->
                          <div id="editService_record<?php echo $sr_row['sr_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-lg">
                              <div class="modal-content">
                              <form action="save_add_personnel_tables.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                              <input name="sr_id" value="<?php echo $sr_row['sr_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Edit Service Record</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                          <div class="form-group row">
                              
                              
                                          <div class="col-sm-12">
                                          <h3><small style="font-size: small;">(If married woman, give also full maiden name. Ignore this form if otherwise.)</small></h3>
                                          
                                            <div class="row">
                                            
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['maid_lname']; ?>" name="maid_lname" type="text" class="form-control" />
                                                <small class="form-text">Last Name</small>
                                              </div>
                                              
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['maid_fname']; ?>" name="maid_fname" type="text" class="form-control" />
                                                <small class="form-text">First Name</small>
                                              </div>
                                              
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['maid_mname']; ?>" name="maid_mname" type="text" class="form-control" />
                                                <small class="form-text">Middle Name</small>
                                              </div> 
                                              
                                            </div>
                                            
                                          </div>
                                          
                                          
                                          <div class="col-sm-12">
                                          
                                            <div class="row">
                                              <div class="col-md-6">
                                              <h3>SERVICES <small>(Inclusive Dates)</h3>
                                              </div>
                                              <div class="col-md-6">
                                              <a href="save_add_personnel_tables_appoint_date.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&sr_id=<?php echo $sr_row['sr_id']; ?>&appointment_date=<?php echo substr($sr_row['serv_date_from'], 5, 2).'/'.substr($sr_row['serv_date_from'], 8, 2).'/'.substr($sr_row['serv_date_from'], 0, 4); ?>" class="btn btn-warning btn-sm pull-right" style="color: green;">Set as Date of Appointment</a></small>
                                              </div>
                                              <div class="col-md-6">
                                                <input value="<?php echo $sr_row['serv_date_from']; ?>" name="serv_date_from" type="date" class="form-control" />
                                                <small class="form-text">From</small>
                                              </div>
                                              
                                              <div class="col-md-6">
                                                <input value="<?php echo $sr_row['serv_date_to']; ?>" name="serv_date_to" type="date" class="form-control" />
                                                <small class="form-text">To</small>
                                              </div> 
                                              
                                            </div>
                                            
                                          </div>
                                          
                                          
                                          <div class="col-sm-12">
                                          <h3>RECORD OF APPOINTMENT</h3>
                                          
                                            <div class="row">
                                            
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['roa_designation']; ?>" name="roa_designation" list="DSGTN_list" type="text" class="form-control" />
                                                <small class="form-text">Designation</small>
                                                
                                                
                                                <datalist id="DSGTN_list">
                                                <?php
                                                
                                                $dsgtnList_stmt = $conn->prepare("SELECT des_name FROM designation");
                                                $dsgtnList_stmt->execute();
                                                $dsgtnList_query = $dsgtnList_stmt;
                                                while($dsgtn_row = $dsgtnList_query->fetch()){ ?>
                                                
                                                <option value="<?php echo $dsgtn_row['des_name']; ?>"><?php echo $dsgtn_row['des_name']; ?></option>
                                                
                                                <?php } ?>
                                                </datalist>
                                                
                                              </div>
                                              
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['roa_status']; ?>" name="roa_status" list="STATUS_list_<?php echo $sr_row['sr_id']; ?>" type="text" class="form-control" />
                                                <small class="form-text">Status</small>
                                                
                                                <datalist id="STATUS_list_<?php echo $sr_row['sr_id']; ?>">
                                                <?php
                                                $statusList_stmt = $conn->prepare("SELECT emp_stat_name FROM emp_status ORDER BY emp_stat_name");
                                                $statusList_stmt->execute();
                                                $statusList_query = $statusList_stmt;
                                                while($status_row = $statusList_query->fetch()){ ?>
                                                  <option value="<?php echo $status_row['emp_stat_name']; ?>"><?php echo $status_row['emp_stat_name']; ?></option>
                                                <?php } ?>
                                                </datalist>
                                              </div> 
                                              
                                            </div>
                                            
                                          </div>
                                          
                                          
                                          <div class="col-sm-12">
                                          
                                            <div class="row">
                                            
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['monthly_salary']; ?>" name="monthly_salary" id="monthly_salary_<?php echo $sr_row['sr_id']; ?>" type="number" step="0.001" min="0" class="form-control monthly-salary-input" />
                                                <small class="form-text">Monthly Salary</small>
                                              </div>
                                              
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['annual_salary']; ?>" name="annual_salary" id="annual_salary_<?php echo $sr_row['sr_id']; ?>" type="number" step="0.001" min="0" class="form-control annual-salary-input" readonly style="background-color: #f0f0f0;" />
                                                <small class="form-text">Annual Salary (Auto-computed)</small>
                                              </div>
                                              
                                            </div>
                                            
                                          </div>
                                          
                                          
                                          <div class="col-sm-12">
                                          
                                            <div class="row">
                                              
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['office_appointment']; ?>" name="office_appointment" list="OA_list" type="text" class="form-control" />
                                                <small class="form-text">Office of Appointment</small>
                                                
                                                <datalist id="OA_list">
                                                <?php
                                                
                                                $fnameList_stmt = $conn->prepare("SELECT dept_office_name FROM dept_offices");
                                                $fnameList_stmt->execute();
                                                $fnameList_query = $fnameList_stmt;
                                                while($fnlq_row = $fnameList_query->fetch()){ ?>
                                                
                                                <option value="<?php echo $fnlq_row['dept_office_name']; ?>"><?php echo $fnlq_row['dept_office_name']; ?></option>
                                                
                                                <?php } ?>
                                                </datalist>
                                      
                                              </div> 
                                              
                                            </div>
                                            
                                          </div>
                                          
                                          
                                          <div class="col-sm-12">
                                          <h3>SEPARATION</h3>
                                          
                                            <div class="row">
                                            
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['separate_date']; ?>" name="separate_date" type="date" class="form-control" />
                                                <small class="form-text">Date</small>
                                              </div>
                                              
                                              <div class="col-md-12">
                                                <input value="<?php echo $sr_row['separate_cause']; ?>" name="separate_cause" type="text" class="form-control" />
                                                <small class="form-text">Cause</small>
                                              </div> 
                                              
                                            </div>
                                            
                                          </div>
                 
                                          
                                        </div>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="update_servRecord" type="submit" class="btn btn-success">Update Record</button>
                                  <button name="add_servRecord" type="submit" class="btn btn-info">Save as New Record</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end EDIT editPersonnel_educ_bg Modal -->
                          
                          
                          
                          <!-- delete student Modal -->
                          <div id="deleteService_record<?php echo $sr_row['sr_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                               <form action="save_add_personnel_tables.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                                <input name="sr_id" value="<?php echo $sr_row['sr_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Service Record</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete Service Record: <br /><br /><?php echo $sr_row['roa_designation'].' - '.$sr_row['roa_status']; ?>?
                                <br />
                                <small class="form-text"> </small>
                                </h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="delete_servRecord" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete student Modal -->