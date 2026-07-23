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
    
    // Use prepared statement to prevent SQL injection
    $staff_query = $conn->prepare("SELECT personnel_id, RFTag_id, personnel_id_code, img, lname, fname, mname, suffix, shift_id, do_id 
                                    FROM personnels WHERE personnel_id = :personnel_id LIMIT 1");
    $staff_query->execute([':personnel_id' => $personnel_id]);
    $staff_row = $staff_query->fetch();
    
    // Handle case where personnel not found
    if (!$staff_row) {
        echo "<script>alert('Personnel not found.'); window.location='home.php';</script>";
        exit;
    }

    // Use prepared statement for shift lookup
    $emp_stat_query5 = $conn->prepare("SELECT shift_id, shift_name FROM shifts WHERE shift_id = :shift_id LIMIT 1");
    $emp_stat_query5->execute([':shift_id' => $staff_row['shift_id']]);
    $es_row5 = $emp_stat_query5->fetch();
    
    // Get department/office name for leave application
    $dept_name = 'Department of Health - Region X'; // Default value
    if (!empty($staff_row['do_id'])) {
        $dept_query = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id LIMIT 1");
        $dept_query->execute([':do_id' => $staff_row['do_id']]);
        $dept_row = $dept_query->fetch();
        if ($dept_row && !empty($dept_row['dept_office_name'])) {
            $dept_name = $dept_row['dept_office_name'];
        }
    }
    
    // Get additional data for print leave card
    $position_name = '';
    $position_query = $conn->prepare("SELECT des.des_name FROM personnels p LEFT JOIN designation des ON p.des_id = des.des_id WHERE p.personnel_id = :pid LIMIT 1");
    $position_query->execute([":pid" => $personnel_id]);
    $position_row = $position_query->fetch();
    if ($position_row) {
        $position_name = strtoupper($position_row["des_name"] ?? "");
    }
    
    $emp_status = 'CASUAL';
    $status_query = $conn->prepare("SELECT roa_status FROM service_record WHERE personnel_id = :pid AND appointDate_status = 'Active' LIMIT 1");
    $status_query->execute([":pid" => $personnel_id]);
    $status_row = $status_query->fetch();
    if ($status_row) {
        $emp_status = strtoupper($status_row["roa_status"] ?? "CASUAL");
    }

    $can_manage_leave_card = ($session_access !== 'User');
    
    // Format personnel name for print
    $personnel_name = '';
    if($staff_row['suffix']=="-") {
        $personnel_name = strtoupper($staff_row['lname'].", ".$staff_row['fname']." ".substr($staff_row['mname'], 0,1).".");
    } else {
        $personnel_name = strtoupper($staff_row['lname'].", ".$staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['suffix']);
    }
    
    ?>


    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item active">Personnels</li>
            <li class="breadcrumb-item active">Leave Management - Leave Card</li>
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
                  <li><a class="dropdown-item" href="leave_application.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Leave Applications</a></li>
                  <li><a class="dropdown-item active" href="leave_card.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>">Leave Card</a></li>
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
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">

            <div class="col-lg-8">
              <h2>Leave Card</h2>
              <p class="text-small text-secondary">Create and manage leave requests</p>
            </div>
            
            <div class="col-lg-4 text-right">
              <?php if ($can_manage_leave_card) { ?>
              <button type="button" 
                      data-toggle="modal"
                      data-target="#add_lc_entry"
                      class="btn btn-success">
                <i class="fa fa-plus"></i> Add Leave Card Entry
              </button>
              <?php } ?>
              
              <button type="button" 
                      onclick="printLeaveCard()"
                      class="btn btn-info">
                <i class="fa fa-print"></i> Print Leave Card
              </button>
            </div>

            <div class="col-lg-12 col-md-12">
              
            <?php
            // Calculate comprehensive leave card statistics
            $vl_total_earned = 0;
            $vl_total_with_pay = 0;
            $vl_total_without_pay = 0;
            $sl_total_earned = 0;
            $sl_total_with_pay = 0;
            $sl_total_without_pay = 0;
            $special_leaves_count = 0;
            $total_entries = 0;
            $vl_current_balance = 0;
            $sl_current_balance = 0;
            
            try {
                // Check if is_special_leave column exists
                $column_check = $conn->query("SHOW COLUMNS FROM leave_card LIKE 'is_special_leave'");
                $has_special_leave_column = $column_check->rowCount() > 0;
                
                if ($has_special_leave_column) {
                    // Exclude special leave "with pay" values from deductions
                    $stats_query = $conn->prepare("SELECT 
                        COALESCE(SUM(vl_earned), 0) as total_vl_earned,
                        COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN vl_with_pay ELSE 0 END), 0) as total_vl_with_pay,
                        COALESCE(SUM(vl_without_pay), 0) as total_vl_without_pay,
                        COALESCE(SUM(sl_earned), 0) as total_sl_earned,
                        COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN sl_with_pay ELSE 0 END), 0) as total_sl_with_pay,
                        COALESCE(SUM(sl_without_pay), 0) as total_sl_without_pay,
                        COALESCE(SUM(CASE WHEN is_special_leave = 1 THEN 1 ELSE 0 END), 0) as special_leaves_count,
                        COUNT(*) as total_entries
                    FROM leave_card 
                    WHERE personnel_id = :personnel_id");
                } else {
                    // Fallback query without is_special_leave column
                    $stats_query = $conn->prepare("SELECT 
                        COALESCE(SUM(vl_earned), 0) as total_vl_earned,
                        COALESCE(SUM(vl_with_pay), 0) as total_vl_with_pay,
                        COALESCE(SUM(vl_without_pay), 0) as total_vl_without_pay,
                        COALESCE(SUM(sl_earned), 0) as total_sl_earned,
                        COALESCE(SUM(sl_with_pay), 0) as total_sl_with_pay,
                        COALESCE(SUM(sl_without_pay), 0) as total_sl_without_pay,
                        0 as special_leaves_count,
                        COUNT(*) as total_entries
                    FROM leave_card 
                    WHERE personnel_id = :personnel_id");
                }
                
                $stats_query->execute([':personnel_id' => $personnel_id]);
                $stats = $stats_query->fetch();
                
                if ($stats) {
                    $vl_total_earned = floatval($stats['total_vl_earned'] ?? 0);
                    $vl_total_with_pay = floatval($stats['total_vl_with_pay'] ?? 0);
                    $vl_total_without_pay = floatval($stats['total_vl_without_pay'] ?? 0);
                    $sl_total_earned = floatval($stats['total_sl_earned'] ?? 0);
                    $sl_total_with_pay = floatval($stats['total_sl_with_pay'] ?? 0);
                    $sl_total_without_pay = floatval($stats['total_sl_without_pay'] ?? 0);
                    $special_leaves_count = intval($stats['special_leaves_count'] ?? 0);
                    $total_entries = intval($stats['total_entries'] ?? 0);
                }
                
                // Calculate current balances
                $vl_current_balance = $vl_total_earned - $vl_total_with_pay;
                $sl_current_balance = $sl_total_earned - $sl_total_with_pay;
                
            } catch (PDOException $e) {
                error_log("Error fetching leave card statistics: " . $e->getMessage());
            }
            ?>
            
            <?php include('encode_daily_log_modal.php'); ?>
            <?php include('restDay_modal.php'); ?>
            <?php include('updateMonthlyLog_modal.php'); ?>
            <?php include('print_monthly_attendance_modal_csf48.php'); ?>
            <?php include('print_monthly_attendance_modal.php'); ?>
            <?php include('print_monthly_LV_modal.php'); ?>
            <?php //include('add_leave_application_modal_list.php'); ?>
            <?php include('print_leave_application_csform6.php'); ?>
            <?php //include('signatories_settings_modal.php'); ?>
            <?php include('personnel_top_panel.php'); ?>
                      
            
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">
                  <h4>Leave Card</h4>
                  </a>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                  
                        <div class="col-lg-12">
                          <!-- Leave Card Summary Statistics - Quick Stats Only -->
                          <div class="row">
                            <!-- Additional Statistics -->
                            <div class="col-lg-12 mt-3">
                              <div class="card border-secondary">
                                <div class="card-body">
                                  <div class="row text-center">
                                    <div class="col-md-3 col-6 mb-2">
                                      <div class="p-2">
                                        <h3 class="mb-0 text-dark"><?php echo $total_entries; ?></h3>
                                        <small class="text-muted"><i class="fa fa-list"></i> Total Entries</small>
                                      </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                      <div class="p-2">
                                        <h3 class="mb-0 text-success"><?php echo $special_leaves_count; ?></h3>
                                        <small class="text-muted"><i class="fa fa-star"></i> Special Leaves</small>
                                      </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                      <div class="p-2">
                                        <h3 class="mb-0 text-primary"><?php echo number_format($vl_current_balance + $sl_current_balance, 3); ?></h3>
                                        <small class="text-muted"><i class="fa fa-calendar-check"></i> Total Balance</small>
                                      </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                      <div class="p-2">
                                        <h3 class="mb-0 text-danger"><?php echo number_format($vl_total_with_pay + $sl_total_with_pay, 3); ?></h3>
                                        <small class="text-muted"><i class="fa fa-calendar-times"></i> Total Used</small>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- End Quick Stats -->


                        <div class="table-responsive">
                        <table id="leaveCardTable" class="display table table-bordered table-striped table-sm" style="width:100%">
                          <thead>
                            <tr>
                              <th colspan="2" style="vertical-align: middle; text-align: center; background-color: #33b35a; color: #ffffff;">PERIOD<br /><small>YYYY-MM-DD</small></th>
                              <th rowspan="2" style="vertical-align: middle; text-align: center; background-color: #33b35a; color: #ffffff;">PARTICULARS</th>
                              <th colspan="4" style="vertical-align: middle; text-align: center; background-color: #33b35a; color: #ffffff;">VACATION LEAVE</th>
                              <th colspan="4" style="vertical-align: middle; text-align: center; background-color: #33b35a; color: #ffffff;">SICK LEAVE</th>
                              <th rowspan="2" style="vertical-align: middle; text-align: center; background-color: #33b35a; color: #ffffff;">REMARKS</th>
                              <?php if ($can_manage_leave_card) { ?>
                              <th rowspan="2" style="vertical-align: middle; text-align: center; background-color: #33b35a; color: #ffffff;">ACTION</th>
                              <?php } ?>
                            </tr>
                            <tr>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">FROM</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">TO</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">EARNED</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">w/ Pay</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">BALANCE</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">w/out Pay</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">EARNED</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">w/ Pay</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">BALANCE</th>
                              <th style="text-align: center; font-size: 11px; background-color: #33b35a; color: #ffffff;">w/out Pay</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $vl_bal = 0.0;
                            $sl_bal = 0.0;
                            
                            // Check if is_special_leave column exists
                            $has_special_leave_column = false;
                            try {
                                $column_check = $conn->query("SHOW COLUMNS FROM leave_card LIKE 'is_special_leave'");
                                $has_special_leave_column = $column_check->rowCount() > 0;
                            } catch (PDOException $e) {
                                error_log("Error checking column: " . $e->getMessage());
                            }
                            
                            try {
                              $lc_query = $conn->prepare("SELECT lc.*, 
                                  (SELECT COUNT(*) FROM leave_applications WHERE leave_card_entry_id = lc.id) as has_leave_application
                                  FROM leave_card lc 
                                  WHERE lc.personnel_id = :personnel_id 
                                  ORDER BY lc.period_from ASC, lc.id ASC");
                              $lc_query->execute([':personnel_id' => $personnel_id]);
                              
                              while ($lc_row = $lc_query->fetch()) { 
                                $vl_earned_val = floatval($lc_row['vl_earned'] ?? 0);
                                $sl_earned_val = floatval($lc_row['sl_earned'] ?? 0);
                                $vl_bal += $vl_earned_val;
                                $sl_bal += $sl_earned_val;
                                
                                // Check if this is a special leave (with fallback for missing column)
                                $is_special = $has_special_leave_column && isset($lc_row['is_special_leave']) && $lc_row['is_special_leave'] == 1;
                                
                                // Check if linked to a leave application
                                $has_leave_app = isset($lc_row['has_leave_application']) && $lc_row['has_leave_application'] > 0;
                            ?>
                                    
                            <tr <?php if($is_special) echo 'class="table-success" title="Special Leave - No Deductions"'; ?>>
                            
                            <td style="vertical-align: middle; text-align: center;"><?php echo $lc_row['period_from']; ?></td>
                            <td style="vertical-align: middle; text-align: center;"><?php echo $lc_row['period_to']; ?></td>
                            
                            <td>
                              <?php 
                                echo $lc_row['particulars']; 
                                if($is_special) {
                                  echo ' <div class="badge badge-success ml-1"><i class="fa fa-star"></i> Special</div>';
                                }
                                if($has_leave_app) {
                                  echo ' <div class="badge badge-info ml-1" title="Linked to Leave Application (CS Form No. 6)"><i class="fa fa-link"></i> App</div>';
                                }
                              ?>
                            </td>
                            
                            <td style="vertical-align: middle; text-align: center;"><?php echo number_format(floatval($lc_row['vl_earned'] ?? 0), 3); ?></td>
                            <td style="vertical-align: middle; text-align: center;">
                              <?php 
                                $vl_with_pay_val = floatval($lc_row['vl_with_pay'] ?? 0);
                                echo number_format($vl_with_pay_val, 3);
                                // Only deduct from balance if NOT special leave
                                if (!$is_special) {
                                  $vl_bal = $vl_bal - $vl_with_pay_val;
                                }
                              ?>
                            </td>
                            <td style="vertical-align: middle; text-align: center;"><?php echo number_format($vl_bal, 3); ?></td>
                            <td style="vertical-align: middle; text-align: center;"><?php echo number_format(floatval($lc_row['vl_without_pay'] ?? 0), 3); ?></td>
                            
                            <td style="vertical-align: middle; text-align: center;"><?php echo number_format(floatval($lc_row['sl_earned'] ?? 0), 3); ?></td>
                            <td style="vertical-align: middle; text-align: center;">
                              <?php 
                                $sl_with_pay_val = floatval($lc_row['sl_with_pay'] ?? 0);
                                echo number_format($sl_with_pay_val, 3);
                                // Only deduct from balance if NOT special leave
                                if (!$is_special) {
                                  $sl_bal = $sl_bal - $sl_with_pay_val;
                                }
                              ?>
                            </td>
                            <td style="vertical-align: middle; text-align: center;"><?php echo number_format($sl_bal, 3); ?></td>
                            <td style="vertical-align: middle; text-align: center;"><?php echo number_format(floatval($lc_row['sl_without_pay'] ?? 0), 3); ?></td>
                            
                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($lc_row['remarks']); ?></td>
                            
                            <?php if ($can_manage_leave_card) { ?>
                            <td style="vertical-align: middle; text-align: center;">
                              <?php if ($has_leave_app): ?>
                                <div class="badge badge-info" title="This entry is from a Leave Application and cannot be edited or deleted manually">
                                  <i class="fa fa-lock"></i> Locked
                                </div>
                              <?php else: ?>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit_lc_entry<?php echo $lc_row['id']; ?>" title="Edit Entry">
                                  <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete_lc_entry<?php echo $lc_row['id']; ?>" title="Delete Entry">
                                  <i class="fa fa-trash"></i>
                                </button>
                              <?php endif; ?>
                            </td>
                            <?php } ?>
                            </tr>
                            
                            <?php if ($can_manage_leave_card && !$has_leave_app): ?>
                              <?php include('edit_leave_card_entry_modal.php'); ?>
                              <?php include('delete_leave_card_entry_modal.php'); ?>
                            <?php endif; ?>
                            
                            <?php 
                              }
                            } catch (PDOException $e) {
                              error_log("Error fetching leave card: " . $e->getMessage());
                              $error_colspan = $can_manage_leave_card ? 13 : 12;
                              echo "<tr><td colspan='" . $error_colspan . "' class='text-center text-danger'>Error loading leave card data.</td></tr>";
                            }
                            ?>
                           
                          </tbody>
                        </table>
                        </div>


                                    <!-- Detailed Leave Summary Statistics - Below Table -->
            <div class="col-lg-12 col-md-12">
              <div class="row">
                <!-- Vacation Leave Summary -->
                <div class="col-lg-6 col-md-6 mb-3">
                  <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                      <h5 class="mb-0"><i class="fa fa-umbrella-beach"></i> Vacation Leave Summary</h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-6 mb-3">
                          <div class="text-center p-3 bg-light rounded">
                            <h2 class="mb-0 text-success"><?php echo number_format($vl_total_earned, 3); ?></h2>
                            <small class="text-muted">Total Earned</small>
                          </div>
                        </div>
                        <div class="col-6 mb-3">
                          <div class="text-center p-3 bg-light rounded">
                            <h2 class="mb-0 text-primary"><?php echo number_format($vl_current_balance, 3); ?></h2>
                            <small class="text-muted">Current Balance</small>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="text-center p-2 border rounded">
                            <h4 class="mb-0 text-danger"><?php echo number_format($vl_total_with_pay, 3); ?></h4>
                            <small class="text-muted">Used (w/ Pay)</small>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="text-center p-2 border rounded">
                            <h4 class="mb-0 text-warning"><?php echo number_format($vl_total_without_pay, 3); ?></h4>
                            <small class="text-muted">Used (w/o Pay)</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Sick Leave Summary -->
                <div class="col-lg-6 col-md-6 mb-3">
                  <div class="card border-info">
                    <div class="card-header bg-info text-white">
                      <h5 class="mb-0"><i class="fa fa-medkit"></i> Sick Leave Summary</h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-6 mb-3">
                          <div class="text-center p-3 bg-light rounded">
                            <h2 class="mb-0 text-success"><?php echo number_format($sl_total_earned, 3); ?></h2>
                            <small class="text-muted">Total Earned</small>
                          </div>
                        </div>
                        <div class="col-6 mb-3">
                          <div class="text-center p-3 bg-light rounded">
                            <h2 class="mb-0 text-info"><?php echo number_format($sl_current_balance, 3); ?></h2>
                            <small class="text-muted">Current Balance</small>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="text-center p-2 border rounded">
                            <h4 class="mb-0 text-danger"><?php echo number_format($sl_total_with_pay, 3); ?></h4>
                            <small class="text-muted">Used (w/ Pay)</small>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="text-center p-2 border rounded">
                            <h4 class="mb-0 text-warning"><?php echo number_format($sl_total_without_pay, 3); ?></h4>
                            <small class="text-muted">Used (w/o Pay)</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Detailed Leave Summary Statistics -->


                      </div>
                </div>
              </div>
              <!-- kinder End-->
              
              
<!-- ############################## ADD LEAVE CARD ENTRY Modal #########################################-->
    
    
                        <!-- ADD LEAVE CARD ENTRY Modal -->
                          <?php if ($can_manage_leave_card) { ?>
                          <div id="add_lc_entry" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-lg">
                              <div class="modal-content">
                              
                              <form action="save_add_leave_card_entry.php" method="POST">
                              
                              <input name="personnel_id" value="<?php echo htmlspecialchars($staff_row['personnel_id'] ?? ''); ?>" type="hidden" />
                              <input name="do_id" value="<?php echo htmlspecialchars($staff_row['do_id'] ?? ''); ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">ADD LEAVE CARD ENTRY</h5>
                                  <a data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="icon-close"></span></a>
                                </div>
                                
                                <div class="modal-body">

                                    <div class="form-group row">
                                        <label class="col-sm-2 form-control-label text-bold">PERIOD</label>
                                        <div class="col-sm-10">
                                        
                                            <div class="row">
                                              <div class="col-6">
                                                <input name="period_from" type="date" value="<?php echo date('Y-m-01', strtotime($currentDate)); ?>" class="form-control" />
                                                <small class="form-text">From</small>
                                              </div>
                                              
                                              <div class="col-6">
                                                <input name="period_to" type="date" value="<?php echo date('Y-m-t', strtotime($currentDate)); ?>" class="form-control" />
                                                <small class="form-text">To</small>
                                              </div> 
                                            </div>
                                        
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 form-control-label text-bold">PARTICULARS</label>
                                        <div class="col-sm-10">
                                            <input name="particulars" type="text" class="form-control mb-2" placeholder="e.g. Jan 1 - Jan 31, <?php echo date('Y'); ?>" />
                                            <?php
                                            // Check if special leave column exists
                                            $show_special_leave = false;
                                            try {
                                                $col_check = $conn->query("SHOW COLUMNS FROM leave_card LIKE 'is_special_leave'");
                                                $show_special_leave = $col_check->rowCount() > 0;
                                            } catch (PDOException $e) {
                                                error_log("Error checking column: " . $e->getMessage());
                                            }
                                            
                                            if ($show_special_leave): ?>
                                            <div class="custom-control custom-checkbox mb-2">
                                              <input type="checkbox" name="is_special_leave" class="custom-control-input" id="is_special_leave" />
                                              <label class="custom-control-label" for="is_special_leave">Special Leave / No leave credit deductions</label>
                                            </div>
                                            <?php else: ?>
                                            <div class="alert alert-info mb-2 py-2">
                                              <small><i class="fa fa-info-circle"></i> <strong>Special Leave feature available!</strong> Run the database update to enable it. See <code>LEAVE_CARD_UPDATE_INSTRUCTIONS.md</code></small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 form-control-label text-bold">VACATION LEAVE</label>
                                        <div class="col-sm-10">
                                        
                                            <div class="row">
                                              <div class="col-4">
                                                <input name="vl_earned" type="number" step="0.001" min="0.000" class="form-control" />
                                                <small class="form-text">Earned</small>
                                              </div> 
                                              
                                              <div class="col-4">
                                                <input name="vl_with_pay" type="number" step="0.001" min="0.000" class="form-control" />
                                                <small class="form-text">With Pay</small>
                                              </div> 
                                              
                                              <div class="col-4">
                                                <input name="vl_without_pay" type="number" step="0.001" min="0.000" class="form-control" />
                                                <small class="form-text">Without Pay</small>
                                              </div>
                                            </div>
                                        
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 form-control-label text-bold">SICK LEAVE</label>
                                        <div class="col-sm-10">
                                        
                                            <div class="row">
                                              <div class="col-4">
                                                <input name="sl_earned" type="number" step="0.001" min="0.000" class="form-control" />
                                                <small class="form-text">Earned</small>
                                              </div> 
                                              
                                              <div class="col-4">
                                                <input name="sl_with_pay" type="number" step="0.001" min="0.000" class="form-control" />
                                                <small class="form-text">With Pay</small>
                                              </div> 
                                              
                                              <div class="col-4">
                                                <input name="sl_without_pay" type="number" step="0.001" min="0.000" class="form-control" />
                                                <small class="form-text">Without Pay</small>
                                              </div> 
                                            </div>
                                        
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 form-control-label text-bold">REMARKS</label>
                                        <div class="col-sm-10">
                                            <input name="remarks" type="text" class="form-control" />
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="modal-footer">
                                  <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                                  <button name="save_new_entry" type="submit" class="btn btn-success">Submit</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end ADD LEAVE CARD ENTRY Modal -->
                          <?php } ?>
                           
            </div>
            
          </div>
        </div>
        
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
    <script>
    $(document).ready(function() {
        // Check if DataTable already exists and destroy it
        if ($.fn.DataTable.isDataTable('#leaveCardTable')) {
            $('#leaveCardTable').DataTable().destroy();
        }
        
        // Initialize DataTable with custom configuration for complex headers
        $('#leaveCardTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "pageLength": 25,
            "order": [[0, "asc"]], // Sort by period_from (FROM date) ascending
            "columnDefs": [
                {
                    "targets": [0, 1], // Date columns (FROM, TO)
                    "type": "date",
                    "width": "100px"
                },
                {
                    "targets": [4, 5, 6, 7, 8, 9, 10, 11], // Numeric columns
                    "className": "text-center",
                    "width": "70px"
                }
            ],
            "language": {
                "search": "Search records:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries available",
                "infoFiltered": "(filtered from _TOTAL_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
            "drawCallback": function(settings) {
                // Re-initialize dropdowns after table redraw
                $('.dropdown-toggle').dropdown();
            }
        });
        
        // Handle special leave checkbox for ADD modal - just show/hide notice
        $('#is_special_leave').on('change', function() {
            var isChecked = $(this).is(':checked');
            var modal = $('#add_lc_entry');
            
            if (isChecked) {
                // Show notice that values are saved but won't deduct from balance
                if (!modal.find('.special-leave-notice').length) {
                    modal.find('input[name="particulars"]').after(
                        '<small class="special-leave-notice text-success d-block mt-1"><i class="fa fa-info-circle"></i> <strong>Special Leave:</strong> "With Pay" values will be saved but will NOT deduct from leave credits balance</small>'
                    );
                }
            } else {
                // Remove notice
                modal.find('.special-leave-notice').remove();
            }
        });
        
        // Handle special leave checkbox for EDIT modals
        $(document).on('change', '[id^="is_special_leave_edit"]', function() {
            var isChecked = $(this).is(':checked');
            var modal = $(this).closest('.modal');
            
            if (isChecked) {
                // Show notice that values are saved but won't deduct from balance
                if (!modal.find('.special-leave-notice').length) {
                    modal.find('input[name="particulars"]').after(
                        '<small class="special-leave-notice text-success d-block mt-1"><i class="fa fa-info-circle"></i> <strong>Special Leave:</strong> "With Pay" values will be saved but will NOT deduct from leave credits balance</small>'
                    );
                }
            } else {
                // Remove notice
                modal.find('.special-leave-notice').remove();
            }
        });
        
        // Initialize edit modal states when they open
        $(document).on('show.bs.modal', '[id^="edit_lc_entry"]', function() {
            var modal = $(this);
            var checkbox = modal.find('[id^="is_special_leave_edit"]');
            
            if (checkbox.is(':checked')) {
                if (!modal.find('.special-leave-notice').length) {
                    modal.find('input[name="particulars"]').after(
                        '<small class="special-leave-notice text-success d-block mt-1"><i class="fa fa-info-circle"></i> <strong>Special Leave:</strong> "With Pay" values will be saved but will NOT deduct from leave credits balance</small>'
                    );
                }
            }
        });
    });
    
    // Print Leave Card Function
    function printLeaveCard() {
        var personnelName = '<?php echo addslashes($personnel_name); ?>';
        var position = '<?php echo addslashes($position_name); ?>';
        var empStatus = '<?php echo addslashes($emp_status); ?>';
        var deptName = '<?php echo addslashes(strtoupper($dept_name)); ?>';
        var vlBalance = '<?php echo number_format($vl_current_balance, 3); ?>';
        var slBalance = '<?php echo number_format($sl_current_balance, 3); ?>';
        
        var printWindow = window.open('', '_blank');
        var printContent = '<!DOCTYPE html><html><head><title>Leave Card</title>';
        printContent += '<style>';
        printContent += '@media print { body { margin: 0; padding: 20px; font-family: Arial, sans-serif; font-size: 11px; } @page { size: auto landscape; margin: 15mm; } .print-container { max-width: 100%; overflow: visible; } }';
        printContent += '/* Support for Letter (8.5"x11"), Folio (8.5"x13"), and Legal (8.5"x14") in landscape */';
        printContent += 'body { font-family: Arial, sans-serif; font-size: 11px; }';
        printContent += '.header-row { display: table; width: 100%; margin-bottom: 8px; }';
        printContent += '.header-col { display: table-cell; width: 33.33%; vertical-align: top; padding: 0 5px; }';
        printContent += '.info-line { margin: 3px 0; font-size: 10px; }';
        printContent += '.info-label { font-weight: normal; display: inline-block; width: 120px; }';
        printContent += '.info-value { font-weight: bold; border-bottom: 1px solid #000; display: inline-block; min-width: 150px; padding: 0 5px; }';
        printContent += '.header-center { text-align: center; margin: 15px 0 10px 0; }';
        printContent += '.header-center h2 { margin: 5px 0; font-size: 16px; }';
        printContent += 'table { width: 100%; border-collapse: collapse; margin-top: 10px; }';
        printContent += 'th, td { border: 1px solid #000; padding: 4px; text-align: center; }';
        printContent += 'th { background-color: #e0e0e0; font-weight: bold; font-size: 10px; }';
        printContent += 'td { font-size: 10px; }';
        printContent += '.text-left { text-align: left !important; }';
        printContent += '</style></head>';
        printContent += '<body onload="window.print(); window.close();">';
        
        // Header with personnel information - 3 columns
        printContent += '<div class="header-row">';
        
        // Column 1
        printContent += '<div class="header-col">';
        printContent += '<div class="info-line"><span class="info-label">Name</span><br><span class="info-value">' + personnelName + '</span></div>';
        printContent += '<div class="info-line"><span class="info-label">Position</span><br><span class="info-value">' + position + '</span></div>';
        printContent += '<div class="info-line"><span class="info-label">Status</span><br><span class="info-value">' + empStatus + '</span></div>';
        printContent += '</div>';
        
        // Column 2
        printContent += '<div class="header-col">';
        printContent += '<div class="info-line"><span class="info-label">Civil Status</span><br><span class="info-value">&nbsp;</span></div>';
        printContent += '<div class="info-line"><span class="info-label">Entrance to Duty</span><br><span class="info-value">&nbsp;</span></div>';
        printContent += '<div class="info-line"><span class="info-label">Unit</span><br><span class="info-value">' + deptName + '</span></div>';
        printContent += '</div>';
        
        // Column 3
        printContent += '<div class="header-col">';
        printContent += '<div class="info-line"><span class="info-label">GSIS Policy No.</span><br><span class="info-value">&nbsp;</span></div>';
        printContent += '<div class="info-line"><span class="info-label">TIN</span><br><span class="info-value">&nbsp;</span></div>';
        printContent += '<div class="info-line"><span class="info-label">National Reference Card No.</span><br><span class="info-value">&nbsp;</span></div>';
        printContent += '</div>';
        
        printContent += '</div>';
        
        printContent += '<div class="header-center">';
        printContent += '<h2>LEAVE CARD</h2>';
        printContent += '</div>';
        
        printContent += '<table><thead><tr>';
        printContent += '<th colspan="2">PERIOD<br><small>YYYY-MM-DD</small></th><th rowspan="2">PARTICULARS</th>';
        printContent += '<th colspan="4">VACATION LEAVE</th><th colspan="4">SICK LEAVE</th><th rowspan="2">REMARKS</th>';
        printContent += '</tr><tr>';
        printContent += '<th>FROM</th><th>TO</th><th>EARNED</th><th>w/ Pay</th><th>BALANCE</th><th>w/out Pay</th>';
        printContent += '<th>EARNED</th><th>w/ Pay</th><th>BALANCE</th><th>w/out Pay</th>';
        printContent += '</tr></thead><tbody>';
        
        $('#leaveCardTable tbody tr').each(function() {
            var row = $(this);
            if (row.find('td').length > 0) {
                printContent += '<tr>';
                row.find('td').each(function(index) {
                    if (index < row.find('td').length - 1) {
                        var tempDiv = $('<div>').html($(this).html());
                        tempDiv.find('.badge, .btn').remove();
                        var cleanContent = tempDiv.text().trim();
                        var className = (index === 2) ? ' class="text-left"' : '';
                        printContent += '<td' + className + '>' + cleanContent + '</td>';
                    }
                });
                printContent += '</tr>';
            }
        });
        
        printContent += '</tbody></table>';
        printContent += '<div style="margin-top: 20px; font-size: 10px;">';
        printContent += '<p><strong>Summary:</strong></p>';
        printContent += '<p>Total VL Balance: ' + vlBalance + ' | Total SL Balance: ' + slBalance + '</p>';
        printContent += '<p>Printed: ' + new Date().toLocaleString() + '</p>';
        printContent += '</div></body></html>';
        
        printWindow.document.write(printContent);
        printWindow.document.close();
    }
    </script>
 
  </body>
</html>