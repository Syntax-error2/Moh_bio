<?php
include('session.php');
include('dbcon.php');
require_once('dtr_sync_lib.php');

if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    http_response_code(403);
    die("Access denied. Please log in first.");
}

$file = isset($_GET['file']) ? basename($_GET['file']) : '';
if (empty($file)) {
    die("No file specified.");
}

$targetPath = DTR_EXPORTS_DIR . DIRECTORY_SEPARATOR . $file;
if (!file_exists($targetPath)) {
    die("File not found.");
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($targetPath));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($targetPath);
exit;
