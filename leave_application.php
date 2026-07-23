<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
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
    
    $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'");
    $staff_row = $staff_query->fetch();

    $emp_stat_query5 = $conn->query("SELECT * FROM shifts WHERE shift_id='$staff_row[shift_id]'");
    $es_row5=$emp_stat_query5->fetch();
    
    // Get department/office name for leave application
    $dept_name = 'Department of Health - Region X'; // Default value
    $is_administrator = ($session_access === 'Administrator');
    if (!empty($staff_row['do_id'])) {
        $dept_query = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id LIMIT 1");
        $dept_query->execute([':do_id' => $staff_row['do_id']]);
        $dept_row = $dept_query->fetch();
        if ($dept_row && !empty($dept_row['dept_office_name'])) {
            $dept_name = $dept_row['dept_office_name'];
        }
    }
    
    ?>


    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item active">Personnels</li>
            <li class="breadcrumb-item active">Leave Application (CS Form No. 6)</li>
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
                <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle active">Leave Management</a>
                <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                  <li><a class="dropdown-item active" href="leave_application.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Leave Applications</a></li>
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

                  <li><a class="dropdown-item" data-toggle="modal" data-target="#encodeDL<?php echo htmlspecialchars($staff_row['RFTag_id']); ?>" href="#">Encode Daily Log</a></li>
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#restDaySetup<?php echo htmlspecialchars($staff_row['RFTag_id']); ?>" href="#">Set Rest Day</a></li>

                  <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo htmlspecialchars($staff_row['RFTag_id']); ?>" href="#">CS Form 48</a></li>
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_attendance<?php echo htmlspecialchars($staff_row['RFTag_id']); ?>" href="#">Detailed DTR</a></li>
                  <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_LV<?php echo htmlspecialchars($staff_row['RFTag_id']); ?>" href="#">Log Validation</a></li>

                </ul>
              </li>
              <?php } ?>

            </ul>
             
          </div>
          <!-- END SUB-MENU -->
      
      
      
      
      <!-- Leave Application Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">

            <div class="col-lg-8">
              <h2>Leave Application (CS Form No. 6)</h2>
              <p class="text-small text-secondary">Create and manage leave applications</p>
            </div>
            
            <div class="col-lg-4 text-right">
              <button type="button" 
                      data-toggle="modal" 
                      data-target="#add_leave_application"
                      onclick="setPersonnelForLeaveApp(<?php echo htmlspecialchars($staff_row['personnel_id'] ?? ''); ?>, '<?php 
                          $full_name = '';
                          if (isset($staff_row['lname']) && isset($staff_row['fname'])) {
                              $full_name = $staff_row['lname'] . ', ' . $staff_row['fname'];
                              if (isset($staff_row['mname']) && !empty($staff_row['mname'])) {
                                  $full_name .= ' ' . substr($staff_row['mname'], 0, 1) . '.';
                              }
                          }
                          echo htmlspecialchars($full_name, ENT_QUOTES); 
                      ?>', '<?php echo htmlspecialchars($dept_name ?? 'Department of Health - Region X', ENT_QUOTES); ?>')"
                      class="btn btn-primary">
                <i class="fa fa-plus"></i> New Leave Application
              </button>
            </div>

            <div class="col-lg-12 mb-3">
              <?php include('personnel_top_panel.php'); ?>
            </div>

            <div class="col-lg-12 col-md-12">
                      
            
              <!-- Leave Applications Table -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxLeave" aria-expanded="true" aria-controls="updates-boxLeave">
                  <h4>Leave Applications</h4>
                  </a>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxLeave" aria-expanded="true" aria-controls="updates-boxLeave"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxLeave" role="tabpanel" class="collapse show">
                  
                        <div class="col-lg-12">
                        <div class="table-responsive" style="margin-top: 12px;">
                        <table id="leaveApplicationTable" class="display table table-bordered table-striped table-sm" style="width:100%">
                          <thead>
                            <tr>
                              <th style="vertical-align: middle; text-align: center;">ACTION</th>
                              <th style="vertical-align: middle; text-align: center;">APPLICATION DATE</th>
                              <th style="vertical-align: middle; text-align: center;">LEAVE TYPE</th>
                              <th style="vertical-align: middle; text-align: center;">INCLUSIVE DATES</th>
                              <th style="vertical-align: middle; text-align: center;">NO. OF DAYS</th>
                              <th style="vertical-align: middle; text-align: center;">STATUS</th>
                              <th style="vertical-align: middle; text-align: center;">RECOMMENDATION</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            try {
                              $la_query = $conn->prepare("SELECT * FROM leave_applications WHERE personnel_id = :personnel_id ORDER BY application_date DESC, created_at DESC");
                              $la_query->execute([':personnel_id' => $_GET['personnel_id']]);
                              
                              while ($la_row = $la_query->fetch()) { 
                                $status_class = '';
                                switch($la_row['status']) {
                                  case 'pending': $status_class = 'badge-warning'; break;
                                  case 'approved': $status_class = 'badge-success'; break;
                                  case 'disapproved': $status_class = 'badge-danger'; break;
                                  default: $status_class = 'badge-secondary';
                                }
                            ?>
                                    
                            <tr>
                            
                            <td style="width: 10px; text-align: center;">
                            
                            <button data-toggle="dropdown" type="button" class="btn btn-outline-primary btn-sm">&nbsp;<i class="fa fa-ellipsis-v"></i>&nbsp;</button>
                                <div class="dropdown-menu">
                                <?php $la_status_lower = strtolower($la_row['status']); ?>
                                <?php if ($is_administrator || $la_status_lower !== 'approved'): ?>
                                <a href="javascript:void(0)" onclick="openSignatoriesSettings()" class="dropdown-item text-info"><i class="fa fa-cog"></i> Signatories Settings</a>
                                <div class="dropdown-divider"></div>
                                <?php endif; ?>
                                <a href="javascript:void(0)" onclick="openPrintLeaveModal(<?php echo $la_row['id']; ?>)" class="dropdown-item"><i class="fa fa-eye"></i> CS Form No. 6 Preview</a>
                                <a href="javascript:void(0)" onclick="openPrintLeaveModal(<?php echo $la_row['id']; ?>)" class="dropdown-item"><i class="fa fa-print"></i> Print CS Form No. 6</a>
                                <?php if ($la_row['leave_type'] === 'Monetized Leave'): ?>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0)" onclick="openMonetizedCertModal(<?php echo $la_row['id']; ?>)" class="dropdown-item text-info"><i class="fa fa-file-text"></i> Print Certification</a>
                                <a href="javascript:void(0)" onclick="openMonetizedVoucherModal(<?php echo $la_row['id']; ?>)" class="dropdown-item text-primary"><i class="fa fa-file-text-o"></i> Print Disbursement Voucher</a>
                                <?php endif; ?>
                                <?php if ($is_administrator || in_array($la_status_lower, ['pending', 'for review'])): ?>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0)" onclick="editLeaveApplication(<?php echo $la_row['id']; ?>)" class="dropdown-item"><i class="fa fa-pencil"></i> Edit</a>
                                <a href="javascript:void(0)" onclick="deleteLeaveApplication(<?php echo $la_row['id']; ?>)" class="dropdown-item"><i class="fa fa-times"></i> Delete</a>
                                <?php endif; ?>
                                </div>
                            
                            </td>
                            
                            <td style="vertical-align: middle; text-align: center;"><?php echo date('M d, Y', strtotime($la_row['application_date'])); ?></td>
                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($la_row['leave_type']); ?></td>
                            <td style="vertical-align: middle; text-align: center;">
                              <?php 
                              // Display multiple date ranges if available
                              $date_ranges = [];
                              if (!empty($la_row['inclusive_dates_json'])) {
                                  $date_ranges = json_decode($la_row['inclusive_dates_json'], true);
                              }
                              
                              if (!empty($date_ranges) && is_array($date_ranges)) {
                                  $range_texts = [];
                                  foreach ($date_ranges as $range) {
                                      if (isset($range['from']) && isset($range['to'])) {
                                          if ($range['from'] === $range['to']) {
                                              $range_texts[] = date('M d, Y', strtotime($range['from']));
                                          } else {
                                              $range_texts[] = date('M d, Y', strtotime($range['from'])) . ' - ' . date('M d, Y', strtotime($range['to']));
                                          }
                                      }
                                  }
                                  echo implode('<br>', $range_texts);
                              } else {
                                  // Fallback to legacy single range
                                  echo date('M d, Y', strtotime($la_row['inclusive_date_from'])) . ' - ' . date('M d, Y', strtotime($la_row['inclusive_date_to']));
                              }
                              ?>
                            </td>
                            <td style="vertical-align: middle; text-align: center;"><?php echo $la_row['number_of_days']; ?></td>
                            <td style="vertical-align: middle; text-align: center;">
                              <div class="badge <?php echo $status_class; ?>"><?php echo strtoupper($la_row['status']); ?></div>
                            </td>
                            <td style="vertical-align: middle;">
                              <?php echo htmlspecialchars($la_row['recommendation'] ?? 'N/A'); ?>
                            </td>
                            </tr>
                            
                            <?php 
                              }
                            } catch (PDOException $e) {
                              error_log("Error fetching leave applications: " . $e->getMessage());
                              echo "<tr><td colspan='7' class='text-center text-danger'>Error loading leave applications.</td></tr>";
                            }
                            ?>
                           
                          </tbody>
                        </table>
                        </div>
                        </div>
                </div>
              </div>
              <!-- End Leave Applications Table-->
              
              
<?php include('add_leave_application_modal_list.php'); ?>
<?php include('edit_leave_application_modal.php'); ?>
<?php include('delete_leave_application_modal.php'); ?>
<?php include('print_leave_application_csform6.php'); ?>
<?php include('signatories_settings_modal.php'); ?>

<?php
// Include monetized leave modals outside the table for all monetized leave applications
try {
  $monetized_query = $conn->prepare("SELECT * FROM leave_applications WHERE personnel_id = :personnel_id AND leave_type = 'Monetized Leave' ORDER BY application_date DESC");
  $monetized_query->execute([':personnel_id' => $_GET['personnel_id']]);
  
  while ($la_row = $monetized_query->fetch()) {
    include('print_monetized_leave_certification.php');
    include('print_monetized_leave_voucher.php');
  }
} catch (PDOException $e) {
  error_log("Error loading monetized leave modals: " . $e->getMessage());
}
?>
                          
                          
            </div>
            
          </div>
        </div>
        
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
    <script>
    $(document).ready(function() {
        // Check if DataTable is already initialized and destroy it
        if ($.fn.DataTable.isDataTable('#leaveApplicationTable')) {
            $('#leaveApplicationTable').DataTable().destroy();
        }
        
        // Initialize DataTable with custom settings
        $('#leaveApplicationTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 25,
            "order": [[1, "desc"]], // Sort by application date descending
            "language": {
                "search": "Search records:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries available",
                "infoFiltered": "(filtered from _TOTAL_ total entries)"
            }
        });
    });
    
    // Functions for Monetized Leave Reports
    function openMonetizedCertModal(leaveAppId) {
        loadMonetizedCertData(leaveAppId);
        $('#print_monetized_cert_' + leaveAppId).modal('show');
    }
    
    function openMonetizedVoucherModal(leaveAppId) {
        loadMonetizedVoucherData(leaveAppId);
        $('#print_monetized_voucher_' + leaveAppId).modal('show');
    }
    
    function loadMonetizedCertData(leaveAppId) {
        $.ajax({
            url: 'get_leave_application_print_data.php',
            type: 'GET',
            data: { id: leaveAppId },
            dataType: 'json',
            success: function(data) {
                console.log('Monetized Cert Data:', data);
                if (data.success) {
                    var la = data.leave_application;
                    var personnel = data.personnel;
                    var signatories = data.signatories;
                    
                    // Calculate monetization with validation
                    var monthlySalary = parseFloat(personnel.monthly_salary || 0);
                    var days = parseFloat(la.number_of_days || 0);
                    var constant = parseFloat(signatories.monetization_constant || 0.0481927);
                    
                    // Validation and warnings
                    if (monthlySalary === 0 || !personnel.monthly_salary) {
                        alert('⚠️ Warning: Monthly Salary is not set for this employee!\n\nPlease update the Service Record with an Active appointment that includes the Monthly Salary.\n\nGo to: Service Record → Set appointDate_status = "Active" and enter Monthly Salary.');
                        console.error('Monthly Salary is missing or zero');
                    }
                    
                    if (constant === 0 || !signatories.monetization_constant) {
                        alert('⚠️ Warning: Monetization Constant is not configured!\n\nPlease set the constant in Signatories Settings.\n\nDefault constant (0.0481927) will be used.');
                        console.warn('Monetization constant is missing, using default: 0.0481927');
                        constant = 0.0481927;
                    }
                    
                    var totalAmount = (monthlySalary * days) * constant;
                    
                    console.log('Calculation:', {
                        monthlySalary: monthlySalary,
                        days: days,
                        constant: constant,
                        totalAmount: totalAmount,
                        formula: '(' + monthlySalary + ' * ' + days + ') * ' + constant + ' = ' + totalAmount
                    });
                    
                    // Populate data
                    $('#cert_request_' + leaveAppId).text(days + ' Days Monetized Leave');
                    $('#cert_payee_' + leaveAppId).text(personnel.full_name);
                    $('#cert_amount_table_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#cert_total_amount_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#cert_approved_amount_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#cert_amount_words_' + leaveAppId).text(numberToWords(totalAmount));
                    
                    // Signatories
                    $('#cert_admin_name_' + leaveAppId).text(signatories.recommending_name || '____________________________');
                    $('#cert_admin_position_' + leaveAppId).text(signatories.recommending_position || 'Municipal Administrator');
                    $('#cert_budget_officer_' + leaveAppId).text(signatories.budget_officer_name || '____________________________');
                    $('#cert_budget_position_' + leaveAppId).text(signatories.budget_officer_position || 'Mun. Budget Officer');
                    $('#cert_treasurer_' + leaveAppId).text(signatories.treasurer_name || '____________________________');
                    $('#cert_treasurer_position_' + leaveAppId).text(signatories.treasurer_position || 'Acting Mun.-Treasurer');
                    $('#cert_accountant_' + leaveAppId).text(signatories.accountant_name || '____________________________');
                    $('#cert_accountant_position_' + leaveAppId).text(signatories.accountant_position || 'Mun. Accountant');
                } else {
                    console.error('Error loading data:', data.message);
                    alert('Error loading certification data: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error loading certification data. Check console for details.');
            }
        });
    }
    
    function loadMonetizedVoucherData(leaveAppId) {
        $.ajax({
            url: 'get_leave_application_print_data.php',
            type: 'GET',
            data: { id: leaveAppId },
            dataType: 'json',
            success: function(data) {
                console.log('Monetized Voucher Data:', data);
                if (data.success) {
                    var la = data.leave_application;
                    var personnel = data.personnel;
                    var signatories = data.signatories;
                    
                    // Calculate monetization with validation
                    var monthlySalary = parseFloat(personnel.monthly_salary || 0);
                    var days = parseFloat(la.number_of_days || 0);
                    var constant = parseFloat(signatories.monetization_constant || 0.0481927);
                    
                    // Validation and warnings
                    if (monthlySalary === 0 || !personnel.monthly_salary) {
                        alert('⚠️ Warning: Monthly Salary is not set for this employee!\n\nPlease update the Service Record with an Active appointment that includes the Monthly Salary.\n\nGo to: Service Record → Set appointDate_status = "Active" and enter Monthly Salary.');
                        console.error('Monthly Salary is missing or zero');
                    }
                    
                    if (constant === 0 || !signatories.monetization_constant) {
                        alert('⚠️ Warning: Monetization Constant is not configured!\n\nPlease set the constant in Signatories Settings.\n\nDefault constant (0.0481927) will be used.');
                        console.warn('Monetization constant is missing, using default: 0.0481927');
                        constant = 0.0481927;
                    }
                    
                    var totalAmount = (monthlySalary * days) * constant;
                    
                    console.log('Calculation:', {
                        monthlySalary: monthlySalary,
                        days: days,
                        constant: constant,
                        totalAmount: totalAmount,
                        formula: '(' + monthlySalary + ' * ' + days + ') * ' + constant + ' = ' + totalAmount
                    });
                    
                    // Populate data
                    $('#voucher_payee_' + leaveAppId).text(personnel.full_name);
                    $('#voucher_address_' + leaveAppId).text('Hinoba-an Negros Occidental');
                    $('#voucher_particulars_' + leaveAppId).text('To payment of ' + days + ' Days Monetized Leave, as per supporting papers hereto attached..');
                    $('#voucher_amount_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#voucher_amount_due_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#voucher_approved_amount_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    
                    // Signatories
                    $('#voucher_admin_' + leaveAppId).text(signatories.recommending_name || '____________________________');
                    $('#voucher_admin_position_' + leaveAppId).text(signatories.recommending_position || 'Municipal Administrator');
                    $('#voucher_accountant_' + leaveAppId).text(signatories.accountant_name || '____________________________');
                    $('#voucher_accountant_position_' + leaveAppId).text(signatories.accountant_position || 'Municipal Accountant');
                    $('#voucher_treasurer_' + leaveAppId).text(signatories.treasurer_name || '____________________________');
                    $('#voucher_treasurer_position_' + leaveAppId).text(signatories.treasurer_position || 'Acting Municipal Treasurer');
                    $('#voucher_mayor_' + leaveAppId).text(signatories.mayor_name || '____________________________');
                    $('#voucher_mayor_position_' + leaveAppId).text(signatories.mayor_position || 'Municipal Mayor');
                    $('#voucher_payee_sig_' + leaveAppId).text(personnel.full_name);
                    
                    // Accounting entries
                    $('#voucher_acct_particulars_' + leaveAppId).text('PTerm 12 Chmnsl Prmy PB');
                    $('#voucher_acct_code_' + leaveAppId).text('1 01 04 070');
                    $('#voucher_acct_debit_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#voucher_acct_credit_' + leaveAppId).text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                } else {
                    console.error('Error loading data:', data.message);
                    alert('Error loading voucher data: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error loading voucher data. Check console for details.');
            }
        });
    }
    
    function printMonetizedCertification(leaveAppId) {
        var printContent = document.getElementById('print_monetized_cert_content_' + leaveAppId).innerHTML;
        var originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
    
    function printMonetizedVoucher(leaveAppId) {
        var printContent = document.getElementById('print_monetized_voucher_content_' + leaveAppId).innerHTML;
        var originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
    
    // Convert number to words for Amount in Words field
    function numberToWords(num) {
        var ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE'];
        var tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
        var teens = ['TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
        
        if (num === 0) return 'ZERO PESOS';
        
        var intPart = Math.floor(num);
        var decPart = Math.round((num - intPart) * 100);
        
        var words = '';
        
        if (intPart >= 1000) {
            var thousands = Math.floor(intPart / 1000);
            words += convertHundreds(thousands, ones, tens, teens) + ' THOUSAND ';
            intPart %= 1000;
        }
        
        words += convertHundreds(intPart, ones, tens, teens);
        words += ' PESOS';
        
        if (decPart > 0) {
            words += ' AND ' + decPart + '/100';
        }
        
        return words.trim();
    }
    
    function convertHundreds(num, ones, tens, teens) {
        var str = '';
        
        if (num >= 100) {
            str += ones[Math.floor(num / 100)] + ' HUNDRED ';
            num %= 100;
        }
        
        if (num >= 20) {
            str += tens[Math.floor(num / 10)] + ' ';
            num %= 10;
        } else if (num >= 10) {
            str += teens[num - 10] + ' ';
            return str;
        }
        
        if (num > 0) {
            str += ones[num] + ' ';
        }
        
        return str;
    }
    </script>
 
  </body>
</html>
