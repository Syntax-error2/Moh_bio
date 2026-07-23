<?php
session_start();
require_once 'dbcon.php';

header('Content-Type: application/json');

// Check if user is logged in (check both possible session variables)
if (!isset($_SESSION['id']) && !isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Accept both POST and GET for AJAX convenience
$leave_application_id = $_POST['leave_application_id'] ?? $_GET['leave_application_id'] ?? null;

if ($leave_application_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM leave_applications WHERE id = :id");
        $stmt->bindParam(':id', $leave_application_id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            // Return the row plus a success flag for better client handling
            echo json_encode(array_merge(['success' => true], $data));
        } else {
            echo json_encode(['error' => 'Leave application not found']);
        }

    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
