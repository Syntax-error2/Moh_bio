<?php

/** Return the calendar holiday for a date, sharing the result across a bulk report. */
function dtrHolidayForDate(PDO $conn, string $isoDate): ?array
{
    static $cache = [];
    $key = spl_object_id($conn) . ':' . $isoDate;
    if (!array_key_exists($key, $cache)) {
        $query = $conn->prepare("SELECT event_title, act_type, status FROM activity_calendar WHERE completeDate = :completeDate AND (act_type IN ('Legal Holiday', 'Special Non-Working Holiday', 'Special Working Holiday') OR UPPER(event_title) LIKE '%HOLIDAY%') ORDER BY CASE WHEN act_type IN ('Legal Holiday', 'Special Non-Working Holiday') THEN 0 ELSE 1 END LIMIT 1");
        $query->execute(['completeDate' => $isoDate]);
        $cache[$key] = $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    return $cache[$key];
}

function dtrIsNonWorkingHoliday(?array $holiday): bool
{
    return $holiday !== null
        && in_array($holiday['act_type'], ['Legal Holiday', 'Special Non-Working Holiday'], true)
        && $holiday['status'] !== 'Add as working day';
}

require_once __DIR__ . '/official_duty.php';

function dtrCheckPassSlip(PDO $conn, string $remarks, ?string $travelCode = null): array
{
    if (isPassSlipNotation($remarks)) {
        return [
            'is_pass_slip' => true,
            'time_info' => ''
        ];
    }
    if (!empty($travelCode)) {
        if (stripos($travelCode, 'PS') !== false) {
            return [
                'is_pass_slip' => true,
                'time_info' => ''
            ];
        }
        static $ps_cache = [];
        $key = spl_object_id($conn) . ':' . $travelCode;
        if (isset($ps_cache[$key])) {
            return $ps_cache[$key];
        }
        $stmt = $conn->prepare("SELECT travel_type, description, time_out, time_in FROM personnel_official_travel_logs WHERE travel_code = ? LIMIT 1");
        $stmt->execute([$travelCode]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isPassSlipNotation($row['travel_type'] ?? '') || isPassSlipNotation($row['description'] ?? '')) {
                $time_str = '';
                if (!empty($row['time_out']) && !empty($row['time_in'])) {
                    $time_str = $row['time_out'] . ' - ' . $row['time_in'];
                } elseif (!empty($row['time_out'])) {
                    $time_str = 'Out: ' . $row['time_out'];
                } elseif (!empty($row['time_in'])) {
                    $time_str = 'In: ' . $row['time_in'];
                }
                $ps_cache[$key] = [
                    'is_pass_slip' => true,
                    'time_info' => $time_str
                ];
                return $ps_cache[$key];
            }
        }
        $ps_cache[$key] = [
            'is_pass_slip' => false,
            'time_info' => ''
        ];
        return $ps_cache[$key];
    }
    return [
        'is_pass_slip' => false,
        'time_info' => ''
    ];
}

function formatCsf48Time($timeStr): string
{
    if (empty($timeStr) || $timeStr === '--:--' || $timeStr === '00:00:00') {
        return '--:--';
    }
    $ts = strtotime($timeStr);
    if ($ts === false) {
        return htmlspecialchars((string)$timeStr, ENT_QUOTES, 'UTF-8');
    }
    return date('h:i', $ts) . ' <sup>' . date('a', $ts) . '</sup>';
}
