<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   // Sanitize and validate GET parameters BEFORE including header.php
   $get_dept = $_GET['dept'] ?? '';
   $personnel_id = $_GET['personnel_id'] ?? '';
   
   // Validate personnel_id exists - redirect if empty
   if(empty($personnel_id)) {
       header('Location: list_personnel.php?dept=' . urlencode($get_dept));
       exit();
   }
   
   // NOW safe to include header which outputs HTML
   include('header.php');
   
   ?>

  <?php
  
  if(isset($_POST['filterPosition'])){
      $filterPosition = $_POST['filter'];
  } else {
      $filterPosition = 'All';
  } 
  ?>
    
    
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <?php
    try {
        // Fetch personnel details using prepared statement
        $staff_query = $conn->prepare("SELECT personnel_id, fname, mname, lname, suffix, img, shift_id 
                                        FROM personnels 
                                        WHERE personnel_id = :personnel_id 
                                        LIMIT 1");
        $staff_query->execute([':personnel_id' => $personnel_id]);
        $staff_row = $staff_query->fetch();
        
        if (!$staff_row) {
            ?>
            <script>
            alert('Personnel not found.');
            window.location = 'list_personnel.php?dept=<?php echo urlencode($get_dept); ?>';
            </script>
            <?php
            exit();
        }
        
        // Fetch shift details using prepared statement
        $emp_stat_query5 = $conn->prepare("SELECT shift_name, type FROM shifts WHERE shift_id = :shift_id LIMIT 1");
        $emp_stat_query5->execute([':shift_id' => $staff_row['shift_id']]);
        $es_row5 = $emp_stat_query5->fetch();
        
        // Set default if no shift found
        if (!$es_row5) {
            $es_row5 = ['shift_name' => 'N/A', 'type' => 'Regular Shift'];
        }
        
    } catch (PDOException $e) {
        error_log("Error fetching personnel details: " . $e->getMessage());
        ?>
        <script>
        alert('An error occurred. Please try again.');
        window.history.back();
        </script>
        <?php
        exit();
    }
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo htmlspecialchars($schoolName); ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <?php if($session_access == 'Administrator') { ?>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo urlencode($get_dept); ?>">List of Personnel</a></li>
            <?php } ?>
            <li class="breadcrumb-item active">Personnel Deductions</li>
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
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">
                  <h4>
                    <?php
                      // Display personnel name
                      $full_name = htmlspecialchars($staff_row['fname']) . " " . 
                                   substr(htmlspecialchars($staff_row['mname']), 0, 1) . ". " . 
                                   htmlspecialchars($staff_row['lname']);
                      
                      if($staff_row['suffix'] != "-" && !empty($staff_row['suffix'])) {
                          $full_name .= " " . htmlspecialchars($staff_row['suffix']);
                      }
                      
                      echo $full_name;
 
                    ?>
              
                  </h4>
                  </a>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                 
                
                <div class="col-lg-12 mt-2 mb-2">
                <a class="btn btn-secondary" href="list_personnel_individual_details.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> PERSONNEL PROFILE</a>
                <a class="btn btn-secondary" href="list_personnel_income.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> INCOME</a>
                <a class="btn btn-primary" style="color: white; font-weight: bold;" href="list_personnel_deductions.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> DEDUCTIONS</a> 
                <a class="btn btn-secondary" href="list_personnel_individual_details_SR.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> PAY HISTORY</a>
                <a class="btn btn-info" style="color: white;" title="Print personnel data sheet..." href="printPersonnelDataSheet_detailed.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a>  
                </div>      
                
                    <div class="col-lg-12 mt-4 mb-4">
                    
                    <?php
                    // Check if personnel_deductions table exists and show helpful setup message
                    $table_exists = false;
                    try {
                        $check_table = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_deductions'");
                        $table_exists = ($check_table->rowCount() > 0);
                    } catch (PDOException $e) {
                        error_log("Error checking table existence: " . $e->getMessage());
                    }
                    
                    if (!$table_exists) {
                    ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Database Setup Required</h5>
                        <p>The <code>pr_tbl_personnel_deductions</code> table has not been created yet.</p>
                        <hr>
                        <p class="mb-2"><strong>Quick Setup Options:</strong></p>
                        <ol class="mb-2">
                            <li><strong>One-Click Setup:</strong> <a href="setup_personnel_deductions.php" class="btn btn-sm btn-warning" target="_blank"><i class="fa fa-magic"></i> Run Setup Wizard</a></li>
                            <li><strong>Manual Setup:</strong> Import SQL file: <code>payroll/db/personnel_deductions_schema.sql</code> in phpMyAdmin</li>
                        </ol>
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> You can enter amounts below, but they won't be saved until the table is created.
                        </small>
                    </div>
                    <?php
                    }
                    
                    // Show success message if redirected after save
                    if (isset($_GET['success']) && $_GET['success'] == 1) {
                    ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><i class="fa fa-check-circle"></i> Success!</strong> 
                        Personnel deductions have been updated successfully.
                    </div>
                    <?php
                    }
                    
                    // Show error message if save failed
                    if (isset($_GET['error'])) {
                        $error_msg = htmlspecialchars($_GET['error']);
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><i class="fa fa-times-circle"></i> Error!</strong> 
                        <?php echo $error_msg; ?>
                    </div>
                    <?php
                    }
                    ?>
                    
                    <?php 
                    // Initialize totals early for summary cards
                    $total_employer = 0;
                    $total_employee = 0;
                    
                    // Pre-calculate totals if table exists
                    if ($table_exists) {
                        try {
                            $totals_query = $conn->prepare("SELECT 
                                                            COALESCE(SUM(employer_amt_per_pay), 0) as total_employer,
                                                            COALESCE(SUM(employee_amt_per_pay), 0) as total_employee
                                                        FROM pr_tbl_personnel_deductions 
                                                        WHERE personnel_id = :personnel_id");
                            $totals_query->execute([':personnel_id' => $personnel_id]);
                            $totals_result = $totals_query->fetch();
                            
                            if ($totals_result) {
                                $total_employer = $totals_result['total_employer'];
                                $total_employee = $totals_result['total_employee'];
                            }
                        } catch (PDOException $e) {
                            // Table doesn't exist or query failed - use defaults
                            error_log("Note: Could not fetch totals: " . $e->getMessage());
                        }
                    }
                    ?>
                    
                    <?php if ($table_exists) { ?>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fa fa-building"></i> Employer Contributions</h6>
                                    <h3 class="mb-0">₱<?php echo number_format($total_employer ?? 0, 2); ?></h3>
                                    <small>per pay period</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fa fa-user"></i> Employee Deductions</h6>
                                    <h3 class="mb-0">₱<?php echo number_format($total_employee ?? 0, 2); ?></h3>
                                    <small>per pay period</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fa fa-calculator"></i> Total Deductions</h6>
                                    <h3 class="mb-0">₱<?php echo number_format(($total_employer + $total_employee) ?? 0, 2); ?></h3>
                                    <small>per pay period</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <form action="save_personnel_deductions.php" method="POST" id="deductionsForm">

                    <input type="hidden" name="personnel_id" value="<?php echo htmlspecialchars($personnel_id); ?>" />
                    <input type="hidden" name="dept" value="<?php echo htmlspecialchars($get_dept); ?>" />
                    <input type="hidden" name="table_exists" value="<?php echo $table_exists ? '1' : '0'; ?>" />

                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="deductionsTable">
                 
                      <thead class="thead-dark">
                        <tr>
                          <th style="width: 40%;">Deduction Details</th>
                          <th style="width: 30%; text-align: right;">Employer Amount per Pay</th>
                          <th style="width: 30%; text-align: right;">Employee Amount per Pay</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                        <?php
                        try {
                            // Fetch all active deductions using prepared statement
                            $deduction_query = $conn->prepare("SELECT deduction_id, deduction_type, deduction_title 
                                                               FROM pr_tbl_deductions 
                                                               WHERE is_deleted = 0 
                                                               ORDER BY 
                                                                   CASE 
                                                                       WHEN deduction_type = 'Mandatory' THEN 1
                                                                       WHEN deduction_type = 'Voluntary' THEN 2
                                                                       ELSE 3
                                                                   END,
                                                                   deduction_title ASC");
                            $deduction_query->execute();
                            
                            // Check if there are any deductions
                            if ($deduction_query->rowCount() === 0) {
                                ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fa fa-info-circle"></i> No deductions have been configured yet. 
                                        <a href="deductions.php">Click here to add deductions</a>.
                                    </td>
                                </tr>
                                <?php
                            } else {
                            
                            // Initialize empty array for existing personnel deductions
                            $existing_deductions = [];
                            
                            // Try to fetch existing personnel deductions (table may not exist yet)
                            try {
                                $existing_deductions_query = $conn->prepare("SELECT deduction_id, employer_amt_per_pay, employee_amt_per_pay 
                                                                             FROM pr_tbl_personnel_deductions 
                                                                             WHERE personnel_id = :personnel_id");
                                $existing_deductions_query->execute([':personnel_id' => $personnel_id]);
                                
                                // Store existing deductions in associative array for quick lookup
                                while ($existing = $existing_deductions_query->fetch()) {
                                    $existing_deductions[$existing['deduction_id']] = $existing;
                                }
                            } catch (PDOException $e) {
                                // Table doesn't exist yet - that's okay, just use empty array
                                // Log this for admin awareness
                                error_log("Note: pr_tbl_personnel_deductions table may not exist: " . $e->getMessage());
                            }
                            
                            // Initialize loop totals (will recalculate from existing deductions)
                            $loop_total_employer = 0;
                            $loop_total_employee = 0;
                            
                            while ($deduction_row = $deduction_query->fetch()) {
                                $deduction_id = $deduction_row['deduction_id'];
                                $employer_amt = 0;
                                $employee_amt = 0;
                                
                                // Check if this personnel already has this deduction
                                if (isset($existing_deductions[$deduction_id])) {
                                    $employer_amt = $existing_deductions[$deduction_id]['employer_amt_per_pay'];
                                    $employee_amt = $existing_deductions[$deduction_id]['employee_amt_per_pay'];
                                }
                                
                                $loop_total_employer += $employer_amt;
                                $loop_total_employee += $employee_amt;
                        ?>
                            
                      <tr data-deduction-id="<?php echo htmlspecialchars($deduction_id); ?>">
                      
                      <td style="vertical-align: middle;">
                      <strong class="text-uppercase"><?php echo htmlspecialchars($deduction_row['deduction_type']); ?></strong><br />
                      <span class="badge badge-info badge-lg"><?php echo htmlspecialchars($deduction_row['deduction_title']); ?></span>
                      
                      <input name="deduction_id[]" value="<?php echo htmlspecialchars($deduction_id); ?>" type="hidden" />
                      </td>
                      
                      <td style="vertical-align: middle;">
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text">₱</span>
                          </div>
                          <input name="employer_amtPP[]" 
                                 class="form-control text-right employer-amt" 
                                 type="number" 
                                 min="0" 
                                 step="0.01" 
                                 value="<?php echo number_format($employer_amt, 2, '.', ''); ?>" 
                                 placeholder="0.00"
                                 data-toggle="tooltip" 
                                 title="Amount paid by employer per pay period" />
                      </div>
                      </td>
                      
                      <td style="vertical-align: middle;">
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text">₱</span>
                          </div>
                          <input name="employee_amtPP[]" 
                                 class="form-control text-right employee-amt" 
                                 type="number" 
                                 min="0" 
                                 step="0.01" 
                                 value="<?php echo number_format($employee_amt, 2, '.', ''); ?>" 
                                 placeholder="0.00"
                                 data-toggle="tooltip" 
                                 title="Amount deducted from employee per pay period" />
                      </div>
                      </td>
 
                      </tr>
                      
                      <?php 
                            } // end while loop
                            } // end if deductions exist check
                        } catch (PDOException $e) {
                            error_log("Error fetching deductions: " . $e->getMessage());
                            // Set loop totals to 0 on error
                            $loop_total_employer = 0;
                            $loop_total_employee = 0;
                            ?>
                            <tr>
                                <td colspan="3" class="text-center text-danger">
                                    <i class="fa fa-exclamation-circle"></i> An error occurred while loading deductions. Please refresh the page.
                                </td>
                            </tr>
                            <?php
                        }
                      ?>

                      <tr class="table-secondary">
                      <th style="text-align: right; vertical-align: middle;">
                          <strong>Subtotals per Pay Period:</strong>
                      </th>
                      
                      <th style="text-align: right;">
                      <input id="total_employer" value="<?php echo number_format($loop_total_employer ?? 0, 2, '.', ''); ?>" class="form-control text-right font-weight-bold bg-light" type="text" readonly="" title="Total employer contribution per pay period" />
                      </th>
                      
                      <th style="text-align: right;">
                      <input id="total_employee" value="<?php echo number_format($loop_total_employee ?? 0, 2, '.', ''); ?>" class="form-control text-right font-weight-bold bg-light" type="text" readonly="" title="Total employee deduction per pay period" />
                      </th>
                      
                      </tr>
                      
                      <tr class="table-info">
                      <th colspan="2" style="text-align: right; vertical-align: middle;">
                          <strong>Grand Total Deduction per Pay Period:</strong>
                      </th>
                      
                      <th style="text-align: right;">
                      <input id="grand_total" value="<?php echo number_format(($loop_total_employer + $loop_total_employee) ?? 0, 2, '.', ''); ?>" class="form-control text-right font-weight-bold bg-warning text-dark" style="font-size: 1.1em;" type="text" readonly="" title="Combined employer and employee total" />
                      </th>
                      
                      </tr>
                      
                      </tbody>
                    </table>
                    </div>
                    
                    <hr />
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info" role="alert">
                                <small>
                                    <i class="fa fa-info-circle"></i> 
                                    <strong>Note:</strong> Amounts are per pay period. 
                                    <?php if (isset($es_row5['shift_name'])) { ?>
                                    This personnel is on <strong><?php echo htmlspecialchars($es_row5['shift_name']); ?></strong> schedule.
                                    <?php } ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="list_personnel.php?dept=<?php echo urlencode($get_dept); ?>" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to Personnel List
                            </a>
                            <a href="generate_payslip.php?personnel_id=<?php echo urlencode($personnel_id); ?>&dept=<?php echo urlencode($get_dept); ?>" 
                               class="btn btn-info btn-lg" 
                               target="_blank"
                               title="Generate and view payslip">
                                <i class="fa fa-file-text"></i> Generate Payslip
                            </a>
                            <button type="submit" name="save_personnel_deductions" class="btn btn-primary btn-lg" <?php echo !$table_exists ? 'disabled title="Please create the database table first"' : ''; ?>>
                                <i class="fa fa-save"></i> Save Deductions
                            </button>
                        </div>
                    </div>
                    
                  </form>
                    
                  </div>
 
                </div>
              </div>
              <!-- kinder End-->
             
            </div>
            
          </div>
        </div>
     
        
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div><!-- End .page -->


    <?php include('scripts_files.php'); ?>
    
    <script>
    // Real-time calculation of totals and enhanced form validation
    $(document).ready(function() {
        // Calculate totals function
        function calculateTotals() {
            let totalEmployer = 0;
            let totalEmployee = 0;
            
            // Sum all employer amounts
            $('.employer-amt').each(function() {
                let val = parseFloat($(this).val()) || 0;
                totalEmployer += val;
            });
            
            // Sum all employee amounts
            $('.employee-amt').each(function() {
                let val = parseFloat($(this).val()) || 0;
                totalEmployee += val;
            });
            
            // Update total fields with currency formatting
            $('#total_employer').val(totalEmployer.toFixed(2));
            $('#total_employee').val(totalEmployee.toFixed(2));
            
            // Update grand total display
            let grandTotal = totalEmployer + totalEmployee;
            if ($('#grand_total').length) {
                $('#grand_total').val(grandTotal.toFixed(2));
            }
        }
        
        // Calculate on page load
        calculateTotals();
        
        // Calculate on input change with debounce for performance
        let calcTimeout;
        $('.employer-amt, .employee-amt').on('input', function() {
            clearTimeout(calcTimeout);
            calcTimeout = setTimeout(calculateTotals, 300);
        });
        
        // Immediate calculation on blur
        $('.employer-amt, .employee-amt').on('blur', function() {
            calculateTotals();
        });
        
        // Number input formatting on blur
        $('.employer-amt, .employee-amt').on('blur', function() {
            let val = parseFloat($(this).val()) || 0;
            $(this).val(val.toFixed(2));
        });
        
        // Form validation with better UX
        $('#deductionsForm').on('submit', function(e) {
            // Check if table exists
            let tableExists = $('input[name="table_exists"]').val() === '1';
            
            if (!tableExists) {
                e.preventDefault();
                if (confirm('Warning: The personnel deductions table has not been created yet. Your changes will not be saved.\n\nWould you like to go to the setup wizard now?')) {
                    window.open('setup_personnel_deductions.php', '_blank');
                }
                return false;
            }
            
            // Check if at least one amount is entered
            let hasValue = false;
            let totalAmount = 0;
            
            $('.employer-amt, .employee-amt').each(function() {
                let val = parseFloat($(this).val()) || 0;
                if (val > 0) {
                    hasValue = true;
                    totalAmount += val;
                }
            });
            
            if (!hasValue) {
                e.preventDefault();
                alert('⚠️ Please enter at least one deduction amount before saving.\n\nIf you want to remove all deductions, enter 0.00 for all fields and save.');
                return false;
            }
            
            // Confirmation dialog with summary
            let confirmMsg = 'You are about to update personnel deductions.\n\n';
            confirmMsg += 'Total Employer: ₱' + $('#total_employer').val() + '\n';
            confirmMsg += 'Total Employee: ₱' + $('#total_employee').val() + '\n';
            confirmMsg += 'Total Deductions: ₱' + totalAmount.toFixed(2) + '\n\n';
            confirmMsg += 'Do you want to continue?';
            
            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $(this).find('button[type="submit"]').prop('disabled', true)
                   .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        });
        
        // Prevent negative values
        $('.employer-amt, .employee-amt').on('input', function() {
            if ($(this).val() < 0) {
                $(this).val(0);
            }
        });
        
        // Add tooltips for better UX
        $('[data-toggle="tooltip"]').tooltip();
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Highlight rows with values on page load
        $('.employer-amt, .employee-amt').each(function() {
            if (parseFloat($(this).val()) > 0) {
                $(this).closest('tr').addClass('table-success');
            }
        });
        
        // Highlight rows on input
        $('.employer-amt, .employee-amt').on('input blur', function() {
            let $row = $(this).closest('tr');
            let employerVal = parseFloat($row.find('.employer-amt').val()) || 0;
            let employeeVal = parseFloat($row.find('.employee-amt').val()) || 0;
            
            if (employerVal > 0 || employeeVal > 0) {
                $row.addClass('table-success');
            } else {
                $row.removeClass('table-success');
            }
        });
    });
    </script>

  </body>
</html>