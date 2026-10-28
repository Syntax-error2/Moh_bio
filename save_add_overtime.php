<?php
include('dbcon.php');
include('session.php');

if(isset($_POST['add_ot'])){
    $info = explode(" | ", $_POST['personnel_info'] ?? '');
    $personnel_id = trim($info[0]);
    $ot_date = $_POST['ot_date'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');
    $date = DateTime::createFromFormat('!Y-m-d', $ot_date);
    if (!$date || $date->format('Y-m-d') !== $ot_date || !ctype_digit($personnel_id) || $remarks === '') {
        http_response_code(400);
        exit('Please provide a valid employee, date and reason.');
    }
    
    // Check if employee exists
    $chk = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = ?");
    $chk->execute([$personnel_id]);
    if($chk->fetch()) {
        $existing = $conn->prepare("SELECT ot_id FROM personnel_overtime_requests WHERE personnel_id = ? AND ot_date = ? AND status IN ('pending', 'approved') LIMIT 1");
        $existing->execute([$personnel_id, $ot_date]);
        if (!$existing->fetchColumn()) {
            $stmt = $conn->prepare("INSERT INTO personnel_overtime_requests (personnel_id, ot_date, remarks, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$personnel_id, $ot_date, $remarks]);
        }
    } else {
        http_response_code(404);
        exit('Employee not found.');
    }
    header('Location: list_overtime.php?cw=list_overtime');
    exit;
}
?>
