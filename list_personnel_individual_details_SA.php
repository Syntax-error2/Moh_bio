<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
    include('dbcon.php');
   
   include('header.php');
   
   ?>
 
    
    
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <?php
    // Sanitize and validate GET parameters
    $personnel_id = $_GET['personnel_id'] ?? '';
    $dept_id = $_GET['dept'] ?? '';
    
    try {
      $staff_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
      $staff_stmt->execute([':personnel_id' => $personnel_id]);
      $staff_query = $staff_stmt;
      $staff_row = $staff_query->fetch();

      $shift_stmt = $conn->prepare("SELECT * FROM shifts WHERE shift_id = :shift_id");
      $shift_stmt->execute([':shift_id' => $staff_row['shift_id']]);
      $emp_stat_query5 = $shift_stmt;
      $es_row5=$emp_stat_query5->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching personnel data: " . $e->getMessage());
    }
    
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item active">Personnels</li>
            <li class="breadcrumb-item active">Seminars Attended</li>
          </ul>
          
        </div>
      </div>
 
          <!-- SUB-MENU -->
          <div class="">
            <ul class="nav nav-pills breadcrumb p-2 pl-4">
              
              <li class="nav-item pl-2">
                <a class="nav-link disabled text-bold" aria-disabled="true">PERSONNELS</a>
              </li>
              
              <li class="nav-item dropdown">
                <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link active language dropdown-toggle">Profile</a>
                <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                  <li><a class="dropdown-item" href="list_personnel_individual_details.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Personnel Data</a></li>
                  <li><a class="dropdown-item" href="list_personnel_individual_details_EB.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Educational Background</a></li>
                  <li><a class="dropdown-item active" href="list_personnel_individual_details_SA.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Seminars Attended</a></li>
                </ul>
              </li>
              
              <li class="nav-item dropdown">
                <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">Leave Management</a>
                <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                  <li><a class="dropdown-item" href="leave_application.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Leave Applications</a></li>
                  <li><a class="dropdown-item" href="leave_card.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Leave Card</a></li>
                </ul>
              </li>
              
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="list_personnel_individual_details_SR.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Service Record</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="list_personnel_individual_details_files.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Files</a>
              </li>

              <?php if ($session_access !== 'User') { ?>
              <li class="nav-item dropdown">
                <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">Quick Options</a>
                <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#encodeDL<?php echo $staff_row['RFTag_id']; ?>" href="#">Encode Daily Log</a></li>
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#restDaySetup<?php echo $staff_row['RFTag_id']; ?>" href="#">Set Rest Day</a></li>
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" href="#">CS Form 48</a></li>
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_attendance<?php echo $staff_row['RFTag_id']; ?>" href="#">Detailed DTR</a></li>
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" href="#">Log Validation</a></li>
                </ul>
              </li>
              <?php } ?>

              
            </ul>
             
          </div>
          <!-- END SUB-MENU -->
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-8">
              <h2>Seminars Attended</h2>
              <p class="text-small text-secondary">Manage personnel seminar and training history</p>
            </div>

            <div class="col-lg-4 text-right">
              <?php if ($session_access !== 'User') { ?>
                <a class="btn btn-primary" style="color: white;" href="add_seminars_modal.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-plus"></i> SEMINARS ATTENDED</a>
              <?php } ?>
              <a class="btn btn-info" style="color: white;" title="Print personnel's Attended Seminars..." href="printPersonnelDataSheet_detailed_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a>
            </div>

            <div class="col-lg-12 mb-3">
              <?php include('personnel_top_panel.php'); ?>
            </div>

            <div class="col-lg-12 col-md-12">
            
            
            <?php include('encode_daily_log_modal.php'); ?>
            <?php include('restDay_modal.php'); ?>
            <?php include('updateMonthlyLog_modal.php'); ?>
            <?php include('print_monthly_attendance_modal_csf48.php'); ?>
            <?php include('print_monthly_attendance_modal.php'); ?>
            <?php include('print_monthly_LV_modal.php'); ?>
                      
                      
            <!-- kinder 1     -->
            <div id="new-updates" class="card updates recent-updated">
              <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                

                        <div class="col-lg-12">
                        <div class="table-responsive" style="margin-top: 12px;">
                        <table id="" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>ACTION</th>
                              <th>TITLE</th>
                              <th>DESCRIPTION</th>
                              <th>VENUE</th>
                              <th>DATE</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                $subjK_ctr=0;
                                
                                try {
                                  $ps_stmt = $conn->prepare("SELECT * FROM personnel_seminars WHERE personnel_id = :personnel_id ORDER BY ps_id ASC");
                                  $ps_stmt->execute([':personnel_id' => $personnel_id]);
                                  $ps_query = $ps_stmt;
                                  while ($ps_row = $ps_query->fetch())
                                    {
                                        ?>
                                    
     
               
                            <tr>
                            
                                <td style="width: 80px;">
                                <?php if ($session_access !== 'User') { ?>
                                  <a title="Edit data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#editPersonnel_seminars<?php echo $ps_row['ps_id']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></a>
                                  <a title="Delete data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deletePersonnel_seminars<?php echo $ps_row['ps_id']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                                <?php } ?>
                                </td>
                            
                            
                            <td><?php echo $ps_row['seminar_title']; ?></td>
                            <td><?php echo $ps_row['seminar_desc']; ?></td>
                            <td><?php echo $ps_row['seminar_venue']; ?></td>
                            <td><?php echo $ps_row['event_date']; ?></td>
                            </tr>
                            
                            <?php include('edit_personnel_seminars_modal.php'); ?>
                            
                             <?php 
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching seminars attended: " . $e->getMessage());
                                }
                             ?>
                           
                          </tbody>
                        </table>
                        </div>
                        </div>
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