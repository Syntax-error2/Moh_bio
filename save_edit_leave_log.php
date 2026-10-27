<?php
include('session.php');
include('dbcon.php');

if(isset($_POST['edit_log']) && isset($_POST['log_id'])) {
    $log_id = $_POST['log_id'];
    $personnel_id = $_GET['personnel_id'];
    $dept = isset($_GET['dept']) ? $_GET['dept'] : '';
    
    $period_from = trim($_POST['period_from']);
    $period_to = trim($_POST['period_to']);
    
    $period = $period_from;
    if(!empty($period_to)) {
        $period .= ' - ' . $period_to;
    }
    
    $particulars = trim($_POST['particulars']);
    
    // Parse numeric inputs
    $vl_earned = (isset($_POST['vl_earned']) && $_POST['vl_earned'] !== '') ? floatval(str_replace(',', '', $_POST['vl_earned'])) : null;
    $vl_balance = (isset($_POST['vl_balance']) && $_POST['vl_balance'] !== '') ? floatval(str_replace(',', '', $_POST['vl_balance'])) : null;
    $vl_w_pay = (isset($_POST['vl_w_pay']) && $_POST['vl_w_pay'] !== '') ? -1 * abs(floatval(str_replace(',', '', $_POST['vl_w_pay']))) : null;
    $vl_wout_pay = (isset($_POST['vl_wout_pay']) && $_POST['vl_wout_pay'] !== '') ? floatval(str_replace(',', '', $_POST['vl_wout_pay'])) : null;
    
    $sl_earned = (isset($_POST['sl_earned']) && $_POST['sl_earned'] !== '') ? floatval(str_replace(',', '', $_POST['sl_earned'])) : null;
    $sl_balance = (isset($_POST['sl_balance']) && $_POST['sl_balance'] !== '') ? floatval(str_replace(',', '', $_POST['sl_balance'])) : null;
    $sl_w_pay = (isset($_POST['sl_w_pay']) && $_POST['sl_w_pay'] !== '') ? -1 * abs(floatval(str_replace(',', '', $_POST['sl_w_pay']))) : null;
    $sl_wout_pay = (isset($_POST['sl_wout_pay']) && $_POST['sl_wout_pay'] !== '') ? floatval(str_replace(',', '', $_POST['sl_wout_pay'])) : null;
    
    // Fetch existing log to check current log_type
    $stmt_curr = $conn->prepare("SELECT log_type, particulars FROM leave_credit_logs WHERE log_id = ?");
    $stmt_curr->execute([$log_id]);
    $curr_row = $stmt_curr->fetch(PDO::FETCH_ASSOC);
    $curr_log_type = $curr_row ? $curr_row['log_type'] : 'Manual Adjustment';

    // Prior running balance before this entry (for calculating delta when editing balance)
    $prev_vl = null;
    $prev_sl = null;
    if (isset($_POST['prev_vl']) && $_POST['prev_vl'] !== '') {
        $prev_vl = floatval(str_replace(',', '', $_POST['prev_vl']));
    }
    if (isset($_POST['prev_sl']) && $_POST['prev_sl'] !== '') {
        $prev_sl = floatval(str_replace(',', '', $_POST['prev_sl']));
    }

    if ($prev_vl === null || $prev_sl === null) {
        $stmt_all = $conn->prepare("SELECT log_id, period, vl_amount, sl_amount FROM leave_credit_logs WHERE personnel_id = ?");
        $stmt_all->execute([$personnel_id]);
        $all_logs = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
        usort($all_logs, function($a, $b) {
            $tsA = strtotime(str_replace('-', '/', trim(explode(' - ', $a['period'] ?? '')[0])));
            $tsB = strtotime(str_replace('-', '/', trim(explode(' - ', $b['period'] ?? '')[0])));
            if(!$tsA) $tsA = 0; if(!$tsB) $tsB = 0;
            if ($tsA == $tsB) return $a['log_id'] <=> $b['log_id'];
            return $tsA <=> $tsB;
        });
        $calc_prev_vl = 0.0;
        $calc_prev_sl = 0.0;
        foreach($all_logs as $l) {
            if ($l['log_id'] == $log_id) break;
            $calc_prev_vl += (float)$l['vl_amount'];
            if ($calc_prev_vl < 0) $calc_prev_vl = 0;
            $calc_prev_sl += (float)$l['sl_amount'];
            if ($calc_prev_sl < 0) $calc_prev_sl = 0;
        }
        if ($prev_vl === null) $prev_vl = $calc_prev_vl;
        if ($prev_sl === null) $prev_sl = $calc_prev_sl;
    }

    require_once('official_duty.php');

    $is_special_leave = isNonDeductibleSpecialLeave($particulars, trim($_POST['remarks'] ?? ''));

    $raw_vl_w_pay = (isset($_POST['vl_w_pay']) && $_POST['vl_w_pay'] !== '') ? abs(floatval(str_replace(',', '', $_POST['vl_w_pay']))) : null;
    $raw_sl_w_pay = (isset($_POST['sl_w_pay']) && $_POST['sl_w_pay'] !== '') ? abs(floatval(str_replace(',', '', $_POST['sl_w_pay']))) : null;

    $vl_amount = 0.000;
    $db_vl_w_pay = 0.000;
    $has_manual_balance = false;
    $has_monthly_earn = false;

    $sl_amount = 0.000;
    $db_sl_w_pay = 0.000;

    if ($is_special_leave) {
        // Special non-deductible leaves (Wellness, Maternity, Paternity, Solo Parent, Rehab, Emergency)
        // do NOT deduct from running balances (vl_amount and sl_amount remain 0.000)
        $vl_amount = 0.000;
        $sl_amount = 0.000;
        if ($raw_vl_w_pay !== null && $raw_vl_w_pay > 0) {
            $db_vl_w_pay = $raw_vl_w_pay;
        } elseif ($raw_sl_w_pay !== null && $raw_sl_w_pay > 0) {
            $db_sl_w_pay = $raw_sl_w_pay;
        } else {
            $db_vl_w_pay = getSpecialLeaveDays(['particulars' => $particulars, 'remarks' => trim($_POST['remarks'] ?? ''), 'period' => $period], $conn);
        }
    } else {
        if ($vl_w_pay !== null) {
            $vl_amount = $vl_w_pay;
            $db_vl_w_pay = abs($vl_w_pay);
        } elseif ($vl_balance !== null) {
            $vl_amount = $vl_balance - $prev_vl;
            $has_manual_balance = true;
        } elseif ($vl_earned !== null) {
            $vl_amount = $vl_earned;
            $has_monthly_earn = true;
        }

        if ($sl_w_pay !== null) {
            $sl_amount = $sl_w_pay;
            $db_sl_w_pay = abs($sl_w_pay);
        } elseif ($sl_balance !== null) {
            $sl_amount = $sl_balance - $prev_sl;
            $has_manual_balance = true;
        } elseif ($sl_earned !== null) {
            $sl_amount = $sl_earned;
            $has_monthly_earn = true;
        }
    }

    $log_type = $curr_log_type;
    if ($has_manual_balance) {
        $log_type = 'Manual Adjustment';
    } elseif ($has_monthly_earn) {
        $log_type = 'Monthly Earn';
    }
    
    $remarks = trim($_POST['remarks']);
    
    try {
        $stmt = $conn->prepare("UPDATE leave_credit_logs SET 
            period = :period,
            particulars = :particulars,
            log_type = :log_type,
            vl_amount = :vl_amount,
            vl_w_pay = :vl_w_pay,
            vl_wout_pay = :vl_wout_pay,
            sl_amount = :sl_amount,
            sl_w_pay = :sl_w_pay,
            sl_wout_pay = :sl_wout_pay,
            remarks = :remarks
            WHERE log_id = :log_id
        ");
        
        $stmt->execute([
            ':period' => $period,
            ':particulars' => $particulars,
            ':log_type' => $log_type,
            ':vl_amount' => $vl_amount,
            ':vl_w_pay' => $db_vl_w_pay,
            ':vl_wout_pay' => $vl_wout_pay,
            ':sl_amount' => $sl_amount,
            ':sl_w_pay' => $db_sl_w_pay,
            ':sl_wout_pay' => $sl_wout_pay,
            ':remarks' => $remarks,
            ':log_id' => $log_id
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
        
        // Redirect back with success script
        echo "<script>
            alert('Leave Entry updated successfully!');
            window.location = 'list_personnel_individual_details_LC.php?personnel_id=" . $personnel_id . "&dept=" . $dept . "';
        </script>";
        
    } catch(PDOException $e) {
        echo "<script>
            alert('Error updating leave entry.');
            window.history.back();
        </script>";
    }
} else {
    header("Location: list_leave.php?cw=list_leave");
    exit;
}
?>
