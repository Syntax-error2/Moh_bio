<?php
session_start();
require_once 'dbcon.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Check if personnel_id is provided
if (!isset($_POST['personnel_id']) || empty($_POST['personnel_id'])) {
    echo json_encode(['success' => false, 'message' => 'Personnel ID required']);
    exit();
}

$personnel_id = $_POST['personnel_id'];

try {
    // Check if is_special_leave column exists
    $column_check = $conn->query("SHOW COLUMNS FROM leave_card LIKE 'is_special_leave'");
    $has_special_leave_column = $column_check->rowCount() > 0;
    
    if ($has_special_leave_column) {
        // Calculate balances excluding special leave "with pay" deductions
        $query = $conn->prepare("SELECT 
            COALESCE(SUM(vl_earned), 0) as total_vl_earned,
            COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN vl_with_pay ELSE 0 END), 0) as total_vl_with_pay,
            COALESCE(SUM(sl_earned), 0) as total_sl_earned,
            COALESCE(SUM(CASE WHEN is_special_leave = 0 THEN sl_with_pay ELSE 0 END), 0) as total_sl_with_pay
        FROM leave_card 
        WHERE personnel_id = :personnel_id");
    } else {
        // Fallback query without is_special_leave column
        $query = $conn->prepare("SELECT 
            COALESCE(SUM(vl_earned), 0) as total_vl_earned,
            COALESCE(SUM(vl_with_pay), 0) as total_vl_with_pay,
            COALESCE(SUM(sl_earned), 0) as total_sl_earned,
            COALESCE(SUM(sl_with_pay), 0) as total_sl_with_pay
        FROM leave_card 
        WHERE personnel_id = :personnel_id");
    }
    
    $query->execute([':personnel_id' => $personnel_id]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $vl_earned = floatval($result['total_vl_earned']);
        $vl_used = floatval($result['total_vl_with_pay']);
        $sl_earned = floatval($result['total_sl_earned']);
        $sl_used = floatval($result['total_sl_with_pay']);
        
        // Calculate current balances
        $vl_balance = $vl_earned - $vl_used;
        $sl_balance = $sl_earned - $sl_used;
        
        echo json_encode([
            'success' => true,
            'vl_earned' => round($vl_earned, 3),
            'vl_used' => round($vl_used, 3),
            'vl_balance' => round($vl_balance, 3),
            'sl_earned' => round($sl_earned, 3),
            'sl_used' => round($sl_used, 3),
            'sl_balance' => round($sl_balance, 3)
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'vl_earned' => 0,
            'vl_used' => 0,
            'vl_balance' => 0,
            'sl_earned' => 0,
            'sl_used' => 0,
            'sl_balance' => 0
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Error fetching leave card balance: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}
?>
