<?php
 
    include('session.php');
    
    $conn->query("UPDATE service_record SET appointDate_status='' WHERE sr_id!='$_GET[sr_id]' AND personnel_id='$_GET[personnel_id]'");
    
    $conn->query("UPDATE service_record SET appointDate_status='Active' WHERE sr_id='$_GET[sr_id]' AND personnel_id='$_GET[personnel_id]'");
    
    $conn->query("UPDATE personnels SET appointment_date='$_GET[appointment_date]' WHERE personnel_id='$_GET[personnel_id]'");
    
 
?>

<script>
window.alert('Appointment date set successfully...');
window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
</script>   


 