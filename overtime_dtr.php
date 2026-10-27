<?php
// OT punches are evidence of time worked. Only an approved request credits minutes.
function overtimeDtrLoad(PDO $conn, $personnelId, $rfTagId, $startDate, $endDate)
{
    $result = ['requests' => [], 'sessions' => []];
    try {
        $requests = $conn->prepare("SELECT ot_date, status FROM personnel_overtime_requests WHERE personnel_id = ? AND ot_date BETWEEN ? AND ? ORDER BY ot_id");
        $requests->execute([$personnelId, $startDate, $endDate]);
        while ($row = $requests->fetch(PDO::FETCH_ASSOC)) {
            $date = $row['ot_date'];
            $status = strtolower($row['status'] ?? '');
            if ($status === 'approved' || !isset($result['requests'][$date]) || $result['requests'][$date] !== 'approved' && $status === 'pending') {
                $result['requests'][$date] = $status;
            }
        }
    } catch (Exception $e) {
        // Fallback if table or column does not exist
    }

    $punches = $conn->prepare("SELECT logDate, logTime, logFlow, log_id FROM personnel_logs WHERE RFTag_id = ? AND logFlow IN ('OT IN', 'OT OUT') AND (logDate BETWEEN ? AND ? OR logDate LIKE ? OR logDate LIKE ?) ORDER BY log_id");
    $legacyMonth = date('m', strtotime($startDate)) . '/%/' . date('Y', strtotime($startDate));
    $legacyShort = (int)date('m', strtotime($startDate)) . '/%/' . date('Y', strtotime($startDate));
    $punches->execute([$rfTagId, $startDate, $endDate, $legacyMonth, $legacyShort]);
    $open = [];
    while ($row = $punches->fetch(PDO::FETCH_ASSOC)) {
        $dateTime = strtotime($row['logDate']);
        if ($dateTime === false) continue;
        $date = date('Y-m-d', $dateTime);
        if ($date < $startDate || $date > $endDate) continue;
        $time = date('H:i:s', strtotime($row['logTime']));
        if ($row['logFlow'] === 'OT IN') {
            $open[$date] = $time;
            $result['sessions'][$date][] = ['in' => $time, 'out' => null];
        } elseif (isset($open[$date]) && !empty($result['sessions'][$date])) {
            $last = count($result['sessions'][$date]) - 1;
            if ($result['sessions'][$date][$last]['out'] === null && $time > $open[$date]) {
                $result['sessions'][$date][$last]['out'] = $time;
                unset($open[$date]);
            }
        }
    }
    return $result;
}

function overtimeDtrDay(array $data, $date, $legacyMinutes = 0)
{
    $sessions = $data['sessions'][$date] ?? [];
    $status = $data['requests'][$date] ?? null;
    $minutes = 0;
    $ranges = [];
    foreach ($sessions as $session) {
        if ($session['out'] !== null) {
            $minutes += max(0, (int)floor((strtotime($session['out']) - strtotime($session['in'])) / 60));
            $ranges[] = substr($session['in'], 0, 5) . '-' . substr($session['out'], 0, 5);
        } else {
            $ranges[] = substr($session['in'], 0, 5) . '-OUT NEEDED';
        }
    }
    $rangeText = implode(', ', $ranges);
    if ($status === 'approved') {
        return ['minutes' => $sessions ? $minutes : max(0, (int)floor($legacyMinutes)), 'label' => $sessions ? 'OT ' . $rangeText : ''];
    }
    if ($sessions) {
        return ['minutes' => 0, 'label' => ($status === 'rejected' ? 'OT REJECTED ' : 'PENDING OT ') . $rangeText];
    }
    return ['minutes' => 0, 'label' => $status === 'pending' ? 'OT REQUEST PENDING' : ''];
}
