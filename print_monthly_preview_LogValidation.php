<!DOCTYPE html>
<html> 

<?php

 
include('session.php');  
//error_reporting(0);

include('dbcon.php');

  $get_RFTag_id=$_GET['RFTag_id'];
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
  $grandTotalTRHr=0;
  $grandTotalTRMin=0;
  $grandTotalpmLateMin=0;
  $grandTotalamLateMin=0;
  
  
                 
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
  
            $classData="<strong>Class - Type:</strong> ";
        
include('header_print.php');

?>
 

<body>

 
<?php

$studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id");
$studData_stmt->execute([':RFTag_id' => $get_RFTag_id]);
$studData_query = $studData_stmt;
$studData_row=$studData_query->fetch();


                        if($studData_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            
                            if($studData_row['suffix']=='-') { $suffix=''; }else{ $suffix=$studData_row['suffix'].' '; }
                            
                            $finalMName=$suffix.$studData_row['mname'];
                        }
                        
?>
 
<table style="width: 100%;">
<tr>
<td align="left" style="width: 100%; border: none;">
<?php include('header_print_letterHead.php'); ?>
</td>
 
</tr>
</table>

<hr />

<center>
<h3>MONTHLY LOG VALIDATION REPORT</h3>
<h4><?php echo $studData_row['lname'].", ".$studData_row['fname']." ".$finalMName; ?></h4>
</center>
<hr />
 

<div class="col-lg-12">
    <div class="row">
    <?php
 
    $RFTag_id=$studData_row['RFTag_id'];
 
    $amPresentCtr=0;
    $pmPresentCtr=0;
    
    $amLateCtr=0;
    $pmLateCtr=0;
    
    $amAbsentCtr=0;
    $pmAbsentCtr=0;
    
    
    for($d=1; $d<$MMmaxDay; $d++){
        
        
        
        if($d<10){
            
        $logDateCtr=$selectedMM.'/0'.$d.'/'.$selectedYYYY;
        }else{
        $logDateCtr=$selectedMM.'/'.$d.'/'.$selectedYYYY;
        }
        
 
    ?>
    
    <?php
    
    $LV_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
    $LV_stmt->execute([':RFTag_id' => $get_RFTag_id, ':logDate' => $logDateCtr]);
    $LV_query = $LV_stmt;
    while($LV_row=$LV_query->fetch()){ ?>
    
    
        <div class="col-md-3">
        <table style="border: none;">
        <tr>
        <td style="border: none;">
        <center>
        <a href="#" data-toggle="modal" data-target="#zoom_snap<?php echo $LV_row['log_id']; ?>" style="cursor: move;" title="Click to zoom image..."><img src="upload/<?php echo $LV_row['captured_img']; ?>" width="100" height="75" class="img-fluid rounded" /></a>
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
        
        <strong>Log Time: </strong><?php echo $logDateCtr.' | '.$LV_row['logTime']; ?> ( <?php echo $LV_row['logFlow']; ?> )
        </small>
        </td>
        </tr>
        </table>
        <hr />            
        </div>
    
    
    <?php } } ?>
    </div>
</div>

<?php include('footer_print.php'); ?>
 
</body>
</html>
       
            