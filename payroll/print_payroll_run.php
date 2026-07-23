<?php
/**
 * Print Payroll Run
 * Print-friendly format for payroll run report
 */

include('session.php');

// Get run_id from URL
$run_id = isset($_GET['run_id']) ? intval($_GET['run_id']) : 0;

if (!$run_id) {
    die("Invalid payroll run ID");
}

// Fetch payroll run details
try {
    $run_query = $conn->prepare("
        SELECT pr.*, 
               pp.profile_name, pp.profile_type,
               creator.fname as creator_fname, creator.lname as creator_lname,
               approver.fname as approver_fname, approver.lname as approver_lname
        FROM pr_tbl_payroll_runs pr
        LEFT JOIN pr_tbl_payroll_profiles pp ON pr.profile_id = pp.profile_id
        LEFT JOIN useraccount creator ON pr.created_by = creator.user_id
        LEFT JOIN useraccount approver ON pr.approved_by = approver.user_id
        WHERE pr.run_id = :run_id
    ");
    $run_query->execute([':run_id' => $run_id]);
    $run = $run_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$run) {
        die("Payroll run not found");
    }
    
} catch (Exception $e) {
    die("Error loading payroll run: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payroll Run Report - <?php echo htmlspecialchars($run['run_name']); ?></title>
    <style>
        /* Flexible page size - supports Letter (8.5\"x11\"), Folio (8.5\"x13\"), Legal (8.5\"x14\") */
        @media print {
            .no-print { display: none; }
            @page { 
                size: auto;
                margin: 0.75in 0.5in;
            }
            body {
                max-width: 7.5in;
                margin: 0 auto;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20pt;
            color: #2c3e50;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16pt;
            color: #34495e;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        .summary-box {
            background: #ecf0f1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        
        .summary-box h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .summary-table {
            width: 100%;
        }
        
        .summary-table td {
            padding: 8px;
        }
        
        .summary-table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .payroll-table th {
            background: #34495e;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #2c3e50;
        }
        
        .payroll-table td {
            padding: 8px;
            border: 1px solid #bdc3c7;
        }
        
        .payroll-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-row {
            background: #ecf0f1 !important;
            font-weight: bold;
            border-top: 2px solid #34495e;
        }
        
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin-top: 40px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 50px;
        }
        
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #2980b9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            color: white;
        }
        
        .status-draft { background: #f39c12; }
        .status-pending { background: #e67e22; }
        .status-approved { background: #27ae60; }
        .status-completed { background: #16a085; }
        .status-cancelled { background: #c0392b; }
    </style>
</head>
<body>

<button onclick="window.print()" class="print-button no-print">
    <i class="fa fa-print"></i> Print Report
</button>

<div class="header">
    <h1>PAYROLL RUN REPORT</h1>
    <h2><?php echo htmlspecialchars($run['run_name']); ?></h2>
    <p>
        <span class="status-badge status-<?php echo $run['run_status']; ?>">
            <?php echo strtoupper($run['run_status']); ?>
        </span>
    </p>
</div>

<div class="info-section">
    <table class="info-table">
        <tr>
            <td>Run ID:</td>
            <td><?php echo $run['run_id']; ?></td>
            <td>Run Type:</td>
            <td><?php echo ucwords(str_replace('_', ' ', $run['run_type'])); ?></td>
        </tr>
        <tr>
            <td>Profile:</td>
            <td><?php echo htmlspecialchars($run['profile_name']); ?></td>
            <td>Profile Type:</td>
            <td><?php echo ucwords($run['profile_type']); ?></td>
        </tr>
        <tr>
            <td>Pay Period:</td>
            <td>
                <?php echo date('F d, Y', strtotime($run['pay_period_start'])); ?> - 
                <?php echo date('F d, Y', strtotime($run['pay_period_end'])); ?>
            </td>
            <td>Payment Date:</td>
            <td><?php echo $run['payment_date'] ? date('F d, Y', strtotime($run['payment_date'])) : 'Not set'; ?></td>
        </tr>
        <tr>
            <td>Created By:</td>
            <td><?php echo htmlspecialchars($run['creator_fname'] . ' ' . $run['creator_lname']); ?></td>
            <td>Created Date:</td>
            <td><?php echo date('F d, Y h:i A', strtotime($run['created_at'])); ?></td>
        </tr>
        <?php if ($run['approved_by']): ?>
        <tr>
            <td>Approved By:</td>
            <td><?php echo htmlspecialchars($run['approver_fname'] . ' ' . $run['approver_lname']); ?></td>
            <td>Approved Date:</td>
            <td><?php echo date('F d, Y h:i A', strtotime($run['approved_at'])); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($run['notes']): ?>
        <tr>
            <td>Notes:</td>
            <td colspan="3"><?php echo nl2br(htmlspecialchars($run['notes'])); ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="summary-box">
    <h3>Financial Summary</h3>
    <table class="summary-table">
        <tr>
            <td>Total Personnel:</td>
            <td><?php echo number_format($run['total_personnel']); ?></td>
        </tr>
        <tr>
            <td>Total Gross Pay:</td>
            <td>₱<?php echo number_format($run['total_gross'], 2); ?></td>
        </tr>
        <tr>
            <td>Total Deductions:</td>
            <td>₱<?php echo number_format($run['total_deductions'], 2); ?></td>
        </tr>
        <tr>
            <td>Total Employer Share:</td>
            <td>₱<?php echo number_format($run['total_employer_share'], 2); ?></td>
        </tr>
        <tr style="border-top: 2px solid #34495e; font-size: 14pt;">
            <td><strong>Total Net Pay:</strong></td>
            <td><strong>₱<?php echo number_format($run['total_net_pay'], 2); ?></strong></td>
        </tr>
    </table>
</div>

<h3 style="margin-top: 30px; color: #2c3e50;">Personnel Payroll Details</h3>

<table class="payroll-table">
    <thead>
        <tr>
            <th width="10%">ID</th>
            <th width="25%">Name</th>
            <th width="20%">Department</th>
            <th width="12%" class="text-right">Gross Pay</th>
            <th width="12%" class="text-right">Deductions</th>
            <th width="12%" class="text-right">Net Pay</th>
            <th width="10%" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $details_query = $conn->prepare("
            SELECT prd.*,
                   p.fname, p.lname, p.mname,
                   d.dept_office_name
            FROM pr_tbl_payroll_run_details prd
            LEFT JOIN personnels p ON prd.personnel_id = p.personnel_id
            LEFT JOIN dept_offices d ON p.do_id = d.do_id
            WHERE prd.run_id = :run_id
            ORDER BY p.lname, p.fname
        ");
        $details_query->execute([':run_id' => $run_id]);
        
        $total_gross = 0;
        $total_deductions = 0;
        $total_net = 0;
        
        while ($detail = $details_query->fetch(PDO::FETCH_ASSOC)):
            $full_name = htmlspecialchars($detail['lname'] . ', ' . $detail['fname'] . ' ' . ($detail['mname'] ? substr($detail['mname'], 0, 1) . '.' : ''));
            
            $total_gross += $detail['gross_pay'];
            $total_deductions += $detail['total_deductions'];
            $total_net += $detail['net_pay'];
        ?>
        <tr>
            <td><?php echo htmlspecialchars($detail['personnel_id']); ?></td>
            <td><?php echo $full_name; ?></td>
            <td><?php echo htmlspecialchars($detail['dept_office_name'] ?? 'N/A'); ?></td>
            <td class="text-right">₱<?php echo number_format($detail['gross_pay'], 2); ?></td>
            <td class="text-right">₱<?php echo number_format($detail['total_deductions'], 2); ?></td>
            <td class="text-right">₱<?php echo number_format($detail['net_pay'], 2); ?></td>
            <td class="text-center"><?php echo ucfirst($detail['payment_status']); ?></td>
        </tr>
        <?php endwhile; ?>
        
        <tr class="total-row">
            <td colspan="3" class="text-right">TOTAL:</td>
            <td class="text-right">₱<?php echo number_format($total_gross, 2); ?></td>
            <td class="text-right">₱<?php echo number_format($total_deductions, 2); ?></td>
            <td class="text-right">₱<?php echo number_format($total_net, 2); ?></td>
            <td></td>
        </tr>
    </tbody>
</table>

<!-- Detailed Breakdown (Optional - uncomment if needed) -->
<?php if (isset($_GET['detailed']) && $_GET['detailed'] == '1'): ?>
<div style="page-break-before: always;"></div>
<h2 style="color: #2c3e50; margin-top: 30px;">Detailed Income & Deductions Breakdown</h2>

<?php
$details_query->execute([':run_id' => $run_id]);
while ($detail = $details_query->fetch(PDO::FETCH_ASSOC)):
    $full_name = htmlspecialchars($detail['lname'] . ', ' . $detail['fname'] . ' ' . ($detail['mname'] ? substr($detail['mname'], 0, 1) . '.' : ''));
?>
<div style="margin-bottom: 30px; border: 1px solid #bdc3c7; padding: 15px; page-break-inside: avoid;">
    <h4><?php echo $full_name; ?> (<?php echo $detail['personnel_id']; ?>)</h4>
    
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <strong>Income Items:</strong>
                <table style="width: 100%; margin-top: 5px;">
                    <?php
                    $income_query = $conn->prepare("
                        SELECT * FROM pr_tbl_payroll_run_income
                        WHERE detail_id = :detail_id
                        ORDER BY income_title
                    ");
                    $income_query->execute([':detail_id' => $detail['detail_id']]);
                    
                    while ($income = $income_query->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($income['income_title']); ?></td>
                        <td style="text-align: right;">₱<?php echo number_format($income['amount'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </td>
            <td width="50%" style="vertical-align: top;">
                <strong>Deduction Items:</strong>
                <table style="width: 100%; margin-top: 5px;">
                    <?php
                    $deduction_query = $conn->prepare("
                        SELECT * FROM pr_tbl_payroll_run_deductions
                        WHERE detail_id = :detail_id
                        ORDER BY deduction_title
                    ");
                    $deduction_query->execute([':detail_id' => $detail['detail_id']]);
                    
                    while ($deduction = $deduction_query->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($deduction['deduction_title']); ?></td>
                        <td style="text-align: right;">₱<?php echo number_format($deduction['employee_amount'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </td>
        </tr>
    </table>
    
    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #bdc3c7;">
        <strong>Gross: ₱<?php echo number_format($detail['gross_pay'], 2); ?></strong> | 
        <strong>Deductions: ₱<?php echo number_format($detail['total_deductions'], 2); ?></strong> | 
        <strong style="color: #27ae60;">Net Pay: ₱<?php echo number_format($detail['net_pay'], 2); ?></strong>
    </div>
</div>
<?php endwhile; ?>
<?php endif; ?>

<div class="signature-section">
    <table style="width: 100%;">
        <tr>
            <td class="signature-box">
                <div class="signature-line">
                    <strong>Prepared By</strong><br>
                    <?php echo htmlspecialchars($run['creator_fname'] . ' ' . $run['creator_lname']); ?><br>
                    <small><?php echo date('F d, Y', strtotime($run['created_at'])); ?></small>
                </div>
            </td>
            <td class="signature-box">
                <div class="signature-line">
                    <strong>Approved By</strong><br>
                    <?php 
                    if ($run['approved_by']) {
                        echo htmlspecialchars($run['approver_fname'] . ' ' . $run['approver_lname']);
                        echo '<br><small>' . date('F d, Y', strtotime($run['approved_at'])) . '</small>';
                    } else {
                        echo '<br><small>Pending Approval</small>';
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<div style="text-align: center; margin-top: 30px; color: #7f8c8d; font-size: 10pt;">
    <p>This is a computer-generated document. Printed on <?php echo date('F d, Y h:i A'); ?></p>
    <p>Page 1 of 1 | Run ID: <?php echo $run['run_id']; ?></p>
</div>

<script>
// Auto-print on load if requested
<?php if (isset($_GET['auto_print']) && $_GET['auto_print'] == '1'): ?>
window.onload = function() {
    window.print();
};
<?php endif; ?>
</script>

</body>
</html>
