<?php
include('session.php');

if(isset($_POST['log_id']) && isset($_POST['field'])) {
    $log_id = $_POST['log_id'];
    $field = $_POST['field'];
    $value = trim($_POST['value']);

    // Fetch current row
    $stmt = $conn->prepare("SELECT * FROM leave_credit_logs WHERE log_id = ?");
    $stmt->execute([$log_id]);
    $row = $stmt->fetch();

    if(!$row) { echo "Error: Log not found"; exit; }

    if($field == 'period_from') {
        $p = explode(' - ', $row['period']);
        $to = isset($p[1]) ? trim($p[1]) : '';
        $new_period = empty($to) ? $value : $value . ' - ' . $to;
        $up = $conn->prepare("UPDATE leave_credit_logs SET period = ? WHERE log_id = ?");
        $up->execute([$new_period, $log_id]);
    } 
    elseif($field == 'period_to') {
        $p = explode(' - ', $row['period']);
        $from = isset($p[0]) ? trim($p[0]) : '';
        $new_period = empty($value) ? $from : $from . ' - ' . $value;
        $up = $conn->prepare("UPDATE leave_credit_logs SET period = ? WHERE log_id = ?");
        $up->execute([$new_period, $log_id]);
    }
    elseif($field == 'particulars') {
        $up = $conn->prepare("UPDATE leave_credit_logs SET particulars = ? WHERE log_id = ?");
        $up->execute([$value, $log_id]);
    }
    elseif($field == 'vl_earned') {
        $val = floatval($value);
        $up = $conn->prepare("UPDATE leave_credit_logs SET vl_amount = ?, log_type = 'Monthly Earn' WHERE log_id = ?");
        $up->execute([$val, $log_id]);
    }
    elseif($field == 'vl_balance') {
        $target_val = floatval($value);
        $stmt_all = $conn->prepare("SELECT log_id, period, vl_amount FROM leave_credit_logs WHERE personnel_id = ?");
        $stmt_all->execute([$row['personnel_id']]);
        $all_logs = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
        usort($all_logs, function($a, $b) {
            $tsA = strtotime(str_replace('-', '/', trim(explode(' - ', $a['period'] ?? '')[0])));
            $tsB = strtotime(str_replace('-', '/', trim(explode(' - ', $b['period'] ?? '')[0])));
            if(!$tsA) $tsA = 0; if(!$tsB) $tsB = 0;
            if ($tsA == $tsB) return $a['log_id'] <=> $b['log_id'];
            return $tsA <=> $tsB;
        });
        $prev_vl = 0.0;
        foreach($all_logs as $l) {
            if($l['log_id'] == $log_id) break;
            $prev_vl += (float)$l['vl_amount'];
            if($prev_vl < 0) $prev_vl = 0;
        }
        $val = $target_val - $prev_vl;
        $up = $conn->prepare("UPDATE leave_credit_logs SET vl_amount = ?, log_type = 'Manual Adjustment' WHERE log_id = ?");
        $up->execute([$val, $log_id]);
    }
    elseif($field == 'vl_w_pay') {
        require_once('official_duty.php');
        $w_val = abs(floatval($value));
        $is_special = isNonDeductibleSpecialLeave($row['particulars'] ?? '', $row['remarks'] ?? '');
        if ($is_special) {
            $up = $conn->prepare("UPDATE leave_credit_logs SET vl_amount = 0.000, vl_w_pay = ? WHERE log_id = ?");
            $up->execute([$w_val, $log_id]);
        } else {
            $val = -1 * $w_val;
            if(empty($value)) $val = 0;
            $up = $conn->prepare("UPDATE leave_credit_logs SET vl_amount = ?, vl_w_pay = ? WHERE log_id = ?");
            $up->execute([$val, $w_val, $log_id]);
        }
    }
    elseif($field == 'vl_wout_pay') {
        $val = floatval($value);
        $up = $conn->prepare("UPDATE leave_credit_logs SET vl_wout_pay = ? WHERE log_id = ?");
        $up->execute([$val, $log_id]);
    }
    elseif($field == 'sl_earned') {
        $val = floatval($value);
        $up = $conn->prepare("UPDATE leave_credit_logs SET sl_amount = ?, log_type = 'Monthly Earn' WHERE log_id = ?");
        $up->execute([$val, $log_id]);
    }
    elseif($field == 'sl_balance') {
        $target_val = floatval($value);
        $stmt_all = $conn->prepare("SELECT log_id, period, sl_amount FROM leave_credit_logs WHERE personnel_id = ?");
        $stmt_all->execute([$row['personnel_id']]);
        $all_logs = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
        usort($all_logs, function($a, $b) {
            $tsA = strtotime(str_replace('-', '/', trim(explode(' - ', $a['period'] ?? '')[0])));
            $tsB = strtotime(str_replace('-', '/', trim(explode(' - ', $b['period'] ?? '')[0])));
            if(!$tsA) $tsA = 0; if(!$tsB) $tsB = 0;
            if ($tsA == $tsB) return $a['log_id'] <=> $b['log_id'];
            return $tsA <=> $tsB;
        });
        $prev_sl = 0.0;
        foreach($all_logs as $l) {
            if($l['log_id'] == $log_id) break;
            $prev_sl += (float)$l['sl_amount'];
            if($prev_sl < 0) $prev_sl = 0;
        }
        $val = $target_val - $prev_sl;
        $up = $conn->prepare("UPDATE leave_credit_logs SET sl_amount = ?, log_type = 'Manual Adjustment' WHERE log_id = ?");
        $up->execute([$val, $log_id]);
    }
    elseif($field == 'sl_w_pay') {
        require_once('official_duty.php');
        $w_val = abs(floatval($value));
        $is_special = isNonDeductibleSpecialLeave($row['particulars'] ?? '', $row['remarks'] ?? '');
        if ($is_special) {
            $up = $conn->prepare("UPDATE leave_credit_logs SET sl_amount = 0.000, sl_w_pay = ? WHERE log_id = ?");
            $up->execute([$w_val, $log_id]);
        } else {
            $val = -1 * $w_val;
            if(empty($value)) $val = 0;
            $up = $conn->prepare("UPDATE leave_credit_logs SET sl_amount = ?, sl_w_pay = ? WHERE log_id = ?");
            $up->execute([$val, $w_val, $log_id]);
        }
    }
    elseif($field == 'sl_wout_pay') {
        $val = floatval($value);
        $up = $conn->prepare("UPDATE leave_credit_logs SET sl_wout_pay = ? WHERE log_id = ?");
        $up->execute([$val, $log_id]);
    }
    elseif($field == 'remarks') {
        $up = $conn->prepare("UPDATE leave_credit_logs SET remarks = ? WHERE log_id = ?");
        $up->execute([$value, $log_id]);
    }
    
    // Recalculate totals for the personnel to update the personnels table vl_balance / sl_balance
    $stmt = $conn->prepare("SELECT vl_amount, sl_amount, period, log_id FROM leave_credit_logs WHERE personnel_id = ?");
    $stmt->execute([$row['personnel_id']]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
    $upd = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
    $upd->execute([$v_run, $s_run, $row['personnel_id']]);

    echo "Success";
}
?>
