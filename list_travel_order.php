<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php'); 
   
   //include('loaderFX.php'); 
  
    $day=date("l"); //Mon-Sun
    
    if(isset($_POST['filterDate'])){
    $filterDate= date('F d, Y', strtotime($_POST['date_from'])) . ' - ' . date('F d, Y', strtotime($_POST['date_to']));
     
    }else{
        
    $filterDate=date('F 01, Y') . ' - ' . date('F t, Y');
   
    } ?>
    
   
    
    
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
 
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
      
      <?php include('add_travel_modal.php'); ?>
      
            <?php
            
            $opt_query = $conn->query("SELECT DISTINCT travel_date FROM personnel_official_travel_logs ORDER BY travel_date DESC");
 
            ?>
            
      
            <!-- TRAVEL ORDER BULLETIN    -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  
                  <form method="POST">
    <table>
    <tr>
        <td style="border: none; background-color: white;">
            <h4>
                <a style="color: white !important;" data-toggle="modal" data-target="#addTravelOrder" href="#" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></a>
                TRAVEL BULLETIN DATA TABLE
            </h4>
        </td>
        <td style="border: none; background-color: white;">
            <input type="date" name="date_from" class="form-control" value="<?php echo isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-01'); ?>">
        </td>
        <td style="border: none; background-color: white; padding-left: 10px; padding-right: 10px;">
            To
        </td>
        <td style="border: none; background-color: white;">
            <input type="date" name="date_to" class="form-control" value="<?php echo isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-t'); ?>">
        </td>
        <td style="border: none; background-color: white; padding-left: 10px;">
            <button name="filterDate" class="btn btn-primary" title="Filter Date"><i class="fa fa-filter"></i></button> 
            <a href="list_travel_order.php?cw=list_travel" class="btn btn-secondary" title="Reset"><i class="fa fa-refresh"></i></a> <button type="submit" formtarget="_blank" formaction="print_travel_report.php" class="btn btn-info" title="Print Report"><i class="fa fa-print"></i></button>
            
        </td>
    </tr>
    </table>
</form>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a>
                
                </div>
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                
                <div class="col-lg-12">
                <div class="table-responsive" style="margin-top: 12px;">
                <table id="" class="display" style="width:100%">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>TO CODE<br /><small>Travel Date</small></th>
                          <th>PERSONNEL</th>
                          <th>DETAILS</th>
                          <th></th>
                        </tr>
                      </thead>
                      
                      <tbody>
                      
                      <?php
                      $row_ctr=0;
                               
                      $date_from = isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-01');
$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-t');

$new_clearance_query = $conn->query("SELECT travel_code, travel_date, purpose, description, location, travel_type, MAX(time_out) as time_out, MAX(time_in) as time_in FROM personnel_official_travel_logs GROUP BY travel_code, travel_date, purpose, description, location, travel_type ORDER BY MAX(travel_log_id) ASC");
$filtered_travels = [];
while($nc_row = $new_clearance_query->fetch()) {
    $dates = explode(' - ', trim($nc_row['travel_date']));
    $start_date_str = trim($dates[0]);
    $end_date_str = isset($dates[1]) ? trim($dates[1]) : $start_date_str;
    
    // Attempt to convert to Y-m-d format
    $start_ts = strtotime($start_date_str);
    $end_ts = strtotime($end_date_str);
    
    if($start_ts && $end_ts) {
        $dbStartDate = date('Y-m-d', $start_ts);
        $dbEndDate = date('Y-m-d', $end_ts);
        
        // Check if the date range overlaps with the filter range
        if($dbStartDate <= $date_to && $dbEndDate >= $date_from) {
            $filtered_travels[] = $nc_row;
        }
    }
}
foreach($filtered_travels as $nc_row) {
    $row_ctr=$row_ctr+1;
                      
                      ?>
                      
                        <tr>
                          <td><?php echo $row_ctr; ?></td>
                          
                          <td>
                          <?php echo $nc_row['travel_code']; ?><br />
                          <small><?php echo $nc_row['travel_date']; ?></small>
                          </td>
                          
                          <td>
                          <?php
                          $pi_query = $conn->prepare("SELECT personnel_id FROM personnel_official_travel_logs WHERE travel_code=? AND purpose=? AND travel_date=?");
                          $pi_query->execute([$nc_row['travel_code'], $nc_row['purpose'], $nc_row['travel_date']]);
                          while($pi_row = $pi_query->fetch())
                          {
                          
                           $studData_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$pi_row[personnel_id]'");
                           $sd_row=$studData_query->fetch();
                           
                           if($sd_row) {
                               if($sd_row['suffix']=="-")
                               {
                                 echo $p_name=$sd_row['fname']." ".substr($sd_row['mname'], 0,1).". ".$sd_row['lname'];
                               
                               }else{
                                 
                                 echo $p_name=$sd_row['fname']." ".substr($sd_row['mname'], 0,1).". ".$sd_row['lname']." ".$sd_row['suffix'];
                               
                               } 
                               echo "<br />"; 
                           }
                           } ?>
                          
                          </td>
                          
                          <td>
                          <strong style="font-weight: bold;">PURPOSE:</strong> <?php echo $nc_row['purpose']; ?><br />
                          <strong style="font-weight: bold;">DESCRIPTION:</strong> <?php echo $nc_row['description']; ?><br />
                          <strong style="font-weight: bold;">LOCATION:</strong> <?php echo $nc_row['location']; ?><br />
                          <strong style="font-weight: bold;">TYPE:</strong> <?php echo $nc_row['travel_type']; ?>
                          <?php if (!empty($nc_row['time_out']) || !empty($nc_row['time_in'])) { 
                              $t_str = trim(($nc_row['time_out'] ?? '') . ' - ' . ($nc_row['time_in'] ?? ''), ' -');
                          ?>
                          <br /><strong style="font-weight: bold;">TIME:</strong> <?php echo htmlspecialchars($t_str); ?>
                          <?php } ?>
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
                          <td colspan="4">Total Files from <strong style="font-size: 20px;"><?php echo $filterDate; ?></strong></td>
                      
                          <td><strong style="font-size: 20px;"><?php echo count($filtered_travels); ?></strong></td>
                        </tr>
                      </tfoot>
                    
                    </table>
                    </div>
                    </div>
 
 
  
                </div>
              </div>
              <!-- TRAVEL ORDER BULLETIN End-->
         
              
              
                </div>
            </div>
        </div>
     </section>
     
      
      
      <?php include('print_monthly_TO_modal.php'); ?>
   
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
 
 


    
  </body>
</html>