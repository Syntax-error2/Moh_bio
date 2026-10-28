<?php
 
    include('session.php');
    
    $dept = $_GET['dept'] ?? '';
    $personnel_id = $_GET['personnel_id'] ?? '';
    $sr_id = $_GET['sr_id'] ?? '';
    $appointment_date = $_GET['appointment_date'] ?? '';

    $stmt1 = $conn->prepare("UPDATE service_record SET appointDate_status='' WHERE sr_id != ? AND personnel_id = ?");
    $stmt1->execute([$sr_id, $personnel_id]);
    
    $stmt2 = $conn->prepare("UPDATE service_record SET appointDate_status='Active' WHERE sr_id = ? AND personnel_id = ?");
    $stmt2->execute([$sr_id, $personnel_id]);
    
    $stmt3 = $conn->prepare("UPDATE personnels SET appointment_date = ? WHERE personnel_id = ?");
    $stmt3->execute([$appointment_date, $personnel_id]);
 
?>

<script>
window.alert('Appointment date set successfully...');
window.location='list_personnel_individual_details_SR.php?dept=<?php echo htmlspecialchars($dept); ?>&personnel_id=<?php echo htmlspecialchars($personnel_id); ?>';
</script>   


 