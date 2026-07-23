<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
  include('dbcon.php');
   
   include('header.php');

  $session_access = $session_access ?? ($_SESSION['useraccess'] ?? '');
  $user_personnel_id = $user_personnel_id ?? ($_SESSION['user_personnel_id'] ?? '');
  $personnel_id = $_GET['personnel_id'] ?? '';
  if ($session_access === 'User' && (string)$user_personnel_id !== (string)$personnel_id) {
    echo '<div class="container-fluid mt-4"><div class="alert alert-danger">Access denied. You can only view your own profile.</div></div>';
    exit;
  }
   
   ?>

  <?php
  
 
  $get_dept=$_GET['dept'];
  
  if(isset($_POST['filterPosition'])){
  $filterPosition=$_POST['filter'];
  }else{
  $filterPosition='All';
  } ?>
    
    
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
    } catch (PDOException $e) {
        error_log("Error fetching personnel: " . $e->getMessage());
        die("Error loading personnel data.");
    }
    
    $emp_stat_stmt5 = $conn->prepare("SELECT * FROM shifts WHERE shift_id = :shift_id");
    $emp_stat_stmt5->execute([':shift_id' => $staff_row['shift_id']]);
    $emp_stat_query5 = $emp_stat_stmt5;
    $es_row5=$emp_stat_query5->fetch();
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item active">Personnels</li>
            <li class="breadcrumb-item active">Personnel Data</li>
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
                  <li><a class="dropdown-item active" href="list_personnel_individual_details.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Personnel Data</a></li>
                  <li><a class="dropdown-item" href="list_personnel_individual_details_EB.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Educational Background</a></li>
                  <li><a class="dropdown-item" href="list_personnel_individual_details_SA.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Seminars Attended</a></li>
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
              <h2>Personal Information</h2>
              <p class="text-small text-secondary">View and manage personnel profile details</p>
            </div>

            <div class="col-lg-4 text-right">
              <?php if ($session_access !== 'User') { ?>
                <a class="btn btn-primary" style="color: white;" href="edit_completePersonnelData.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-pencil"></i> PERSONAL INFORMATION</a>
              <?php } ?>
              <a class="btn btn-info" style="color: white;" title="Print personal information..." href="printPersonnelDataSheet_detailed_PI.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a>
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
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display mb-0">
                    <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">
                      Personal Information
                    </a>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                    <?php
                    $dept_stmt = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id LIMIT 1");
                    $dept_stmt->execute([':do_id' => $staff_row['do_id']]);
                    $dept_row = $dept_stmt->fetch(PDO::FETCH_ASSOC);

                    $des_stmt = $conn->prepare("SELECT des_name FROM designation WHERE des_id = :des_id LIMIT 1");
                    $des_stmt->execute([':des_id' => $staff_row['des_id']]);
                    $des_row = $des_stmt->fetch(PDO::FETCH_ASSOC);

                    $status_stmt = $conn->prepare("SELECT emp_stat_name, position_class, status FROM emp_status WHERE empStat_id = :empStat_id LIMIT 1");
                    $status_stmt->execute([':empStat_id' => $staff_row['empStat_id']]);
                    $status_row = $status_stmt->fetch(PDO::FETCH_ASSOC);

                    $contact_person_name = trim((string)$staff_row['conPerson_fname'] . ' ' . (string)$staff_row['conPerson_mname'] . ' ' . (string)$staff_row['conPerson_lname']);
                    $appointment_text = '-';
                    if (!empty($staff_row['appointment_date'])) {
                        if ($status_row && (string)$status_row['status'] === 'Active') {
                            $appointment_text = $staff_row['appointment_date'] . ' - Present';
                        } else {
                            $appointment_text = $staff_row['appointment_date'] . ' - ' . ($staff_row['separation_date'] !== '' ? $staff_row['separation_date'] : '-');
                        }
                    }

                    $years_text = '';
                    if (!empty($staff_row['appointment_date']) && $staff_row['appointment_date'] !== '  /  /    ') {
                        if (empty($staff_row['separation_date']) || $staff_row['separation_date'] === '  /  /    ') {
                            $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date('m/d/Y')));
                        } else {
                            $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date($staff_row['separation_date'])));
                        }
                        $years_text = ' (' . $diff->format('%y') . ' yrs.)';
                    }
                    ?>

                    <div class="container-fluid pt-2 pb-2">
                      <div class="row">
                        <div class="col-12 mb-3">
                          <div class="card h-100 border-light">
                            <div class="card-body">
                              <h5 class="card-title mb-3">Basic Information</h5>
                              <div class="row">
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['fname']); ?></strong><br /><small class="text-muted">First Name</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['mname']); ?></strong><br /><small class="text-muted">Middle Name</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['lname']); ?></strong><br /><small class="text-muted">Last Name</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['suffix']); ?></strong><br /><small class="text-muted">Suffix</small></div>
                                <div class="col-sm-4 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['age']); ?></strong><br /><small class="text-muted">Age</small></div>
                                <div class="col-sm-4 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['sex']); ?></strong><br /><small class="text-muted">Sex</small></div>
                                <div class="col-sm-4 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['marital_status']); ?></strong><br /><small class="text-muted">Marital Status</small></div>
                                <div class="col-sm-5 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['bdMM'] . '/' . (string)$staff_row['bdDD'] . '/' . (string)$staff_row['bdYYYY']); ?></strong><br /><small class="text-muted">Date of Birth</small></div>
                                <div class="col-sm-7 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['birth_place']); ?></strong><br /><small class="text-muted">Place of Birth</small></div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 mb-3">
                          <div class="card h-100 border-light">
                            <div class="card-body">
                              <h5 class="card-title mb-3">Contact Information</h5>
                              <div class="row">
                                <div class="col-12 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['address']); ?></strong><br /><small class="text-muted">Home Address</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['email']); ?></strong><br /><small class="text-muted">Email Address</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['personal_pnum']); ?></strong><br /><small class="text-muted">Contact Number</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars($contact_person_name !== '' ? $contact_person_name : '-'); ?></strong><br /><small class="text-muted">Contact Person</small></div>
                                <div class="col-sm-3 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['conPerson_relationship']); ?></strong><br /><small class="text-muted">Relationship</small></div>
                                <div class="col-sm-3 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['emergency_pnum']); ?></strong><br /><small class="text-muted">Contact #</small></div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 mb-3">
                          <div class="card h-100 border-light">
                            <div class="card-body">
                              <h5 class="card-title mb-3">Employment Details</h5>
                              <div class="row">
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)($dept_row['dept_office_name'] ?? '-')); ?></strong><br /><small class="text-muted">Office / Department</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)($des_row['des_name'] ?? '-')); ?></strong><br /><small class="text-muted">Designation</small></div>
                                <div class="col-sm-6 mb-2"><strong>Salary Grade <?php echo htmlspecialchars((string)$staff_row['sal_grade']); ?> / Step <?php echo htmlspecialchars((string)$staff_row['sal_step']); ?></strong><br /><small class="text-muted">Salary Grade / Step</small></div>
                                <div class="col-sm-6 mb-2"><strong>Level <?php echo htmlspecialchars((string)$staff_row['sal_level']); ?> | <?php echo htmlspecialchars((string)$staff_row['rate_per_day']); ?></strong><br /><small class="text-muted">Level | Rate/Day</small></div>
                                <div class="col-sm-4 mb-2"><strong><?php echo htmlspecialchars((string)($status_row['emp_stat_name'] ?? '-')); ?></strong><br /><small class="text-muted">Status</small></div>
                                <div class="col-sm-4 mb-2"><strong><?php echo htmlspecialchars((string)($status_row['position_class'] ?? '-')); ?></strong><br /><small class="text-muted">Class</small></div>
                                <div class="col-sm-4 mb-2"><strong><?php echo htmlspecialchars((string)($status_row['status'] ?? '-')); ?></strong><br /><small class="text-muted">Type</small></div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 mb-3">
                          <div class="card h-100 border-light">
                            <div class="card-body">
                              <h5 class="card-title mb-3">Appointment and Government IDs</h5>
                              <div class="row">
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['eligibility']); ?></strong><br /><small class="text-muted">Eligibility</small></div>
                                <div class="col-sm-6 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['plantilla_num']); ?></strong><br /><small class="text-muted">Plantilla Number</small></div>
                                <div class="col-12 mb-2"><strong><?php echo htmlspecialchars($appointment_text . $years_text); ?></strong><br /><small class="text-muted">Appointment Period (No. of years)</small></div>
                                <div class="col-sm-6 col-lg-3 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['tin_num']); ?></strong><br /><small class="text-muted">TIN</small></div>
                                <div class="col-sm-6 col-lg-3 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['gsis_num']); ?></strong><br /><small class="text-muted">SSS/GSIS</small></div>
                                <div class="col-sm-6 col-lg-3 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['pagibig_num']); ?></strong><br /><small class="text-muted">Pag-IBIG MID</small></div>
                                <div class="col-sm-6 col-lg-3 mb-2"><strong><?php echo htmlspecialchars((string)$staff_row['philHealth_num']); ?></strong><br /><small class="text-muted">PhilHealth</small></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>


                        <div class="col-lg-12">

                        <!-- ADD FAMILY MEMBER MODAL -->
                          <div id="add_fam_bg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_personnel_tables.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">ADD FAMILY MEMBER</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                                    <div class="form-group row">
                                    
                                        <div class="col-sm-12">
                                            <div class="row">
                                            
                                                <div class="col-md-12">
                                                <input name="fullname" type="text" class="form-control" placeholder="Enter Lastname, First Name, Middle Name" required="" />
                                                <small class="form-text">Family Member's Fullname</small>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                <select name="sex" class="form-control">
                                                <option>-</option>
                                                <option>Male</option>
                                                <option>Female</option>
                                                </select>
                                                <small class="form-text">Sex</small>
                                                </div>
                                                
                                                <div class="col-md-8">
                                                <select name="relationship" class="form-control">
                                                <option>-</option>
                                                <option>Spouse</option>
                                                <option>Child</option>
                                                <option>Parents</option>
                                                <option>Siblings</option>
                                                </select>
                                                <small class="form-text">Relationship</small>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                <input name="contact_num" type="text" class="form-control" placeholder="Enter contact number..." />
                                                <small class="form-text">Contact Number (Optional)</small>
                                                </div>
                                                  
                                            </div>
                                        </div>
                                    </div>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="save_add_fam_bg" type="submit" class="btn btn-primary">Save</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- END ADD FAMILY MEMBER MODAL -->
                          
                        <div class="table-responsive" style="margin-top: 12px;">
                        <?php if ($session_access !== 'User') { ?>
                        <a title="Click to add family members..." style="color: white !important; margin-top: 12px; margin-bottom: 12px;" data-toggle="modal" data-target="#add_fam_bg" href="#" class="btn btn-primary"><i class="fa fa-plus"></i> Add Family Member</a>
                        <?php } ?>
                        
                        <table id="" class="display" style="width:100%">
                          <thead>
                            <tr>
                            <?php if ($session_access !== 'User') { ?><th>ACTION</th><?php } ?>
                            <th style="font-weight: bold; width: 40%;">FULLNAME</th>
                            <th style="font-weight: bold;">SEX</th>
                            <th style="font-weight: bold; width: 20%;">RELATIONSHIP</th>
                            <th style="font-weight: bold; width: 20%;">CONTACT #</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                $subjK_ctr=0;
                                
                                try {
                                    $fam_stmt = $conn->prepare("SELECT * FROM personnel_fam_bg WHERE personnel_id = :personnel_id ORDER BY relationship ASC");
                                    $fam_stmt->execute([':personnel_id' => $personnel_id]);
                                    $ps_query = $fam_stmt;
                                    while ($ps_row = $ps_query->fetch())
                                    {
                                        ?>
                                    
     
               
                            <tr>
                            
                                <?php if ($session_access !== 'User') { ?>
                                <td style="width: 80px;">
                                  <a title="Edit data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#editPersonnel_seminars<?php echo $ps_row['fm_id']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></a>
                                  <a title="Delete data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deletePersonnel_seminars<?php echo $ps_row['fm_id']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                                </td>
                                <?php } ?>
                            
                            
                            <td><?php echo $ps_row['fullname']; ?></td>
                            <td><?php echo $ps_row['sex']; ?></td>
                            <td><?php echo $ps_row['relationship']; ?> <?php if($ps_row['relationship']==='Parents'){ if($ps_row['sex']==='Male'){ echo "(Father)"; }else{ echo "(Mother)"; } } ?></td>
                            <td><?php echo $ps_row['contact_num']; ?></td>
                            </tr>
                            
                            <?php include('edit_personnel_fam_bg_modal.php'); ?>
                            
                             <?php 
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching family background: " . $e->getMessage());
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