<?php
include_once('dbcon.php');
include_once('myFunctions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['RFTag_id'])) {
    file_put_contents('debug_bio.txt', date('Y-m-d H:i:s') . " - POST received: " . print_r($_POST, true) . "\n", FILE_APPEND);
    $rftag = trim($_POST['RFTag_id']); // This might be biometric_id or RFTag_id
    
    // Normalize localhost IPv6 to IPv4
    if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '::1') {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    
    try {
        // Check if the user exists
        $personnelQuery = $conn->prepare("SELECT personnel_id, biometric_id, RFTag_id, lname, fname, mname, suffix, do_id, shift_id, img FROM personnels WHERE RFTag_id = :tag1 OR biometric_id = :tag2 OR personnel_id = :tag3 LIMIT 1");
        $personnelQuery->execute(['tag1' => $rftag, 'tag2' => $rftag, 'tag3' => $rftag]);
        $personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC);
        
        if($personnel) {
            $biometricId = $personnel['biometric_id'] ?: $rftag; // Fallback to whatever tag they used
            $personnelId = $personnel['personnel_id'];
            $rfTagId = $personnel['RFTag_id'] ?: $biometricId; // Ensure we have a string for RFTag_id
            
            // 1-Minute Buffer Time Check to prevent accidental double scans
            $bufferQuery = $conn->prepare("SELECT time_in, time_out FROM bio_dtr WHERE personnel_id = :pid AND log_date = :ldate ORDER BY time_in DESC LIMIT 1");
            $bufferQuery->execute(['pid' => $personnelId, 'ldate' => $currentDate]);
            $lastLog = $bufferQuery->fetch(PDO::FETCH_ASSOC);
            
            if ($lastLog) {
                if (empty($lastLog['time_out']) || $lastLog['time_out'] == '00:00:00') {
                    $lastActionTime = $lastLog['time_in'];
                } else {
                    $lastActionTime = $lastLog['time_out'];
                }
                $timeDiff = strtotime($currentTime) - strtotime($lastActionTime);
                if ($timeDiff >= 0 && $timeDiff < 60) {
                    echo json_encode(['status' => 'error', 'message' => '<span style="font-size: 0.65em; line-height: 1.2;">Scanned too recently. Please wait 1 min.</span>', 'sound' => 'invalid']);
                    exit;
                }
            }
            
            // Intelligently determine flow based on existing personnel_logs
            $personnelLogDate = date('m/d/Y'); // personnel_logs expects m/d/Y
            $hour = (int)date('H', strtotime($currentTime));
            
            // Determine flow exclusively based on bio_dtr records for today
            $recentLogQuery = $conn->prepare("SELECT time_in, time_out FROM bio_dtr WHERE personnel_id = :pid AND log_date = :ldate ORDER BY bio_id DESC LIMIT 1");
            $recentLogQuery->execute(['pid' => $personnelId, 'ldate' => $currentDate]);
            $recentLog = $recentLogQuery->fetch(PDO::FETCH_ASSOC);

            $flow = 'IN'; // Default to IN if no logs today
            if ($recentLog) {
                // If the most recent log has no time_out, they are currently IN, so the next action must be OUT
                if ($recentLog['time_out'] === null || $recentLog['time_out'] === '00:00:00') {
                    $flow = 'OUT';
                } else {
                    // They have completed an IN-OUT cycle, so the next action is a fresh IN
                    $flow = 'IN';
                }
            }
            
            // Find if there's an open log for today
            $openLogQuery = $conn->prepare("SELECT time_in FROM bio_dtr WHERE personnel_id = :pid AND log_date = :ldate AND (time_out IS NULL OR time_out = '00:00:00') ORDER BY time_in DESC LIMIT 1");
            $openLogQuery->execute(['pid' => $personnelId, 'ldate' => $currentDate]);
            $openLog = $openLogQuery->fetch(PDO::FETCH_ASSOC);
            
            if ($flow == 'OUT') {
                if ($openLog) {
                    // Update only the most recent open log (essential if a previous log was missed and left open)
                    $update = $conn->prepare("UPDATE bio_dtr SET time_out = :tout WHERE personnel_id = :pid AND log_date = :ldate AND (time_out IS NULL OR time_out = '00:00:00') ORDER BY time_in DESC LIMIT 1");
                    $update->execute(['tout' => $currentTime, 'pid' => $personnelId, 'ldate' => $currentDate]);
                } else {
                    // Insert a new log with only time_out (missed IN)
                    $insertOut = $conn->prepare("INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out) VALUES (:pid, :bid, :ldate, '00:00:00', :tout)");
                    $insertOut->execute(['pid' => $personnelId, 'bid' => $biometricId, 'ldate' => $currentDate, 'tout' => $currentTime]);
                }
            } else {
                // flow == 'IN'
                // Create a new IN log
                $insert = $conn->prepare("INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out) VALUES (:pid, :bid, :ldate, :tin, '00:00:00')");
                $insert->execute(['pid' => $personnelId, 'bid' => $biometricId, 'ldate' => $currentDate, 'tin' => $currentTime]);
            }
            
            // Sync to personnel_logs
            $logTimeFormatted = date('h:i:s a', strtotime($currentTime));
            $str_current_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", date("H:i:s", strtotime($currentTime)));
            sscanf($str_current_time, "%d:%d:%d", $hours, $minutes, $seconds);
            $logTimeSec = ($hours * 3600) + ($minutes * 60) + $seconds;
            
            if ($flow == 'IN') {
                $personnelFlow = ($hour < 12) ? 'AM IN' : 'PM IN';
            } else {
                $personnelFlow = ($hour < 13) ? 'AM OUT' : 'PM OUT';
            }
            
            $checkLog = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :rftag AND logDate = :ldate AND logFlow = :lflow");
            $checkLog->execute(['rftag' => $rfTagId, 'ldate' => $personnelLogDate, 'lflow' => $personnelFlow]);
            
            $shouldInsert = true;
            if ($checkLog->rowCount() > 0) {
                if ($flow == 'IN') {
                    $shouldInsert = false;
                } else {
                    $delStmt = $conn->prepare("DELETE FROM personnel_logs WHERE RFTag_id = :rftag AND logDate = :ldate AND logFlow = :lflow");
                    $delStmt->execute(['rftag' => $rfTagId, 'ldate' => $personnelLogDate, 'lflow' => $personnelFlow]);
                }
            }
            
            if ($shouldInsert) {
                $img = $personnel['img'] ? $personnel['img'] : '';
                $insertPersonnel = $conn->prepare("INSERT INTO personnel_logs (RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, late_status, logFlow, client_ip, remarks, captured_img, travel_leave_code) 
                    VALUES (:rftag, :img, :lname, :fname, :mname, :suffix, :doid, :shiftid, :ldate, :ltime, :ltimesec, 'off', :lflow, :ip, '', '', '')");
                $insertPersonnel->execute([
                    'rftag' => $rfTagId,
                    'img' => $img,
                    'lname' => $personnel['lname'],
                    'fname' => $personnel['fname'],
                    'mname' => $personnel['mname'],
                    'suffix' => $personnel['suffix'],
                    'doid' => $personnel['do_id'],
                    'shiftid' => $personnel['shift_id'],
                    'ldate' => $personnelLogDate,
                    'ltime' => $logTimeFormatted,
                    'ltimesec' => $logTimeSec,
                    'lflow' => $personnelFlow,
                    'ip' => $ip
                ]);
            }
            
            $output = json_encode(['status' => 'success', 'flow' => $flow, 'sound' => 'valid']);
            file_put_contents('debug_bio.txt', date('Y-m-d H:i:s') . " - Output: " . $output . "\n", FILE_APPEND);
            echo $output;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Personnel not found.', 'sound' => 'invalid']);
        }
    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage(), 'sound' => 'invalid']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.', 'sound' => 'invalid']);
}
