<!DOCTYPE html>
<html>

<?php
include('session.php');

// Sanitize and validate input parameters
$ageFrom = isset($_GET['ageFrom']) ? (int)$_GET['ageFrom'] : 18;
$ageTo = isset($_GET['ageTo']) ? (int)$_GET['ageTo'] : 75;
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
        // Default values if no data found
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
    // Default values on error
    $schoolName = 'Institution Name';
    $sf_row = [
        'logo' => 'default_logo.png',
        'address' => 'Address',
        'region' => '',
        'division' => ''
    ];
}

include('header_print.php');

// Function to generate personnel data table
function generatePersonnelTable($conn, $sex, $ageFrom, $ageTo, $empStat_id, $title, $show_counter = true) {
    try {
        // Build query with JOIN to eliminate N+1 problem
        $sql = "SELECT 
            p.personnel_id, 
            p.lname, 
            p.fname, 
            p.mname, 
            p.suffix, 
            p.age, 
            p.bdMM, 
            p.bdDD, 
            p.bdYYYY,
            d.des_name,
            o.dept_office_name,
            e.emp_stat_name
        FROM personnels p
        LEFT JOIN designation d ON p.des_id = d.des_id
        LEFT JOIN dept_offices o ON p.do_id = o.do_id
        LEFT JOIN emp_status e ON p.empStat_id = e.empStat_id
        WHERE p.age BETWEEN :ageFrom AND :ageTo
        AND (p.separation_date = '' OR p.separation_date = '  /  /    ')";
        
        $params = [
            ':ageFrom' => $ageFrom,
            ':ageTo' => $ageTo
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
        
        $sql .= " ORDER BY p.age, p.lname, p.fname ASC";
        
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
                $full_name .= $row['lname'] . ', ' . $row['fname'] . " " . $final_mname;
            } else {
                $full_name .= $row['lname'] . " " . $row['suffix'] . ', ' . $row['fname'] . " " . $final_mname;
            }
            
            // Format birthdate
            $birthdate = $row['bdMM'] . '/' . $row['bdDD'] . '/' . $row['bdYYYY'];
            
            // Display age or setup message
            $age_display = ($row['age'] == 0) ? 'Set Up Date of Birth' : $row['age'];
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($full_name) . '</td>';
            echo '<td>' . htmlspecialchars($row['dept_office_name'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['des_name'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['emp_stat_name'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($birthdate) . '</td>';
            echo '<td>' . htmlspecialchars($age_display) . '</td>';
            echo '</tr>';
        }
        
        if ($list_ctr == 0) {
            echo '<tr><td colspan="6" style="text-align: center;">No personnel found in this age range.</td></tr>';
        }
        
    } catch (PDOException $e) {
        error_log("Error fetching personnel data: " . $e->getMessage());
        echo '<tr><td colspan="6" style="text-align: center; color: red;">Error loading data.</td></tr>';
    }
}

// Function to render report section
function renderReportSection($conn, $sex, $ageFrom, $ageTo, $empStat_id, $title, $include_header = true) {
    global $schoolName, $sf_row;
    
    if ($include_header) {
        include('header_print_letterHead.php');
    }
    
    echo '<center>';
    echo '<h3 style="font-weight: bold;">' . strtoupper($title) . ' PERSONNELS AGE with DATE OF BIRTH</h3>';
    echo '<h4>Ages ' . htmlspecialchars($ageFrom) . ' - ' . htmlspecialchars($ageTo) . '</h4>';
    echo '</center>';
    
    echo '<table style="width:99%; margin: 8px;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="width: 20%;">PERSONNEL</th>';
    echo '<th style="width: 25%;">OFFICE/DEPARTMENT</th>';
    echo '<th style="width: 25%;">DESIGNATION</th>';
    echo '<th style="width: 15%;">STATUS</th>';
    echo '<th style="width: 20%;">DATE OF BIRTH</th>';
    echo '<th style="width: 10%;">AGE</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    generatePersonnelTable($conn, $sex, $ageFrom, $ageTo, $empStat_id, $title);
    
    echo '</tbody>';
    echo '</table>';
}

?>
 
<body>
                    
<!-- MALE ONLY -->
<?php if ($print_output === 'Male Only'): ?>
    <div class="row">
        <div class="col-lg-12">
            <?php renderReportSection($conn, 'Male', $ageFrom, $ageTo, $empStat_id, 'MALE'); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

<!-- FEMALE ONLY -->
<?php if ($print_output === 'Female Only'): ?>
    <div class="row">
        <div class="col-lg-12">
            <?php renderReportSection($conn, 'Female', $ageFrom, $ageTo, $empStat_id, 'FEMALE'); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

<!-- MALE-FEMALE (Both on separate pages) -->
<?php if ($print_output === 'Male-Female'): ?>
    <!-- Male Section -->
    <div class="row">
        <div class="col-lg-12">
            <?php renderReportSection($conn, 'Male', $ageFrom, $ageTo, $empStat_id, 'MALE'); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
    
    <div class="pb" style="margin-top: 24px;"></div>
    
    <!-- Female Section -->
    <div class="row">
        <div class="col-lg-12">
            <?php renderReportSection($conn, 'Female', $ageFrom, $ageTo, $empStat_id, 'FEMALE'); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

<!-- ALL-MIXED -->
<?php if ($print_output === 'All-Mixed'): ?>
    <div class="row">
        <div class="col-lg-12">
            <?php renderReportSection($conn, 'All', $ageFrom, $ageTo, $empStat_id, ''); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

</body>
</html>
