<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php'); 
   
   //include('loaderFX.php'); 
  
    $day=date("l"); //Mon-Sun
    
    $date_from = isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-01');
    $date_to = isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-t');

    $filterDate = isset($_POST['filterDate']) ? ($_POST['reportDate'] ?? date('m/d/Y')) : date('m/d/Y');
    ?>

    <?php
    $total_travel_orders = 0;
    $unique_travel_personnel = 0;
    try {
      $summary_stmt = $conn->prepare("SELECT COUNT(DISTINCT travel_code) AS total_travel_orders, COUNT(DISTINCT personnel_id) AS unique_travel_personnel FROM personnel_official_travel_logs WHERE DATE(SUBSTRING_INDEX(travel_date, ' - ', 1)) <= :date_to AND DATE(SUBSTRING_INDEX(travel_date, ' - ', -1)) >= :date_from");
      $summary_stmt->execute([
        ':date_from' => $date_from,
        ':date_to' => $date_to
      ]);
      $summary_row = $summary_stmt->fetch(PDO::FETCH_ASSOC);
      if ($summary_row) {
        $total_travel_orders = (int)($summary_row['total_travel_orders'] ?? 0);
        $unique_travel_personnel = (int)($summary_row['unique_travel_personnel'] ?? 0);
      }
    } catch (PDOException $e) {
      error_log("Error fetching travel summary: " . $e->getMessage());
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
                <li class="breadcrumb-item active">Employee's Travel Order Bulletin</li>
              </ul>
            </div>
          </div>      
 
      <section class="dashboard-counts section-padding">
        <div class="container-fluid">
          <div class="row mb-3">
            <div class="col-lg-8">
              <h2>Travel Order Bulletin</h2>
              <p class="text-muted">View and monitor official travel orders</p>
            </div>
            <div class="col-lg-4 text-right">
              <a title="Add travel order..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#addTravelOrder" href="#" class="btn btn-success">
                <i class="fa fa-plus"></i> Add Travel Order
              </a>
            </div>
          </div>

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
                    <button type="submit" name="filterDate" class="btn btn-primary mr-2">
                      <i class="fa fa-search"></i> Apply Filter
                    </button>
                    <a class="btn btn-secondary mr-2" href="list_travel_order.php">
                      <i class="fa fa-refresh"></i> Reset
                    </a>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#print_monthly_TO" title="Print Travel Order list...">
                      <i class="fa fa-print"></i> Print Report
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xl-6 col-md-6 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name">
                  <strong class="text-uppercase">Travel Orders</strong>
                  <span>Distinct travel codes</span>
                  <div class="count-number"><?php echo $total_travel_orders; ?></div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-6 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-user"></i></div>
                <div class="name">
                  <strong class="text-uppercase">Personnel</strong>
                  <span>Personnel on travel</span>
                  <div class="count-number"><?php echo $unique_travel_personnel; ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-lg-12 col-md-12">
              <?php include('add_travel_modal.php'); ?>

              <!-- TRAVEL ORDER BULLETIN    -->
              <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="fa fa-table"></i> Travel Order List</h5>
                </div>
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                
                <div class="card-body">
                <div class="table-responsive">
                <table id="travelOrdersTable" class="table table-striped table-bordered table-hover" style="width:100%">
                      <thead>
                        <tr>
                          <th>TO Code</th>
                          <th>Travel Dates</th>
                          <th>PERSONNEL</th>
                          <th>PURPOSE / LOCATION</th>
                          <th>TYPE</th>
                          <th></th>
                        </tr>
                      </thead>
                      
                      <tbody>
                      
                      <?php
                      $row_ctr=0;
                               
                      $new_clearance_query = $conn->prepare("SELECT DISTINCT 
                      travel_code, 
                      travel_date,
                      purpose,
                      description,
                      location,
                      travel_type  FROM personnel_official_travel_logs WHERE DATE(SUBSTRING_INDEX(travel_date, ' - ', 1)) <= :date_to AND DATE(SUBSTRING_INDEX(travel_date, ' - ', -1)) >= :date_from ORDER BY travel_log_id ASC");
                      $new_clearance_query->execute([
                        ':date_from' => $date_from,
                        ':date_to' => $date_to
                      ]);
                      while($nc_row = $new_clearance_query->fetch()){
                      $row_ctr=$row_ctr+1;
                      
                      ?>
                      
                        <tr>
                          <td>
                            <strong><?php echo htmlspecialchars($nc_row['travel_code']); ?></strong>
                          </td>
                          
                          <td>
                            <?php
                              $travel_date_parts = array_map('trim', explode(' - ', (string)$nc_row['travel_date']));
                              $travel_date_from = $travel_date_parts[0] ?? '';
                              $travel_date_to = $travel_date_parts[1] ?? $travel_date_from;
                            ?>
                            <div><strong>From:</strong> <?php echo htmlspecialchars($travel_date_from); ?></div>
                            <div><strong>To:</strong> <?php echo htmlspecialchars($travel_date_to); ?></div>
                          </td>
                          
                          <td>
                          <?php
                          $pi_stmt = $conn->prepare("SELECT personnel_id FROM personnel_official_travel_logs WHERE travel_code = :travel_code");
                          $pi_stmt->execute([':travel_code' => $nc_row['travel_code']]);
                          $pi_query = $pi_stmt;
                          $personnel_names = [];
                          while($pi_row = $pi_query->fetch()) {
                            $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
                            $studData_stmt->execute([':personnel_id' => $pi_row['personnel_id']]);
                            $studData_query = $studData_stmt;
                            $sd_row = $studData_query->fetch();

                            if ($sd_row) {
                              if ($sd_row['suffix'] == "-") {
                                $personnel_names[] = $sd_row['fname'] . " " . substr($sd_row['mname'], 0, 1) . ". " . $sd_row['lname'];
                              } else {
                                $personnel_names[] = $sd_row['fname'] . " " . substr($sd_row['mname'], 0, 1) . ". " . $sd_row['lname'] . " " . $sd_row['suffix'];
                              }
                            }
                          }
                          echo implode('<br />', array_map(function ($name) {
                            return htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                          }, $personnel_names));
                          ?>
                          
                          </td>
                          
                          <td>
                          <div><strong>Purpose:</strong> <?php echo htmlspecialchars($nc_row['purpose']); ?></div>
                          <div><strong>Location:</strong> <?php echo htmlspecialchars($nc_row['location']); ?></div>
                          </td>

                          <td>
                            <span class="badge badge-info"><?php echo htmlspecialchars($nc_row['travel_type']); ?></span>
                          </td>
                          
                          
                          <td style="width: 10px;">
                          <a title="View travel details..." style="color: white !important; margin-top: 3px;" href="list_travel_order_detailed.php?travel_code=<?php echo $nc_row['travel_code']; ?>" class="btn btn-info btn-sm">&nbsp;<i class="fa fa-info"></i>&nbsp;</a>
                          <a title="Delete data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deleteTO<?php echo $nc_row['travel_code']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          </td>
                          
                          
                        </tr>
                      
                      
                      
                       <!-- delete travel Modal -->
                          <div id="deleteTO<?php echo $nc_row['travel_code']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_travel_leave.php?travel_code=<?php echo $nc_row['travel_code']; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Travel Order</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete TO <?php echo $nc_row['travel_code']; ?>? </h4> <br />
                                
                                <strong style="font-weight: bold;">PURPOSE:</strong> <?php echo $nc_row['purpose']; ?><br />
                                <strong style="font-weight: bold;">DESCRIPTION:</strong> <?php echo $nc_row['description']; ?><br />
                                <strong style="font-weight: bold;">LOCATION:</strong> <?php echo $nc_row['location']; ?><br />
                                <strong style="font-weight: bold;">TYPE:</strong> <?php echo $nc_row['travel_type']; ?>
                               
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deleteTravel" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete travel Modal -->
                          
                      <?php }?>
                      
                      </tbody>
                      <tfoot>
                      <tr>
                          <td colspan="5">Total Travel Orders as of: <strong style="font-size: 20px;"><?php echo htmlspecialchars($date_from); ?> to <?php echo htmlspecialchars($date_to); ?></strong></td>
                      
                          <td><strong style="font-size: 20px;"><?php echo $new_clearance_query->rowCount(); ?></strong></td>
                        </tr>
                      </tfoot>
                    
                    </table>
                    </div>
                    </div>
                    </div>


                </div>
              </div>
              <!-- TRAVEL ORDER BULLETIN End-->


            </div>
        </div>
     </section>
     
      
      
      <?php include('print_monthly_TO_modal.php'); ?>
   
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
 
    <script>
      $(document).ready(function() {
        $('#travelOrdersTable').DataTable({
          "order": [[0, "desc"]],
          "pageLength": 25,
          "responsive": true,
          "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ travel orders",
            "infoEmpty": "No travel orders found",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "zeroRecords": "No matching travel orders found"
          },
          "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
          "columnDefs": [
            { "orderable": false, "targets": 5 }
          ]
        });
      });
    </script>
 


    
  </body>
</html>