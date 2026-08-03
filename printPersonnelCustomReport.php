<?php
include('session.php');
include('header_print.php');

$group_by = isset($_POST['group_by']) ? $_POST['group_by'] : 'alphabetical';
$cols = isset($_POST['cols']) ? $_POST['cols'] : ['fullname'];

// Ensure fullname is always in cols
if (!in_array('fullname', $cols)) {
    array_unshift($cols, 'fullname');
}

// Columns mapping for Headers and SQL Selection
$colHeaders = [
    'fullname' => 'PERSONNEL',
    'sex' => 'SEX',
    'age' => 'AGE',
    'dob' => 'DATE OF BIRTH',
    'pob' => 'PLACE OF BIRTH',
    'address' => 'HOME ADDRESS',
    'contact' => 'CONTACT NO.',
    'email' => 'EMAIL',
    'civil_status' => 'CIVIL STATUS',
    'department' => 'DEPT / OFFICE',
    'designation' => 'DESIGNATION',
    'emp_status' => 'EMPLOYMENT STATUS',
    'date_hired' => 'DATE HIRED',
    'sal_grade' => 'SALARY GRADE/STEP',
    'monthly_salary' => 'MONTHLY SALARY'
];

$select_fields = [
    'p.personnel_id', 'p.lname', 'p.fname', 'p.mname', 'p.suffix', // Always needed for fullname
    'p.sex', 'p.age', 'p.bdMM', 'p.bdDD', 'p.bdYYYY', 'p.birth_place',
    'p.address', 'p.personal_pnum', 'p.email', 'p.marital_status',
    'p.appointment_date', 'p.sal_grade', 'p.sal_step', 'p.monthly_salary',
    'do.dept_office_name', 'des.des_name', 'es.emp_stat_name'
];

// Helper to render table header
function renderTableHeader($cols, $colHeaders) {
    echo '<thead><tr>';
    foreach ($cols as $col) {
        if (isset($colHeaders[$col])) {
            echo '<th>' . htmlspecialchars($colHeaders[$col]) . '</th>';
        }
    }
    echo '</tr></thead>';
}

// Helper to render table row
function renderTableRow($row, $cols) {
    echo '<tr>';
    foreach ($cols as $col) {
        echo '<td style="vertical-align: middle; padding: 4px;">';
        switch ($col) {
            case 'fullname':
                $name = $row['fname'] . " ";
                if ($row['mname'] && $row['mname'] !== '-') $name .= substr($row['mname'], 0, 1) . ". ";
                $name .= $row['lname'];
                if ($row['suffix'] && $row['suffix'] !== '-') $name .= " " . $row['suffix'];
                echo htmlspecialchars($name);
                break;
            case 'sex':
                echo htmlspecialchars($row['sex'] ?? '');
                break;
            case 'age':
                echo htmlspecialchars($row['age'] ?? '');
                break;
            case 'dob':
                if (!empty($row['bdMM']) && !empty($row['bdDD']) && !empty($row['bdYYYY'])) {
                    echo htmlspecialchars($row['bdMM'] . '/' . $row['bdDD'] . '/' . $row['bdYYYY']);
                }
                break;
            case 'pob':
                echo htmlspecialchars($row['birth_place'] ?? '');
                break;
            case 'address':
                echo htmlspecialchars($row['address'] ?? '');
                break;
            case 'contact':
                echo htmlspecialchars($row['personal_pnum'] ?? '');
                break;
            case 'email':
                echo htmlspecialchars($row['email'] ?? '');
                break;
            case 'civil_status':
                echo htmlspecialchars($row['marital_status'] ?? '');
                break;
            case 'department':
                echo htmlspecialchars($row['dept_office_name'] ?? '');
                break;
            case 'designation':
                echo htmlspecialchars($row['des_name'] ?? '');
                break;
            case 'emp_status':
                echo htmlspecialchars($row['emp_stat_name'] ?? '');
                break;
            case 'date_hired':
                echo htmlspecialchars($row['appointment_date'] ?? '');
                break;
            case 'sal_grade':
                if (!empty($row['sal_grade']) || !empty($row['sal_step'])) {
                    echo htmlspecialchars(($row['sal_grade'] ?? '') . ' / ' . ($row['sal_step'] ?? ''));
                }
                break;
            case 'monthly_salary':
                if (!empty($row['monthly_salary'])) {
                    echo htmlspecialchars('₱' . number_format((float)$row['monthly_salary'], 2));
                }
                break;
        }
        echo '</td>';
    }
    echo '</tr>';
}

// Prepare the base query with joins
$base_query = "SELECT " . implode(", ", $select_fields) . " 
               FROM personnels p
               LEFT JOIN dept_offices do ON p.do_id = do.do_id
               LEFT JOIN designation des ON p.des_id = des.des_id
               LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id 
               WHERE (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ')";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Custom Personnel Report</title>
    <style>
        .custom-report-table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 30px;}
        .custom-report-table th, .custom-report-table td { border: 1px solid #dee2e6; padding: 6px; text-align: left; font-size: 13px; }
        .custom-report-table th { background-color: #f8f9fa; font-weight: bold; }
        .group-header { margin-top: 30px; margin-bottom: 10px; font-size: 16px; font-weight: bold; background: #e9ecef; padding: 6px 12px; border-radius: 4px; border-left: 5px solid #1a4d2e; }
    </style>
</head>
<body>

<table style="width: 100%;">
<tr>
<td align="left" style="width: 100%; border: none;">
<?php include('header_print_letterHead.php'); ?>
</td>
</tr>
</table>

<hr />
<center>
    <h3>CUSTOM PERSONNEL REPORT</h3>
    <h4>AS OF <?php echo date('m/d/Y'); ?></h4>
</center>
<hr />

<div class="col-lg-12" style="margin: 20px auto;">
<?php
try {
    if ($group_by === 'alphabetical') {
        echo '<div class="group-header">ALL PERSONNEL (ALPHABETICAL)</div>';
        echo '<table class="custom-report-table">';
        renderTableHeader($cols, $colHeaders);
        echo '<tbody>';
        $stmt = $conn->prepare($base_query . " ORDER BY p.lname ASC, p.fname ASC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            renderTableRow($row, $cols);
        }
        echo '</tbody></table>';

    } elseif ($group_by === 'male_only') {
        echo '<div class="group-header">MALE PERSONNEL ONLY</div>';
        echo '<table class="custom-report-table">';
        renderTableHeader($cols, $colHeaders);
        echo '<tbody>';
        $stmt = $conn->prepare($base_query . " AND p.sex = 'Male' ORDER BY p.lname ASC, p.fname ASC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            renderTableRow($row, $cols);
        }
        echo '</tbody></table>';

    } elseif ($group_by === 'female_only') {
        echo '<div class="group-header">FEMALE PERSONNEL ONLY</div>';
        echo '<table class="custom-report-table">';
        renderTableHeader($cols, $colHeaders);
        echo '<tbody>';
        $stmt = $conn->prepare($base_query . " AND p.sex = 'Female' ORDER BY p.lname ASC, p.fname ASC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            renderTableRow($row, $cols);
        }
        echo '</tbody></table>';

    } elseif ($group_by === 'department') {
        $dept_stmt = $conn->query("SELECT do_id, dept_office_name FROM dept_offices ORDER BY dept_office_name ASC");
        while ($dept = $dept_stmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt = $conn->prepare($base_query . " AND p.do_id = :do_id ORDER BY p.lname ASC, p.fname ASC");
            $stmt->execute([':do_id' => $dept['do_id']]);
            
            if ($stmt->rowCount() > 0) {
                echo '<div class="group-header">DEPARTMENT: ' . strtoupper(htmlspecialchars($dept['dept_office_name'])) . ' (' . $stmt->rowCount() . ')</div>';
                echo '<table class="custom-report-table">';
                renderTableHeader($cols, $colHeaders);
                echo '<tbody>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    renderTableRow($row, $cols);
                }
                echo '</tbody></table>';
            }
        }
        
        // Unassigned
        $stmt = $conn->prepare($base_query . " AND (p.do_id IS NULL OR p.do_id = 0) ORDER BY p.lname ASC, p.fname ASC");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo '<div class="group-header">DEPARTMENT: UNASSIGNED (' . $stmt->rowCount() . ')</div>';
            echo '<table class="custom-report-table">';
            renderTableHeader($cols, $colHeaders);
            echo '<tbody>';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                renderTableRow($row, $cols);
            }
            echo '</tbody></table>';
        }

    } elseif ($group_by === 'employment_status') {
        $stat_stmt = $conn->query("SELECT empStat_id, emp_stat_name FROM emp_status WHERE status='Active' ORDER BY emp_stat_name ASC");
        while ($stat = $stat_stmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt = $conn->prepare($base_query . " AND p.empStat_id = :stat_id ORDER BY p.lname ASC, p.fname ASC");
            $stmt->execute([':stat_id' => $stat['empStat_id']]);
            
            if ($stmt->rowCount() > 0) {
                echo '<div class="group-header">EMPLOYMENT STATUS: ' . strtoupper(htmlspecialchars($stat['emp_stat_name'])) . ' (' . $stmt->rowCount() . ')</div>';
                echo '<table class="custom-report-table">';
                renderTableHeader($cols, $colHeaders);
                echo '<tbody>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    renderTableRow($row, $cols);
                }
                echo '</tbody></table>';
            }
        }
        
        // Unassigned
        $stmt = $conn->prepare($base_query . " AND (p.empStat_id IS NULL OR p.empStat_id = 0) ORDER BY p.lname ASC, p.fname ASC");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo '<div class="group-header">EMPLOYMENT STATUS: UNASSIGNED (' . $stmt->rowCount() . ')</div>';
            echo '<table class="custom-report-table">';
            renderTableHeader($cols, $colHeaders);
            echo '<tbody>';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                renderTableRow($row, $cols);
            }
            echo '</tbody></table>';
        }
    }
} catch (PDOException $e) {
    echo "Error generating report: " . $e->getMessage();
}
?>
</div>

<?php include('footer_print.php'); ?>
<script>
    // window.print();
</script>
</body>
</html>
