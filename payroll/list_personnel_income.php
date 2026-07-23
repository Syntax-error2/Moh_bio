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
            <li class="breadcrumb-item active">Personnel Income</li>
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
                <a class="btn btn-primary" style="color: white; font-weight: bold;" href="list_personnel_income.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> INCOME</a>
                <a class="btn btn-secondary" href="list_personnel_deductions.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> DEDUCTIONS</a> 
                <a class="btn btn-secondary" href="list_personnel_individual_details_SR.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>"> PAY HISTORY</a>
                <a class="btn btn-info" style="color: white;" title="Print personnel data sheet..." href="printPersonnelDataSheet_detailed.php?dept=<?php echo urlencode($get_dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a>  
                </div>      
                
                    <div class="col-lg-12 mt-4 mb-4">
                    
                    <?php
                    // Check if personnel_income table exists and show helpful setup message
                    $table_exists = false;
                    try {
                        $check_table = $conn->query("SHOW TABLES LIKE 'pr_tbl_personnel_income'");
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
                        <p>The <code>pr_tbl_personnel_income</code> table has not been created yet.</p>
                        <hr>
                        <p class="mb-2"><strong>Quick Setup Options:</strong></p>
                        <ol class="mb-2">
                            <li><strong>One-Click Setup:</strong> <a href="setup_personnel_income.php" class="btn btn-sm btn-warning" target="_blank"><i class="fa fa-magic"></i> Run Setup Wizard</a></li>
                            <li><strong>Manual Setup:</strong> Import SQL file: <code>payroll/db/personnel_income_schema.sql</code> in phpMyAdmin</li>
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
                        Personnel income has been updated successfully.
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
                    // Initialize total early for summary card
                    $total_income = 0;
                    
                    // Pre-calculate total if table exists
                    if ($table_exists) {
                        try {
                            $total_query = $conn->prepare("SELECT COALESCE(SUM(amount_per_pay), 0) as total_income
                                                          FROM pr_tbl_personnel_income 
                                                          WHERE personnel_id = :personnel_id 
                                                            AND is_active = 1");
                            $total_query->execute([':personnel_id' => $personnel_id]);
                            $total_result = $total_query->fetch();
                            
                            if ($total_result) {
                                $total_income = $total_result['total_income'];
                            }
                        } catch (PDOException $e) {
                            // Table doesn't exist or query failed - use defaults
                            error_log("Note: Could not fetch total income: " . $e->getMessage());
                        }
                    }
                    ?>
                    
                    <?php if ($table_exists) { ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fa fa-money"></i> Total Gross Income</h6>
                                    <h3 class="mb-0">₱<?php echo number_format($total_income ?? 0, 2); ?></h3>
                                    <small>per pay period</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <form action="save_personnel_income.php" method="POST" id="incomeForm">

                    <input type="hidden" name="personnel_id" value="<?php echo htmlspecialchars($personnel_id); ?>" />
                    <input type="hidden" name="dept" value="<?php echo htmlspecialchars($get_dept); ?>" />
                    <input type="hidden" name="table_exists" value="<?php echo $table_exists ? '1' : '0'; ?>" />

                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="incomeTable">
                 
                      <thead class="thead-dark">
                        <tr>
                          <th style="width: 60%;">Income Details</th>
                          <th style="width: 40%; text-align: right;">Amount per Pay</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                        <?php
                        try {
                            // Fetch all active income types using prepared statement
                            $income_query = $conn->prepare("SELECT income_id, income_type, income_title 
                                                           FROM pr_tbl_income 
                                                           WHERE is_deleted = 0 
                                                           ORDER BY 
                                                               CASE 
                                                                   WHEN income_type = 'Regular' THEN 1
                                                                   WHEN income_type = 'Additional' THEN 2
                                                                   ELSE 3
                                                               END,
                                                               income_title ASC");
                            $income_query->execute();
                            
                            // Check if there are any income types
                            if ($income_query->rowCount() === 0) {
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        <i class="fa fa-info-circle"></i> No income types have been configured yet. 
                                        <a href="income.php">Click here to add income types</a>.
                                    </td>
                                </tr>
                                <?php
                            } else {
                            
                            // Initialize empty array for existing personnel income
                            $existing_income = [];
                            
                            // Try to fetch existing personnel income (table may not exist yet)
                            try {
                                $existing_income_query = $conn->prepare("SELECT income_id, amount_per_pay 
                                                                        FROM pr_tbl_personnel_income 
                                                                        WHERE personnel_id = :personnel_id
                                                                          AND is_active = 1");
                                $existing_income_query->execute([':personnel_id' => $personnel_id]);
                                
                                // Store existing income in associative array for quick lookup
                                while ($existing = $existing_income_query->fetch()) {
                                    $existing_income[$existing['income_id']] = $existing;
                                }
                            } catch (PDOException $e) {
                                // Table doesn't exist yet - that's okay, just use empty array
                                error_log("Note: pr_tbl_personnel_income table may not exist: " . $e->getMessage());
                            }
                            
                            // Initialize loop total (will recalculate from existing income)
                            $loop_total_income = 0;
                            
                            while ($income_row = $income_query->fetch()) {
                                $income_id = $income_row['income_id'];
                                $amount = 0;
                                
                                // Check if this personnel already has this income
                                if (isset($existing_income[$income_id])) {
                                    $amount = $existing_income[$income_id]['amount_per_pay'];
                                }
                                
                                $loop_total_income += $amount;
                        ?>
                            
                      <tr data-income-id="<?php echo htmlspecialchars($income_id); ?>">
                      
                      <td style="vertical-align: middle;">
                      <strong class="text-uppercase"><?php echo htmlspecialchars($income_row['income_type']); ?></strong><br />
                      <span class="badge badge-success badge-lg"><?php echo htmlspecialchars($income_row['income_title']); ?></span>
                      
                      <input name="income_id[]" value="<?php echo htmlspecialchars($income_id); ?>" type="hidden" />
                      </td>
                      
                      <td style="vertical-align: middle;">
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text">₱</span>
                          </div>
                          <input name="amount_per_pay[]" 
                                 class="form-control text-right income-amt" 
                                 type="number" 
                                 min="0" 
                                 step="0.01" 
                                 value="<?php echo number_format($amount, 2, '.', ''); ?>" 
                                 placeholder="0.00"
                                 data-toggle="tooltip" 
                                 title="Amount paid per pay period" />
                      </div>
                      </td>
 
                      </tr>
                      
                      <?php 
                            } // end while loop
                            } // end if income types exist check
                        } catch (PDOException $e) {
                            error_log("Error fetching income types: " . $e->getMessage());
                            // Set loop total to 0 on error
                            $loop_total_income = 0;
                            ?>
                            <tr>
                                <td colspan="2" class="text-center text-danger">
                                    <i class="fa fa-exclamation-circle"></i> An error occurred while loading income types. Please refresh the page.
                                </td>
                            </tr>
                            <?php
                        }
                      ?>

                      <tr class="table-success">
                      <th style="text-align: right; vertical-align: middle;">
                          <strong>Total Gross Income per Pay Period:</strong>
                      </th>
                      
                      <th style="text-align: right;">
                      <input id="total_income" value="<?php echo number_format($loop_total_income ?? 0, 2, '.', ''); ?>" class="form-control text-right font-weight-bold bg-light" style="font-size: 1.1em;" type="text" readonly="" title="Total gross income per pay period" />
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
                            <button type="submit" name="save_personnel_income" class="btn btn-success btn-lg"<?php if (!$table_exists) { echo ' disabled'; } ?>>
                                <i class="fa fa-save"></i> Save Income
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
    // Real-time calculation of total income and enhanced form validation
    $(document).ready(function() {
        // Calculate total function
        function calculateTotal() {
            let totalIncome = 0;
            
            // Sum all income amounts
            $('.income-amt').each(function() {
                let val = parseFloat($(this).val()) || 0;
                totalIncome += val;
            });
            
            // Update total field with currency formatting
            $('#total_income').val(totalIncome.toFixed(2));
        }
        
        // Calculate on page load
        calculateTotal();
        
        // Calculate on input change with debounce for performance
        let calcTimeout;
        $('.income-amt').on('input', function() {
            clearTimeout(calcTimeout);
            calcTimeout = setTimeout(calculateTotal, 300);
        });
        
        // Immediate calculation on blur
        $('.income-amt').on('blur', function() {
            calculateTotal();
        });
        
        // Number input formatting on blur
        $('.income-amt').on('blur', function() {
            let val = parseFloat($(this).val()) || 0;
            $(this).val(val.toFixed(2));
        });
        
        // Form validation with better UX
        $('#incomeForm').on('submit', function(e) {
            // Check if table exists
            let tableExists = $('input[name="table_exists"]').val() === '1';
            
            if (!tableExists) {
                e.preventDefault();
                if (confirm('Warning: The personnel income table has not been created yet. Your changes will not be saved.\n\nWould you like to go to the setup wizard now?')) {
                    window.open('setup_personnel_income.php', '_blank');
                }
                return false;
            }
            
            // Calculate total amount
            let totalAmount = 0;
            $('.income-amt').each(function() {
                let val = parseFloat($(this).val()) || 0;
                totalAmount += val;
            });
            
            // Confirmation dialog with summary
            let confirmMsg = 'You are about to update personnel income.\n\n';
            confirmMsg += 'Total Gross Income: ₱' + totalAmount.toFixed(2) + '\n';
            confirmMsg += 'per pay period\n\n';
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
        $('.income-amt').on('input', function() {
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
        $('.income-amt').each(function() {
            if (parseFloat($(this).val()) > 0) {
                $(this).closest('tr').addClass('table-success');
            }
        });
        
        // Highlight rows on input
        $('.income-amt').on('input blur', function() {
            let $row = $(this).closest('tr');
            let val = parseFloat($(this).val()) || 0;
            
            if (val > 0) {
                $row.addClass('table-success');
            } else {
                $row.removeClass('table-success');
            }
        });
    });
    </script>

  </body>
</html>