<?php
// This script is called automatically to check if leaves have been accrued for the current month
if (isset($conn) && $conn !== null) {
    $current_month_year = date('Y-m'); // e.g. 2026-10
    
    // Ensure tracking table exists
    $conn->query("CREATE TABLE IF NOT EXISTS leave_accrual_history (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        accrual_month VARCHAR(7) NOT NULL UNIQUE,
        date_processed DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $chk = $conn->prepare("SELECT id FROM leave_accrual_history WHERE accrual_month = ?");
    $chk->execute([$current_month_year]);
    
    if ($chk->rowCount() == 0) {
        // Prevent race conditions by inserting record first
        try {
            $insert = $conn->prepare("INSERT INTO leave_accrual_history (accrual_month) VALUES (?)");
            $insert->execute([$current_month_year]);
            
            $vl_add = 1.250;
            $sl_add = 1.250;
            $remarks = "Auto-accrual for " . date('Y-m');
            $period = date('m-01-y') . ' - ' . date('m-t-y'); // E.g. 10-01-26 - 10-31-26
            
            // 1. Get eligible employment status IDs (Permanent, Casual, Elective, Co-terminous)
            $stat_query = $conn->query("SELECT empStat_id FROM emp_status WHERE LOWER(emp_stat_name) LIKE '%permanent%' OR LOWER(emp_stat_name) LIKE '%casual%' OR LOWER(emp_stat_name) LIKE '%elective%' OR LOWER(emp_stat_name) LIKE '%co-terminous%'");
            $eligible_ids = $stat_query ? $stat_query->fetchAll(PDO::FETCH_COLUMN) : [1, 2, 5];
            
            if (!empty($eligible_ids)) {
                $placeholders = implode(',', array_fill(0, count($eligible_ids), '?'));
                $emps = $conn->prepare("SELECT personnel_id FROM personnels WHERE empStat_id IN ($placeholders) AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')");
                $emps->execute($eligible_ids);
                
                while ($emp = $emps->fetch(PDO::FETCH_ASSOC)) {
                    $pid = $emp['personnel_id'];
                    
                    // Add log
                    $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, vl_w_pay, sl_w_pay, vl_wout_pay, sl_wout_pay, remarks, period, particulars) 
                                           VALUES (?, 'Monthly Earn', ?, ?, 0.000, 0.000, 0.000, 0.000, ?, ?, 'Accrual')");
                    $log->execute([$pid, $vl_add, $sl_add, $remarks, $period]);
                    
                    // Recalculate balance chronologically
                    $stmt = $conn->prepare("SELECT vl_amount, sl_amount, period, log_id FROM leave_credit_logs WHERE personnel_id = ?");
                    $stmt->execute([$pid]);
                    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    usort($logs, function($a, $b) {
                        $tsA = strtotime(str_replace('-', '/', trim(explode(' - ', $a['period'] ?? '')[0])));
                        $tsB = strtotime(str_replace('-', '/', trim(explode(' - ', $b['period'] ?? '')[0])));
                        if (!$tsA) $tsA = 0; if (!$tsB) $tsB = 0;
                        if ($tsA == $tsB) return $a['log_id'] <=> $b['log_id'];
                        return $tsA <=> $tsB;
                    });
                    $v_run = 0; $s_run = 0;
                    foreach ($logs as $l) {
                        $v_run += (float)$l['vl_amount'];
                        if ($v_run < 0) $v_run = 0;
                        $s_run += (float)$l['sl_amount'];
                        if ($s_run < 0) $s_run = 0;
                    }
                    
                    $upd = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
                    $upd->execute([$v_run, $s_run, $pid]);
                }
            }
        } catch (PDOException $e) {
            // Duplicate key error or already running, safely ignore
        }
    }
}
?>
