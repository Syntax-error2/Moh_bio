<?php

include('session.php');
require_once('personnel_files_lib.php');

ignore_user_abort(true);
set_time_limit(0);

pfm_ensure_schema($conn);

$filePath = '';

if (isset($_GET['file_id'])) {
    $file_id = (int)$_GET['file_id'];
    $fileStmt = $conn->prepare("SELECT file_name, personnel_id FROM files WHERE file_id = :file_id LIMIT 1");
    $fileStmt->execute([':file_id' => $file_id]);
    $fileRow = $fileStmt->fetch(PDO::FETCH_ASSOC);

    if (!$fileRow) {
        exit('File not found.');
    }

    $isAdmin = pfm_is_admin($session_access);
    $isOwner = (int)$fileRow['personnel_id'] === (int)$user_personnel_id;

    if (!$isAdmin && !$isOwner) {
        exit('Not allowed.');
    }

    $filePath = $fileRow['file_name'];
} elseif (isset($_GET['download_file'])) {
    $legacyFile = $_GET['download_file'];
    $fileStmt = $conn->prepare("SELECT file_name, personnel_id FROM files WHERE file_name = :file_name LIMIT 1");
    $fileStmt->execute([':file_name' => $legacyFile]);
    $fileRow = $fileStmt->fetch(PDO::FETCH_ASSOC);

    if (!$fileRow) {
        exit('File not found.');
    }

    $isAdmin = pfm_is_admin($session_access);
    $isOwner = (int)$fileRow['personnel_id'] === (int)$user_personnel_id;

    if (!$isAdmin && !$isOwner) {
        exit('Not allowed.');
    }

    $filePath = $fileRow['file_name'];
}

if ($filePath === '' || !file_exists($filePath)) {
    exit('File does not exist.');
}

$fd = fopen($filePath, 'rb');
if (!$fd) {
    exit('Unable to open file.');
}

$fsize = filesize($filePath);
$path_parts = pathinfo($filePath);
$ext = strtolower($path_parts['extension'] ?? '');

switch ($ext) {
    case 'pdf':
        header('Content-type: application/pdf');
        break;
    default:
        header('Content-type: application/octet-stream');
        break;
}

header('Content-Disposition: attachment; filename="' . ($path_parts['basename'] ?? 'download.bin') . '"');
header('Content-length: ' . $fsize);
header('Cache-control: private');

while (!feof($fd)) {
    echo fread($fd, 8192);
}

fclose($fd);
exit();
?>