<?php
include('dbcon.php');
include('session.php');

$date_from = isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-01');
$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-t');
$d1 = new DateTime($date_from);
$d2 = new DateTime($date_to);
$period_str = $d1->format('F d, Y') . ' - ' . $d2->format('F d, Y');

?>
<!DOCTYPE html>
<html>
<head>
    <title>REPORT</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()" style="padding:10px 20px; font-size:16px;">Print Report</button>
    </div>
    <div class="header">
        <h2>MUNICIPALITY OF BINALBAGAN</h2>
        <h3>Human Resource Management Office</h3>
        <h4>OVERTIME REQUESTS REPORT</h4>
        <p>Period: <?php echo $period_str; ?></p>
    </div>
    <table>
        <thead>
            <tr><th>Applicant</th><th>Department</th><th>Date</th><th>Purpose</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php
$query = $conn->prepare("SELECT ot.*, p.lname, p.fname, d.dept_office_name FROM personnel_overtime_requests ot LEFT JOIN personnels p ON ot.personnel_id = p.personnel_id LEFT JOIN dept_offices d ON p.do_id = d.do_id WHERE ot.ot_date BETWEEN ? AND ? ORDER BY ot.ot_date DESC");
$query->execute([$date_from, $date_to]);
while($r = $query->fetch()) {
    echo "<tr>";
    echo "<td>".htmlspecialchars($r['lname'].', '.$r['fname'])."</td>";
    echo "<td>".htmlspecialchars($r['dept_office_name'] ?? '')."</td>";
    echo "<td>".date('M d, Y', strtotime($r['ot_date']))."</td>";
    echo "<td>".htmlspecialchars($r['remarks'] ?? '')."</td>";
    echo "<td>".htmlspecialchars($r['status'])."</td>";
    echo "</tr>";
}
?>
        </tbody>
    </table>
<?php include_once('universal_excel_export.php'); ?>
</body>
</html>
