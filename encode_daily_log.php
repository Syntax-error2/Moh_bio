<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
  
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  
  <?php $logDate=$_POST['selectedMM'].'/'.$_POST['selectedDD'].'/'.$_POST['selectedYYYY']; ?>   

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    
    <?php
    $staff_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
    $staff_stmt->execute([':personnel_id' => $_GET['personnel_id']]);
    $staff_query = $staff_stmt;
    $staff_row = $staff_query->fetch();
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo $_GET['dept']; ?>">List of Personnel</a></li>
            
            <li class="breadcrumb-item"><a href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {

                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } 
                                    
                                    ?></a>
            </li>
            
            <li class="breadcrumb-item active">Encode Daily Log</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
              
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  
               
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="font-weight: bold !important;">ENCODE DAILY LOG [ <?php echo substr($staff_row['fname'], 0,1).'. '.$staff_row['lname']; ?> ]</strong></a>
                  
                  
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="background-color: yellow; padding: 8px; font-weight: bold;">DATE: <?php echo $logDate; ?></strong> <i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show" style="padding: 14px;">

                <!-- edit Time Sched Modal -->
                
                <?php
                
                $am_in_log_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE logDate = :logDate AND logFlow = 'AM IN' AND RFTag_id = :RFTag_id");
                $am_in_log_stmt->execute([':logDate' => $logDate, ':RFTag_id' => $staff_row['RFTag_id']]);
                $am_in_log_query = $am_in_log_stmt;
                $am_in_log_row=$am_in_log_query->fetch();
                if($am_in_log_query->rowCount()>0){ $am_in_lq="Update"; }else{ $am_in_lq="Insert"; }
 
                $am_out_log_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE logDate = :logDate AND logFlow = 'AM OUT' AND RFTag_id = :RFTag_id");
                $am_out_log_stmt->execute([':logDate' => $logDate, ':RFTag_id' => $staff_row['RFTag_id']]);
                $am_out_log_query = $am_out_log_stmt;
                $am_out_log_row=$am_out_log_query->fetch();
                if($am_out_log_query->rowCount()>0){ $am_out_lq="Update"; }else{ $am_out_lq="Insert"; }
                
                $pm_in_log_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE logDate = :logDate AND logFlow = 'PM IN' AND RFTag_id = :RFTag_id");
                $pm_in_log_stmt->execute([':logDate' => $logDate, ':RFTag_id' => $staff_row['RFTag_id']]);
                $pm_in_log_query = $pm_in_log_stmt;
                $pm_in_log_row=$pm_in_log_query->fetch();
                if($pm_in_log_query->rowCount()>0){ $pm_in_lq="Update"; }else{ $pm_in_lq="Insert"; }
                
                $pm_out_log_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE logDate = :logDate AND logFlow = 'PM OUT' AND RFTag_id = :RFTag_id");
                $pm_out_log_stmt->execute([':logDate' => $logDate, ':RFTag_id' => $staff_row['RFTag_id']]);
                $pm_out_log_query = $pm_out_log_stmt;
                $pm_out_log_row=$pm_out_log_query->fetch();
                if($pm_out_log_query->rowCount()>0){ $pm_out_lq="Update"; }else{ $pm_out_lq="Insert"; }
                
                ?>
                
                <form action="save_updateMonthlyLog.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&am_in_lq=<?php echo $am_in_lq; ?>&am_out_lq=<?php echo $am_out_lq; ?>&pm_in_lq=<?php echo $pm_in_lq; ?>&pm_out_lq=<?php echo $pm_out_lq; ?>" method="POST">

                <input type="hidden" name="logDate" value="<?php echo $logDate; ?>" />
                <input type="hidden" name="RFTag_id" value="<?php echo $staff_row['RFTag_id']; ?>" />
                <input type="hidden" name="shift_id" value="<?php echo $staff_row['shift_id']; ?>" />
 
                
                <div class="form-group row">
                  <label class="col-sm-2 form-control-label">SET AM - IN <br /><small>AM Arrival Time</small></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-4">
                        <select name="am_in_hr" class="form-control">
                        
                        
                        <option>
                        <?php if($am_in_log_query->rowCount()>0){ echo substr($am_in_log_row['logTime'], 0, 2); }else{ echo "-"; } ?>
                        </option>
                        
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
                        
                        <option>
                        <?php if($am_in_log_query->rowCount()>0){ echo substr($am_in_log_row['logTime'], 3, 2); }else{ echo "-"; } ?>
                        </option>
                        
                        <?php include('min_page.php'); ?>
                        </select>
                        <small class="form-text">Minutes</small>
                      </div>
                      
                      
                      
                      <div class="col-md-4">
                        <select name="am_in_ampm" class="form-control">
                        
                        <option>
                        <?php if($am_in_log_query->rowCount()>0){ echo substr($am_in_log_row['logTime'], -2, 2); }else{ echo "-"; } ?>
                        </option>
                        
                     
                        <option>AM</option>
                        <option>PM</option>
                        </select>
                        <small class="form-text">am/pm [ Late: <input title="Check if log is late..." type="checkbox" <?php if($am_in_log_row['late_status']=="on"){ ?> checked="true" <?php } ?> name="am_in_late" value="am_late" /> ]</small>
                      </div>
    
                    </div>
                  </div>
                </div>
    
    
                <div class="form-group row">
                  <label class="col-sm-2 form-control-label">SET AM - OUT <br /><small>AM Departure Time</small></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-4">
                        <select name="am_out_hr" class="form-control">
                        
                        <option>
                        <?php if($am_out_log_query->rowCount()>0){ echo substr($am_out_log_row['logTime'], 0, 2); }else{ echo "-"; } ?>
                        </option>
                        
                        <option></option>
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
                        <option>
                        <?php if($am_out_log_query->rowCount()>0){ echo substr($am_out_log_row['logTime'], 3, 2); }else{ echo "-"; } ?>
                        </option>
                           
                        <?php include('min_page.php'); ?>
                        </select>
                        <small class="form-text">Minutes</small>
                      </div>
                      
                      
                      
                      <div class="col-md-4">
                        <select name="am_out_ampm" class="form-control">
                        <option>
                        <?php if($am_out_log_query->rowCount()>0){ echo substr($am_out_log_row['logTime'], -2, 2); }else{ echo "-"; } ?>
                        </option>
                      
                        <option>AM</option>
                        <option>PM</option>
                        </select>
                        <small class="form-text">am/pm [ Undertime: <input title="Check if log is undertime..." type="checkbox" <?php if($am_out_log_row['late_status']=="on"){ ?> checked="true" <?php } ?> name="am_out_undertime" value="am_undertime" /> ]</small>
                      </div>
    
                    </div>
                  </div>
                </div>
    
    
                <div class="form-group row">
                  <label class="col-sm-2 form-control-label">SET PM - IN <br /><small>PM Arrival Time</small></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-4">
                        <select name="pm_in_hr" class="form-control">
                        
                        <option>
                        <?php if($pm_in_log_query->rowCount()>0){ echo substr($pm_in_log_row['logTime'], 0, 2); }else{ echo "-"; } ?>
                        </option>
                        
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
                        
                        <option>
                        <?php if($pm_in_log_query->rowCount()>0){ echo substr($pm_in_log_row['logTime'], 3, 2); }else{ echo "-"; } ?>
                        </option>
                        
                        
                        <?php include('min_page.php'); ?>
                        </select>
                        <small class="form-text">Minutes</small>
                      </div>
                      
                     
                      
                      <div class="col-md-4">
                        <select name="pm_in_ampm" class="form-control">
                        
                        <option>
                        <?php if($pm_in_log_query->rowCount()>0){ echo substr($pm_in_log_row['logTime'], -2, 2); }else{ echo "-"; } ?>
                        </option>
                        
                        <option>AM</option>
                        <option>PM</option>
                        </select>
                        <small class="form-text">am/pm [ Late: <input title="Check if log is late..." type="checkbox" <?php if($pm_in_log_row['late_status']=="on"){ ?> checked="true" <?php } ?> name="pm_in_late" value="pm_late" /> ]</small>
                      </div>
    
                    </div>
                  </div>
                </div>
    
    
                <div class="form-group row">
                  <label class="col-sm-2 form-control-label">SET PM - OUT <br /><small>PM Departure Time</small></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-4">
                        <select name="pm_out_hr" class="form-control">
                        
                        <option>
                        <?php if($pm_out_log_query->rowCount()>0){ echo substr($pm_out_log_row['logTime'], 0, 2); }else{ echo "-"; } ?>
                        </option>
                        
                        
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
                        
                        <option>
                        <?php if($pm_out_log_query->rowCount()>0){ echo substr($pm_out_log_row['logTime'], 3, 2); }else{ echo "-"; } ?>
                        </option>
                        
                       
                        <?php include('min_page.php'); ?>
                        </select>
                        <small class="form-text">Minutes</small>
                      </div>
                      
                       
                      
                      <div class="col-md-4">
                        <select name="pm_out_ampm" class="form-control">
                        
                        <option>
                        <?php if($pm_out_log_query->rowCount()>0){ echo substr($pm_out_log_row['logTime'], -2, 2); }else{ echo "-"; } ?>
                        </option>
                        
                        <option>AM</option>
                        <option>PM</option>
                        </select>
                        <small class="form-text">am/pm [ Undertime: <input title="Check if log is undertime..." type="checkbox" name="pm_out_undertime" <?php if($pm_out_log_row['late_status']=="on"){ ?> checked="true" <?php } ?> value="pm_undertime" /> ]</small>
                      </div>
    
                    </div>
                  </div>
                </div>
 
               
                                
                <div class="modal-footer">
                  <a style="color: white;" href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" class="btn btn-secondary">Cancel</a>
                  <button name="updateDailyLog" type="submit" class="btn btn-primary">Save</button>
                </div>
                                
               </form>
                               
                          <!-- end Edit Time Sched Modal -->  

                </div>
              </div>
              <!-- kinder End-->
              
              
              
            </div>
            
          </div>
        </div>
              
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

     
    
  </body>
</html>