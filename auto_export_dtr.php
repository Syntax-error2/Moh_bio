<?php
/**
 * MOB BIO - Dedicated DTR Auto-Export Script
 * Automatically packages unexported DTR records and saves them to the spare folder (dtr_exports/)
 * Can be run via:
 *   1. Web UI (clicking 'Auto-Export to Spare Folder' button in dtr_sync_manager.php)
 *   2. Windows Task Scheduler / CLI: `php.exe c:\xampp\htdocs\mob_bio\auto_export_dtr.php`
 *   3. Scheduled HTTP Cron: `http://localhost/mob_bio/auto_export_dtr.php?token=mob_bio_sync`
 */

$isCli = (php_sapi_name() === 'cli' || empty($_SERVER['REMOTE_ADDR']));

if (!$isCli) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Allow if authenticated in session OR valid cron secret token provided
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    $isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']);
    
    if (!isset($_SESSION['username']) && !isset($_SESSION['id']) && $token !== 'mob_bio_sync' && !$isLocal) {
        http_response_code(403);
        die("Access denied. Please log in or provide valid token.");
    }
}

require_once('dbcon.php');
require_once('dtr_sync_lib.php');

$limit = isset($_REQUEST['limit']) ? (int)$_REQUEST['limit'] : 5000;

$options = [
    'filter_type' => $_REQUEST['filter_type'] ?? 'unexported',
    'only_unexported' => true,
    'username' => $_SESSION['username'] ?? 'auto_scheduler',
    'selected_month' => $_REQUEST['selected_month'] ?? date('m'),
    'selected_year' => $_REQUEST['selected_year'] ?? date('Y'),
    'date_from' => $_REQUEST['date_from'] ?? '',
    'date_to' => $_REQUEST['date_to'] ?? '',
    'limit' => $limit
];

try {
    $result = buildDtrExportPackage($conn, $options);

    if (!empty($result['is_empty'])) {
        $msg = 'No new unexported attendance records to export. All records are currently synchronized.';
        if (!$isCli && isset($_SESSION['username'])) {
            $_SESSION['dtr_sync_flash'] = [
                'type' => 'info',
                'title' => 'Auto-Export Status',
                'message' => $msg
            ];
            header('Location: dtr_sync_manager.php');
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'count' => 0,
                'message' => $msg
            ], JSON_PRETTY_PRINT);
            exit;
        }
    }

    $savedPath = saveDtrPackageToSpareFolder($result['filename'], $result['json']);

    if (!$savedPath) {
        throw new RuntimeException("Failed to write export package to spare folder: " . DTR_EXPORTS_DIR);
    }

    $successMsg = "Successfully exported " . ($result['bio_count'] + $result['logs_count']) . " attendance records (" . $result['bio_count'] . " bio sessions, " . $result['logs_count'] . " punches) to spare folder: " . $result['filename'];

    if (!$isCli && isset($_SESSION['username'])) {
        $_SESSION['dtr_sync_flash'] = [
            'type' => 'success',
            'title' => '⚡ Auto-Export to Spare Folder Successful!',
            'message' => $successMsg,
            'details' => [
                'file_name' => $result['filename'],
                'exported_at' => $result['exported_at'],
                'bio_count' => $result['bio_count'],
                'logs_count' => $result['logs_count']
            ]
        ];
        header('Location: dtr_sync_manager.php');
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $successMsg,
            'filename' => $result['filename'],
            'filepath' => $savedPath,
            'bio_count' => $result['bio_count'],
            'logs_count' => $result['logs_count'],
            'exported_at' => $result['exported_at']
        ], JSON_PRETTY_PRINT);
        exit;
    }

} catch (Exception $e) {
    $errMsg = "Auto-export failed: " . $e->getMessage();
    if (!$isCli && isset($_SESSION['username'])) {
        $_SESSION['dtr_sync_flash'] = [
            'type' => 'danger',
            'title' => 'Auto-Export Error',
            'message' => $errMsg
        ];
        header('Location: dtr_sync_manager.php');
        exit;
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $errMsg
        ], JSON_PRETTY_PRINT);
        exit;
    }
}
