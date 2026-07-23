<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
    
    if(isset($_POST['dateFilter'])){
    
    // Input comes as YYYY-MM-DD from date input
    $selected_date_from = $_POST['dateFrom'];
    $selected_date_to = $_POST['dateTo'];
    
    // Keep same format for database query (YYYY-MM-DD)
    $dateFrom = $selected_date_from;
    $dateTo = $selected_date_to;
    
    }else{
        
    // Default to current month (first day to last day)
    $selected_date_from = date('Y-m-01');
    $selected_date_to = date('Y-m-t');
    
    $dateFrom = $selected_date_from;
    $dateTo = $selected_date_to;
    
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
                    <strong style="margin-left: 8px; font-size: 18px;">PERSONNEL REPORTS: SEMINARS ATTENDED</strong>
                    </td>
                    </tr>
                    </table>
                
                <!-- Filter Form -->
                <div class="row mb-3">
                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <form method="POST" class="form-inline">
                          <label class="mr-2"><i class="fa fa-filter"></i> Filter by Date Range:</label>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">From:</label>
                            <input type="date" name="dateFrom" class="form-control" value="<?php echo htmlspecialchars($selected_date_from); ?>" required />
                          </div>
                          
                          <div class="form-group mr-2">
                            <label class="mr-2">To:</label>
                            <input type="date" name="dateTo" class="form-control" value="<?php echo htmlspecialchars($selected_date_to); ?>" required />
                          </div>
                          
                          <button type="submit" name="dateFilter" class="btn btn-primary mr-2">
                            <i class="fa fa-search"></i> Apply Filter
                          </button>
                          
                          <a href="printReports_bySeminar.php?crw=SEMINAR" class="btn btn-secondary mr-2">
                            <i class="fa fa-refresh"></i> Reset
                          </a>
                          
                          <a style="color: white;" target="_blank" class="btn btn-info" href="printPersonnelSeminarData.php?dateFrom=<?php echo urlencode($dateFrom); ?>&dateTo=<?php echo urlencode($dateTo); ?>">
                            <i class="fa fa-print"></i> Print Report
                          </a>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>  
                
                
                  
                <hr />
                
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>TITLE</th>
                              <th>DESCRIPTION</th>
                              <th>VENUE</th>
                              <th>DATE</th>
                            </tr>
                          </thead>
                          <tbody> 
                                <?php
                                try {
                                    $printSeminarData_query = $conn->prepare("SELECT personnel_id, seminar_title, seminar_desc, seminar_venue, event_date FROM personnel_seminars WHERE event_date BETWEEN :dateFrom AND :dateTo ORDER BY ps_id ASC");
                                    $printSeminarData_query->execute([':dateFrom' => $dateFrom, ':dateTo' => $dateTo]);
                                    
                                    while($printSD_row=$printSeminarData_query->fetch()){  
                                    
                                    
                                    $staff_query = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
                                    $staff_query->execute([':personnel_id' => $printSD_row['personnel_id']]);
                                    $staff_row = $staff_query->fetch(); ?>
     
                                    
     
               
                            <tr>
 
                            <td>
                              <?php
                              if ($staff_row) {
                                  // Format middle name
                                  $final_mname = '';
                                  if (!empty($staff_row['mname']) && $staff_row['mname'] !== '-') {
                                      $final_mname = substr($staff_row['mname'], 0, 1) . ". ";
                                  }
                                  
                                  // Format full name
                                  if ($staff_row['suffix'] == "-" || empty($staff_row['suffix'])) {
                                      echo htmlspecialchars($staff_row['fname'] . " " . $final_mname . $staff_row['lname']);
                                  } else {
                                      echo htmlspecialchars($staff_row['fname'] . " " . $final_mname . $staff_row['lname'] . " " . $staff_row['suffix']);
                                  }
                              } else {
                                  echo 'N/A';
                              }
                              ?>
                            </td>
                            <td><?php echo htmlspecialchars($printSD_row['seminar_title']); ?></td>
                            <td><?php echo htmlspecialchars($printSD_row['seminar_desc']); ?></td>
                            <td><?php echo htmlspecialchars($printSD_row['seminar_venue']); ?></td>
                            <td><?php echo htmlspecialchars($printSD_row['event_date']); ?></td>
                            </tr> 
                             <?php 
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching seminar data: " . $e->getMessage());
                                    echo '<tr><td colspan="5" style="text-align: center; color: red;">Error loading data.</td></tr>';
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