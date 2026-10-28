<?php
include 'session.php';
include 'header.php'; // some scripts need header or whatever but session is enough

if(isset($_POST['earn_monthly_leave'])) {
    $personnel_id = $_POST['personnel_id'];
    $dept = $_POST['dept'];
    $month_year = $_POST['month_year'];
    
    $vl_add = 1.250;
    $sl_add = 1.250;
    
    $ts = !empty($month_year) ? strtotime($month_year) : time();
    if(!$ts) $ts = time();
    $period = date('m-01-y', $ts) . ' - ' . date('m-t-y', $ts);
    $particulars = 'Accrual';
    $remarks = "Earned Monthly Leave Credits (" . date('F Y', $ts) . ")";
    
    // Log with complete period and particulars
    $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, period, particulars, vl_amount, sl_amount, remarks) VALUES (?, 'Monthly Earn', ?, ?, ?, ?, ?)");
    $log->execute([$personnel_id, $period, $particulars, $vl_add, $sl_add, $remarks]);
    
    // Recalculate personnel balances chronologically
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
    $upd = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
    $upd->execute([$v_run, $s_run, $personnel_id]);
    
    header("Location: list_personnel_individual_details_LC.php?dept=" . $dept . "&personnel_id=" . $personnel_id);
    exit();
}
?>