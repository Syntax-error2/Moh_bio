<?php
include('dbcon.php');
require_once('session.php');
require_once('official_duty.php');

$personnel_id = isset($_GET['personnel_id']) ? intval($_GET['personnel_id']) : 0;

$stmt = $conn->prepare("SELECT p.*, d.des_name as designation_name, do.dept_office_name as do_name, es.emp_stat_name as empStat_name 
                        FROM personnels p 
                        LEFT JOIN designation d ON p.des_id = d.des_id 
                        LEFT JOIN dept_offices do ON p.do_id = do.do_id 
                        LEFT JOIN emp_status es ON p.empStat_id = es.empStat_id 
                        WHERE p.personnel_id = ?");
$stmt->execute([$personnel_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    die("Personnel not found.");
}

$has_leave_credits = personnelHasLeaveCredits($conn, $staff);

if (!$has_leave_credits) {
    echo "<script>alert('Leave credit certification is only available for employees with leave credits (Permanent, Casual, or Elective).'); window.close();</script>";
    exit();
}

// Name formatting: e.g. JERRY S. YULO
$m_initial = (!empty($staff['mname']) && $staff['mname'] != '-') ? ' ' . strtoupper(substr(trim($staff['mname']), 0, 1)) . '.' : '';
$suffix = (!empty($staff['suffix']) && $staff['suffix'] != '-') ? ' ' . strtoupper(trim($staff['suffix'])) : '';
$default_emp_name = strtoupper(trim($staff['fname']) . $m_initial . ' ' . trim($staff['lname']) . $suffix);
$emp_name = (isset($_GET['emp_name']) && trim($_GET['emp_name']) !== '') ? strtoupper(trim($_GET['emp_name'])) : $default_emp_name;

// Guarantor Name (for loan guarantee signature below)
$guarantor_name = (isset($_GET['guarantor_name']) && trim($_GET['guarantor_name']) !== '') ? strtoupper(trim($_GET['guarantor_name'])) : $emp_name;

// Position & Employment Status
if (isset($_GET['emp_position']) && trim($_GET['emp_position']) !== '') {
    $emp_position = strtoupper(trim($_GET['emp_position']));
} else {
    $emp_position = !empty($staff['designation_name']) ? strtoupper($staff['designation_name']) : 'EMPLOYEE';
}

if (isset($_GET['emp_status']) && trim($_GET['emp_status']) !== '') {
    $emp_status = strtolower(trim($_GET['emp_status']));
} else {
    $emp_status = !empty($staff['empStat_name']) ? strtolower($staff['empStat_name']) : 'permanent';
}

// Dates
if (isset($_GET['as_of_date']) && trim($_GET['as_of_date']) !== '') {
    $as_of_date = trim($_GET['as_of_date']);
} else {
    $as_of_date = date('F t, Y');
}

if (isset($_GET['given_date']) && trim($_GET['given_date']) !== '') {
    $given_date = trim($_GET['given_date']);
} else {
    $given_date = date('jS \d\a\y \o\f F Y');
}

// Balances
$vl_balance = isset($_GET['vl_balance']) && $_GET['vl_balance'] !== '' ? floatval($_GET['vl_balance']) : floatval($staff['vl_balance']);
$sl_balance = isset($_GET['sl_balance']) && $_GET['sl_balance'] !== '' ? floatval($_GET['sl_balance']) : floatval($staff['sl_balance']);
$total_credits = isset($_GET['total_credits']) && $_GET['total_credits'] !== '' ? floatval($_GET['total_credits']) : ($vl_balance + $sl_balance);

// Monthly Salary (stored in rate_per_day)
if (isset($_GET['monthly_salary']) && $_GET['monthly_salary'] !== '') {
    $monthly_salary = floatval($_GET['monthly_salary']);
} else {
    $monthly_salary = floatval($staff['rate_per_day']);
    if ($monthly_salary <= 0) {
        $sr_q = $conn->query("SELECT salary FROM service_record WHERE personnel_id='".$staff['personnel_id']."' ORDER BY sr_id DESC LIMIT 1");
        if ($sr_q && $sr_r = $sr_q->fetch()) {
            $raw_sal = preg_replace('/[^0-9.]/', '', $sr_r['salary']);
            if (stripos($sr_r['salary'], 'annum') !== false || stripos($sr_r['salary'], '/pa') !== false) {
                $monthly_salary = floatval($raw_sal) / 12;
            } else {
                $monthly_salary = floatval($raw_sal);
            }
        }
    }
}

// Amount Due: Total Leave Credits * 0.0481927 * Monthly Salary
if (isset($_GET['amount_due']) && $_GET['amount_due'] !== '') {
    $amount_due = floatval($_GET['amount_due']);
} else {
    $amount_due = $total_credits * 0.0481927 * $monthly_salary;
}

// Pronoun (his/her)
if (isset($_GET['pronoun']) && in_array(strtolower($_GET['pronoun']), ['his', 'her', 'their'])) {
    $pronoun = strtolower($_GET['pronoun']);
} else {
    $pronoun = (strtolower($staff['sex'] ?? '') == 'female') ? 'her' : 'his';
}

// Signatory
$signatory_name = isset($_GET['signatory_name']) && trim($_GET['signatory_name']) !== '' ? strtoupper(trim($_GET['signatory_name'])) : 'ROSELLE J. MENDOZA';
$signatory_pos = isset($_GET['signatory_pos']) && trim($_GET['signatory_pos']) !== '' ? strtoupper(trim($_GET['signatory_pos'])) : 'MGADH-I';

// Loan Guarantee
$include_guarantee = isset($_GET['include_guarantee']) ? ($_GET['include_guarantee'] == '1' || $_GET['include_guarantee'] == 'on') : true;
$guarantee_to = isset($_GET['guarantee_to']) && trim($_GET['guarantee_to']) !== '' ? trim($_GET['guarantee_to']) : 'Binalbagan Multi-Purpose Cooperative (BIMEC)';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="img/72123-mob_seal.png">
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <title>Certification of Leave Credits - <?php echo htmlspecialchars($emp_name); ?></title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .cert-container {
            width: 8.5in;
            min-height: 11in;
            margin: 25px auto;
            padding: 1in 1.1in 1in 1.1in;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
        }
        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 25px;
        }
        .letterhead-table td {
            border: none;
            padding: 0;
        }
        .republic-text {
            font-size: 13px;
            line-height: 1.35;
        }
        .cert-title {
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2.5px;
            margin-top: 45px;
            margin-bottom: 45px;
        }
        .salutation {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 25px;
        }
        .cert-body-p {
            text-indent: 45px;
            text-align: justify;
            font-size: 15px;
            line-height: 1.85;
            margin-top: 0;
            margin-bottom: 28px;
        }
        .balance-table {
            border-collapse: collapse;
            border: none;
            font-size: 15px;
            line-height: 2.2;
            margin-left: 60px;
            margin-bottom: 28px;
        }
        .balance-table td {
            border: none;
            padding: 2px 0;
        }
        .balance-label {
            width: 220px;
        }
        .balance-value {
            width: 140px;
            text-align: right;
            font-weight: bold;
            padding-right: 15px !important;
        }
        .amount-underlined {
            text-decoration: underline;
            font-weight: bold;
        }
        .signatory-box {
            width: 320px;
            margin-left: auto;
            text-align: center;
            margin-top: 40px;
            margin-bottom: 35px;
        }
        .signatory-name {
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .signatory-title {
            font-size: 14px;
            margin-top: 2px;
        }
        .guarantee-p {
            text-indent: 45px;
            text-align: justify;
            font-size: 14.5px;
            line-height: 1.85;
            margin-top: 30px;
            margin-bottom: 0;
        }
        .guarantor-box {
            width: 320px;
            margin-left: auto;
            text-align: center;
            margin-top: 45px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .guarantor-name {
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .print-toolbar {
            background: #2b3035;
            padding: 10px 20px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .btn-print {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 12px;
        }
        .btn-print:hover {
            background-color: #218838;
        }
        .btn-close {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 8px 18px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-close:hover {
            background-color: #5a6268;
        }
        @media print {
            body {
                background: #fff;
            }
            .cert-container {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0.25in 0.5in 0.25in 0.5in;
                box-shadow: none;
            }
            .print-toolbar {
                display: none !important;
            }
            @page {
                size: letter portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="print-toolbar">
        <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Print Document</button>
        <button onclick="window.close()" class="btn-close"><i class="fa fa-times"></i> Close Window</button>
    </div>

    <div class="cert-container">
        <!-- Letterhead Header -->
        <table class="letterhead-table">
            <tr>
                <td style="width: 80px; vertical-align: middle;">
                    <img src="img/72123-mob_seal.png" style="width: 65px; height: 65px;">
                </td>
                <td style="vertical-align: middle; padding-left: 12px;">
                    <div class="republic-text">
                        <div>Republic of the Philippines</div>
                        <div>Province of Negros Occidental</div>
                        <div style="font-weight: bold; font-size: 14px;">MUNICIPALITY OF BINALBAGAN</div>
                        <div style="font-weight: bold; font-size: 12px; color: #222;">HUMAN RESOURCE MANAGEMENT OFFICE</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Document Title -->
        <div class="cert-title">CERTIFICATION</div>

        <!-- Salutation -->
        <div class="salutation">TO WHOM IT MAY CONCERN:</div>

        <!-- Body Paragraph 1 -->
        <p class="cert-body-p">
            This is to certify that as per record kept in this office <strong><?php echo htmlspecialchars($emp_name); ?>, <?php echo htmlspecialchars($emp_position); ?>,</strong> <?php echo htmlspecialchars($emp_status); ?> employee has the following balance of leave credits as of <strong><?php echo htmlspecialchars($as_of_date); ?></strong>.
        </p>

        <!-- Leave Credits Breakdown -->
        <table class="balance-table">
            <tr>
                <td class="balance-label">Vacation Leave</td>
                <td class="balance-value"><?php echo number_format($vl_balance, 3); ?></td>
            </tr>
            <tr>
                <td class="balance-label">Sick Leave</td>
                <td class="balance-value"><?php echo number_format($sl_balance, 3); ?></td>
            </tr>
            <tr>
                <td class="balance-label" style="font-weight: bold;">Total</td>
                <td class="balance-value"><?php echo number_format($total_credits, 3); ?></td>
            </tr>
            <tr>
                <td class="balance-label" style="font-weight: bold;">Amount Due:</td>
                <td class="balance-value amount-underlined">P <?php echo number_format($amount_due, 2); ?></td>
            </tr>
        </table>

        <!-- Body Paragraph 2 -->
        <p class="cert-body-p">
            This certification is issued upon the request of the above-named person for whatever legal purpose it may serve <?php echo htmlspecialchars($pronoun); ?> best.
        </p>

        <!-- Body Paragraph 3 (Given Date) -->
        <p class="cert-body-p">
            Given this <?php echo htmlspecialchars($given_date); ?> at Binalbagan, Negros Occidental.
        </p>

        <!-- Signatory Block -->
        <div class="signatory-box">
            <div class="signatory-name"><?php echo htmlspecialchars($signatory_name); ?></div>
            <div class="signatory-title"><?php echo htmlspecialchars($signatory_pos); ?></div>
        </div>

        <!-- Loan Guarantee Clause -->
        <?php if ($include_guarantee) { ?>
        <p class="guarantee-p">
            With the certification above issued by the Human Resource Development Office. I guarantee my leave credits stated above to <?php echo htmlspecialchars($guarantee_to); ?> in consideration to my loan application. I further guarantee my leave credits earned until of my retirement.
        </p>
        <div class="guarantor-box">
            <div class="guarantor-name"><?php echo htmlspecialchars($guarantor_name); ?></div>
        </div>
        <?php } ?>
    </div>

</body>
</html>
