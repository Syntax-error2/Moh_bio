<!DOCTYPE html>
<html>

<?php 
include('session.php');  
include('dbcon.php');
//error_reporting(0);

 
  $selectedMM=substr($_GET['dateFrom'], 0,2);
  $selectedDD=substr($_GET['dateFrom'], 3,2);
  $selectedYYYY=substr($_GET['dateFrom'], 6,4);


  
  
                 
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
<h3>DAILY LOG VALIDATION REPORT</h3>
<h4><?php echo $mmWords.' '.$selectedDD.', '.$selectedYYYY; ?></h4>
</center>

<hr />
 
 
<div class="col-lg-12">

    <div class="row">
    
    <?php
    $dateFrom = $_GET['dateFrom'] ?? '';
    $LV_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE logDate = :dateFrom ORDER BY log_id DESC");
    $LV_stmt->execute([':dateFrom' => $dateFrom]);
    $LV_query = $LV_stmt;
    while($LV_row=$LV_query->fetch()){
    
    $personnel_stmt = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id");
    $personnel_stmt->execute([':RFTag_id' => $LV_row['RFTag_id']]);
    $printAll_Data_query = $personnel_stmt;
    $printALL_row=$printAll_Data_query->fetch();
                        
                        
                        if($printALL_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            
                            if($printALL_row['suffix']=='-') { $suffix=''; }else{ $suffix=$printALL_row['suffix'].' '; }
                            
                            $finalMName=$suffix.substr($printALL_row['mname'], 0, 1).'.';
                        }
                        
    ?>
    
    
        <div class="col-md-3" style="border: 1px dotted blue; margin: 2px 2px 2px 2px; padding: 2px 4px 2px 4px;">
        <table style="border: none; width: 98%;">
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
        <strong>Fullname: </strong><?php echo $printALL_row['lname'].", ".$printALL_row['fname']." ".$finalMName; ?><br />
        <?php echo $LV_row['logTime']; ?> ( <?php echo $LV_row['logFlow']; ?> )
        </small>
        </td>
        </tr>
        </table>
               
        </div>
    
    
    <?php } ?>
    
    </div>
    
</div>

<?php include('footer_print.php'); ?>

</body>
</html>
       
            