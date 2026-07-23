<?php


include('session.php');
require_once('personnel_files_lib.php');

pfm_ensure_schema($conn);
  
$file_id = $_GET['file_id'] ?? '';
$fileStmt = $conn->prepare("SELECT f.file_id, f.file_name, f.personnel_id, f.folder_id, ff.is_system_201
						   FROM files f
						   LEFT JOIN personnel_file_folders ff ON f.folder_id = ff.folder_id
						   WHERE f.file_id = :file_id LIMIT 1");
$fileStmt->execute([':file_id' => $file_id]);
$fileRow = $fileStmt->fetch(PDO::FETCH_ASSOC);
$files_backlink = 'list_personnel_individual_details_files.php?dept=' . urlencode((string)($_GET['dept'] ?? '')) . '&personnel_id=' . (int)($fileRow['personnel_id'] ?? 0);

if (!$fileRow) {
	?>
	<script>
	window.alert('File not found.');
	window.location='<?php echo $files_backlink; ?>';
	</script>
	<?php
	exit;
}

$isAdmin = pfm_is_admin($session_access);
$canDelete = false;

if ($isAdmin) {
	$canDelete = true;
} elseif ((int)$fileRow['personnel_id'] === (int)$user_personnel_id && (int)($fileRow['is_system_201'] ?? 0) !== 1) {
	$canDelete = true;
}

if (!$canDelete) {
	?>
	<script>
	window.alert('You are not allowed to delete this file.');
	window.location='<?php echo $files_backlink; ?>';
	</script>
	<?php
	exit;
}

$deleteStmt = $conn->prepare("DELETE FROM files WHERE file_id = :file_id");
$deleteStmt->execute([':file_id' => $file_id]);

pfm_log_action(
	$conn,
	'delete_file',
	(int)$user_personnel_id,
	$session_access,
	(int)$fileRow['personnel_id'],
	isset($fileRow['folder_id']) ? (int)$fileRow['folder_id'] : null,
	(int)$fileRow['file_id'],
	'file_name=' . basename((string)$fileRow['file_name'])
);

if (!empty($fileRow['file_name']) && file_exists($fileRow['file_name'])) {
	@unlink($fileRow['file_name']);
}
 
?>

<script>
window.alert('File deleted successfully...');
window.location='<?php echo $files_backlink; ?>';
</script>    
