<!DOCTYPE html>
<html>

    <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
   
  <?php
 
    $day=date("l"); //Mon-Sun
    
    $requestedDate = $_GET['dateFrom'] ?? $_POST['reportDate'] ?? date('m/d/Y');
    $parsedDate = DateTime::createFromFormat('!m/d/Y', $requestedDate);
    $filterDate = $parsedDate && $parsedDate->format('m/d/Y') === $requestedDate
        ? $requestedDate
        : date('m/d/Y');
    $filterOffice = $_GET['do_id'] ?? $_POST['reportOffice'] ?? '';
    if ($filterOffice !== '' && !ctype_digit((string)$filterOffice)) {
        $filterOffice = '';
    }
    ?>
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
                  
                  <form method="GET" action="log_validation_per_office.php" style="width: 100%;">
                  <table style="width: 100%;">
                  <tr>
                  
                  <td style="border: none; background-color: white; width: 20%;">
                  <h4>PER OFFICE VIEW</h4>
                  </td>
                  
                  <td style="border: none; background-color: white; width: 25%;">
                  <select name="dateFrom" class="form-control">
                  <option><?php echo $filterDate; ?></option>
                   
                  <?php
                  $opt_query = $conn->query("SELECT day FROM (
                      SELECT log_date AS day FROM bio_dtr
                      UNION
                      SELECT STR_TO_DATE(logDate, '%m/%d/%Y') AS day FROM personnel_logs
                  ) AS log_days WHERE day IS NOT NULL ORDER BY day DESC");
                  while ($opt_row = $opt_query->fetch()) 
                  { 
                    $optionDate = date('m/d/Y', strtotime($opt_row['day']));
                    if($filterDate==$optionDate){
                        
                    }else{ ?>
                    <option><?php echo $optionDate; ?></option>
                    <?php
                    } } ?>
                  </select>
                  </td>

                  <td style="border: none; background-color: white; width: 35%;">
                  <select name="do_id" class="form-control" required>
                  <?php 
                  if(isset($filterOffice) && $filterOffice != '') { 
                      $off_name_q = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                      $off_name_q->execute([':do_id' => $filterOffice]);
                      $off_name = $off_name_q->fetchColumn();
                      echo '<option value="'.htmlspecialchars($filterOffice).'">'.htmlspecialchars($off_name ?: '').'</option>';
                  } else { 
                      echo "<option value=''>-- Select Office --</option>"; 
                  } 
                  
                  $opt_off_query = $conn->query("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
                  while ($off_row = $opt_off_query->fetch()) {
                      if($filterOffice != $off_row['do_id']){
                          echo "<option value='".$off_row['do_id']."'>".$off_row['dept_office_name']."</option>";
                      }
                  }
                  ?>
                  </select>
                  </td>
                  
                  <td style="border: none; background-color: white; width: 20%; text-align: right;">
                  <button type="submit" class="btn btn-primary" title="Filter"><i class="fa fa-filter"></i></button>
                  <button type="submit" formaction="print_per_office_view.php" formtarget="_blank" class="btn btn-info" style="color: white;" title="Print per office view..."><i class="fa fa-print"></i></button>
                  </td>
                  </tr>
                  </table>
                  </form>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                <div class="col-lg-12">
                
                <?php
                $am_html = '';
                $pm_html = '';
                if (isset($filterOffice) && $filterOffice != '') {
                    $db_filterDate = date('Y-m-d', strtotime($filterDate));
                    $pl_filterDate1 = date('m/d/Y', strtotime($filterDate)); // mm/dd/yyyy
                    $pl_filterDate2 = date('Y-m-d', strtotime($filterDate)); // YYYY-MM-DD
                    
                    // Fetch active personnel
                    $p_q = $conn->prepare("
                        SELECT p.* 
                        FROM personnels p
                        WHERE p.do_id = :doid
                          AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ')
                        ORDER BY p.lname ASC
                    ");
                    $p_q->execute([':doid' => $filterOffice]);
                    $personnels = $p_q->fetchAll(PDO::FETCH_ASSOC);

                    // Fetch logs for the day
                    $b_q = $conn->prepare("SELECT * FROM bio_dtr WHERE log_date = :ld");
                    $b_q->execute([':ld' => $db_filterDate]);
                    $bio_dtr = $b_q->fetchAll(PDO::FETCH_ASSOC);

                    $logs_by_pid = [];
                    foreach($bio_dtr as $b) {
                        $pid = $b['personnel_id'];
                        if (!isset($logs_by_pid[$pid])) {
                            $logs_by_pid[$pid] = [];
                        }
                        $logs_by_pid[$pid][] = $b;
                    }
                    
                    // Fetch manual logs
                    $pl_q = $conn->prepare("SELECT * FROM personnel_logs WHERE (logDate = :ld1 OR logDate = :ld2) AND (captured_img != '' OR logDate LIKE '%-%')");
                    $pl_q->execute([':ld1' => $pl_filterDate1, ':ld2' => $pl_filterDate2]);
                    $personnel_logs = $pl_q->fetchAll(PDO::FETCH_ASSOC);
                    
                    $manual_logs_by_rf = [];
                    foreach($personnel_logs as $pl) {
                        $rf = $pl['RFTag_id'];
                        if (!isset($manual_logs_by_rf[$rf])) { $manual_logs_by_rf[$rf] = []; }
                        $manual_logs_by_rf[$rf][] = $pl;
                    }
                    
                    $row_ctr = 0;
                    foreach($personnels as $nc_row) {
                        $row_ctr++;
                        $suffix = ($nc_row['suffix']=='-' || empty($nc_row['suffix'])) ? '' : ' '.$nc_row['suffix'];
                        $mname = empty($nc_row['mname']) ? '' : substr($nc_row['mname'], 0, 1).'.';
                        $fullname = $nc_row['lname'].", ".$nc_row['fname']." ".$mname.$suffix;
                        
                        $pid = $nc_row['personnel_id'];
                        $rfid = $nc_row['RFTag_id'];
                        $am_in = '---';
                        $am_out = '---';
                        $pm_in = '---';
                        $pm_out = '---';
                        
                        if (isset($logs_by_pid[$pid])) {
                            foreach($logs_by_pid[$pid] as $log) {
                                if ($log['time_in'] && $log['time_in'] != '00:00:00') {
                                    if ($log['time_in'] < '12:00:00' && $am_in == '---') $am_in = date("h:i:s a", strtotime($log['time_in']));
                                    elseif ($log['time_in'] >= '12:00:00' && $pm_in == '---') $pm_in = date("h:i:s a", strtotime($log['time_in']));
                                }
                                if ($log['time_out'] && $log['time_out'] != '00:00:00') {
                                    if ($log['time_out'] < '13:00:00' && $am_out == '---') $am_out = date("h:i:s a", strtotime($log['time_out']));
                                    elseif ($log['time_out'] >= '13:00:00' && $pm_out == '---') $pm_out = date("h:i:s a", strtotime($log['time_out']));
                                }
                            }
                        }
                        
                        $manualEntries = $manual_logs_by_rf[$rfid] ?? [];
                        $biometricId = $nc_row['biometric_id'] ?? '';
                        if ($biometricId !== '' && $biometricId !== $rfid) {
                            $manualEntries = array_merge($manualEntries, $manual_logs_by_rf[$biometricId] ?? []);
                        }
                        foreach($manualEntries as $ml) {
                                $mlTimeStr = date("h:i:s a", strtotime($ml['logTime']));
                                if ($ml['logFlow'] == 'AM IN') { $am_in = $mlTimeStr; }
                                if ($ml['logFlow'] == 'AM OUT') { $am_out = $mlTimeStr; }
                                if ($ml['logFlow'] == 'PM IN') { $pm_in = $mlTimeStr; }
                                if ($ml['logFlow'] == 'PM OUT') { $pm_out = $mlTimeStr; }
                        }
                        
                        $img_src = empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img'];
                        $common_td = '
                          <th scope="row">'.$row_ctr.'</th>
                          <td>
                            <center>
                            <img src="'.$img_src.'" width="60" height="75" class="img-fluid rounded" />
                            </center>
                          </td>
                          <td><strong>'.$fullname.'</strong></td>
                        ';
                        
                        $am_html .= '<tr>'.$common_td.'<td>'.$am_in.'</td><td>'.$am_out.'</td></tr>';
                        $pm_html .= '<tr>'.$common_td.'<td>'.$pm_in.'</td><td>'.$pm_out.'</td></tr>';
                    }
                } else {
                    $am_html = "<tr><td colspan='5' class='text-center'>Please select an office to view.</td></tr>";
                    $pm_html = "<tr><td colspan='5' class='text-center'>Please select an office to view.</td></tr>";
                }
                ?>
                
                <ul class="nav nav-tabs" id="logTabs" role="tablist" style="margin-top: 15px;">
                  <li class="nav-item">
                    <a class="nav-link active" id="am-tab" data-toggle="tab" href="#am" role="tab" aria-controls="am" aria-selected="true" style="font-weight: bold;">AM LOGS</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pm-tab" data-toggle="tab" href="#pm" role="tab" aria-controls="pm" aria-selected="false" style="font-weight: bold;">PM LOGS</a>
                  </li>
                </ul>
                <div class="tab-content" id="logTabsContent">
                  <div class="tab-pane fade show active" id="am" role="tabpanel" aria-labelledby="am-tab">
                    <div class="table-responsive" style="margin-top: 15px;">
                      <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><center>IMAGE</center></th>
                            <th>NAME</th>
                            <th>AM IN</th>
                            <th>AM OUT</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $am_html; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="pm" role="tabpanel" aria-labelledby="pm-tab">
                    <div class="table-responsive" style="margin-top: 15px;">
                      <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><center>IMAGE</center></th>
                            <th>NAME</th>
                            <th>PM IN</th>
                            <th>PM OUT</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $pm_html; ?>
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
