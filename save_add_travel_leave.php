<?php

include('dbcon.php');
require_once('session.php'); 


if(isset($_POST['add_travel']) || isset($_POST['add_leave'])){

$remarks=$_POST['remarks'];
if ($remarks == '1') {
    $remarks = isset($_POST['leave_spec']) && trim($_POST['leave_spec']) !== '' ? trim($_POST['leave_spec']) : 'Others';
}
  

$p1=0;

$no_of_personnels = isset($_POST['no_of_personnels']) ? $_POST['no_of_personnels'] : 1;

while($p1 < $no_of_personnels) //40 of 75 personnels...
{
    
    $n1=0;
    $p1=$p1+1;
    
    if($p1==1){
         
        $parts = explode(" | ", $_POST["jud1"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==2){
      
        $parts = explode(" | ", $_POST["jud2"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==3){
       
        $parts = explode(" | ", $_POST["jud3"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==4){
        //2019-01-21 
        $parts = explode(" | ", $_POST["jud4"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==5){
        
        $parts = explode(" | ", $_POST["jud5"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==6){
        
        $parts = explode(" | ", $_POST["jud6"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==7){
        
        $parts = explode(" | ", $_POST["jud7"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==8){
        
        $parts = explode(" | ", $_POST["jud8"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==9){
        
        $parts = explode(" | ", $_POST["jud9"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==10){
        
        $parts = explode(" | ", $_POST["jud10"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==11){
        
        $parts = explode(" | ", $_POST["jud11"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==12){
        
        $parts = explode(" | ", $_POST["jud12"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==13){
        
        $parts = explode(" | ", $_POST["jud13"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==14){
        
        $parts = explode(" | ", $_POST["jud14"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==15){
        
        $parts = explode(" | ", $_POST["jud15"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==16){
        
        $parts = explode(" | ", $_POST["jud16"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==17){
        
        $parts = explode(" | ", $_POST["jud17"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==18){
        
        $parts = explode(" | ", $_POST["jud18"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==19){
        
        $parts = explode(" | ", $_POST["jud19"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==20){
        
        $parts = explode(" | ", $_POST["jud20"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==21){
        
        $parts = explode(" | ", $_POST["jud21"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==22){
        
        $parts = explode(" | ", $_POST["jud22"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==23){
        
        $parts = explode(" | ", $_POST["jud23"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==24){
        
        $parts = explode(" | ", $_POST["jud24"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==25){
        
        $parts = explode(" | ", $_POST["jud25"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==26){
        
        $parts = explode(" | ", $_POST["jud26"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==27){
        
        $parts = explode(" | ", $_POST["jud27"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==28){
        
        $parts = explode(" | ", $_POST["jud28"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==29){
        
        $parts = explode(" | ", $_POST["jud29"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==30){
        
        $parts = explode(" | ", $_POST["jud30"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==31){
        
        $parts = explode(" | ", $_POST["jud31"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==32){
        
        $parts = explode(" | ", $_POST["jud32"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==33){
        
        $parts = explode(" | ", $_POST["jud33"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==34){
        
        $parts = explode(" | ", $_POST["jud34"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==35){
        
        $parts = explode(" | ", $_POST["jud35"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==36){
        
        $parts = explode(" | ", $_POST["jud36"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==37){
        
        $parts = explode(" | ", $_POST["jud37"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    if($p1==38){
        
        $parts = explode(" | ", $_POST["jud38"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
     if($p1==39){
        
        $parts = explode(" | ", $_POST["jud39"]); $personnel_id_extracted = trim($parts[0]);
    }
    
    
    if($p1==40){
        
        $parts = explode(" | ", $_POST["jud40"]); $personnel_id_extracted = trim($parts[0]);
    }
    


$perData1_query = $conn->query("SELECT RFTag_id, personnel_id, img, lname, fname, mname, suffix, do_id, shift_id FROM personnels WHERE personnel_id='$personnel_id_extracted'");
$pd1_row=$perData1_query->fetch();
        
$img='personnelImg/'.$pd1_row['img'];

        $substitute_pid = 0;
        if (isset($_POST['substitute_rfid']) && !empty(trim($_POST['substitute_rfid']))) {
            $parts2 = explode(" | ", $_POST['substitute_rfid']);
            $sub_id_ext = trim($parts2[0]);
            $perData2_query = $conn->query("SELECT personnel_id FROM personnels WHERE personnel_id='$sub_id_ext'");
            $pd2_row = $perData2_query->fetch();
            if ($pd2_row) {
                $substitute_pid = $pd2_row['personnel_id'];
            }
        }
        
        $leave_code = isset($_POST['leave_code']) ? $_POST['leave_code'] : (isset($_POST['travel_code']) ? $_POST['travel_code'] : '');
        $leave_type_desc = isset($_POST['leave_type_desc']) ? $_POST['leave_type_desc'] : '';
        
        $dates_array = [];
        $first_date = '';
        $last_date = '';
        
        while($n1<$_POST['no_of_days'])
        {
            $n1=$n1+1;
            
            $input_name = 'con' . $n1;
            if(isset($_POST[$input_name])) {
                $logDate = $_POST[$input_name];
                
                if($n1 == 1) {
                    $first_date = $logDate;
                }
                $last_date = $logDate;
                
                $dates_array[] = $logDate;
                
                //save to student logs
                $conn->query("INSERT INTO personnel_logs(RFTag_id, img, lname, fname, mname, suffix, do_id, shift_id, logDate, remarks, travel_leave_code)
                VALUES ('$pd1_row[RFTag_id]', '$img', '$pd1_row[lname]', '$pd1_row[fname]', '$pd1_row[mname]', '$pd1_row[suffix]', '$pd1_row[do_id]', '$pd1_row[shift_id]', '$logDate', '$remarks', '$leave_code')");
            }
        }
  
        // Define final_logDate for leave_applicants (e.g. "YYYY-MM-DD - YYYY-MM-DD")
        $final_logDate = $first_date . " - " . $last_date;
  
        if(isset($_POST['add_leave'])) {
            if(strpos(strtolower($remarks), 'travel') !== false || strpos(strtolower($leave_type_desc), 'travel') !== false) {
                // Determine destination if not provided
                $location = isset($_POST['location']) && !empty($_POST['location']) ? $_POST['location'] : 'Filed via Leave Modal';
                $conn->query("INSERT INTO personnel_official_travel_logs(personnel_id, travel_code, purpose, description, location, travel_date, travel_type, numDays)
                VALUES ('$pd1_row[personnel_id]', '$leave_code', '$leave_type_desc', '$remarks', '$location', '$final_logDate', '$remarks', '$_POST[no_of_days]')");
            }
            
            $conn->query("INSERT INTO leave_applicants(leave_code, leave_date, leave_type, leave_type_desc, substitute_id, applicant_id, do_id, numDays)
            VALUES ('$leave_code', '$final_logDate', '$remarks', '$leave_type_desc', '$substitute_pid', '$pd1_row[personnel_id]', '$pd1_row[do_id]', '$_POST[no_of_days]')");
        
        // --- DEDUCT FROM LEAVE BALANCES AND LOG TO LEAVE CARD ---
        $numDays = (float)$_POST['no_of_days'];
        $vl_deduct = 0;
        $sl_deduct = 0;
        $vl_wout_pay = 0;
        $sl_wout_pay = 0;
        
        $stmt_bal = $conn->prepare("SELECT vl_balance, sl_balance FROM personnels WHERE personnel_id = ?");
        $stmt_bal->execute([$pd1_row['personnel_id']]);
        $bal_row = $stmt_bal->fetch(PDO::FETCH_ASSOC);
        $curr_vl = (float)$bal_row['vl_balance'];
        $curr_sl = (float)$bal_row['sl_balance'];
        
        if(strpos(strtolower($remarks), 'travel') !== false || strpos(strtolower($leave_type_desc), 'travel') !== false ||
           strpos(strtolower($remarks), 'wellness') !== false || strpos(strtolower($leave_type_desc), 'wellness') !== false ||
           strpos(strtolower($remarks), 'emergency') !== false || strpos(strtolower($leave_type_desc), 'emergency') !== false) {
            $vl_deduct = 0;
            $sl_deduct = 0;
            $vl_wout_pay = 0;
            $sl_wout_pay = 0;
        } else if($remarks == 'Vacation Leave' || $remarks == 'Mandatory/Forced Leave') {
            if ($curr_vl >= $numDays) {
                $vl_deduct = $numDays;
                $curr_vl -= $numDays;
            } else {
                $vl_deduct = $curr_vl;
                $vl_wout_pay = $numDays - $curr_vl;
                $curr_vl = 0;
            }
        } else if($remarks == 'Sick Leave') {
            if ($curr_sl >= $numDays) {
                $sl_deduct = $numDays;
                $curr_sl -= $numDays;
            } else {
                $sl_deduct = $curr_sl;
                // Cascade to VL if SL is empty
                $rem = $numDays - $curr_sl;
                $curr_sl = 0;
                
                if ($curr_vl >= $rem) {
                    $vl_deduct = $rem;
                    $curr_vl -= $rem;
                } else {
                    $vl_deduct = $curr_vl;
                    $sl_wout_pay = $rem - $curr_vl;
                    $curr_vl = 0;
                }
            }
        }
        
        $update = $conn->prepare("UPDATE personnels SET vl_balance = ?, sl_balance = ? WHERE personnel_id = ?");
        $update->execute([$curr_vl, $curr_sl, $pd1_row['personnel_id']]);
        
        // Format period for Leave Card (e.g. "06/13/25" or "06/13/25 - 06/15/25")
        $p_from = date('m/d/y', strtotime($first_date));
        $p_to = date('m/d/y', strtotime($last_date));
        $period_str = $p_from . " - " . $p_to;
        
        $particulars = $_POST['leave_type_desc'];
        if(empty(trim($particulars))) {
            $particulars = $remarks;
        }
        
        $logRemarks = "Applied for $remarks [Code: $_POST[leave_code]]";
        if ($vl_wout_pay > 0 || $sl_wout_pay > 0) {
            $lwop_tot = $vl_wout_pay + $sl_wout_pay;
            $logRemarks .= " (Plus $lwop_tot LWOP)";
        }
        
        $log = $conn->prepare("INSERT INTO leave_credit_logs (personnel_id, log_type, period, particulars, vl_amount, sl_amount, vl_wout_pay, sl_wout_pay, remarks) VALUES (?, 'Leave Application', ?, ?, ?, ?, ?, ?, ?)");
        $log->execute([$pd1_row['personnel_id'], $period_str, $particulars, -$vl_deduct, -$sl_deduct, $vl_wout_pay, $sl_wout_pay, $logRemarks]);
        // ----------------------------------
        } // End of if(isset($_POST['add_leave']))

        // --- ADD TRAVEL LOGIC ---
        if(isset($_POST['add_travel'])) {
            if(isset($_POST['add_to'])) {
                $purpose = $_POST['purpose_title'];
                $description = $_POST['description'];
                $location = $_POST['location_venue'];
                
                $conn->query("INSERT INTO personnel_official_travel_logs(personnel_id, travel_code, purpose, description, location, travel_date, travel_type, numDays)
                VALUES ('$pd1_row[personnel_id]', '$leave_code', '$purpose', '$description', '$location', '$final_logDate', '$remarks', '$_POST[no_of_days]')");
            }
            
            if(isset($_POST['add_201_sr'])) {
                $purpose = $_POST['purpose_title'];
                $description = $_POST['description'];
                $location = $_POST['location_venue'];
                $dFrom = date('m/d/Y', strtotime($first_date));
                $dTo = date('m/d/Y', strtotime($last_date));
                
                $conn->query("INSERT INTO personnel_seminars(personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, event_date_to, entry_type)
                VALUES ('$pd1_row[personnel_id]', '$purpose', '$description', '$location', '$dFrom', '$dTo', '$leave_code')");
            }
        }
        
    }

$perData1_query=null;
$perData2_query=null;

    if(isset($_POST['add_travel']) && isset($_POST['add_to'])) {
        $new_tng_sequence = $_POST['new_tng_sequence'];
        $new_tng_mm = isset($_POST['new_tng_mm']) ? $_POST['new_tng_mm'] : date('m');
        $conn->query("UPDATE travel_num_generator SET sequence='$new_tng_sequence', mm='$new_tng_mm'");
    }

$conn=null; 
   
?>
 
<?php if(isset($_POST['add_leave'])){ ?>
<script>
window.alert('DTR log with date: <?php echo $final_logDate; ?> successfully updated with Leave Entry');
window.location='list_leave.php?cw=list_leave'; 
</script>
<?php } else if(isset($_POST['add_travel'])){ ?>
<script>
window.alert('Travel Order successfully added!');
window.location='list_travel_order.php?cw=list_travel'; 
</script>
<?php } ?>

<?php } ?>


<?php
//DELETE LEAVE         DELETE LEAVE           DELETE LEAVE           DELETE LEAVE

if(isset($_POST['deleteLeave'])){

$new_clearance_query = $conn->query("SELECT * FROM leave_applicants WHERE lap_id='$_GET[lap_id]'");
$nc_row = $new_clearance_query->fetch();
 

$conn->query("DELETE FROM personnel_logs WHERE travel_leave_code='$nc_row[leave_code]'"); 

// --- REFUND LEAVE BALANCES ---
$remarks = $nc_row['leave_type'];

// Try to find the exact deduction log for this leave application
$stmt_log = $conn->prepare("SELECT vl_amount, sl_amount, vl_wout_pay, sl_wout_pay FROM leave_credit_logs WHERE personnel_id = ? AND remarks LIKE ? LIMIT 1");
$stmt_log->execute([$nc_row['applicant_id'], "%[Code: $nc_row[leave_code]]%"]);
$log_row = $stmt_log->fetch(PDO::FETCH_ASSOC);

if ($log_row) {
    // Refund based on the exact amounts that were originally deducted (amounts are stored as negative)
    $vl_refund = abs((float)$log_row['vl_amount']);
    $sl_refund = abs((float)$log_row['sl_amount']);
    
    $update = $conn->prepare("UPDATE personnels SET vl_balance = vl_balance + ?, sl_balance = sl_balance + ? WHERE personnel_id = ?");
    $update->execute([$vl_refund, $sl_refund, $nc_row['applicant_id']]);
    
    $logRemarks = "Refunded deleted $remarks";
    $conn->query("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks) VALUES ('$nc_row[applicant_id]', 'Manual Adjustment', '$vl_refund', '$sl_refund', '$logRemarks')");
} else {
    // Fallback if log is not found (for old leaves before this fix)
    if($remarks == 'Vacation Leave' || $remarks == 'Mandatory/Forced Leave') {
        $numDays = (float)$nc_row['numDays'];
        $conn->query("UPDATE personnels SET vl_balance = vl_balance + $numDays WHERE personnel_id = '$nc_row[applicant_id]'");
        $logRemarks = "Refunded deleted $remarks (Fallback)";
        $conn->query("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks) VALUES ('$nc_row[applicant_id]', 'Manual Adjustment', '$numDays', '0', '$logRemarks')");
    } else if($remarks == 'Sick Leave') {
        $numDays = (float)$nc_row['numDays'];
        $conn->query("UPDATE personnels SET sl_balance = sl_balance + $numDays WHERE personnel_id = '$nc_row[applicant_id]'");
        $logRemarks = "Refunded deleted $remarks (Fallback)";
        $conn->query("INSERT INTO leave_credit_logs (personnel_id, log_type, vl_amount, sl_amount, remarks) VALUES ('$nc_row[applicant_id]', 'Manual Adjustment', '0', '$numDays', '$logRemarks')");
    }
}
// -----------------------------

$conn->query("DELETE FROM leave_applicants WHERE lap_id='$_GET[lap_id]'");

$new_clearance_query=null;
$perData1_query=null;
$conn=null;
      
 ?>
 
<script>
window.alert('Leave Entry successfully deleted...');
window.location='list_leave.php?cw=list_leave'; 
</script>

<?php } ?>


<?php
// DELETE TRAVEL
if(isset($_POST['deleteTravel'])){

    $travel_code = $_GET['travel_code'];
    
    // Delete from personnel logs (DTR entries)
    $stmt1 = $conn->prepare("DELETE FROM personnel_logs WHERE travel_leave_code=?");
    $stmt1->execute([$travel_code]);
    
    // Delete from official travel logs
    $stmt2 = $conn->prepare("DELETE FROM personnel_official_travel_logs WHERE travel_code=?");
    $stmt2->execute([$travel_code]);
    
    // Delete from personnel seminars (201 File SR) if added
    $stmt3 = $conn->prepare("DELETE FROM personnel_seminars WHERE entry_type=?");
    $stmt3->execute([$travel_code]);

    $conn=null;
?>
<script>
window.alert('Travel Order successfully deleted...');
window.location='list_travel_order.php?cw=list_travel'; 
</script>
<?php } ?>
