<?php

include('session.php');

$currentDate=date('m/d/Y');
$logTime=date('h:i:s A');
$dateTimeSave=$currentDate.' | '.$logTime;

$blank=''; ?>


<?php 
//EMPLOYEE FAMILY BG
if(isset($_POST['save_add_fam_bg']))
{   
        $conn->query("INSERT INTO personnel_fam_bg (personnel_id, fullname, sex, relationship, contact_num)VALUES('$_GET[personnel_id]', '$_POST[fullname]', '$_POST[sex]', '$_POST[relationship]', '$_POST[contact_num]')");
        
        ?>
          
        <script>
        window.alert('Family member added successfully...');
        window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>    
         
 
 <?php } ?>

<?php 
//UPDATE FAMILY BG
if(isset($_POST['update_fam_bg']))
{   
        $conn->query("UPDATE personnel_fam_bg SET fullname='$_POST[fullname]', sex='$_POST[sex]', relationship='$_POST[relationship]', contact_num='$_POST[contact_num]' WHERE fm_id='$_POST[fm_id]'");
        
        ?>
          
        <script>
        window.alert('Family member updated successfully...');
        window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>    
         
 
 <?php } ?>

<?php
//DELETE FAMILY BG
if(isset($_POST['delete_fam_bg']))
{
     
    $conn->query("DELETE FROM personnel_fam_bg WHERE fm_id='$_POST[fm_id]'");
 
?>

        <script>
        window.alert('Family member deleted successfully...');
        window.location='list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>   


<?php } ?>




<?php 
//EMPLOYEE EDUCATIONAL BG
if(isset($_POST['update_educ_bg']))
{   
        $conn->query("UPDATE personnel_educ_bg SET degree='$_POST[degree]', course_details='$_POST[course_details]', units='$_POST[units]', year_grad='$_POST[year_grad]', school_name='$_POST[school_name]' WHERE eb_id='$_POST[eb_id]'");
        
        ?>
          
        <script>
        window.alert('Educational attainment updated successfully...');
        window.location='list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>    
         
 
 <?php } ?>
        


<?php

if(isset($_POST['delete_educ_bg']))
{
     
    $conn->query("DELETE FROM personnel_educ_bg WHERE eb_id='$_POST[eb_id]'");
 
?>

<script>
window.alert('Educational attainment deleted successfully...');
window.location='list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
</script>   


<?php } ?>






<?php 
//EMPLOYEE SEMINARS
if(isset($_POST['update_seminar']))
{       
        $dateFrom=substr($_POST['sem_date_from'], 5,2).'/'.substr($_POST['sem_date_from'], 8,2).'/'.substr($_POST['sem_date_from'], 0,4);
        $dateTo=substr($_POST['sem_date_to'], 5,2).'/'.substr($_POST['sem_date_to'], 8,2).'/'.substr($_POST['sem_date_to'], 0,4);
         
        
        $conn->query("UPDATE personnel_seminars SET seminar_title='$_POST[purpose_title]', seminar_desc='$_POST[description]', seminar_venue='$_POST[location_venue]', event_date='$dateFrom', event_date_to='$dateTo' WHERE ps_id='$_POST[ps_id]'");
        
        ?>
          
        <script>
        window.alert('Seminar updated successfully...');
        window.location='list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>    
         
 
 <?php } ?>
        


<?php

if(isset($_POST['delete_seminar']))
{
     
    $conn->query("DELETE FROM personnel_seminars WHERE ps_id='$_POST[ps_id]'");
 
?>

<script>
window.alert('Seminar deleted successfully...');
window.location='list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
</script>   


<?php } ?>




<?php
//EMPLOYEE SERVICE RECORD

//ADD SERVICE RECORDS
if(isset($_POST['add_servRecord']))
{
    $personnel_id = !empty($_GET['personnel_id']) ? $_GET['personnel_id'] : ($_POST['personnel_id'] ?? '');
    $dept = $_GET['dept'] ?? '';

    $maid_lname = trim($_POST['maid_lname'] ?? '');
    $maid_fname = trim($_POST['maid_fname'] ?? '');
    $maid_mname = trim($_POST['maid_mname'] ?? '');
    $serv_date_from = trim($_POST['serv_date_from'] ?? '');
    $serv_date_to = trim($_POST['serv_date_to'] ?? '');
    $roa_designation = trim($_POST['roa_designation'] ?? '');
    $roa_status = trim($_POST['roa_status'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $office_appointment = trim($_POST['office_appointment'] ?? '');
    $separate_date = trim($_POST['separate_date'] ?? '');
    $separate_cause = trim($_POST['separate_cause'] ?? '');
        
    $studDataCHK_query = $conn->prepare("SELECT sr_id FROM service_record WHERE personnel_id = ? AND serv_date_from = ? AND serv_date_to = ?");
    $studDataCHK_query->execute([$personnel_id, $serv_date_from, $serv_date_to]);
    if($studDataCHK_query->rowCount() > 0){
     ?>

    <script>
    window.alert('Service Record data already exist...');
    window.location='list_personnel_individual_details_SR.php?dept=<?php echo htmlspecialchars($dept); ?>&personnel_id=<?php echo htmlspecialchars($personnel_id); ?>';
    </script>    
    
    <?php
    
    }else{
        
    $ins_stmt = $conn->prepare("INSERT INTO service_record(personnel_id, maid_lname, maid_fname, maid_mname, appointDate_status, serv_date_from, serv_date_to, roa_designation, roa_status, salary, office_appointment, separate_date, separate_cause)
    VALUES (?, ?, ?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?)");
    $ins_stmt->execute([
        $personnel_id,
        $maid_lname,
        $maid_fname,
        $maid_mname,
        $serv_date_from,
        $serv_date_to,
        $roa_designation,
        $roa_status,
        $salary,
        $office_appointment,
        $separate_date,
        $separate_cause
    ]);
    
    ?>
      
    <script>
    window.alert('Service Record added successfully...');
    window.location='list_personnel_individual_details_SR.php?dept=<?php echo htmlspecialchars($dept); ?>&personnel_id=<?php echo htmlspecialchars($personnel_id); ?>';
    </script>    
     
    <?php } } ?>
    
    
<?php 

if(isset($_POST['update_servRecord']))
{       
    $dept = $_GET['dept'] ?? '';
    $personnel_id = $_GET['personnel_id'] ?? ($_POST['personnel_id'] ?? '');
    $sr_id = $_POST['sr_id'] ?? '';

    $maid_lname = trim($_POST['maid_lname'] ?? '');
    $maid_fname = trim($_POST['maid_fname'] ?? '');
    $maid_mname = trim($_POST['maid_mname'] ?? '');
    $serv_date_from = trim($_POST['serv_date_from'] ?? '');
    $serv_date_to = trim($_POST['serv_date_to'] ?? '');
    $roa_designation = trim($_POST['roa_designation'] ?? '');
    $roa_status = trim($_POST['roa_status'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $office_appointment = trim($_POST['office_appointment'] ?? '');
    $separate_date = trim($_POST['separate_date'] ?? '');
    $separate_cause = trim($_POST['separate_cause'] ?? '');
    
    $upd_stmt = $conn->prepare("UPDATE service_record SET
        maid_lname = ?,
        maid_fname = ?,
        maid_mname = ?,
        serv_date_from = ?,
        serv_date_to = ?,
        roa_designation = ?,
        roa_status = ?,
        salary = ?,
        office_appointment = ?,
        separate_date = ?,
        separate_cause = ?
        WHERE sr_id = ?");
    $upd_stmt->execute([
        $maid_lname,
        $maid_fname,
        $maid_mname,
        $serv_date_from,
        $serv_date_to,
        $roa_designation,
        $roa_status,
        $salary,
        $office_appointment,
        $separate_date,
        $separate_cause,
        $sr_id
    ]);

    ?>
      
    <script>
    window.alert('Service Record updated successfully...');
    window.location='list_personnel_individual_details_SR.php?dept=<?php echo htmlspecialchars($dept); ?>&personnel_id=<?php echo htmlspecialchars($personnel_id); ?>';
    </script>    
     

 <?php } ?>
 
<?php

if(isset($_POST['delete_servRecord']))
{
    $dept = $_GET['dept'] ?? '';
    $personnel_id = $_GET['personnel_id'] ?? ($_POST['personnel_id'] ?? '');
    $sr_id = $_POST['sr_id'] ?? '';
     
    $del_stmt = $conn->prepare("DELETE FROM service_record WHERE sr_id = ?");
    $del_stmt->execute([$sr_id]);
 
?>

<script>
window.alert('Service Record deleted successfully...');
window.location='list_personnel_individual_details_SR.php?dept=<?php echo htmlspecialchars($dept); ?>&personnel_id=<?php echo htmlspecialchars($personnel_id); ?>';
</script>   


<?php } ?>


    