                        <!-- edit sched Flow Modal -->
                          <div id="editSchedFlow<?php echo $schedule_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_schedule.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $_GET['shift_id']; ?>&shift=<?php echo $_GET['shift']; ?>&type=<?php echo $_GET['type']; ?>" method="POST">
                              
                              <input name="schedule_id" value="<?php echo $schedule_id; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Update Class Flow</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                <h5>Type: <?php echo $sched_row['type']; ?></h5>
                                <h5>Day: <?php echo $sched_row['day']; ?></h5>
                                <h5>Shift:
                                <?php
                               
                                   echo $sched_row['gradeLevel']." - ".$sched_row['section'];
                                
                                  ?>
                                
                                </h5>
                                <hr />
                              <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Select Flow Status</label>
                              
                              <div class="col-sm-10">
                               <div class="row">
                                  <div class="col-md-6">
                                  <select name="currentFlow" class="form-control">
                                  <option><?php echo $sched_row['currentFlow']; ?></option>
                                  <option>AM IN</option>
                                  <option>PM IN</option>
                                  
                                  </select>
                                  <small class="form-text">Flow</small>
                                  </div>
                                  
                                  
                                  <div class="col-md-6">
                                  <select name="lateStatus" class="form-control">
                                  <option><?php echo $sched_row['lateStatus']; ?></option>
                                  <option>on</option>
                                  <option>off</option>
                                  </select>
                                  <small class="form-text">Late Status</small>
                                  </div>
                                  
                               </div>
                                
                                    
                                  </div>
                                </div>
                                
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="updateSchedFlow" type="submit" class="btn btn-primary">Update</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end sched Flow Modal -->
                          
                          
                          <!-- edit sched Flow2 Modal -->
                          <div id="editSchedFlow2<?php echo $schedule_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_schedule.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $_GET['shift_id']; ?>&shift=<?php echo $_GET['shift']; ?>&type=<?php echo $_GET['shift']; ?>" method="POST">
                              
                              <input name="schedule_id" value="<?php echo $schedule_id; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Update Class Flow</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                                <h5>Day: <?php echo $sched_row['day']; ?></h5>
                                <h5>Shift:
                                <?php
                               
                                   echo $sched_row['gradeLevel']." - ".$sched_row['section'];
                                
                                  ?>
                                
                                </h5>
                                <hr />
                              <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Select Flow Status</label>
                              
                              <div class="col-sm-10">
                               <div class="row">
                                  <div class="col-md-6">
                                  <select name="currentFlow" class="form-control">
                                  <option><?php echo $sched_row['currentFlow2']; ?></option>
                                  
                                  <option>AM OUT</option>
                                  <option>PM OUT</option>
                                  </select>
                                  <small class="form-text">Flow</small>
                                  </div>
                                  
                                  
                                  <div class="col-md-6">
                                  <select name="lateStatus" class="form-control">
                                  <option><?php echo $sched_row['lateStatus2']; ?></option>
                                  <option>on</option>
                                  <option>off</option>
                                  </select>
                                  <small class="form-text">Late Status</small>
                                  </div>
                                  
                               </div>
                                
                                    
                                  </div>
                                </div>
                                
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="updateSchedFlow2" type="submit" class="btn btn-primary">Update</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end sched Flow2 Modal -->
                         
                         
                         
                         
                         
                         
                         
                          <!-- edit Time Sched Modal -->
                          <div id="editTimeSched<?php echo $schedule_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_schedule.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $_GET['shift_id']; ?>&shift=<?php echo $_GET['shift']; ?>&type=<?php echo $_GET['shift']; ?>" method="POST">
                              
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Edit Schedule</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                  
                                <div class="modal-body">
                                <h5>Type: <?php echo $sched_row['type']; ?></h5>
                                <h5>Day: <?php echo $sched_row['day']; ?></h5>
                                <h5>Dept / Office: <?php echo $don_row['dept_office_name']; ?></h5>
                                <h5>Shift: <?php echo $_GET['shift']; ?>
                                <?php
                               
                                   //echo $sched_row['gradeLevel']." - ".$sched_row['section'];
                                
                                  ?>
                                
                                </h5>
                                <hr />
                                
                              <input type="hidden" name="schedule_id" value="<?php echo $schedule_id; ?>" />
                              <input type="hidden" name="type" value="<?php echo $sched_row['type']; ?>" />
                              <input type="hidden" name="day" value="<?php echo $sched_row['day']; ?>" />
                              
                              <div class="form-group row">
                              <label class="col-sm-3 form-control-label">AM - IN</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="am_in_hr" class="form-control">
                                    <option><?php echo substr($sched_row['am_IN'], 0, 2); ?></option>
                                  
                                    <option>00</option>
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    </select>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_min" class="form-control">
                                    <option><?php echo substr($sched_row['am_IN'], 3, 2); ?></option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_ampm" class="form-control">
                                    <option><?php echo substr($sched_row['am_IN'], 6, 2); ?></option>
                                    <option>am</option>
                                    <option>pm</option>
                                    </select>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">AM - IN (Late)</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="am_in_hr_late" class="form-control">
                                    <option><?php echo substr($sched_row['am_IN_co'], 0, 2); ?></option>
                                  
                                    <option>00</option>
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    </select>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_min_late" class="form-control">
                                    <option><?php echo substr($sched_row['am_IN_co'], 3, 2); ?></option>
                              
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_ampm_late" class="form-control">
                                    <option><?php echo substr($sched_row['am_IN_co'], 6, 2); ?></option>
                                    <option>am</option>
                                    <option>pm</option>
                                    </select>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">AM - OUT</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="am_out_hr" class="form-control">
                                    <option><?php echo substr($sched_row['am_OUT'], 0, 2); ?></option>
                                    <option>00</option>
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    </select>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_out_min" class="form-control">
                                    <option><?php echo substr($sched_row['am_OUT'], 3, 2); ?></option>
                                          
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_out_ampm" class="form-control">
                                    <option><?php echo substr($sched_row['am_OUT'], 6, 2); ?></option>
                                    <option>am</option>
                                    <option>pm</option>
                                    </select>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
 
                            
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">PM - IN</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="pm_in_hr" class="form-control">
                                    <option><?php echo substr($sched_row['pm_IN'], 0, 2); ?></option>
                                    <option>00</option>
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    </select>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_min" class="form-control">
                                    <option><?php echo substr($sched_row['pm_IN'], 3, 2); ?></option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_ampm" class="form-control">
                                    <option><?php echo substr($sched_row['pm_IN'], 6, 2); ?></option>
                                    <option>am</option>
                                    <option>pm</option>
                                    </select>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">PM - IN (Late)</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="pm_in_hr_late" class="form-control">
                                    <option><?php echo substr($sched_row['pm_IN_co'], 0, 2); ?></option>
                                    <option>00</option>
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    </select>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_min_late" class="form-control">
                                    <option><?php echo substr($sched_row['pm_IN_co'], 3, 2); ?></option>
                                    
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_ampm_late" class="form-control">
                                    <option><?php echo substr($sched_row['pm_IN_co'], 6, 2); ?></option>
                                    <option>pm</option>
                                    <option>am</option>
                                    </select>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">PM - OUT</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="pm_out_hr" class="form-control">
                                    <option><?php echo substr($sched_row['pm_OUT'], 0, 2); ?></option>
                                    <option>00</option>
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    </select>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_out_min" class="form-control">
                                    <option><?php echo substr($sched_row['pm_OUT'], 3, 2); ?></option>
                                   
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_out_ampm" class="form-control">
                                    <option><?php echo substr($sched_row['pm_OUT'], 6, 2); ?></option>
                                    <option>pm</option>
                                    <option>am</option>
                                    </select>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                             
                              </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="updateTimeSched" type="submit" class="btn btn-primary">Update</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end Edit Time Sched Modal -->
                          
                          
                            <!-- delete sched Modal -->
                          <div id="deleteSched<?php echo $schedule_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_schedule.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $_GET['shift_id']; ?>&shift=<?php echo $_GET['shift']; ?>&type=<?php echo $_GET['shift']; ?>" method="POST">
                              
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Schedule</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                <h3>Do you want to deete schedule?</h3>
                                <br />
                                
                                <h4>Day: <?php echo $sched_row['day']; ?></h4>
                                <br />
                                <h4>Dept / Office: <?php echo $don_row['dept_office_name']; ?></h4>
                                <br />
                                <h4>Shift: <?php echo $_GET['shift']; ?></h4>
                              
                              <input name="schedule_id" value="<?php echo $schedule_id; ?>" type="hidden" />
                              <input type="hidden" name="sched_type" value="<?php echo $sched_row['sched_type']; ?>" />
                              <input type="hidden" name="day" value="<?php echo $sched_row['day']; ?>" />
                              <input type="hidden" name="gradeLevel" value="<?php echo $sched_row['gradeLevel']; ?>" />
                              <input type="hidden" name="strand" value="<?php echo $sched_row['strand']; ?>" />
                              <input type="hidden" name="section" value="<?php echo $sched_row['section']; ?>" />
                               
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deleteSched" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete sched Modal -->