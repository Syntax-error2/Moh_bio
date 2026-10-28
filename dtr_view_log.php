<?php
include('session.php');
include('dbcon.php');

if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    http_response_code(403);
    die("Access denied. Please log in first.");
}

$logFile = __DIR__ . DIRECTORY_SEPARATOR . 'dtr_exports' . DIRECTORY_SEPARATOR . 'auto_export.log';
$content = file_exists($logFile) ? file_get_contents($logFile) : 'No auto-export activity recorded yet.';
?>
<!DOCTYPE html>
<html>
<head>
    <title>MOB BIO - Auto-Export Execution Log</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <style>
        body { background: #f8f9fa; font-family: Consolas, monospace; padding: 20px; font-size: 13px; }
        .log-box { background: #1e1e1e; color: #d4d4d4; padding: 16px; border-radius: 4px; overflow-x: auto; white-space: pre-wrap; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fa fa-history text-success"></i> Auto-Export Activity Log (<code>dtr_exports/auto_export.log</code>)</h4>
            <div>
                <button onclick="window.location.reload();" class="btn btn-sm btn-outline-primary"><i class="fa fa-refresh"></i> Refresh</button>
                <button onclick="window.close();" class="btn btn-sm btn-outline-secondary"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <div class="log-box"><?php echo htmlspecialchars($content); ?></div>
    </div>
</body>
</html>
