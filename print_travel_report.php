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
        <h4>TRAVEL BULLETIN REPORT</h4>
        <p>Period: <?php echo $period_str; ?></p>
    </div>
    <table>
        <thead>
            <tr><th>Travel Code</th><th>Date(s)</th><th>Purpose</th><th>Location</th><th>Travel Type</th></tr>
        </thead>
        <tbody>
            <?php
$query = $conn->query("SELECT DISTINCT travel_code, travel_date, purpose, description, location, travel_type FROM personnel_official_travel_logs ORDER BY travel_log_id ASC");
$filtered = [];
while($r = $query->fetch()) {
    $ts = strtotime(str_replace('-', '/', trim($r['travel_date'])));
    if($ts) {
        $dbDate = date('Y-m-d', $ts);
        if($dbDate >= $date_from && $dbDate <= $date_to) $filtered[] = $r;
    }
}
foreach($filtered as $r) {
    echo "<tr>";
    echo "<td>".htmlspecialchars($r['travel_code'])."</td>";
    echo "<td>".htmlspecialchars($r['travel_date'])."</td>";
    echo "<td>".htmlspecialchars($r['purpose'])."</td>";
    echo "<td>".htmlspecialchars($r['location'])."</td>";
    echo "<td>".htmlspecialchars($r['travel_type'])."</td>";
    echo "</tr>";
}
?>
        </tbody>
    </table>
<?php include_once('universal_excel_export.php'); ?>
</body>
</html>
