<?php include('session.php'); ?>

<?php

if(isset($_POST['createIncome']))
{
    try {
        $income_type = $_POST['income_type'];
        $income_title = trim($_POST['income_title']);
        
        // Check if income reference already exists using prepared statement
        $payProfile_query = $conn->prepare("SELECT income_id FROM pr_tbl_income WHERE income_type = :income_type AND income_title = :income_title AND is_deleted = 0");
        $payProfile_query->execute([
            ':income_type' => $income_type,
            ':income_title' => $income_title
        ]);
        
        if($payProfile_query->rowCount() > 0){
?>

<script>
window.alert('Income reference <?php echo htmlspecialchars($income_type . " - " . $income_title); ?> already exist, please try again.');
window.location='income.php';
</script>

<?php
        } else {
            // Insert new income reference using prepared statement
            $insert_query = $conn->prepare("INSERT INTO pr_tbl_income(income_type, income_title, user_id, created_at) VALUES(:income_type, :income_title, :user_id, NOW())");
            $insert_query->execute([
                ':income_type' => $income_type,
                ':income_title' => $income_title,
                ':user_id' => $session_id
            ]);
?>

<script>
window.alert('Income reference <?php echo htmlspecialchars($income_title); ?> added successfully.');
window.location='income.php';
</script>

<?php
        }
    } catch (PDOException $e) {
        error_log("Error creating income reference: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while adding the income reference. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>



<?php

if(isset($_POST['updateIncome']))
{
    try {
        $income_id = $_POST['income_id'];
        $income_type = $_POST['income_type'];
        $income_title = trim($_POST['income_title']);
        
        // Update income reference using prepared statement
        $update_query = $conn->prepare("UPDATE pr_tbl_income SET 
            income_type = :income_type, 
            income_title = :income_title,
            updated_at = NOW()
        WHERE income_id = :income_id");
        
        $update_query->execute([
            ':income_type' => $income_type,
            ':income_title' => $income_title,
            ':income_id' => $income_id
        ]);
?>

<script>
window.alert('Income reference <?php echo htmlspecialchars($income_title); ?> updated successfully.');
window.location='income.php';
</script>

<?php
    } catch (PDOException $e) {
        error_log("Error updating income reference: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while updating the income reference. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>


<?php

if(isset($_POST['delIncome']))
{   
    try {
        $income_id = $_POST['income_id'];
        
        // Get income details before deletion
        $payProfile_query = $conn->prepare("SELECT income_title FROM pr_tbl_income WHERE income_id = :income_id");
        $payProfile_query->execute([':income_id' => $income_id]);
        $payProfile_row = $payProfile_query->fetch();
        
        if (!$payProfile_row) {
?>
<script>
window.alert('Income reference not found.');
window.location='income.php';
</script>
<?php
            exit();
        }
        
        $income_title = $payProfile_row['income_title'];
        
        // Soft delete using prepared statement
        $delete_query = $conn->prepare("UPDATE pr_tbl_income SET is_deleted = 1, deleted_at = NOW() WHERE income_id = :income_id");
        $delete_query->execute([':income_id' => $income_id]);
    
?>

<script>
window.alert('Income reference <?php echo htmlspecialchars($income_title); ?> deleted successfully.');
window.location='income.php';
</script>

<?php
    } catch (PDOException $e) {
        error_log("Error deleting income reference: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while deleting the income reference. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>