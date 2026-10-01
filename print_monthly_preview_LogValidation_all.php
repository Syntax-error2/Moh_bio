<!DOCTYPE html>
<html> 

<?php

 
include('session.php');  
//error_reporting(0);

 
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
  
           
        
include('header_print.php');

?>

<body>

<?php include('header_print_letterHead.php'); ?>

<hr />

<center>
<h3>MONTHLY LOG VALIDATION REPORT</h3>
<h4><?php echo $mmWords.' '.$selectedYYYY; ?></h4>
</center>
<hr />
<?php

$printAll_Data_query = $conn->query("select * FROM personnels ORDER BY lname, fname ASC");
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
    $LV_query = $conn->query("select * FROM personnel_logs WHERE personnel_id='$printALL_row[personnel_id]' AND logDate='$logDateCtr'");
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
        <td style="border: none; text-align: center;">
        <a href="#" data-toggle="modal" data-target="#zoom_snap<?php echo $LV_row['log_id']; ?>" style="cursor: move;" title="Click to zoom image..."><img src="upload/<?php echo $LV_row['captured_img']; ?>" width="100" height="75" class="img-fluid rounded" /></a>
        </td>
        <td style="border: none; text-align: center;">
        <img src="<?php echo $LV_row['img']; ?>" width="60" height="75" class="img-fluid rounded" />
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
       
            
