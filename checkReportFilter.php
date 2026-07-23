
    <?php if(isset($_POST['print_filter_emp_status'])){ ?>
    
    <script>
    window.open('printPersonnelEmpStatusData.php?empStat_id=<?php echo $_GET['empStat_id']; ?>&print_output=<?php echo $_POST['print_output']; ?>', '_blank');
    window.location='home.php';  
    </script>
    
    <?php } ?>
    
    
    
    <?php if(isset($_POST['print_filter_byAge'])){ ?>
    
    <script>
    window.open('printPersonnelAgeData.php?ageFrom=<?php echo $_GET['ageFrom']; ?>&ageTo=<?php echo $_GET['ageTo']; ?>&print_output=<?php echo $_POST['print_output']; ?>', '_blank');
    window.location='printReports_byAge.php?crw=AGE';  
    </script>
    
    <?php } ?>
    
    
    <?php if(isset($_POST['print_filter_byEduc'])){ ?>
    
    <script>
    window.open('printPersonnelEducationData.php?degree=<?php echo urlencode($_GET['degree']); ?>&school_name=<?php echo urlencode($_GET['school_name']); ?>&empStat_id=<?php echo urlencode($_GET['empStat_id']); ?>&print_output=<?php echo urlencode($_POST['print_output']); ?>', '_blank');
    window.location='printReports_byEduc.php?crw=EDUCATION';  
    </script>
    
    <?php } ?>
    
    
    <?php if(isset($_POST['print_filter_byService'])){ ?>
    
    <script>
    window.open('printPersonnelNumYearsData.php?yearsFrom=<?php echo urlencode($_GET['yearsFrom']); ?>&yearsTo=<?php echo urlencode($_GET['yearsTo']); ?>&empStat_id=<?php echo urlencode($_GET['empStat_id']); ?>&print_output=<?php echo urlencode($_POST['print_output']); ?>', '_blank');
    window.location='printReports_byService.php?crw=SERVICE';  
    </script>
    
    <?php } ?>
    
 