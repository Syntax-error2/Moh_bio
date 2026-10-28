<?php
/**
 * Save Signatory Template AJAX Endpoint
 */
include('session.php');

// Only allow admins
if (strtolower($session_access) !== 'administrator' && strtolower($session_access) !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Only administrators can save templates.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
$sig_roles = isset($_POST['sig_role']) ? $_POST['sig_role'] : [];
$sig_names = isset($_POST['sig_name']) ? $_POST['sig_name'] : [];
$sig_titles = isset($_POST['sig_title']) ? $_POST['sig_title'] : [];

if (!$template_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid template ID.']);
    exit();
}

try {
    $conn->beginTransaction();

    // Delete existing items for this template
    $del_stmt = $conn->prepare("DELETE FROM pr_tbl_signatory_items WHERE template_id = :tid");
    $del_stmt->execute([':tid' => $template_id]);

    // Insert new items
    $ins_stmt = $conn->prepare("INSERT INTO pr_tbl_signatory_items (template_id, role_title, person_name, person_title, display_order) VALUES (:tid, :role, :name, :title, :order)");
    
    $order = 1;
    for ($i = 0; $i < count($sig_roles); $i++) {
        $role = trim($sig_roles[$i]);
        $name = isset($sig_names[$i]) ? trim($sig_names[$i]) : '';
        $title = isset($sig_titles[$i]) ? trim($sig_titles[$i]) : '';
        
        if (!empty($role)) {
            $ins_stmt->execute([
                ':tid' => $template_id,
                ':role' => $role,
                ':name' => $name,
                ':title' => $title,
                ':order' => $order
            ]);
            $order++;
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Signatory template saved successfully!']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
