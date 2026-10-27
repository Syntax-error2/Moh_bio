<?php
include('session.php');
include('dbcon.php');
require_once('dtr_sync_lib.php');

if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    http_response_code(403);
    die("Access denied. Please log in first.");
}

$specificFile = isset($_GET['file']) ? basename($_GET['file']) : '';

ensureDtrFoldersExist();

if (!empty($specificFile)) {
    $targetPath = DTR_EXPORTS_DIR . DIRECTORY_SEPARATOR . $specificFile;
    if (!file_exists($targetPath)) {
        $_SESSION['dtr_sync_flash'] = [
            'type' => 'danger',
            'title' => 'File Not Found',
            'message' => "The file '$specificFile' does not exist in the spare folder."
        ];
        header('Location: dtr_sync_manager.php');
        exit;
    }
    $filesToProcess = [$targetPath];
} else {
    // Process all pending .dtr files in dtr_exports/
    $filesToProcess = glob(DTR_EXPORTS_DIR . DIRECTORY_SEPARATOR . '*.dtr');
}

if (empty($filesToProcess)) {
    $_SESSION['dtr_sync_flash'] = [
        'type' => 'info',
        'title' => 'No Files to Import',
        'message' => 'No pending .dtr packages found in the spare folder (dtr_exports/).'
    ];
    header('Location: dtr_sync_manager.php');
    exit;
}

$totalBioInserted = 0;
$totalBioUpdated = 0;
$totalBioSkipped = 0;
$totalLogsInserted = 0;
$totalLogsSkipped = 0;
$processedCount = 0;
$allUnmatched = [];

// Pre-load maps once for maximum performance across multiple files
$maps = loadPersonnelResolutionMaps($conn);

try {
    foreach ($filesToProcess as $filePath) {
        $content = file_get_contents($filePath);
        if (!$content) continue;

        $res = processDtrImportPackage($conn, $content, $maps);

        $totalBioInserted += $res['bio_inserted'];
        $totalBioUpdated += $res['bio_updated'];
        $totalBioSkipped += $res['bio_skipped'];
        $totalLogsInserted += $res['logs_inserted'];
        $totalLogsSkipped += $res['logs_skipped'];
        if (!empty($res['unmatched'])) {
            $allUnmatched = array_merge($allUnmatched, $res['unmatched']);
        }

        // Move processed file into imported archive subfolder
        $destArchive = DTR_IMPORTED_DIR . DIRECTORY_SEPARATOR . date('Ymd_His') . '_' . basename($filePath);
        @rename($filePath, $destArchive);
        $processedCount++;
    }

    $_SESSION['dtr_sync_flash'] = [
        'type' => 'success',
        'title' => "Spare Folder Auto-Import Completed ($processedCount file(s) processed)!",
        'details' => [
            'file_name' => "$processedCount package(s) from spare folder",
            'exported_at' => date('Y-m-d H:i:s'),
            'source_host' => 'Spare Folder',
            'bio_inserted' => $totalBioInserted,
            'bio_updated' => $totalBioUpdated,
            'bio_skipped' => $totalBioSkipped,
            'logs_inserted' => $totalLogsInserted,
            'logs_skipped' => $totalLogsSkipped,
            'unmatched' => $allUnmatched
        ]
    ];

} catch (Exception $e) {
    $_SESSION['dtr_sync_flash'] = [
        'type' => 'danger',
        'title' => 'Import Error',
        'message' => 'Error importing from spare folder: ' . $e->getMessage()
    ];
}

header('Location: dtr_sync_manager.php');
exit;
