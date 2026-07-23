<!DOCTYPE html>
<html>

  <?php
  
  include('session.php');
  include('dbcon.php');
  include('header.php');
  
  ?>
  
  
  <?php
  
 
  $get_do_id=$_GET['do_id'];
  
  ?>
  

<?php //include('loaderFX.php'); ?>
 
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            
            <?php
            $do_id = $_GET['do_id'] ?? '';
            $shift_id = $_GET['shift_id'] ?? '';
            $do_id_name_stmt = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
            $do_id_name_stmt->execute([':do_id' => $do_id]);
            $do_id_name_query = $do_id_name_stmt;
            $don_row = $do_id_name_query->fetch();
            
            $schedType_stmt = $conn->prepare("SELECT type FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id");
            $schedType_stmt->execute([':do_id' => $do_id, ':shift_id' => $shift_id]);
            $schedType_query = $schedType_stmt;
            $schedType_row=$schedType_query->fetch();
            ?>
            
            <li class="breadcrumb-item active">Schedule Preferences - <?php echo $don_row['dept_office_name']; ?></li>
 
          </ul>
          
        </div>
      </div>

      <style>
      .page-title-block { margin-bottom: 18px; }
      .page-title-block h2 { margin-bottom: 4px; font-weight: 700; color: #243447; }
      .page-title-block p { margin-bottom: 0; color: #6b7a88; }
      </style>

      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row page-title-block align-items-center">
            <div class="col-lg-8 col-md-8">
              <h2>Schedule Preferences</h2>
              <p>Configure schedule settings by department and shift</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right">
              <?php if($_GET['do_id']!='' AND $_GET['shift_id']!='' AND $_GET['shift']!=''){ ?>
                <a data-toggle="modal" data-target="#addScheduleModal" href="#" class="btn btn-primary"><i class="fa fa-plus"></i> Add Schedule</a>
              <?php }else{ ?>
                <a href="#" title="Please select Dept / Office and Shift to continue..." class="btn btn-secondary disabled"><i class="fa fa-plus"></i> Add Schedule</a>
              <?php } ?>
            </div>
          </div>
        </div>
      </section>
      
    
     
      
      <!-- Header Section-->
      
      <br />
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              

             <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Schedule Settings</h5>
                
                <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a> 
               
                  
                </div>
                
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                
                <div style="margin-bottom: 3px;" class="tab">
                
                
                <?php
                $do_id_off_query = $conn->query("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
                while ($do_row = $do_id_off_query->fetch()) 
                {  ?>
                
                
                
                <?php if($_GET['do_id']==$do_row['do_id']){ ?>
                <a title="List of schedules of <?php echo $do_row['dept_office_name']; ?>" href="schedule_preferences.php?do_id=<?php echo $do_row['do_id']; ?>&shift_id=&shift=&type=" class="tablinks active" style="font-weight: bolder;"><?php echo $do_row['dept_office_name']; ?></a>
                <?php }else{?>
                <a title="List of schedules of <?php echo $do_row['dept_office_name']; ?>" href="schedule_preferences.php?do_id=<?php echo $do_row['do_id']; ?>&shift_id=&shift=&type=" class="tablinks"><?php echo $do_row['dept_office_name']; ?></a>
                <?php } ?>
                
           
                <?php } ?>
                
              
                
                </div>
                
                <?php if($_GET['do_id']!=""){ ?>
     
                
                <div class="dropdown" style="margin-left: 8px;">
                  <button class="dropbtn" style="border: solid 1px lightgray;">REGULAR SHIFT</button>
                  <div class="dropdown-content">
                  <?php
                  
                  $shiftDataRS_stmt = $conn->prepare("SELECT * FROM shifts WHERE (do_id = :do_id OR do_id = '0') AND type = 'Regular Shift' ORDER BY shift_name ASC");
                  $shiftDataRS_stmt->execute([':do_id' => $do_id]);
                  $shiftDataRS_query = $shiftDataRS_stmt;
                  while($sdRS_row = $shiftDataRS_query->fetch()){
                  
                  ?>
                    
                    <a href="schedule_preferences.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $sdRS_row['shift_id']; ?>&shift=<?php echo $sdRS_row['shift_name']; ?>&type=<?php echo $sdRS_row['type']; ?>">
                    <?php echo $sdRS_row['shift_name']; ?>
                    </a>
             
                  <?php } ?>
                  
                  <a href="list_shift.php"><i class="fa fa-plus"></i> Shift</a>
                  </div>
                </div>
 
                <div class="dropdown" style="margin-left: 8px;">
                  <button class="dropbtn" style="border: solid 1px lightgray;">NIGHT SHIFT</button>
                  <div class="dropdown-content">
                  <?php
                  
                  $shiftDataNight_stmt = $conn->prepare("SELECT * FROM shifts WHERE (do_id = :do_id OR do_id = '0') AND type = 'Night Shift' ORDER BY shift_name ASC");
                  $shiftDataNight_stmt->execute([':do_id' => $do_id]);
                  $shiftDataRS_query = $shiftDataNight_stmt;
                  while($sdRS_row = $shiftDataRS_query->fetch()){
                  
                  ?>
                    
                    <a href="schedule_preferences.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $sdRS_row['shift_id']; ?>&shift=<?php echo $sdRS_row['shift_name']; ?>&type=<?php echo $sdRS_row['type']; ?>">
                    <?php echo $sdRS_row['shift_name']; ?>
                    </a>
             
                  <?php } ?>
                  <a href="list_shift.php"><i class="fa fa-plus"></i> Shift</a>
                  </div>
                </div>
                
                
                <div class="dropdown" style="margin-left: 8px;">
                  <button class="dropbtn" style="border: solid 1px lightgray;">24 HOUR SHIFT</button>
                  <div class="dropdown-content">
                  <?php
                  
                  $shiftData24_stmt = $conn->prepare("SELECT * FROM shifts WHERE (do_id = :do_id OR do_id = '0') AND type = '24 Hours Shift' ORDER BY shift_name ASC");
                  $shiftData24_stmt->execute([':do_id' => $do_id]);
                  $shiftDataRS_query = $shiftData24_stmt;
                  while($sdRS_row = $shiftDataRS_query->fetch()){
                  
                  ?>
                    
                    <a href="schedule_preferences.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $sdRS_row['shift_id']; ?>&shift=<?php echo $sdRS_row['shift_name']; ?>&type=<?php echo $sdRS_row['type']; ?>">
                    <?php echo $sdRS_row['shift_name']; ?>
                    </a>
             
                  <?php } ?>
                  <a href="list_shift.php"><i class="fa fa-plus"></i> Shift</a>
                  </div>
                </div>
                <?php } ?>
                
             
                <h3 style="margin: 16px 16px 16px 16px;"><?php echo $don_row['dept_office_name']; ?></h3>
               
                  
                <?php include('list_sched.php'); ?>
                
                </div>
              </div>
              <!-- kinder End-->
    
            
 
            <!-- addSubjKinder Modal -->
                  <div id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      
                      <form action="save_add_schedule.php?do_id=<?php echo $_GET['do_id']; ?>&shift_id=<?php echo $_GET['shift_id']; ?>&shift=<?php echo $_GET['shift']; ?>&type=<?php echo $_GET['type']; ?>" method="POST">
                      <div class="modal-content">
                        <div class="modal-header">
                        
                        <?php
                        $mod_do_id_off_stmt = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                        $mod_do_id_off_stmt->execute([':do_id' => $do_id]);
                        $mod_do_id_off_query = $mod_do_id_off_stmt;
                        $modDOid_row=$mod_do_id_off_query->fetch();
                        
                        ?>
                          <h5 id="exampleModalLabel" class="modal-title">Add Schedule</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>

                        <div class="modal-body">
  
                            
                            <div class="form-group row">
                             
                              <div class="col-sm-12">
                              <small><strong><?php echo $modDOid_row['dept_office_name']; ?> - <?php echo $_GET['shift']; ?></strong><br />
                              Department / Office - Shift</small>
                           
                              </div>

                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Type</label>
                              
                              <div class="col-sm-10">
                              <input value="<?php echo $_GET['type']; ?>" class="form-control form-control-sm" readonly="" />
                              </div> 
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Day</label>
                              
                              <div class="col-sm-10">
                              
                              <table class="table table-bordered">
                              <tr>
                              <td><input name="checkbox2[]" value="Sunday" type="checkbox" />Sun</td>
                              <td><input name="checkbox2[]" value="Monday" type="checkbox" />Mon</td>
                              <td><input name="checkbox2[]" value="Tuesday" type="checkbox" />Tue</td>
                              <td><input name="checkbox2[]" value="Wednesday" type="checkbox" />Wed</td>
                              <td><input name="checkbox2[]" value="Thursday" type="checkbox" />Thu</td>
                              <td><input name="checkbox2[]" value="Friday" type="checkbox" />Fri</td>
                              <td><input name="checkbox2[]" value="Saturday" type="checkbox" />Sat</td>
                              </tr>
                              </table>
                              
                              </div>

                            </div>

                            
                            <?php if($_GET['type']==='Regular Shift'){ ?>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">AM - IN</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="am_in_hr" class="form-control">
                                    <option>05</option>
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
                                    <option>30</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_ampm" class="form-control">
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
                                    <option>07</option>
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
                                    <option>30</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_ampm_late" class="form-control">
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
                                    <option>11</option>
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
                                    <option>30</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_out_ampm" class="form-control">
                                    <option>am</option>
                                    <option>pm</option>
                                    </select>
                                    <small class="form-text">Minutes</small>
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
                                    <option>11</option>
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
                                    <option>30</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_ampm" class="form-control">
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
                                    <option>30</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_ampm_late" class="form-control">
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
                                    <option>04</option>
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
                                    <option>30</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_out_ampm" class="form-control">
                                    <option>pm</option>
                                    <option>am</option>
                                    </select>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            <?php }elseif($_GET['type']==='Night Shift'){ ?>
                            
                            <div class="form-group row">
                              <div class="col-sm-12">
                              <h5>The current day</h5>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">PM - IN</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="pm_in_hr" class="form-control">
                                    <option>06</option>
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
                                    <option>30</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_ampm" class="form-control">
                                    <option>pm</option>
                                    <option>am</option>
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
                                    <option>10</option>
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
                                    <option>15</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="pm_in_ampm_late" class="form-control">
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
                                    <input name="pm_out_hr" value="11" class="form-control" readonly=""/>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="pm_out_min" value="59" class="form-control" readonly=""/>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="pm_out_ampm" value="pm" class="form-control" readonly=""/>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <div class="col-sm-12">
                              <h5>The next day</h5>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">AM - IN</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <input name="am_in_hr" value="12" class="form-control" readonly=""/>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="am_in_min" value="00" class="form-control" readonly=""/>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="am_in_ampm" value="am" class="form-control" readonly=""/>
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
                                    <input name="am_in_hr_late" value="12" class="form-control" readonly=""/>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="am_in_min_late" value="10" class="form-control" readonly=""/>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="am_in_ampm_late" value="am" class="form-control" readonly=""/>
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
                                    <option>06</option>
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
                                    <option>00</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_out_ampm" class="form-control">
                                    <option>am</option>
                                    <option>pm</option>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            <?php }elseif($_GET['type']==='24 Hours Shift'){ ?>
                            
                            <div class="form-group row">
                              <div class="col-sm-12">
                              <h5>The current day</h5>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">AM - IN</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <select name="am_in_hr" class="form-control">
                                    <option>06</option>
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
                                    <option>00</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_ampm" class="form-control">
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
                                    <option>08</option>
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
                                    <option>15</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_in_ampm_late" class="form-control">
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
                                    <input value="11" class="form-control" readonly=""/>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input value="59" class="form-control" readonly=""/>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input value="am" class="form-control" readonly=""/>
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
                                    <input name="pm_in_hr" value="12" class="form-control" readonly=""/>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="pm_in_min" value="00" class="form-control" readonly=""/>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="pm_in_ampm" value="pm" class="form-control" readonly=""/>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            <!-- PM IN Late Values -->
                            <input name="pm_in_hr_late" value="12" type="hidden" class="form-control" readonly=""/>
                            <input name="pm_in_min_late" value="10" type="hidden" class="form-control" readonly=""/>
                            <input name="pm_in_ampm_late" value="pm" type="hidden" class="form-control" readonly=""/>
                                    
                            
                             
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">PM - OUT</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <input name="pm_out_hr" value="11" class="form-control" readonly=""/>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="pm_out_min" value="59" class="form-control" readonly=""/>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="pm_out_ampm" value="pm" class="form-control" readonly=""/>
                                    <small class="form-text">am/pm</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <div class="col-sm-12">
                              <h5>The next day</h5>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">AM - IN</label>
                              <div class="col-sm-9">
                                <div class="row">
                                  <div class="col-md-4">
                                    <input value="12" class="form-control" readonly=""/>
                                    <small class="form-text">Hour</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="am_in_min" value="00" class="form-control" readonly=""/>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="am_in_ampm" value="am" class="form-control" readonly=""/>
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
                                    <option>08</option>
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
                                    <option>00</option>
                                    <?php include('min_page.php'); ?>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <select name="am_out_ampm" class="form-control">
                                    <option>am</option>
                                    <option>pm</option>
                                    </select>
                                    <small class="form-text">Minutes</small>
                                  </div>

                                </div>
                              </div>
                            </div>
                            
                            <?php } ?>
                            
                             
                            <div class="form-group row">
                            
                              <div class="col-sm-12">
                              <p><strong>Select Dept / Office</strong></p>
                              
                              <input type="text" class="form-control" id="myInputAddSched" onkeyup="myFunctionAddSched()" placeholder="Search for class details..." title="Type a class details..." />
                  
                              <table class="table table-striped table-hover table-sm" id="myTableAddSched">
                              
                              <thead>
                              <tr>
                              <th></th>
                              <th>Dept / Office Details</th>
                              </tr>
                              </thead>
                              
                              <tbody>
                              <?php
                              
                              $dept_office_query = $conn->query("SELECT * FROM dept_offices ORDER BY dept_office_name, do_id ASC");
                              
                              while ($do_row = $dept_office_query->fetch()) 
                              { 
                              
                              ?>
                                
                              <tr>
                              <td><input name="checkbox[]" type="checkbox" value="<?php echo $do_row['do_id']; ?>"></td>
                              <td><?php echo $do_row['dept_office_name']; ?></td>
                              </tr>
                               <?php } ?>
                              </tbody>
                              
                              </table>
                               
                               </div>
                            </div>
                          </div>     
                       
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addSchedule" type="submit" class="btn btn-primary">Add</button>
                        </div>
                      </div>
                      </form>
                    </div>
                  </div>
                  <!-- end addSubjKinder Modal -->
 
            </div>
            
          </div>
        </div>
              
      </section>

      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
  </body>
</html>
