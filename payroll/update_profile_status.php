<?php
include('session.php');

header('Content-Type: application/json');

if (!isset($_POST['profile_id']) || !isset($_POST['is_active'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
    exit;
}

$profile_id = (int)$_POST['profile_id'];
$is_active = (int)$_POST['is_active'];

try {
    $stmt = $conn->prepare("UPDATE pr_tbl_payroll_profiles SET is_active = :is_active WHERE profile_id = :profile_id");
    $stmt->execute([
        ':is_active' => $is_active,
        ':profile_id' => $profile_id
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
