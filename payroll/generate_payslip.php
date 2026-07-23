<?php
/**
 * Payslip Generator
 * Generates individual payslip for personnel showing income, deductions, and net pay
 * Based on MOH HRMS Payslip format
 */

// Start output buffering
ob_start();

include('session.php');

// Get parameters
$personnel_id = $_GET['personnel_id'] ?? '';
$dept = $_GET['dept'] ?? '';
$pay_period_start = $_GET['period_start'] ?? date('Y-m-01'); // First day of current month
$pay_period_end = $_GET['period_end'] ?? date('Y-m-t'); // Last day of current month
$format = $_GET['format'] ?? 'html'; // html or pdf

// Validate required parameters
if (empty($personnel_id)) {
    ob_end_clean();
    header('Location: list_personnel.php?error=' . urlencode('Personnel ID is required'));
    exit();
}

try {
    // ===========================================
    // 1. GET PERSONNEL INFORMATION
    // ===========================================
    $personnel_query = $conn->prepare("
        SELECT 
            p.*,
            d.dept_office_name as department_name,
            des.des_name as designation_name,
            es.emp_stat_name as employment_status
        FROM personnels p
        LEFT JOIN dept_offices d ON p.do_id = d.do_id
        LEFT JOIN designation des ON p.des_id = des.des_id
        LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id
        WHERE p.personnel_id = :personnel_id
        LIMIT 1
    ");
    $personnel_query->execute([':personnel_id' => $personnel_id]);
    $personnel = $personnel_query->fetch(PDO::FETCH_ASSOC);
    
    if (!$personnel) {
        throw new Exception('Personnel not found');
    }
    
    // Format personnel name
    $full_name = trim($personnel['fname'] . ' ' . ($personnel['mname'] ? $personnel['mname'][0] . '. ' : '') . $personnel['lname']);
    
    // ===========================================
    // 2. GET INCOME DATA
    // ===========================================
    $income_query = $conn->prepare("
        SELECT 
            i.income_title as income_name,
            i.income_id as income_code,
            pi.amount_per_pay,
            i.income_type
        FROM pr_tbl_personnel_income pi
        INNER JOIN pr_tbl_income i ON pi.income_id = i.income_id
        WHERE pi.personnel_id = :personnel_id 
          AND pi.is_active = 1
        ORDER BY i.income_id ASC, i.income_title ASC
    ");
    $income_query->execute([':personnel_id' => $personnel_id]);
    $income_items = $income_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total gross income
    $total_gross = 0;
    foreach ($income_items as $item) {
        $total_gross += floatval($item['amount_per_pay']);
    }
    
    // ===========================================
    // 3. GET DEDUCTIONS DATA
    // ===========================================
    $deductions_query = $conn->prepare("
        SELECT 
            d.deduction_title as deduction_name,
            d.deduction_id as deduction_code,
            pd.employer_amt_per_pay as employer_amt,
            pd.employee_amt_per_pay as employee_amt,
            d.deduction_type
        FROM pr_tbl_personnel_deductions pd
        INNER JOIN pr_tbl_deductions d ON pd.deduction_id = d.deduction_id
        WHERE pd.personnel_id = :personnel_id 
          AND pd.is_active = 1
        ORDER BY d.deduction_id ASC, d.deduction_title ASC
    ");
    $deductions_query->execute([':personnel_id' => $personnel_id]);
    $deduction_items = $deductions_query->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total deductions (employee portion only)
    $total_deductions = 0;
    $total_employer_share = 0;
    foreach ($deduction_items as $item) {
        $total_deductions += floatval($item['employee_amt']);
        $total_employer_share += floatval($item['employer_amt']);
    }
    
    // ===========================================
    // 4. CALCULATE NET PAY
    // ===========================================
    $net_pay = $total_gross - $total_deductions;
    
    // ===========================================
    // 5. GET SCHOOL INFORMATION (for header)
    // ===========================================
    $school_query = $conn->query("SELECT * FROM school_form LIMIT 1");
    $school_info = $school_query->fetch(PDO::FETCH_ASSOC);
    
    $school_name = $school_info['schoolName'] ?? 'Ministry of Health';
    $division = $school_info['division'] ?? 'Human Resource Management System';
    $region = $school_info['region'] ?? '';
    
} catch (Exception $e) {
    ob_end_clean();
    error_log("Error generating payslip: " . $e->getMessage());
    header('Location: list_personnel_income.php?dept=' . urlencode($dept) . '&personnel_id=' . urlencode($personnel_id) . '&error=' . urlencode('Error generating payslip'));
    exit();
}

// Clear output buffer before generating payslip
ob_end_clean();

// ===========================================
// 6. GENERATE PAYSLIP HTML
// ===========================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - <?php echo htmlspecialchars($full_name); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Header */
        .payslip-header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .payslip-header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .payslip-header h2 {
            font-size: 14pt;
            font-weight: normal;
            margin-bottom: 3px;
        }
        
        .payslip-header p {
            font-size: 10pt;
            color: #666;
        }
        
        .payslip-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        /* Personnel Info */
        .personnel-info {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            background: #f9f9f9;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 180px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex-grow: 1;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11pt;
        }
        
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        table tr:hover {
            background: #f5f5f5;
        }
        
        .amount-col {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 30px;
            border-top: 2px solid #333;
            padding-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            font-size: 12pt;
        }
        
        .summary-row.total {
            background: #2c3e50;
            color: white;
            font-weight: bold;
            font-size: 14pt;
            margin-top: 10px;
            padding: 15px;
        }
        
        .summary-row.gross {
            background: #27ae60;
            color: white;
            font-weight: bold;
        }
        
        .summary-row.deductions {
            background: #e74c3c;
            color: white;
            font-weight: bold;
        }
        
        .summary-label {
            font-weight: bold;
        }
        
        .summary-amount {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        /* Footer */
        .payslip-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-line {
            border-top: 2px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-weight: bold;
        }
        
        .signature-label {
            font-size: 9pt;
            color: #666;
            margin-top: 3px;
        }
        
        /* Print Styles - Flexible page size support */
        @media print {
            body {
                background: white;
                padding: 0;
                max-width: 7.5in;
                margin: 0 auto;
            }
            
            @page {
                size: auto;
                margin: 0.5in;
            }
            
            .payslip-container {
                box-shadow: none;
                max-width: 100%;
                page-break-inside: avoid;
            }
            
            .no-print {
                display: none !important;
            }
            
            table tr:hover {
                background: transparent;
            }
        }
        
        /* Action Buttons */
        .action-buttons {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #ecf0f1;
            border-radius: 5px;
        }
        
        .action-buttons button,
        .action-buttons a {
            padding: 10px 20px;
            margin: 0 5px;
            font-size: 11pt;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-print {
            background: #3498db;
            color: white;
        }
        
        .btn-print:hover {
            background: #2980b9;
        }
        
        .btn-back {
            background: #95a5a6;
            color: white;
        }
        
        .btn-back:hover {
            background: #7f8c8d;
        }
        
        .empty-message {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        
        <!-- Action Buttons (No Print) -->
        <div class="action-buttons no-print">
            <button class="btn-print" onclick="window.print()">
                <i class="fa fa-print"></i> Print Payslip
            </button>
            <a href="list_personnel_income.php?dept=<?php echo urlencode($dept); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        
        <!-- Header -->
        <div class="payslip-header">
            <h1><?php echo htmlspecialchars($school_name); ?></h1>
            <h2><?php echo htmlspecialchars($division); ?></h2>
            <?php if ($region): ?>
                <p><?php echo htmlspecialchars($region); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="payslip-title">PAYSLIP</div>
        
        <!-- Personnel Information -->
        <div class="personnel-info">
            <div class="info-row">
                <span class="info-label">Employee Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($full_name); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Employee ID:</span>
                <span class="info-value"><?php echo htmlspecialchars($personnel_id); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Department:</span>
                <span class="info-value"><?php echo htmlspecialchars($personnel['department_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Position:</span>
                <span class="info-value"><?php echo htmlspecialchars($personnel['designation_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Employment Status:</span>
                <span class="info-value"><?php echo htmlspecialchars($personnel['employment_status'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Pay Period:</span>
                <span class="info-value">
                    <?php echo date('F d, Y', strtotime($pay_period_start)); ?> - 
                    <?php echo date('F d, Y', strtotime($pay_period_end)); ?>
                </span>
            </div>
        </div>
        
        <!-- INCOME SECTION -->
        <h3 style="margin-top: 30px; margin-bottom: 10px; color: #27ae60;">INCOME</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 60%;">Description</th>
                    <th style="width: 20%;">Code</th>
                    <th style="width: 20%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($income_items)): ?>
                    <?php foreach ($income_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['income_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['income_code']); ?></td>
                            <td class="amount-col">₱ <?php echo number_format($item['amount_per_pay'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty-message">No income items configured</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- DEDUCTIONS SECTION -->
        <h3 style="margin-top: 30px; margin-bottom: 10px; color: #e74c3c;">DEDUCTIONS</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 15%;">Code</th>
                    <th style="width: 17.5%; text-align: right;">Employee</th>
                    <th style="width: 17.5%; text-align: right;">Employer</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($deduction_items)): ?>
                    <?php foreach ($deduction_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['deduction_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['deduction_code']); ?></td>
                            <td class="amount-col">₱ <?php echo number_format($item['employee_amt'], 2); ?></td>
                            <td class="amount-col">₱ <?php echo number_format($item['employer_amt'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-message">No deductions configured</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- SUMMARY SECTION -->
        <div class="summary-section">
            <div class="summary-row gross">
                <span class="summary-label">TOTAL GROSS INCOME:</span>
                <span class="summary-amount">₱ <?php echo number_format($total_gross, 2); ?></span>
            </div>
            
            <div class="summary-row deductions">
                <span class="summary-label">TOTAL DEDUCTIONS:</span>
                <span class="summary-amount">₱ <?php echo number_format($total_deductions, 2); ?></span>
            </div>
            
            <div class="summary-row total">
                <span class="summary-label">NET PAY:</span>
                <span class="summary-amount">₱ <?php echo number_format($net_pay, 2); ?></span>
            </div>
            
            <?php if ($total_employer_share > 0): ?>
                <div class="summary-row" style="background: #95a5a6; color: white; margin-top: 10px;">
                    <span class="summary-label">Total Employer Contribution:</span>
                    <span class="summary-amount">₱ <?php echo number_format($total_employer_share, 2); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer / Signature Section -->
        <div class="payslip-footer">
            <p style="text-align: center; font-size: 9pt; color: #666; margin-bottom: 20px;">
                This is a system-generated payslip. Generated on <?php echo date('F d, Y h:i A'); ?>
            </p>
            
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"><?php echo htmlspecialchars($full_name); ?></div>
                    <div class="signature-label">Employee Signature / Date</div>
                </div>
                
                <div class="signature-box">
                    <div class="signature-line">Authorized Signatory</div>
                    <div class="signature-label">HR Manager / Date</div>
                </div>
            </div>
        </div>
        
    </div>
    
    <script>
        // Auto-print functionality (optional)
        <?php if (isset($_GET['auto_print']) && $_GET['auto_print'] == '1'): ?>
            window.onload = function() {
                window.print();
            };
        <?php endif; ?>
    </script>
</body>
</html>
