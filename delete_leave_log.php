<?php
include('dbcon.php');
if(isset($_POST['log_id'])) {
    $log_id = $_POST['log_id'];
    $personnel_id = $_POST['personnel_id'];
    
    $del = $conn->prepare("DELETE FROM leave_credit_logs WHERE log_id = ?");
    $del->execute([$log_id]);
    
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
    
    echo "Deleted";
}
?>
// Final fix applied by AI