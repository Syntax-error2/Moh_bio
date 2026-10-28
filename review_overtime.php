<?php
require_once __DIR__ . '/session.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !in_array($session_access, ['Administrator', 'Admin'], true)) {
    http_response_code(403);
    exit('Not authorized.');
}
if (empty($_SESSION['overtime_csrf']) || !hash_equals($_SESSION['overtime_csrf'], $_POST['csrf'] ?? '')) {
    http_response_code(403);
    exit('Invalid request token.');
}
$otId = filter_input(INPUT_POST, 'ot_id', FILTER_VALIDATE_INT);
$decision = $_POST['decision'] ?? '';
if (!$otId || !in_array($decision, ['approved', 'rejected'], true)) {
    http_response_code(400);
    exit('Invalid overtime decision.');
}
$review = $conn->prepare("UPDATE personnel_overtime_requests SET status = ?, reviewed_by = ?, reviewed_at = NOW() WHERE ot_id = ? AND status = 'pending'");
$review->execute([$decision, $session_id, $otId]);
header('Location: list_overtime.php?cw=list_overtime');
exit;
