<?php include('session.php'); ?>
  
<?php

if(isset($_POST['save_new_entry']))
{
    try {
        $personnel_id = $_POST['personnel_id'];
        $do_id = $_POST['do_id'];
        
        $period_from = $_POST['period_from'];
        $period_to = $_POST['period_to'];
        
        $particulars = $_POST['particulars'];
        
        // Check if special leave (values are saved but won't deduct from balance)
        $is_special_leave = isset($_POST['is_special_leave']) ? 1 : 0;
        
        // Save all values as entered by user (even for special leave)
        // Special leave values are saved but balance calculation in leave_card.php ignores them
        // Cast all numeric values to float to ensure proper storage and calculation
        $vl_earned = floatval($_POST['vl_earned'] ?? 0);
        $vl_with_pay = floatval($_POST['vl_with_pay'] ?? 0);
        $vl_without_pay = floatval($_POST['vl_without_pay'] ?? 0);
        
        $sl_earned = floatval($_POST['sl_earned'] ?? 0);
        $sl_with_pay = floatval($_POST['sl_with_pay'] ?? 0);
        $sl_without_pay = floatval($_POST['sl_without_pay'] ?? 0);
        
        $remarks = trim($_POST['remarks'] ?? '');
        
        // Check if entry period already exists using prepared statement
        $chk_entry_query = $conn->prepare("SELECT id FROM leave_card WHERE personnel_id = :personnel_id AND period_from = :period_from AND period_to = :period_to");
        $chk_entry_query->execute([
            ':personnel_id' => $personnel_id,
            ':period_from' => $period_from,
            ':period_to' => $period_to
        ]);
        
        if($chk_entry_query->rowCount() > 0){
?>

<script>
window.alert('Entry period already exist, please try again.');
window.location='leave_card.php?dept=<?php echo urlencode($do_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>';
</script>

<?php
        } else {
            // Insert new entry using prepared statement
            $insert_query = $conn->prepare("INSERT INTO leave_card(
                personnel_id, 
                period_from, 
                period_to, 
                particulars, 
                vl_earned, 
                vl_with_pay, 
                vl_without_pay, 
                sl_with_pay, 
                sl_earned, 
                sl_without_pay, 
                remarks,
                is_special_leave
            ) VALUES (
                :personnel_id, 
                :period_from, 
                :period_to, 
                :particulars, 
                :vl_earned, 
                :vl_with_pay, 
                :vl_without_pay, 
                :sl_with_pay, 
                :sl_earned, 
                :sl_without_pay, 
                :remarks,
                :is_special_leave
            )");
            
            $insert_query->execute([
                ':personnel_id' => $personnel_id,
                ':period_from' => $period_from,
                ':period_to' => $period_to,
                ':particulars' => $particulars,
                ':vl_earned' => $vl_earned,
                ':vl_with_pay' => $vl_with_pay,
                ':vl_without_pay' => $vl_without_pay,
                ':sl_with_pay' => $sl_with_pay,
                ':sl_earned' => $sl_earned,
                ':sl_without_pay' => $sl_without_pay,
                ':remarks' => $remarks,
                ':is_special_leave' => $is_special_leave
            ]);
?>

<script>
window.alert('Leave card entry successfully added...');
window.location='leave_card.php?dept=<?php echo urlencode($do_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>';
</script>

<?php
        }
        
    } catch (PDOException $e) {
        error_log("Error saving leave card entry: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while saving the entry. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>



<?php

if(isset($_POST['update_lc_entry']))
{
    try {
        $id = $_POST['id'];
        
        $personnel_id = $_POST['personnel_id'];
        $do_id = $_POST['do_id'] ?? '';
        
        // If do_id not provided, fetch from database
        if (empty($do_id)) {
            $dept_query = $conn->prepare("SELECT do_id FROM personnels WHERE personnel_id = :personnel_id");
            $dept_query->execute([':personnel_id' => $personnel_id]);
            $dept_row = $dept_query->fetch();
            $do_id = $dept_row['do_id'] ?? '';
        }
        
        $period_from = $_POST['period_from'];
        $period_to = $_POST['period_to'];
        
        $particulars = $_POST['particulars'];
        
        // Check if special leave (values are saved but won't deduct from balance)
        $is_special_leave = isset($_POST['is_special_leave']) ? 1 : 0;
        
        // Save all values as entered by user (even for special leave)
        // Special leave values are saved but balance calculation in leave_card.php ignores them
        // Cast all numeric values to float to ensure proper storage and calculation
        $vl_earned = floatval($_POST['vl_earned'] ?? 0);
        $vl_with_pay = floatval($_POST['vl_with_pay'] ?? 0);
        $vl_without_pay = floatval($_POST['vl_without_pay'] ?? 0);
        
        $sl_earned = floatval($_POST['sl_earned'] ?? 0);
        $sl_with_pay = floatval($_POST['sl_with_pay'] ?? 0);
        $sl_without_pay = floatval($_POST['sl_without_pay'] ?? 0);
        
        $remarks = trim($_POST['remarks'] ?? '');
        
        // Update entry using prepared statement
        $update_query = $conn->prepare("UPDATE leave_card SET 
            period_from = :period_from, 
            period_to = :period_to, 
            particulars = :particulars, 
            vl_earned = :vl_earned, 
            vl_with_pay = :vl_with_pay, 
            vl_without_pay = :vl_without_pay, 
            sl_with_pay = :sl_with_pay, 
            sl_earned = :sl_earned, 
            sl_without_pay = :sl_without_pay, 
            remarks = :remarks,
            is_special_leave = :is_special_leave 
        WHERE id = :id");
        
        $update_query->execute([
            ':period_from' => $period_from,
            ':period_to' => $period_to,
            ':particulars' => $particulars,
            ':vl_earned' => $vl_earned,
            ':vl_with_pay' => $vl_with_pay,
            ':vl_without_pay' => $vl_without_pay,
            ':sl_with_pay' => $sl_with_pay,
            ':sl_earned' => $sl_earned,
            ':sl_without_pay' => $sl_without_pay,
            ':remarks' => $remarks,
            ':is_special_leave' => $is_special_leave,
            ':id' => $id
        ]);
        
        // ====================================
        // UPDATE CORRESPONDING LEAVE APPLICATION
        // ====================================
        // Check if this leave card entry is linked to a leave application
        $linked_app_query = $conn->prepare("SELECT id FROM leave_applications WHERE leave_card_entry_id = :leave_card_id");
        $linked_app_query->execute([':leave_card_id' => $id]);
        $linked_app = $linked_app_query->fetch();
        
        if ($linked_app) {
            // Update the corresponding leave application with new values
            $update_app_query = $conn->prepare("UPDATE leave_applications SET
                inclusive_date_from = :date_from,
                inclusive_date_to = :date_to,
                less_application_vl = :vl_with_pay,
                less_application_sl = :sl_with_pay,
                updated_at = NOW()
            WHERE leave_card_entry_id = :leave_card_id");
            
            $update_app_query->execute([
                ':date_from' => $period_from,
                ':date_to' => $period_to,
                ':vl_with_pay' => $vl_with_pay,
                ':sl_with_pay' => $sl_with_pay,
                ':leave_card_id' => $id
            ]);
        }
?>

<script>
window.alert('Leave card entry successfully updated...');
window.location='leave_card.php?dept=<?php echo urlencode($do_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>';
</script>

<?php
    } catch (PDOException $e) {
        error_log("Error updating leave card entry: " . $e->getMessage());
?>
<script>
window.alert('An error occurred while updating the entry. Please try again.');
window.history.back();
</script>
<?php
    }
} ?>


<?php

if(isset($_POST['delete_lc_entry']))
{   
    try {
        $id = $_POST['id'];
        $personnel_id = $_POST['personnel_id'];
        $do_id = $_POST['do_id'] ?? '';
        
        // Get entry details before deletion for confirmation message
        $entry_query = $conn->prepare("SELECT * FROM leave_card WHERE id = :id AND personnel_id = :personnel_id");
        $entry_query->execute([
            ':id' => $id,
            ':personnel_id' => $personnel_id
        ]);
        $entry_row = $entry_query->fetch();
        
        if (!$entry_row) {
            ?>
            <script>
            window.alert('Leave card entry not found or access denied.');
            window.history.back();
            </script>
            <?php
            exit();
        }
        
        // Get department ID for redirect if not provided
        if (empty($do_id)) {
            $dept_query = $conn->prepare("SELECT do_id FROM personnels WHERE personnel_id = :personnel_id");
            $dept_query->execute([':personnel_id' => $personnel_id]);
            $dept_row = $dept_query->fetch();
            $do_id = $dept_row['do_id'] ?? '';
        }
        
        // ====================================
        // DELETE CORRESPONDING LEAVE APPLICATION FIRST
        // ====================================
        // Check if this leave card entry is linked to a leave application
        $linked_app_query = $conn->prepare("SELECT id, leave_type FROM leave_applications WHERE leave_card_entry_id = :leave_card_id");
        $linked_app_query->execute([':leave_card_id' => $id]);
        $linked_app = $linked_app_query->fetch();
        
        if ($linked_app) {
            // Delete the linked leave application
            $delete_app_query = $conn->prepare("DELETE FROM leave_applications WHERE leave_card_entry_id = :leave_card_id");
            $delete_app_query->execute([':leave_card_id' => $id]);
            
            // Also delete any DTR entries linked to this application
            $delete_dtr_query = $conn->prepare("DELETE FROM leave_applicants WHERE leave_application_id = :app_id");
            $delete_dtr_query->execute([':app_id' => $linked_app['id']]);
        }
        
        // Delete the leave card entry using prepared statement
        $delete_query = $conn->prepare("DELETE FROM leave_card WHERE id = :id AND personnel_id = :personnel_id");
        $delete_query->execute([
            ':id' => $id,
            ':personnel_id' => $personnel_id
        ]);
        
        if ($delete_query->rowCount() > 0) {
            $message = 'Leave card entry for period ' . htmlspecialchars($entry_row['period_from'] . ' to ' . $entry_row['period_to']) . ' successfully deleted.';
            if ($linked_app) {
                $message .= ' (Associated leave application also deleted)';
            }
            ?>
            <script>
            window.alert('<?php echo $message; ?>');
            window.location='leave_card.php?dept=<?php echo urlencode($do_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>';
            </script>
            <?php
        } else {
            ?>
            <script>
            window.alert('Failed to delete leave card entry. Please try again.');
            window.history.back();
            </script>
            <?php
        }
        
    } catch (PDOException $e) {
        error_log("Error deleting leave card entry: " . $e->getMessage());
        ?>
        <script>
        window.alert('An error occurred while deleting the entry. Please try again.');
        window.history.back();
        </script>
        <?php
    }
} ?>