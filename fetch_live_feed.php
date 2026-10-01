<?php
include_once('dbcon.php');

$currentDate = date('Y-m-d');
// Fetch logs from bio_dtr
$query = $conn->prepare("SELECT p.img, p.lname, p.fname, p.mname, b.time_in, b.time_out 
                         FROM bio_dtr b
                         LEFT JOIN personnels p ON b.personnel_id = p.personnel_id
                         WHERE b.log_date = :logDate
                         ORDER BY b.time_in DESC 
                         LIMIT 15");
$query->execute(['logDate' => $currentDate]);
$logs = $query->fetchAll(PDO::FETCH_ASSOC);

if(count($logs) == 0) {
    echo '<div class="text-center text-muted mt-5" style="opacity: 0.5;">
            <i class="fa fa-fingerprint fa-3x mb-3"></i>
            <h5 style="font-weight: 400;">No activity recorded today yet.</h5>
          </div>';
} else {
    foreach($logs as $log) {
        $img = $log['img'] ? 'personnelImg/'.$log['img'] : 'personnelImg/default_img.jpg';
        $name = strtoupper($log['lname']) . ", " . $log['fname'];
        $timeIn = $log['time_in'] ? date("h:i A", strtotime($log['time_in'])) : '--:--';
        $timeOut = $log['time_out'] ? date("h:i A", strtotime($log['time_out'])) : '--:--';
        
        echo '
        <div class="log-card mb-3" style="background: #ffffff; border-radius: 16px; padding: 15px 20px; display: flex; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05); transition: transform 0.2s;">
            <img src="'.$img.'" style="width:50px; height:50px; object-fit:cover; border-radius:50%; border:2px solid #e2e8f0; margin-right:15px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <div class="flex-grow-1">
                <h6 class="mb-1" style="font-weight:700; color: #1e293b; font-size: 15px;">'.$name.'</h6>
                <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 5px;">
                    <i class="fa fa-check-circle text-success" style="font-size: 10px;"></i> Biometric Verified
                </div>
            </div>
            <div class="text-end" style="min-width: 120px; display: flex; flex-direction: column; gap: 6px;">
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 4px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 10px; font-weight: 700; color: #059669; letter-spacing: 0.5px;">IN</span>
                    <span style="font-size: 12px; font-weight: 600; color: #334155;">'.$timeIn.'</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 4px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 10px; font-weight: 700; color: #e11d48; letter-spacing: 0.5px;">OUT</span>
                    <span style="font-size: 12px; font-weight: 600; color: #334155;">'.$timeOut.'</span>
                </div>
            </div>
        </div>';
    }
}
?>
