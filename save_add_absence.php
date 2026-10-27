<?php
include('dbcon.php');
require_once('session.php');

if(isset($_POST['add_absence'])){
    $personnel_id = $_POST['personnel_id'];
    $dept = $_POST['dept'];
    
    $period = $_POST['period'];
    $particulars = $_POST['particulars'];
    $remarks = $_POST['remarks'];
    
    // Convert to float
    $vl_w_pay = (float)$_POST['vl_w_pay'];
    $vl_wout_pay = (float)$_POST['vl_wout_pay'];
    $sl_w_pay = (float)$_POST['sl_w_pay'];
    $sl_wout_pay = (float)$_POST['sl_wout_pay'];
    
    // w/ pay gets stored as NEGATIVE in the amount columns because it deducts
    $vl_amount = -$vl_w_pay;
    $sl_amount = -$sl_w_pay;
    
    // Update current balances in personnels table
    $update = $conn->prepare("UPDATE personnels SET vl_balance = vl_balance - ?, sl_balance = sl_balance - ? WHERE personnel_id = ?");
    $update->execute([$vl_w_pay, $sl_w_pay, $personnel_id]);
    
    // Insert into logs
    $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, period, particulars, vl_amount, vl_wout_pay, sl_amount, sl_wout_pay, remarks) 
                           VALUES (?, 'Manual Absence', ?, ?, ?, ?, ?, ?, ?)");
    $log->execute([$personnel_id, $period, $particulars, $vl_amount, $vl_wout_pay, $sl_amount, $sl_wout_pay, $remarks]);

    $conn=null;
    ?>
    <script>
    window.alert('Manual absence/undertime successfully logged!');
    window.location='list_personnel_individual_details_LC.php?dept=<?php echo $dept; ?>&personnel_id=<?php echo $personnel_id; ?>';
    </script>
    <?php
}
?>
