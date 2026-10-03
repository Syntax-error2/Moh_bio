<?php
 
    include('session.php');
    
    $stmt = $conn->prepare("UPDATE service_record SET appointDate_status='' WHERE sr_id!=? AND personnel_id=?");
    $stmt->execute([$_GET['sr_id'], $_GET['personnel_id']]);
    
    $stmt = $conn->prepare("UPDATE service_record SET appointDate_status='Active' WHERE sr_id=? AND personnel_id=?");
    $stmt->execute([$_GET['sr_id'], $_GET['personnel_id']]);
    
    $stmt = $conn->prepare("UPDATE personnels SET appointment_date=? WHERE personnel_id=?");
    $stmt->execute([$_GET['appointment_date'], $_GET['personnel_id']]);
    
 
?>

<script>
window.alert('Appointment date set successfully...');
window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
</script>   


 