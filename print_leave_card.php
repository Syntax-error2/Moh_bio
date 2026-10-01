<?php
include('dbcon.php');
require_once('session.php');

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
    <link rel="shortcut icon" href="img/moh seal.jpg">
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
        .btn-print {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            float: right;
            margin-bottom: 10px;
            position: relative;
            z-index: 10;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: 0.2s;
        }
        .btn-print:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        @media print {
            .btn-print { display: none; }
            @page { size: A4 landscape; margin: 5mm; }
            body { 
                background-color: transparent !important; /* let physical paper provide color */
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

<div class="clearfix" style="padding-top: 10px; padding-right: 10px;"><a href="javascript:window.print()" class="btn-print" title="Print this Card"><i class="fa fa-print"></i></a></div>

<!-- Entire top section wrapper with background -->
<div class="top-section-wrapper" style="position: relative; overflow: hidden; padding: 15px 30px; padding-bottom: 5px;">
    
    <div style="position: relative; z-index: 2;">
        <table style="width: 100%; border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="width: 80px; border: none; text-align: left; vertical-align: middle;">
                    <img src="img/moh seal.jpg" class="logo-img" style="width: 65px; height: 65px;">
                </td>
                <td style="border: none; text-align: left; vertical-align: middle;">
                    <div class="republic-text" style="font-size: 12px; line-height: 1.2;">
                        <div>Republic of the Philippines</div>
                        <div>Province of Negros Occidental</div>
                        <div>Municipality of Hinoba-an</div>
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
        $log_query = $conn->prepare("SELECT * FROM leave_credit_logs WHERE personnel_id=? ORDER BY log_id ASC");
        $log_query->execute([$personnel_id]);
        $logs = $log_query->fetchAll(PDO::FETCH_ASSOC);

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

        // Reverse the array to show descending by date
        $logs = array_reverse($logs);

        $rowCount = count($logs);
        $minRows = 28; // Adjusted for landscape height
        
        foreach($logs as $log) {
            $vl_earned = ($log['vl_amount'] > 0) ? $log['vl_amount'] : '';
            $vl_w_pay = ($log['vl_amount'] < 0) ? abs($log['vl_amount']) : '';
            $vl_wout_pay = (!empty($log["vl_wout_pay"]) && $log["vl_wout_pay"] > 0) ? $log["vl_wout_pay"] : "";

            $sl_earned = ($log['sl_amount'] > 0) ? $log['sl_amount'] : '';
            $sl_w_pay = ($log['sl_amount'] < 0) ? abs($log['sl_amount']) : '';
            $sl_wout_pay = (!empty($log["sl_wout_pay"]) && $log["sl_wout_pay"] > 0) ? $log["sl_wout_pay"] : "";

            $vl_run = $log['vl_run_calc'];
            $sl_run = $log['sl_run_calc'];
            
            $period = !empty($log['period']) ? $log['period'] : date('m-d-y', strtotime($log['date_logged']));
            $particulars = !empty($log['particulars']) ? $log['particulars'] : $log['log_type'];
        ?>
        <tr>
            <?php 
    $period_from = $period;
    $period_to = '';
    if(strpos($period, ' - ') !== false) {
        $p_parts = explode(' - ', $period);
        $period_from = trim($p_parts[0]);
        $period_to = trim($p_parts[1]);
    } else {
        if(strtolower($log['log_type']) == 'leave application' || stripos($log['particulars'], 'leave') !== false) {
            $period_to = $period_from;
        }
    }
    $remarks_display = htmlspecialchars($log['remarks']);
    // Remove "Manual adjustment by HR" from remarks case-insensitively
    $remarks_display = trim(str_ireplace('Manual adjustment by HR', '', $remarks_display));
    
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

<?php include_once('universal_excel_export.php'); ?>
</body>
</html>

