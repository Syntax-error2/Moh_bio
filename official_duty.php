<?php

function isPassSlipNotation($notation)
{
    if (empty($notation)) return false;
    $normalized = strtoupper(preg_replace('/\s+/', ' ', trim((string)$notation)));
    return (
        $normalized === 'PASS SLIP' ||
        strpos($normalized, 'PASS SLIP') !== false ||
        strpos($normalized, 'PASSSLIP') !== false
    );
}

function isOfficialDutyNotation($notation)
{
    $normalized = strtoupper(preg_replace('/\s+/', ' ', trim((string)$notation)));
    return in_array($normalized, [
        'OFFICIAL BUSINESS',
        'OFFICIAL BUSINESS TRIP',
        'BUSINESS TRIP',
        'OFFICIAL TRAVEL',
        'OFFICIAL TRAVEL ORDER',
        'PASS SLIP',
    ], true) || isPassSlipNotation($notation);
}

function isZeroValueOfficialDutyLeaveLog($log)
{
    if (strcasecmp((string)($log['log_type'] ?? ''), 'Leave Application') !== 0) {
        return false;
    }

    foreach (['vl_amount', 'sl_amount', 'vl_wout_pay', 'sl_wout_pay'] as $amount) {
        if (abs((float)($log[$amount] ?? 0)) > 0.000001) {
            return false;
        }
    }

    if (isOfficialDutyNotation($log['particulars'] ?? '')) {
        return true;
    }

    $remarks = (string)($log['remarks'] ?? '');
    if (preg_match('/^Applied for (.+?)(?:\s*\[Code:|$)/i', $remarks, $matches)) {
        return isOfficialDutyNotation($matches[1]);
    }

    return false;
}

/**
 * Checks if a leave type or particulars represents a special non-deductible leave
 * (Leaves with full pay that do NOT deduct from Vacation or Sick Leave balances).
 * Includes: Wellness Leave, Maternity Leave, Paternity Leave, Solo Parent Leave,
 * Rehabilitation Leave, Emergency/Calamity Leave, Magna Carta for Women, VAWC.
 */
function isNonDeductibleSpecialLeave($particulars, $remarks = '')
{
    $p = strtoupper(trim((string)$particulars));
    $r = strtoupper(trim((string)$remarks));
    $combined = $p . ' ' . $r;
    
    if (empty($combined)) {
        return false;
    }

    // High-priority matches for special non-deductible leaves
    $specialKeywords = [
        'WELLNESS',
        'MATERNITY',
        'PATERNITY',
        'SOLO PARENT',
        'PARENTAL LEAVE',
        'REHABILITATION',
        'REHAB',
        'EMERGENCY',
        'CALAMITY',
        'MAGNA CARTA',
        'VAWC',
        'SPECIAL PRIVILEGE',
        'SPL'
    ];

    foreach ($specialKeywords as $kw) {
        if (strpos($p, $kw) !== false || strpos($r, $kw) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Extracts or calculates the number of days for a special non-deductible leave entry.
 */
function getSpecialLeaveDays($log, $conn = null)
{
    // 1. Direct explicit value from log if set
    if (!empty($log['vl_w_pay']) && (float)$log['vl_w_pay'] > 0) {
        return (float)$log['vl_w_pay'];
    }
    if (!empty($log['sl_w_pay']) && (float)$log['sl_w_pay'] > 0) {
        return (float)$log['sl_w_pay'];
    }

    $particulars = (string)($log['particulars'] ?? '');
    $remarks = (string)($log['remarks'] ?? '');
    $period = (string)($log['period'] ?? '');

    // 2. Query leave_applicants if leave_code is present in remarks [Code: XXXXX]
    if ($conn && preg_match('/\[Code:\s*([A-Za-z0-9]+)\]/i', $remarks, $m)) {
        $code = trim($m[1]);
        try {
            $stmt = $conn->prepare("SELECT numDays FROM leave_applicants WHERE leave_code = ? LIMIT 1");
            $stmt->execute([$code]);
            $nd = $stmt->fetchColumn();
            if ($nd && (float)$nd > 0) {
                return (float)$nd;
            }
        } catch (Exception $e) {}
    }

    // 3. Match explicit "X DAYS" in remarks
    if (preg_match('/(\d+(?:\.\d+)?)\s*DAYS?/i', $remarks, $m)) {
        return (float)$m[1];
    }

    // 4. Match trailing number in particulars (e.g. "Wellness Leave 2" => 2, "WELLNESS LEAVE 1" => 1)
    if (preg_match('/(?:Leave|Privilege|Wellness|Emergency|Maternity|Paternity|Parent|Rehab)\s+(\d+(?:\.\d+)?)$/i', $particulars, $m)) {
        return (float)$m[1];
    }
    if (is_numeric(trim($particulars))) {
        return (float)trim($particulars);
    }

    // 5. Calculate weekdays from period date range (e.g. "09-17-26 - 09-18-26")
    if (!empty($period)) {
        $parts = explode(' - ', $period);
        $sStr = trim($parts[0]);
        $eStr = isset($parts[1]) ? trim($parts[1]) : $sStr;
        $tS = strtotime(str_replace('-', '/', $sStr));
        $tE = strtotime(str_replace('-', '/', $eStr));
        if ($tS && $tE) {
            if ($tS > $tE) { $tmp = $tS; $tS = $tE; $tE = $tmp; }
            $cnt = 0;
            $cur = $tS;
            while ($cur <= $tE) {
                $w = (int)date('w', $cur);
                if ($w !== 0 && $w !== 6) { // Weekdays Monday to Friday
                    $cnt++;
                }
                $cur = strtotime('+1 day', $cur);
            }
            if ($cnt > 0) return (float)$cnt;
        }
    }

    return 1.000;
}

/**
 * Synchronizes an approved leave application into leave_credit_logs and deducts from leave balances.
 * Handles:
 * - Mandatory/Forced Leave: 1:1 deduction against Vacation Leave (VL)
 * - Vacation Leave: 1:1 deduction against Vacation Leave (VL)
 * - Sick Leave: 1:1 deduction against Sick Leave (SL), cascading to VL if SL is exhausted
 * - Terminal Leave / Monetization: Deductions against VL and/or SL
 * - Special Non-Cumulative Leaves: 0 balance deduction, days reflected in w/ pay column
 */
function syncLeaveApplicantToCreditLogs($conn, $app)
{
    if (!$conn || empty($app['applicant_id'])) {
        return false;
    }

    $applicant_id = (int)$app['applicant_id'];
    $leave_code = trim((string)($app['leave_code'] ?? ''));
    $numDays = (float)($app['numDays'] ?? 0);

    if (empty($leave_code) || $numDays <= 0) {
        return false;
    }

    // Check if already synchronized in leave_credit_logs
    $stmt_check = $conn->prepare("SELECT log_id FROM leave_credit_logs WHERE personnel_id = ? AND remarks LIKE ? LIMIT 1");
    $stmt_check->execute([$applicant_id, "%[Code: $leave_code]%"]);
    if ($stmt_check->fetch()) {
        return false; // Already logged, prevent duplicate deduction
    }

    // Extract & format period string
    $raw_date = trim((string)($app['leave_date'] ?? ''));
    $period_str = $raw_date;
    if (!empty($raw_date)) {
        $parts = explode(' - ', $raw_date);
        $d1 = strtotime(str_replace('-', '/', trim($parts[0])));
        $d2 = isset($parts[1]) ? strtotime(str_replace('-', '/', trim($parts[1]))) : $d1;
        if ($d1 && $d2) {
            // Only auto-sync approved applications from 2026 onwards for a fresh start (ignore old RFID records)
            if ((int)date('Y', $d1) < 2026) {
                return false;
            }
            $p1 = date('m-d-y', $d1);
            $p2 = date('m-d-y', $d2);
            $period_str = ($p1 === $p2) ? $p1 : ($p1 . ' - ' . $p2);
        }
    }

    $leave_type = trim((string)($app['leave_type'] ?? ''));
    $leave_desc = trim((string)($app['leave_type_desc'] ?? ''));

    if ($leave_type === '1' || empty($leave_type)) {
        $leave_type = !empty($leave_desc) ? $leave_desc : 'Leave';
    }

    $is_forced_leave = (
        $leave_type === 'Mandatory/Forced Leave' || 
        $leave_type === 'Forced Leave' || 
        $leave_type === 'Force Leave' || 
        stripos($leave_type, 'forced leave') !== false || 
        stripos($leave_type, 'force leave') !== false || 
        stripos($leave_desc, 'force leave') !== false || 
        stripos($leave_desc, 'forced leave') !== false
    );

    $particulars = $leave_type;
    if ($is_forced_leave) {
        $particulars = 'Mandatory/Forced Leave';
        $leave_type = 'Mandatory/Forced Leave';
    }

    $is_terminal = (
        stripos($leave_type, 'terminal') !== false || 
        stripos($particulars, 'terminal') !== false || 
        stripos($leave_desc, 'terminal') !== false
    );

    // Safeguard for Terminal Leave: Check if an entry already exists for this personnel on the same period
    if ($is_terminal) {
        $p_first = explode(' - ', $period_str)[0];
        $stmt_check_tl = $conn->prepare("
            SELECT log_id, remarks FROM leave_credit_logs 
            WHERE personnel_id = ? 
            AND (particulars LIKE '%terminal%' OR log_type LIKE '%terminal%' OR remarks LIKE '%terminal%' OR remarks LIKE '%Certified Correct%')
            AND (period = ? OR period LIKE ?)
            LIMIT 1
        ");
        $stmt_check_tl->execute([$applicant_id, $period_str, "%$p_first%"]);
        if ($tl_row = $stmt_check_tl->fetch()) {
            // Already logged! Ensure code is attached to remarks so future checks match directly
            if (stripos($tl_row['remarks'] ?? '', "[Code: $leave_code]") === false) {
                $new_rem = trim(($tl_row['remarks'] ?? 'Certified Correct') . " [Code: $leave_code]");
                $conn->prepare("UPDATE leave_credit_logs SET remarks = ? WHERE log_id = ?")
                     ->execute([$new_rem, $tl_row['log_id']]);
            }
            return false;
        }
    }

    // Fetch current personnel balances
    $stmt_bal = $conn->prepare("SELECT vl_balance, sl_balance FROM personnels WHERE personnel_id = ?");
    $stmt_bal->execute([$applicant_id]);
    $bal = $stmt_bal->fetch(PDO::FETCH_ASSOC);
    $curr_vl = $bal ? (float)$bal['vl_balance'] : 0.000;
    $curr_sl = $bal ? (float)$bal['sl_balance'] : 0.000;

    $vl_deduct = 0.000;
    $sl_deduct = 0.000;
    $vl_w_pay = 0.000;
    $sl_w_pay = 0.000;
    $vl_wout_pay = 0.000;
    $sl_wout_pay = 0.000;

    $is_special = isNonDeductibleSpecialLeave($leave_type, $leave_desc);

    if ($is_special && !$is_forced_leave) {
        // Special non-cumulative leaves (Wellness, Maternity, Paternity, Solo Parent, Rehab, Emergency)
        // 0 deduction from balances, but all days reflected in Absence w/ pay
        $vl_deduct = 0.000;
        $sl_deduct = 0.000;
        $vl_w_pay = $numDays;
        $sl_w_pay = 0.000;
    } else if (stripos($leave_type, 'monetization') !== false || stripos($leave_type, 'terminal') !== false) {
        // Terminal / Monetization: 100% with pay, deducted from balances (VL first, then SL)
        if ($curr_vl >= $numDays) {
            $vl_deduct = $numDays;
            $curr_vl -= $numDays;
            $vl_w_pay = $numDays;
            $sl_deduct = 0.000;
            $sl_w_pay = 0.000;
        } else {
            $vl_deduct = $curr_vl;
            $rem = $numDays - $curr_vl;
            $curr_vl = 0.000;
            $sl_deduct = $rem;
            $curr_sl -= $rem;
            $vl_w_pay = $vl_deduct;
            $sl_w_pay = $rem;
        }
    } else if ($is_forced_leave || stripos($leave_type, 'vacation') !== false || $leave_type === 'Regular Leave') {
        // Mandatory/Forced Leave & Vacation Leave: Tanan sa Absence with pay (VL w/ pay), deducted 1:1 against VL
        $vl_deduct = $numDays;
        $curr_vl -= $numDays;
        $vl_w_pay = $numDays;
        $sl_deduct = 0.000;
        $sl_w_pay = 0.000;
    } else if (stripos($leave_type, 'sick') !== false) {
        // Sick Leave: Tanan sa Absence with pay (SL w/ pay), deducted 1:1 against SL
        $sl_deduct = $numDays;
        $curr_sl -= $numDays;
        $sl_w_pay = $numDays;
        $vl_deduct = 0.000;
        $vl_w_pay = 0.000;
    } else {
        // Fallback: Tanan sa Absence with pay (VL w/ pay), deducted 1:1 against VL
        $vl_deduct = $numDays;
        $curr_vl -= $numDays;
        $vl_w_pay = $numDays;
        $sl_deduct = 0.000;
        $sl_w_pay = 0.000;
    }

    // Approved leaves are with pay: zero absence without pay
    $vl_wout_pay = 0.000;
    $sl_wout_pay = 0.000;

    // Update balances in personnels table
    $update = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
    $update->execute([$curr_vl, $curr_sl, $applicant_id]);

    if ($is_terminal || stripos($particulars, 'terminal') !== false) {
        $logRemarks = "Certified Correct [Code: $leave_code]";
    } else {
        $logRemarks = "Applied for $particulars [Code: $leave_code]";
        if ($vl_wout_pay > 0 || $sl_wout_pay > 0) {
            $tot_lwop = $vl_wout_pay + $sl_wout_pay;
            $logRemarks .= " (Plus $tot_lwop LWOP)";
        }
    }

    $stmt_ins = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, period, particulars, vl_amount, sl_amount, vl_w_pay, sl_w_pay, vl_wout_pay, sl_wout_pay, remarks) VALUES (?, 'Leave Application', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_ins->execute([
        $applicant_id,
        $period_str,
        $particulars,
        -$vl_deduct,
        -$sl_deduct,
        $vl_w_pay,
        $sl_w_pay,
        $vl_wout_pay,
        $sl_wout_pay,
        $logRemarks
    ]);

    return true;
}

/**
 * Automatically cleans up any duplicate Terminal Leave entries created by repeated auto-sync,
 * retaining only the primary/authentic Terminal Leave log entry with the highest deduction,
 * and accurately recalculating the running balance.
 */
function cleanupDuplicateTerminalLeaveLogs($conn, $personnel_id)
{
    if (!$conn || empty($personnel_id)) {
        return;
    }

    try {
        $stmt = $conn->prepare("
            SELECT log_id, period, particulars, log_type, vl_amount, sl_amount, vl_w_pay, sl_w_pay, remarks 
            FROM leave_credit_logs 
            WHERE personnel_id = ? 
            AND (particulars LIKE '%terminal%' OR log_type LIKE '%terminal%' OR remarks LIKE '%terminal%' OR remarks LIKE '%Certified Correct%')
            ORDER BY log_id ASC
        ");
        $stmt->execute([$personnel_id]);
        $tl_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($tl_logs) <= 1) {
            return;
        }

        // Group by normalized period start date
        $groups = [];
        foreach ($tl_logs as $l) {
            $p = trim(explode(' - ', $l['period'] ?? '')[0]);
            $groups[$p][] = $l;
        }

        $deleted_any = false;
        foreach ($groups as $period_key => $rows) {
            if (count($rows) <= 1) {
                continue;
            }

            // Keep the row with the largest combined deduction (the authentic initial terminal leave entry)
            usort($rows, function ($a, $b) {
                $deductA = abs((float)$a['vl_amount']) + abs((float)$a['sl_amount']);
                $deductB = abs((float)$b['vl_amount']) + abs((float)$b['sl_amount']);
                if ($deductA == $deductB) {
                    return $a['log_id'] <=> $b['log_id'];
                }
                return $deductB <=> $deductA; // Descending
            });

            $primary = $rows[0];
            $duplicates = array_slice($rows, 1);

            foreach ($duplicates as $dup) {
                $del = $conn->prepare("DELETE FROM leave_credit_logs WHERE log_id = ?");
                $del->execute([$dup['log_id']]);
                $deleted_any = true;
            }
        }

        if ($deleted_any) {
            // Recalculate balances in personnels table
            $stmt2 = $conn->prepare("SELECT vl_amount, sl_amount, period, log_id FROM leave_credit_logs WHERE personnel_id = ?");
            $stmt2->execute([$personnel_id]);
            $all_logs = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            usort($all_logs, function ($a, $b) {
                $tsA = strtotime(str_replace('-', '/', trim(explode(' - ', $a['period'] ?? '')[0])));
                $tsB = strtotime(str_replace('-', '/', trim(explode(' - ', $b['period'] ?? '')[0])));
                if (!$tsA) $tsA = 0;
                if (!$tsB) $tsB = 0;
                if ($tsA == $tsB) return $a['log_id'] <=> $b['log_id'];
                return $tsA <=> $tsB;
            });
            $v_run = 0;
            $s_run = 0;
            foreach ($all_logs as $l) {
                $v_run += (float)$l['vl_amount'];
                if ($v_run < 0) $v_run = 0;
                $s_run += (float)$l['sl_amount'];
                if ($s_run < 0) $s_run = 0;
            }
            $upd = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
            $upd->execute([$v_run, $s_run, $personnel_id]);
        }
    } catch (Exception $e) {
        // Silently continue
    }
}

/**
 * Scans and auto-syncs any recent unlogged approved leave applications for a personnel into leave_credit_logs.
 */
function syncPersonnelUnloggedLeaves($conn, $personnel_id)
{
    if (!$conn || empty($personnel_id)) {
        return 0;
    }

    try {
        // Automatically prune any duplicate Terminal Leave logs first
        cleanupDuplicateTerminalLeaveLogs($conn, $personnel_id);

        // Only sync applications starting from year 2026 onwards to prevent pulling legacy RFID records
        $stmt = $conn->prepare("
            SELECT * FROM leave_applicants 
            WHERE applicant_id = ? 
            AND leave_code IS NOT NULL AND leave_code != '' 
            AND numDays > 0
            AND (leave_date LIKE '%2026%' OR leave_date LIKE '%/26%' OR leave_date LIKE '%-26%')
            ORDER BY lap_id ASC
        ");
        $stmt->execute([$personnel_id]);
        $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $synced = 0;
        foreach ($apps as $app) {
            if (syncLeaveApplicantToCreditLogs($conn, $app)) {
                $synced++;
            }
        }
        return $synced;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Checks if a personnel has or should have leave credit records / leave card access.
 * Returns true for:
 * - Permanent, Casual, Elective employees
 * - Separated former employees (Death, Retired, Resigned, End of Term, Separated)
 * - Employees with non-zero VL or SL balance
 * - Employees who have leave credit logs or leave applications
 */
function personnelHasLeaveCredits($conn, $staff_row)
{
    if (!$conn || empty($staff_row)) {
        return false;
    }

    // 1. Direct check of status name from staff row if available
    $emp_stat_name = $staff_row['emp_stat_name'] ?? ($staff_row['empStat_name'] ?? '');
    if (!empty($emp_stat_name)) {
        $st = strtolower(trim((string)$emp_stat_name));
        if (strpos($st, 'casual') !== false ||
            strpos($st, 'permanent') !== false ||
            strpos($st, 'elective') !== false ||
            strpos($st, 'death') !== false ||
            strpos($st, 'retired') !== false ||
            strpos($st, 'resigned') !== false ||
            strpos($st, 'separated') !== false ||
            strpos($st, 'end of term') !== false) {
            return true;
        }
    }

    // 2. Check emp_status table by ID
    $emp_stat_id = $staff_row['empStat_id'] ?? null;
    if (!empty($emp_stat_id)) {
        $lc_emp_stat_query = $conn->query("SELECT emp_stat_name, status FROM emp_status WHERE empStat_id='" . intval($emp_stat_id) . "'");
        if ($lc_emp_stat_query && $lc_es = $lc_emp_stat_query->fetch(PDO::FETCH_ASSOC)) {
            $st = strtolower(trim((string)$lc_es['emp_stat_name']));
            $status_field = strtolower(trim((string)($lc_es['status'] ?? '')));
            if (strpos($st, 'casual') !== false ||
                strpos($st, 'permanent') !== false ||
                strpos($st, 'elective') !== false ||
                strpos($st, 'death') !== false ||
                strpos($st, 'retired') !== false ||
                strpos($st, 'resigned') !== false ||
                strpos($st, 'separated') !== false ||
                strpos($st, 'end of term') !== false ||
                $status_field === 'separated') {
                return true;
            }
        }
    }

    // 3. Any non-zero leave balance
    if (floatval($staff_row['vl_balance'] ?? 0) != 0 || floatval($staff_row['sl_balance'] ?? 0) != 0) {
        return true;
    }

    // 4. Any leave credit logs
    $pid = $staff_row['personnel_id'] ?? null;
    if (!empty($pid)) {
        $chk_logs = $conn->prepare("SELECT log_id FROM leave_credit_logs WHERE personnel_id = ? LIMIT 1");
        $chk_logs->execute([$pid]);
        if ($chk_logs->fetch()) {
            return true;
        }

        // 5. Any leave applications / requests
        $chk_apps = $conn->prepare("SELECT lap_id FROM leave_applicants WHERE applicant_id = ? LIMIT 1");
        $chk_apps->execute([$pid]);
        if ($chk_apps->fetch()) {
            return true;
        }

        try {
            $chk_apps2 = $conn->prepare("SELECT lap_id FROM leave_applicants WHERE personnel_id = ? LIMIT 1");
            $chk_apps2->execute([$pid]);
            if ($chk_apps2->fetch()) {
                return true;
            }
        } catch (Exception $e) {}
    }

    return false;
}

