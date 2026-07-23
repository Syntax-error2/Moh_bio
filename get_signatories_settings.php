<?php
session_start();
include('dbcon.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

try {
    // Fetch signatories settings
    $query = $conn->prepare("SELECT * FROM signatories_settings LIMIT 1");
    $query->execute();
    $data = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } else {
        // Return default values
        echo json_encode([
            'success' => true,
            'data' => [
                'hrmo_name' => '',
                'hrmo_position' => 'Human Resource Management Officer',
                'recommending_name' => '',
                'recommending_position' => 'Immediate Supervisor',
                'approving_name' => '',
                'approving_position' => 'Regional Director'
            ]
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Error fetching signatories settings: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
