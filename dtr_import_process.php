<?php
include('session.php');
include('dbcon.php');
require_once('dtr_sync_lib.php');

if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    http_response_code(403);
    die("Access denied. Please log in first.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['dtr_file'])) {
    $_SESSION['dtr_sync_flash'] = [
        'type' => 'danger',
        'message' => 'Please select a valid .dtr file to import.'
    ];
    header('Location: dtr_sync_manager.php');
    exit;
}

$file = $_FILES['dtr_file'];

if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] <= 0) {
    $_SESSION['dtr_sync_flash'] = [
        'type' => 'danger',
        'message' => 'Upload failed or empty file uploaded.'
    ];
    header('Location: dtr_sync_manager.php');
    exit;
}

$content = file_get_contents($file['tmp_name']);

try {
    $result = processDtrImportPackage($conn, $content);

    // Also copy uploaded file into spare folder if not already there, for audit and backup
    ensureDtrFoldersExist();
    $destBackup = DTR_EXPORTS_DIR . DIRECTORY_SEPARATOR . 'uploaded_' . date('Ymd_His') . '_' . basename($file['name']);
    @copy($file['tmp_name'], $destBackup);

    $_SESSION['dtr_sync_flash'] = [
        'type' => 'success',
        'title' => 'DTR Import Completed Successfully (Zero Duplicates)!',
        'details' => [
            'file_name' => htmlspecialchars($file['name']),
            'exported_at' => $result['exported_at'],
            'source_host' => $result['source_host'],
            'bio_inserted' => $result['bio_inserted'],
            'bio_updated' => $result['bio_updated'],
            'bio_skipped' => $result['bio_skipped'],
            'logs_inserted' => $result['logs_inserted'],
            'logs_skipped' => $result['logs_skipped'],
            'unmatched' => $result['unmatched']
        ]
    ];

} catch (Exception $e) {
    $_SESSION['dtr_sync_flash'] = [
        'type' => 'danger',
        'title' => 'Import Error',
        'message' => 'Error during DTR import: ' . $e->getMessage()
    ];
}

header('Location: dtr_sync_manager.php');
exit;
