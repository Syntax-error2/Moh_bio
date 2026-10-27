<?php
include('session.php');
include('dbcon.php');
include('myFunctions.php');

if (!isset($_GET['date']) || !isset($_GET['do_id'])) {
    die("Parameters missing.");
}

$dateFilter = date('Y-m-d', strtotime($_GET['date']));
$pl_filterDate = date('m/d/Y', strtotime($_GET['date']));
$dateDisplay = date('F d, Y', strtotime($_GET['date']));
$do_id = $_GET['do_id'];

// Get Office Name
$off_name_q = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :doid");
$off_name_q->execute([':doid' => $do_id]);
$officeName = $off_name_q->fetchColumn();

// Fetch Personnel and Logs
$p_q = $conn->prepare("
    SELECT p.* 
    FROM personnels p 
    LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id
    WHERE p.do_id = :doid AND es.status = 'Active'
    ORDER BY p.lname ASC
");
$p_q->execute([':doid' => $do_id]);
$personnels = $p_q->fetchAll(PDO::FETCH_ASSOC);

$b_q = $conn->prepare("SELECT * FROM bio_dtr WHERE log_date = :ld");
$b_q->execute([':ld' => $dateFilter]);
$bio_dtr = $b_q->fetchAll(PDO::FETCH_ASSOC);

$logs_by_pid = [];
foreach($bio_dtr as $b) {
    $pid = $b['personnel_id'];
    if (!isset($logs_by_pid[$pid])) {
        $logs_by_pid[$pid] = [];
    }
    $logs_by_pid[$pid][] = $b;
}

$pl_filterDate1 = date('m/d/Y', strtotime($_GET['date']));
$pl_filterDate2 = date('Y-m-d', strtotime($_GET['date']));

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
        if (isDateWithinRange($dateFilter, $t['travel_date'])) {
            $travel_by_pid[$t['personnel_id']] = $t;
        }
    }
    
    $la_stmt = $conn->query("SELECT * FROM leave_applicants WHERE applicant_id IN ($pids_in)");
    while ($l = $la_stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isDateWithinRange($dateFilter, $l['leave_date'])) {
            $leave_by_pid[$l['applicant_id']] = $l;
        }
    }
}

// Compute attendance summary and row data
$rows_data = [];
$total_personnel = count($personnels);
$total_am_present = 0;
$total_pm_present = 0;
$total_present = 0;
$total_travel = 0;
$total_leave = 0;
$total_absent = 0;
$absent_names = [];
$travel_leave_names = [];

foreach($personnels as $nc_row) { 
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
                if ($log['time_in'] < '12:00:00' && $am_in == '---') $am_in = date("h:i a", strtotime($log['time_in']));
                elseif ($log['time_in'] >= '12:00:00' && $pm_in == '---') $pm_in = date("h:i a", strtotime($log['time_in']));
            }
            if ($log['time_out'] && $log['time_out'] != '00:00:00') {
                if ($log['time_out'] < '13:00:00' && $am_out == '---') $am_out = date("h:i a", strtotime($log['time_out']));
                elseif ($log['time_out'] >= '13:00:00' && $pm_out == '---') $pm_out = date("h:i a", strtotime($log['time_out']));
            }
        }
    }
    
    if (!empty($rfid) && isset($manual_logs_by_rf[$rfid])) {
        foreach($manual_logs_by_rf[$rfid] as $ml) {
            $mlTimeStr = date("h:i a", strtotime($ml['logTime']));
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
    $is_on_travel = false;
    $is_on_leave = false;
    $is_whole_absent = false;

    // 1. Official Travel takes precedence
    if ($travel_info) {
        $is_on_travel = true;
        $status = 'ON TRAVEL';
        $remark_label = !empty($travel_info['travel_type']) && $travel_info['travel_type'] != '-' ? $travel_info['travel_type'] : 'OFFICIAL BUSINESS TRIP';
        $total_travel++;
        $travel_leave_names[] = $fullname . ' (' . $remark_label . ')';
    } 
    // 2. Approved Leave takes precedence
    elseif ($leave_info) {
        $is_on_leave = true;
        $status = 'ON LEAVE';
        $remark_label = !empty($leave_info['leave_type']) ? $leave_info['leave_type'] : 'LEAVE';
        $total_leave++;
        $travel_leave_names[] = $fullname . ' (' . $remark_label . ')';
    } 
    // 3. Travel/Leave notation in personnel logs
    elseif ($log_remark) {
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
        $travel_leave_names[] = $fullname . ' (' . $remark_label . ')';
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
        $absent_names[] = $fullname;
    }

    $rows_data[] = [
        'fullname' => $fullname,
        'am_in' => $am_in,
        'am_out' => $am_out,
        'pm_in' => $pm_in,
        'pm_out' => $pm_out,
        'has_am' => $has_am,
        'has_pm' => $has_pm,
        'is_present' => $has_scans,
        'is_on_travel' => $is_on_travel,
        'is_on_leave' => $is_on_leave,
        'is_whole_absent' => $is_whole_absent,
        'status' => $status,
        'remark_label' => $remark_label
    ];
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Office Attendance - <?php echo $officeName; ?></title>
    <link rel="shortcut icon" href="img/<?php echo $sf_row['logo'];?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
            margin: 20px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-section h3, .header-section h4 {
            margin: 5px 0;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 8px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .summary-box {
            margin: 10px auto 20px auto;
            width: 90%;
        }
        .summary-table th {
            background-color: #f1f3f5 !important;
            font-size: 13px;
            text-align: center;
        }
        .summary-table td {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
        }
        .absent-text {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .absent-row {
            background-color: #fff2f2 !important;
        }
        .travel-text {
            color: #0056b3 !important;
            font-weight: bold;
        }
        .travel-row {
            background-color: #f0f7ff !important;
        }
        .leave-text {
            color: #856404 !important;
            font-weight: bold;
        }
        .leave-row {
            background-color: #fffdf0 !important;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container-fluid">
        <?php include('header_print_letterHead.php'); ?>
        <hr />
        
        <div class="header-section" style="margin-top: 15px;">
            <h4><strong>DAILY OFFICE ATTENDANCE</strong></h4>
            <h5><?php echo $officeName; ?></h5>
            <h5>Date: <?php echo $dateDisplay; ?></h5>
        </div>

        <!-- Attendance Summary Box per Office -->
        <div class="row justify-content-center summary-box">
            <div class="col-12">
                <table class="table table-bordered summary-table">
                    <thead>
                        <tr>
                            <th>Total Active Personnel</th>
                            <th style="color: #28a745;">Total Present</th>
                            <th style="color: #007bff;">On Travel / Leave</th>
                            <th>AM Present</th>
                            <th>PM Present</th>
                            <th class="absent-text">Total Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $total_personnel; ?></td>
                            <td style="color: #28a745;"><?php echo $total_present; ?></td>
                            <td style="color: #007bff;">
                                <?php echo ($total_travel + $total_leave); ?>
                                <?php if(($total_travel + $total_leave) > 0) { ?>
                                <br><small style="font-size: 11px; font-weight: normal; color: #555;">(Travel: <?php echo $total_travel; ?> | Leave: <?php echo $total_leave; ?>)</small>
                                <?php } ?>
                            </td>
                            <td><?php echo $total_am_present; ?></td>
                            <td><?php echo $total_pm_present; ?></td>
                            <td class="absent-text" style="font-size: 18px;"><?php echo $total_absent; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Personnel Name</th>
                    <th class="text-center">AM IN</th>
                    <th class="text-center">AM OUT</th>
                    <th class="text-center">PM IN</th>
                    <th class="text-center">PM OUT</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $ctr = 1;
                foreach($rows_data as $row) { 
                    $is_absent = $row['is_whole_absent'];
                    $is_travel = $row['is_on_travel'];
                    $is_leave = $row['is_on_leave'];
                    
                    if ($is_absent) {
                        $row_class = 'class="absent-row"';
                        $display_am_in = '<strong class="absent-text">ABSENT</strong>';
                        $display_am_out = '<span class="text-muted">---</span>';
                        $display_pm_in = '<strong class="absent-text">ABSENT</strong>';
                        $display_pm_out = '<span class="text-muted">---</span>';
                    } elseif ($is_travel) {
                        $row_class = 'class="travel-row"';
                        $display_am_in = '<strong class="travel-text" style="font-size: 11px;">' . htmlspecialchars($row['remark_label']) . '</strong>';
                        $display_am_out = '<span class="text-muted">---</span>';
                        $display_pm_in = '<strong class="travel-text" style="font-size: 11px;">' . htmlspecialchars($row['remark_label']) . '</strong>';
                        $display_pm_out = '<span class="text-muted">---</span>';
                    } elseif ($is_leave) {
                        $row_class = 'class="leave-row"';
                        $display_am_in = '<strong class="leave-text" style="font-size: 11px;">' . htmlspecialchars($row['remark_label']) . '</strong>';
                        $display_am_out = '<span class="text-muted">---</span>';
                        $display_pm_in = '<strong class="leave-text" style="font-size: 11px;">' . htmlspecialchars($row['remark_label']) . '</strong>';
                        $display_pm_out = '<span class="text-muted">---</span>';
                    } else {
                        $row_class = '';
                        $display_am_in = ($row['am_in'] !== '---') ? $row['am_in'] : '<span class="text-muted">---</span>';
                        $display_am_out = ($row['am_out'] !== '---') ? $row['am_out'] : '<span class="text-muted">---</span>';
                        $display_pm_in = ($row['pm_in'] !== '---') ? $row['pm_in'] : '<span class="text-muted">---</span>';
                        $display_pm_out = ($row['pm_out'] !== '---') ? $row['pm_out'] : '<span class="text-muted">---</span>';
                    }
                ?>
                <tr <?php echo $row_class; ?>>
                    <td><?php echo $ctr++; ?></td>
                    <td>
                        <strong><?php echo $row['fullname']; ?></strong>
                        <?php if ($is_absent) { ?>
                            <span class="badge badge-danger text-danger ml-2" style="font-size: 11px; font-weight: bold;">[ABSENT]</span>
                        <?php } elseif ($is_travel) { ?>
                            <span class="badge text-primary ml-2" style="font-size: 10px; font-weight: bold;">[ON TRAVEL: <?php echo htmlspecialchars($row['remark_label']); ?>]</span>
                        <?php } elseif ($is_leave) { ?>
                            <span class="badge ml-2" style="font-size: 10px; font-weight: bold; color: #856404;">[ON LEAVE: <?php echo htmlspecialchars($row['remark_label']); ?>]</span>
                        <?php } elseif (!empty($row['remark_label'])) { ?>
                            <span class="badge text-info ml-2" style="font-size: 10px; font-weight: bold;">[<?php echo htmlspecialchars($row['remark_label']); ?>]</span>
                        <?php } ?>
                    </td>
                    <td class="text-center"><?php echo $display_am_in; ?></td>
                    <td class="text-center"><?php echo $display_am_out; ?></td>
                    <td class="text-center"><?php echo $display_pm_in; ?></td>
                    <td class="text-center"><?php echo $display_pm_out; ?></td>
                </tr>
                <?php } ?>
                
                <?php if (count($personnels) == 0) { ?>
                <tr>
                    <td colspan="6" class="text-center">No active personnel found for this office.</td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td colspan="2" class="text-right">OFFICE ATTENDANCE SUMMARY:</td>
                    <td class="text-center">AM: <?php echo $total_am_present; ?> Present</td>
                    <td class="text-center" style="color: #007bff;">Travel/Leave: <?php echo ($total_travel + $total_leave); ?></td>
                    <td class="text-center">PM: <?php echo $total_pm_present; ?> Present</td>
                    <td class="text-center absent-text"><?php echo $total_absent; ?> Absent</td>
                </tr>
            </tfoot>
        </table>

        <?php if (($total_travel + $total_leave) > 0) { ?>
        <div class="mt-2 text-primary" style="font-size: 13px;">
            <strong>Personnel on Official Travel / Leave (<?php echo ($total_travel + $total_leave); ?>):</strong> <?php echo implode('; ', $travel_leave_names); ?>
        </div>
        <?php } ?>

        <?php if ($total_absent > 0) { ?>
        <div class="mt-2 text-danger" style="font-size: 13px;">
            <strong>Absent Personnel (<?php echo $total_absent; ?>):</strong> <?php echo implode(', ', $absent_names); ?>
        </div>
        <?php } ?>

        <!-- Signatories -->
        <br><br>
        <div class="row" style="margin-top: 50px;">
            <div class="col-6">
                <p>Prepared by:</p>
                <br>
                <p style="margin-bottom: 0;">____________________________________</p>
                <p><strong>HR Officer / System Admin</strong></p>
            </div>
            <div class="col-6 text-right">
                <p>Noted by:</p>
                <br>
                <p style="margin-bottom: 0;">____________________________________</p>
                <p><strong>Department Head</strong></p>
            </div>
        </div>

    </div>

<?php include_once('universal_excel_export.php'); ?>
</body>
</html>

