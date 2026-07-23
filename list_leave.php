<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   include('header.php'); 
   
   // Fetch institution preferences for print header
   $institution_name = '';
   $institution_address = '';
   $institution_region = '';
   $institution_division = '';
   $institution_logo = '';
   
   try {
       $inst_query = $conn->prepare("SELECT institution_name, address, region, division, logo FROM institution_preferences LIMIT 1");
       $inst_query->execute();
       $inst_data = $inst_query->fetch();
       
       if ($inst_data) {
           $institution_name = $inst_data['institution_name'];
           $institution_address = $inst_data['address'];
           $institution_region = $inst_data['region'];
           $institution_division = $inst_data['division'];
           $institution_logo = $inst_data['logo'];
       }
   } catch (PDOException $e) {
       error_log("Error fetching institution preferences: " . $e->getMessage());
   }
   
   // Date range filter handling
   $date_from = isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-01'); // First day of current month
   $date_to = isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-t'); // Last day of current month
   
   // Fetch leave applications statistics
   $total_applications = 0;
   $pending_applications = 0;
   $approved_applications = 0;
   $rejected_applications = 0;
   
   try {
       // Get statistics from leave_applications table
       $stats_query = $conn->prepare("SELECT 
           COUNT(*) as total,
           SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
           SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
           SUM(CASE WHEN status = 'rejected' OR status = 'disapproved' THEN 1 ELSE 0 END) as rejected
       FROM leave_applications 
       WHERE DATE(application_date) BETWEEN :date_from AND :date_to");
       
       $stats_query->execute([
           ':date_from' => $date_from,
           ':date_to' => $date_to
       ]);
       
       $stats = $stats_query->fetch();
       
       if ($stats) {
           $total_applications = $stats['total'] ?? 0;
           $pending_applications = $stats['pending'] ?? 0;
           $approved_applications = $stats['approved'] ?? 0;
           $rejected_applications = $stats['rejected'] ?? 0;
       }
   } catch (PDOException $e) {
       error_log("Error fetching leave statistics: " . $e->getMessage());
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
            <li class="breadcrumb-item active">Leave Applications Management</li>
          </ul>
        </div>
      </div>
      
      
      <!-- Summary Cards Section -->
      <section class="dashboard-counts section-padding">
        <div class="container-fluid">

          <!-- Page Header -->
          <div class="row mb-3">
            <div class="col-lg-8">
              <h2>Leave Bulletin</h2>
              <p class="text-muted">View and monitor employee leave applications</p>
            </div>
            <div class="col-lg-4 text-right">
              <!--
              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addLeave">
                <i class="fa fa-plus"></i> Add Leave Application
              </button>
  --> 
            </div>
          </div>
          
          <!-- Filter Form -->
          <div class="row mb-3">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <form method="POST" class="form-inline">
                    <label class="mr-2"><i class="fa fa-filter"></i> Filter by Date Range:</label>
                    
                    <div class="form-group mr-2">
                      <label class="mr-2">From:</label>
                      <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>" required />
                    </div>
                    
                    <div class="form-group mr-2">
                      <label class="mr-2">To:</label>
                      <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>" required />
                    </div>
                    
                    <button type="submit" name="filter_date_range" class="btn btn-primary mr-2">
                      <i class="fa fa-search"></i> Apply Filter
                    </button>
                    
                    <a href="list_leave.php" class="btn btn-secondary mr-2">
                      <i class="fa fa-refresh"></i> Reset
                    </a>
                    
                    <button type="button" class="btn btn-info" onclick="printLeaveApplicationsReport()">
                      <i class="fa fa-print"></i> Print Report
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
          
            <!-- Total Applications -->
            <div class="col-xl-3 col-md-6 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name">
                  <strong class="text-uppercase">Total Applications</strong>
                  <span>All Leave Applications</span>
                  <div class="count-number"><?php echo $total_applications; ?></div>
                </div>
              </div>
            </div>
            
            <!-- Pending Applications -->
            <div class="col-xl-3 col-md-6 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-clock"></i></div>
                <div class="name">
                  <strong class="text-uppercase">Pending</strong>
                  <span>Awaiting Approval</span>
                  <div class="count-number"><?php echo $pending_applications; ?></div>
                </div>
              </div>
            </div>
            
            <!-- Approved Applications -->
            <div class="col-xl-3 col-md-6 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-check"></i></div>
                <div class="name">
                  <strong class="text-uppercase">Approved</strong>
                  <span>Approved Leaves</span>
                  <div class="count-number"><?php echo $approved_applications; ?></div>
                </div>
              </div>
            </div>
            
            <!-- Rejected Applications -->
            <div class="col-xl-3 col-md-6 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-close"></i></div>
                <div class="name">
                  <strong class="text-uppercase">Rejected</strong>
                  <span>Disapproved Leaves</span>
                  <div class="count-number"><?php echo $rejected_applications; ?></div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </section>
      <!-- End Summary Cards Section -->
      
      
      <!-- Leave Applications Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          
          
          
          <!-- Leave Applications Data Table -->
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <div class="card">
                <div class="card-header bg-success text-white">
                  <h5 class="mb-0"><i class="fa fa-table"></i> Leave Applications List</h5>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="leaveApplicationsTable" class="display leave-table" style="width:100%">
                      <thead class="thead-dark">
                        <tr>
                          <th>Application Date</th>
                          <th>Applicant<br /><small>Department/Office</small></th>
                          <th>Leave Type</th>
                          <th>Inclusive Dates<br /><small>No. of Days</small></th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $row_ctr = 0;
                      
                      try {
                          // Fetch leave applications using prepared statements
                          $leave_query = $conn->prepare("SELECT la.*, 
                              p.lname, p.fname, p.mname, p.suffix, p.do_id,
                              d.dept_office_name
                          FROM leave_applications la
                          LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
                          LEFT JOIN dept_offices d ON p.do_id = d.do_id
                          WHERE DATE(la.application_date) BETWEEN :date_from AND :date_to
                          ORDER BY la.application_date DESC, la.id DESC");
                          
                          $leave_query->execute([
                              ':date_from' => $date_from,
                              ':date_to' => $date_to
                          ]);
                          
                          while ($leave_row = $leave_query->fetch()) {
                              $row_ctr++;
                              
                              // Format personnel name
                              $personnel_name = '';
                              if ($leave_row['suffix'] == "-" || empty($leave_row['suffix'])) {
                                  $personnel_name = $leave_row['fname'] . " " . substr($leave_row['mname'], 0, 1) . ". " . $leave_row['lname'];
                              } else {
                                  $personnel_name = $leave_row['fname'] . " " . substr($leave_row['mname'], 0, 1) . ". " . $leave_row['lname'] . " " . $leave_row['suffix'];
                              }
                              
                              // Status badge styling
                              $status = $leave_row['status'] ?? 'pending';
                              $status_class = 'secondary';
                              $status_icon = 'fa-question-circle';
                              
                              switch(strtolower($status)) {
                                  case 'approved':
                                      $status_class = 'success';
                                      $status_icon = 'fa-check-circle';
                                      break;
                                  case 'rejected':
                                  case 'disapproved':
                                      $status_class = 'danger';
                                      $status_icon = 'fa-times-circle';
                                      break;
                                  case 'pending':
                                      $status_class = 'warning';
                                      $status_icon = 'fa-clock';
                                      break;
                              }
                      ?>
                        <tr>
                          <td><?php echo date('M d, Y', strtotime($leave_row['application_date'])); ?></td>
                          <td>
                            <strong><?php echo htmlspecialchars($personnel_name); ?></strong><br />
                            <small class="text-muted"><?php echo htmlspecialchars($leave_row['dept_office_name'] ?? 'N/A'); ?></small>
                          </td>
                          <td><?php echo htmlspecialchars($leave_row['leave_type'] ?? 'N/A'); ?></td>
                          <td>
                            <?php 
                            echo date('M d, Y', strtotime($leave_row['inclusive_date_from'])) . ' - ' . date('M d, Y', strtotime($leave_row['inclusive_date_to'])); 
                            ?>
                            <br />
                            <small class="badge badge-info"><?php echo number_format($leave_row['number_of_days'] ?? 0, 2); ?> days</small>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-<?php echo $status_class; ?>">
                              <i class="fa <?php echo $status_icon; ?>"></i> <?php echo ucfirst($status); ?>
                            </span>
                          </td>
                          <td class="text-center">
                            <a href="leave_application.php?dept=<?php echo $leave_row['do_id']; ?>&personnel_id=<?php echo $leave_row['personnel_id']; ?>" 
                               class="btn btn-sm btn-info" 
                               target="_blank"
                               title="View Personnel Leave Application">
                              <i class="fa fa-external-link"></i>
                            </a>
                          </td>
                        </tr>
                        
                      <?php 
                          }
                      } catch (PDOException $e) {
                          error_log("Error fetching leave applications: " . $e->getMessage());
                          echo '<tr><td colspan="6" class="text-center text-danger">Error loading leave applications. Please try again.</td></tr>';
                      }
                      
                      if ($row_ctr == 0) {
                          echo '<tr><td colspan="6" class="text-center text-muted">No leave applications found for the selected date range.</td></tr>';
                      }
                      ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
         
        <!-- Modals -->           
        <?php 
          include('add_leave_modal.php');
        ?>
                  
      </section>
      
 
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
      <style>
        .leave-table td,
        .leave-table th {
          vertical-align: middle;
        }
      </style>
    
    <!-- DataTables and Custom Scripts -->
    <script>
      $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#leaveApplicationsTable')) {
          $('#leaveApplicationsTable').DataTable().destroy();
        }

        // Initialize DataTable with custom configuration
        var leaveTable = $('#leaveApplicationsTable').DataTable({
          "order": [[0, "desc"]], // Sort by application date descending
          "pageLength": 25,
          "responsive": true,
          "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ leave applications",
            "infoEmpty": "No leave applications found",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "zeroRecords": "No matching leave applications found"
          },
          "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
          "columnDefs": [
            { "orderable": false, "targets": 5 } // Disable sorting on action column
          ]
        });
      });

      // Print Leave Applications Report Function
      function printLeaveApplicationsReport() {
        var dateFrom = '<?php echo date('F d, Y', strtotime($date_from)); ?>';
        var dateTo = '<?php echo date('F d, Y', strtotime($date_to)); ?>';
        var totalApps = <?php echo $stats['total']; ?>;
        var pendingApps = <?php echo $stats['pending']; ?>;
        var approvedApps = <?php echo $stats['approved']; ?>;
        var rejectedApps = <?php echo $stats['rejected']; ?>;
        
        // Institution data from database
        var institutionName = '<?php echo addslashes($institution_name); ?>';
        var institutionAddress = '<?php echo addslashes($institution_address); ?>';
        var institutionRegion = 'Region <?php echo addslashes($institution_region); ?>';
        var institutionDivision = 'Division <?php echo addslashes($institution_division); ?>';
        var institutionLogo = '<?php echo addslashes($institution_logo); ?>';
        
        var printWindow = window.open('', '_blank');
        var printContent = '';
        
        printContent += '<!DOCTYPE html>';
        printContent += '<html><head>';
        printContent += '<title>Leave Applications Report</title>';
        printContent += '<style>';
        printContent += 'body { font-family: Arial, sans-serif; margin: 20px; }';
        printContent += '.header { text-align: center; margin-bottom: 30px; }';
        printContent += '.header img { width: 80px; height: 80px; margin-bottom: 10px; }';
        printContent += '.header h3 { margin: 5px 0; font-size: 14px; }';
        printContent += '.header h2 { margin: 5px 0; font-size: 18px; font-weight: bold; }';
        printContent += '.header h4 { margin: 5px 0; font-size: 12px; font-weight: normal; }';
        printContent += '.report-title { text-align: center; margin: 20px 0 10px 0; font-size: 16px; font-weight: bold; text-decoration: underline; }';
        printContent += '.period { text-align: center; margin-bottom: 20px; color: #666; font-size: 14px; }';
        printContent += '.summary { display: flex; justify-content: space-around; margin: 20px 0; }';
        printContent += '.summary-item { text-align: center; padding: 10px; border: 1px solid #333; flex: 1; margin: 0 5px; }';
        printContent += '.summary-label { font-size: 12px; color: #666; }';
        printContent += '.summary-value { font-size: 20px; font-weight: bold; margin-top: 5px; }';
        printContent += 'table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }';
        printContent += 'th, td { border: 1px solid #333; padding: 6px; text-align: left; }';
        printContent += 'th { background-color: #28a745; color: white; font-weight: bold; }';
        printContent += '.text-center { text-align: center; }';
        printContent += '@media print { body { margin: 0; } .no-print { display: none; } }';
        printContent += '</style>';
        printContent += '</head><body>';
        
        // Header with institution details
        printContent += '<div class="header">';
        if (institutionLogo) {
          printContent += '<img src="uploaded_logos/' + institutionLogo + '" alt="Logo" />';
        }
        printContent += '<h3>' + institutionRegion + '</h3>';
        printContent += '<h3>' + institutionDivision + '</h3>';
        printContent += '<h2>' + institutionName + '</h2>';
        printContent += '<h4>' + institutionAddress + '</h4>';
        printContent += '</div>';
        
        printContent += '<div class="report-title">LEAVE APPLICATIONS REPORT</div>';
        printContent += '<div class="period">Period: ' + dateFrom + ' to ' + dateTo + '</div>';
        
        // Summary section
        printContent += '<div class="summary">';
        printContent += '<div class="summary-item"><div class="summary-label">Total Applications</div><div class="summary-value">' + totalApps + '</div></div>';
        printContent += '<div class="summary-item"><div class="summary-label">Pending</div><div class="summary-value">' + pendingApps + '</div></div>';
        printContent += '<div class="summary-item"><div class="summary-label">Approved</div><div class="summary-value">' + approvedApps + '</div></div>';
        printContent += '<div class="summary-item"><div class="summary-label">Rejected</div><div class="summary-value">' + rejectedApps + '</div></div>';
        printContent += '</div>';
        
        printContent += '<table>';
        printContent += '<thead><tr>';
        printContent += '<th>Application Date</th>';
        printContent += '<th>Applicant</th>';
        printContent += '<th>Department/Office</th>';
        printContent += '<th>Leave Type</th>';
        printContent += '<th>Inclusive Dates</th>';
        printContent += '<th class="text-center">Days</th>';
        printContent += '<th class="text-center">Status</th>';
        printContent += '</tr></thead><tbody>';
        
        // Get table data
        var table = $('#leaveApplicationsTable').DataTable();
        var data = table.rows({search: 'applied'}).data();
        
        if (data.length === 0) {
          printContent += '<tr><td colspan="7" class="text-center">No leave applications found</td></tr>';
        } else {
          for (var i = 0; i < data.length; i++) {
            var row = table.row(i).node();
            var cells = $(row).find('td');
            
            printContent += '<tr>';
            printContent += '<td>' + $(cells[0]).text() + '</td>';
            
            // Applicant name (remove small tag)
            var applicantCell = $(cells[1]).clone();
            applicantCell.find('small').remove();
            printContent += '<td>' + applicantCell.text().trim() + '</td>';
            
            // Department
            var deptText = $(cells[1]).find('small').text();
            printContent += '<td>' + deptText + '</td>';
            
            printContent += '<td>' + $(cells[2]).text() + '</td>';
            
            // Dates without badge
            var datesCell = $(cells[3]).clone();
            var daysText = datesCell.find('.badge').text();
            datesCell.find('.badge').remove();
            printContent += '<td>' + datesCell.text().trim().replace(/\s+/g, ' ') + '</td>';
            
            // Days only
            printContent += '<td class="text-center">' + daysText + '</td>';
            
            // Status
            var statusText = $(cells[4]).find('.badge').text().trim();
            printContent += '<td class="text-center">' + statusText + '</td>';
            
            printContent += '</tr>';
          }
        }
        
        printContent += '</tbody></table>';
        printContent += '<br /><p style="font-size: 10px; color: #666;"><em>Generated on: ' + new Date().toLocaleString() + '</em></p>';
        printContent += '</body></html>';
        
        printWindow.document.open();
        printWindow.document.write(printContent);
        printWindow.document.close();
        
        setTimeout(function() {
          printWindow.print();
        }, 500);
      }
      
      // Set default dates on page load
      $(document).ready(function() {
        <?php if (!isset($_POST['filterDate'])): ?>
        // Auto-submit form on date change for better UX (optional)
        $('#date_from, #date_to').on('change', function() {
          // You can enable auto-submit by uncommenting the line below
          // $('#filterForm').submit();
        });
        <?php endif; ?>
      });
    </script>
    
  </body>
</html>