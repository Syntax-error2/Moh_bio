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

$pl_q = $conn->prepare("SELECT * FROM personnel_logs WHERE logDate = :ld");
$pl_q->execute([':ld' => $pl_filterDate]);
$personnel_logs = $pl_q->fetchAll(PDO::FETCH_ASSOC);

$manual_logs_by_rf = [];
foreach($personnel_logs as $pl) {
    $rf = $pl['RFTag_id'];
    if (!isset($manual_logs_by_rf[$rf])) { $manual_logs_by_rf[$rf] = []; }
    $manual_logs_by_rf[$rf][] = $pl;
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
            margin-bottom: 30px;
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
                foreach($personnels as $nc_row) { 
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
                ?>
                <tr>
                    <td><?php echo $ctr++; ?></td>
                    <td><strong><?php echo $fullname; ?></strong></td>
                    <td class="text-center"><?php echo $am_in; ?></td>
                    <td class="text-center"><?php echo $am_out; ?></td>
                    <td class="text-center"><?php echo $pm_in; ?></td>
                    <td class="text-center"><?php echo $pm_out; ?></td>
                </tr>
                <?php } ?>
                
                <?php if (count($personnels) == 0) { ?>
                <tr>
                    <td colspan="6" class="text-center">No active personnel found for this office.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

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

