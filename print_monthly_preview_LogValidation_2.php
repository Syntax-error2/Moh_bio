<!DOCTYPE html>
<html> 

<?php

 
include('session.php');  
//error_reporting(0);

include('dbcon.php');

 
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
 
  
  
                 
                if($selectedMM=="01")
                {
                    
                    $mmWords="January";
                    $MMmaxDay=32;
                }
                
                if($selectedMM=="02")
                {
                    $mmWords="February";
                    
                    $leap = date('L', mktime(0, 0, 0, 1, 1, $selectedYYYY));
            
                    if($leap==0)
                    {
                    $MMmaxDay=29;    
                    }else{
                    $MMmaxDay=30;        
                    }
                    
                }
                
                
                if($selectedMM=="03")
                {
                    $mmWords="March";
                    $MMmaxDay=32;    
                }
                
                
                if($selectedMM=="04")
                {
                    $mmWords="April";
                    $MMmaxDay=31;    
                }
                
                
                if($selectedMM=="05")
                {
                    $mmWords="May";
                    $MMmaxDay=32;  

                }
                
                
                if($selectedMM=="06")
                {
                    $mmWords="June";
                    $MMmaxDay=31;
                }
                
                
                
                if($selectedMM=="07")
                {
                    $mmWords="July";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="08")
                {
                    $mmWords="August";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="09")
                {
                    $mmWords="September";
                    $MMmaxDay=31;
                }
                
                
                if($selectedMM=="10")
                {
                    $mmWords="October";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="11")
                {
                    $mmWords="November";
                    $MMmaxDay=31;
                }
                
                
                if($selectedMM=="12")
                {
                    $mmWords="December";
                    $MMmaxDay=32;
                }
                
                $dept_id = $_GET['do_id'] ?? '';
                $emp_stat_stmt = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
                $emp_stat_stmt->execute([':do_id' => $dept_id]);
                $es_row=$emp_stat_stmt->fetch();
        
include('header_print.php');

?>
 
<body>

<?php include('header_print_letterHead.php'); ?>
<hr />
<center>
<h3>MONTHLY LOG VALIDATION REPORT</h3>
<h4><?php echo $es_row['dept_office_name'].' | '.$mmWords.' '.$selectedYYYY; ?></h4>
</center>
<hr />
<?php

$printAll_Data_stmt = $conn->prepare("SELECT * FROM personnels WHERE do_id = :do_id ORDER BY lname, fname ASC");
$printAll_Data_stmt->execute([':do_id' => $dept_id]);
$printAll_Data_query = $printAll_Data_stmt;
while($printALL_row=$printAll_Data_query->fetch()){
 

?>

    <?php
 
    
    for($d=1; $d<$MMmaxDay; $d++){
        
        
        
        if($d<10){
            
        $logDateCtr=$selectedMM.'/0'.$d.'/'.$selectedYYYY;
        }else{
        $logDateCtr=$selectedMM.'/'.$d.'/'.$selectedYYYY;
        }
        
 
    ?>
<div class="col-lg-12">
    <div class="row">
    
    <?php
    $LV_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
    $LV_stmt->execute([':RFTag_id' => $printALL_row['RFTag_id'], ':logDate' => $logDateCtr]);
    $LV_query = $LV_stmt;
    while($LV_row=$LV_query->fetch()){
    
    if($LV_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            
                            if($LV_row['suffix']=='-') { $suffix=''; }else{ $suffix=$LV_row['suffix'].' '; }
                            
                            $finalMName=$suffix.substr($LV_row['mname'], 0, 1).'.';
                        }
                        
    ?>
    
    
        <div class="col-md-3">
        <table style="border: none;">
        <tr>
        <td style="border: none;">
        <center>
        <img src="upload/<?php echo $LV_row['captured_img']; ?>" width="100" height="75" class="img-fluid rounded" />
        </center>
        </td>
        <td style="border: none;">
        <center>
        <img src="<?php echo $LV_row['img']; ?>" width="60" height="75" class="img-fluid rounded" />
        </center>
        </td>
        </tr>
        
        <tr>
        <td colspan="2" style="border: none;">
        <small>
        <strong>Fullname: </strong><?php echo $LV_row['lname'].", ".$LV_row['fname']." ".$finalMName; ?><br />
        <?php echo $logDateCtr.' | '.$LV_row['logTime']; ?> ( <?php echo $LV_row['logFlow']; ?> )
        </small>
        </td>
        </tr>
        </table>
        <hr />            
        </div>
    
    
    <?php } } ?>
    </div>
</div>
<?php } ?>

<?php include('footer_print.php'); ?>

</body>
</html>
       
            