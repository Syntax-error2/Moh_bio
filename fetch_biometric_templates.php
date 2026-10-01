<?php
include('dbcon.php');

$flatTemplates = [];
$stmt = $conn->query("SELECT personnel_id, RFTag_id, biometric_id, fingerprint_template FROM personnels WHERE fingerprint_template IS NOT NULL AND fingerprint_template != ''");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data = json_decode($row['fingerprint_template'], true);
    if (is_array($data)) {
        foreach($data as $finger => $base64) {
            $flatTemplates[] = [
                'personnel_id' => $row['personnel_id'],
                'RFTag_id' => $row['RFTag_id'],
                'biometric_id' => $row['biometric_id'],
                'finger' => $finger,
                'fingerprint_template' => $base64
            ];
        }
    } else {
        $flatTemplates[] = [
            'personnel_id' => $row['personnel_id'],
            'RFTag_id' => $row['RFTag_id'],
            'biometric_id' => $row['biometric_id'],
            'finger' => 'legacy',
            'fingerprint_template' => $row['fingerprint_template']
        ];
    }
}

echo json_encode($flatTemplates);
?>
