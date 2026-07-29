<?php
/**
 * Audit Logger Utility
 * Handles writing to the `audit_trail` table.
 */

function log_audit_action($conn, $user_id, $user_name, $action, $module, $details = null) {
    if (!$conn) return false;
    
    try {
        $stmt = $conn->prepare("INSERT INTO audit_trail (user_id, user_name, action, module, details) VALUES (:user_id, :user_name, :action, :module, :details)");
        $stmt->execute([
            'user_id' => $user_id,
            'user_name' => $user_name,
            'action' => $action,
            'module' => $module,
            'details' => $details
        ]);
        return true;
    } catch (PDOException $e) {
        // Log to file if database logging fails, don't break the app
        error_log("Audit Trail Error: " . $e->getMessage());
        return false;
    }
}
?>
