<?php
include_once('dbcon.php');

if(isset($_GET['RFTag_id'])) {
    $tag = $_GET['RFTag_id'];
    $currentDate = date('Y-m-d');
    
    // First find the person
    $personnelQuery = $conn->prepare("SELECT personnel_id, img, lname, fname, biometric_id FROM personnels WHERE RFTag_id = :tag1 OR biometric_id = :tag2 LIMIT 1");
    $personnelQuery->execute(['tag1' => $tag, 'tag2' => $tag]);
    $personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC);
    
    if($personnel) {
        $personnelId = $personnel['personnel_id'];
        
        // Fetch their latest log for today
        $query = $conn->prepare("SELECT time_in, time_out 
                                 FROM bio_dtr 
                                 WHERE personnel_id = :pid AND log_date = :logDate
                                 ORDER BY time_in DESC 
                                 LIMIT 1");
        $query->execute(['pid' => $personnelId, 'logDate' => $currentDate]);
        $log = $query->fetch(PDO::FETCH_ASSOC);
        
        $img = $personnel['img'] ? 'personnelImg/'.$personnel['img'] : 'personnelImg/default_img.jpg';
        $name = strtoupper($personnel['lname']) . ", " . $personnel['fname'];
        
        $logFlow = 'IN'; // default if no logs exist (but this shouldn't happen if they just scanned)
        $logTime = date('h:i A'); // fallback to current time
        if($log) {
            // If time_out is not null, their last action was OUT
            if ($log['time_out'] !== null && $log['time_out'] !== '00:00:00') {
                $logFlow = 'TIME OUT';
                $logTime = date('h:i A', strtotime($log['time_out']));
            } else {
                $logFlow = 'TIME IN';
                $logTime = date('h:i A', strtotime($log['time_in']));
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'img' => $img,
            'name' => $name,
            'logFlow' => $logFlow,
            'time' => $logTime
        ]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>
