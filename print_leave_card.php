<?php
include('dbcon.php');
require_once('session.php');
require_once('official_duty.php');

$personnel_id = $_GET['personnel_id'];

// Get Personnel Details
$stmt = $conn->prepare("SELECT p.*, d.des_name as designation_name, do.dept_office_name as do_name, es.emp_stat_name as empStat_name 
                        FROM personnels p 
                        LEFT JOIN designation d ON p.des_id = d.des_id 
                        LEFT JOIN dept_offices do ON p.do_id = do.do_id 
                        LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id 
                        WHERE p.personnel_id = ?");
$stmt->execute([$personnel_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

$name = strtoupper($staff['lname'] . ', ' . $staff['fname'] . ' ' . $staff['mname']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="img/72123-mob_seal.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Leave Card - <?php echo $name; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0;
            color: #000;
            background-color: #faf5e1; /* Manila paper color */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: bold 22px;
        }
        .header-banner {
            position: relative;
            padding: 20px 30px;
            margin-bottom: 10px;
            height: 160px;
            overflow: hidden;
            border-bottom: 2px solid transparent;
            display: flex;
            align-items: center;
        }
        .header-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 15%;
            opacity: 0.4;
            z-index: 1;
        }
        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
        }
        .logo-img {
            width: 70px;
            height: 70px;
            margin-right: 20px;
        }
        .republic-text {
            font-size: 11px;
            line-height: 1.2;
        }
        .title-card {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 8px;
            margin-bottom: 10px;
            margin-top: -10px;
            position: relative;
            z-index: 2;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
            position: relative;
            z-index: 2;
        }
        .info-col {
            width: 32%;
        }
        .form-row {
            display: flex;
            margin-bottom: 4px;
            align-items: flex-end;
        }
        .form-label {
            font-weight: bold;
            margin-right: 10px;
            white-space: nowrap;
        }
        .form-value {
            flex-grow: 1;
            border-bottom: 1px solid #000;
            font-weight: bold;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            background: transparent;
            position: relative;
            z-index: 2;
        }
        th, td {
            border: 1px solid #000;
            text-align: center;
            padding: 1px 2px;
        }
        th {
            background-color: transparent;
            font-weight: bold;
        }
        /* Top Navigation Toolbar */
        .leave-card-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin: 12px 25px 16px 25px;
            padding: 10px 18px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            position: relative;
            z-index: 100;
        }
        .tb-left, .tb-center, .tb-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tb-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            line-height: 1.4;
            white-space: nowrap;
        }
        .tb-btn:hover {
            text-decoration: none;
            transform: translateY(-1px);
        }
        .tb-btn:active {
            transform: translateY(0);
        }
        .tb-btn-light {
            background: #f8fafc;
            color: #334155;
            border-color: #cbd5e1;
        }
        .tb-btn-light:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .tb-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .tb-chip i {
            color: #0284c7;
        }
        .tb-chip-name {
            color: #0f172a;
            font-weight: 700;
        }
        .tb-chip-sub {
            color: #64748b;
        }
        /* Segmented control for sorting order */
        .tb-segmented-control {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 3px;
            gap: 3px;
        }
        .tb-control-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            padding: 0 8px 0 6px;
        }
        .tb-segment {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 7px;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .tb-segment:hover {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.6);
            text-decoration: none;
        }
        .tb-segment.active {
            background: #0f172a;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        }
        .tb-segment.active i {
            color: #38bdf8;
        }
        /* Right action buttons */
        .tb-btn-excel {
            background: #107c41;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(16, 124, 65, 0.25);
        }
        .tb-btn-excel:hover {
            background: #0d6535;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 124, 65, 0.35);
        }
        .tb-btn-print {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
        }
        .tb-btn-print:hover {
            background: #0369a1;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        /* Suppress duplicate fixed button from universal_excel_export.php */
        .export-btn-container {
            display: none !important;
        }
        /* Certified True Copy Stamp */
        .certified-stamp-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 14px;
            margin-bottom: 8px;
            padding-right: 25px;
            position: relative;
            z-index: 2;
            page-break-inside: avoid;
        }
        .certified-stamp-box {
            width: 290px;
            border: 3.5px solid #0f2b48;
            border-radius: 10px;
            padding: 10px 14px 12px 14px;
            color: #0f2b48;
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
            background: transparent;
        }
        .stamp-title {
            text-align: center;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.5px;
            line-height: 1.25;
        }
        .stamp-field {
            display: flex;
            align-items: flex-end;
            margin-top: 12px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
        }
        .stamp-label {
            white-space: nowrap;
            padding-right: 4px;
            letter-spacing: 0.5px;
        }
        .stamp-dots {
            flex-grow: 1;
            border-bottom: 2px dotted #0f2b48;
            height: 12px;
            margin-bottom: 1px;
        }
        @media print {
            .leave-card-toolbar, .btn-print, .btn-order, .no-print, .export-btn-container { 
                display: none !important; 
            }
            @page { size: A4 landscape; margin: 5mm; }
            body { 
                background-color: transparent !important; /* let physical paper provide color */
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            .certified-stamp-box {
                border-color: #0f2b48 !important;
                color: #0f2b48 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .stamp-dots {
                border-bottom-color: #0f2b48 !important;
            }
        }
    </style>
</head>
<body>

<?php
$order = isset($_GET['order']) && strtolower(trim($_GET['order'])) === 'desc' ? 'desc' : 'asc';
?>

<div class="leave-card-toolbar no-print">
    <div class="tb-left">
        <a href="list_personnel_individual_details_LC.php?dept=<?php echo urlencode($staff['do_id'] ?? ''); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>" class="tb-btn tb-btn-light" title="Return to Personnel Leave Management">
            <i class="fa fa-arrow-left"></i> <span>Back to Leave Record</span>
        </a>
        <div class="tb-chip">
            <i class="fa fa-id-card-o"></i>
            <span class="tb-chip-name"><?php echo htmlspecialchars($staff['fname'] . ' ' . $staff['lname']); ?></span>
            <?php if (!empty($staff['do_name'])) { ?>
                <span class="tb-chip-sub">&bull; <?php echo htmlspecialchars($staff['do_name']); ?></span>
            <?php } ?>
        </div>
    </div>

    <div class="tb-center">
        <div class="tb-segmented-control">
            <span class="tb-control-label"><i class="fa fa-sort"></i> Order:</span>
            <a href="print_leave_card.php?personnel_id=<?php echo htmlspecialchars($personnel_id); ?>&order=asc" class="tb-segment <?php echo $order === 'asc' ? 'active' : ''; ?>" title="Oldest logs first (Chronological)">
                <i class="fa fa-sort-amount-asc"></i> <span>Start to Last (Asc)</span>
            </a>
            <a href="print_leave_card.php?personnel_id=<?php echo htmlspecialchars($personnel_id); ?>&order=desc" class="tb-segment <?php echo $order === 'desc' ? 'active' : ''; ?>" title="Newest logs first (Reverse chronological)">
                <i class="fa fa-sort-amount-desc"></i> <span>Newest First (Desc)</span>
            </a>
        </div>
    </div>

    <div class="tb-right">
        <button type="button" class="tb-btn tb-btn-excel" onclick="exportUniversalExcel(event)" title="Export Leave Card to Excel">
            <i class="fa fa-file-excel-o"></i> <span>Export to Excel</span>
        </button>
        <button type="button" class="tb-btn tb-btn-print" onclick="window.print()" title="Print Leave Card (A4 Landscape)">
            <i class="fa fa-print"></i> <span>Print Card</span>
        </button>
    </div>
</div>

<!-- Entire top section wrapper with background -->
<div class="top-section-wrapper" style="position: relative; overflow: hidden; padding: 15px 30px; padding-bottom: 5px;">
    
    <div style="position: relative; z-index: 2;">
        <table style="width: 100%; border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="width: 80px; border: none; text-align: left; vertical-align: middle;">
                    <img src="img/72123-mob_seal.png" class="logo-img" style="width: 65px; height: 65px;">
                </td>
                <td style="border: none; text-align: left; vertical-align: middle;">
                    <div class="republic-text" style="font-size: 12px; line-height: 1.2;">
                        <div>Republic of the Philippines</div>
                        <div>Province of Negros Occidental</div>
                        <div>Municipality of Binalbagan</div>
                        <div style="font-weight: bold; font-size: 11px;">HUMAN RESOURCE MANAGEMENT OFFICE</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="title-card" style="text-align: center; font-family: 'Arial Black', Arial, sans-serif; font-size: 26px; font-weight: 900; letter-spacing: 5px; margin-top: 15px; margin-bottom: 25px; color: #000; text-shadow: 0px 0px 1px #000;">LEAVE CARD</div>

        <div class="info-grid" style="display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 11px;">
            <div class="info-col" style="width: 32%;">
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 65px;">Name</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($name); ?></div>
                </div>
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 65px;">Position</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($staff['designation_name']); ?></div>
                </div>
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 65px;">Status</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($staff['empStat_name']); ?></div>
                </div>
            </div>
            
            <div class="info-col" style="width: 32%;">
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 105px;">Civil Status</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($staff['marital_status']); ?></div>
                </div>
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 105px;">Entrance to Duty</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($staff['appointment_date']); ?></div>
                </div>
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 105px;">Unit</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($staff['do_name']); ?></div>
                </div>
            </div>
            
            <div class="info-col" style="width: 32%;">
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 115px;">GSIS Policy No.</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($staff['gsis_num']); ?></div>
                </div>
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 115px;">TIN</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($staff['tin_num']); ?></div>
                </div>
                <div class="form-row" style="display: flex; margin-bottom: 3px; align-items: flex-end;">
                    <div class="form-label" style="font-weight: bold; margin-right: 10px; width: 115px;">Nat'l Ref. Card No.</div>
                    <div class="form-value" style="flex-grow: 1; border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th colspan="2" rowspan="2">PERIOD</th>
            <th rowspan="2">PARTICULARS</th>
            <th colspan="4">VACATION LEAVE</th>
            <th colspan="4">SICK LEAVE</th>
            <th rowspan="2">REMARKS</th>
        </tr>
        <tr>
            <th>EARNED</th>
            <th style="padding:0;">
                <div style="border-bottom: 1px solid #000; padding: 2px;">Absence<br>Undertime</div>
                <div style="padding: 2px;">w/ pay</div>
            </th>
            <th>BALANCE</th>
            <th style="padding:0;">
                <div style="border-bottom: 1px solid #000; padding: 2px;">Absence<br>Undertime</div>
                <div style="padding: 2px;">w/out pay</div>
            </th>
            <th>EARNED</th>
            <th style="padding:0;">
                <div style="border-bottom: 1px solid #000; padding: 2px;">Absence<br>Undertime</div>
                <div style="padding: 2px;">w/ pay</div>
            </th>
            <th>BALANCE</th>
            <th style="padding:0;">
                <div style="border-bottom: 1px solid #000; padding: 2px;">Absence<br>Undertime</div>
                <div style="padding: 2px;">w/out pay</div>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (function_exists('syncPersonnelUnloggedLeaves')) {
            syncPersonnelUnloggedLeaves($conn, $personnel_id);
        }
        $log_query = $conn->prepare("SELECT * FROM leave_credit_logs WHERE personnel_id=? ORDER BY log_id ASC");
        $log_query->execute([$personnel_id]);
        $logs = $log_query->fetchAll(PDO::FETCH_ASSOC);
        $logs = array_values(array_filter($logs, function ($log) {
            if (isZeroValueOfficialDutyLeaveLog($log)) {
                return false;
            }
            // Exclude legacy pre-2026 RFID leave applications (keep records encoded in the new system)
            if ($log['log_type'] === 'Leave Application' && stripos($log['remarks'], 'Applied for') !== false && stripos($log['remarks'], '[HR Encoded]') === false) {
                $p = trim(explode(' - ', $log['period'])[0]);
                $ts = strtotime(str_replace('-', '/', $p));
                $y = $ts ? (int)date('Y', $ts) : 0;
                if ($y > 0 && $y < 2026) {
                    return false;
                }
            }
            return true;
        }));

usort($logs, function($a, $b) {
    // Get start date of period
    $a_p = trim(explode(' - ', $a['period'])[0]);
    $b_p = trim(explode(' - ', $b['period'])[0]);
    $tsA = strtotime(str_replace('-', '/', $a_p));
    $tsB = strtotime(str_replace('-', '/', $b_p));
    
    // If invalid date, fallback to 0
    if(!$tsA) $tsA = 0;
    if(!$tsB) $tsB = 0;
    
    // Primary sort by date, secondary sort by log_id (so manual adjustments on same day go after accruals)
    if ($tsA == $tsB) {
        return $a['log_id'] <=> $b['log_id'];
    }
    return $tsA <=> $tsB;
});

        $vl_run = 0.000;
        $sl_run = 0.000;
        foreach ($logs as $key => $log) {
            $vl_run += (float)$log['vl_amount'];
            if ($vl_run < 0) $vl_run = 0;
            $sl_run += (float)$log['sl_amount'];
            if ($sl_run < 0) $sl_run = 0;
            
            $logs[$key]['vl_run_calc'] = $vl_run;
            $logs[$key]['sl_run_calc'] = $sl_run;
        }

        $has_terminal_leave = false;
        foreach ($logs as $l) {
            if (stripos($l['particulars'] ?? '', 'terminal') !== false ||
                stripos($l['log_type'] ?? '', 'terminal') !== false ||
                stripos($l['remarks'] ?? '', 'terminal') !== false) {
                $has_terminal_leave = true;
                break;
            }
        }
        if (!$has_terminal_leave) {
            $chk_tl_log = $conn->prepare("SELECT log_id FROM leave_credit_logs WHERE personnel_id = ? AND (particulars LIKE '%terminal%' OR log_type LIKE '%terminal%' OR remarks LIKE '%terminal%') LIMIT 1");
            $chk_tl_log->execute([$personnel_id]);
            if ($chk_tl_log->fetch()) {
                $has_terminal_leave = true;
            }
        }
        if (!$has_terminal_leave) {
            $chk_tl_app = $conn->prepare("SELECT lap_id FROM leave_applicants WHERE applicant_id = ? AND (leave_type LIKE '%terminal%' OR leave_type_desc LIKE '%terminal%') LIMIT 1");
            $chk_tl_app->execute([$personnel_id]);
            if ($chk_tl_app->fetch()) {
                $has_terminal_leave = true;
            }
        }

        // Default order is Ascending (from the start date to last date)
        if ($order === 'desc') {
            $logs = array_reverse($logs);
        }

        $rowCount = count($logs);
        $minRows = $has_terminal_leave ? 20 : 28; // Adjusted for landscape height with stamp
        
        foreach($logs as $log) {
            $log_type_clean = strtolower(trim((string)$log['log_type']));
            $particulars_clean = strtolower(trim((string)$log['particulars']));
            $is_monthly_earn = ($log_type_clean === 'monthly earn' || $particulars_clean === 'accrual' || stripos((string)$log['remarks'], 'auto-accrual') !== false);

            $period = !empty($log['period']) ? $log['period'] : date('m-d-y', strtotime($log['date_logged']));
            $particulars = !empty($log['particulars']) ? $log['particulars'] : $log['log_type'];

            $is_special_leave = isNonDeductibleSpecialLeave($particulars, $log['remarks']);

            $vl_earned = ($is_monthly_earn && $log['vl_amount'] > 0) ? $log['vl_amount'] : '';
            $vl_w_pay = (!empty($log['vl_w_pay']) && (float)$log['vl_w_pay'] > 0) ? (float)$log['vl_w_pay'] : (($log['vl_amount'] < 0) ? abs($log['vl_amount']) : '');
            $vl_wout_pay = (!empty($log["vl_wout_pay"]) && (float)$log["vl_wout_pay"] > 0) ? $log["vl_wout_pay"] : "";

            $sl_earned = ($is_monthly_earn && $log['sl_amount'] > 0) ? $log['sl_amount'] : '';
            $sl_w_pay = (!empty($log['sl_w_pay']) && (float)$log['sl_w_pay'] > 0) ? (float)$log['sl_w_pay'] : (($log['sl_amount'] < 0) ? abs($log['sl_amount']) : '');
            $sl_wout_pay = (!empty($log["sl_wout_pay"]) && (float)$log["sl_wout_pay"] > 0) ? $log["sl_wout_pay"] : "";

            // Approved leave applications are paid leaves: tanan sa absence with pay, zero sa without pay
            if ($log_type_clean === 'leave application' || stripos($particulars_clean, 'leave') !== false) {
                if (!empty($vl_wout_pay) && (float)$vl_wout_pay > 0) {
                    $vl_w_pay = ((float)$vl_w_pay > 0 ? (float)$vl_w_pay : 0) + (float)$vl_wout_pay;
                    $vl_wout_pay = '';
                }
                if (!empty($sl_wout_pay) && (float)$sl_wout_pay > 0) {
                    $sl_w_pay = ((float)$sl_w_pay > 0 ? (float)$sl_w_pay : 0) + (float)$sl_wout_pay;
                    $sl_wout_pay = '';
                }
            }

            // Non-deductible special leaves (Wellness, Maternity, Paternity, Solo Parent, Rehab, Emergency)
            // reflect their days with pay without deducting from running balances
            if ($is_special_leave && (float)$log['vl_amount'] == 0 && (float)$log['sl_amount'] == 0) {
                $days = getSpecialLeaveDays($log, $conn);
                if (!empty($log['sl_w_pay']) && (float)$log['sl_w_pay'] > 0) {
                    $sl_w_pay = (float)$log['sl_w_pay'];
                } else {
                    $vl_w_pay = $days;
                }
            }

            $vl_run = $log['vl_run_calc'];
            $sl_run = $log['sl_run_calc'];
        ?>
        <tr>
            <?php 
    $period_from = $period;
    $period_to = '';
    if(strpos($period, ' - ') !== false) {
        $p_parts = explode(' - ', $period);
        $period_from = trim($p_parts[0]);
        $period_to = trim($p_parts[1]);
    }
    
    // If period_to is empty, automatically compute appropriate end date
    if ($period_to === '' && $period_from !== '') {
        if(strtolower($log['log_type']) == 'leave application' || stripos($log['particulars'], 'leave') !== false) {
            $period_to = $period_from;
        } else {
            $clean_from = str_replace('-', '/', $period_from);
            $ts_from = strtotime($clean_from);
            if ($ts_from) {
                $sep = (strpos($period_from, '/') !== false) ? '/' : '-';
                $p_parts = explode($sep, $period_from);
                if (isset($p_parts[0]) && strlen(trim($p_parts[0])) == 4) {
                    $period_to = date("Y{$sep}m{$sep}t", $ts_from);
                } else {
                    $m_len = isset($p_parts[0]) ? strlen(trim($p_parts[0])) : 2;
                    $y_len = isset($p_parts[2]) ? strlen(trim($p_parts[2])) : (isset($p_parts[1]) ? strlen(trim($p_parts[1])) : 2);
                    $m_fmt = ($m_len == 1) ? 'n' : 'm';
                    $y_fmt = ($y_len == 4) ? 'Y' : 'y';
                    $period_to = date("{$m_fmt}{$sep}t{$sep}{$y_fmt}", $ts_from);
                }
            }
        }
    }
    $remarks_display = htmlspecialchars($log['remarks']);
    // Remove "Manual adjustment by HR" from remarks case-insensitively
    $remarks_display = trim(str_ireplace('Manual adjustment by HR', '', $remarks_display));
    // Remove LWOP text from remarks for leave applications
    $remarks_display = trim(preg_replace('/\s*\(Plus\s+[\d\.]+\s+LWOP\)/i', '', $remarks_display));
    
    // Automatically display Certified Correct for Terminal Leave if empty or generic
    if (stripos($particulars, 'terminal') !== false || stripos($log['log_type'], 'terminal') !== false || stripos($remarks_display, 'terminal') !== false) {
        if ($remarks_display === '' || stripos($remarks_display, 'applied for') !== false || stripos($remarks_display, 'encoded') !== false || stripos($remarks_display, 'certified correct') !== false) {
            $remarks_display = 'Certified Correct';
        }
    }
    
    // Remove "Balance Forwarded" from particulars if it's a manual entry
    $particulars_display = htmlspecialchars($particulars);
    if (strtolower($log['log_type']) == 'manual adjustment') {
        $particulars_display = trim(str_ireplace('Balance Forwarded', '', $particulars_display));
    }
?>
            <td><?php echo htmlspecialchars($period_from); ?></td>
            <td><?php echo htmlspecialchars($period_to); ?></td>
            <td><?php echo $particulars_display; ?></td>
            <td><?php echo $vl_earned ? number_format($vl_earned, 3) : ''; ?></td>
            <td><?php echo $vl_w_pay ? number_format($vl_w_pay, 3) : ''; ?></td>
            <td style="font-weight: bold;"><?php echo number_format($vl_run, 3); ?></td>
            <td><?php echo $vl_wout_pay ? number_format($vl_wout_pay, 3) : ''; ?></td>
            
            <td><?php echo $sl_earned ? number_format($sl_earned, 3) : ''; ?></td>
            <td><?php echo $sl_w_pay ? number_format($sl_w_pay, 3) : ''; ?></td>
            <td style="font-weight: bold;"><?php echo number_format($sl_run, 3); ?></td>
            <td><?php echo $sl_wout_pay ? number_format($sl_wout_pay, 3) : ''; ?></td>
            
            <td style="text-align: left; max-width: 250px; white-space: normal;"><small><?php echo $remarks_display; ?></small></td>
        </tr>
        <?php } ?>
        
        <?php 
        for($i = $rowCount; $i < $minRows; $i++) {
            echo "<tr>
                <td style='height: 13px;'></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<?php if ($has_terminal_leave) { ?>
<div class="certified-stamp-container">
    <div class="certified-stamp-box">
        <div class="stamp-title">CERTIFIED TRUE COPY OF</div>
        <div class="stamp-title">AN ORIGINAL DOCUMENT</div>
        <div class="stamp-field">
            <span class="stamp-label">SIGNED</span>
            <span class="stamp-dots"></span>
        </div>
        <div class="stamp-field">
            <span class="stamp-label">DATE</span>
            <span class="stamp-dots"></span>
        </div>
    </div>
</div>
<?php } ?>

<?php 
$hide_floating_excel_btn = true;
include_once('universal_excel_export.php'); 
?>
</body>
</html>

