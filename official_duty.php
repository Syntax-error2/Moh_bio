<?php

function isOfficialDutyNotation($notation)
{
    $normalized = strtoupper(preg_replace('/\s+/', ' ', trim((string)$notation)));
    return in_array($normalized, [
        'OFFICIAL BUSINESS',
        'OFFICIAL BUSINESS TRIP',
        'BUSINESS TRIP',
        'OFFICIAL TRAVEL',
        'OFFICIAL TRAVEL ORDER',
    ], true);
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
