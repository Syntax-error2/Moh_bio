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
 
        
        $studDataCHK_query = $conn->query("SELECT * FROM service_record WHERE personnel_id='$_GET[personnel_id]' AND serv_date_from='$_POST[serv_date_from]' AND serv_date_to='$_POST[serv_date_to]'") or die(mysql_error());
        if($studDataCHK_query->rowCount()>0){
         ?>
 
        <script>
        window.alert('Service Record data already exist...');
        window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>    
        
        <?php
        
        }else{
        
        $office_appointment=addslashes($_POST['office_appointment']);
        $roa_designation=addslashes($_POST['roa_designation']);
            
        $conn->query("INSERT INTO service_record(personnel_id, maid_lname, maid_fname, maid_mname, serv_date_from, serv_date_to, roa_designation, roa_status, salary, office_appointment, separate_date, separate_cause)
        VALUES ('$_GET[personnel_id]', '$_POST[maid_lname]', '$_POST[maid_fname]', '$_POST[maid_mname]', '$_POST[serv_date_from]', '$_POST[serv_date_to]', '$roa_designation', '$_POST[roa_status]', '$_POST[salary]', '$office_appointment', '$_POST[separate_date]', '$_POST[separate_cause]')");
        
        ?>
          
        <script>
        window.alert('Service Record added successfully...');
        window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>    
         
        <?php } } ?>
        
        
<?php 

if(isset($_POST['update_servRecord']))
{       
        try {
            $sr_id = $_POST['sr_id'];
            $monthly_salary = isset($_POST['monthly_salary']) ? $_POST['monthly_salary'] : 0;
            $annual_salary = isset($_POST['annual_salary']) ? $_POST['annual_salary'] : 0;
            
            // Update service record
            $update_stmt = $conn->prepare("UPDATE service_record SET
                maid_lname = :maid_lname,
                maid_fname = :maid_fname,
                maid_mname = :maid_mname,
                serv_date_from = :serv_date_from,
                serv_date_to = :serv_date_to,
                roa_designation = :roa_designation,
                roa_status = :roa_status,
                monthly_salary = :monthly_salary,
                annual_salary = :annual_salary,
                office_appointment = :office_appointment,
                separate_date = :separate_date,
                separate_cause = :separate_cause
            WHERE sr_id = :sr_id");
            
            $update_stmt->execute([
                ':maid_lname' => $_POST['maid_lname'],
                ':maid_fname' => $_POST['maid_fname'],
                ':maid_mname' => $_POST['maid_mname'],
                ':serv_date_from' => $_POST['serv_date_from'],
                ':serv_date_to' => $_POST['serv_date_to'],
                ':roa_designation' => $_POST['roa_designation'],
                ':roa_status' => $_POST['roa_status'],
                ':monthly_salary' => $monthly_salary,
                ':annual_salary' => $annual_salary,
                ':office_appointment' => $_POST['office_appointment'],
                ':separate_date' => $_POST['separate_date'],
                ':separate_cause' => $_POST['separate_cause'],
                ':sr_id' => $sr_id
            ]);
            
            // Get personnel_id from service record
            $get_personnel = $conn->prepare("SELECT personnel_id FROM service_record WHERE sr_id = :sr_id");
            $get_personnel->execute([':sr_id' => $sr_id]);
            $personnel_data = $get_personnel->fetch(PDO::FETCH_ASSOC);
            
            if ($personnel_data) {
                // Sync monthly_salary to personnels table
                require_once 'sync_monthly_salary.php';
                $sync = new MonthlySalarySync($conn);
                $sync->syncPersonnel($personnel_data['personnel_id']);
            }
            
        } catch (PDOException $e) {
            error_log("Error updating service record: " . $e->getMessage());
            die("Error updating service record. Please try again.");
        }
        
        ?>
          
        <script>
        window.alert('Service Record updated successfully...');
        window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
        </script>    
         
 
 <?php } ?>
 
<?php

if(isset($_POST['delete_servRecord']))
{
     
    $conn->query("DELETE FROM service_record WHERE sr_id='$_POST[sr_id]'");
 
?>

<script>
window.alert('Service Record deleted successfully...');
window.location='list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>';
</script>   


<?php } ?>

    