<?php

include('session.php');
include('dbcon.php');

if(isset($_POST['setDH'])){
	$personnelInput = trim($_POST['personnel_id_code'] ?? '');
	$personnelIdCodeParts = explode('|', $personnelInput, 2);
	$personnelIdCode = trim($personnelIdCodeParts[0]);

	$fnameList_stmt = $conn->prepare("SELECT personnel_id FROM personnels WHERE personnel_id_code = :personnel_id_code LIMIT 1");
	$fnameList_stmt->execute([':personnel_id_code' => $personnelIdCode]);
	$fnlq_row = $fnameList_stmt->fetch();

	if (!$fnlq_row) {
?>

<script>
window.alert('Personnel ID code not found.');
window.location='home.php';
</script>

<?php
		exit();
	}

	$do_id = $_GET['do_id'] ?? '';
	$updateStmt = $conn->prepare("UPDATE dept_offices SET officeHead_id = :officeHead_id WHERE do_id = :do_id");
	$updateStmt->execute([
	':officeHead_id' => $fnlq_row['personnel_id'],
	':do_id' => $do_id,
	]);

?>

<script>
window.alert('Department / Office head successfully updated...');
window.location='home.php';
</script>

<?php } ?>