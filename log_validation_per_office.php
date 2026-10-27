<!DOCTYPE html>
<html>

    <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
   
  <?php
 
    $day=date("l"); //Mon-Sun
    
    if(isset($_POST['filterDateBtn'])){
        $filterDate=$_POST['reportDate'];
        $filterOffice=$_POST['reportOffice'];
    } elseif(isset($_GET['date']) && isset($_GET['do_id'])) {
        $filterDate=$_GET['date'];
        $filterOffice=$_GET['do_id'];
    } else {
        $filterDate=date('m/d/Y');
        $filterOffice="";
    }
    
    if(isset($_POST['print_per_office'])){ 
        $printDate = $_POST['reportDate'];
        $printOffice = $_POST['reportOffice'];
        if ($printOffice != '') {
    ?>
    
    <script>
    window.open('print_per_office_view.php?date=<?php echo $printDate; ?>&do_id=<?php echo $printOffice; ?>', '_blank');
    window.location='log_validation_per_office.php';
    </script>
    
    <?php } else {
            echo "<script>alert('Please select an office to print!'); window.location='log_validation_per_office.php';</script>";
        }
    } 
    ?>
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">
    
    <?php include('navbar_header.php');
    
    if($session_access==='Administrator'){ ?>
    
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
                  
                  <form method="POST" style="width: 100%;">
                  <table style="width: 100%;">
                  <tr>
                  
                  <td style="border: none; background-color: white; width: 20%;">
                  <h4>PER OFFICE VIEW</h4>
                  </td>
                  
                  <td style="border: none; background-color: white; width: 25%;">
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

                  <td style="border: none; background-color: white; width: 35%;">
                  <select name="reportOffice" class="form-control" required>
                  <?php 
                  if(isset($filterOffice) && $filterOffice != '') { 
                      $off_name_q = $conn->query("SELECT dept_office_name FROM dept_offices WHERE do_id='$filterOffice'");
                      $off_name = $off_name_q->fetchColumn();
                      echo "<option value='$filterOffice'>$off_name</option>"; 
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
                  <button name="filterDateBtn" class="btn btn-primary" title="Filter"><i class="fa fa-filter"></i></button>
                  <button name="print_per_office" class="btn btn-info" style="color: white;" title="Print per office view..."><i class="fa fa-print"></i></button>
                  </td>
                  </tr>
                  </table>
                  </form>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                <div class="col-lg-12">
                
                <?php
                $has_office_selected = isset($filterOffice) && $filterOffice != '';
                $all_rows_html = '';
                $am_html = '';
                $pm_html = '';
                $travel_rows_html = '';
                $absent_rows_html = '';
                $total_personnel = 0;
                $total_am_present = 0;
                $total_pm_present = 0;
                $total_present = 0;
                $total_travel = 0;
                $total_leave = 0;
                $total_absent = 0;
                $absent_list = [];
                $travel_leave_list = [];

                if (!function_exists('isDateWithinRange')) {
                    function isDateWithinRange($checkDateYmd, $dateRangeStr) {
                        $dateRangeStr = trim((string)$dateRangeStr);
                        if (empty($dateRangeStr)) return false;
                        if (strpos($dateRangeStr, ' - ') !== false) {
                            $parts = explode(' - ', $dateRangeStr);
                            $sTime = strtotime(trim($parts[0]));
                            $eTime = strtotime(trim($parts[1]));
                            if ($sTime === false || $eTime === false) return false;
                            $start = date('Y-m-d', $sTime);
                            $end = date('Y-m-d', $eTime);
                            return ($checkDateYmd >= $start && $checkDateYmd <= $end);
                        } else {
                            $t = strtotime($dateRangeStr);
                            if ($t === false) return false;
                            return ($checkDateYmd === date('Y-m-d', $t));
                        }
                    }
                }

                if (!function_exists('getRemarkCategory')) {
                    function getRemarkCategory($remarkText, $code = '') {
                        $r = strtoupper(trim((string)$remarkText));
                        $c = strtoupper(trim((string)$code));
                        
                        if (
                            strpos($r, 'TRAVEL') !== false || 
                            strpos($r, 'OFFICIAL') !== false || 
                            strpos($r, 'BUSINESS') !== false || 
                            strpos($r, 'SEMINAR') !== false || 
                            strpos($r, 'TRAINING') !== false || 
                            strpos($r, 'TRIP') !== false || 
                            strpos($r, 'PASS SLIP') !== false ||
                            $r === 'OB' || $r === 'TO' ||
                            strpos($c, 'TO') === 0
                        ) {
                            return 'TRAVEL';
                        }
                        
                        if (
                            strpos($r, 'LEAVE') !== false || 
                            strpos($r, 'VACATION') !== false || 
                            strpos($r, 'SICK') !== false || 
                            strpos($r, 'FORCED') !== false || 
                            strpos($r, 'MATERNITY') !== false || 
                            strpos($r, 'PATERNITY') !== false || 
                            strpos($r, 'WELLNESS') !== false || 
                            strpos($r, 'EMERGENCY') !== false || 
                            strpos($r, 'SOLO PARENT') !== false ||
                            strpos($c, 'LV') === 0
                        ) {
                            return 'LEAVE';
                        }
                        
                        return 'OTHER';
                    }
                }

                if ($has_office_selected) {
                    $db_filterDate = date('Y-m-d', strtotime($filterDate));
                    $pl_filterDate1 = date('m/d/Y', strtotime($filterDate)); // mm/dd/yyyy
                    $pl_filterDate2 = date('Y-m-d', strtotime($filterDate)); // YYYY-MM-DD
                    
                    // Fetch active personnel
                    $p_q = $conn->prepare("
                        SELECT p.* 
                        FROM personnels p 
                        LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id
                        WHERE p.do_id = :doid AND es.status = 'Active'
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
                    
                    // Fetch manual logs and remarks
                    $pl_q = $conn->prepare("SELECT * FROM personnel_logs WHERE (logDate = :ld1 OR logDate = :ld2)");
                    $pl_q->execute([':ld1' => $pl_filterDate1, ':ld2' => $pl_filterDate2]);
                    $personnel_logs = $pl_q->fetchAll(PDO::FETCH_ASSOC);
                    
                    $manual_logs_by_rf = [];
                    $remarks_by_rf = [];
                    $remarks_by_name = [];

                    foreach($personnel_logs as $pl) {
                        $rf = $pl['RFTag_id'];
                        if (!empty($rf)) {
                            if (!empty($pl['logFlow'])) {
                                if (!isset($manual_logs_by_rf[$rf])) { $manual_logs_by_rf[$rf] = []; }
                                $manual_logs_by_rf[$rf][] = $pl;
                            }
                            if (!empty($pl['travel_leave_code']) || (!empty($pl['remarks']) && !in_array($pl['remarks'], ['Updated', 'Inserted', '24hrs', 'REST DAY']))) {
                                $remarks_by_rf[$rf] = $pl;
                            }
                        }
                        $name_key = strtoupper(trim($pl['lname']) . '|' . trim($pl['fname']));
                        if (!empty($pl['travel_leave_code']) || (!empty($pl['remarks']) && !in_array($pl['remarks'], ['Updated', 'Inserted', '24hrs', 'REST DAY']))) {
                            $remarks_by_name[$name_key] = $pl;
                        }
                    }

                    // Pre-fetch Travel Orders and Leaves for this office
                    $travel_by_pid = [];
                    $leave_by_pid = [];
                    $office_pids = array_map(function($p) { return (int)$p['personnel_id']; }, $personnels);
                    if (!empty($office_pids)) {
                        $pids_in = implode(',', $office_pids);
                        
                        $to_stmt = $conn->query("SELECT * FROM personnel_official_travel_logs WHERE personnel_id IN ($pids_in)");
                        while ($t = $to_stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isDateWithinRange($db_filterDate, $t['travel_date'])) {
                                $travel_by_pid[$t['personnel_id']] = $t;
                            }
                        }
                        
                        $la_stmt = $conn->query("SELECT * FROM leave_applicants WHERE applicant_id IN ($pids_in)");
                        while ($l = $la_stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (isDateWithinRange($db_filterDate, $l['leave_date'])) {
                                $leave_by_pid[$l['applicant_id']] = $l;
                            }
                        }
                    }
                    
                    $total_personnel = count($personnels);
                    $row_ctr = 0;
                    foreach($personnels as $nc_row) {
                        $row_ctr++;
                        $suffix = ($nc_row['suffix']=='-' || empty($nc_row['suffix'])) ? '' : ' '.$nc_row['suffix'];
                        $mname = empty($nc_row['mname']) ? '' : substr($nc_row['mname'], 0, 1).'.';
                        $fullname = $nc_row['lname'].", ".$nc_row['fname']." ".$mname.$suffix;
                        
                        $pid = $nc_row['personnel_id'];
                        $rfid = $nc_row['RFTag_id'];
                        $bioid = $nc_row['biometric_id'];
                        $name_key = strtoupper(trim($nc_row['lname']) . '|' . trim($nc_row['fname']));

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
                        
                        if (!empty($rfid) && isset($manual_logs_by_rf[$rfid])) {
                            foreach($manual_logs_by_rf[$rfid] as $ml) {
                                $mlTimeStr = date("h:i:s a", strtotime($ml['logTime']));
                                if ($ml['logFlow'] == 'AM IN') { $am_in = $mlTimeStr; }
                                if ($ml['logFlow'] == 'AM OUT') { $am_out = $mlTimeStr; }
                                if ($ml['logFlow'] == 'PM IN') { $pm_in = $mlTimeStr; }
                                if ($ml['logFlow'] == 'PM OUT') { $pm_out = $mlTimeStr; }
                            }
                        }

                        $has_am = ($am_in !== '---' || $am_out !== '---');
                        $has_pm = ($pm_in !== '---' || $pm_out !== '---');
                        $has_scans = ($has_am || $has_pm);

                        // Travel & Leave checks
                        $travel_info = $travel_by_pid[$pid] ?? null;
                        $leave_info = $leave_by_pid[$pid] ?? null;
                        $log_remark = null;
                        if (!empty($rfid) && isset($remarks_by_rf[$rfid])) {
                            $log_remark = $remarks_by_rf[$rfid];
                        } elseif (!empty($bioid) && isset($remarks_by_rf[$bioid])) {
                            $log_remark = $remarks_by_rf[$bioid];
                        } elseif (isset($remarks_by_name[$name_key])) {
                            $log_remark = $remarks_by_name[$name_key];
                        }

                        $status = 'PRESENT';
                        $remark_label = '';
                        $details = '';
                        $is_on_travel = false;
                        $is_on_leave = false;
                        $is_whole_absent = false;

                        // 1. Official Travel takes precedence
                        if ($travel_info) {
                            $is_on_travel = true;
                            $raw_type = !empty($travel_info['travel_type']) && $travel_info['travel_type'] != '-' ? $travel_info['travel_type'] : 'OFFICIAL BUSINESS TRIP';
                            if (stripos($raw_type, 'PASS SLIP') !== false || stripos($travel_info['description'] ?? '', 'PASS SLIP') !== false) {
                                $remark_label = 'PASS SLIP';
                            } else {
                                $remark_label = $raw_type;
                            }
                            $is_pass_slip = ($remark_label === 'PASS SLIP');
                            
                            $ps_time_info = '';
                            if (!empty($travel_info['time_out']) && !empty($travel_info['time_in'])) {
                                $ps_time_info = $travel_info['time_out'] . ' - ' . $travel_info['time_in'];
                            } elseif (!empty($travel_info['time_out'])) {
                                $ps_time_info = 'Out: ' . $travel_info['time_out'];
                            } elseif (!empty($travel_info['time_in'])) {
                                $ps_time_info = 'In: ' . $travel_info['time_in'];
                            }
                            
                            $status = $is_pass_slip ? ($has_scans ? 'PASS SLIP (PRESENT)' : 'PASS SLIP') : 'ON TRAVEL';
                            $details = trim(($travel_info['purpose'] ?? '') . ' ' . (!empty($travel_info['location']) ? 'at ' . $travel_info['location'] : ''));
                            if (!empty($ps_time_info)) {
                                $details = "[$ps_time_info] " . $details;
                            }
                            
                            $total_travel++;
                            if ($is_pass_slip && $has_scans) {
                                $total_present++;
                                if ($has_am) $total_am_present++;
                                if ($has_pm) $total_pm_present++;
                            }
                            
                            $travel_leave_list[] = [
                                'row_ctr' => ($total_travel + $total_leave),
                                'fullname' => $fullname,
                                'img' => empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img'],
                                'pid' => $pid,
                                'status' => $status,
                                'type' => $remark_label . (!empty($ps_time_info) ? ' ('.$ps_time_info.')' : ''),
                                'details' => $details,
                                'code' => $travel_info['travel_code'] ?? ''
                            ];
                        } 
                        // 2. Approved Leave takes precedence
                        elseif ($leave_info) {
                            $is_on_leave = true;
                            $status = 'ON LEAVE';
                            $remark_label = !empty($leave_info['leave_type']) ? $leave_info['leave_type'] : 'LEAVE';
                            $details = !empty($leave_info['leave_type_desc']) ? $leave_info['leave_type_desc'] : $leave_info['leave_type'];
                            $total_leave++;
                            $travel_leave_list[] = [
                                'row_ctr' => ($total_travel + $total_leave),
                                'fullname' => $fullname,
                                'img' => empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img'],
                                'pid' => $pid,
                                'status' => 'ON LEAVE',
                                'type' => $remark_label,
                                'details' => $details,
                                'code' => $leave_info['leave_code'] ?? ''
                            ];
                        } 
                        // 3. Travel/Leave notation in personnel logs
                        elseif ($log_remark) {
                            $rem_text = $log_remark['remarks'] ?? '';
                            $is_ps_rem = (stripos($rem_text, 'PASS SLIP') !== false);
                            if ($is_ps_rem) {
                                $is_on_travel = true;
                                $is_pass_slip = true;
                                $remark_label = $rem_text;
                                $status = $has_scans ? 'PASS SLIP (PRESENT)' : 'PASS SLIP';
                                $total_travel++;
                                if ($has_scans) {
                                    $total_present++;
                                    if ($has_am) $total_am_present++;
                                    if ($has_pm) $total_pm_present++;
                                }
                            } else {
                                $cat = getRemarkCategory($log_remark['remarks'], $log_remark['travel_leave_code']);
                                if ($cat === 'TRAVEL') {
                                    $is_on_travel = true;
                                    $status = 'ON TRAVEL';
                                    $remark_label = !empty($log_remark['remarks']) ? $log_remark['remarks'] : 'OFFICIAL TRAVEL';
                                    $total_travel++;
                                } elseif ($cat === 'LEAVE') {
                                    $is_on_leave = true;
                                    $status = 'ON LEAVE';
                                    $remark_label = !empty($log_remark['remarks']) ? $log_remark['remarks'] : 'LEAVE';
                                    $total_leave++;
                                } else {
                                    $is_on_travel = true;
                                    $status = 'ON DUTY / SPECIAL';
                                    $remark_label = $log_remark['remarks'];
                                    $total_travel++;
                                }
                            }
                            $details = $log_remark['remarks'] . (!empty($log_remark['travel_leave_code']) ? ' ['.$log_remark['travel_leave_code'].']' : '');
                            $travel_leave_list[] = [
                                'row_ctr' => ($total_travel + $total_leave),
                                'fullname' => $fullname,
                                'img' => empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img'],
                                'pid' => $pid,
                                'status' => $status,
                                'type' => $remark_label,
                                'details' => $details,
                                'code' => $log_remark['travel_leave_code'] ?? ''
                            ];
                        } 
                        // 4. Regular Present (punches found)
                        elseif ($has_scans) {
                            $total_present++;
                            if ($has_am) $total_am_present++;
                            if ($has_pm) $total_pm_present++;
                            $status = 'PRESENT';
                        } 
                        // 5. True Absent (no punches, no travel, no leave)
                        else {
                            $is_whole_absent = true;
                            $status = 'ABSENT';
                            $total_absent++;
                            $absent_list[] = [
                                'row_ctr' => $total_absent,
                                'fullname' => $fullname,
                                'img' => empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img'],
                                'pid' => $pid
                            ];
                        }
                        
                        $img_src = empty($nc_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$nc_row['img'];
                        
                        $badge_tag = '';
                        if ($is_whole_absent) {
                            $badge_tag = '<br><span class="badge badge-danger text-white mt-1" style="font-size: 11px;"><i class="fa fa-user-times mr-1"></i>ABSENT</span>';
                        } elseif (!empty($is_pass_slip)) {
                            $badge_tag = '<br><span class="badge badge-info text-white mt-1" style="font-size: 11px; background-color: #17a2b8;"><i class="fa fa-clock-o mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                        } elseif ($is_on_travel) {
                            $badge_tag = '<br><span class="badge badge-info text-white mt-1" style="font-size: 11px; background-color: #17a2b8;"><i class="fa fa-briefcase mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                        } elseif ($is_on_leave) {
                            $badge_tag = '<br><span class="badge badge-warning text-dark mt-1" style="font-size: 11px; background-color: #ffc107;"><i class="fa fa-calendar-minus-o mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                        } elseif (!empty($remark_label)) {
                            $badge_tag = '<br><span class="badge badge-light border border-info text-info mt-1" style="font-size: 10px;">'.htmlspecialchars($remark_label).'</span>';
                        }

                        $common_td = '
                          <th scope="row">'.$row_ctr.'</th>
                          <td>
                            <center>
                            <img src="'.$img_src.'" width="60" height="75" class="img-fluid rounded" />
                            </center>
                          </td>
                          <td>
                            <strong>'.$fullname.'</strong>
                            '.$badge_tag.'
                          </td>
                        ';

                        // AM tab displays
                        if ($is_whole_absent) {
                            $am_in_display = '<span class="badge badge-danger" style="font-size: 12px; padding: 5px 8px;">ABSENT</span>';
                            $am_out_display = '<span class="text-muted">---</span>';
                        } elseif ($is_on_travel) {
                            if (!empty($is_pass_slip) && $has_am) {
                                $am_in_display = $am_in;
                                $am_out_display = $am_out;
                            } elseif (!empty($is_pass_slip) && $has_scans) {
                                $am_in_display = '<span class="badge badge-info text-white" style="font-size: 11px; padding: 4px 6px; background-color: #17a2b8;"><i class="fa fa-clock-o mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                                $am_out_display = '<span class="text-muted">---</span>';
                            } else {
                                $am_in_display = '<span class="badge badge-info text-white" style="font-size: 11px; padding: 4px 6px; background-color: #17a2b8;"><i class="fa fa-briefcase mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                                $am_out_display = '<span class="text-muted">---</span>';
                            }
                        } elseif ($is_on_leave) {
                            $am_in_display = '<span class="badge badge-warning text-dark" style="font-size: 11px; padding: 4px 6px; background-color: #ffc107;"><i class="fa fa-calendar-minus-o mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                            $am_out_display = '<span class="text-muted">---</span>';
                        } elseif (!$has_am && $has_pm) {
                            $am_in_display = '<span class="badge badge-warning text-dark" style="font-size: 11px; padding: 4px 6px;">NO AM LOG</span>';
                            $am_out_display = '<span class="text-muted">---</span>';
                        } else {
                            $am_in_display = $am_in;
                            $am_out_display = $am_out;
                        }

                        // PM tab displays
                        if ($is_whole_absent) {
                            $pm_in_display = '<span class="badge badge-danger" style="font-size: 12px; padding: 5px 8px;">ABSENT</span>';
                            $pm_out_display = '<span class="text-muted">---</span>';
                        } elseif ($is_on_travel) {
                            if (!empty($is_pass_slip) && $has_pm) {
                                $pm_in_display = $pm_in;
                                $pm_out_display = $pm_out;
                            } elseif (!empty($is_pass_slip) && $has_scans) {
                                $pm_in_display = '<span class="badge badge-info text-white" style="font-size: 11px; padding: 4px 6px; background-color: #17a2b8;"><i class="fa fa-clock-o mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                                $pm_out_display = '<span class="text-muted">---</span>';
                            } else {
                                $pm_in_display = '<span class="badge badge-info text-white" style="font-size: 11px; padding: 4px 6px; background-color: #17a2b8;"><i class="fa fa-briefcase mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                                $pm_out_display = '<span class="text-muted">---</span>';
                            }
                        } elseif ($is_on_leave) {
                            $pm_in_display = '<span class="badge badge-warning text-dark" style="font-size: 11px; padding: 4px 6px; background-color: #ffc107;"><i class="fa fa-calendar-minus-o mr-1"></i>'.htmlspecialchars($remark_label).'</span>';
                            $pm_out_display = '<span class="text-muted">---</span>';
                        } elseif ($has_am && !$has_pm) {
                            $pm_in_display = '<span class="badge badge-warning text-dark" style="font-size: 11px; padding: 4px 6px;">NO PM LOG</span>';
                            $pm_out_display = '<span class="text-muted">---</span>';
                        } else {
                            $pm_in_display = $pm_in;
                            $pm_out_display = $pm_out;
                        }

                        // All logs / Overview displays
                        if ($is_whole_absent) {
                            $all_status = '<span class="badge badge-danger" style="font-size: 12px; padding: 5px 8px;">ABSENT</span>';
                            $all_row_bg = 'style="background-color: #fff2f2;"';
                            $all_am_in = '<strong style="color: #dc3545;">ABSENT</strong>';
                            $all_pm_in = '<strong style="color: #dc3545;">ABSENT</strong>';
                        } elseif ($is_on_travel) {
                            if (!empty($is_pass_slip) && $has_scans) {
                                $all_status = '<span class="badge badge-info text-white" style="font-size: 12px; padding: 5px 8px; background-color: #17a2b8;">PASS SLIP</span>';
                                $all_row_bg = 'style="background-color: #f0f7ff;"';
                                $all_am_in = ($am_in !== '---') ? $am_in : '<span class="badge badge-info text-white" style="font-size: 10px;">PASS SLIP</span>';
                                $all_pm_in = ($pm_in !== '---') ? $pm_in : '<span class="badge badge-info text-white" style="font-size: 10px;">PASS SLIP</span>';
                            } else {
                                $all_status = '<span class="badge badge-info text-white" style="font-size: 12px; padding: 5px 8px; background-color: #17a2b8;">ON TRAVEL</span>';
                                $all_row_bg = 'style="background-color: #f0f7ff;"';
                                $all_am_in = '<strong style="color: #007bff; font-size: 11px;"><i class="fa fa-briefcase mr-1"></i>'.htmlspecialchars($remark_label).'</strong>';
                                $all_pm_in = '<strong style="color: #007bff; font-size: 11px;"><i class="fa fa-briefcase mr-1"></i>'.htmlspecialchars($remark_label).'</strong>';
                            }
                        } elseif ($is_on_leave) {
                            $all_status = '<span class="badge badge-warning text-dark" style="font-size: 12px; padding: 5px 8px; background-color: #ffc107;">ON LEAVE</span>';
                            $all_row_bg = 'style="background-color: #fffdf0;"';
                            $all_am_in = '<strong style="color: #d39e00; font-size: 11px;"><i class="fa fa-calendar-minus-o mr-1"></i>'.htmlspecialchars($remark_label).'</strong>';
                            $all_pm_in = '<strong style="color: #d39e00; font-size: 11px;"><i class="fa fa-calendar-minus-o mr-1"></i>'.htmlspecialchars($remark_label).'</strong>';
                        } else {
                            $all_status = '<span class="badge badge-success" style="font-size: 12px; padding: 5px 8px;">PRESENT</span>';
                            $all_row_bg = '';
                            $all_am_in = ($am_in !== '---') ? $am_in : '<span class="text-muted">---</span>';
                            $all_pm_in = ($pm_in !== '---') ? $pm_in : '<span class="text-muted">---</span>';
                        }
                        $all_am_out = ($am_out !== '---') ? $am_out : '<span class="text-muted">---</span>';
                        $all_pm_out = ($pm_out !== '---') ? $pm_out : '<span class="text-muted">---</span>';

                        $row_bg = $is_whole_absent ? 'style="background-color: #fff2f2;"' : ($is_on_travel ? 'style="background-color: #f0f7ff;"' : ($is_on_leave ? 'style="background-color: #fffdf0;"' : ''));

                        $am_html .= '<tr '.$row_bg.'>'.$common_td.'<td class="text-center">'.$am_in_display.'</td><td class="text-center">'.$am_out_display.'</td></tr>';
                        $pm_html .= '<tr '.$row_bg.'>'.$common_td.'<td class="text-center">'.$pm_in_display.'</td><td class="text-center">'.$pm_out_display.'</td></tr>';
                        $all_rows_html .= '<tr '.$all_row_bg.'>'.$common_td.'<td class="text-center">'.$all_am_in.'</td><td class="text-center">'.$all_am_out.'</td><td class="text-center">'.$all_pm_in.'</td><td class="text-center">'.$all_pm_out.'</td><td class="text-center">'.$all_status.'</td></tr>';
                    }

                    // Travel & Leave tab content
                    if (count($travel_leave_list) > 0) {
                        foreach($travel_leave_list as $tl) {
                            $status_badge = ($tl['status'] === 'ON LEAVE') 
                                ? '<span class="badge badge-warning text-dark" style="font-size: 12px; padding: 6px 12px; background-color: #ffc107;">ON LEAVE</span>' 
                                : '<span class="badge badge-info text-white" style="font-size: 12px; padding: 6px 12px; background-color: #17a2b8;">'.$tl['status'].'</span>';
                                
                            $type_icon = ($tl['status'] === 'ON LEAVE') ? 'fa-calendar-minus-o text-warning' : 'fa-briefcase text-primary';
                            
                            $travel_rows_html .= '
                              <tr style="background-color: #f8fbff;">
                                <th scope="row">'.$tl['row_ctr'].'</th>
                                <td>
                                  <center>
                                  <img src="'.$tl['img'].'" width="60" height="75" class="img-fluid rounded" />
                                  </center>
                                </td>
                                <td>
                                  <strong>'.$tl['fullname'].'</strong><br>
                                  <small class="text-muted">Personnel ID: '.htmlspecialchars($tl['pid']).'</small>
                                </td>
                                <td>
                                  <strong style="color: #0056b3;"><i class="fa '.$type_icon.' mr-1"></i>'.htmlspecialchars($tl['type']).'</strong>
                                  '.(!empty($tl['code']) ? '<br><small class="text-muted">Ref/Code: '.htmlspecialchars($tl['code']).'</small>' : '').'
                                </td>
                                <td>'.htmlspecialchars(!empty($tl['details']) ? $tl['details'] : $tl['type']).'</td>
                                <td class="text-center">'.$status_badge.'</td>
                              </tr>
                            ';
                        }
                    } else {
                        $travel_rows_html = '
                          <tr>
                            <td colspan="6" class="text-center text-muted py-4" style="font-size: 15px;">
                              <i class="fa fa-info-circle fa-2x mb-2 text-info"></i><br>
                              No personnel were recorded on travel or leave in this office on '.htmlspecialchars($filterDate).'.
                            </td>
                          </tr>
                        ';
                    }

                    // Absent tab content
                    if ($total_absent > 0) {
                        foreach($absent_list as $ab) {
                            $absent_rows_html .= '
                              <tr style="background-color: #fff5f5;">
                                <th scope="row">'.$ab['row_ctr'].'</th>
                                <td>
                                  <center>
                                  <img src="'.$ab['img'].'" width="60" height="75" class="img-fluid rounded" />
                                  </center>
                                </td>
                                <td>
                                  <strong>'.$ab['fullname'].'</strong><br>
                                  <small class="text-muted">Personnel ID: '.htmlspecialchars($ab['pid']).'</small>
                                </td>
                                <td class="text-center"><span class="badge badge-danger" style="font-size: 12px; padding: 6px 12px;">WHOLE DAY ABSENT</span></td>
                                <td><span class="text-danger font-weight-bold"><i class="fa fa-exclamation-circle mr-1"></i> No biometric punches, official travel, or leave found on '.htmlspecialchars($filterDate).'</span></td>
                              </tr>
                            ';
                        }
                    } else {
                        $absent_rows_html = '
                          <tr>
                            <td colspan="5" class="text-center text-success py-4" style="font-size: 16px;">
                              <i class="fa fa-check-circle fa-2x mb-2 text-success"></i><br>
                              <strong>Complete Attendance! No personnel were absent in this office on '.htmlspecialchars($filterDate).'.</strong>
                            </td>
                          </tr>
                        ';
                    }
                } else {
                    $placeholder = "<tr><td colspan='8' class='text-center'>Please select an office to view.</td></tr>";
                    $all_rows_html = $placeholder;
                    $am_html = "<tr><td colspan='5' class='text-center'>Please select an office to view.</td></tr>";
                    $pm_html = "<tr><td colspan='5' class='text-center'>Please select an office to view.</td></tr>";
                    $travel_rows_html = "<tr><td colspan='6' class='text-center'>Please select an office to view.</td></tr>";
                    $absent_rows_html = "<tr><td colspan='5' class='text-center'>Please select an office to view.</td></tr>";
                }
                ?>

                <?php if ($has_office_selected) { ?>
                <!-- OFFICE ATTENDANCE SUMMARY CARDS -->
                <div class="card mb-3" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-top: 10px;">
                  <div class="card-body" style="padding: 15px 20px;">
                    <div class="row text-center">
                      <div class="col-md border-right mb-2 mb-md-0">
                        <span class="text-muted" style="font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Total Personnel</span>
                        <h3 style="margin: 6px 0 2px; color: #212529; font-weight: 800;"><?php echo $total_personnel; ?></h3>
                        <small class="text-muted">Active in Office</small>
                      </div>
                      <div class="col-md border-right mb-2 mb-md-0">
                        <span class="text-success" style="font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Present Personnel</span>
                        <h3 style="margin: 6px 0 2px; color: #28a745; font-weight: 800;"><?php echo $total_present; ?></h3>
                        <small class="text-muted">AM: <strong><?php echo $total_am_present; ?></strong> | PM: <strong><?php echo $total_pm_present; ?></strong></small>
                      </div>
                      <div class="col-md border-right mb-2 mb-md-0">
                        <span class="text-primary" style="font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">On Travel / Leave</span>
                        <h3 style="margin: 6px 0 2px; color: #007bff; font-weight: 800;"><?php echo ($total_travel + $total_leave); ?></h3>
                        <small class="text-muted">Travel: <strong><?php echo $total_travel; ?></strong> | Leave: <strong><?php echo $total_leave; ?></strong></small>
                      </div>
                      <div class="col-md border-right mb-2 mb-md-0">
                        <span class="text-danger" style="font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Absent Personnel</span>
                        <h3 style="margin: 6px 0 2px; color: #dc3545; font-weight: 800;"><?php echo $total_absent; ?></h3>
                        <small class="<?php echo ($total_absent > 0) ? 'text-danger font-weight-bold' : 'text-success font-weight-bold'; ?>">
                          <?php echo ($total_absent > 0) ? $total_absent.' without logs' : 'No unaccounted absences'; ?>
                        </small>
                      </div>
                      <div class="col-md">
                        <span class="text-info" style="font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Turnout Rate</span>
                        <h3 style="margin: 6px 0 2px; color: #17a2b8; font-weight: 800;">
                          <?php 
                          $accounted = $total_present + $total_travel + $total_leave;
                          echo ($total_personnel > 0) ? round(($accounted / $total_personnel) * 100).'%' : '0%'; 
                          ?>
                        </h3>
                        <small class="text-muted"><?php echo $accounted; ?> of <?php echo $total_personnel; ?> Accounted</small>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } ?>
                
                <ul class="nav nav-tabs" id="logTabs" role="tablist" style="margin-top: 15px;">
                  <li class="nav-item">
                    <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true" style="font-weight: bold;">
                      ALL LOGS <?php if ($has_office_selected) { ?><span class="badge badge-secondary"><?php echo $total_personnel; ?></span><?php } ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="am-tab" data-toggle="tab" href="#am" role="tab" aria-controls="am" aria-selected="false" style="font-weight: bold;">
                      AM LOGS <?php if ($has_office_selected) { ?><span class="badge badge-success"><?php echo $total_am_present; ?></span><?php } ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pm-tab" data-toggle="tab" href="#pm" role="tab" aria-controls="pm" aria-selected="false" style="font-weight: bold;">
                      PM LOGS <?php if ($has_office_selected) { ?><span class="badge badge-success"><?php echo $total_pm_present; ?></span><?php } ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-primary" id="travel-tab" data-toggle="tab" href="#travel" role="tab" aria-controls="travel" aria-selected="false" style="font-weight: bold;">
                      <i class="fa fa-plane mr-1"></i> ON TRAVEL / LEAVE <?php if ($has_office_selected) { ?><span class="badge badge-primary"><?php echo ($total_travel + $total_leave); ?></span><?php } ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-danger" id="absent-tab" data-toggle="tab" href="#absent" role="tab" aria-controls="absent" aria-selected="false" style="font-weight: bold;">
                      <i class="fa fa-user-times mr-1"></i> ABSENT <?php if ($has_office_selected) { ?><span class="badge badge-danger"><?php echo $total_absent; ?></span><?php } ?>
                    </a>
                  </li>
                </ul>

                <div class="tab-content" id="logTabsContent">
                  <!-- ALL LOGS TAB -->
                  <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive" style="margin-top: 15px;">
                      <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><center>IMAGE</center></th>
                            <th>NAME</th>
                            <th class="text-center">AM IN</th>
                            <th class="text-center">AM OUT</th>
                            <th class="text-center">PM IN</th>
                            <th class="text-center">PM OUT</th>
                            <th class="text-center">STATUS</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $all_rows_html; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- AM LOGS TAB -->
                  <div class="tab-pane fade" id="am" role="tabpanel" aria-labelledby="am-tab">
                    <div class="table-responsive" style="margin-top: 15px;">
                      <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><center>IMAGE</center></th>
                            <th>NAME</th>
                            <th class="text-center">AM IN</th>
                            <th class="text-center">AM OUT</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $am_html; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- PM LOGS TAB -->
                  <div class="tab-pane fade" id="pm" role="tabpanel" aria-labelledby="pm-tab">
                    <div class="table-responsive" style="margin-top: 15px;">
                      <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><center>IMAGE</center></th>
                            <th>NAME</th>
                            <th class="text-center">PM IN</th>
                            <th class="text-center">PM OUT</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $pm_html; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- ON TRAVEL / LEAVE TAB -->
                  <div class="tab-pane fade" id="travel" role="tabpanel" aria-labelledby="travel-tab">
                    <div class="table-responsive" style="margin-top: 15px;">
                      <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><center>IMAGE</center></th>
                            <th>NAME</th>
                            <th>TYPE</th>
                            <th>PURPOSE / PARTICULARS</th>
                            <th class="text-center">STATUS</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $travel_rows_html; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- ABSENT TAB -->
                  <div class="tab-pane fade" id="absent" role="tabpanel" aria-labelledby="absent-tab">
                    <div class="table-responsive" style="margin-top: 15px;">
                      <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><center>IMAGE</center></th>
                            <th>NAME</th>
                            <th class="text-center">ABSENCE STATUS</th>
                            <th>REMARKS</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $absent_rows_html; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <script>
                document.addEventListener("DOMContentLoaded", function() {
                  if (typeof jQuery !== 'undefined') {
                    jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                      if (jQuery.fn.dataTable) {
                        jQuery(jQuery.fn.dataTable.tables(true)).DataTable().columns.adjust();
                      }
                    });
                  }
                });
                </script>

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