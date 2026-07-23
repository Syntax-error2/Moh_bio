<?php
session_start();
require_once 'dbcon.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get leave application ID
$leave_application_id = $_GET['id'] ?? null;

if (!$leave_application_id) {
    $_SESSION['error'] = "Invalid leave application ID";
    header("Location: leave_application.php");
    exit();
}

// Fetch leave application data
try {
    $stmt = $conn->prepare("SELECT la.*, 
        CONCAT(p.lastname, ', ', p.firstname, ' ', COALESCE(p.middlename, '')) as full_name,
        d.designation_name,
        p.personnel_id
        FROM leave_applications la
        LEFT JOIN personnels p ON la.personnel_id = p.personnel_id
        LEFT JOIN designation d ON p.designation_id = d.designation_id
        WHERE la.id = :id");
    $stmt->bindParam(':id', $leave_application_id);
    $stmt->execute();
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        $_SESSION['error'] = "Leave application not found";
        header("Location: leave_application.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching leave application: " . $e->getMessage();
    header("Location: leave_application.php");
    exit();
}

// Format dates
function formatDate($date) {
    if (empty($date)) return '';
    $dt = new DateTime($date);
    return $dt->format('F d, Y');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS Form No. 6 - Application for Leave</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        /* Flexible page size - supports Letter (8.5"x11"), Folio (8.5"x13"), Legal (8.5"x14") */
        @page {
            size: auto;
            margin: 0.5in 0.5in;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
        }

        .form-container {
            width: 7.5in;
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-sizing: border-box;
        }

        .form-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 5px;
        }

        .form-subtitle {
            text-align: center;
            font-size: 9pt;
            font-style: italic;
            margin-bottom: 20px;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .form-table td,
        .form-table th {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        .label-cell {
            font-weight: bold;
            background-color: #f0f0f0;
            width: 30%;
        }

        .value-cell {
            width: 70%;
        }

        .checkbox-group {
            margin: 3px 0;
        }

        .checkbox-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            margin-right: 5px;
            text-align: center;
            line-height: 14px;
            vertical-align: middle;
        }

        .checked {
            font-weight: bold;
        }

        .signature-block {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 250px;
            margin: 30px auto 5px;
            text-align: center;
        }

        .signature-label {
            text-align: center;
            font-size: 9pt;
            margin-bottom: 20px;
        }

        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
            padding: 5px;
            margin-top: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .form-container {
                width: 100%;
                max-width: 7.5in;
                padding: 0;
                margin: 0 auto;
                page-break-inside: avoid;
            }
            
            @page {
                margin: 0.5in 0.5in;
            }
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn btn-primary btn-print">
        <i class="fa fa-print"></i> Print Form
    </button>
</div>

<div class="form-container">
    
    <div class="form-title">CS FORM NO. 6</div>
    <div class="form-subtitle">(Revised 2020)</div>
    <div class="form-title">APPLICATION FOR LEAVE</div>

    <table class="form-table">
        <tr>
            <td class="label-cell">OFFICE/AGENCY</td>
            <td class="value-cell"><?php echo htmlspecialchars($app['office_agency']); ?></td>
        </tr>
        <tr>
            <td class="label-cell">NAME (Last, First, Middle)</td>
            <td class="value-cell"><?php echo htmlspecialchars($app['full_name']); ?></td>
        </tr>
        <tr>
            <td class="label-cell">DATE OF FILING</td>
            <td class="value-cell"><?php echo formatDate($app['application_date']); ?></td>
        </tr>
        <tr>
            <td class="label-cell">POSITION</td>
            <td class="value-cell"><?php echo htmlspecialchars($app['designation_name']); ?></td>
        </tr>
    </table>

    <div class="section-header">DETAILS OF LEAVE APPLICATION</div>

    <table class="form-table">
        <tr>
            <td class="label-cell">TYPE OF LEAVE TO BE AVAILED OF</td>
            <td class="value-cell">
                <?php
                $leave_types = [
                    'Vacation Leave' => 'Vacation Leave',
                    'Mandatory/Forced Leave' => 'Mandatory/Forced Leave',
                    'Sick Leave' => 'Sick Leave',
                    'Maternity Leave' => 'Maternity Leave',
                    'Paternity Leave' => 'Paternity Leave',
                    'Special Privilege Leave' => 'Special Privilege Leave',
                    'Solo Parent Leave' => 'Solo Parent Leave',
                    'Study Leave' => 'Study Leave',
                    '10-Day VAWC Leave' => '10-Day VAWC Leave',
                    'Rehabilitation Privilege' => 'Rehabilitation Privilege',
                    'Special Leave Benefits for Women' => 'Special Leave Benefits for Women',
                    'Special Emergency (Calamity) Leave' => 'Special Emergency (Calamity) Leave',
                    'Adoption Leave' => 'Adoption Leave'
                ];

                foreach ($leave_types as $key => $label) {
                    $checked = (strpos($app['leave_type'], $key) !== false) ? '✓' : '';
                    echo '<div class="checkbox-group">';
                    echo '<span class="checkbox-box ' . ($checked ? 'checked' : '') . '">' . $checked . '</span> ';
                    echo $label;
                    echo '</div>';
                }

                // Others
                $is_others = !in_array($app['leave_type'], $leave_types);
                $checked = $is_others ? '✓' : '';
                echo '<div class="checkbox-group">';
                echo '<span class="checkbox-box ' . ($checked ? 'checked' : '') . '">' . $checked . '</span> ';
                echo 'Others: <u>' . ($is_others ? htmlspecialchars($app['other_leave_specification']) : '_______________') . '</u>';
                echo '</div>';
                ?>
            </td>
        </tr>
    </table>

    <table class="form-table">
        <tr>
            <td class="label-cell">DETAILS OF LEAVE</td>
            <td class="value-cell">
                <?php
                if (strpos($app['leave_type'], 'Vacation') !== false) {
                    echo '<strong>Vacation Leave:</strong> ' . htmlspecialchars($app['vacation_details']);
                } elseif (strpos($app['leave_type'], 'Sick') !== false) {
                    echo '<strong>Sick Leave:</strong> ' . htmlspecialchars($app['sick_details']);
                } elseif (strpos($app['leave_type'], 'Study') !== false) {
                    echo '<strong>Study Leave:</strong> ' . htmlspecialchars($app['study_details']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td class="label-cell">NUMBER OF WORKING DAYS APPLIED FOR</td>
            <td class="value-cell"><strong><?php echo $app['number_of_days']; ?></strong> day(s)</td>
        </tr>
        <tr>
            <td class="label-cell">INCLUSIVE DATES</td>
            <td class="value-cell">
                <strong><?php echo formatDate($app['inclusive_date_from']); ?> to <?php echo formatDate($app['inclusive_date_to']); ?></strong>
            </td>
        </tr>
    </table>

    <table class="form-table">
        <tr>
            <td class="label-cell">COMMUTATION</td>
            <td class="value-cell">
                <span class="checkbox-box <?php echo ($app['commutation'] === 'requested') ? 'checked' : ''; ?>">
                    <?php echo ($app['commutation'] === 'requested') ? '✓' : ''; ?>
                </span> Requested
                &nbsp;&nbsp;&nbsp;
                <span class="checkbox-box <?php echo ($app['commutation'] === 'not_requested') ? 'checked' : ''; ?>">
                    <?php echo ($app['commutation'] === 'not_requested') ? '✓' : ''; ?>
                </span> Not Requested
            </td>
        </tr>
    </table>

    <div class="signature-block">
        <div class="signature-line"><?php echo htmlspecialchars($app['full_name']); ?></div>
        <div class="signature-label">(Signature of Applicant)</div>
    </div>

    <div class="section-header" style="margin-top: 30px;">DETAILS OF ACTION ON APPLICATION</div>

    <table class="form-table">
        <tr>
            <td colspan="2" style="background-color: #f5f5f5; font-weight: bold;">CERTIFICATION OF LEAVE CREDITS</td>
        </tr>
        <tr>
            <td class="label-cell">AS OF</td>
            <td class="value-cell"><?php echo formatDate($app['as_of_date']); ?></td>
        </tr>
        <tr>
            <td class="label-cell">TOTAL EARNED</td>
            <td class="value-cell">
                VL: <?php echo number_format($app['total_earned_vl'], 3); ?> &nbsp;&nbsp;&nbsp;
                SL: <?php echo number_format($app['total_earned_sl'], 3); ?>
            </td>
        </tr>
        <tr>
            <td class="label-cell">LESS THIS APPLICATION</td>
            <td class="value-cell">
                VL: <?php echo number_format($app['less_application_vl'], 3); ?> &nbsp;&nbsp;&nbsp;
                SL: <?php echo number_format($app['less_application_sl'], 3); ?>
            </td>
        </tr>
        <tr>
            <td class="label-cell">BALANCE</td>
            <td class="value-cell">
                <strong>
                    VL: <?php echo number_format($app['balance_vl'], 3); ?> &nbsp;&nbsp;&nbsp;
                    SL: <?php echo number_format($app['balance_sl'], 3); ?>
                </strong>
            </td>
        </tr>
    </table>

    <div class="signature-block">
        <div class="signature-line">_______________________________</div>
        <div class="signature-label">(Authorized Officer)</div>
    </div>

    <table class="form-table" style="margin-top: 20px;">
        <tr>
            <td colspan="2" style="background-color: #f5f5f5; font-weight: bold;">RECOMMENDATION</td>
        </tr>
        <tr>
            <td class="label-cell">STATUS</td>
            <td class="value-cell">
                <?php
                $status_labels = [
                    'pending' => 'PENDING',
                    'approved' => 'APPROVED',
                    'disapproved' => 'DISAPPROVED'
                ];
                echo '<strong>' . ($status_labels[$app['status']] ?? 'PENDING') . '</strong>';
                ?>
            </td>
        </tr>
        <?php if (!empty($app['recommendation'])): ?>
        <tr>
            <td class="label-cell">REMARKS</td>
            <td class="value-cell"><?php echo nl2br(htmlspecialchars($app['recommendation'])); ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="signature-block">
        <div class="signature-line">_______________________________</div>
        <div class="signature-label">(Department Head/Authorized Officer)</div>
    </div>

    <div style="margin-top: 30px; font-size: 8pt; text-align: center; color: #666;">
        Civil Service Form No. 6 (Revised 2020)<br>
        Printed: <?php echo date('F d, Y h:i A'); ?>
    </div>

</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
