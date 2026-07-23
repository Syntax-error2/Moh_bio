<!DOCTYPE html>
<html>

<?php
include('session.php');

// Sanitize and validate input parameters
$yearsFrom = isset($_GET['yearsFrom']) ? (int)$_GET['yearsFrom'] : 0;
$yearsTo = isset($_GET['yearsTo']) ? (int)$_GET['yearsTo'] : 50;
$empStat_id = isset($_GET['empStat_id']) ? (int)$_GET['empStat_id'] : 0;
$print_output = isset($_GET['print_output']) ? $_GET['print_output'] : 'All-Mixed';

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

// Function to generate personnel service records table
function generateServiceTable($conn, $sex, $yearsFrom, $yearsTo, $empStat_id, $title, $show_counter = true) {
    try {
        // Build query with JOIN to eliminate N+1 problem
        $sql = "SELECT 
            p.personnel_id, 
            p.lname, 
            p.fname, 
            p.mname, 
            p.suffix, 
            p.appointment_date, 
            p.num_of_yrs,
            d.des_name,
            o.dept_office_name,
            e.emp_stat_name
        FROM personnels p
        LEFT JOIN designation d ON p.des_id = d.des_id
        LEFT JOIN dept_offices o ON p.do_id = o.do_id
        LEFT JOIN emp_status e ON p.empStat_id = e.empStat_id
        WHERE p.num_of_yrs BETWEEN :yearsFrom AND :yearsTo
        AND (p.separation_date = '' OR p.separation_date = '  /  /    ')";
        
        $params = [
            ':yearsFrom' => $yearsFrom,
            ':yearsTo' => $yearsTo
        ];
        
        // Add sex filter if not "All-Mixed"
        if ($sex !== 'All') {
            $sql .= " AND p.sex = :sex";
            $params[':sex'] = $sex;
        }
        
        // Add employment status filter
        if ($empStat_id > 0) {
            $sql .= " AND p.empStat_id = :empStat_id";
            $params[':empStat_id'] = $empStat_id;
        }
        
        $sql .= " ORDER BY p.num_of_yrs DESC, p.lname, p.fname ASC";
        
        $query = $conn->prepare($sql);
        $query->execute($params);
        
        $list_ctr = 0;
        
        while ($row = $query->fetch()) {
            $list_ctr++;
            
            // Format middle name
            $final_mname = '';
            if (!empty($row['mname']) && $row['mname'] !== '-') {
                $final_mname = substr($row['mname'], 0, 1) . ". ";
            }
            
            // Format full name with counter
            $full_name = '';
            if ($show_counter) {
                $full_name = $list_ctr . '. ';
            }
            
            if ($row['suffix'] == "-" || empty($row['suffix'])) {
                $full_name .= $row['fname'] . " " . $final_mname . $row['lname'];
            } else {
                $full_name .= $row['fname'] . " " . $final_mname . $row['lname'] . " " . $row['suffix'];
            }
            
            // Format office and designation
            $office = $row['dept_office_name'] ?? 'N/A';
            $designation = $row['des_name'] ?? 'N/A';
            
            // Check if appointment date is valid
            $appointment_display = '';
            if (empty($row['appointment_date']) || $row['appointment_date'] == '  /  /    ') {
                $appointment_display = '<span style="color: red; font-style: italic;">Not Set</span>';
            } else {
                $appointment_display = htmlspecialchars($row['appointment_date']);
            }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($full_name); ?></td>
                <td><?php echo htmlspecialchars($office); ?></td>
                <td><?php echo htmlspecialchars($designation); ?></td>
                <td><?php echo $appointment_display; ?></td>
                <td style="text-align: center; font-weight: bold;"><?php echo htmlspecialchars($row['num_of_yrs']); ?></td>
            </tr>
            <?php
        }
        
        return $list_ctr;
        
    } catch (PDOException $e) {
        error_log("Error generating service table: " . $e->getMessage());
        echo '<tr><td colspan="5" style="text-align: center; color: red;">Error loading data. Please try again.</td></tr>';
        return 0;
    }
}

// Function to render report section
function renderReportSection($conn, $sex, $yearsFrom, $yearsTo, $empStat_id, $title) {
    echo '<div style="page-break-after: always;">';
    echo '<h4 style="margin-top: 20px; border-bottom: 2px solid #333; padding-bottom: 5px;">' . htmlspecialchars($title) . '</h4>';
    echo '<table class="table table-bordered" style="width:100%; margin-top: 10px;">';
    echo '<thead style="background-color: #f0f0f0;">
            <tr>
                <th style="width: 25%;">PERSONNEL</th>
                <th style="width: 20%;">OFFICE/DEPARTMENT</th>
                <th style="width: 20%;">DESIGNATION</th>
                <th style="width: 15%;">DATE HIRED</th>
                <th style="width: 10%; text-align: center;">NO. OF YEARS</th>
            </tr>
          </thead>
          <tbody>';
    
    $count = generateServiceTable($conn, $sex, $yearsFrom, $yearsTo, $empStat_id, $title, true);
    
    if ($count == 0) {
        echo '<tr><td colspan="5" style="text-align: center; color: #999; font-style: italic;">No personnel found for this category.</td></tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
}
?>

<body>

<script>
$(document).ready(function() {
    $('#example').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'print',
            title: '<?php echo htmlspecialchars($schoolName); ?>',
            messageTop: '<center><h3>PERSONNEL YEARS OF SERVICE</h3><h4><?php echo htmlspecialchars("Years: " . $yearsFrom . " - " . $yearsTo); ?></h4></center><hr />',
            messageBottom: '<center><?php echo htmlspecialchars($schoolName); ?> - Human Resource Management Office</center>'
        }]
    });
});
</script>

<div class="row">
    <div class="col-lg-12">
        <?php include('header_print_letterHead.php'); ?>
        
        <center>
            <h3>PERSONNEL YEARS OF SERVICE</h3>
            <h4><?php echo htmlspecialchars("Years: " . $yearsFrom . " - " . $yearsTo); ?></h4>
            <?php 
            // Display employment status filter if applied
            if ($empStat_id > 0) {
                try {
                    $status_query = $conn->prepare("SELECT emp_stat_name FROM emp_status WHERE empStat_id = :empStat_id");
                    $status_query->execute([':empStat_id' => $empStat_id]);
                    $status_row = $status_query->fetch();
                    if ($status_row) {
                        echo '<h5 style="color: #666;">Status: ' . htmlspecialchars($status_row['emp_stat_name']) . '</h5>';
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching status name: " . $e->getMessage());
                }
            }
            ?>
        </center>
        
        <?php
        // Render based on print output selection
        if ($print_output == 'Male Only') {
            renderReportSection($conn, 'Male', $yearsFrom, $yearsTo, $empStat_id, 'MALE PERSONNEL');
        } elseif ($print_output == 'Female Only') {
            renderReportSection($conn, 'Female', $yearsFrom, $yearsTo, $empStat_id, 'FEMALE PERSONNEL');
        } elseif ($print_output == 'Male-Female') {
            renderReportSection($conn, 'Male', $yearsFrom, $yearsTo, $empStat_id, 'MALE PERSONNEL');
            renderReportSection($conn, 'Female', $yearsFrom, $yearsTo, $empStat_id, 'FEMALE PERSONNEL');
        } else { // All-Mixed
            ?>
            <div class="table-responsive" style="margin-top: 12px;">
                <table id="example" class="display table table-bordered" style="width:100%">
                    <thead style="background-color: #f0f0f0;">
                        <tr>
                            <th style="width: 25%;">PERSONNEL</th>
                            <th style="width: 20%;">OFFICE/DEPARTMENT</th>
                            <th style="width: 20%;">DESIGNATION</th>
                            <th style="width: 15%;">DATE HIRED</th>
                            <th style="width: 10%; text-align: center;">NO. OF YEARS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = generateServiceTable($conn, 'All', $yearsFrom, $yearsTo, $empStat_id, 'ALL PERSONNEL', true);
                        
                        if ($count == 0) {
                            echo '<tr><td colspan="5" style="text-align: center; color: #999; font-style: italic;">No personnel found for the selected years of service range.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include('footer_print.php'); ?>
</body>
</html>
       
            