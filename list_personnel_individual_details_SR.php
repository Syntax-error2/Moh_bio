<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
    include('dbcon.php');
   
   include('header.php');

  $session_access = $session_access ?? ($_SESSION['useraccess'] ?? '');
   
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
            <li class="breadcrumb-item active">Service Record</li>
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
                <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">Profile</a>
                <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                  <li><a class="dropdown-item" href="list_personnel_individual_details.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Personnel Data</a></li>
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
                <a class="nav-link active" aria-current="page" href="list_personnel_individual_details_SR.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Service Record</a>
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
              <h2>Service Record</h2>
              <p class="text-small text-secondary">Manage personnel appointment and service history</p>
            </div>

            <div class="col-lg-4 text-right">
              <?php if ($session_access !== 'User') { ?>
                <a class="btn btn-primary" style="color: white;" data-toggle="modal" data-target="#addService_record" href="#"><i class="fa fa-plus"></i> SERVICE RECORD</a>
              <?php } ?>
              <div class="btn-group d-inline-block">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-print"></i> Print Service Record
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item text-info" href="javascript:void(0)" onclick="openSignatoriesSettings()"><i class="fa fa-cog"></i> Signatories Settings</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" title="Print personnel's Service Records with note..." href="printPersonnelDataSheet_detailed_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&note=yes" target="_blank">With Note</a>
                  <a class="dropdown-item" title="Print personnel's Service Records without note..." href="printPersonnelDataSheet_detailed_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&note=no" target="_blank">Without Note</a>
                </div>
              </div>
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
            <?php include('signatories_settings_modal.php'); ?>
             
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                    
                        <div class="col-lg-12">
                        <div class="table-responsive" style="margin-top: 12px;">
                        <table id="" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>ACTION</th>
                              <th>SERVICES<br /><small>From - To</small></th>
                              <th>RECORD OF APPOINTMENT<br /><small>Designation - Status</small></th>
                              <th>MONTHLY SALARY</th>
                              <th>ANNUAL SALARY</th>
                              <th>OFFICE OF APPOINTMENT</th>
                              <th>SEPARATION<br /><small>Date - Cause</small></th>
                            </tr>
                          </thead>
                          <tbody>
                      
                            <?php
                            $subjK_ctr=0;
                            
                            try {
                              $sr_stmt = $conn->prepare("SELECT * FROM service_record WHERE personnel_id = :personnel_id ORDER BY sr_id DESC");
                              $sr_stmt->execute([':personnel_id' => $personnel_id]);
                              $sr_query = $sr_stmt;
                              while ($sr_row = $sr_query->fetch())
                                {
                                    ?>
                                
 
           
                        <tr>
                        
                        <td style="width: 80px;">
                        <?php if ($session_access !== 'User') { ?>
                          <a title="Edit data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#editService_record<?php echo $sr_row['sr_id']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></a>
                          <a title="Delete data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deleteService_record<?php echo $sr_row['sr_id']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                        <?php } ?>
                        </td>
                        
                        <td style="font-size: small; width: 150px">
                        <?php echo substr($sr_row['serv_date_from'], 5, 2).'/'.substr($sr_row['serv_date_from'], 8, 2).'/'.substr($sr_row['serv_date_from'], 0, 4); ?> - 
                        <?php
                        if($sr_row['serv_date_to'] == null){
                            echo "Present";
                        }else{
                            echo substr($sr_row['serv_date_to'], 5, 2).'/'.substr($sr_row['serv_date_to'], 8, 2).'/'.substr($sr_row['serv_date_to'], 0, 4);
                        }
                        ?>
                        <?php if($sr_row['appointDate_status'] == 'Active'){ ?><div class="badge badge-primary">Active Appointment Date: <?php echo substr($sr_row['serv_date_from'], 5, 2).'/'.substr($sr_row['serv_date_from'], 8, 2).'/'.substr($sr_row['serv_date_from'], 0, 4); ?></div><?php } ?>
                        </td>
                        
                        <td><?php echo $sr_row['roa_designation'].' - '.$sr_row['roa_status']; ?></td>
                        <td style="font-size: small; width: 150px">
                          <?php 
                            // Display monthly_salary, fallback to old 'salary' field if it exists
                            $monthly = isset($sr_row['monthly_salary']) ? number_format($sr_row['monthly_salary'], 2) : (isset($sr_row['salary']) ? number_format($sr_row['salary'], 2) : '0.00');
                            echo '₱ ' . $monthly;
                          ?>
                        </td>
                        <td style="font-size: small; width: 150px">
                          <?php 
                            // Display annual_salary
                            $annual = isset($sr_row['annual_salary']) ? number_format($sr_row['annual_salary'], 2) : '0.00';
                            echo '₱ ' . $annual;
                          ?>
                        </td>
                        <td><?php echo $sr_row['office_appointment']; ?></td>
                        
                        <td>
                        <?php
                        if($sr_row['separate_date'] != null){
                            echo substr($sr_row['separate_date'], 5, 2).'/'.substr($sr_row['separate_date'], 8, 2).'/'.substr($sr_row['separate_date'], 0, 4).' - '.$sr_row['separate_cause'];
                        }
                        ?>
                        </td>
                        
                        </tr>
                        
                        <?php include('edit_service_record_modal.php'); ?>
                        
                         <?php 
                                }
                            } catch (PDOException $e) {
                                error_log("Error fetching service record: " . $e->getMessage());
                            }
                         ?>
                       
                      </tbody>
                    </table>
                    </div>
                    </div>
                    
                    <!-- Include Add Service Record Modal -->
                    <?php include('add_service_record_modal.php'); ?>
                    
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
  
    <script>
    // Auto-compute annual salary from monthly salary
    document.addEventListener('DOMContentLoaded', function() {
      
      // For ADD modal
      var monthlySalaryAdd = document.getElementById('monthly_salary_add');
      if (monthlySalaryAdd) {
        monthlySalaryAdd.addEventListener('input', function() {
          var monthlySalary = parseFloat(this.value) || 0;
          var annualSalary = monthlySalary * 12;
          var annualSalaryInput = document.getElementById('annual_salary_add');
          if (annualSalaryInput) {
            annualSalaryInput.value = annualSalary.toFixed(3);
          }
        });
      }
      
      // For EDIT modals
      var monthlySalaryInputs = document.querySelectorAll('.monthly-salary-input');
      
      monthlySalaryInputs.forEach(function(input) {
        input.addEventListener('input', function() {
          var srId = this.id.replace('monthly_salary_', '');
          var monthlySalary = parseFloat(this.value) || 0;
          var annualSalary = monthlySalary * 12;
          var annualSalaryInput = document.getElementById('annual_salary_' + srId);
          if (annualSalaryInput) {
            annualSalaryInput.value = annualSalary.toFixed(3);
          }
        });
        
        // Trigger calculation on page load if there's already a value
        if (input.value) {
          var srId = input.id.replace('monthly_salary_', '');
          var monthlySalary = parseFloat(input.value) || 0;
          var annualSalary = monthlySalary * 12;
          var annualSalaryInput = document.getElementById('annual_salary_' + srId);
          if (annualSalaryInput) {
            annualSalaryInput.value = annualSalary.toFixed(3);
          }
        }
      });
    });

    function openSignatoriesSettings() {
      $('#signatories_settings_modal').modal('show');
    }

    window.openSignatoriesSettings = openSignatoriesSettings;
    </script>
  
  </body>
</html>