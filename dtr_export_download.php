<?php
include('session.php');
include('dbcon.php');
require_once('dtr_sync_lib.php');

// Restrict to authorized users
if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    http_response_code(403);
    die("Access denied. Please log in first.");
}

$options = array_merge($_GET, $_POST);
$options['username'] = $_SESSION['username'] ?? ($user_row['username'] ?? 'admin');

try {
    $result = buildDtrExportPackage($conn, $options);

    if (!empty($result['is_empty'])) {
        $_SESSION['dtr_sync_flash'] = [
            'type' => 'warning',
            'title' => 'No Records Found',
            'message' => 'No attendance records found matching the selected export criteria.'
        ];
        header('Location: dtr_sync_manager.php');
        exit;
    }

    // Automatically archive a copy into the spare folder for the new server
    $sparePath = saveDtrPackageToSpareFolder($result['filename'], $result['json']);

    // Stream download directly to client's browser/USB
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
    header('Content-Length: ' . strlen($result['json']));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $result['json'];
    exit;

} catch (Exception $e) {
    $_SESSION['dtr_sync_flash'] = [
        'type' => 'danger',
        'title' => 'Export Error',
        'message' => 'Failed to export attendance records: ' . $e->getMessage()
    ];
    header('Location: dtr_sync_manager.php');
    exit;
}
