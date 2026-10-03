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
    $vl_w_pay = (isset($_POST['vl_w_pay']) && $_POST['vl_w_pay'] !== '') ? -1 * abs(floatval(str_replace(',', '', $_POST['vl_w_pay']))) : null;
    $vl_wout_pay = (isset($_POST['vl_wout_pay']) && $_POST['vl_wout_pay'] !== '') ? floatval(str_replace(',', '', $_POST['vl_wout_pay'])) : null;
    
    $sl_earned = (isset($_POST['sl_earned']) && $_POST['sl_earned'] !== '') ? floatval(str_replace(',', '', $_POST['sl_earned'])) : null;
    $sl_w_pay = (isset($_POST['sl_w_pay']) && $_POST['sl_w_pay'] !== '') ? -1 * abs(floatval(str_replace(',', '', $_POST['sl_w_pay']))) : null;
    $sl_wout_pay = (isset($_POST['sl_wout_pay']) && $_POST['sl_wout_pay'] !== '') ? floatval(str_replace(',', '', $_POST['sl_wout_pay'])) : null;
    
    $vl_amount = ($vl_w_pay !== null) ? $vl_w_pay : (($vl_earned !== null) ? $vl_earned : 0.000);
    $sl_amount = ($sl_w_pay !== null) ? $sl_w_pay : (($sl_earned !== null) ? $sl_earned : 0.000);
    
    $remarks = trim($_POST['remarks']);
    
    try {
        $stmt = $conn->prepare("UPDATE leave_credit_logs SET 
            period = :period,
            particulars = :particulars,
            vl_amount = :vl_amount,
            vl_wout_pay = :vl_wout_pay,
            sl_amount = :sl_amount,
            sl_wout_pay = :sl_wout_pay,
            remarks = :remarks
            WHERE log_id = :log_id
        ");
        
        $stmt->execute([
            ':period' => $period,
            ':particulars' => $particulars,
            ':vl_amount' => $vl_amount,
            ':vl_wout_pay' => $vl_wout_pay,
            ':sl_amount' => $sl_amount,
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
