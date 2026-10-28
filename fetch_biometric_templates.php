<?php
define('FAST_DB_INIT', true);
ob_start();
include('dbcon.php');
ob_clean();

// Query recent punches to prioritize employees who actively attend work
$recentPunches = [];
try {
    $recentPunches = $conn->query("
        SELECT personnel_id, MAX(log_date) as last_date 
        FROM bio_dtr 
        GROUP BY personnel_id
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {}

$stmt = $conn->query("SELECT p.personnel_id, p.RFTag_id, p.biometric_id, p.fingerprint_template FROM personnels p JOIN emp_status es ON es.empStat_id = p.empStat_id WHERE es.status = 'Active' AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ') AND p.fingerprint_template IS NOT NULL AND p.fingerprint_template != ''");

$primaryTemplates = [];
$secondaryTemplates = [];
$fingerOrder = ['rt' => 1, 'ri' => 2, 'lt' => 3, 'li' => 4];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pid = $row['personnel_id'];
    $lastLogDate = $recentPunches[$pid] ?? '1970-01-01';
    
    $data = json_decode($row['fingerprint_template'], true);
    if (is_array($data)) {
        foreach($data as $finger => $base64) {
            $item = [
                'personnel_id' => $row['personnel_id'],
                'RFTag_id' => $row['RFTag_id'],
                'biometric_id' => $row['biometric_id'],
                'finger' => $finger,
                'fingerprint_template' => $base64,
                '_last_log' => $lastLogDate,
                '_priority' => $fingerOrder[$finger] ?? 99
            ];
            if ($finger === 'rt' || $finger === 'ri') {
                $primaryTemplates[] = $item;
            } else {
                $secondaryTemplates[] = $item;
            }
        }
    } else {
        $primaryTemplates[] = [
            'personnel_id' => $row['personnel_id'],
            'RFTag_id' => $row['RFTag_id'],
            'biometric_id' => $row['biometric_id'],
            'finger' => 'legacy',
            'fingerprint_template' => $row['fingerprint_template'],
            '_last_log' => $lastLogDate,
            '_priority' => 1
        ];
    }
}

// Sort primary templates (Right Thumb, Right Index) by recent attendance first
usort($primaryTemplates, function($a, $b) {
    if ($a['_last_log'] !== $b['_last_log']) {
        return strcmp($b['_last_log'], $a['_last_log']);
    }
    if ($a['_priority'] !== $b['_priority']) {
        return $a['_priority'] <=> $b['_priority'];
    }
    return $a['personnel_id'] <=> $b['personnel_id'];
});

// Sort secondary templates (Left Thumb, Left Index) by recent attendance
usort($secondaryTemplates, function($a, $b) {
    if ($a['_last_log'] !== $b['_last_log']) {
        return strcmp($b['_last_log'], $a['_last_log']);
    }
    return $a['_priority'] <=> $b['_priority'];
});

// Clean internal sorting metadata
$flatTemplates = array_map(function($t) {
    unset($t['_last_log'], $t['_priority']);
    return $t;
}, array_merge($primaryTemplates, $secondaryTemplates));

$response = json_encode($flatTemplates);
if ($response === false) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Unable to load fingerprint templates.']);
    exit;
}
$etag = '"' . hash('sha256', $response) . '"';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-cache');
header('ETag: ' . $etag);
if (trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}
echo $response;
?>
