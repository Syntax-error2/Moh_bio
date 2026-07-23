<?php

 
include('session.php');
include('dbcon.php');

?>
 
<?php

if(isset($_POST['leave_application']))
{
    $lap_code=$_POST['lap_code'];
    $application_date=date('m/d/Y');
    $leave_type=$_POST['leave_type']; 
    $leave_type_desc=$_POST['leave_type_desc']; 
 
    $lap_insert_stmt = $conn->prepare("INSERT INTO leave_applicants(lap_code, application_date, leave_type, leave_type_desc, applicant_id, do_id, noted_by_id, approved_by_id, status)
    VALUES(:lap_code, :application_date, :leave_type, :leave_type_desc, :applicant_id, :do_id, 0, 0, 'Pending')");
    $lap_insert_stmt->execute([
        ':lap_code' => $lap_code,
        ':application_date' => $application_date,
        ':leave_type' => $leave_type,
        ':leave_type_desc' => $leave_type_desc,
        ':applicant_id' => $user_personnel_id,
        ':do_id' => $user_dept
    ]);
?>

<script>
window.alert('Application for <?php echo $leave_type; ?> successfully added, this will be reviewed by your office and HR head and check this page for application status...');
window.location='list_leave.php?cw=list_leave';
</script>

<?php } ?>


<?php

if(isset($_POST['updateLAP']))
{
    $lap_code=$_POST['lap_code'];
    $application_date=date('m/d/Y');
    $leave_type=$_POST['leave_type']; 
    $leave_type_desc=$_POST['leave_type_desc']; 
 
    $lap_update_stmt = $conn->prepare("UPDATE leave_applicants SET application_date = :application_date, leave_type = :leave_type, leave_type_desc = :leave_type_desc, noted_by_id = 0, approved_by_id = 0, status = 'Pending' WHERE lap_code = :lap_code");
    $lap_update_stmt->execute([
        ':application_date' => $application_date,
        ':leave_type' => $leave_type,
        ':leave_type_desc' => $leave_type_desc,
        ':lap_code' => $lap_code
    ]);

?>

<script>
window.alert('Application for <?php echo $leave_type; ?> successfully updated, this will be reviewed by your office and HR head and check this page for application status...');
window.location='list_leave.php?cw=list_leave';
</script>

<?php } ?>


<?php

if(isset($_POST['updateNotedLAP']))
{
    $lap_code=$_POST['lap_code'];
    $noted_by_id=$_POST['noted_by_id'];
    
        if($noted_by_id>0){
        $noted_stmt = $conn->prepare("UPDATE leave_applicants SET noted_by_id = :noted_by_id WHERE lap_code = :lap_code");
        $noted_stmt->execute([':noted_by_id' => $noted_by_id, ':lap_code' => $lap_code]);
        }else{
        $noted_reset_stmt = $conn->prepare("UPDATE leave_applicants SET noted_by_id = :noted_by_id, approved_by_id = 0, status = 'Pending' WHERE lap_code = :lap_code");
        $noted_reset_stmt->execute([':noted_by_id' => $noted_by_id, ':lap_code' => $lap_code]);
        
        $LAPData_stmt = $conn->prepare("SELECT * FROM leave_applicants WHERE lap_code = :lap_code");
        $LAPData_stmt->execute([':lap_code' => $lap_code]);
        $LAPData_query = $LAPData_stmt;
        $lapdq_row=$LAPData_query->fetch();
        
        $perData_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
        $perData_stmt->execute([':personnel_id' => $lapdq_row['applicant_id']]);
        $perData_query = $perData_stmt;
        $pdq_row=$perData_query->fetch();
        
        $lapNoD_stmt = $conn->prepare("SELECT * FROM lap_dates WHERE lap_code = :lap_code ORDER BY leave_date_mm, leave_date_dd, leave_date_yyyy ASC");
        $lapNoD_stmt->execute([':lap_code' => $lap_code]);
        $lapNoD_query = $lapNoD_stmt;
        while ($lapNoD_row = $lapNoD_query->fetch())
        {
        
        $logDate=$lapNoD_row['leave_date_mm'].'/'.$lapNoD_row['leave_date_dd'].'/'.$lapNoD_row['leave_date_yyyy'];
        //save to student logs
        $delete_leave_log_stmt = $conn->prepare("DELETE FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND remarks = 'On Leave'");
        $delete_leave_log_stmt->execute([':RFTag_id' => $pdq_row['RFTag_id'], ':logDate' => $logDate]);
        
        } 
        
        }
    
    

?>

<script>
window.alert('Dept. / Office Head Level application status updated...');
window.location='list_leave.php?cw=list_leave';
</script>

<?php } ?>


<?php

if(isset($_POST['updateApprovedLAP']))
{
    $lap_code=$_POST['lap_code'];
    $approved_by_id=$_POST['approved_by_id'];
    
    if($approved_by_id>0){
        $approved_stmt = $conn->prepare("UPDATE leave_applicants SET approved_by_id = :approved_by_id, status = 'Approved' WHERE lap_code = :lap_code");
        $approved_stmt->execute([':approved_by_id' => $approved_by_id, ':lap_code' => $lap_code]);
        
        
        $LAPData_stmt = $conn->prepare("SELECT * FROM leave_applicants WHERE lap_code = :lap_code");
        $LAPData_stmt->execute([':lap_code' => $lap_code]);
        $LAPData_query = $LAPData_stmt;
        $lapdq_row=$LAPData_query->fetch();
        
        $perData_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
        $perData_stmt->execute([':personnel_id' => $lapdq_row['applicant_id']]);
        $perData_query = $perData_stmt;
        $pdq_row=$perData_query->fetch();
        
        $lapNoD_stmt = $conn->prepare("SELECT * FROM lap_dates WHERE lap_code = :lap_code ORDER BY leave_date_mm, leave_date_dd, leave_date_yyyy ASC");
        $lapNoD_stmt->execute([':lap_code' => $lap_code]);
        $lapNoD_query = $lapNoD_stmt;
        while ($lapNoD_row = $lapNoD_query->fetch())
        {
        
        $logDate=$lapNoD_row['leave_date_mm'].'/'.$lapNoD_row['leave_date_dd'].'/'.$lapNoD_row['leave_date_yyyy'];
        //save to student logs
        $insert_leave_log_stmt = $conn->prepare("INSERT INTO personnel_logs(RFTag_id, logDate, remarks) VALUES (:RFTag_id, :logDate, 'On Leave')");
        $insert_leave_log_stmt->execute([':RFTag_id' => $pdq_row['RFTag_id'], ':logDate' => $logDate]);
        
        }
        
        
        
        
        //SEND EMAIL
        /* $to = $pdq_row['email'];
        $subject = "BCC - HR [ Leave Application Notification ]";
        
        $message = "
        <html>
        <head>
        <title>BCC - HR Leave Application Notification</title>
        </head>
        <body>
        <p>Leave Application Details</p>
        <table>
        
        <tr>
        <th>Date Applied</th>
        <td>".$lapdq_row['application_date']."</td>
        </tr>
        
        <tr>
        <th>Type of Leave</th>
        <td>".$lapdq_row['leave_type']."</td>
        </tr>
        
        <tr>
        <th>Description</th>
        <td>".$lapdq_row['leave_type_desc']."</td>
        </tr>
        
        <tr>
        <th>Number of days:</th>
        <td>".$lapNoD_query->rowCount()."</td>
        </tr>
        
        <tr>
        <th>Status</th>
        <td><strong>Approved</strong></td>
        </tr>
        
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: Binalbagan Catholic College - HR' . "\r\n";
        $headers .= 'Cc: emiloimagtolis@gmail.com' . "\r\n";
        
        mail($to,$subject,$message,$headers); */
        
        //END SEND EMAIL
        
        
        
    }else{
        
        $pending_stmt = $conn->prepare("UPDATE leave_applicants SET approved_by_id = :approved_by_id, status = 'Pending' WHERE lap_code = :lap_code");
        $pending_stmt->execute([':approved_by_id' => $approved_by_id, ':lap_code' => $lap_code]);
        
        $LAPData_stmt = $conn->prepare("SELECT * FROM leave_applicants WHERE lap_code = :lap_code");
        $LAPData_stmt->execute([':lap_code' => $lap_code]);
        $LAPData_query = $LAPData_stmt;
        $lapdq_row=$LAPData_query->fetch();
        
        $perData_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
        $perData_stmt->execute([':personnel_id' => $lapdq_row['applicant_id']]);
        $perData_query = $perData_stmt;
        $pdq_row=$perData_query->fetch();
        
        $lapNoD_stmt = $conn->prepare("SELECT * FROM lap_dates WHERE lap_code = :lap_code ORDER BY leave_date_mm, leave_date_dd, leave_date_yyyy ASC");
        $lapNoD_stmt->execute([':lap_code' => $lap_code]);
        $lapNoD_query = $lapNoD_stmt;
        while ($lapNoD_row = $lapNoD_query->fetch())
        {
        
        $logDate=$lapNoD_row['leave_date_mm'].'/'.$lapNoD_row['leave_date_dd'].'/'.$lapNoD_row['leave_date_yyyy'];
        //save to student logs
        $delete_leave_log_stmt = $conn->prepare("DELETE FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND remarks = 'On Leave'");
        $delete_leave_log_stmt->execute([':RFTag_id' => $pdq_row['RFTag_id'], ':logDate' => $logDate]);
        
        }
        
        
        //SEND EMAIL
        /* $to = $pdq_row['email'];
        $subject = "BCC - HR [ Leave Application Notification ]";
        
        $message = "
        <html>
        <head>
        <title>BCC - HR Leave Application Notification</title>
        </head>
        <body>
        <p>Leave Application Details</p>
        <table>
        
        <tr>
        <th>Date Applied</th>
        <td>".$lapdq_row['application_date']."</td>
        </tr>
        
        <tr>
        <th>Type of Leave</th>
        <td>".$lapdq_row['leave_type']."</td>
        </tr>
        
        <tr>
        <th>Description</th>
        <td>".$lapdq_row['leave_type_desc']."</td>
        </tr>
        
        <tr>
        <th>Number of days:</th>
        <td>".$lapNoD_query->rowCount()."</td>
        </tr>
        
        <tr>
        <th>Status</th>
        <td><strong>Pending</strong></td>
        </tr>
        
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: Binalbagan Catholic College - HR' . "\r\n";
        $headers .= 'Cc: emiloimagtolis@gmail.com' . "\r\n";
        
        mail($to,$subject,$message,$headers); */
        
        //END SEND EMAIL
        
    }
    
?>

<script>
window.alert('HR Head level application status updated...');
window.location='home.php';
</script>

<?php } ?>

<?php

if(isset($_POST['deleteLAP']))
{   
    $lap_code=$_POST['lap_code']; 
    
    $delete_lap_stmt = $conn->prepare("DELETE FROM leave_applicants WHERE lap_code = :lap_code");
    $delete_lap_stmt->execute([':lap_code' => $lap_code]);
?>

<script>
window.alert('Leave application successfully deleted...');
window.location='list_leave.php?cw=list_leave';
</script>

<?php } ?>


<?php

if(isset($_POST['addLAPDate']))
{
    $lap_code=$_POST['lap_code'];
    
    $leave_date_mm=substr($_POST['leave_date'], 5,2);
    $leave_date_dd=substr($_POST['leave_date'], 8,2);
    $leave_date_yyyy=substr($_POST['leave_date'], 0,4);
 
    $insert_lap_date_stmt = $conn->prepare("INSERT INTO lap_dates(lap_code, leave_date_mm, leave_date_dd, leave_date_yyyy)
    VALUES(:lap_code, :leave_date_mm, :leave_date_dd, :leave_date_yyyy)");
    $insert_lap_date_stmt->execute([
        ':lap_code' => $lap_code,
        ':leave_date_mm' => $leave_date_mm,
        ':leave_date_dd' => $leave_date_dd,
        ':leave_date_yyyy' => $leave_date_yyyy
    ]);
?>

<script>
window.alert('Leave date <?php echo $leave_date_mm.'/'.$leave_date_dd.'/'.$leave_date_yyyy; ?> successfully added...');
window.location='list_leave.php?cw=list_leave';
</script>

<?php } ?>


<?php

if(isset($_POST['updateLAPDate']))
{
    $lap_dates_id=$_POST['lap_dates_id'];
    
    $leave_date_mm=substr($_POST['leave_date'], 5,2);
    $leave_date_dd=substr($_POST['leave_date'], 8,2);
    $leave_date_yyyy=substr($_POST['leave_date'], 0,4);
 
    $update_lap_date_stmt = $conn->prepare("UPDATE lap_dates SET leave_date_mm = :leave_date_mm, leave_date_dd = :leave_date_dd, leave_date_yyyy = :leave_date_yyyy WHERE lap_dates_id = :lap_dates_id");
    $update_lap_date_stmt->execute([
        ':leave_date_mm' => $leave_date_mm,
        ':leave_date_dd' => $leave_date_dd,
        ':leave_date_yyyy' => $leave_date_yyyy,
        ':lap_dates_id' => $lap_dates_id
    ]);
?>

<script>
window.alert('Leave date <?php echo $leave_date_mm.'/'.$leave_date_dd.'/'.$leave_date_yyyy; ?> successfully updated...');
window.location='list_leave.php?cw=list_leave';
</script>

<?php } ?>

 
<?php

if(isset($_POST['deleteLAPDate']))
{   
    $lap_dates_id=$_POST['lap_dates_id'];
    
    $delete_lap_date_stmt = $conn->prepare("DELETE FROM lap_dates WHERE lap_dates_id = :lap_dates_id");
    $delete_lap_date_stmt->execute([':lap_dates_id' => $lap_dates_id]);
?>

<script>
window.alert('Leave date successfully deleted...');
window.location='list_leave.php?cw=list_leave';
</script>

<?php } ?>


<?php

if(isset($_POST['addDept']))
{
    $dept_office_name=addslashes($_POST['dept_office_name']); 
 
    $insert_dept_stmt = $conn->prepare("INSERT INTO dept_offices(dept_office_name) VALUES(:dept_office_name)");
    $insert_dept_stmt->execute([':dept_office_name' => $dept_office_name]);
?>

<script>
window.alert('Department / Office: <?php echo $dept_office_name; ?> successfully added...');
window.location='list_dept.php';
</script>

<?php } ?>



<?php

if(isset($_POST['updateDept']))
{
    
    $do_id=$_POST['do_id'];
    $dept_office_name=addslashes($_POST['dept_office_name']); 
     
    $update_dept_stmt = $conn->prepare("UPDATE dept_offices SET dept_office_name = :dept_office_name WHERE do_id = :do_id");
    $update_dept_stmt->execute([':dept_office_name' => $dept_office_name, ':do_id' => $do_id]);
?>

<script>
window.alert('Department / Office: <?php echo $dept_office_name; ?> successfully updated...');
window.location='list_dept.php';
</script>

<?php } ?>


<?php

if(isset($_POST['deleteDept']))
{   
    $do_id=$_POST['do_id'];
    $dept_office_name=$_POST['dept_office_name'];
    
    $delete_dept_stmt = $conn->prepare("DELETE FROM dept_offices WHERE do_id = :do_id");
    $delete_dept_stmt->execute([':do_id' => $do_id]);
?>

<script>
window.alert('Department / Office: <?php echo $dept_office_name; ?> successfully deleted...');
window.location='list_dept.php';
</script>

<?php } ?>












<?php

if(isset($_POST['addDes']))
{
    $des_name=addslashes($_POST['des_name']); 
 
    $insert_des_stmt = $conn->prepare("INSERT INTO designation(des_name) VALUES(:des_name)");
    $insert_des_stmt->execute([':des_name' => $des_name]);
?>

<script>
window.alert('Designation: <?php echo $des_name; ?> successfully added...');
window.location='list_designation.php';
</script>

<?php } ?>



<?php

if(isset($_POST['updateDes']))
{
    
    $des_id=$_POST['des_id'];
    $des_name=addslashes($_POST['des_name']); 
     
    $update_des_stmt = $conn->prepare("UPDATE designation SET des_name = :des_name WHERE des_id = :des_id");
    $update_des_stmt->execute([':des_name' => $des_name, ':des_id' => $des_id]);
?>

<script>
window.alert('Designation: <?php echo $des_name; ?> successfully updated...');
window.location='list_designation.php';
</script>

<?php } ?>


<?php

if(isset($_POST['deleteDes']))
{   
    $des_id=$_POST['des_id'];
    $des_name=$_POST['des_name']; 
    
    $delete_des_stmt = $conn->prepare("DELETE FROM designation WHERE des_id = :des_id");
    $delete_des_stmt->execute([':des_id' => $des_id]);
?>

<script>
window.alert('Designation: <?php echo $des_name; ?> successfully deleted...');
window.location='list_designation.php';
</script>

<?php } ?>














<?php

if(isset($_POST['addGASS']))
{
     
    $gass_name = intval($_POST['gass_name']); //salary grade
    $ratePerDay = $_POST['ratePerDay'];

    if($gass_name <= 10){
        $level = "First Level";
    }elseif($gass_name <= 24){
        $level = "Second Level";
    }elseif($gass_name <= 38){
        $level = "Executive / Managerial";
    }elseif($gass_name <= 52){
        $level = "Third Level";
    }else{
        $level = "Third Level";
    }

    try {
        $insertGASS = $conn->prepare("INSERT INTO gass(gass_name, level, step, ratePerDay) VALUES
        (:gass_name, :level, 1, :rate1),
        (:gass_name2, :level2, 2, :rate2),
        (:gass_name3, :level3, 3, :rate3),
        (:gass_name4, :level4, 4, :rate4),
        (:gass_name5, :level5, 5, :rate5),
        (:gass_name6, :level6, 6, :rate6),
        (:gass_name7, :level7, 7, :rate7),
        (:gass_name8, :level8, 8, :rate8)");
        $insertGASS->execute([
            ':gass_name' => $gass_name, ':level' => $level, ':rate1' => $ratePerDay,
            ':gass_name2' => $gass_name, ':level2' => $level, ':rate2' => $ratePerDay,
            ':gass_name3' => $gass_name, ':level3' => $level, ':rate3' => $ratePerDay,
            ':gass_name4' => $gass_name, ':level4' => $level, ':rate4' => $ratePerDay,
            ':gass_name5' => $gass_name, ':level5' => $level, ':rate5' => $ratePerDay,
            ':gass_name6' => $gass_name, ':level6' => $level, ':rate6' => $ratePerDay,
            ':gass_name7' => $gass_name, ':level7' => $level, ':rate7' => $ratePerDay,
            ':gass_name8' => $gass_name, ':level8' => $level, ':rate8' => $ratePerDay,
        ]);
?>
<script>
window.alert('Salary Grade <?php echo $gass_name; ?> (<?php echo $level; ?>) with rate <?php echo $ratePerDay; ?> successfully added (Steps 1-8 created)...');
window.location='list_gass.php';
</script>
<?php
    } catch(PDOException $e) {
?>
<script>
window.alert('Error saving Salary Grade: <?php echo addslashes($e->getMessage()); ?>');
history.back();
</script>
<?php
    }
} ?>



<?php

if(isset($_POST['updateGASS']))
{
    
    $gass_id=$_POST['gass_id'];
    
    $gass_name=$_POST['gass_name']; //salary grade
    
    $level = "Third Level";
    if($gass_name<=10){
        $level="First Level";
    }elseif($gass_name<=24 AND $gass_name>10){
        $level="Second Level";
    }elseif($gass_name<=38 AND $gass_name>24){
        $level="Executive / Managerial";
    }elseif($gass_name<=52 AND $gass_name>38){
        $level="Third Level";
    }
    
    $step=$_POST['step'];
    $ratePerDay=$_POST['ratePerDay'];
     
    $update_gass_stmt = $conn->prepare("UPDATE gass SET gass_name = :gass_name, level = :level, step = :step, ratePerDay = :ratePerDay WHERE gass_id = :gass_id");
    $update_gass_stmt->execute([
        ':gass_name' => $gass_name,
        ':level' => $level,
        ':step' => $step,
        ':ratePerDay' => $ratePerDay,
        ':gass_id' => $gass_id
    ]);
?>

<script>
window.alert('Salary Grade: <?php echo $gass_name; ?> | Level: <?php echo $level; ?> | Step: <?php echo $step; ?> | Rate per day: <?php echo $ratePerDay; ?>  successfully updated...');
window.location='list_gass.php';
</script>

<?php } ?>


<?php

if(isset($_POST['deleteGASS']))
{   
    $gass_id=$_POST['gass_id'];
    
    
    $subjK_stmt = $conn->prepare("SELECT * FROM gass WHERE gass_id = :gass_id");
    $subjK_stmt->execute([':gass_id' => $gass_id]);
    $subjK_query = $subjK_stmt;
    $subjK_row = $subjK_query->fetch();
    
    $delete_gass_stmt = $conn->prepare("DELETE FROM gass WHERE gass_id = :gass_id");
    $delete_gass_stmt->execute([':gass_id' => $gass_id]);
?>

<script>
window.alert('Salary Grade: <?php echo $subjK_row['gass_name']; ?> | Level: <?php echo $subjK_row['level']; ?> | Step: <?php echo $subjK_row['step']; ?> | Rate per day: <?php echo $subjK_row['ratePerDay']; ?>  successfully deleted...');
window.location='list_gass.php';
</script>

<?php } ?>
 







<?php

if(isset($_POST['addShift']))
{   
    $do_id=$_POST['do_id'];
    $shift_name=addslashes($_POST['shift_name']); 
    $type=$_POST['type'];
    
    
    $insert_shift_stmt = $conn->prepare("INSERT INTO shifts(do_id, shift_name, type) VALUES(:do_id, :shift_name, :type)");
    $insert_shift_stmt->execute([':do_id' => $do_id, ':shift_name' => $shift_name, ':type' => $type]);
?>

<script>
window.alert('Shift: <?php echo $shift_name; ?> successfully added...');
window.location='list_shift.php';
</script>

<?php } ?>



<?php

if(isset($_POST['updateShift']))
{
    
    $shift_id=$_POST['shift_id'];
    $do_id=$_POST['do_id'];
    $shift_name=addslashes($_POST['shift_name']); 
    $type=$_POST['type'];
    
    $update_shift_stmt = $conn->prepare("UPDATE shifts SET do_id = :do_id, shift_name = :shift_name, type = :type WHERE shift_id = :shift_id");
    $update_shift_stmt->execute([':do_id' => $do_id, ':shift_name' => $shift_name, ':type' => $type, ':shift_id' => $shift_id]);
?>

<script>
window.alert('Shift: <?php echo $shift_name; ?> successfully updated...');
window.location='list_shift.php';
</script>

<?php } ?>


<?php

if(isset($_POST['deleteShift']))
{   
    $shift_id=$_POST['shift_id'];
    $shift_name=addslashes($_POST['shift_name']); 
    
    $delete_shift_stmt = $conn->prepare("DELETE FROM shifts WHERE shift_id = :shift_id");
    $delete_shift_stmt->execute([':shift_id' => $shift_id]);
?>

<script>
window.alert('Shift: <?php echo $shift_name; ?> successfully deleted...');
window.location='list_shift.php';
</script>

<?php } ?>








<?php

if(isset($_POST['addEmpStatus']))
{
    $emp_stat_name=addslashes($_POST['emp_stat_name']); 
    $status=$_POST['status']; 
    $position_class=$_POST['position_class']; 
    
    $insert_emp_status_stmt = $conn->prepare("INSERT INTO emp_status(emp_stat_name, position_class, status) VALUES(:emp_stat_name, :position_class, :status)");
    $insert_emp_status_stmt->execute([':emp_stat_name' => $emp_stat_name, ':position_class' => $position_class, ':status' => $status]);
?>

<script>
window.alert('Appointment status: <?php echo $emp_stat_name; ?> | Class: <?php echo $position_class; ?> | Type: <?php echo $status; ?> successfully added...');
window.location='list_EStatus.php';
</script>

<?php } ?>



<?php

if(isset($_POST['updateEmpStatus']))
{
    
    $empStat_id=$_POST['empStat_id'];
    $emp_stat_name=addslashes($_POST['emp_stat_name']); 
    $status=$_POST['status']; 
    $position_class=$_POST['position_class']; 
    
    $update_emp_status_stmt = $conn->prepare("UPDATE emp_status SET emp_stat_name = :emp_stat_name, position_class = :position_class, status = :status WHERE empStat_id = :empStat_id");
    $update_emp_status_stmt->execute([
        ':emp_stat_name' => $emp_stat_name,
        ':position_class' => $position_class,
        ':status' => $status,
        ':empStat_id' => $empStat_id
    ]);
?>

<script>
window.alert('Appointment status: <?php echo $emp_stat_name; ?> | Class: <?php echo $position_class; ?> | Type: <?php echo $status; ?> successfully updated...');
window.location='list_EStatus.php';
</script>

<?php } ?>


<?php

if(isset($_POST['deleteEmpStatus']))
{   
    $empStat_id=$_POST['empStat_id'];
    
    $subjK_stmt = $conn->prepare("SELECT * FROM emp_status WHERE empStat_id = :empStat_id");
    $subjK_stmt->execute([':empStat_id' => $empStat_id]);
    $subjK_query = $subjK_stmt;
    $subjK_row = $subjK_query->fetch();
                            
    $delete_emp_status_stmt = $conn->prepare("DELETE FROM emp_status WHERE empStat_id = :empStat_id");
    $delete_emp_status_stmt->execute([':empStat_id' => $empStat_id]);
?>

<script>
window.alert('Appointment status: <?php echo $subjK_row['emp_stat_name']; ?> | Class: <?php echo $subjK_row['position_class']; ?> | Type: <?php echo $subjK_row['status']; ?> successfully deleted...');
window.location='list_EStatus.php';
</script>

<?php } ?>