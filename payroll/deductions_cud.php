<?php include('session.php'); ?>

<?php

if(isset($_POST['createDeduction']))
{
    try {
        $deduction_type = $_POST['deduction_type'];
        $deduction_title = trim($_POST['deduction_title']);
        
        // Check if deduction reference already exists using prepared statement
        $payProfile_query = $conn->prepare("SELECT deduction_id FROM pr_tbl_deductions WHERE deduction_type = :deduction_type AND deduction_title = :deduction_title AND is_deleted = 0");
        $payProfile_query->execute([
            ':deduction_type' => $deduction_type,
            ':deduction_title' => $deduction_title
        ]);
        
        if($payProfile_query->rowCount() > 0){
?>

<script>
window.alert('Deduction reference <?php echo htmlspecialchars($deduction_type . " - " . $deduction_title); ?> already exist, please try again.');
window.location='deductions.php';
</script>

<?php
        } else {
            // Insert new deduction reference using prepared statement
            $insert_query = $conn->prepare("INSERT INTO pr_tbl_deductions(deduction_type, deduction_title, user_id, created_at) VALUES(:deduction_type, :deduction_title, :user_id, NOW())");
            $insert_query->execute([
                ':deduction_type' => $deduction_type,
                ':deduction_title' => $deduction_title,
                ':user_id' => $session_id
            ]);
?>

<script>
window.alert('Deduction reference <?php echo htmlspecialchars($deduction_title); ?> added successfully.');
window.location='deductions.php';
</script>

<?php
        }
    } catch (PDOException $e) {
        error_log("Error creating deduction reference: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while adding the deduction reference. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>



<?php

if(isset($_POST['updateDeduction']))
{
    try {
        $deduction_id = $_POST['deduction_id'];
        $deduction_type = $_POST['deduction_type'];
        $deduction_title = trim($_POST['deduction_title']);
        
        // Update deduction reference using prepared statement
        $update_query = $conn->prepare("UPDATE pr_tbl_deductions SET 
            deduction_type = :deduction_type, 
            deduction_title = :deduction_title,
            updated_at = NOW()
        WHERE deduction_id = :deduction_id");
        
        $update_query->execute([
            ':deduction_type' => $deduction_type,
            ':deduction_title' => $deduction_title,
            ':deduction_id' => $deduction_id
        ]);
?>

<script>
window.alert('Deduction reference <?php echo htmlspecialchars($deduction_title); ?> updated successfully.');
window.location='deductions.php';
</script>

<?php
    } catch (PDOException $e) {
        error_log("Error updating deduction reference: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while updating the deduction reference. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>


<?php

if(isset($_POST['delDeduction']))
{   
    try {
        $deduction_id = $_POST['deduction_id'];
        
        // Get deduction details before deletion
        $payProfile_query = $conn->prepare("SELECT deduction_title FROM pr_tbl_deductions WHERE deduction_id = :deduction_id");
        $payProfile_query->execute([':deduction_id' => $deduction_id]);
        $payProfile_row = $payProfile_query->fetch();
        
        if (!$payProfile_row) {
?>
<script>
window.alert('Deduction reference not found.');
window.location='deductions.php';
</script>
<?php
            exit();
        }
        
        $deduction_title = $payProfile_row['deduction_title'];
        
        // Soft delete using prepared statement
        $delete_query = $conn->prepare("UPDATE pr_tbl_deductions SET is_deleted = 1, deleted_at = NOW() WHERE deduction_id = :deduction_id");
        $delete_query->execute([':deduction_id' => $deduction_id]);
    
?>

<script>
window.alert('Deduction reference <?php echo htmlspecialchars($deduction_title); ?> deleted successfully.');
window.location='deductions.php';
</script>

<?php
    } catch (PDOException $e) {
        error_log("Error deleting deduction reference: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while deleting the deduction reference. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>