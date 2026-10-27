<?php
// This script is called automatically to check if leaves have been accrued for the current month
if(isset($conn)) {
    $current_month_year = date('m-Y'); // e.g. 08-2026
    
    // Check if we already accrued for this month
    $conn->query("CREATE TABLE IF NOT EXISTS cron_monthly_leave (id INT AUTO_INCREMENT PRIMARY KEY, month_year VARCHAR(7) UNIQUE, date_executed DATETIME DEFAULT CURRENT_TIMESTAMP)");
    $chk = $conn->prepare("SELECT id FROM cron_monthly_leave WHERE month_year = ?");
    $chk->execute([$current_month_year]);
    
    if($chk->rowCount() == 0) {
        // We have not accrued yet for this month!
        // IMPORTANT: Prevent race conditions by inserting the record FIRST.
        // If it fails (due to UNIQUE constraint), another process is already doing it.
        try {
            $insert = $conn->prepare("INSERT INTO cron_monthly_leave (month_year) VALUES (?)");
            $insert->execute([$current_month_year]);
            
            // It successfully inserted, meaning WE are the ones who should run the accrual
            $vl_add = 1.250;
            $sl_add = 1.250;
            $remarks = "Auto-accrual for " . date('Y-m');
            $period = date('m-01-y') . ' - ' . date('m-t-y'); // E.g. 08-01-26 - 08-31-26
            
            // 1. Get all active personnels (Casual, Permanent, and Elective)
            $emps = $conn->query("SELECT personnel_id FROM personnels WHERE empStat_id IN (1, 2, 4) AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')");
            while($emp = $emps->fetch()) {
                $pid = $emp['personnel_id'];
                
                // Add log
                $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks, period, particulars) VALUES (?, 'Monthly Earn', ?, ?, ?, ?, 'Accrual')");
                $log->execute([$pid, $vl_add, $sl_add, $remarks, $period]);
                
                // Recalculate their balance securely by summing up the table with clamping
                $stmt = $conn->prepare("SELECT vl_amount, sl_amount, period, log_id FROM leave_credit_logs WHERE personnel_id = ?");
    $stmt->execute([$pid]);
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
                $upd->execute([$v_run, $s_run, $pid]);
            }
        } catch (PDOException $e) {
            // Probably a duplicate key error, meaning it was already handled. Safely ignore.
        }
    }
}
?>
