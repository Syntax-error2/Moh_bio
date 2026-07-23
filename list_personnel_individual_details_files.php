<!DOCTYPE html>
<html>

<?php
include('session.php');
include('dbcon.php');
include('header.php');
require_once('personnel_files_lib.php');

pfm_ensure_schema($conn);

$personnel_id = (int)($_GET['personnel_id'] ?? 0);
$session_access = $session_access ?? ($_SESSION['useraccess'] ?? '');
$dept_id = $_GET['dept'] ?? '';

$staff_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id LIMIT 1");
$staff_stmt->execute([':personnel_id' => $personnel_id]);
$staff_row = $staff_stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff_row) {
    echo "<script>alert('Personnel not found.'); window.location='list_personnel.php?dept=" . urlencode((string)$dept_id) . "';</script>";
    exit;
}

$shift_stmt = $conn->prepare("SELECT * FROM shifts WHERE shift_id = :shift_id LIMIT 1");
$shift_stmt->execute([':shift_id' => $staff_row['shift_id']]);
$es_row5 = $shift_stmt->fetch(PDO::FETCH_ASSOC);

$session_access = $session_access ?? ($_SESSION['useraccess'] ?? '');
$user_personnel_id = $user_personnel_id ?? (int)($_SESSION['user_personnel_id'] ?? 0);

$isAdmin = pfm_is_admin($session_access);
$canManageFiles = pfm_can_manage_personnel_files($session_access, $user_personnel_id, $personnel_id);

$defaultFolderId = pfm_ensure_default_201_folder($conn, $personnel_id);

$folder_stmt = $conn->prepare("SELECT folder_id, folder_name, folder_slug, is_system_201 FROM personnel_file_folders WHERE personnel_id = :personnel_id ORDER BY is_system_201 DESC, folder_name ASC");
$folder_stmt->execute([':personnel_id' => $personnel_id]);
$folders = $folder_stmt->fetchAll(PDO::FETCH_ASSOC);

$file_stmt = $conn->prepare("SELECT f.file_id, f.file_name, f.file_type, f.date_time_uploaded, ff.folder_id, ff.folder_name, ff.is_system_201
                            FROM files f
                            LEFT JOIN personnel_file_folders ff ON f.folder_id = ff.folder_id
                            WHERE f.personnel_id = :personnel_id
                            ORDER BY f.file_id DESC");
$file_stmt->execute([':personnel_id' => $personnel_id]);
$files = $file_stmt->fetchAll(PDO::FETCH_ASSOC);

$audit_stmt = $conn->prepare("SELECT l.audit_id, l.action_name, l.actor_personnel_id, l.actor_access, l.target_personnel_id, l.folder_id, l.file_id, l.action_details, l.date_created,
                     p.lname, p.fname, p.mname
                 FROM personnel_file_audit_logs l
                 LEFT JOIN personnels p ON l.actor_personnel_id = p.personnel_id
                 WHERE l.target_personnel_id = :target_personnel_id
                 ORDER BY l.audit_id DESC
                 LIMIT 200");
$audit_stmt->execute([':target_personnel_id' => $personnel_id]);
$audit_logs = $audit_stmt->fetchAll(PDO::FETCH_ASSOC);

$folderFileCounts = [];
foreach ($folders as $folder) {
    $folderFileCounts[(int)$folder['folder_id']] = 0;
}
foreach ($files as $fileRow) {
    $fid = isset($fileRow['folder_id']) ? (int)$fileRow['folder_id'] : 0;
    if (!isset($folderFileCounts[$fid])) {
        $folderFileCounts[$fid] = 0;
    }
    $folderFileCounts[$fid]++;
}
?>

<style>
.pfm-shell {
    border: 1px solid #d8dee9;
    border-radius: 12px;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 38%);
}
.pfm-panel {
    border: 1px solid #d9e2ec;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
}
.pfm-folder-strip {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 6px;
}
.pfm-folder-pill {
    border: 1px solid #d5dbe5;
    border-radius: 999px;
    background: #f1f5f9;
    color: #243b53;
    font-size: 13px;
    padding: 8px 12px;
    white-space: nowrap;
    cursor: pointer;
}
.pfm-folder-pill.active {
    background: #1f9d55;
    border-color: #1f9d55;
    color: #fff;
}
.pfm-folder-pill small {
    opacity: 0.85;
}
.pfm-preview {
    height: 112px;
    border-radius: 10px;
    background: linear-gradient(145deg, #2d3748, #4a5568);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    margin-bottom: 10px;
}
.pfm-preview i {
    font-size: 28px;
}
.pfm-file-card {
    border: 1px solid #d9e2ec;
    border-radius: 10px;
    transition: transform 0.16s ease, box-shadow 0.16s ease;
}
.pfm-file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
}
.pfm-meta {
    font-size: 12px;
    color: #627d98;
}
.pfm-badge-count {
    background: #0f766e;
    color: #ffffff;
    font-weight: 600;
    font-size: 14px;
    line-height: 1.2;
    padding: 8px 12px;
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
}
.pfm-badge-protected {
    background: #374151;
    color: #ffffff;
    font-weight: 600;
    font-size: 12px;
    line-height: 1;
    padding: 4px 8px;
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    vertical-align: middle;
}
.pfm-badge-folder {
    background: #eef2ff;
    color: #1e3a8a;
    border: 1px solid #c7d2fe;
    font-weight: 600;
    font-size: 12px;
    line-height: 1.2;
    padding: 5px 8px;
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
}
.pfm-badge-type {
    background: #334155;
    color: #ffffff;
    font-weight: 600;
    font-size: 12px;
    line-height: 1.2;
    padding: 5px 8px;
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
}
.pfm-badge-audit-total {
    background: #1f2937;
    color: #ffffff;
    font-weight: 600;
    font-size: 13px;
    line-height: 1.2;
    padding: 7px 10px;
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
}
.pfm-badge-action {
    background: #0369a1;
    color: #ffffff;
    font-weight: 600;
    font-size: 12px;
    line-height: 1.2;
    padding: 5px 8px;
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
}
</style>

<body>

<?php include('menu_sidebar.php'); ?>

<div class="page">

<?php include('navbar_header.php'); ?>

<div class="breadcrumb-holder">
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item active">Personnels</li>
            <li class="breadcrumb-item active">Files</li>
        </ul>
    </div>
</div>

<div class="">
    <ul class="nav nav-pills breadcrumb p-2 pl-4">
        <li class="nav-item pl-2">
            <a class="nav-link disabled text-bold" aria-disabled="true">PERSONNELS</a>
        </li>

        <li class="nav-item dropdown">
            <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">Profile</a>
            <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                <li><a class="dropdown-item" href="list_personnel_individual_details.php?dept=<?php echo urlencode((string)$dept_id); ?>&personnel_id=<?php echo urlencode((string)$personnel_id); ?>">Personnel Data</a></li>
                <li><a class="dropdown-item" href="list_personnel_individual_details_EB.php?dept=<?php echo urlencode((string)$dept_id); ?>&personnel_id=<?php echo urlencode((string)$personnel_id); ?>">Educational Background</a></li>
                <li><a class="dropdown-item" href="list_personnel_individual_details_SA.php?dept=<?php echo urlencode((string)$dept_id); ?>&personnel_id=<?php echo urlencode((string)$personnel_id); ?>">Seminars Attended</a></li>
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">Leave Management</a>
            <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                <li><a class="dropdown-item" href="leave_application.php?dept=<?php echo urlencode((string)$dept_id); ?>&personnel_id=<?php echo urlencode((string)$personnel_id); ?>">Leave Applications</a></li>
                <li><a class="dropdown-item" href="leave_card.php?dept=<?php echo urlencode((string)$dept_id); ?>&personnel_id=<?php echo urlencode((string)$personnel_id); ?>">Leave Card</a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="list_personnel_individual_details_SR.php?dept=<?php echo urlencode((string)$dept_id); ?>&personnel_id=<?php echo urlencode((string)$personnel_id); ?>">Service Record</a>
        </li>

        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="list_personnel_individual_details_files.php?dept=<?php echo urlencode((string)$dept_id); ?>&personnel_id=<?php echo urlencode((string)$personnel_id); ?>">Files</a>
        </li>

        <?php if ($session_access !== 'User') { ?>
        <li class="nav-item dropdown">
            <a id="dash-x-menu" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">Quick Options</a>
            <ul aria-labelledby="dash-x-menu" class="dropdown-menu p-0">
                <li><a class="dropdown-item" data-toggle="modal" data-target="#encodeDL<?php echo $staff_row['RFTag_id']; ?>" href="#">Encode Daily Log</a></li>
                <li><a class="dropdown-item" data-toggle="modal" data-target="#restDaySetup<?php echo $staff_row['RFTag_id']; ?>" href="#">Set Rest Day</a></li>
                <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" href="#">CS Form 48</a></li>
                <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_attendance<?php echo $staff_row['RFTag_id']; ?>" href="#">Detailed DTR</a></li>
                <li><a class="dropdown-item" data-toggle="modal" data-target="#print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" href="#">Log Validation</a></li>
            </ul>
        </li>
        <?php } ?>

    </ul>
</div>

<section class="mt-30px mb-30px">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <h2>Files Manager</h2>
                <p class="text-small text-secondary">Organize personnel documents and view activity logs</p>
            </div>

            <div class="col-lg-4 text-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createFolderModal" <?php if(!$canManageFiles){ ?>disabled<?php } ?>>
                    <i class="fa fa-folder"></i> Create Folder
                </button>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadFileModal" <?php if(!$canManageFiles){ ?>disabled<?php } ?>>
                    <i class="fa fa-upload"></i> Upload File
                </button>
            </div>

            <div class="col-lg-12 col-md-12">

                <?php include('encode_daily_log_modal.php'); ?>
                <?php include('restDay_modal.php'); ?>
                <?php include('updateMonthlyLog_modal.php'); ?>
                <?php include('print_monthly_attendance_modal_csf48.php'); ?>
                <?php include('print_monthly_attendance_modal.php'); ?>
                <?php include('print_monthly_LV_modal.php'); ?>
                <?php include('personnel_top_panel.php'); ?>

                <div class="card updates recent-updated pfm-shell">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Files Manager</h2>
                    </div>

                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pfm-files-tab" data-toggle="tab" href="#pfm-files-pane" role="tab" aria-controls="pfm-files-pane" aria-selected="true">Files</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pfm-audit-tab" data-toggle="tab" href="#pfm-audit-pane" role="tab" aria-controls="pfm-audit-pane" aria-selected="false">Activity Logs</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pfm-files-pane" role="tabpanel" aria-labelledby="pfm-files-tab">
                        <div class="mb-3">
                            <div class="d-flex flex-wrap" id="fileTypeChips" style="gap: 8px;">
                                <button type="button" class="btn btn-sm btn-outline-secondary pfm-type-chip active" data-file-group="">All Types</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary pfm-type-chip" data-file-group="image">Images</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary pfm-type-chip" data-file-group="pdf">PDF</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary pfm-type-chip" data-file-group="doc">Docs</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary pfm-type-chip" data-file-group="sheet">Sheets</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary pfm-type-chip" data-file-group="text">Text</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary pfm-type-chip" data-file-group="other">Other</button>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-6 mb-2">
                                <input id="filesSearchInput" type="text" class="form-control" placeholder="Search file name..." />
                            </div>
                            <div class="col-md-2 mb-2">
                                <select id="filesFolderFilter" class="form-control">
                                    <option value="">All Folders</option>
                                    <?php foreach($folders as $folder){ ?>
                                        <option value="<?php echo htmlspecialchars(strtolower((string)$folder['folder_name'])); ?>"><?php echo htmlspecialchars((string)$folder['folder_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <select id="filesSortSelect" class="form-control">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="az">A-Z</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2 text-md-right">
                                <div class="badge p-2 pfm-badge-count">Files: <?php echo count($files); ?></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Folders</h6>
                                <small class="text-muted">Tap a folder pill to filter</small>
                            </div>
                            <div class="pfm-folder-strip" id="folderPills">
                                <button type="button" class="pfm-folder-pill active" data-folder="">
                                    All Folders <small>(<?php echo (int)count($files); ?>)</small>
                                </button>
                                <?php foreach($folders as $folder){
                                    $folderId = (int)$folder['folder_id'];
                                    $count = isset($folderFileCounts[$folderId]) ? (int)$folderFileCounts[$folderId] : 0;
                                    ?>
                                    <button type="button" class="pfm-folder-pill" data-folder="<?php echo htmlspecialchars(strtolower((string)$folder['folder_name'])); ?>">
                                        <?php echo htmlspecialchars((string)$folder['folder_name']); ?> <small>(<?php echo $count; ?>)</small>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="table-responsive mb-3 pfm-panel p-2">
                            <table class="table table-bordered table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Folder</th>
                                        <th style="width: 40%;">Rename Folder</th>
                                        <th style="width: 25%;">Delete Folder</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($folders as $folder){ ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars((string)$folder['folder_name']); ?>
                                            <?php if((int)$folder['is_system_201'] === 1){ ?>
                                                <div class="badge pfm-badge-protected">Protected</div>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if($canManageFiles && (int)$folder['is_system_201'] !== 1){ ?>
                                            <form action="save_add_personnel.php?dept=<?php echo urlencode((string)$dept_id); ?>" method="POST" class="form-inline">
                                                <input type="hidden" name="personnel_id" value="<?php echo (int)$personnel_id; ?>" />
                                                <input type="hidden" name="folder_id" value="<?php echo (int)$folder['folder_id']; ?>" />
                                                <input type="text" name="folder_name" class="form-control form-control-sm mr-2" value="<?php echo htmlspecialchars((string)$folder['folder_name']); ?>" required />
                                                <button type="submit" name="save_renameFileFolder" class="btn btn-outline-primary btn-sm">Save</button>
                                            </form>
                                            <?php }else{ ?>
                                            <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if($canManageFiles && (int)$folder['is_system_201'] !== 1){ ?>
                                            <form action="save_add_personnel.php?dept=<?php echo urlencode((string)$dept_id); ?>" method="POST" onsubmit="return confirm('Delete this folder? It must be empty.');">
                                                <input type="hidden" name="personnel_id" value="<?php echo (int)$personnel_id; ?>" />
                                                <input type="hidden" name="folder_id" value="<?php echo (int)$folder['folder_id']; ?>" />
                                                <button type="submit" name="save_deleteFileFolder" class="btn btn-outline-danger btn-sm">Delete</button>
                                            </form>
                                            <?php }else{ ?>
                                            <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row" id="filesGrid">
                            <?php foreach($files as $file){
                                $folderName = (string)($file['folder_name'] ?? '201-files');
                                $fileName = basename((string)$file['file_name']);
                                $fileType = strtoupper((string)($file['file_type'] ?? 'FILE'));
                                $canDeleteFile = $isAdmin || ((int)$user_personnel_id === (int)$personnel_id && (int)($file['is_system_201'] ?? 0) !== 1);
                                $fileExt = strtolower((string)($file['file_type'] ?? ''));
                                $iconClass = 'fa-file-o';
                                $fileGroup = 'other';
                                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                                    $iconClass = 'fa-file-image-o';
                                    $fileGroup = 'image';
                                } elseif ($fileExt === 'pdf') {
                                    $iconClass = 'fa-file-pdf-o';
                                    $fileGroup = 'pdf';
                                } elseif (in_array($fileExt, ['doc', 'docx'], true)) {
                                    $iconClass = 'fa-file-word-o';
                                    $fileGroup = 'doc';
                                } elseif (in_array($fileExt, ['xls', 'xlsx'], true)) {
                                    $iconClass = 'fa-file-excel-o';
                                    $fileGroup = 'sheet';
                                } elseif ($fileExt === 'txt') {
                                    $fileGroup = 'text';
                                }
                            ?>
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-3 file-card"
                                     data-file-name="<?php echo htmlspecialchars(strtolower($fileName)); ?>"
                                     data-folder-name="<?php echo htmlspecialchars(strtolower($folderName)); ?>"
                                     data-file-group="<?php echo htmlspecialchars($fileGroup); ?>"
                                     data-file-id="<?php echo (int)$file['file_id']; ?>"
                                     data-uploaded="<?php echo htmlspecialchars((string)$file['date_time_uploaded']); ?>">
                                    <div class="card h-100 pfm-file-card">
                                        <div class="card-body d-flex flex-column">
                                            <div class="pfm-preview">
                                                <i class="fa <?php echo $iconClass; ?>" aria-hidden="true"></i>
                                            </div>
                                            <div class="mb-2 d-flex flex-wrap">
                                                <div class="badge mr-1 pfm-badge-folder"><i class="fa fa-folder-open"></i> <?php echo htmlspecialchars($folderName); ?></div>
                                                <div class="badge pfm-badge-type"><?php echo htmlspecialchars($fileType); ?></div>
                                            </div>
                                            <h6 class="mb-2" style="word-break: break-word;"><?php echo htmlspecialchars($fileName); ?></h6>
                                            <small class="pfm-meta mb-3"><?php echo htmlspecialchars((string)$file['date_time_uploaded']); ?></small>
                                            <div class="mt-auto d-flex justify-content-between">
                                                <a class="btn btn-sm btn-info" href="download_201.php?file_id=<?php echo (int)$file['file_id']; ?>">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                                <?php if($canDeleteFile){ ?>
                                                <a class="btn btn-sm btn-danger" onclick="return confirm('Delete this file?');" href="delete201Files.php?dept=<?php echo urlencode((string)$dept_id); ?>&file_id=<?php echo (int)$file['file_id']; ?>">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div id="filesEmptyState" class="alert alert-secondary" style="display: none;">
                            No files match your search/filter.
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 pfm-panel p-2">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <span class="mr-2">Per page:</span>
                                <select id="pfmPerPage" class="form-control form-control-sm" style="width: 90px;">
                                    <option value="8">8</option>
                                    <option value="12" selected>12</option>
                                    <option value="24">24</option>
                                </select>
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" id="pfmPrevPage" class="btn btn-sm btn-outline-secondary mr-2">Prev</button>
                                <span id="pfmPageInfo" class="mr-2">Page 1 of 1</span>
                                <button type="button" id="pfmNextPage" class="btn btn-sm btn-outline-secondary">Next</button>
                            </div>
                        </div>

                            </div>

                            <div class="tab-pane fade" id="pfm-audit-pane" role="tabpanel" aria-labelledby="pfm-audit-tab">
                                <div class="row mb-2">
                                    <div class="col-md-6 mb-2">
                                        <input id="auditSearchInput" type="text" class="form-control" placeholder="Search activity logs..." />
                                    </div>
                                    <div class="col-md-6 text-md-right mb-2">
                                        <div class="badge p-2 pfm-badge-audit-total">Showing latest <?php echo (int)count($audit_logs); ?> activities</div>
                                    </div>
                                </div>

                                <div class="table-responsive pfm-panel p-2">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Date</th>
                                                <th style="width: 15%;">Action</th>
                                                <th style="width: 20%;">Actor</th>
                                                <th style="width: 45%;">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($audit_logs as $log){
                                                $mi = !empty($log['mname']) ? (' ' . strtoupper(substr((string)$log['mname'], 0, 1)) . '.') : '';
                                                $actorName = trim((string)($log['fname'] ?? '') . $mi . ' ' . (string)($log['lname'] ?? ''));
                                                if ($actorName === '') {
                                                    $actorName = 'System/User #' . (int)($log['actor_personnel_id'] ?? 0);
                                                }
                                                $detailsText = (string)($log['action_details'] ?? '');
                                                ?>
                                                <tr class="audit-row" data-audit-text="<?php echo htmlspecialchars(strtolower((string)$log['action_name'] . ' ' . $actorName . ' ' . $detailsText)); ?>">
                                                    <td><?php echo htmlspecialchars((string)$log['date_created']); ?></td>
                                                    <td><div class="badge pfm-badge-action"><?php echo htmlspecialchars((string)$log['action_name']); ?></div></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($actorName); ?><br />
                                                        <small class="text-muted"><?php echo htmlspecialchars((string)($log['actor_access'] ?? '')); ?></small>
                                                    </td>
                                                    <td style="word-break: break-word;"><?php echo htmlspecialchars($detailsText !== '' ? $detailsText : '-'); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="createFolderModal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <form action="save_add_personnel.php?dept=<?php echo urlencode((string)$dept_id); ?>" method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Folder</h5>
                                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="personnel_id" value="<?php echo (int)$personnel_id; ?>" />
                                            <div class="form-group mb-0">
                                                <label>Folder Name</label>
                                                <input type="text" name="folder_name" class="form-control" placeholder="Enter folder name" <?php if(!$canManageFiles){ ?>disabled<?php } ?> required />
                                                <small class="form-text text-muted">Do not use 201-files, this is a reserved system folder.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                                            <button type="submit" name="save_createFileFolder" class="btn btn-primary" <?php if(!$canManageFiles){ ?>disabled<?php } ?>>Create</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div id="uploadFileModal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="save_add_personnel.php?dept=<?php echo urlencode((string)$dept_id); ?>" method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Upload File</h5>
                                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="personnel_id" value="<?php echo (int)$personnel_id; ?>" />
                                            <div class="form-row">
                                                <div class="col-md-5">
                                                    <label>Folder</label>
                                                    <select name="folder_id" class="form-control" <?php if(!$canManageFiles){ ?>disabled<?php } ?> required>
                                                        <?php foreach($folders as $folder){ ?>
                                                            <option value="<?php echo (int)$folder['folder_id']; ?>" <?php if((int)$folder['folder_id'] === (int)$defaultFolderId){ ?>selected<?php } ?>>
                                                                <?php echo htmlspecialchars((string)$folder['folder_name']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-7">
                                                    <label>Select File</label>
                                                    <input type="file" name="per_file" class="form-control" <?php if(!$canManageFiles){ ?>disabled<?php } ?> required />
                                                </div>
                                            </div>
                                            <small class="form-text text-muted mt-2">Allowed: PDF, DOC/DOCX, XLS/XLSX, JPG/PNG/GIF, TXT. Maximum file size is 10MB.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                                            <button type="submit" name="save_add201File" class="btn btn-success" <?php if(!$canManageFiles){ ?>disabled<?php } ?>>Upload</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>

</div>

<?php include('scripts_files.php'); ?>

<script>
(function() {
    var searchInput = document.getElementById('filesSearchInput');
    var folderFilter = document.getElementById('filesFolderFilter');
    var sortSelect = document.getElementById('filesSortSelect');
    var typeChips = document.querySelectorAll('.pfm-type-chip');
    var selectedFileGroup = '';
    var pillsWrap = document.getElementById('folderPills');
    var folderPills = pillsWrap ? pillsWrap.querySelectorAll('.pfm-folder-pill') : [];
    var fileCards = document.querySelectorAll('.file-card');
    var allCards = Array.prototype.slice.call(fileCards);
    var grid = document.getElementById('filesGrid');
    var emptyState = document.getElementById('filesEmptyState');
    var perPageSelect = document.getElementById('pfmPerPage');
    var prevBtn = document.getElementById('pfmPrevPage');
    var nextBtn = document.getElementById('pfmNextPage');
    var pageInfo = document.getElementById('pfmPageInfo');
    var currentPage = 1;

    var auditSearchInput = document.getElementById('auditSearchInput');
    var auditRows = document.querySelectorAll('.audit-row');

    function parseDateValue(raw) {
        if (!raw) {
            return 0;
        }
        var text = raw.split('|')[0].trim();
        var parts = text.split('/');
        if (parts.length !== 3) {
            return 0;
        }
        var mm = parseInt(parts[0], 10) - 1;
        var dd = parseInt(parts[1], 10);
        var yy = parseInt(parts[2], 10);
        var dt = new Date(yy, mm, dd);
        return dt.getTime() || 0;
    }

    function applySort() {
        if (!grid || !sortSelect) {
            return;
        }
        var cards = Array.prototype.slice.call(allCards);
        var mode = sortSelect.value || 'newest';

        cards.sort(function(a, b) {
            if (mode === 'az') {
                var an = (a.getAttribute('data-file-name') || '');
                var bn = (b.getAttribute('data-file-name') || '');
                return an.localeCompare(bn);
            }
            var ad = parseDateValue(a.getAttribute('data-uploaded'));
            var bd = parseDateValue(b.getAttribute('data-uploaded'));
            if (mode === 'oldest') {
                return ad - bd;
            }
            return bd - ad;
        });

        cards.forEach(function(card) {
            grid.appendChild(card);
        });
    }

    function getFilteredCards() {
        var searchVal = (searchInput.value || '').toLowerCase().trim();
        var folderVal = (folderFilter.value || '').toLowerCase().trim();
        var filtered = [];

        allCards.forEach(function(card) {
            var cardName = card.getAttribute('data-file-name') || '';
            var cardFolder = card.getAttribute('data-folder-name') || '';
            var cardGroup = card.getAttribute('data-file-group') || '';

            var matchName = searchVal === '' || cardName.indexOf(searchVal) !== -1;
            var matchFolder = folderVal === '' || cardFolder === folderVal;
            var matchGroup = selectedFileGroup === '' || cardGroup === selectedFileGroup;

            if (matchName && matchFolder && matchGroup) {
                filtered.push(card);
            }
        });

        return filtered;
    }

    function updatePaginationControls(totalItems, perPage) {
        var totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        if (pageInfo) {
            pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages;
        }
        if (prevBtn) {
            prevBtn.disabled = currentPage <= 1;
        }
        if (nextBtn) {
            nextBtn.disabled = currentPage >= totalPages;
        }

        return totalPages;
    }

    function applyFileFilters() {
        var filteredCards = getFilteredCards();
        var perPage = parseInt((perPageSelect && perPageSelect.value) || '12', 10);
        if (!perPage || perPage < 1) {
            perPage = 12;
        }

        updatePaginationControls(filteredCards.length, perPage);

        var start = (currentPage - 1) * perPage;
        var end = start + perPage;

        allCards.forEach(function(card) {
            card.style.display = 'none';
        });

        filteredCards.slice(start, end).forEach(function(card) {
            card.style.display = '';
        });

        emptyState.style.display = filteredCards.length === 0 ? '' : 'none';
    }

    function setFolderFilter(folderValue) {
        if (folderFilter) {
            folderFilter.value = folderValue;
        }
        folderPills.forEach(function(pill) {
            pill.classList.toggle('active', (pill.getAttribute('data-folder') || '') === folderValue);
        });
        currentPage = 1;
        applyFileFilters();
    }

    function setTypeFilter(groupValue) {
        selectedFileGroup = groupValue || '';
        typeChips.forEach(function(chip) {
            chip.classList.toggle('active', (chip.getAttribute('data-file-group') || '') === selectedFileGroup);
        });
        currentPage = 1;
        applyFileFilters();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentPage = 1;
            applyFileFilters();
        });
    }

    if (folderFilter) {
        folderFilter.addEventListener('change', function() {
            setFolderFilter(folderFilter.value || '');
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            applySort();
            currentPage = 1;
            applyFileFilters();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            applyFileFilters();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                applyFileFilters();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentPage++;
            applyFileFilters();
        });
    }

    typeChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            var value = chip.getAttribute('data-file-group') || '';
            setTypeFilter(value);
        });
    });

    folderPills.forEach(function(pill) {
        pill.addEventListener('click', function() {
            var folderValue = pill.getAttribute('data-folder') || '';
            setFolderFilter(folderValue);
        });
    });

    applySort();
    if (folderFilter) {
        setFolderFilter(folderFilter.value || '');
    } else {
        applyFileFilters();
    }

    if (auditSearchInput && auditRows.length > 0) {
        auditSearchInput.addEventListener('input', function() {
            var q = (auditSearchInput.value || '').toLowerCase().trim();
            auditRows.forEach(function(row) {
                var haystack = row.getAttribute('data-audit-text') || '';
                row.style.display = (q === '' || haystack.indexOf(q) !== -1) ? '' : 'none';
            });
        });
    }
})();
</script>

</body>
</html>
