<?php
include('session.php');

header('Content-Type: application/json');

if (!isset($_POST['profile_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing profile ID.']);
    exit;
}

$profile_id = (int)$_POST['profile_id'];

try {
    $conn->beginTransaction();

    // Check if it's default
    $stmt = $conn->prepare("SELECT is_default FROM pr_tbl_payroll_profiles WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);
    $is_default = $stmt->fetchColumn();

    if ($is_default == 1) {
        throw new Exception("Cannot delete the default profile.");
    }

    // Check if used in runs
    $stmt = $conn->prepare("SELECT COUNT(*) FROM pr_tbl_payroll_runs WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cannot delete profile because it has been used in payroll runs.");
    }

    // Delete relationships
    $stmt = $conn->prepare("DELETE FROM pr_tbl_payroll_profile_income WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);
    
    $stmt = $conn->prepare("DELETE FROM pr_tbl_payroll_profile_deductions WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);
    
    $stmt = $conn->prepare("DELETE FROM pr_tbl_payroll_profile_filters WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);

    // Delete profile
    $stmt = $conn->prepare("DELETE FROM pr_tbl_payroll_profiles WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
