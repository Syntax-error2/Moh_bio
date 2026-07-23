<?php

function pfm_is_admin($session_access) {
    return $session_access === 'Administrator' || stripos((string)$session_access, 'HR') !== false;
}

function pfm_slugify($name) {
    $slug = strtolower(trim((string)$name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim((string)$slug, '-');
    return $slug === '' ? 'folder' : $slug;
}

function pfm_ensure_schema($conn) {
    $conn->exec("CREATE TABLE IF NOT EXISTS personnel_file_folders (
        folder_id INT AUTO_INCREMENT PRIMARY KEY,
        personnel_id INT NOT NULL,
        folder_name VARCHAR(255) NOT NULL,
        folder_slug VARCHAR(255) NOT NULL,
        is_system_201 TINYINT(1) NOT NULL DEFAULT 0,
        date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_personnel_folder_slug (personnel_id, folder_slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->exec("CREATE TABLE IF NOT EXISTS personnel_file_audit_logs (
        audit_id INT AUTO_INCREMENT PRIMARY KEY,
        action_name VARCHAR(100) NOT NULL,
        actor_personnel_id INT NULL,
        actor_access VARCHAR(100) NULL,
        target_personnel_id INT NOT NULL,
        folder_id INT NULL,
        file_id INT NULL,
        action_details TEXT NULL,
        date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_pfa_target_personnel (target_personnel_id),
        INDEX idx_pfa_action_name (action_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $columns = $conn->query("SHOW COLUMNS FROM files")->fetchAll(PDO::FETCH_ASSOC);
    $existing = [];
    foreach ($columns as $col) {
        $existing[$col['Field']] = true;
    }

    if (!isset($existing['folder_id'])) {
        $conn->exec("ALTER TABLE files ADD COLUMN folder_id INT NULL AFTER personnel_id");
    }
    if (!isset($existing['uploaded_by_personnel_id'])) {
        $conn->exec("ALTER TABLE files ADD COLUMN uploaded_by_personnel_id INT NULL AFTER folder_id");
    }
    if (!isset($existing['uploaded_by_access'])) {
        $conn->exec("ALTER TABLE files ADD COLUMN uploaded_by_access VARCHAR(100) NULL AFTER uploaded_by_personnel_id");
    }

    $personnels = $conn->query("SELECT DISTINCT personnel_id FROM files WHERE personnel_id IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
    $updateStmt = $conn->prepare("UPDATE files SET folder_id = :folder_id WHERE personnel_id = :personnel_id AND (folder_id IS NULL OR folder_id = 0)");

    foreach ($personnels as $row) {
        $pid = (int)$row['personnel_id'];
        if ($pid <= 0) {
            continue;
        }
        $defaultFolderId = pfm_ensure_default_201_folder($conn, $pid);
        $updateStmt->execute([
            ':folder_id' => $defaultFolderId,
            ':personnel_id' => $pid,
        ]);
    }
}

function pfm_allowed_extensions() {
    return ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
}

function pfm_allowed_mime_map() {
    return [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword', 'application/vnd.ms-office', 'application/octet-stream'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
        'xls' => ['application/vnd.ms-excel', 'application/octet-stream'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'txt' => ['text/plain', 'application/octet-stream'],
    ];
}

function pfm_detect_mime_type($tmpFilePath) {
    if (!function_exists('finfo_open')) {
        return '';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        return '';
    }

    $mimeType = (string)finfo_file($finfo, $tmpFilePath);
    finfo_close($finfo);
    return strtolower(trim($mimeType));
}

function pfm_is_valid_extension_mime($extension, $mimeType) {
    $extension = strtolower(trim((string)$extension));
    $mimeType = strtolower(trim((string)$mimeType));
    if ($extension === '' || $mimeType === '') {
        return false;
    }

    $mimeMap = pfm_allowed_mime_map();
    if (!isset($mimeMap[$extension])) {
        return false;
    }

    return in_array($mimeType, $mimeMap[$extension], true);
}

function pfm_log_action($conn, $actionName, $actorPersonnelId, $actorAccess, $targetPersonnelId, $folderId = null, $fileId = null, $actionDetails = null) {
    try {
        $stmt = $conn->prepare("INSERT INTO personnel_file_audit_logs (action_name, actor_personnel_id, actor_access, target_personnel_id, folder_id, file_id, action_details)
                               VALUES (:action_name, :actor_personnel_id, :actor_access, :target_personnel_id, :folder_id, :file_id, :action_details)");
        $stmt->execute([
            ':action_name' => (string)$actionName,
            ':actor_personnel_id' => $actorPersonnelId !== null ? (int)$actorPersonnelId : null,
            ':actor_access' => (string)$actorAccess,
            ':target_personnel_id' => (int)$targetPersonnelId,
            ':folder_id' => $folderId !== null ? (int)$folderId : null,
            ':file_id' => $fileId !== null ? (int)$fileId : null,
            ':action_details' => $actionDetails !== null ? (string)$actionDetails : null,
        ]);
    } catch (Throwable $e) {
        error_log('PFM audit log error: ' . $e->getMessage());
    }
}

function pfm_ensure_default_201_folder($conn, $personnel_id) {
    $personnel_id = (int)$personnel_id;
    $stmt = $conn->prepare("SELECT folder_id FROM personnel_file_folders WHERE personnel_id = :personnel_id AND is_system_201 = 1 LIMIT 1");
    $stmt->execute([':personnel_id' => $personnel_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return (int)$row['folder_id'];
    }

    $insert = $conn->prepare("INSERT INTO personnel_file_folders (personnel_id, folder_name, folder_slug, is_system_201) VALUES (:personnel_id, '201-files', '201-files', 1)");
    $insert->execute([':personnel_id' => $personnel_id]);
    return (int)$conn->lastInsertId();
}

function pfm_can_manage_personnel_files($session_access, $session_personnel_id, $target_personnel_id) {
    if (pfm_is_admin($session_access)) {
        return true;
    }

    return (int)$session_personnel_id > 0 && (int)$session_personnel_id === (int)$target_personnel_id;
}
