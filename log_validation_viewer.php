<!DOCTYPE html>
<html>

    <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
   
  <?php
 
   //include('loaderFX.php'); 
  
    $day=date("l"); //Mon-Sun
    
    if(isset($_POST['filterDate'])){
    $filterDate=$_POST['reportDate'];
     
    }else{
        
    $filterDate=date('m/d/Y');
   
    }
    
    if(isset($_POST['print_daily_LV'])){ ?>
    
    <script>
    window.open('print_daily_preview_LogValidation.php?dateFrom=<?php echo $filterDate; ?>', '_blank');
    window.location='log_validation_viewer.php';
    </script>
    
    <?php } ?>

    <?php
    if(isset($_POST['print_daily_summary'])){ ?>
    
    <script>
    window.open('print_daily_summary.php?date=<?php echo $filterDate; ?>', '_blank');
    window.location='log_validation_viewer.php';
    </script>
    
    <?php } ?>
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">
    
    <?php include('navbar_header.php');
    
    if($session_access==='Administrator' || $session_access==='Admin'){ ?>
    
        <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Log Validation Viewer</li>
          </ul>
        </div>
      </div>
      
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">

            <div class="col-lg-12" style="margin-bottom: 12px;">
            <a class="btn btn-primary" style="color: white; font-weight: bold;" href="log_validation_viewer.php">ALL BIOMETRIC LOGS</a>
            <a class="btn btn-success" style="color: white; font-weight: bold;" href="log_validation_per_office.php">PER OFFICE VIEW</a>
            </div>
                   
                    
            <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  
                  <form method="POST">
                  <table>
                  <tr>
                  
                  <td style="border: none; background-color: white;">
                  <h4>LIST OF LOG VALIDATION</h4>
                  </td>
                  
                  <td style="border: none; background-color: white;">
                  <select name="reportDate" class="form-control">
                  <option><?php echo $filterDate; ?></option>
                   
                  <?php
                  $currentDate="";
                  $opt_query = $conn->query("SELECT DISTINCT DATE_FORMAT(log_date, '%m/%d/%Y') as logDate FROM bio_dtr ORDER BY log_date DESC");
                  while ($opt_row = $opt_query->fetch()) 
                  { 
                    if($filterDate==$opt_row['logDate']){
                        
                    }else{ ?>
                    
                    <option><?php echo $opt_row['logDate']; ?></option>
                    
                    <?php
                    
                    $currentDate=$opt_row['logDate'];
                    
                    } } ?>
                  </select>
                  </td>
                  
                  <td style="border: none; background-color: white;">
                  <button name="filterDate" class="btn btn-primary" title="Filter Date"><i class="fa fa-filter"></i></button>
                  <button name="print_daily_LV" class="btn btn-info" style="color: white;" title="Print daily log validation list..."><i class="fa fa-print"></i></button>
                  <button name="print_daily_summary" class="btn btn-warning" style="color: white;" title="Print daily attendance summary per office..."><i class="fa fa-file-text-o"></i></button>
                  </td>
                  </tr>
                  </table>
                  </form>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                <div class="col-lg-12">
                <ul class="nav nav-tabs" id="logTabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="am-tab" data-toggle="tab" href="#am" role="tab" aria-controls="am" aria-selected="true" style="font-weight: bold;">AM LOGS</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pm-tab" data-toggle="tab" href="#pm" role="tab" aria-controls="pm" aria-selected="false" style="font-weight: bold;">PM LOGS</a>
                  </li>
                </ul>
                <div class="tab-content" id="logTabsContent" style="margin-top: 15px;">
                  
                  <!-- AM LOGS TAB -->
                  <div class="tab-pane fade show active" id="am" role="tabpanel" aria-labelledby="am-tab">
                    <div class="table-responsive">
                    <table class="display table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th><center>IMAGE</center></th>
                          <th><center>DEPARTMENT</center></th>
                          <th>DETAILS</th>
                          <th>ACTION</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                      <?php
                      $db_filterDate = date('Y-m-d', strtotime($filterDate));
                      $pl_filterDate1 = date('m/d/Y', strtotime($filterDate)); // mm/dd/yyyy
                      $pl_filterDate2 = date('Y-m-d', strtotime($filterDate)); // YYYY-MM-DD
                               
                      $new_clearance_query = $conn->query("
                          SELECT b.*, p.lname, p.fname, p.mname, p.suffix, p.img, d.dept_office_name 
                          FROM bio_dtr b 
                          JOIN personnels p ON b.personnel_id = p.personnel_id 
                          LEFT JOIN dept_offices d ON p.do_id = d.do_id 
                          WHERE b.log_date = '$db_filterDate' 
                          ORDER BY b.time_in ASC
                      ");
                      
                      $all_logs = $new_clearance_query->fetchAll(PDO::FETCH_ASSOC);

                      $pl_query = $conn->query("
                          SELECT pl.*, p.lname, p.fname, p.mname, p.suffix, p.img, p.personnel_id, d.dept_office_name 
                          FROM personnel_logs pl 
                          JOIN personnels p ON (pl.RFTag_id = p.RFTag_id OR pl.RFTag_id = p.biometric_id) AND pl.RFTag_id != ''
                          LEFT JOIN dept_offices d ON p.do_id = d.do_id 
                          WHERE (pl.logDate = '$pl_filterDate1' OR pl.logDate = '$pl_filterDate2') AND (pl.captured_img != '' OR pl.logDate LIKE '%-%')
                          ORDER BY pl.log_id ASC
                      ");
                      $personnel_logs = $pl_query->fetchAll(PDO::FETCH_ASSOC);

                      $am_merged = [];
                      $pm_merged = [];
                      
                      // Process Bio logs
                      foreach($all_logs as $log) {
                          $pid = $log['personnel_id'];
                          $t_in = $log['time_in'];
                          $t_out = $log['time_out'];
                          
                          if (!isset($am_merged[$pid])) {
                              $am_merged[$pid] = $log;
                              $am_merged[$pid]['time_in'] = '';
                              $am_merged[$pid]['time_out'] = '';
                              $am_merged[$pid]['captured_img_in'] = '';
                              $am_merged[$pid]['captured_img_out'] = '';
                          }
                          if (!isset($pm_merged[$pid])) {
                              $pm_merged[$pid] = $log;
                              $pm_merged[$pid]['time_in'] = '';
                              $pm_merged[$pid]['time_out'] = '';
                              $pm_merged[$pid]['captured_img_in'] = '';
                              $pm_merged[$pid]['captured_img_out'] = '';
                          }
                          
                          if ($t_in && $t_in != '00:00:00') {
                              if ($t_in < '12:00:00' && empty($am_merged[$pid]['time_in'])) {
                                  $am_merged[$pid]['time_in'] = $t_in;
                              } elseif ($t_in >= '12:00:00' && empty($pm_merged[$pid]['time_in'])) {
                                  $pm_merged[$pid]['time_in'] = $t_in;
                              }
                          }
                          if ($t_out && $t_out != '00:00:00') {
                              if ($t_out < '13:00:00' && empty($am_merged[$pid]['time_out'])) {
                                  $am_merged[$pid]['time_out'] = $t_out;
                              } elseif ($t_out >= '13:00:00' && empty($pm_merged[$pid]['time_out'])) {
                                  $pm_merged[$pid]['time_out'] = $t_out;
                              }
                          }
                      }
                      
                      // Process Manual logs (override/add)
                      foreach($personnel_logs as $pl) {
                          $pid = $pl['personnel_id'];
                          $time_24 = date('H:i:s', strtotime($pl['logTime']));
                          
                          if ($pl['logFlow'] == 'AM IN' || $pl['logFlow'] == 'AM OUT') {
                              if (!isset($am_merged[$pid])) {
                                  $am_merged[$pid] = [
                                      'personnel_id' => $pl['personnel_id'],
                                      'lname' => $pl['lname'], 'fname' => $pl['fname'], 'mname' => $pl['mname'], 'suffix' => $pl['suffix'], 'img' => $pl['img'],
                                      'dept_office_name' => $pl['dept_office_name'],
                                      'log_date' => date('Y-m-d', strtotime($pl['logDate'])),
                                      'time_in' => '', 'time_out' => '',
                                      'captured_img_in' => '', 'captured_img_out' => ''
                                  ];
                              }
                              if (!isset($am_merged[$pid]['captured_img_in'])) { $am_merged[$pid]['captured_img_in'] = ''; $am_merged[$pid]['captured_img_out'] = ''; }
                              if ($pl['logFlow'] == 'AM IN') { $am_merged[$pid]['time_in'] = $time_24; $am_merged[$pid]['captured_img_in'] = $pl['captured_img']; }
                              if ($pl['logFlow'] == 'AM OUT') { $am_merged[$pid]['time_out'] = $time_24; $am_merged[$pid]['captured_img_out'] = $pl['captured_img']; }
                          }
                          if ($pl['logFlow'] == 'PM IN' || $pl['logFlow'] == 'PM OUT') {
                              if (!isset($pm_merged[$pid])) {
                                  $pm_merged[$pid] = [
                                      'personnel_id' => $pl['personnel_id'],
                                      'lname' => $pl['lname'], 'fname' => $pl['fname'], 'mname' => $pl['mname'], 'suffix' => $pl['suffix'], 'img' => $pl['img'],
                                      'dept_office_name' => $pl['dept_office_name'],
                                      'log_date' => date('Y-m-d', strtotime($pl['logDate'])),
                                      'time_in' => '', 'time_out' => '',
                                      'captured_img_in' => '', 'captured_img_out' => ''
                                  ];
                              }
                              if (!isset($pm_merged[$pid]['captured_img_in'])) { $pm_merged[$pid]['captured_img_in'] = ''; $pm_merged[$pid]['captured_img_out'] = ''; }
                              if ($pl['logFlow'] == 'PM IN') { $pm_merged[$pid]['time_in'] = $time_24; $pm_merged[$pid]['captured_img_in'] = $pl['captured_img']; }
                              if ($pl['logFlow'] == 'PM OUT') { $pm_merged[$pid]['time_out'] = $time_24; $pm_merged[$pid]['captured_img_out'] = $pl['captured_img']; }
                          }
                      }
                      
                      // Filter out empty rows
                      $am_logs = array_filter(array_values($am_merged), function($log) {
                          return !empty($log['time_in']) || !empty($log['time_out']);
                      });
                      $pm_logs = array_filter(array_values($pm_merged), function($log) {
                          return !empty($log['time_in']) || !empty($log['time_out']);
                      });
                      
                      $row_ctr=0;
                      foreach($am_logs as $nc_row){
                      $row_ctr=$row_ctr+1;
                      
                      if($nc_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            if($nc_row['suffix']=='-') { $suffix=''; }else{ $suffix=$nc_row['suffix'].' '; }
                            $finalMName=$suffix.$nc_row['mname'];
                        } ?> 
                      
                        <tr>
                          <td><?php echo $row_ctr; ?></td>
                          
                          <td>
                          <center>
                          <img src="<?php echo empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img']; ?>" width="60" height="75" class="img-fluid rounded" />
                          </center>
                          </td>
                          
                          <td>
                          <center>
                            <strong><?php echo $nc_row['dept_office_name']; ?></strong>
                          </center>
                          </td>
                          
                          <td>
                          <p><strong>Fullname: </strong><?php echo $nc_row['lname'].", ".$nc_row['fname']." ".$finalMName; ?></p>
                          <p><strong>Log Date: </strong><?php echo date('m/d/Y', strtotime($nc_row['log_date'])); ?></p>
                          <p><strong>Time In: </strong><?php echo $nc_row['time_in'] ? date('h:i:s a', strtotime($nc_row['time_in'])) : '---'; ?></p>
                          <p><strong>Time Out: </strong><?php echo ($nc_row['time_out'] && $nc_row['time_out'] != '00:00:00') ? date('h:i:s a', strtotime($nc_row['time_out'])) : '---'; ?></p>
                          </td>
                
                          <td> </td>
                        </tr>
                      <?php }?>
                      </tbody>
                    </table>
                    </div>
                  </div>
                  
                  <!-- PM LOGS TAB -->
                  <div class="tab-pane fade" id="pm" role="tabpanel" aria-labelledby="pm-tab">
                    <div class="table-responsive">
                    <table class="display table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th><center>IMAGE</center></th>
                          <th><center>DEPARTMENT</center></th>
                          <th>DETAILS</th>
                          <th>ACTION</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                      <?php
                      $row_ctr=0;
                      foreach($pm_logs as $nc_row){
                      $row_ctr=$row_ctr+1;
                      
                      if($nc_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            if($nc_row['suffix']=='-') { $suffix=''; }else{ $suffix=$nc_row['suffix'].' '; }
                            $finalMName=$suffix.$nc_row['mname'];
                        } ?> 
                      
                        <tr>
                          <td><?php echo $row_ctr; ?></td>
                          
                          <td>
                          <center>
                          <img src="<?php echo empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img']; ?>" width="60" height="75" class="img-fluid rounded" />
                          </center>
                          </td>
                          
                          <td>
                          <center>
                            <strong><?php echo $nc_row['dept_office_name']; ?></strong>
                          </center>
                          </td>
                          
                          <td>
                          <p><strong>Fullname: </strong><?php echo $nc_row['lname'].", ".$nc_row['fname']." ".$finalMName; ?></p>
                          <p><strong>Log Date: </strong><?php echo date('m/d/Y', strtotime($nc_row['log_date'])); ?></p>
                          <p><strong>Time In: </strong><?php echo $nc_row['time_in'] ? date('h:i:s a', strtotime($nc_row['time_in'])) : '---'; ?></p>
                          <p><strong>Time Out: </strong><?php echo ($nc_row['time_out'] && $nc_row['time_out'] != '00:00:00') ? date('h:i:s a', strtotime($nc_row['time_out'])) : '---'; ?></p>
                          </td>
                
                          <td> </td>
                        </tr>
                      <?php }?>
                      </tbody>
                    </table>
                    </div>
                  </div>
                  
                </div>
                      
                </div>
 
 
  
                </div>
              </div>
              <!-- kinder End-->
                </div>
            </div>
        </div>
     </section>
     
     <?php } ?>
             
  
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
 
 


    
  </body>
</html>