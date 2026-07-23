<?php
include('session.php');

// Get leave application ID from URL
$leave_application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$leave_application_id) {
    die('Invalid leave application ID');
}

// Fetch leave application data
$stmt = $conn->prepare("
    SELECT la.*, 
           p.fname, p.mname, p.lname, p.suffix,
           d.des_name as position,
           do.dept_office_name as office_agency,
           sr.monthly_salary
    FROM leave_applications la
    LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
    LEFT JOIN designation d ON p.des_id = d.des_id
    LEFT JOIN dept_offices do ON p.do_id = do.do_id
    LEFT JOIN service_record sr ON p.personnel_id = sr.personnel_id AND sr.appointDate_status = 'Active'
    WHERE la.id = :id
");
$stmt->execute([':id' => $leave_application_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Leave application not found');
}

// Fetch signatories
$sig_stmt = $conn->prepare("SELECT * FROM signatories_settings WHERE id = 1");
$sig_stmt->execute();
$signatories = $sig_stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Get institution preferences (already loaded in dbcon.php via session.php, but fetch again for clarity)
$inst_stmt = $conn->prepare("SELECT * FROM institution_preferences WHERE id = 1");
$inst_stmt->execute();
$institution = $inst_stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// ====================================
// USE STORED SNAPSHOT VALUES FROM leave_applications TABLE
// These values were captured when the application was created/approved
// This ensures reprinting shows the SAME values as the original print
// ====================================

// Get stored deduction values
$vl_this_application = floatval($data['less_application_vl'] ?? 0);
$sl_this_application = floatval($data['less_application_sl'] ?? 0);

// Get stored balance values (snapshot at time of application)
$stored_total_earned_vl = $data['total_earned_vl'] ?? null;
$stored_total_earned_sl = $data['total_earned_sl'] ?? null;
$stored_balance_vl = $data['balance_vl'] ?? null;
$stored_balance_sl = $data['balance_sl'] ?? null;

// Check if stored values exist
$has_stored_values = ($stored_total_earned_vl !== null && $stored_total_earned_vl !== '' && 
                      $stored_balance_vl !== null && $stored_balance_vl !== '') ||
                     ($stored_total_earned_sl !== null && $stored_total_earned_sl !== '' && 
                      $stored_balance_sl !== null && $stored_balance_sl !== '');

if ($has_stored_values) {
    // Use stored snapshot values - this ensures reprinting shows consistent data
    $vl_total_earned = floatval($stored_total_earned_vl);
    $sl_total_earned = floatval($stored_total_earned_sl);
    $vl_balance = floatval($stored_balance_vl);
    $sl_balance = floatval($stored_balance_sl);
} else {
    // Fall back to calculation from leave_card for old records without stored values
    $balance_stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(vl_earned), 0) as vl_total_earned,
            COALESCE(SUM(sl_earned), 0) as sl_total_earned,
            COALESCE(SUM(vl_earned), 0) - COALESCE(SUM(vl_with_pay), 0) - COALESCE(SUM(vl_without_pay), 0) as vl_balance,
            COALESCE(SUM(sl_earned), 0) - COALESCE(SUM(sl_with_pay), 0) - COALESCE(SUM(sl_without_pay), 0) as sl_balance
        FROM leave_card
        WHERE personnel_id = :personnel_id
    ");
    $balance_stmt->execute([':personnel_id' => $data['personnel_id']]);
    $balance = $balance_stmt->fetch(PDO::FETCH_ASSOC);
    
    $vl_total_earned = floatval($balance['vl_total_earned'] ?? 0);
    $sl_total_earned = floatval($balance['sl_total_earned'] ?? 0);
    
    // Add back this application's deduction to show balance BEFORE this application
    $vl_balance = floatval($balance['vl_balance'] ?? 0) + $vl_this_application;
    $sl_balance = floatval($balance['sl_balance'] ?? 0) + $sl_this_application;
}


// Build full name
$full_name = $data['fname'] . ' ';
if (!empty($data['mname'])) {
    $full_name .= substr($data['mname'], 0, 1) . '. ';
}
$full_name .= $data['lname'];
if (!empty($data['suffix']) && $data['suffix'] != '-') {
    $full_name .= ' ' . $data['suffix'];
}

// Helper function
function formatDate($dateString) {
    if (!$dateString) return '';
    return date('F j, Y', strtotime($dateString));
}

// Determine leave type
$leaveTypeLower = strtolower($data['leave_type'] ?? '');
function isLeaveType($keywords, $leaveType) {
    foreach ($keywords as $keyword) {
        if (strpos($leaveType, $keyword) !== false) return true;
    }
    return false;
}

$favicon_path = '';
if (!empty($institution['logo']) && file_exists('img/' . $institution['logo'])) {
    $favicon_path = 'img/' . rawurlencode($institution['logo']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS Form No. 6 - Leave Application</title>
    <?php if (!empty($favicon_path)): ?>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon_path) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= htmlspecialchars($favicon_path) ?>">
    <?php endif; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            background: #f0f0f0;
        }
        
        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #2c3e50;
            padding: 10px 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .toolbar button {
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-print {
            background: #3498db;
            color: white;
        }
        
        .btn-download {
            background: #27ae60;
            color: white;
        }
        
        .btn-close {
            background: #e74c3c;
            color: white;
            margin-left: auto;
        }
        
        .toolbar button:hover {
            opacity: 0.9;
        }
        
        .page-container {
            margin-top: 60px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        
        .cs-form {
            width: 8.5in;
            min-height: 11in;
            background: white;
            padding: 0.5in;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        
        .form-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }
        
        .main-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10px;
            line-height: 1.3;
        }
        
        .section-header {
            background-color: #d0d0d0;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 3px !important;
        }
        
        .chk {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 4px;
            vertical-align: middle;
            text-align: center;
            line-height: 10px;
            font-size: 11px;
        }
        
        .chk.checked::after {
            content: '✓';
            font-weight: bold;
        }
        
        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 100px;
            padding: 0 5px;
        }
        
        .leave-item {
            margin: 2px 0;
            line-height: 1.4;
        }
        
        .leave-ref {
            font-size: 8px;
            color: #333;
        }
        
        .sig-block {
            text-align: center;
            margin-top: 10px;
        }
        
        .sig-line {
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 180px;
            margin-top: 14px;
            padding-top: 3px;
            font-weight: bold;
        }
        
        .sig-title {
            font-size: 9px;
        }
        
        .credit-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        
        .credit-table td, .credit-table th {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
            font-size: 10px;
        }
        
        @media print {
            .toolbar {
                display: none !important;
            }
            
            .page-container {
                margin-top: 0;
                padding: 0;
            }
            
            .cs-form {
                box-shadow: none;
                width: 100%;
                padding: 0.25in;
            }
            
            body {
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print
        </button>
        <button class="btn-download" onclick="downloadPDF()">
            📥 Download PDF
        </button>
        <button class="btn-close" onclick="window.close()">
            ✕ Close
        </button>
    </div>
    
    <div class="page-container">
        <div class="cs-form" id="cs-form">
            <!-- CS Form Header - 3 Column Layout -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                <!-- Left: CS Form Number and Logo -->
                <div style="width: 25%; text-align: left;">
                    <div style="font-size: 9px; font-style: italic;">Civil Service Form No. 6</div>
                    <div style="font-size: 8px; font-style: italic;">Revised 2020</div>
                    <div style="margin-top: 10px;">
                        <?php if (!empty($institution['logo']) && file_exists('img/' . $institution['logo'])): ?>
                            <img src="img/<?= htmlspecialchars($institution['logo']) ?>" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; border: 2px solid #999; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #999; text-align: center; line-height: 1.2;">
                                AGENCY<br>LOGO
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Center: Agency Info -->
                <div style="width: 50%; text-align: center;">
                    <div style="font-size: 10px; font-weight: bold;">Republic of the Philippines</div>
                    <div style="font-size: 10px; font-weight: bold;"><?= htmlspecialchars($institution['institution_name'] ?? 'Agency Name') ?></div>
                    <div style="font-size: 9px;"><?= htmlspecialchars($institution['address'] ?? 'Agency Address') ?></div>
                </div>
                
                <!-- Right: Annex and Stamp -->
                <div style="width: 25%; text-align: right;">
                    <div style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">ANNEX A</div>
                    <div style="border: 1px solid #000; padding: 8px; font-size: 8px; text-align: center; min-height: 40px; display: flex; align-items: center; justify-content: center;">
                        Stamp of Date of Receipt
                    </div>
                </div>
            </div>
            
            <div class="form-title">APPLICATION FOR LEAVE</div>
            
            <table class="main-table">
                <colgroup>
                    <col style="width: 27.5%;">
                    <col style="width: 27.5%;">
                    <col style="width: 22.5%;">
                    <col style="width: 22.5%;">
                </colgroup>
                <!-- Row 1: Office/Dept and Name -->
                <tr>
                    <td style="width: 50%;" colspan="2">
                        <strong>1. OFFICE/DEPARTMENT</strong><br>
                        <span style="margin-left: 15px;"><?= htmlspecialchars($data['office_agency'] ?? '') ?></span>
                    </td>
                    <td style="width: 50%;" colspan="2">
                        <strong>2. NAME:</strong>
                        <span style="margin-left: 20px;">(Last)</span>
                        <span style="margin-left: 50px;">(First)</span>
                        <span style="margin-left: 50px;">(Middle)</span><br>
                        <span style="margin-left: 50px;"><strong><?= htmlspecialchars($data['lname'] ?? '') ?></strong></span>
                        <span style="margin-left: 20px;"><strong><?= htmlspecialchars($data['fname'] ?? '') ?></strong></span>
                        <span style="margin-left: 20px;"><strong><?= htmlspecialchars($data['mname'] ?? '') ?></strong></span>
                    </td>
                </tr>
                
                <!-- Row 2: Date, Position, Salary -->
                <tr>
                    <td>
                        <strong>3. DATE OF FILING</strong><br>
                        <span class="underline"><?= formatDate($data['application_date']) ?></span>
                    </td>
                    <td>
                        <strong>4. POSITION</strong><br>
                        <span class="underline"><?= htmlspecialchars($data['position'] ?? '') ?></span>
                    </td>
                    <td colspan="2">
                        <strong>5. SALARY</strong><br>
                        <span class="underline"><?= htmlspecialchars($data['monthly_salary'] ?? '') ?></span>
                    </td>
                </tr>
                
                <!-- Section 6 Header -->
                <tr>
                    <td colspan="4" class="section-header">6. DETAILS OF APPLICATION</td>
                </tr>
                
                <!-- Row: Type of Leave and Details -->
                <tr>
                    <td colspan="2" style="width: 55%; vertical-align: top;">
                        <strong>6.A TYPE OF LEAVE TO BE AVAILED OF</strong><br>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['vacation'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Vacation Leave <span class="leave-ref">(Sec. 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['mandatory', 'forced'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Mandatory/Forced Leave <span class="leave-ref">(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['sick'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Sick Leave <span class="leave-ref">(Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['maternity'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Maternity Leave <span class="leave-ref">(R.A. No. 11210 / IRR issued by CSC, DOH, DOLE and SSS)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['paternity'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Paternity Leave <span class="leave-ref">(R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['special privilege'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Special Privilege Leave <span class="leave-ref">(Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['solo parent'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Solo Parent Leave <span class="leave-ref">(RA No. 8972 / CSC MC No. 8, s. 2004)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['study'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Study Leave <span class="leave-ref">(Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['vawc', '10-day'], $leaveTypeLower) ? 'checked' : '' ?>"></span>10-Day VAWC Leave <span class="leave-ref">(RA No. 9262 / CSC MC No. 15, s. 2005)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['rehabilitation'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Rehabilitation Privilege <span class="leave-ref">(Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['women', 'gynecological'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Special Leave Benefits for Women <span class="leave-ref">(RA No. 9710 / CSC MC No. 25, s. 2010)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['calamity', 'emergency'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Special Emergency (Calamity) Leave <span class="leave-ref">(CSC MC No. 2, s. 2012, as amended)</span></div>
                        <div class="leave-item"><span class="chk <?= isLeaveType(['adoption'], $leaveTypeLower) ? 'checked' : '' ?>"></span>Adoption Leave <span class="leave-ref">(R.A. No. 8552)</span></div>
                        <div class="leave-item" style="margin-top: 5px;"><em>Others:</em></div>
                        <div class="underline" style="width: 85%; margin-left: 20px;"></div>
                    </td>
                    <td colspan="2" style="width: 45%; vertical-align: top;">
                        <strong>6.B DETAILS OF LEAVE</strong><br><br>
                        <div style="margin-bottom: 8px;">
                            <em>In case of Vacation/Special Privilege Leave:</em>
                            <div style="margin-left: 10px;"><span class="chk"></span> Within the Philippines <span class="underline" style="min-width: 60px;"></span></div>
                            <div style="margin-left: 10px;"><span class="chk"></span> Abroad (Specify) <span class="underline" style="min-width: 60px;"></span></div>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <em>In case of Sick Leave:</em>
                            <div style="margin-left: 10px;"><span class="chk"></span> In Hospital (Specify Illness) <span class="underline" style="min-width: 40px;"></span></div>
                            <div style="margin-left: 10px;"><span class="chk"></span> Out Patient (Specify Illness) <span class="underline" style="min-width: 40px;"></span></div>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <em>In case of Special Leave Benefits for Women:</em>
                            <div style="margin-left: 10px;">(Specify Illness) <span class="underline" style="min-width: 100px;"></span></div>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <em>In case of Study Leave:</em>
                            <div style="margin-left: 10px;"><span class="chk"></span> Completion of Master's Degree</div>
                            <div style="margin-left: 10px;"><span class="chk"></span> BAR/Board Examination Review</div>
                        </div>
                        <div>
                            <em>Other purpose:</em>
                            <div style="margin-left: 10px;"><span class="chk <?= isLeaveType(['monetiz'], $leaveTypeLower) ? 'checked' : '' ?>"></span> Monetization of Leave Credits</div>
                            <div style="margin-left: 10px;"><span class="chk <?= isLeaveType(['terminal'], $leaveTypeLower) ? 'checked' : '' ?>"></span> Terminal Leave</div>
                        </div>
                    </td>
                </tr>
                
                <!-- Row: Number of Days and Commutation -->
                <tr>
                    <td colspan="2" style="vertical-align: top;">
                        <strong>6.C NUMBER OF WORKING DAYS APPLIED FOR</strong><br>
                        <div class="underline" style="width: 70%; text-align: center; margin: 5px auto;"><strong><?= $data['number_of_days'] ?? '' ?></strong></div>
                        <strong>INCLUSIVE DATES</strong><br>
                        <?php
                        $inclusiveDates = formatDate($data['inclusive_date_from']);
                        if (!empty($data['inclusive_date_to']) && $data['inclusive_date_to'] !== $data['inclusive_date_from']) {
                            $inclusiveDates .= ' - ' . formatDate($data['inclusive_date_to']);
                        }
                        ?>
                        <div class="underline" style="width: 70%; text-align: center; margin: 5px auto;"><?= $inclusiveDates ?></div>
                    </td>
                    <td colspan="2" style="vertical-align: top;">
                        <strong>6.D COMMUTATION</strong><br>
                        <div style="margin: 5px 0;"><span class="chk"></span> Not Requested</div>
                        <div style="margin: 5px 0;"><span class="chk checked"></span> Requested</div>
                        <div class="sig-block" style="margin-top: 20px;">
                            <div class="sig-line"><?= htmlspecialchars($full_name) ?></div><br>
                            <span class="sig-title">(Signature of Applicant)</span>
                        </div>
                    </td>
                </tr>
                
                <!-- Section 7 Header -->
                <tr>
                    <td colspan="4" class="section-header">7. DETAILS OF ACTION ON APPLICATION</td>
                </tr>
                
                <!-- Row: Certification and Recommendation -->
                <tr>
                    <td colspan="2" style="vertical-align: top;">
                        <strong>7.A CERTIFICATION OF LEAVE CREDITS</strong><br>
                        <div style="text-align: center; margin: 5px 0;">As of <span class="underline"><?= formatDate($data['application_date']) ?></span></div>
                        <table class="credit-table">
                            <tr>
                                <th></th>
                                <th>Vacation Leave</th>
                                <th>Sick Leave</th>
                            </tr>
                            <tr>
                                <td style="text-align: left;"><em>Total Earned</em></td>
                                <td><?= number_format($vl_total_earned, 3) ?></td>
                                <td><?= number_format($sl_total_earned, 3) ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;"><em>Less this application</em></td>
                                <td><?= number_format($vl_this_application, 3) ?></td>
                                <td><?= number_format($sl_this_application, 3) ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;"><em>Balance</em></td>
                                <td><?= number_format($vl_balance, 3) ?></td>
                                <td><?= number_format($sl_balance, 3) ?></td>
                            </tr>
                        </table>
                        <div class="sig-block">
                            <div class="sig-line"><?= htmlspecialchars($signatories['hrmo_name'] ?? '') ?></div><br>
                            <span class="sig-title"><?= htmlspecialchars($signatories['hrmo_position'] ?? 'Human Resource Management Officer') ?></span>
                        </div>
                    </td>
                    <td colspan="2" style="vertical-align: top;">
                        <strong>7.B RECOMMENDATION</strong><br>
                        <div style="margin: 8px 0;"><span class="chk checked"></span> For approval</div>
                        <div style="margin: 8px 0;"><span class="chk"></span> For disapproval due to <span class="underline" style="min-width: 80px;"></span></div>
                        <div class="underline" style="width: 85%; margin-left: 20px;"></div>
                        <div class="sig-block" style="margin-top: 15px;">
                            <div class="sig-line"><?= htmlspecialchars($signatories['recommending_name'] ?? '') ?></div><br>
                            <span class="sig-title"><?= htmlspecialchars($signatories['recommending_position'] ?? '') ?></span>
                        </div>
                    </td>
                </tr>
                
                <!-- Row: Approved For and Disapproved -->
                <?php
                $daysWithPay = $vl_this_application + $sl_this_application;
                $daysWithoutPay = floatval($data['less_application_vl_without_pay'] ?? 0) + floatval($data['less_application_sl_without_pay'] ?? 0);
                if ($daysWithPay == 0 && $daysWithoutPay == 0) {
                    $daysWithPay = floatval($data['number_of_days'] ?? 0);
                }
                ?>
                <tr>
                    <td colspan="2" style="vertical-align: top;">
                        <strong>7.C APPROVED FOR:</strong><br>
                        <div style="margin: 5px 0;"><span class="underline" style="min-width: 50px; text-align: center;"><?= $daysWithPay > 0 ? number_format($daysWithPay, 1) : '' ?></span> days with pay</div>
                        <div style="margin: 5px 0;"><span class="underline" style="min-width: 50px; text-align: center;"><?= $daysWithoutPay > 0 ? number_format($daysWithoutPay, 1) : '' ?></span> days without pay</div>
                        <div style="margin: 5px 0;">others (Specify) <span class="underline" style="min-width: 60px;"></span></div>
                        <div class="sig-block" style="margin-top: 10px;">
                            <div class="sig-line"><?= htmlspecialchars($signatories['approving_name'] ?? '') ?></div><br>
                            <span class="sig-title"><?= htmlspecialchars($signatories['approving_position'] ?? 'Municipal Administrator') ?></span>
                        </div>
                    </td>
                    <td colspan="2" style="vertical-align: top;">
                        <strong>7.D DISAPPROVED DUE TO:</strong><br>
                        <div class="underline" style="width: 90%; margin: 8px 0;"></div>
                        <div class="underline" style="width: 90%; margin: 8px 0;"></div>
                        <div style="margin-top: 15px;"><strong>Approved:</strong></div>
                        <div class="sig-block" style="margin-top: 10px;">
                            <div class="sig-line"><?= htmlspecialchars($signatories['mayor_name'] ?? '') ?></div><br>
                            <span class="sig-title"><?= htmlspecialchars($signatories['mayor_position'] ?? 'Municipal Mayor') ?></span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            var element = document.getElementById('cs-form');
            var filename = 'CS_Form6_Leave_Application_<?= date('Ymd_Hi') ?>_<?= strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['lname'] ?? '')) ?>_<?= strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['fname'] ?? '')) ?>.pdf';
            
            var opt = {
                margin: [0.3, 0.3, 0.3, 0.3],
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                },
                jsPDF: { 
                    unit: 'in', 
                    format: 'letter', 
                    orientation: 'portrait' 
                }
            };
            
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
