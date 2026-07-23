


<?php if(isset($_POST['print_monthly_dtr'])){ 
    
if($_POST['doc_type']==="CS Form 48"){
    
if($_POST['do_id']==="print_all"){

?>
<script>
    
    window.open('print_monthly_preview_csf48_all.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php

}else{

?>
<script>
    
    window.open('print_monthly_preview_csf48_2.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php

}
 
}elseif($_POST['doc_type']==="CS Form 48 (1-15)"){

if($_POST['do_id']==="print_all"){

?>
<script>
    
    window.open('print_monthly_preview_csf48_1_15_all.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php

}else{

?>
<script>
    
    window.open('print_monthly_preview_csf48_1_15_2.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php

}

}elseif($_POST['doc_type']==="CS Form 48 (16-31)"){
    
if($_POST['do_id']==="print_all"){

?>
<script>
    
    window.open('print_monthly_preview_csf48_16_31_all.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php

}else{

?>
<script>
    
    window.open('print_monthly_preview_csf48_16_31_2.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php

}

}elseif($_POST['doc_type']==="Log Validation History"){
        
        if($_POST['do_id']==="print_all"){
        
        ?>
        <script>
            
            window.open('print_monthly_preview_LogValidation_all.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
            
        </script>
        
        <?php
        
        }else{
        
        ?>
        <script>
            
            window.open('print_monthly_preview_LogValidation_2.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
            
        </script>
        
        <?php
        
        }
        
        
}else{

if($_POST['do_id']==="print_all"){

?>
<script>
    
    window.open('print_monthly_preview_all.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php

}else{

?>
<script>
    
    window.open('print_monthly_preview2.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&doc_type=<?php echo $_POST['doc_type']; ?>&do_id=<?php echo $_POST['do_id']; ?>', '_blank');
    
</script>

<?php } } ?>

<script> window.location='printReports.php'; </script>

<?php } ?>





<?php if(isset($_POST['checkPrintDetailsMonthly_log_validation'])){ ?>
<script>
    
    window.open('print_monthly_preview_LogValidation.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&RFTag_id=<?php echo $_GET['RFTag_id']; ?>', '_blank');
    window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
    
</script>

<?php } ?>



<?php if(isset($_POST['checkPrintDetailsMonthly_travel_order'])){ ?>
<script>
    
    window.open('print_TO_monthly_preview.php?dateFrom=<?php echo $_POST['dateFrom']; ?>', '_blank');
    window.location='list_travel_order.php?cw=list_travel';
    
</script>

<?php } ?>


<?php if(isset($_POST['checkPrintDetailsMonthly'])){ ?>
<script>
    
    window.open('print_monthly_preview.php?RFTag_id=<?php echo $_POST['RFTag_id']; ?>&dateFrom=<?php echo $_POST['dateFrom']; ?>&dateTo=<?php echo isset($_POST['dateTo']) ? $_POST['dateTo'] : ''; ?>', '_blank');
    window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
    
</script>

<?php } ?>



<?php if(isset($_POST['checkPrintDetailsMonthly_csf48'])){
    
if($_POST['doc_type']==="CS Form 48 (1-15)"){
?>
<script>
    
    window.open('print_monthly_preview_csf48_1_15.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&RFTag_id=<?php echo $_POST['RFTag_id']; ?>', '_blank');
    window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
    
</script>

<?php
}elseif($_POST['doc_type']==="CS Form 48 (16-31)"){
?>
<script>
    
    window.open('print_monthly_preview_csf48_16_31.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&RFTag_id=<?php echo $_POST['RFTag_id']; ?>', '_blank');
    window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
    
</script>

<?php
}elseif($_POST['doc_type']==="CS Form 48"){ ?>

<script>
    
    window.open('print_monthly_preview_csf48.php?RFTag_id=<?php echo $_POST['RFTag_id']; ?>&dateFrom=<?php echo $_POST['dateFrom']; ?>', '_blank');
    window.location='list_personnel.php?dept=<?php echo $_GET['dept']; ?>';
    
</script>

<?php } } ?>




<?php if(isset($_POST['checkPrintDetails'])){
    
    if($_POST['class']=="ALL")
    { if($_POST['dateFrom']==$_POST['dateTo'])
      {
    ?>
    <script>
    
    window.open('print_preview_all.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&dateTo=<?php echo $_POST['dateTo']; ?>&class_id=ALL', '_blank');
     
    </script>
    <?php
      }else{
        
      ?>
      
    <script>
    window.open('print_preview2_all.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&dateTo=<?php echo $_POST['dateTo']; ?>&class_id=<?php echo $_POST['class']; ?>', '_blank');
    </script>
    
    <?php } ?>
    
    
    
    <?php }else{ 
        
    if($_POST['dateFrom']==$_POST['dateTo'])
      {
    ?>
    <script>
    window.open('print_preview.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&dateTo=<?php echo $_POST['dateTo']; ?>&class_id=<?php echo $_POST['class']; ?>', '_blank');
    </script>
    <?php
      }else{
        
      ?>
      
    <script>
    window.open('print_preview2.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&dateTo=<?php echo $_POST['dateTo']; ?>&class_id=<?php echo $_POST['class']; ?>', '_blank');
    </script>
    
    <?php } } ?>
    
    <script>
     
    window.location='printReports.php';
    
    </script>
    
<?php } ?>