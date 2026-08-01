<?php
/**
 * Print Official Registry (Dynamic Layout)
 */
include('session.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method. Please configure the registry first.");
}

$run_id = isset($_POST['run_id']) ? intval($_POST['run_id']) : 0;
$emp_status_filter = isset($_POST['emp_status_filter']) ? $_POST['emp_status_filter'] : 'all';
$membership_filter = isset($_POST['membership_filter']) ? $_POST['membership_filter'] : 'all';
$group_by_dept = isset($_POST['group_by_dept']) ? true : false;

$income_cols = isset($_POST['income_cols']) ? $_POST['income_cols'] : [];
$deduction_cols = isset($_POST['deduction_cols']) ? $_POST['deduction_cols'] : [];

// Signatories from POST
$signatories = [];
$certified_sig = null;
$sig_roles = isset($_POST['sig_role']) ? $_POST['sig_role'] : [];
$sig_names = isset($_POST['sig_name']) ? $_POST['sig_name'] : [];
$sig_titles = isset($_POST['sig_title']) ? $_POST['sig_title'] : [];

for ($i = 0; $i < count($sig_roles); $i++) {
    $role = isset($sig_roles[$i]) ? $sig_roles[$i] : '';
    $name = isset($sig_names[$i]) ? $sig_names[$i] : '';
    $title = isset($sig_titles[$i]) ? $sig_titles[$i] : '';
    
    if (!empty($role) || !empty($name) || !empty($title)) {
        // If the role contains "Certified Correct", save it for the bottom table
        if (stripos($role, 'certified correct') !== false) {
            $certified_sig = ['role' => $role, 'name' => $name, 'title' => $title];
        } else {
            $signatories[] = ['role' => $role, 'name' => $name, 'title' => $title];
        }
    }
}

try {
    // 1. Fetch Run Info
    $run_query = $conn->prepare("
        SELECT pr.*, 
               pp.profile_name,
               creator.fname as creator_fname, creator.lname as creator_lname
        FROM pr_tbl_payroll_runs pr
        LEFT JOIN pr_tbl_payroll_profiles pp ON pr.profile_id = pp.profile_id
        LEFT JOIN useraccount creator ON pr.created_by = creator.user_id
        WHERE pr.run_id = :run_id
    ");
    $run_query->execute([':run_id' => $run_id]);
    $run = $run_query->fetch(PDO::FETCH_ASSOC);

    if (!$run) {
        die("Payroll run not found.");
    }

    // 2. Build Base Query for Personnel
    $sql = "
        SELECT prd.*,
               p.fname, p.lname, p.mname, p.rate_per_day, p.empStat_id,
               d.dept_office_name
        FROM pr_tbl_payroll_run_details prd
        LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
        LEFT JOIN dept_offices d ON p.do_id = d.do_id
        WHERE prd.run_id = :run_id
    ";

    // 3. Apply Filters
    $params = [':run_id' => $run_id];

    if ($emp_status_filter !== 'all') {
        $sql .= " AND p.empStat_id = :emp_stat";
        $params[':emp_stat'] = intval($emp_status_filter);
    }

    // GSIS / SSS filter is tricky because it depends on their deductions or specific fields.
    // If they have GSIS / SSS deductions, we can filter by that.
    if ($membership_filter === 'gsis') {
        $sql .= " AND EXISTS (SELECT 1 FROM pr_tbl_payroll_run_deductions dd WHERE dd.detail_id = prd.detail_id AND (dd.deduction_title LIKE '%GSIS%' OR dd.deduction_type LIKE '%GSIS%'))";
    } elseif ($membership_filter === 'sss') {
        $sql .= " AND EXISTS (SELECT 1 FROM pr_tbl_payroll_run_deductions dd WHERE dd.detail_id = prd.detail_id AND (dd.deduction_title LIKE '%SSS%' OR dd.deduction_type LIKE '%SSS%'))";
    }

    // Order by department if grouping, then by name
    if ($group_by_dept) {
        $sql .= " ORDER BY d.dept_office_name ASC, p.lname ASC, p.fname ASC";
    } else {
        $sql .= " ORDER BY p.lname ASC, p.fname ASC";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Pre-fetch all selected incomes and deductions for all these personnel
    $incomes_data = [];
    $deductions_data = [];
    
    if (count($personnel) > 0) {
        // Fetch Incomes
        $inc_stmt = $conn->prepare("SELECT detail_id, income_title, amount FROM pr_tbl_payroll_run_income WHERE run_id = :run_id");
        $inc_stmt->execute([':run_id' => $run_id]);
        while ($row = $inc_stmt->fetch(PDO::FETCH_ASSOC)) {
            $incomes_data[$row['detail_id']][$row['income_title']] = $row['amount'];
        }

        // Fetch Deductions
        $ded_stmt = $conn->prepare("SELECT detail_id, deduction_title, employee_amount FROM pr_tbl_payroll_run_deductions WHERE run_id = :run_id");
        $ded_stmt->execute([':run_id' => $run_id]);
        while ($row = $ded_stmt->fetch(PDO::FETCH_ASSOC)) {
            $deductions_data[$row['detail_id']][$row['deduction_title']] = $row['employee_amount'];
        }
    }

    // Get institution preferences for header
    $school_query = $conn->query("SELECT * FROM institution_preferences LIMIT 1");
    $school = $school_query->fetch(PDO::FETCH_ASSOC);
    $institution_name = $school ? $school['institution_name'] : 'Local Government Unit';
    $school_logo = $school ? $school['logo'] : 'default.png';

} catch (Exception $e) {
    die("Error generating registry: " . $e->getMessage());
}

// Helper to chunk array for grouping
function groupPersonnel($personnel, $group_by_dept) {
    if (!$group_by_dept) {
        return ['ALL DEPARTMENTS' => $personnel];
    }
    $grouped = [];
    foreach ($personnel as $p) {
        $dept = $p['dept_office_name'] ?: 'UNASSIGNED';
        $grouped[$dept][] = $p;
    }
    return $grouped;
}

$grouped_personnel = groupPersonnel($personnel, $group_by_dept);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Official Payroll Registry - <?php echo htmlspecialchars($run['run_name']); ?></title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="../img/<?php echo htmlspecialchars($school_logo); ?>">
    <style>
        /* Landscape formatting for legal size */
        @media print {
            @page { 
                size: legal landscape;
                margin: 0.5in;
            }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            body { margin: 0; background: #fff; }
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt;
            background: #f0f0f0;
            padding: 20px;
        }

        .document-container {
            background: #fff;
            padding: 30px;
            margin: 0 auto;
            max-width: 14in; /* Width for legal landscape */
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        @media print {
            .document-container { box-shadow: none; padding: 0; max-width: none; }
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.2;
            position: relative;
        }
        
        .header-logo {
            position: absolute;
            left: 50px;
            top: 0;
            width: 80px;
            height: auto;
        }

        .header-section h4 { margin: 0; font-weight: normal; font-size: 10pt; }
        .header-section h3 { margin: 5px 0; font-weight: bold; font-size: 11pt; }
        .header-section h2 { margin: 10px 0; font-weight: bold; font-size: 14pt; letter-spacing: 2px; }
        
        .table-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
            color: #d35400; /* Distinct color for header if needed, else black */
            font-size: 10pt;
            text-transform: uppercase;
        }

        .registry-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 30px;
        }

        .registry-table th, .registry-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        .registry-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .registry-table .col-money {
            text-align: right;
            width: 70px;
        }

        .registry-table .col-name {
            font-weight: bold;
            text-align: left;
            white-space: nowrap;
        }

        .registry-table .col-sig {
            width: 100px;
        }

        .group-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left !important;
            padding: 6px !important;
        }

        .totals-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .signatory-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signatory-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .signatory-box {
            width: 23%;
            margin-bottom: 40px;
            text-align: center;
            font-size: 9pt;
        }

        .signatory-role {
            font-size: 8pt;
            margin-bottom: 35px;
            text-align: left;
            font-weight: bold;
        }

        .signatory-name {
            font-weight: bold;
            text-transform: uppercase;
            padding-bottom: 2px;
        }

        .signatory-title {
            font-size: 8pt;
            margin-top: 2px;
        }

        .btn-print {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: none;
            padding: 5px;
            cursor: pointer;
            font-size: 16pt;
            opacity: 0.5;
            transition: opacity 0.2s;
        }
        .btn-print:hover { opacity: 1; }

    </style>
</head>
<body>

<button class="btn-print no-print" onclick="window.print()" title="Print Registry">🖨️</button>

<?php 
$is_first_page = true;
foreach ($grouped_personnel as $group_name => $employees): 
    if (!$is_first_page) echo '<div class="page-break"></div>';
    $is_first_page = false;
?>

<div class="document-container">
    
    <div class="header-section">
        <img src="../img/<?php echo htmlspecialchars($school_logo); ?>" class="header-logo" alt="Logo">
        <h4>Republic of the Philippines</h4>
        <h3><?php echo htmlspecialchars($institution_name); ?></h3>
        <h2>P A Y R O L L</h2>
        <h4><?php echo date('F d, Y', strtotime($run['pay_period_start'])); ?> to <?php echo date('F d, Y', strtotime($run['pay_period_end'])); ?></h4>
    </div>
    
    <div style="margin-bottom: 5px; font-weight: bold; font-size: 10pt; text-transform: uppercase; color: #000;">
        <?php echo date('F Y', strtotime($run['pay_period_end'])); ?>
    </div>
    <div style="font-size: 7.5pt; margin-bottom: 10px;">
        We acknowledge receipt of the sum shown opposite our names as full compensation for services rendered for the period stated.
    </div>

    <?php if ($group_name !== 'ALL DEPARTMENTS'): ?>
    <div class="table-title">DEPARTMENT: <?php echo htmlspecialchars($group_name); ?></div>
    <?php endif; ?>

    <table class="registry-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">NO.</th>
                <th rowspan="2" style="width: 150px;">Employee Name</th>
                <th colspan="7">INCOME</th>
                <th rowspan="2">Less:<br>Absent</th>
                <th rowspan="2">Less:<br>Late</th>
                <th rowspan="2">TOTAL</th>
                <?php if (count($deduction_cols) > 0): ?>
                <th colspan="<?php echo count($deduction_cols) + 1; ?>">OTHER DEDUCTIONS</th>
                <?php else: ?>
                <th rowspan="2">TOTAL<br>DEDUCTIONS</th>
                <?php endif; ?>
                <th rowspan="2">NET PAY</th>
                <th rowspan="2" class="col-sig">SIGNATURE</th>
            </tr>
            <tr>
                <!-- Income sub-headers -->
                <th>Monthly<br>Rate</th>
                <th>DAILY<br>RATE</th>
                <th>No. Of<br>Work Days</th>
                <th>LESS:No. of<br>Days Absent</th>
                <th>TOTAL #<br>of Days present</th>
                <th>Gross<br>Income</th>
                <th>REFUND</th>
                
                <!-- Deduction sub-headers -->
                <?php foreach ($deduction_cols as $ded_col): ?>
                <th><?php echo htmlspecialchars($ded_col); ?></th>
                <?php endforeach; ?>
                <?php if (count($deduction_cols) > 0): ?>
                <th>TOTAL<br>DEDUCTIONS</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $counter = 1;
            
            // Totals
            $total_gross = 0;
            $total_less_absent = 0;
            $total_less_late = 0;
            $total_total_income = 0;
            $total_total_ded = 0;
            $total_net = 0;
            $total_deductions = array_fill_keys($deduction_cols, 0);
            $total_refund = 0;

            foreach ($employees as $emp): 
                $detail_id = $emp['detail_id'];
                $emp_incomes = $incomes_data[$detail_id] ?? [];
                $emp_deductions = $deductions_data[$detail_id] ?? [];
                
                $full_name = htmlspecialchars($emp['lname'] . ', ' . $emp['fname'] . ' ' . ($emp['mname'] ? substr($emp['mname'], 0, 1) . '.' : ''));
                
                // rate_per_day actually stores the Monthly Salary in this database schema
                $monthly_rate = floatval($emp['rate_per_day']); 
                $work_days = 10; // As per the specific screenshots showing 10 days for the period
                $absent_days = isset($emp['absent_days']) ? $emp['absent_days'] : 0;
                $present_days = $work_days - $absent_days;
                $gross = $emp['gross_pay'];
                
                // Fallback: If Monthly Rate is 0 in the DB (like for Job Orders), 
                // use their base computed gross income as their effective rate.
                if ($monthly_rate <= 0) {
                    $monthly_rate = $gross;
                    $daily_rate = $work_days > 0 ? ($gross / $work_days) : 0;
                } else {
                    $daily_rate = $monthly_rate / 22; // Standard government formula: Monthly / 22 days
                }
                
                // If refund is treated as an income
                $refund = isset($emp_incomes['REFUND']) ? $emp_incomes['REFUND'] : 0;
                
                // Assuming absent/late deductions might be stored in deductions, or derived
                $less_absent = 0; 
                $less_late = 0;
                $dynamic_total_deductions = 0;
                
                // If they exist as specific deductions, map them:
                foreach ($emp_deductions as $title => $amt) {
                    if (stripos($title, 'absent') !== false) { 
                        $less_absent += $amt; 
                    }
                    elseif (stripos($title, 'late') !== false) { 
                        $less_late += $amt; 
                    }
                    else {
                        // Any other deduction adds to the actual Total Deductions
                        $dynamic_total_deductions += $amt;
                    }
                }
                
                // True Total Income = Gross + Refund - Absent - Late
                $total_income_computed = $gross + $refund - $less_absent - $less_late;
                
                // True Net Pay
                $dynamic_net_pay = $total_income_computed - $dynamic_total_deductions;
                
                $total_gross += $gross;
                $total_refund += $refund;
                $total_less_absent += $less_absent;
                $total_less_late += $less_late;
                $total_total_income += $total_income_computed;
                
                $total_total_ded += $dynamic_total_deductions;
                $total_net += $dynamic_net_pay;
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $counter++; ?></td>
                <td class="col-name"><?php echo $full_name; ?></td>
                
                <td class="col-money"><?php echo number_format($monthly_rate, 2); ?></td>
                <td class="col-money"><?php echo $daily_rate > 0 ? number_format($daily_rate, 2) : '-'; ?></td>
                <td style="text-align: center; color: #c0392b;"><?php echo $work_days; ?></td>
                <td style="text-align: center; color: #c0392b;"><?php echo $absent_days > 0 ? $absent_days : '-'; ?></td>
                <td style="text-align: center; color: #c0392b;"><?php echo $present_days; ?></td>
                <td class="col-money"><?php echo number_format($gross, 2); ?></td>
                <td class="col-money"><?php echo $refund > 0 ? number_format($refund, 2) : '-'; ?></td>
                
                <td class="col-money"><?php echo $less_absent > 0 ? number_format($less_absent, 2) : '-'; ?></td>
                <td class="col-money"><?php echo $less_late > 0 ? number_format($less_late, 2) : '-'; ?></td>
                
                <td class="col-money" style="font-weight:bold;"><?php echo number_format($total_income_computed, 2); ?></td>

                <!-- Deduction Columns Data -->
                <?php foreach ($deduction_cols as $ded_col): 
                    $amt = isset($emp_deductions[$ded_col]) ? $emp_deductions[$ded_col] : 0;
                    $total_deductions[$ded_col] += $amt;
                ?>
                <td class="col-money"><?php echo $amt > 0 ? number_format($amt, 2) : '-'; ?></td>
                <?php endforeach; ?>

                <td class="col-money" style="font-weight:bold;"><?php echo number_format($dynamic_total_deductions, 2); ?></td>
                <td class="col-money" style="font-weight:bold;"><?php echo number_format($dynamic_net_pay, 2); ?></td>
                <td class="col-sig"></td>
            </tr>
            <?php endforeach; ?>
            
            <!-- TOTALS ROW (Includes Left-Side Certification) -->
            <tr class="totals-row" style="background-color: transparent; border-top: 1px solid #000; border-bottom: 2px solid #000;">
                <td colspan="7" style="text-align: left; padding-left: 10px; font-weight: bold; font-size: 8pt; vertical-align: top; border-bottom: 2px solid #000;">
                    CERTIFIED: Service duly rendered as stated:
                </td>
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000;"><?php echo number_format($total_gross, 2); ?></td>
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000;"><?php echo $total_refund > 0 ? number_format($total_refund, 2) : '-'; ?></td>
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000;"><?php echo $total_less_absent > 0 ? number_format($total_less_absent, 2) : '-'; ?></td>
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000;"><?php echo $total_less_late > 0 ? number_format($total_less_late, 2) : '-'; ?></td>
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000;"><?php echo number_format($total_total_income, 2); ?></td>
                
                <?php foreach ($deduction_cols as $ded_col): ?>
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000;"><?php echo $total_deductions[$ded_col] > 0 ? number_format($total_deductions[$ded_col], 2) : '-'; ?></td>
                <?php endforeach; ?>
                
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000;"><?php echo number_format($total_total_ded, 2); ?></td>
                <td class="col-money" style="font-weight: bold; border-bottom: 2px solid #000; color: #27ae60;"><?php echo number_format($total_net, 2); ?></td>
                <td class="col-sig" style="border-bottom: 2px solid #000;"></td>
            </tr>
            
            <!-- CERTIFICATION ROW (Bottom Half) -->
            <tr>
                <!-- Empty left side, no border -->
                <td colspan="8" style="border: none !important;"></td>
                
                <!-- B and C Blocks on the right side -->
                <td colspan="<?php echo 5 + count($deduction_cols); ?>" style="border: none !important; padding: 5px 0;">
                    
                    <div style="display: flex; align-items: center; justify-content: flex-start; width: 100%;">
                        <!-- B Block -->
                        <div style="display: flex; align-items: center; margin-right: 40px;">
                            <div style="border: 1px solid #000; padding: 1px 4px; font-weight: bold; font-size: 8pt; margin-right: 2px; line-height: 1;">B</div>
                            <div style="border: 1px solid #000; width: 60px; height: 14px; margin-right: 5px;"></div>
                            <div style="font-weight: bold; font-size: 8pt; white-space: nowrap;">
                                CERTIFIED: Supporting documents complete and proper:
                            </div>
                        </div>
                        
                        <!-- C Block -->
                        <div style="display: flex; align-items: center;">
                            <div style="border: 1px solid #000; padding: 1px 4px; font-weight: bold; font-size: 8pt; margin-right: 2px; line-height: 1;">C</div>
                            <div style="border: 1px solid #000; width: 60px; height: 14px; margin-right: 5px;"></div>
                            <div style="font-weight: bold; font-size: 8pt; white-space: nowrap;">
                                CERTIFIED: Cash available for the purpose.
                            </div>
                        </div>
                </td>
            </tr>
        </tbody>
    </table>

    <?php
    // Extract names for specific roles to populate the hardcoded layout
    $mayor_name = '';
    $accountant_name = '';
    $treasury_name = '';
    $disbursing_name = '';

    foreach ($signatories as $sig) {
        // Search across all fields in case the user typed the position in the wrong box
        $search_string = strtolower($sig['title'] . ' ' . $sig['role'] . ' ' . $sig['name']);
        
        // If they typed the position into the name field, we'll use that as a fallback for the name
        $actual_name = !empty($sig['name']) ? $sig['name'] : (!empty($sig['title']) ? $sig['title'] : '');
        
        if (strpos($search_string, 'mayor') !== false && empty($mayor_name)) $mayor_name = $actual_name;
        if (strpos($search_string, 'accountant') !== false && empty($accountant_name)) $accountant_name = $actual_name;
        if (strpos($search_string, 'treasury') !== false && empty($treasury_name)) $treasury_name = $actual_name;
        if (strpos($search_string, 'disbursing') !== false && empty($disbursing_name)) $disbursing_name = $actual_name;
    }
    
    // If Certified Correct was filled out in the separate config block, use it, otherwise fallback to Accountant
    $certified_name = $accountant_name;
    if (isset($certified_sig) && !empty($certified_sig['name'])) {
        $certified_name = $certified_sig['name'];
    }
    ?>

    <!-- Hardcoded Signatory Layout perfectly matching the PDF -->
    <div style="margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
            
            <!-- ROW 1: Top Signatories -->
            <tr>
                <!-- Col 1: Mayor -->
                <td style="width: 33.33%; padding: 10px; vertical-align: bottom;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div style="width: 70%; text-align: center;">
                            <div style="font-weight: bold; text-transform: uppercase; min-height: 12px; margin-bottom: 1px;">
                                <?php echo !empty($mayor_name) ? htmlspecialchars($mayor_name) : '&nbsp;'; ?>
                            </div>
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%;"></div>
                            <div style="font-size: 7pt; margin-top: 1px;">Signature over Printed Name</div>
                            <div style="font-size: 8pt; margin-top: 1px;">Municipal Mayor</div>
                        </div>
                        <div style="width: 25%; text-align: center;">
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%; margin-bottom: 1px;"></div>
                            <div style="font-size: 7pt;">Date</div>
                        </div>
                    </div>
                </td>
                
                <!-- Col 2: Accountant -->
                <td style="width: 33.33%; padding: 10px; vertical-align: bottom;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div style="width: 70%; text-align: center;">
                            <div style="font-weight: bold; text-transform: uppercase; min-height: 12px; margin-bottom: 1px;">
                                <?php echo !empty($accountant_name) ? htmlspecialchars($accountant_name) : '&nbsp;'; ?>
                            </div>
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%;"></div>
                            <div style="font-size: 7pt; margin-top: 1px;">Signature over Printed Name</div>
                            <div style="font-size: 8pt; margin-top: 1px;">Municipal Accountant</div>
                        </div>
                        <div style="width: 25%; text-align: center;">
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%; margin-bottom: 1px;"></div>
                            <div style="font-size: 7pt;">Date</div>
                        </div>
                    </div>
                </td>
                
                <!-- Col 3: Treasury -->
                <td style="width: 33.33%; padding: 10px; vertical-align: bottom;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div style="width: 70%; text-align: center;">
                            <div style="font-weight: bold; text-transform: uppercase; min-height: 12px; margin-bottom: 1px;">
                                <?php echo !empty($treasury_name) ? htmlspecialchars($treasury_name) : '&nbsp;'; ?>
                            </div>
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%;"></div>
                            <div style="font-size: 7pt; margin-top: 1px;">Signature over Printed Name</div>
                            <div style="font-size: 8pt; margin-top: 1px;">Head of Treasury Division/Unit</div>
                        </div>
                        <div style="width: 25%; text-align: center;">
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%; margin-bottom: 1px;"></div>
                            <div style="font-size: 7pt;">Date</div>
                        </div>
                    </div>
                </td>
            </tr>

            <!-- ROW 2: Certifications with Continuous Lines -->
            <tr>
                <td style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 5px 10px; font-weight: bold;">
                    APPROVED FOR PAYMENT: P <span style="display:inline-block; width: 120px; border-bottom: 1px solid #000;"></span>
                </td>
                <td style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 5px 10px;">
                    <div style="display: flex; align-items: flex-start; font-weight: bold;">
                        <div style="border: 1px solid #000; padding: 1px 4px; line-height: 1; margin-right: 2px;">E</div>
                        <div style="border: 1px solid #000; width: 40px; height: 13px; margin-right: 5px;"></div>
                        <div style="font-size: 7.5pt; max-width: 250px;">
                            CERTIFIED: Each employee whose name appears on the payroll has been paid the amount as indicated
                        </div>
                    </div>
                </td>
                <td style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 5px 10px;">
                    <div style="display: flex; align-items: flex-start;">
                        <div style="border: 1px solid #000; padding: 1px 4px; line-height: 1; margin-right: 2px; font-weight: bold;">F</div>
                        <div style="border: 1px solid #000; width: 40px; height: 13px;"></div>
                    </div>
                </td>
            </tr>

            <!-- ROW 3: Bottom Signatories -->
            <tr>
                <!-- Col 1: Mayor Again -->
                <td style="width: 33.33%; padding: 10px; vertical-align: bottom;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div style="width: 70%; text-align: center;">
                            <div style="font-weight: bold; text-transform: uppercase; min-height: 12px; margin-bottom: 1px;">
                                <?php echo !empty($mayor_name) ? htmlspecialchars($mayor_name) : '&nbsp;'; ?>
                            </div>
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%;"></div>
                            <div style="font-size: 7pt; margin-top: 1px;">Signature over Printed Name</div>
                            <div style="font-size: 8pt; margin-top: 1px;">Municipal Mayor</div>
                        </div>
                        <div style="width: 25%; text-align: center;">
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%; margin-bottom: 1px;"></div>
                            <div style="font-size: 7pt;">Date</div>
                        </div>
                    </div>
                </td>
                
                <!-- Col 2: Disbursing Officer -->
                <td style="width: 33.33%; padding: 10px; vertical-align: bottom;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div style="width: 70%; text-align: center;">
                            <div style="font-weight: bold; text-transform: uppercase; min-height: 12px; margin-bottom: 1px;">
                                <?php echo !empty($disbursing_name) ? htmlspecialchars($disbursing_name) : '&nbsp;'; ?>
                            </div>
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%;"></div>
                            <div style="font-size: 7pt; margin-top: 1px;">Signature over Printed Name</div>
                            <div style="font-size: 8pt; margin-top: 1px;">Disbursing Officer</div>
                        </div>
                        <div style="width: 25%; text-align: center;">
                            <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%; margin-bottom: 1px;"></div>
                            <div style="font-size: 7pt;">Date</div>
                        </div>
                    </div>
                </td>
                
                <!-- Col 3: CAFOA Block -->
                <td style="width: 33.33%; padding: 10px; vertical-align: bottom;">
                    <div style="width: 100%; text-align: left; padding-left: 10px;">
                        <div style="margin-bottom: 5px;">CAFOA No: <span style="display:inline-block; width: 120px; border-bottom: 1px solid #000;"></span></div>
                        <div>Date: <span style="display:inline-block; width: 142px; border-bottom: 1px solid #000;"></span></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Accounting Entries Section -->
    <div style="margin-top: 5px; page-break-inside: avoid;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <!-- Left Side Accounting Entries -->
            <div style="width: 48%;">
                <div style="font-weight: bold; font-size: 8pt; margin-bottom: 1px; padding-left: 20px;">ACCOUNTING ENTRIES</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #000;">
                    <tr>
                        <th style="border: 1px solid #000; padding: 2px;">Account Code</th>
                        <th style="border: 1px solid #000; padding: 2px;">Debit</th>
                        <th style="border: 1px solid #000; padding: 2px;">Credit</th>
                    </tr>
                    <tr><td style="border: 1px solid #000; height: 12px;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td></tr>
                    <tr><td style="border: 1px solid #000; height: 12px;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td></tr>
                    <tr><td style="border: 1px solid #000; height: 12px;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td></tr>
                </table>
            </div>

            <!-- Right Side Particulars -->
            <div style="width: 48%;">
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #000; margin-top: 13px;">
                    <tr>
                        <th style="border: 1px solid #000; padding: 2px; width: 45%;">Particulars</th>
                        <th style="border: 1px solid #000; padding: 2px;">Account Code</th>
                        <th style="border: 1px solid #000; padding: 2px;">Debit</th>
                        <th style="border: 1px solid #000; padding: 2px;">Credit</th>
                    </tr>
                    <tr><td style="border: 1px solid #000; height: 12px;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td></tr>
                    <tr><td style="border: 1px solid #000; height: 12px;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td></tr>
                    <tr><td style="border: 1px solid #000; height: 12px;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td><td style="border: 1px solid #000;"></td></tr>
                </table>
                
                <!-- Certified Correct Block attached precisely to the right side bottom -->
                <div style="margin-top: 15px; display: flex; justify-content: flex-start; align-items: flex-end;">
                    <div style="font-weight: bold; font-size: 8pt; margin-right: 15px; margin-bottom: 2px;">Certified Correct:</div>
                    <div style="width: 300px; text-align: center;">
                        <div style="border-top: 1px solid #000; margin: 0 auto; width: 100%;"></div>
                        <div style="font-weight: bold; text-transform: uppercase; font-size: 8pt; margin-top: 2px;">
                            <?php echo !empty($certified_name) ? htmlspecialchars($certified_name) : '&nbsp;'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    </div>

</div>
<?php endforeach; ?>

<?php if (empty($grouped_personnel)): ?>
<div class="document-container text-center">
    <h3>No personnel found matching the selected filters.</h3>
</div>
<?php endif; ?>

</body>
</html>
