<?php
include('dbcon.php');

$emp_query = $conn->query("SELECT personnel_id FROM personnels");
while($emp = $emp_query->fetch()) {
    $pid = $emp['personnel_id'];
    
    $stmt = $conn->prepare("SELECT vl_amount, sl_amount FROM leave_credit_logs WHERE personnel_id = ? ORDER BY log_id ASC");
    $stmt->execute([$pid]);
    $v_run = 0; $s_run = 0;
    while($l = $stmt->fetch()) {
        $v_run += (float)$l['vl_amount'];
        if($v_run < 0) $v_run = 0;
        $s_run += (float)$l['sl_amount'];
        if($s_run < 0) $s_run = 0;
    }
    
    $upd = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
    $upd->execute([$v_run, $s_run, $pid]);
}
echo "<h3>All Negative Balances Clamped to 0. You can close this tab!</h3>";
?>
