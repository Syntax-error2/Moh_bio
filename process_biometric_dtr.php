<?php
define('FAST_DB_INIT', true);
include_once('dbcon.php');
include_once('myFunctions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['RFTag_id'])) {
    $rftag = trim($_POST['RFTag_id']); // This might be biometric_id or RFTag_id
    
    // Normalize localhost IPv6 to IPv4
    if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '::1') {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    
    try {
        // Check if the user exists: exact string match on RFTag_id or biometric_id FIRST
        $personnelQuery = $conn->prepare("SELECT p.personnel_id, p.biometric_id, p.RFTag_id, p.lname, p.fname, p.mname, p.suffix, p.do_id, p.shift_id, p.img 
                                          FROM personnels p 
                                          JOIN emp_status es ON es.empStat_id = p.empStat_id 
                                          WHERE (p.RFTag_id = :tag1 OR p.biometric_id = :tag2) 
                                            AND es.status = 'Active' 
                                            AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ') 
                                          LIMIT 1");
        $personnelQuery->execute(['tag1' => $rftag, 'tag2' => $rftag]);
        $personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC);
        
        // Fallback: only if input is purely numeric digits, match personnel_id (avoids MySQL string coercion)
        if (!$personnel && ctype_digit((string)$rftag)) {
            $personnelQuery = $conn->prepare("SELECT p.personnel_id, p.biometric_id, p.RFTag_id, p.lname, p.fname, p.mname, p.suffix, p.do_id, p.shift_id, p.img 
                                              FROM personnels p 
                                              JOIN emp_status es ON es.empStat_id = p.empStat_id 
                                              WHERE p.personnel_id = :pid 
                                                AND es.status = 'Active' 
                                                AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ') 
                                              LIMIT 1");
            $personnelQuery->execute(['pid' => (int)$rftag]);
            $personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC);
        }

        // Fallback: match personnel_id_code
        if (!$personnel) {
            $personnelQuery = $conn->prepare("SELECT p.personnel_id, p.biometric_id, p.RFTag_id, p.lname, p.fname, p.mname, p.suffix, p.do_id, p.shift_id, p.img 
                                              FROM personnels p 
                                              JOIN emp_status es ON es.empStat_id = p.empStat_id 
                                              WHERE p.personnel_id_code = :pcode 
                                                AND es.status = 'Active' 
                                                AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ') 
                                              LIMIT 1");
            $personnelQuery->execute(['pcode' => $rftag]);
            $personnel = $personnelQuery->fetch(PDO::FETCH_ASSOC);
        }
        
        if($personnel) {
            $biometricId = $personnel['biometric_id'] ?: $rftag; // Fallback to whatever tag they used
            $personnelId = (int)$personnel['personnel_id'];
            $rfTagId = $personnel['RFTag_id'] ?: $biometricId; // Ensure we have a string for RFTag_id

            // Serialize scans for the same person across kiosks and verify active state in 1 atomic query
            $conn->beginTransaction();
            $lockPersonnel = $conn->prepare("SELECT p.personnel_id FROM personnels p JOIN emp_status es ON es.empStat_id = p.empStat_id WHERE p.personnel_id = :pid AND es.status = 'Active' AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ') FOR UPDATE");
            $lockPersonnel->execute(['pid' => $personnelId]);
            if (!$lockPersonnel->fetchColumn()) {
                $conn->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Personnel is no longer active.', 'sound' => 'invalid']);
                exit;
            }
            
            // Consolidated single query for 1-minute buffer check and today's open log detection
            $bufferQuery = $conn->prepare("SELECT bio_id, time_in, time_out FROM bio_dtr WHERE personnel_id = :pid AND log_date = :ldate ORDER BY bio_id DESC LIMIT 1");
            $bufferQuery->execute(['pid' => $personnelId, 'ldate' => $currentDate]);
            $lastLog = $bufferQuery->fetch(PDO::FETCH_ASSOC);
            
            if ($lastLog) {
                $lastActionTime = (empty($lastLog['time_out']) || $lastLog['time_out'] === '00:00:00') ? $lastLog['time_in'] : $lastLog['time_out'];
                $timeDiff = strtotime($currentTime) - strtotime($lastActionTime);
                if ($timeDiff >= 0 && $timeDiff < 60) {
                    $conn->rollBack();
                    echo json_encode(['status' => 'error', 'message' => '<span style="font-size: 0.65em; line-height: 1.2;">Scanned too recently. Please wait 1 min.</span>', 'sound' => 'invalid']);
                    exit;
                }
            }
            
            // Build list of all possible identifier tags for this person (RFTag_id, biometric_id, and scanned tag)
            $tagList = array_values(array_unique(array_filter([$personnel['RFTag_id'], $personnel['biometric_id'], $rftag])));
            $inPlaceholders = implode(',', array_fill(0, count($tagList), '?'));
            
            // Support both m/d/Y and Y-m-d formats when checking existing logs
            $dateMdy = date('m/d/Y');
            $dateYmd = date('Y-m-d');
            $personnelLogDate = $dateMdy; // Preferred for new insertions
            $hour = (int)date('H', strtotime($currentTime));
            $minute = (int)date('i', strtotime($currentTime));

            // Find if there's an open log in bio_dtr for today from the already-fetched lastLog
            $openLog = ($lastLog && (empty($lastLog['time_out']) || $lastLog['time_out'] === '00:00:00')) ? $lastLog : null;
            $hasOpenBio = (bool)$openLog;
            $openHour = $hasOpenBio ? (int)date('H', strtotime($openLog['time_in'])) : null;
            $isOpenMorningBio = $hasOpenBio && ($openHour !== null && $openHour < 12);
            
            // Check existing flows in personnel_logs in a single fast query
            $stmt_flows = $conn->prepare("SELECT DISTINCT logFlow FROM personnel_logs WHERE RFTag_id IN ($inPlaceholders) AND logDate IN (?, ?)");
            $stmt_flows->execute(array_merge($tagList, [$dateMdy, $dateYmd]));
            $existingFlows = $stmt_flows->fetchAll(PDO::FETCH_COLUMN);

            $has_am_in = in_array('AM IN', $existingFlows) || $isOpenMorningBio;
            $has_am_out = in_array('AM OUT', $existingFlows);
            $has_pm_in = in_array('PM IN', $existingFlows);
            $has_pm_out = in_array('PM OUT', $existingFlows);

            $has_open_ot = false;
            if ($has_pm_out) {
                $otOpenQuery = $conn->prepare("SELECT i.log_id FROM personnel_logs i WHERE i.RFTag_id IN ($inPlaceholders) AND i.logDate IN (?, ?) AND i.logFlow='OT IN' AND NOT EXISTS (SELECT 1 FROM personnel_logs o WHERE o.RFTag_id=i.RFTag_id AND o.logDate=i.logDate AND o.logFlow='OT OUT' AND o.log_id>i.log_id) ORDER BY i.log_id DESC LIMIT 1");
                $otOpenQuery->execute(array_merge($tagList, [$dateMdy, $dateYmd]));
                $has_open_ot = (bool)$otOpenQuery->fetchColumn();
            }

            $isLateLunchOut = ($hour >= 13 && $hour < 15 && ($has_am_in || $isOpenMorningBio) && !$has_am_out && !$has_pm_in);
            $flow = '';
            $personnelFlow = '';

            if ($has_pm_out) {
                // Punches after regular PM OUT are overtime
                $flow = $has_open_ot ? 'OUT' : 'IN';
                $personnelFlow = ($flow == 'IN') ? 'OT IN' : 'OT OUT';
            } else if ($hour < 12) {
                // Morning (< 12:00 PM)
                if (!$has_am_in) {
                    $flow = 'IN';
                    $personnelFlow = 'AM IN';
                } else {
                    // Clocked in morning, now clocking out (undertime / early departure)
                    $flow = 'OUT';
                    $personnelFlow = 'AM OUT';
                }
            } else if ($hour == 12) {
                // 12:00 PM - 12:59 PM (Lunch hour)
                if (($has_am_in || $isOpenMorningBio) && !$has_am_out) {
                    // Standard lunch departure: closes morning session as AM OUT
                    $flow = 'OUT';
                    $personnelFlow = 'AM OUT';
                } else if ($has_am_out) {
                    // If already timed AM OUT, prevent accidental premature PM IN before 12:30 PM
                    if ($minute < 30) {
                        $conn->rollBack();
                        echo json_encode([
                            'status' => 'error',
                            'message' => '<span style="font-size: 0.65em; line-height: 1.2;">Already timed AM OUT.<br>PM IN begins at 12:30 PM.</span>',
                            'sound' => 'invalid'
                        ]);
                        exit;
                    }
                    if ($has_pm_in) {
                        $conn->rollBack();
                        echo json_encode([
                            'status' => 'error',
                            'message' => '<span style="font-size: 0.65em; line-height: 1.2;">Already timed PM IN.<br>PM OUT begins in the afternoon.</span>',
                            'sound' => 'invalid'
                        ]);
                        exit;
                    }
                    // 12:30 PM onwards: returning from lunch
                    $flow = 'IN';
                    $personnelFlow = 'PM IN';
                } else {
                    // Neither AM IN nor AM OUT in personnel_logs
                    if ($isOpenMorningBio) {
                        $flow = 'OUT';
                        $personnelFlow = 'AM OUT';
                    } else if ($minute < 30) {
                        // 12:00 - 12:29 PM: lunch departure
                        $flow = 'OUT';
                        $personnelFlow = 'AM OUT';
                    } else {
                        // 12:30 - 12:59 PM: arrival for afternoon
                        $flow = 'IN';
                        $personnelFlow = 'PM IN';
                    }
                }
            } else {
                // 13:00 onwards
                if (!$has_pm_in) {
                    if ($isLateLunchOut) {
                        // A late lunch scan still closes the morning session
                        $flow = 'OUT';
                        $personnelFlow = 'AM OUT';
                    } else if ($hour >= 15) {
                        // Leaving for the day without prior PM IN
                        $flow = 'OUT';
                        $personnelFlow = 'PM OUT';
                    } else {
                        // Arriving for afternoon shift
                        $flow = 'IN';
                        $personnelFlow = 'PM IN';
                    }
                } else {
                    // Clocking out for the day
                    $flow = 'OUT';
                    $personnelFlow = 'PM OUT';
                }
            }
            
            if ($flow == 'OUT') {
                if ($openLog) {
                    // Update only the most recent open log and reset is_exported so the completed record is exported
                    $update = $conn->prepare("UPDATE bio_dtr SET time_out = :tout, is_exported = 0 WHERE bio_id = :bid");
                    $update->execute(['tout' => $currentTime, 'bid' => $openLog['bio_id']]);
                } else {
                    // Insert a new log with only time_out (missed IN)
                    $insertOut = $conn->prepare("INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out, is_exported) VALUES (:pid, :bid, :ldate, '00:00:00', :tout, 0)");
                    $insertOut->execute(['pid' => $personnelId, 'bid' => $biometricId, 'ldate' => $currentDate, 'tout' => $currentTime]);
                }
            } else {
                // flow == 'IN': Create a new IN log
                $insert = $conn->prepare("INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out, is_exported) VALUES (:pid, :bid, :ldate, :tin, '00:00:00', 0)");
                $insert->execute(['pid' => $personnelId, 'bid' => $biometricId, 'ldate' => $currentDate, 'tin' => $currentTime]);
            }
            
            // Sync to personnel_logs
            $logTimeFormatted = date('h:i:s a', strtotime($currentTime));
            $str_current_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", date("H:i:s", strtotime($currentTime)));
            sscanf($str_current_time, "%d:%d:%d", $hours, $minutes, $seconds);
            $logTimeSec = ($hours * 3600) + ($minutes * 60) + $seconds;
            
            $shouldInsert = true;
            $existingRegularLog = false;
            if (!$has_pm_out) {
                $checkLog = $conn->prepare("SELECT 1 FROM personnel_logs WHERE RFTag_id IN ($inPlaceholders) AND logDate IN (?, ?) AND logFlow = ? LIMIT 1");
                $checkLog->execute(array_merge($tagList, [$dateMdy, $dateYmd, $personnelFlow]));
                $existingRegularLog = (bool)$checkLog->fetchColumn();
            }
            if ($existingRegularLog) {
                if ($flow == 'IN') {
                    $shouldInsert = false;
                } else {
                    $delStmt = $conn->prepare("DELETE FROM personnel_logs WHERE RFTag_id IN ($inPlaceholders) AND logDate IN (?, ?) AND logFlow = ?");
                    $delStmt->execute(array_merge($tagList, [$dateMdy, $dateYmd, $personnelFlow]));
                }
            }
            
            if ($shouldInsert) {
                $img = $personnel['img'] ? $personnel['img'] : '';
                $insertPersonnel = $conn->prepare("INSERT INTO personnel_logs (RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, late_status, logFlow, client_ip, remarks, captured_img, travel_leave_code, ref_log_id, is_exported) 
                    VALUES (:rftag, :img, :lname, :fname, :mname, :suffix, :doid, :shiftid, :ldate, :ltime, :ltimesec, 'off', :lflow, :ip, '', '', '', 0, 0)");
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
            
            $overlayImg = !empty($personnel['img']) ? 'personnelImg/' . $personnel['img'] : 'personnelImg/default_img.jpg';
            $overlayName = strtoupper($personnel['lname']) . ', ' . $personnel['fname'];
            
            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'flow' => $flow,
                'logFlow' => $personnelFlow,
                'sound' => 'valid',
                'img' => $overlayImg,
                'name' => $overlayName
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Personnel not found.', 'sound' => 'invalid']);
        }
    } catch(PDOException $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        $errMsg = 'Biometric DTR database error: ' . $e->getMessage();
        error_log($errMsg);
        @file_put_contents(__DIR__ . '/dtr_error.log', date('[Y-m-d H:i:s] ') . $errMsg . PHP_EOL, FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Attendance could not be saved. Please contact the administrator.', 'sound' => 'invalid']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.', 'sound' => 'invalid']);
}
