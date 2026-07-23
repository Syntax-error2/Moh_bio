
    <?php if(isset($_POST['print_filter_emp_status'])){ ?>
    
    <script>
    window.open('printPersonnelEmpStatusData.php?empStat_id=<?php echo $_GET['empStat_id']; ?>&print_output=<?php echo $_POST['print_output']; ?>', '_blank');
    window.location='home.php';  
    </script>
    
    <?php } ?>
    
    
    
    <?php if(isset($_POST['print_filter_byAge'])){ ?>
    
    <script>
    window.open('printPersonnelAgeData.php?ageFrom=<?php echo $_GET['ageFrom']; ?>&ageTo=<?php echo $_GET['ageTo']; ?>&empStat_id=<?php echo $_GET['empStat_id']; ?>&print_output=<?php echo $_POST['print_output']; ?>', '_blank');
    window.location='printReports_byAge.php?crw=AGE';  
    </script>
    
    <?php } ?>
    
    
    <?php if(isset($_POST['print_filter_byEduc'])){ ?>
    
    <script>
    window.open('printPersonnelEducationData.php?degree=<?php echo $_GET['degree']; ?>&school_name=<?php echo $_GET['school_name']; ?>&print_output=<?php echo $_POST['print_output']; ?>', '_blank');
    window.location='printReports_byEduc.php?crw=EDUCATION';  
    </script>
    
    <?php } ?>
    
 
    <?php
    
    if(isset($_POST['print_general_reports'])){
        
    $do_id = $_POST['do_id'];
    $empStat_id = $_POST['empStat_id'];
    $sex = $_POST['sex'];
    
    ?>
    
    <script>
    window.open('print_general_reports.php?do_id=<?php echo $do_id; ?>&empStat_id=<?php echo $empStat_id; ?>&sex=<?php echo $sex; ?>', '_blank');
    window.location='home.php';  
    </script>
    
    <?php } ?>
 
 