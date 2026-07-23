<!DOCTYPE html>
<html>
<?php

include('session.php');

// Sanitize and validate input parameters
$degree = isset($_GET['degree']) ? $_GET['degree'] : 'ALL';
$school_name = isset($_GET['school_name']) ? $_GET['school_name'] : 'ALL';
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

// Function to generate education records for a personnel
function generateEducationRecords($conn, $personnel_id, $degree, $school_name) {
    try {
        $sql = "SELECT * FROM personnel_educ_bg WHERE personnel_id = :personnel_id";
        $params = [':personnel_id' => $personnel_id];
        
        // Add filters
        if ($degree !== 'ALL' && $school_name !== 'ALL') {
            $sql .= " AND degree = :degree AND school_name = :school_name";
            $params[':degree'] = $degree;
            $params[':school_name'] = $school_name;
        } elseif ($degree !== 'ALL') {
            $sql .= " AND degree = :degree";
            $params[':degree'] = $degree;
        } elseif ($school_name !== 'ALL') {
            $sql .= " AND school_name = :school_name";
            $params[':school_name'] = $school_name;
        }
        
        $sql .= " ORDER BY year_grad, school_name, degree ASC";
        
        $peb_query = $conn->prepare($sql);
        $peb_query->execute($params);
        
        while ($peb_row = $peb_query->fetch()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($peb_row['degree'] . " | " . $peb_row['course_details'] . " | " . $peb_row['units']) . '</td>';
            echo '<td>' . htmlspecialchars($peb_row['year_grad']) . '</td>';
            echo '<td>' . htmlspecialchars($peb_row['school_name']) . '</td>';
            echo '</tr>';
        }
    } catch (PDOException $e) {
        error_log("Error fetching education records: " . $e->getMessage());
        echo '<tr><td colspan="3" style="color: red;">Error loading education records.</td></tr>';
    }
}

// Function to render personnel education table
function renderPersonnelEducationTable($conn, $sex, $degree, $school_name, $title, $empStat_id) {
    global $schoolName, $sf_row;
    
    include('header_print_letterHead.php');
    
    echo '<center>';
    echo '<h3>' . strtoupper($title) . ' PERSONNELS SCHOLASTIC DATA</h3>';
    echo '<p style="font-size: medium;">Degree: ' . htmlspecialchars($degree) . ' | School: ' . htmlspecialchars($school_name) . '</p>';
    echo '</center>';
    echo '<hr />';
    
    echo '<table id="example" class="display" style="width:100%">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>PERSONNEL <br /><small>Status</small></th>';
    echo '<th>SCHOLASTIC RECORDS</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    try {
        // Build query with JOIN to eliminate N+1 problem
        $sql = "SELECT 
            p.personnel_id,
            p.lname,
            p.fname,
            p.mname,
            p.suffix,
            e.emp_stat_name
        FROM personnels p
        LEFT JOIN emp_status e ON p.empStat_id = e.empStat_id
        WHERE (p.separation_date = '' OR p.separation_date = '  /  /    ')";
        
        $params = [];
        
        if ($sex !== 'All') {
            $sql .= " AND p.sex = :sex";
            $params[':sex'] = $sex;
        }
        
        // Add employment status filter
        if ($empStat_id > 0) {
            $sql .= " AND p.empStat_id = :empStat_id";
            $params[':empStat_id'] = $empStat_id;
        }
        
        $sql .= " ORDER BY p.lname, p.fname ASC";
        
        $query = $conn->prepare($sql);
        $query->execute($params);
        
        $list_ctr = 0;
        
        while ($row = $query->fetch()) {
            $list_ctr++;
            
            // Format middle name
            $final_mname = '';
            if (!empty($row['mname']) && $row['mname'] !== '-') {
                $final_mname = substr($row['mname'], 0, 1);
            }
            
            // Format full name
            $full_name = $list_ctr . ". ";
            if ($row['suffix'] == "-" || empty($row['suffix'])) {
                $full_name .= $row['lname'] . ", " . $row['fname'] . " " . $final_mname;
            } else {
                $full_name .= $row['lname'] . ", " . $row['fname'] . " " . $row['suffix'] . " " . $final_mname;
            }
            
            echo '<tr>';
            echo '<td>';
            echo htmlspecialchars($full_name);
            echo '<br /><small>' . strtoupper(htmlspecialchars($row['emp_stat_name'] ?? 'N/A')) . '</small>';
            echo '</td>';
            echo '<td>';
            echo '<table>';
            echo '<tr>';
            echo '<th style="width: 45%;">Course Details</th>';
            echo '<th style="width: 15%;">Year Graduated</th>';
            echo '<th style="width: 40%;">School</th>';
            echo '</tr>';
            
            generateEducationRecords($conn, $row['personnel_id'], $degree, $school_name);
            
            echo '</table>';
            echo '</td>';
            echo '</tr>';
        }
        
        if ($list_ctr == 0) {
            echo '<tr><td colspan="2" style="text-align: center;">No personnel found.</td></tr>';
        }
        
    } catch (PDOException $e) {
        error_log("Error fetching personnel data: " . $e->getMessage());
        echo '<tr><td colspan="2" style="text-align: center; color: red;">Error loading data.</td></tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
}

?>
 
<body>

<!-- MALE ONLY -->
<?php if ($print_output === 'Male Only'): ?>
    <div class="row">
        <div class="col-lg-12">
            <?php renderPersonnelEducationTable($conn, 'Male', $degree, $school_name, 'MALE', $empStat_id); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

<!-- FEMALE ONLY -->
<?php if ($print_output === 'Female Only'): ?>
    <div class="row">
        <div class="col-lg-12">
            <?php renderPersonnelEducationTable($conn, 'Female', $degree, $school_name, 'FEMALE', $empStat_id); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

<!-- MALE-FEMALE (Both on separate pages) -->
<?php if ($print_output === 'Male-Female'): ?>
    <!-- Male Section -->
    <div class="row">
        <div class="col-lg-12">
            <?php renderPersonnelEducationTable($conn, 'Male', $degree, $school_name, 'MALE', $empStat_id); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
    
    <div class="pb" style="margin-top: 24px;"></div>
    
    <!-- Female Section -->
    <div class="row">
        <div class="col-lg-12">
            <?php renderPersonnelEducationTable($conn, 'Female', $degree, $school_name, 'FEMALE', $empStat_id); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

<!-- ALL-MIXED -->
<?php if ($print_output === 'All-Mixed'): ?>
    <div class="row">
        <div class="col-lg-12">
            <?php renderPersonnelEducationTable($conn, 'All', $degree, $school_name, '', $empStat_id); ?>
        </div>
    </div>
    <?php include('footer_print.php'); ?>
<?php endif; ?>

</body>
