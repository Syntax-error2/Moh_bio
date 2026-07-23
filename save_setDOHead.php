<?php

include('session.php');
include('dbcon.php');

if(isset($_POST['setDH'])){
    

$RFTag_id=substr($_POST['RFTag_id'], 0, 8);

$fnameList_stmt = $conn->prepare("SELECT personnel_id FROM personnels WHERE RFTag_id = :RFTag_id");
$fnameList_stmt->execute([':RFTag_id' => $RFTag_id]);
$fnameList_query = $fnameList_stmt;
$fnlq_row = $fnameList_query->fetch();
                                                
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