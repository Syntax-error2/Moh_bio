<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
    // Handle filter submission
    if(isset($_POST['serviceFilter'])){
        $yearsFrom = isset($_POST['yearsFrom']) ? (int)$_POST['yearsFrom'] : 0;
        $yearsTo = isset($_POST['yearsTo']) ? (int)$_POST['yearsTo'] : 50;
        $empStat_id = isset($_POST['empStat_id']) ? (int)$_POST['empStat_id'] : 0;
    } else {
        $yearsFrom = 0;
        $yearsTo = 50;
        $empStat_id = 0;
    }
    
    // Update num_of_yrs for all personnel (optimized with prepared statements)
    try {
        // First, get all personnel with valid appointment dates
        $update_query = $conn->prepare("SELECT personnel_id, appointment_date FROM personnels WHERE appointment_date != '' AND appointment_date != '  /  /    '");
        $update_query->execute();
        
        $update_stmt = $conn->prepare("UPDATE personnels SET num_of_yrs = :num_of_yrs WHERE personnel_id = :personnel_id");
        
        while($row = $update_query->fetch()) {
            $appointmentDate = $row['appointment_date'];
            
            // Parse appointment date (MM/DD/YYYY format)
            $dateParts = explode("/", $appointmentDate);
            if(count($dateParts) == 3 && checkdate($dateParts[0], $dateParts[1], $dateParts[2])) {
                // Calculate years of service
                $num_of_yrs = (date("md", mktime(0, 0, 0, $dateParts[0], $dateParts[1], $dateParts[2])) > date("md")
                    ? ((date("Y") - $dateParts[2]) - 1)
                    : (date("Y") - $dateParts[2]));
                
                if($num_of_yrs >= 0 && $num_of_yrs < 100) {
                    $update_stmt->execute([
                        ':num_of_yrs' => $num_of_yrs,
                        ':personnel_id' => $row['personnel_id']
                    ]);
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Error updating years of service: " . $e->getMessage());
    }
    
   ?>
  
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
            <li class="breadcrumb-item active">Print Reports</li>
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
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="font-weight: bold !important;">REPORTS</strong></a>
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                    
                    <table>
                    <tr>
                    <td style="background-color: white;  border: none;">
                    
                        <div class="dropdown" style="margin-left: 8px;"><a href="printReports.php" class="dropbtn" style="color: white;">ATTENDANCE REPORTS</a></div>
                        
                        <div class="dropdown" style="margin-left: 8px;">
                        
                          <button class="dropbtn">PERSONNEL REPORTS</button>
                          
                          <div class="dropdown-content">
                            <a href="printReports_byAge.php?crw=AGE">Age with Date of Birth</a>
                            <a href="printReports_byEduc.php?crw=EDUCATION">Educational Attainment</a>
                            <a href="printReports_bySeminar.php?crw=SEMINAR">Seminars Attended</a>
                            <a href="printReports_byService.php?crw=SERVICE">Date Hired with No. of Years</a>
                          </div>
                          
                        </div>
                        
                        <div class="dropdown" style="margin-left: 8px;">
                        
                          <button class="dropbtn">COMPANY REPORTS</button>
                          
                          <div class="dropdown-content">
                            <a href="#">Calendar</a>
                          </div>
                          
                        </div>
                        
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="background-color: white;  border: none;">
                    <strong style="margin-left: 8px; font-size: 18px;">PERSONNEL REPORTS: DATE HIRED WITH NUMBER OF YEARS</strong>
                    </td>
                    </tr>
                    </table>
                
                <!-- Filter Form -->
                <div class="row mb-3">
                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <form method="POST" class="form-inline">
                          <label class="mr-2"><i class="fa fa-filter"></i> Filter Years of Service:</label>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">From:</label>
                            <input type="number" name="yearsFrom" min="0" max="75" step="1" class="form-control" style="width: 100px;" value="<?php echo htmlspecialchars($yearsFrom); ?>" required />
                          </div>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">To:</label>
                            <input type="number" name="yearsTo" min="0" max="75" step="1" class="form-control" style="width: 100px;" value="<?php echo htmlspecialchars($yearsTo); ?>" required />
                          </div>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">Status:</label>
                            <select name="empStat_id" class="form-control" style="width: 200px;">
                              <option value="0" <?php if($empStat_id == 0) echo 'selected'; ?>>All Status</option>
                              <?php
                              try {
                                  $empStat_query = $conn->prepare("SELECT empStat_id, emp_stat_name, status FROM emp_status ORDER BY status DESC, emp_stat_name ASC");
                                  $empStat_query->execute();
                                  
                                  while($es_row = $empStat_query->fetch()) {
                                      $selected = ($empStat_id == $es_row['empStat_id']) ? 'selected' : '';
                                      $color = ($es_row['status'] === 'Active') ? 'color: green;' : 'color: red;';
                                      echo '<option value="'.htmlspecialchars($es_row['empStat_id']).'" '.$selected.' style="'.$color.'">'.htmlspecialchars($es_row['emp_stat_name']).'</option>';
                                  }
                              } catch (PDOException $e) {
                                  error_log("Error fetching employment status: " . $e->getMessage());
                              }
                              ?>
                            </select>
                          </div>
                          
                          <button type="submit" name="serviceFilter" class="btn btn-primary mr-2">
                            <i class="fa fa-search"></i> Apply Filter
                          </button>
                          
                          <a href="printReports_byService.php?crw=SERVICE" class="btn btn-secondary mr-2">
                            <i class="fa fa-refresh"></i> Reset
                          </a>
                          
                          <a data-toggle="modal" data-target="#print_filter" style="color: white;" class="btn btn-info">
                            <i class="fa fa-print"></i> Print Report
                          </a>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Print Filter Modal -->
                <div id="print_filter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                  <div role="document" class="modal-dialog">
                    <div class="modal-content">
                      <form action="checkReportFilter.php?yearsFrom=<?php echo urlencode($yearsFrom); ?>&yearsTo=<?php echo urlencode($yearsTo); ?>&empStat_id=<?php echo urlencode($empStat_id); ?>" method="POST">
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">PRINT SERVICE YEARS: <?php echo htmlspecialchars($yearsFrom); ?> TO <?php echo htmlspecialchars($yearsTo); ?></h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                          <div class="form-group row">
                            <label class="col-sm-4 form-control-label">Print Output:</label>
                            <div class="col-sm-8">
                              <select name="print_output" class="form-control">
                                <option>Male Only</option>
                                <option>Female Only</option>
                                <option>Male-Female</option>
                                <option>All-Mixed</option>
                              </select>
                            </div>
                          </div> 
                        </div>
                        
                        <div class="modal-footer">
                          <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                          <button type="submit" name="print_filter_byService" class="btn btn-primary">Print Report</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                
                <hr />
                
                <div class="col-lg-12">
                  <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                      <thead>
                        <tr>
                          <th>Personnel</th>
                          <th>Office - Designation</th>
                          <th>Date Hired</th>
                          <th># of Years</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      try {
                          // Use JOIN to eliminate N+1 query problem
                          $sql = "SELECT 
                                      p.personnel_id,
                                      p.lname, 
                                      p.fname, 
                                      p.mname, 
                                      p.suffix, 
                                      p.appointment_date, 
                                      p.num_of_yrs,
                                      p.do_id,
                                      d.des_name,
                                      o.dept_office_name
                                  FROM personnels p
                                  LEFT JOIN designation d ON p.des_id = d.des_id
                                  LEFT JOIN dept_offices o ON p.do_id = o.do_id
                                  WHERE p.num_of_yrs BETWEEN :yearsFrom AND :yearsTo
                                  AND (p.separation_date = '' OR p.separation_date = '  /  /    ')";
                          
                          $params = [
                              ':yearsFrom' => $yearsFrom,
                              ':yearsTo' => $yearsTo
                          ];
                          
                          if ($empStat_id > 0) {
                              $sql .= " AND p.empStat_id = :empStat_id";
                              $params[':empStat_id'] = $empStat_id;
                          }
                          
                          $sql .= " ORDER BY p.lname, p.fname ASC";
                          
                          $printDataService_query = $conn->prepare($sql);
                          $printDataService_query->execute($params);
                          
                          $row_count = 0;
                          while($printDS_row = $printDataService_query->fetch()) { 
                              $row_count++;
                              
                              // Format middle name
                              $final_mname = '';
                              if (!empty($printDS_row['mname']) && $printDS_row['mname'] !== '-') {
                                  $final_mname = substr($printDS_row['mname'], 0, 1) . ". ";
                              }
                              
                              // Format full name
                              if ($printDS_row['suffix'] == "-" || empty($printDS_row['suffix'])) {
                                  $full_name = $printDS_row['fname'] . " " . $final_mname . $printDS_row['lname'];
                              } else {
                                  $full_name = $printDS_row['fname'] . " " . $final_mname . $printDS_row['lname'] . " " . $printDS_row['suffix'];
                              }
                              
                              // Format office and designation
                              $office_des = ($printDS_row['dept_office_name'] ?? 'N/A') . ' - ' . ($printDS_row['des_name'] ?? 'N/A');
                              ?>
                              <tr>
                                  <td><?php echo htmlspecialchars($full_name); ?></td>
                                  <td><?php echo htmlspecialchars($office_des); ?></td>
                                  <td>
                                      <?php
                                      if(empty($printDS_row['appointment_date']) || $printDS_row['appointment_date'] == '  /  /    ') { ?>
                                          <a href="edit_completePersonnelData.php?dept=<?php echo urlencode($printDS_row['do_id']); ?>&personnel_id=<?php echo urlencode($printDS_row['personnel_id']); ?>" class="btn btn-warning btn-sm" style="color: white;">
                                              Set Up Date Hired
                                          </a>
                                      <?php } else { 
                                          echo htmlspecialchars($printDS_row['appointment_date']); 
                                      } ?>
                                  </td>
                                  <td><?php echo htmlspecialchars($printDS_row['num_of_yrs']); ?></td>
                              </tr>
                              <?php
                          }
                          
                          // Show message if no data found
                          if ($row_count == 0) {
                              echo '<tr><td colspan="4" style="text-align: center; color: #999;">No personnel found for the selected years of service range.</td></tr>';
                          }
                          
                      } catch (PDOException $e) {
                          error_log("Error fetching service data: " . $e->getMessage());
                          echo '<tr><td colspan="4" class="text-center text-danger">Error loading data. Please try again.</td></tr>';
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