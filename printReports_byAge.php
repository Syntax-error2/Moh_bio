<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   include('header.php');
   
   // Handle filter parameters with defaults
   $ageFrom = 18;
   $ageTo = 75;
   $empStat_id = 0;
   
   if (isset($_POST['ageFilter'])) {
       $ageFrom = (int)$_POST['ageFrom'];
       $ageTo = (int)$_POST['ageTo'];
       $empStat_id = (int)$_POST['empStat_id'];
   }
   
   // Update ages in database efficiently
   try {
       // Update ages for all personnel with valid birthdates
       $update_age_query = $conn->prepare("
           UPDATE personnels 
           SET age = TIMESTAMPDIFF(YEAR, 
               STR_TO_DATE(CONCAT(bdYYYY, '-', bdMM, '-', bdDD), '%Y-%m-%d'), 
               CURDATE()
           ) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(STR_TO_DATE(CONCAT(bdYYYY, '-', bdMM, '-', bdDD), '%Y-%m-%d'), '%m%d'))
           WHERE bdYYYY IS NOT NULL 
           AND bdYYYY != '' 
           AND bdMM IS NOT NULL 
           AND bdMM != '' 
           AND bdDD IS NOT NULL 
           AND bdDD != ''
           AND TIMESTAMPDIFF(YEAR, STR_TO_DATE(CONCAT(bdYYYY, '-', bdMM, '-', bdDD), '%Y-%m-%d'), CURDATE()) < 80
       ");
       $update_age_query->execute();
   } catch (PDOException $e) {
       error_log("Error updating ages: " . $e->getMessage());
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
            <li class="breadcrumb-item active">Personnel Reports: List by Age</li>
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
                    <strong style="margin-left: 8px; font-size: 18px;">PERSONNEL REPORTS: AGE WITH DATE OF BIRTH</strong>
                    </td>
                    </tr>
                    </table>
                
                <!-- Age update handled at top of page -->
                
                <!-- Filter Form -->
                <div class="row mb-3">
                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <form method="POST" class="form-inline">
                          <label class="mr-2"><i class="fa fa-filter"></i> Filter Age Bracket:</label>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">From Age:</label>
                            <input name="ageFrom" type="number" min="18" max="75" step="1" value="<?php echo htmlspecialchars($ageFrom); ?>" class="form-control" style="width: 100px;" />
                          </div>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">To Age:</label>
                            <input name="ageTo" type="number" min="18" max="75" step="1" value="<?php echo htmlspecialchars($ageTo); ?>" class="form-control" style="width: 100px;" />
                          </div>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">Status:</label>
                            <select name="empStat_id" class="form-control" style="width: 200px;">
                            <?php 
                            if ($empStat_id > 0) {
                                try {
                                    $emp_stat2_query = $conn->prepare("SELECT * FROM emp_status WHERE empStat_id = :empStat_id");
                                    $emp_stat2_query->execute([':empStat_id' => $empStat_id]);
                                    $es2_row = $emp_stat2_query->fetch();
                                    
                                    if ($es2_row) {
                                        $color = ($es2_row['status'] === 'Active') ? 'green' : 'red';
                            ?>
                                <option value="<?php echo $es2_row['empStat_id']; ?>" style="color: <?php echo $color; ?>;"><?php echo htmlspecialchars($es2_row['emp_stat_name']); ?></option>
                                <option value="0">All</option>
                            <?php 
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching employment status: " . $e->getMessage());
                                }
                            } else { 
                            ?>
                                <option value="0">All</option>
                            <?php } ?>
                            
                            <?php
                            try {
                                $emp_stat_query = $conn->prepare("SELECT * FROM emp_status ORDER BY emp_stat_name ASC");
                                $emp_stat_query->execute();
                                while ($es_row = $emp_stat_query->fetch()) {
                                    $color = ($es_row['status'] === 'Active') ? 'green' : 'red';
                            ?>
                            <option value="<?php echo $es_row['empStat_id']; ?>" style="color: <?php echo $color; ?>;"><?php echo htmlspecialchars($es_row['emp_stat_name']); ?></option>
                            <?php 
                                }
                            } catch (PDOException $e) {
                                error_log("Error fetching employment statuses: " . $e->getMessage());
                            }
                            ?>
                            </select>
                          </div>
                          
                          <button type="submit" name="ageFilter" class="btn btn-primary mr-2">
                            <i class="fa fa-search"></i> Apply Filter
                          </button>
                          
                          <a href="printReports_byAge.php?crw=AGE" class="btn btn-secondary mr-2">
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
 
                <!-- report filter Modal -->
                  <div id="print_filter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="checkReportFilter.php?ageFrom=<?php echo urlencode($ageFrom); ?>&ageTo=<?php echo urlencode($ageTo); ?>&empStat_id=<?php echo urlencode($empStat_id); ?>" method="POST">
      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">PRINT AGE BRACKET: <?php echo htmlspecialchars($ageFrom); ?> TO <?php echo htmlspecialchars($ageTo); ?></h5>
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
                          <a href="#" data-dismiss="modal" class="btn btn-secondary" style="color: white;">Cancel</a>
                          <button name="print_filter_byAge" type="submit" class="btn btn-primary">Print</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end report filter Modal -->
                
                <hr />
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                    
                      <thead>
                        <tr>
                          <th>Personnel</th>
                          <th>Office - Designation</th>
                          <th>Sex</th>
                          <th>Date of Birth</th>
                          <th>Age</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      
                      try {
                          // Build query with JOIN to get all data in one query (no N+1 problem)
                          $sql = "SELECT 
                              p.personnel_id, 
                              p.lname, 
                              p.fname, 
                              p.mname, 
                              p.suffix, 
                              p.age, 
                              p.do_id, 
                              p.des_id, 
                              p.empStat_id, 
                              p.bdMM, 
                              p.bdDD, 
                              p.bdYYYY,
                              p.sex,
                              d.des_name,
                              o.dept_office_name
                          FROM personnels p
                          LEFT JOIN designation d ON p.des_id = d.des_id
                          LEFT JOIN dept_offices o ON p.do_id = o.do_id
                          WHERE p.age BETWEEN :ageFrom AND :ageTo
                          AND (p.separation_date = '' OR p.separation_date = '  /  /    ')";
                          
                          $params = [
                              ':ageFrom' => $ageFrom,
                              ':ageTo' => $ageTo
                          ];
                          
                          if ($empStat_id > 0) {
                              $sql .= " AND p.empStat_id = :empStat_id";
                              $params[':empStat_id'] = $empStat_id;
                          }
                          
                          $sql .= " ORDER BY p.age, p.lname, p.fname ASC";
                          
                          $printDataAge_query = $conn->prepare($sql);
                          $printDataAge_query->execute($params);
                          
                          $row_count = 0;
                          while ($printDA_row = $printDataAge_query->fetch()) { 
                              $row_count++;
                              
                              // Format middle name
                              $final_mname = '';
                              if (!empty($printDA_row['mname']) && $printDA_row['mname'] !== '-') {
                                  $final_mname = substr($printDA_row['mname'], 0, 1) . ". ";
                              }
                              
                              // Format full name
                              $full_name = '';
                              if ($printDA_row['suffix'] == "-" || empty($printDA_row['suffix'])) {
                                  $full_name = $printDA_row['lname'] . ', ' . $printDA_row['fname'] . " " . $final_mname;
                              } else {
                                  $full_name = $printDA_row['lname'] . " " . $printDA_row['suffix'] . ', ' . $printDA_row['fname'] . " " . $final_mname;
                              }
                              
                              // Format office and designation
                              $office_designation = ($printDA_row['dept_office_name'] ?? 'N/A') . ' - ' . ($printDA_row['des_name'] ?? 'N/A');
                              
                              // Format birthdate
                              $birthdate = $printDA_row['bdMM'] . '/' . $printDA_row['bdDD'] . '/' . $printDA_row['bdYYYY'];
                      ?>
                      
                      <tr>
                      <td><?php echo htmlspecialchars($full_name); ?></td>
                      <td><?php echo htmlspecialchars($office_designation); ?></td>
                      <td><?php echo htmlspecialchars($printDA_row['sex']); ?></td>
                      <td><?php echo htmlspecialchars($birthdate); ?></td>
                      <td>
                      <?php
                      if ($printDA_row['age'] == 0) { ?>
                        <a href="edit_completePersonnelData.php?dept=<?php echo urlencode($printDA_row['do_id']); ?>&personnel_id=<?php echo urlencode($printDA_row['personnel_id']); ?>" class="btn btn-warning btn-sm" style="color: white;">Set Up Date of Birth</a>
                      <?php } else { 
                          echo htmlspecialchars($printDA_row['age']); 
                      } ?>
                      </td>
                      </tr>
                    
                      <?php 
                          }
                          
                          if ($row_count == 0) {
                              echo '<tr><td colspan="5" class="text-center text-muted">No personnel found in the selected age range.</td></tr>';
                          }
                      } catch (PDOException $e) {
                          error_log("Error fetching personnel data: " . $e->getMessage());
                          echo '<tr><td colspan="5" class="text-center text-danger">Error loading data. Please try again.</td></tr>';
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
        
        <?php include('add_client_comp_modal.php'); ?>
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

     
    
  </body>
</html>