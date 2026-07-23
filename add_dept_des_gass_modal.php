               
                  <!-- addClassKinder Modal -->
                  <div id="addDept_Off" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="save_add_dept_des_gass.php" method="POST">
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Department / Office</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
  
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Department / Office</label>
                              <div class="col-sm-10">
                                <input name="dept_office_name" type="text" class="form-control" placeholder="Dept. / Office name..." />
                              </div>
                            </div> 
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addDept" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end addClassKinder Modal -->
                  
                  
                  <!-- add Designation Modal -->
                  <div id="addDes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="save_add_dept_des_gass.php" method="POST">
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Designation</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
  
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Designation</label>
                              <div class="col-sm-10">
                                <input name="des_name" type="text" class="form-control" placeholder="Designation name..." />
                              </div>
                            </div> 
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addDes" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end add Designation Modal -->
                  
                  
                  
                  
                  <!-- add GASS Modal -->
                  <div id="addGASS" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="save_add_dept_des_gass.php" method="POST">
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Salary Grade</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
  
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Salary Grade</label>
                              <div class="col-sm-9">
                                <input name="gass_name" min="1" max="33" step="1" type="number" class="form-control" placeholder="Salary grade..." required />
                              </div>
                            </div> 
                            
                            <div class="form-group row">
                               
                              <label class="col-sm-3 form-control-label">Monthly Rate</label>
                              
                              <div class="col-sm-9">
                                <input name="ratePerDay" min="1.00" max="999999999.99" step="0.01" type="number" class="form-control" placeholder="Monthly rate..." required />
                              </div>
                              
                            </div> 
                      
                            
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addGASS" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end add GASS Modal -->
                  
                  
                  
                  <!-- add Shifts Modal -->
                  <div id="addShift" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="save_add_dept_des_gass.php" method="POST">
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Shift</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
  
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Shift</label>
                              <div class="col-sm-9">
                                <input name="shift_name" type="text" class="form-control" placeholder="Shift name..." />
                              </div>
                            </div> 
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Type</label>
                              <div class="col-sm-9">
                                <select class="form-control" name="type">
                              <option value="Regular Shift">-</option>
                              <option>Regular Shift</option>
                              <option>Night Shift</option>
                              <option>24 Hours Shift</option>
                              </select>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Dept / Office</label>
                              <div class="col-sm-9">
                              <select name="do_id" class="form-control">
                              <option value="0">All</option>
                              <?php
                              $dept_offices_stmt = $conn->prepare("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
                              $dept_offices_stmt->execute();
                              $emp_stat_query = $dept_offices_stmt;
                              while($es_row=$emp_stat_query->fetch()){
                              ?>
                              <option value="<?php echo $es_row['do_id']; ?>"><?php echo $es_row['dept_office_name']; ?></option>
                              <?php } ?>
                              </select>
                              </div>
                            </div>
                            
                            
                                            
                                            
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addShift" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end add Shifts Modal -->
                  
                  
                  
                  
                  <!-- add Emp Stat Modal -->
                  <div id="addEmpStat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="save_add_dept_des_gass.php" method="POST">
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Status of Appointment</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
  
                            <div class="form-group row">
                            
                            <label class="col-sm-3 form-control-label">Status</label>
                              <div class="col-sm-9">
                                <input name="emp_stat_name" type="text" class="form-control" placeholder="Employment status name..." />
                              </div>
                              
                            <label class="col-sm-3 form-control-label">Type</label>
                              <div class="col-sm-9">
                                <select name="status" class="form-control">
                                <option>-</option>
                                <option>Active</option>
                                <option>Separated</option>
                                </select>
                              </div>
                              
                              <label class="col-sm-3 form-control-label">Position Class</label>
                              <div class="col-sm-9">
                                <select name="position_class" class="form-control">
                                <option>-</option>
                                <option>Career Positions</option>
                                <option>Non-Career Positions</option>
                                </select>
                              </div>
                              
                            </div> 
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addEmpStatus" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end add Emp Stat Modal -->
                  
                  
                  
                  
                  <!-- apply leave Modal -->
                  <div id="applyLeave" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="save_add_dept_des_gass.php" method="POST">
                      
                      <?php
                        function randomcode() {
                        $var = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        srand((double)microtime()*1000000);
                        $i = 0;
                        $code = '';
                        while ($i <= 9) {
                        $num = rand() % 33;
                        $tmp = substr($var, $num, 1);
                        $code = $code . $tmp;
                        $i++;
                        }
                        return $code;
                        } 
                        
                        ?>

                      <input name="lap_code" class="form-control" type="hidden" value="<?php echo randomcode(); ?>" />
                      
                        <div class="modal-header">  
                          <h5 id="exampleModalLabel" class="modal-title">LEAVE APPLICATION</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
  
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Leave Type</label>
                              <div class="col-sm-10">
                                <select name="leave_type" class="form-control">
                                <option>-</option>
                                <option>Vacation Leave</option>
                                <option>Sick Leave</option>
                                <option>Maternity Leave (RA 8282)</option>
                                <option>Paternity Leave (RA 8187)</option>
                                <option>Parental Leave for Solo Parents (RA 8972)</option>
                                <option>Others, please specify...</option>
                                </select>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Description</label>
                              <div class="col-sm-10">
                                <input name="leave_type_desc" type="text" class="form-control" />
                              </div>
                            </div> 
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="leave_application" type="submit" class="btn btn-primary">Submit Application</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end apply leave Modal -->
                  
                  
                  
                  
                  
                  