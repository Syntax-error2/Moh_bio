<?php
session_start();
include('dbcon.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Get POST data
$hrmo_name = $_POST['hrmo_name'] ?? '';
$hrmo_position = $_POST['hrmo_position'] ?? 'Human Resource Management Officer';
$recommending_name = $_POST['recommending_name'] ?? '';
$recommending_position = $_POST['recommending_position'] ?? 'Immediate Supervisor';
$approving_name = $_POST['approving_name'] ?? '';
$approving_position = $_POST['approving_position'] ?? 'Regional Director';
$monetization_constant = $_POST['monetization_constant'] ?? '0.0481927';
$budget_officer_name = $_POST['budget_officer_name'] ?? '';
$budget_officer_position = $_POST['budget_officer_position'] ?? 'Municipal Budget Officer';
$treasurer_name = $_POST['treasurer_name'] ?? '';
$treasurer_position = $_POST['treasurer_position'] ?? 'Acting Municipal Treasurer';
$accountant_name = $_POST['accountant_name'] ?? '';
$accountant_position = $_POST['accountant_position'] ?? 'Municipal Accountant';
$mayor_name = $_POST['mayor_name'] ?? '';
$mayor_position = $_POST['mayor_position'] ?? 'Municipal Mayor';

try {
    // Check if signatories table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'signatories_settings'");
    
    if ($table_check->rowCount() == 0) {
        // Create table if it doesn't exist
        $create_table = "CREATE TABLE signatories_settings (
            id INT(11) PRIMARY KEY AUTO_INCREMENT,
            hrmo_name VARCHAR(255) DEFAULT NULL,
            hrmo_position VARCHAR(255) DEFAULT 'Human Resource Management Officer',
            recommending_name VARCHAR(255) DEFAULT NULL,
            recommending_position VARCHAR(255) DEFAULT 'Immediate Supervisor',
            approving_name VARCHAR(255) DEFAULT NULL,
            approving_position VARCHAR(255) DEFAULT 'Regional Director',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $conn->exec($create_table);
    }
    
    // Check if record exists
    $check_query = $conn->prepare("SELECT id FROM signatories_settings LIMIT 1");
    $check_query->execute();
    $existing = $check_query->fetch();
    
    if ($existing) {
        // Update existing record
        $update_query = $conn->prepare("UPDATE signatories_settings SET 
            hrmo_name = :hrmo_name,
            hrmo_position = :hrmo_position,
            recommending_name = :recommending_name,
            recommending_position = :recommending_position,
            approving_name = :approving_name,
            approving_position = :approving_position,
            monetization_constant = :monetization_constant,
            budget_officer_name = :budget_officer_name,
            budget_officer_position = :budget_officer_position,
            treasurer_name = :treasurer_name,
            treasurer_position = :treasurer_position,
            accountant_name = :accountant_name,
            accountant_position = :accountant_position,
            mayor_name = :mayor_name,
            mayor_position = :mayor_position,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id");
        
        $update_query->execute([
            ':hrmo_name' => $hrmo_name,
            ':hrmo_position' => $hrmo_position,
            ':recommending_name' => $recommending_name,
            ':recommending_position' => $recommending_position,
            ':approving_name' => $approving_name,
            ':approving_position' => $approving_position,
            ':monetization_constant' => $monetization_constant,
            ':budget_officer_name' => $budget_officer_name,
            ':budget_officer_position' => $budget_officer_position,
            ':treasurer_name' => $treasurer_name,
            ':treasurer_position' => $treasurer_position,
            ':accountant_name' => $accountant_name,
            ':accountant_position' => $accountant_position,
            ':mayor_name' => $mayor_name,
            ':mayor_position' => $mayor_position,
            ':id' => $existing['id']
        ]);
    } else {
        // Insert new record
        $insert_query = $conn->prepare("INSERT INTO signatories_settings 
            (hrmo_name, hrmo_position, recommending_name, recommending_position, approving_name, approving_position,
             monetization_constant, budget_officer_name, budget_officer_position, treasurer_name, treasurer_position,
             accountant_name, accountant_position, mayor_name, mayor_position) 
            VALUES (:hrmo_name, :hrmo_position, :recommending_name, :recommending_position, :approving_name, :approving_position,
                    :monetization_constant, :budget_officer_name, :budget_officer_position, :treasurer_name, :treasurer_position,
                    :accountant_name, :accountant_position, :mayor_name, :mayor_position)");
        
        $insert_query->execute([
            ':hrmo_name' => $hrmo_name,
            ':hrmo_position' => $hrmo_position,
            ':recommending_name' => $recommending_name,
            ':recommending_position' => $recommending_position,
            ':approving_name' => $approving_name,
            ':approving_position' => $approving_position,
            ':monetization_constant' => $monetization_constant,
            ':budget_officer_name' => $budget_officer_name,
            ':budget_officer_position' => $budget_officer_position,
            ':treasurer_name' => $treasurer_name,
            ':treasurer_position' => $treasurer_position,
            ':accountant_name' => $accountant_name,
            ':accountant_position' => $accountant_position,
            ':mayor_name' => $mayor_name,
            ':mayor_position' => $mayor_position
        ]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Signatories settings saved successfully']);
    
} catch (PDOException $e) {
    error_log("Error saving signatories settings: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
