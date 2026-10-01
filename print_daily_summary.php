<?php
include('session.php');
include('dbcon.php');
include('myFunctions.php');

if (!isset($_GET['date'])) {
    die("Date parameter missing.");
}

$dateFilter = date('Y-m-d', strtotime($_GET['date']));
$plFilterDate1 = date('m/d/Y', strtotime($_GET['date']));
$plFilterDate2 = date('Y-m-d', strtotime($_GET['date']));
$dateDisplay = date('F d, Y', strtotime($_GET['date']));

// Fetch the summary
$query = $conn->prepare("
    SELECT 
        d.dept_office_name, 
        COUNT(DISTINCT p.personnel_id) AS total_personnel,
        COUNT(DISTINCT CASE WHEN (b.time_in < '12:00:00' AND b.time_in != '00:00:00') OR (b.time_out < '12:00:00' AND b.time_out != '00:00:00') OR (pl.logFlow = 'AM IN' OR pl.logFlow = 'AM OUT') THEN p.personnel_id ELSE NULL END) AS am_present,
        COUNT(DISTINCT CASE WHEN (b.time_in >= '12:00:00') OR (b.time_out >= '12:00:00' AND b.time_out != '00:00:00') OR (pl.logFlow = 'PM IN' OR pl.logFlow = 'PM OUT') THEN p.personnel_id ELSE NULL END) AS pm_present,
        COUNT(DISTINCT p.personnel_id) - COUNT(DISTINCT CASE WHEN b.personnel_id IS NOT NULL OR pl.RFTag_id IS NOT NULL THEN p.personnel_id ELSE NULL END) AS absent
    FROM dept_offices d
    LEFT JOIN personnels p ON d.do_id = p.do_id
    LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id
    LEFT JOIN bio_dtr b ON p.personnel_id = b.personnel_id AND b.log_date = :dateFilter
    LEFT JOIN personnel_logs pl ON p.RFTag_id = pl.RFTag_id AND p.RFTag_id != '' AND (pl.logDate = :plFilterDate1 OR pl.logDate = :plFilterDate2)
    WHERE es.status = 'Active'
    GROUP BY d.do_id, d.dept_office_name
    ORDER BY d.dept_office_name ASC
");
$query->execute([':dateFilter' => $dateFilter, ':plFilterDate1' => $plFilterDate1, ':plFilterDate2' => $plFilterDate2]);
$summaryData = $query->fetchAll(PDO::FETCH_ASSOC);

$total_active = 0;
$total_am = 0;
$total_pm = 0;
$total_absent = 0;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Attendance Summary - <?php echo $dateDisplay; ?></title>
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
            <h4><strong>DAILY ATTENDANCE SUMMARY</strong></h4>
            <h5>Date: <?php echo $dateDisplay; ?></h5>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Department / Office</th>
                    <th class="text-center">Total Active Personnel</th>
                    <th class="text-center">AM Present</th>
                    <th class="text-center">PM Present</th>
                    <th class="text-center">Absent / No Log</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $ctr = 1;
                foreach($summaryData as $row) { 
                    $total_active += $row['total_personnel'];
                    $total_am += $row['am_present'];
                    $total_pm += $row['pm_present'];
                    $total_absent += $row['absent'];
                ?>
                <tr>
                    <td><?php echo $ctr++; ?></td>
                    <td><?php echo $row['dept_office_name']; ?></td>
                    <td class="text-center"><?php echo $row['total_personnel']; ?></td>
                    <td class="text-center"><strong><?php echo $row['am_present']; ?></strong></td>
                    <td class="text-center"><strong><?php echo $row['pm_present']; ?></strong></td>
                    <td class="text-center text-danger"><?php echo $row['absent']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td colspan="2" class="text-right">GRAND TOTAL:</td>
                    <td class="text-center"><?php echo $total_active; ?></td>
                    <td class="text-center"><?php echo $total_am; ?></td>
                    <td class="text-center"><?php echo $total_pm; ?></td>
                    <td class="text-center"><?php echo $total_absent; ?></td>
                </tr>
            </tfoot>
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

