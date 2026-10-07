<?php
ob_start();
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/attendance_transfer_lib.php';

if (!in_array($session_access, ['Admin', 'Administrator'], true)) {
    http_response_code(403);
    exit('Administrator access required.');
}
if (!isset($_SESSION['attendance_transfer_csrf'])) {
    $_SESSION['attendance_transfer_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['attendance_transfer_csrf'];
$error = '';
$notice = '';
$preview = null;
$package = null;
$ready = false;

try {
    $ready = at_schema_ready($conn) && class_exists('ZipArchive');
    $device = at_device_id();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            throw new RuntimeException('The upload is too large for this PHP server. Export a shorter date range (maximum 30 MB).');
        }
        if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
            throw new RuntimeException('Session expired. Reload this page and try again.');
        }
        if (!$ready) {
            throw new RuntimeException('Install the attendance transfer migration and enable PHP ZIP on this device first.');
        }
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'export') {
            $from = at_date((string)($_POST['from'] ?? ''));
            $to = at_date((string)($_POST['to'] ?? ''));
            if ($from > $to) {
                throw new RuntimeException('The start date is after the end date.');
            }
            $file = at_export($conn, $device, $from, $to);
            try {
                while (ob_get_level()) { ob_end_clean(); }
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="attendance_' . $from . '_to_' . $to . '.zip"');
                header('Content-Length: ' . filesize($file));
                header('Cache-Control: no-store');
                readfile($file);
            } finally {
                @unlink($file);
            }
            exit;
        }
        if ($action === 'preview') {
            $upload = $_FILES['package'] ?? null;
            if (!$upload || $upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > 30 * 1024 * 1024) {
                throw new RuntimeException('Choose an attendance ZIP no larger than 30 MB.');
            }
            $file = tempnam(sys_get_temp_dir(), 'attendance_import_');
            if ($file === false) { throw new RuntimeException('Cannot create a temporary import file.'); }
            if (!move_uploaded_file($upload['tmp_name'], $file)) {
                @unlink($file);
                throw new RuntimeException('Could not stage the uploaded file.');
            }
            if (!empty($_SESSION['attendance_transfer_pending']['file'])) {
                @unlink($_SESSION['attendance_transfer_pending']['file']);
            }
            $_SESSION['attendance_transfer_pending'] = ['file' => $file, 'hash' => hash_file('sha256', $file), 'time' => time()];
        }
        if ($action === 'preview' || $action === 'confirm') {
            $pending = $_SESSION['attendance_transfer_pending'] ?? null;
            if (!$pending || time() - $pending['time'] > 1800 || !is_file($pending['file']) ||
                !hash_equals($pending['hash'], hash_file('sha256', $pending['file']))) {
                throw new RuntimeException('The preview expired. Upload the ZIP again.');
            }
            $package = at_read_package($pending['file']);
            if ($package['source_device'] === $device) {
                throw new RuntimeException('This package came from this device. Choose a package exported by the other device.');
            }
            $preview = at_plan($conn, $package);
            if ($action === 'confirm') {
                if ($preview['conflicts']) {
                    throw new RuntimeException('The import has conflicts. No records were added.');
                }
                $result = at_import($conn, $pending['file'], $package, (int)$session_id);
                $notice = "Imported {$result['add_bio']} biometric DTR rows and {$result['add_logs']} attendance logs; updated {$result['update_bio']} completed biometric rows; skipped {$result['existing']} existing rows.";
                @unlink($pending['file']);
                unset($_SESSION['attendance_transfer_pending']);
                $preview = $package = null;
            }
        } elseif ($action !== 'export') {
            throw new RuntimeException('Unknown action.');
        }
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
    if (!isset($device)) { $ready = false; }
}

function at_html($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/header.php'; ?>
<body>
<?php include __DIR__ . '/menu_sidebar.php'; ?>
<div class="page">
<?php include __DIR__ . '/navbar_header.php'; ?>
<div class="breadcrumb-holder"><div class="container-fluid"><ul class="breadcrumb"><li class="breadcrumb-item"><a href="home.php">Home</a></li><li class="breadcrumb-item active">Attendance Transfer</li></ul></div></div>
<section class="statistics"><div class="container-fluid" style="padding-top:24px">
  <h2>Attendance Transfer</h2>
  <p>Use this page on both the offline laptop and the server. Export attendance from one device, then preview and import that ZIP on the other. The transfer includes biometric DTR rows, attendance logs, and captured photos when present. Detailed DTR and CS Form 48 read the resulting records.</p>
  <p><strong>This device:</strong> <code><?= at_html($device ?? 'Unavailable') ?></code></p>
  <?php if (!$ready): ?><div class="alert alert-warning">Setup needed on this device: run <code>db/attendance_transfer_migration.sql</code> in <code>moh_bio</code> and enable the PHP ZIP extension.</div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= at_html($error) ?></div><?php endif; ?>
  <?php if ($notice): ?><div class="alert alert-success"><?= at_html($notice) ?></div><?php endif; ?>
  <div class="row">
    <div class="col-lg-6"><div class="card"><div class="card-body">
      <h3 class="h5">Export attendance</h3>
      <p>Choose the dates recorded on this device. You can export an overlapping range again; the receiving device checks each record before adding it.</p>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= at_html($csrf) ?>"><input type="hidden" name="action" value="export">
        <div class="form-group"><label>From <input class="form-control" type="date" name="from" required value="<?= at_html(date('Y-m-01')) ?>"></label></div>
        <div class="form-group"><label>To <input class="form-control" type="date" name="to" required value="<?= at_html(date('Y-m-d')) ?>"></label></div>
        <button class="btn btn-primary" type="submit" <?= $ready ? '' : 'disabled' ?>>Download attendance ZIP</button>
      </form>
    </div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body">
      <h3 class="h5">Import attendance</h3>
      <p>Upload a ZIP from the other device. Review matches and conflicts before adding any records.</p>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= at_html($csrf) ?>"><input type="hidden" name="action" value="preview">
        <div class="form-group"><input class="form-control-file" type="file" name="package" accept=".zip,application/zip" required></div>
        <button class="btn btn-primary" type="submit" <?= $ready ? '' : 'disabled' ?>>Preview import</button>
      </form>
    </div></div></div>
  </div>
  <?php if ($preview && $package): ?>
  <div class="card"><div class="card-body">
    <h3 class="h5">Import preview</h3>
    <p>Source device: <code><?= at_html($package['source_device']) ?></code><br>
       Package: <code><?= at_html($package['package_id']) ?></code><br>
       Dates: <?= at_html($package['from'] ?? '') ?> to <?= at_html($package['to'] ?? '') ?></p>
    <ul><li><?= (int)$preview['add_bio'] ?> biometric DTR rows to add</li><li><?= (int)$preview['add_logs'] ?> attendance logs to add</li><li><?= (int)$preview['update_bio'] ?> completed biometric rows to update</li><li><?= (int)$preview['existing'] ?> existing rows to skip</li><li><?= count($preview['conflicts']) ?> conflicts</li></ul>
    <?php if ($preview['conflicts']): ?><div class="alert alert-danger"><strong>Import blocked until these are fixed:</strong><ul><?php foreach (array_slice($preview['conflicts'], 0, 30) as $conflict): ?><li><?= at_html($conflict) ?></li><?php endforeach; ?></ul></div><?php else: ?>
      <form method="post"><input type="hidden" name="csrf" value="<?= at_html($csrf) ?>"><input type="hidden" name="action" value="confirm"><button class="btn btn-success" type="submit">Import these records</button></form>
    <?php endif; ?>
  </div></div>
  <?php endif; ?>
</div></section>
<?php include __DIR__ . '/footer.php'; ?>
</div>
<?php include __DIR__ . '/scripts_files.php'; ?>
</body></html>
