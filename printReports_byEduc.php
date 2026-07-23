<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
    if(isset($_POST['educFilter'])){
        
    $degree=$_POST['degree'];
    $school_name=$_POST['school_name'];
    $empStat_id = isset($_POST['empStat_id']) ? (int)$_POST['empStat_id'] : 0;
    
    }else{
        
    $degree='ALL';
    $school_name='ALL';
    $empStat_id = 0;
    
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
                    <strong style="margin-left: 8px; font-size: 18px;">PERSONNEL REPORTS: EDUCATIONAL ATTAINMENT</strong>
                    </td>
                    </tr>
                    </table>
                 
                <!-- Filter Form -->
                <div class="row mb-3">
                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <form method="POST" class="form-inline">
                          <label class="mr-2"><i class="fa fa-filter"></i> Filter:</label>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">Degree:</label>
                            <select name="degree" class="form-control" style="width: 150px;">
                            <option><?php echo htmlspecialchars($degree); ?></option>
                            <option>Elementary</option>
                            <option>High School</option>
                            <option>Undergrad</option>
                            <option>Bachelors</option>
                            <option>Masters</option>
                            <option>Doctors</option>
                            <option>Others</option>
                            <option>ALL</option>
                            </select>
                          </div>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">School:</label>
                            <select name="school_name" class="form-control" style="width: 200px;">
                            <option><?php echo htmlspecialchars($school_name); ?></option>
                            <?php
                            try {
                                $pebSName_query = $conn->prepare("SELECT DISTINCT school_name FROM personnel_educ_bg ORDER BY school_name ASC");
                                $pebSName_query->execute();
                                while ($pebSN_row = $pebSName_query->fetch()) {
                                    echo '<option>' . htmlspecialchars($pebSN_row['school_name']) . '</option>';
                                }
                            } catch (PDOException $e) {
                                error_log("Error fetching school names: " . $e->getMessage());
                            }
                            ?>
                            <option>ALL</option>
                            </select>
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
                                $emp_stat_query = $conn->prepare("SELECT * FROM emp_status ORDER BY status DESC, emp_stat_name ASC");
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
                          
                          <button type="submit" name="educFilter" class="btn btn-primary mr-2">
                            <i class="fa fa-search"></i> Apply Filter
                          </button>
                          
                          <a href="printReports_byEduc.php?crw=EDUCATION" class="btn btn-secondary mr-2">
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
                      
                      <form action="checkReportFilter.php?degree=<?php echo urlencode($degree); ?>&school_name=<?php echo urlencode($school_name); ?>&empStat_id=<?php echo urlencode($empStat_id); ?>" method="POST">
      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">PRINT SCHOLASTIC RECORDS</h5>
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
                          <button name="print_filter_byEduc" type="submit" class="btn btn-primary">Print</button>
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
                              <th>PERSONNEL</th>
                              <th>DEGREE</th>
                              <th>COURSE</th>
                              <th>UNITS</th>
                              <th>YEAR GRADUATED</th>
                              <th>SCHOOL</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                
                                try {
                                    // Build query with JOIN to eliminate N+1 problem
                                    $sql = "SELECT 
                                        peb.*,
                                        p.lname,
                                        p.fname,
                                        p.mname,
                                        p.suffix
                                    FROM personnel_educ_bg peb
                                    INNER JOIN personnels p ON peb.personnel_id = p.personnel_id
                                    WHERE (p.separation_date = '' OR p.separation_date = '  /  /    ')";
                                    
                                    $params = [];
                                    
                                    // Add employment status filter
                                    if ($empStat_id > 0) {
                                        $sql .= " AND p.empStat_id = :empStat_id";
                                        $params[':empStat_id'] = $empStat_id;
                                    }
                                    
                                    // Add filters based on selections
                                    if ($degree !== 'ALL' && $school_name !== 'ALL') {
                                        $sql .= " AND peb.degree = :degree AND peb.school_name = :school_name";
                                        $params[':degree'] = $degree;
                                        $params[':school_name'] = $school_name;
                                        $sql .= " ORDER BY peb.degree ASC";
                                    } elseif ($degree !== 'ALL' && $school_name === 'ALL') {
                                        $sql .= " AND peb.degree = :degree";
                                        $params[':degree'] = $degree;
                                        $sql .= " ORDER BY peb.school_name ASC";
                                    } elseif ($degree === 'ALL' && $school_name !== 'ALL') {
                                        $sql .= " AND peb.school_name = :school_name";
                                        $params[':school_name'] = $school_name;
                                        $sql .= " ORDER BY peb.school_name ASC";
                                    } else {
                                        $sql .= " ORDER BY peb.school_name ASC";
                                    }
                                    
                                    $peb_query = $conn->prepare($sql);
                                    $peb_query->execute($params);
                                    
                                    while ($peb_row = $peb_query->fetch())
                                    {
                                        $printDA_row = $peb_row; // Data already joined 
                      
                                    ?>
                                    
     
               
                            <tr>
                            <td>
                                    <?php
                                    // Format middle name
                                    $final_mname = '';
                                    if (!empty($printDA_row['mname']) && $printDA_row['mname'] !== '-') {
                                        $final_mname = substr($printDA_row['mname'], 0, 1) . ". ";
                                    }
                                    
                                    // Format full name
                                    if ($printDA_row['suffix'] == "-" || empty($printDA_row['suffix'])) {
                                        echo htmlspecialchars($printDA_row['lname'] . ", " . $printDA_row['fname'] . " " . $final_mname);
                                    } else {
                                        echo htmlspecialchars($printDA_row['lname'] . ", " . $printDA_row['fname'] . " " . $final_mname . $printDA_row['suffix']);
                                    }
                                    ?>
                            </td>
                            
                            <td><?php echo htmlspecialchars($peb_row['degree']); ?></td>
                            <td><?php echo htmlspecialchars($peb_row['course_details']); ?></td>
                            <td><?php echo htmlspecialchars($peb_row['units']); ?></td>
                            <td><?php echo htmlspecialchars($peb_row['year_grad']); ?></td>
                            <td><?php echo htmlspecialchars($peb_row['school_name']); ?></td>
                            </tr>
                              
                            
                            
                             <?php
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching education data: " . $e->getMessage());
                                    echo '<tr><td colspan="6" style="text-align: center; color: red;">Error loading data.</td></tr>';
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