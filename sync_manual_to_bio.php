<?php
    // Sync manual encodes to bio_dtr table
    $stmt_p = $conn->prepare("SELECT personnel_id, biometric_id FROM personnels WHERE RFTag_id = ?");
    $stmt_p->execute([$RFTag_id]);
    $p_row = $stmt_p->fetch();
    
    if($p_row) {
        $pid = $p_row['personnel_id'];
        $bid = $p_row['biometric_id'] ?: $RFTag_id;
        
        // Handle AM
        if($am_in_stat != "No Log" || $am_out_stat != "No Log") {
            $t_in = ($am_in_stat != "No Log" && $am_in_stat != "") ? date('H:i:s', strtotime($am_IN)) : null;
            $t_out = ($am_out_stat != "No Log" && $am_out_stat != "") ? date('H:i:s', strtotime($am_OUT)) : null;
            
            $am_q = $conn->prepare("SELECT bio_id FROM bio_dtr WHERE personnel_id = ? AND log_date = ? AND (time_in < '12:00:00' OR time_out < '13:00:00' OR (time_in IS NULL AND time_out IS NULL)) LIMIT 1");
            $am_q->execute([$pid, $logDate]);
            $am_row = $am_q->fetch();
            
            if($am_row) {
                // Update existing AM row
                $q = "UPDATE bio_dtr SET ";
                $params = [];
                if($t_in !== null) { $q .= "time_in=?, "; $params[] = $t_in; }
                if($t_out !== null) { $q .= "time_out=?, "; $params[] = $t_out; }
                
                if(count($params) > 0) {
                    $q = rtrim($q, ", ");
                    $q .= " WHERE bio_id=?";
                    $params[] = $am_row['bio_id'];
                    $conn->prepare($q)->execute($params);
                }
            } else {
                // Insert new AM row
                $t_in_val = $t_in !== null ? $t_in : '00:00:00';
                $t_out_val = $t_out !== null ? $t_out : '00:00:00';
                $conn->prepare("INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out) VALUES (?, ?, ?, ?, ?)")->execute([$pid, $bid, $logDate, $t_in_val, $t_out_val]);
            }
        }
        
        // Handle PM
        if($pm_in_stat != "No Log" || $pm_out_stat != "No Log") {
            $t_in = ($pm_in_stat != "No Log" && $pm_in_stat != "") ? date('H:i:s', strtotime($pm_IN)) : null;
            $t_out = ($pm_out_stat != "No Log" && $pm_out_stat != "") ? date('H:i:s', strtotime($pm_OUT)) : null;
            
            $pm_q = $conn->prepare("SELECT bio_id FROM bio_dtr WHERE personnel_id = ? AND log_date = ? AND (time_in >= '12:00:00' OR time_out >= '13:00:00') LIMIT 1");
            $pm_q->execute([$pid, $logDate]);
            $pm_row = $pm_q->fetch();
            
            if($pm_row) {
                // Update existing PM row
                $q = "UPDATE bio_dtr SET ";
                $params = [];
                if($t_in !== null) { $q .= "time_in=?, "; $params[] = $t_in; }
                if($t_out !== null) { $q .= "time_out=?, "; $params[] = $t_out; }
                
                if(count($params) > 0) {
                    $q = rtrim($q, ", ");
                    $q .= " WHERE bio_id=?";
                    $params[] = $pm_row['bio_id'];
                    $conn->prepare($q)->execute($params);
                }
            } else {
                // Insert new PM row
                $t_in_val = $t_in !== null ? $t_in : '00:00:00';
                $t_out_val = $t_out !== null ? $t_out : '00:00:00';
                $conn->prepare("INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out) VALUES (?, ?, ?, ?, ?)")->execute([$pid, $bid, $logDate, $t_in_val, $t_out_val]);
            }
        }
    }
?>
