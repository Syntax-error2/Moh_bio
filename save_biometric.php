<?php
include('dbcon.php');
require_once('session.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pid']) && isset($_POST['template']) && isset($_POST['finger_type'])) {
    $pid = $_POST['pid'];
    $template = $_POST['template'];
    $finger_type = $_POST['finger_type'];
    
    try {
        // Fetch current templates
        $query = $conn->prepare("SELECT fingerprint_template FROM personnels WHERE personnel_id = :pid");
        $query->execute(['pid' => $pid]);
        $row = $query->fetch();
        
        $fingerData = [];
        if (!empty($row['fingerprint_template'])) {
            $decoded = json_decode($row['fingerprint_template'], true);
            if (is_array($decoded)) {
                $fingerData = $decoded;
            } else {
                $fingerData['legacy'] = $row['fingerprint_template'];
            }
        }
        
        // Add new finger
        $fingerData[$finger_type] = $template;
        
        // Save back as JSON
        $newJson = json_encode($fingerData);
        
        // Get current biometric_id
        $bidStmt = $conn->prepare("SELECT biometric_id FROM personnels WHERE personnel_id = :pid");
        $bidStmt->execute(['pid' => $pid]);
        $bidRow = $bidStmt->fetch(PDO::FETCH_ASSOC);
        
        $updateFields = "fingerprint_template = :template";
        $params = [
            'template' => $newJson,
            'pid' => $pid
        ];
        
        // Auto-generate biometric_id if empty
        if (empty($bidRow['biometric_id'])) {
            $newBid = "BIO-" . str_pad($pid, 6, "0", STR_PAD_LEFT);
            $updateFields .= ", biometric_id = :bid";
            $params['bid'] = $newBid;
        }

        $updateStmt = $conn->prepare("UPDATE personnels SET $updateFields WHERE personnel_id = :pid");
        if ($updateStmt->execute($params)) {
            echo "success";
        }
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "invalid request";
}
?>
