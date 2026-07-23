<!DOCTYPE html>
<html>

<?php
include('session.php');

// Sanitize and validate input parameters
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-01');
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-t');

// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
    $dateFrom = date('Y-m-01');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
    $dateTo = date('Y-m-t');
}

// Format dates for display
$dateFrom_display = date('F d, Y', strtotime($dateFrom));
$dateTo_display = date('F d, Y', strtotime($dateTo));

// Fetch institution preferences for letterhead
try {
    $inst_query = $conn->prepare("SELECT institution_name, address, region, division, logo FROM institution_preferences LIMIT 1");
    $inst_query->execute();
    $sf_row = $inst_query->fetch();
    
    if ($sf_row) {
        $schoolName = $sf_row['institution_name'];
    } else {
        $schoolName = 'Institution Name';
        $sf_row = [
            'logo' => 'default_logo.png',
            'address' => 'Address',
            'region' => '',
            'division' => ''
        ];
    }
} catch (PDOException $e) {
    error_log("Error fetching institution preferences: " . $e->getMessage());
    $schoolName = 'Institution Name';
    $sf_row = [
        'logo' => 'default_logo.png',
        'address' => 'Address',
        'region' => '',
        'division' => ''
    ];
}

include('header_print.php');
?>

<body>

<script>
$(document).ready(function() {
    $('#example').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'print',
            title: '<?php echo htmlspecialchars($schoolName); ?>',
            messageTop: '<center><h3>PERSONNEL SEMINARS ATTENDED</h3><h4><?php echo htmlspecialchars($dateFrom_display . " - " . $dateTo_display); ?></h4></center><hr />',
            messageBottom: '<center><?php echo htmlspecialchars($schoolName); ?> - Human Resource Management Office</center>'
        }]
    });
});
</script>

<div class="row">
    <div class="col-lg-12">
        <?php include('header_print_letterHead.php'); ?>
        
        <center>
            <h3>PERSONNEL SEMINARS ATTENDED</h3>
            <h4><?php echo htmlspecialchars($dateFrom_display . " - " . $dateTo_display); ?></h4>
        </center>
        
        <div class="table-responsive" style="margin-top: 12px;">
            <table id="example" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 25% !important;">PERSONNEL</th>
                        <th>TITLE</th>
                        <th>DESCRIPTION</th>
                        <th>VENUE</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        // Use JOIN to eliminate N+1 query problem
                        $sql = "SELECT 
                                    ps.seminar_title, 
                                    ps.seminar_desc, 
                                    ps.seminar_venue, 
                                    ps.event_date,
                                    p.fname,
                                    p.mname,
                                    p.lname,
                                    p.suffix
                                FROM personnel_seminars ps
                                INNER JOIN personnels p ON ps.personnel_id = p.personnel_id
                                WHERE ps.event_date BETWEEN :dateFrom AND :dateTo
                                AND (p.separation_date = '' OR p.separation_date = '  /  /    ')
                                ORDER BY ps.ps_id ASC";
                        
                        $printSeminarData_query = $conn->prepare($sql);
                        $printSeminarData_query->execute([
                            ':dateFrom' => $dateFrom,
                            ':dateTo' => $dateTo
                        ]);
                        
                        $row_count = 0;
                        while ($row = $printSeminarData_query->fetch()) {
                            $row_count++;
                            
                            // Format middle name
                            $final_mname = '';
                            if (!empty($row['mname']) && $row['mname'] !== '-') {
                                $final_mname = substr($row['mname'], 0, 1) . ". ";
                            }
                            
                            // Format full name
                            if ($row['suffix'] == "-" || empty($row['suffix'])) {
                                $full_name = $row['fname'] . " " . $final_mname . $row['lname'];
                            } else {
                                $full_name = $row['fname'] . " " . $final_mname . $row['lname'] . " " . $row['suffix'];
                            }
                            
                            // Format date
                            $event_date_formatted = date('F d, Y', strtotime($row['event_date']));
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($full_name); ?></td>
                                <td><p style="word-wrap: break-word !important;"><?php echo htmlspecialchars($row['seminar_title']); ?></p></td>
                                <td><p style="word-wrap: break-word !important;"><?php echo htmlspecialchars($row['seminar_desc']); ?></p></td>
                                <td><p style="word-wrap: break-word !important;"><?php echo htmlspecialchars($row['seminar_venue']); ?></p></td>
                                <td><?php echo htmlspecialchars($event_date_formatted); ?></td>
                            </tr>
                            <?php
                        }
                        
                        // Show message if no data found
                        if ($row_count == 0) {
                            echo '<tr><td colspan="5" style="text-align: center; color: #999;">No seminar data found for the selected date range.</td></tr>';
                        }
                        
                    } catch (PDOException $e) {
                        error_log("Error fetching seminar data: " . $e->getMessage());
                        echo '<tr><td colspan="5" style="text-align: center; color: red;">Error loading data. Please try again.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer_print.php'); ?>

</body>
</html>
       
            