<?php
define('FAST_DB_INIT', true);
include_once('dbcon.php');

if(isset($_GET['RFTag_id'])) {
    $tag = $_GET['RFTag_id'];
    $currentDate = date('Y-m-d');
    
    // First find the person (exact match on RFTag_id or biometric_id)
    $personnelQuery = $conn->prepare("SELECT personnel_id, img, lname, fname, biometric_id, RFTag_id FROM personnels WHERE RFTag_id = :tag1 OR biometric_id = :tag2 LIMIT 1");
    $personnelQuery->execute(['tag1' => $tag, 'tag2' => $tag]);
    $personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC);

    // Fallback: only if input is purely numeric
    if (!$personnel && ctype_digit((string)$tag)) {
        $personnelQuery = $conn->prepare("SELECT personnel_id, img, lname, fname, biometric_id, RFTag_id FROM personnels WHERE personnel_id = :pid LIMIT 1");
        $personnelQuery->execute(['pid' => (int)$tag]);
        $personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC);
    }
    
    if($personnel) {
        $personnelId = $personnel['personnel_id'];
        
        // Fetch their latest log for today
        $query = $conn->prepare("SELECT time_in, time_out 
                                 FROM bio_dtr 
                                 WHERE personnel_id = :pid AND log_date = :logDate
                                 ORDER BY bio_id DESC 
                                 LIMIT 1");
        $query->execute(['pid' => $personnelId, 'logDate' => $currentDate]);
        $log = $query->fetch(PDO::FETCH_ASSOC);
        
        $img = $personnel['img'] ? 'personnelImg/'.$personnel['img'] : 'personnelImg/default_img.jpg';
        $name = strtoupper($personnel['lname']) . ", " . $personnel['fname'];
        
        $logFlow = 'TIME IN'; // default
        if($log) {
            if ($log['time_out'] !== null && $log['time_out'] !== '00:00:00') {
                $logFlow = 'TIME OUT';
            } else {
                $logFlow = 'TIME IN';
            }
        }

        // Check if personnel_logs has the exact flow (e.g. AM IN, AM OUT, PM IN, PM OUT)
        $tagList = array_values(array_unique(array_filter([$personnel['RFTag_id'], $personnel['biometric_id'], $tag])));
        $inPlaceholders = implode(',', array_fill(0, count($tagList), '?'));
        $flowStmt = $conn->prepare("SELECT logFlow FROM personnel_logs WHERE RFTag_id IN ($inPlaceholders) AND logDate IN (?, ?) ORDER BY log_id DESC LIMIT 1");
        $flowStmt->execute(array_merge($tagList, [date('m/d/Y'), date('Y-m-d')]));
        $exactFlow = $flowStmt->fetchColumn();
        if ($exactFlow) {
            $logFlow = $exactFlow;
        }
        
        echo json_encode([
            'status' => 'success',
            'img' => $img,
            'name' => $name,
            'logFlow' => $logFlow
        ]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>
