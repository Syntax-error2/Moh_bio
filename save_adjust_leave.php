<?php
include('dbcon.php');
require_once('session.php');

if(isset($_POST['adjust_leave'])){
    $personnel_id = $_POST['personnel_id'];
    $dept = $_POST['dept'];
    $vl_balance = $_POST['vl_balance'];
    $sl_balance = $_POST['sl_balance'];
    $remarks = $_POST['remarks'];
    if(empty($remarks)) {
        $remarks = "Manual adjustment by HR";
    }

    // Get old balances for log calculation
    $stmt = $conn->prepare("SELECT vl_balance, sl_balance FROM personnels WHERE personnel_id = :pid");
    $stmt->execute(['pid' => $personnel_id]);
    $old = $stmt->fetch(PDO::FETCH_ASSOC);

    require_once('official_duty.php');

    $diff_vl = (float)$_POST['vl_balance'];
    $diff_sl = (float)$_POST['sl_balance'];
    $vl_w_pay = isset($_POST['vl_w_pay']) ? (float)$_POST['vl_w_pay'] : 0.000;
    $sl_w_pay = isset($_POST['sl_w_pay']) ? (float)$_POST['sl_w_pay'] : 0.000;
    $vl_wout_pay = isset($_POST['vl_wout_pay']) ? (float)$_POST['vl_wout_pay'] : 0.000;
    $sl_wout_pay = isset($_POST['sl_wout_pay']) ? (float)$_POST['sl_wout_pay'] : 0.000;

    $period_from = trim($_POST['period_from'] ?? '');
    $period_to = trim($_POST['period_to'] ?? '');

    if (empty($period_to) && !empty($period_from)) {
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

    $period = !empty($period_to) ? ($period_from . ' - ' . $period_to) : $period_from;
    $particulars = $_POST['particulars'];

    if(empty($remarks) || $remarks === "Manual adjustment by HR") {
        if(stripos($particulars, 'terminal') !== false) {
            $remarks = "Certified Correct";
        } else {
            $remarks = "Manual adjustment by HR";
        }
    }

    $is_special = isNonDeductibleSpecialLeave($particulars, $remarks);
    if ($is_special) {
        $diff_vl = 0.000;
        $diff_sl = 0.000;
        if ($vl_w_pay <= 0) {
            $vl_w_pay = getSpecialLeaveDays(['particulars' => $particulars, 'remarks' => $remarks, 'period' => $period], $conn);
        }
    } else {
        if ($diff_vl < 0 && $vl_w_pay <= 0) {
            $vl_w_pay = abs($diff_vl);
        }
        if ($diff_sl < 0 && $sl_w_pay <= 0) {
            $sl_w_pay = abs($diff_sl);
        }
    }
    
    $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, vl_w_pay, sl_w_pay, vl_wout_pay, sl_wout_pay, remarks, period, particulars) 
                           VALUES (:pid, 'Manual Adjustment', :vl, :sl, :vl_wp, :sl_wp, :vl_wop, :sl_wop, :remarks, :period, :particulars)");
    $log->execute([
        'pid' => $personnel_id, 
        'vl' => $diff_vl, 
        'sl' => $diff_sl, 
        'vl_wp' => $vl_w_pay,
        'sl_wp' => $sl_w_pay,
        'vl_wop' => $vl_wout_pay, 
        'sl_wop' => $sl_wout_pay,
        'remarks' => $remarks, 
        'period' => $period, 
        'particulars' => $particulars
    ]);

        // Recalculate secure final balance chronologically
    $stmt2 = $conn->prepare("SELECT vl_amount, sl_amount, period, log_id FROM leave_credit_logs WHERE personnel_id = ?");
    $stmt2->execute([$personnel_id]);
    $logs = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    usort($logs, function($a, $b) {
        $tsA = strtotime(str_replace('-', '/', trim(explode(' - ', $a['period'] ?? '')[0])));
        $tsB = strtotime(str_replace('-', '/', trim(explode(' - ', $b['period'] ?? '')[0])));
        if(!$tsA) $tsA = 0; if(!$tsB) $tsB = 0;
        if ($tsA == $tsB) return $a['log_id'] <=> $b['log_id'];
        return $tsA <=> $tsB;
    });
    $v_run = 0; $s_run = 0;
    foreach($logs as $l) {
        $v_run += (float)$l['vl_amount'];
        if($v_run < 0) $v_run = 0;
        $s_run += (float)$l['sl_amount'];
        if($s_run < 0) $s_run = 0;
    }
    $upd2 = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
    $upd2->execute([$v_run, $s_run, $personnel_id]);
    
    $conn=null;
    ?>
    <script>
    window.alert('Leave balances successfully adjusted!');
    window.location='list_personnel_individual_details_LC.php?dept=<?php echo $dept; ?>&personnel_id=<?php echo $personnel_id; ?>';
    </script>
    <?php
}
?>
// Final fix applied by AI