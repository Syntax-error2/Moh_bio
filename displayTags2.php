
<?php

include('dbcon.php');

$dpCtr=0;

$currentDateDisplay=date('Y-m-d');
$bdChecker=date('m-d');

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


if(get_client_ip()=="::1")
{
  $machine_ip=gethostbyname(trim(`hostname`));  
}else{
  $machine_ip=get_client_ip();
}


    
$tr2_img="";
$tr2_gl="";
$tr2_logTime="";
$cf2="";

$tr3_img="";
$tr3_gl="";
$tr3_logTime="";
$cf3="";

$tr4_img="";
$tr4_gl="";
$tr4_logTime="";
$cf4="";
 
$currentDateDisplayMdy = date('m/d/Y');
$displayLog_query = $conn->query("SELECT * FROM personnel_logs WHERE (logDate='$currentDateDisplay' OR logDate='$currentDateDisplayMdy') AND client_ip='$machine_ip' AND remarks='' ORDER BY log_id DESC");

$cc_query = $conn->query("select * from client_computer WHERE ipAddress='$machine_ip'");
$cc_row = $cc_query->fetch();

if($cc_row['display_time']==15){

while($dpLog_row=$displayLog_query->fetch())
{
 
 
    if(($dpLog_row['logTime']==='11:59 PM' AND $dpLog_row['logFlow']==='PM OUT') OR ($dpLog_row['logTime']==='12:00 AM' AND $dpLog_row['logFlow']==='AM IN')){
        
    }else{
        
    $dpCtr+=1;

    if($dpCtr==1)
    {
 
        $tr2_img=$dpLog_row['img'];
        
        
        if($dpLog_row['suffix']=="-")
        {
            $tr2_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr2_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
 
        //$tr2_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr2_gl="";
        
        
        $cf2=$dpLog_row['logFlow'];
        $tr2_logTime=$dpLog_row['logTime'];
        
        $studData_query = $conn->query("SELECT * FROM personnels WHERE lname='$dpLog_row[lname]' AND fname='$dpLog_row[fname]' AND mname='$dpLog_row[mname]'");
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'-'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting2='Display';
        }else{
            $bdayGreeting2='N/A';
        }
    }
    elseif($dpCtr==2)
    {

        $tr3_img=$dpLog_row['img'];
        
        
        if($dpLog_row['suffix']=="-")
        {
            $tr3_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr3_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
        //$tr3_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr3_gl="";
        
        
        $cf3=$dpLog_row['logFlow'];
        $tr3_logTime=$dpLog_row['logTime'];
        
        $studData_query = $conn->query("SELECT * FROM personnels WHERE lname='$dpLog_row[lname]' AND fname='$dpLog_row[fname]' AND mname='$dpLog_row[mname]'");
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'-'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting3='Display';
        }else{
            $bdayGreeting3='N/A';
        }
    }
    elseif($dpCtr==3)
    {
        
        $tr4_img=$dpLog_row['img'];
        
        
        if($dpLog_row['suffix']=="-")
        {
            $tr4_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr4_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
        //$tr4_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr4_gl="";
        
        $cf4=$dpLog_row['logFlow'];
        $tr4_logTime=$dpLog_row['logTime'];

        $studData_query = $conn->query("SELECT * FROM personnels WHERE lname='$dpLog_row[lname]' AND fname='$dpLog_row[fname]' AND mname='$dpLog_row[mname]'");
        $sd_row=$studData_query->fetch();
        
        if(!empty($sd_row)){
            $bday=$sd_row['bdMM'].'-'.$sd_row['bdDD'];
            if($bdChecker==$bday)
            {
                $bdayGreeting4='Display';
            }else{
                $bdayGreeting4='N/A';
            }
        }else{
            $bdayGreeting4='N/A';
        }
        
    }
 
     
} }

}else{

while($dpLog_row=$displayLog_query->fetch())
{
    
    if(($dpLog_row['logTime']==='11:59 PM' AND $dpLog_row['logFlow']==='PM OUT') OR ($dpLog_row['logTime']==='12:00 AM' AND $dpLog_row['logFlow']==='AM IN')){
        
    }else{
        
    $dpCtr+=1;

    if($dpCtr==2)
    {
 
        $tr2_img=$dpLog_row['img'];
        
        
        if($dpLog_row['suffix']=="-")
        {
            $tr2_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr2_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
        //$tr2_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr2_gl="";
        $cf2=$dpLog_row['logFlow'];
        $tr2_logTime=$dpLog_row['logTime'];
        
        $studData_query = $conn->query("SELECT * FROM personnels WHERE lname='$dpLog_row[lname]' AND fname='$dpLog_row[fname]' AND mname='$dpLog_row[mname]'");
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'-'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting2='Display';
        }else{
            $bdayGreeting2='N/A';
        }
    }
    elseif($dpCtr==3)
    {

        $tr3_img=$dpLog_row['img'];
        
        
        if($dpLog_row['suffix']=="-")
        {
            $tr3_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr3_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
        //$tr3_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr3_gl="";
        
        
        $cf3=$dpLog_row['logFlow'];
        $tr3_logTime=$dpLog_row['logTime'];
        $studData_query = $conn->query("SELECT * FROM personnels WHERE lname='$dpLog_row[lname]' AND fname='$dpLog_row[fname]' AND mname='$dpLog_row[mname]'");
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'-'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting3='Display';
        }else{
            $bdayGreeting3='N/A';
        }
        
    }
    elseif($dpCtr==4)
    {
        
        $tr4_img=$dpLog_row['img'];
        
        
        if($dpLog_row['suffix']=="-")
        {
            $tr4_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr4_name=$dpLog_row['lname'].", ".$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
        
        //$tr4_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr4_gl="";
        
        
        $cf4=$dpLog_row['logFlow'];
        $tr4_logTime=$dpLog_row['logTime'];

        $studData_query = $conn->query("SELECT * FROM personnels WHERE lname='$dpLog_row[lname]' AND fname='$dpLog_row[fname]' AND mname='$dpLog_row[mname]'");
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'-'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting4='Display';
        }else{
            $bdayGreeting4='N/A';
        }
        
    }
 
     
}

} }

 
?>




<!--  display 2 -->


<!-- start 2nd --> 
<div style="height: 23.33%; width: 20%; background-color: transparent; float: left; margin-left: 5%;">

  
<?php
if($tr2_img==""){ ?>
 
   
   
<?php }else{ ?>
<div class="polaroid2">
  <img src="<?php echo $tr2_img; ?>" alt="second to the last tapped" style="width:100%; height:100%; border: solid white 1px;">
</div>  
<?php } ?>

</div>


<div style="height: 23.33%; width: 73%; background-color: transparent; float: right; margin: 2% 0% 0% 0%">
<?php
if($tr2_img==""){ ?>
     
<?php }else{ ?>
    
    
    <?php if($bdayGreeting2=='Display'){ ?>
    <span style="font-size: medium; background-color: white; padding: 2px 4px 0px 4px;"><strong><i class="fa fa-birthday-cake"></i> Happy Birthday!</strong></span><br />
    <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr2_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr2_gl; ?></span></p>
    <p align="right" style="background-color: #008aff; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px;"><strong style="float: left">&nbsp;<?php echo $cf2; ?></strong><strong><?php echo $tr2_logTime; ?></strong></p>
    <?php }else{ ?>
    <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr2_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr2_gl; ?></span></p>
    <br />
    <p align="right" style="background-color: #008aff; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px;"><strong style="float: left">&nbsp;<?php echo $cf2; ?></strong><strong><?php echo $tr2_logTime; ?></strong></p>
    <?php } ?>
    
    
    
    
  
<?php } ?>
</div>
<!-- end 2nd -->


<!-- start 3rd --> 
<div style="height: 23.33%; width: 20%; background-color: transparent; float: left; margin-left: 5%;">

  
<?php
if($tr3_img==""){ ?>
 
   
   
<?php }else{ ?>
<div class="polaroid2">
  <img src="<?php echo $tr3_img; ?>" alt="second to the last tapped" style="width:100%; height:100%; border: solid white 1px;">
</div>  
<?php } ?>


</div>


<div style="height: 23.33%; width: 73%; background-color: transparent; float: right; margin: 0% 0% 0% 0%">
<?php
if($tr3_img==""){ ?>
     
<?php }else{ ?>

    <?php if($bdayGreeting3=='Display'){ ?>
    <span style="font-size: medium; background-color: white; padding: 2px 4px 0px 4px;"><strong><i class="fa fa-birthday-cake"></i> Happy Birthday!</strong></span><br />
    <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr3_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr3_gl; ?></span></p>
    <p align="right" style="background-color: #008aff; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px;"><strong style="float: left">&nbsp;<?php echo $cf3; ?></strong><strong><?php echo $tr3_logTime; ?></strong></p>
    <?php }else{ ?>
    <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr3_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr3_gl; ?></span></p>
    <br />
    <p align="right" style="background-color: #008aff; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px;"><strong style="float: left">&nbsp;<?php echo $cf3; ?></strong><strong><?php echo $tr3_logTime; ?></strong></p>
    <?php } ?>
     


<?php } ?>
</div>
<!-- end 3rd -->


<!-- start 4th --> 
<div style="height: 23.33%; width: 20%; background-color: transparent; float: left; margin-left: 5%;">

  
<?php
if($tr4_img==""){ ?>
 
   
   
<?php }else{ ?>
<div class="polaroid2">
  <img src="<?php echo $tr4_img; ?>" alt="second to the last tapped" style="width:100%; height:100%; border: solid white 1px;">
</div>  
<?php } ?>


</div>


<div style="height: 23.33%; width: 73%; background-color: transparent; float: right; margin: 0% 0% 0% 0%">
<?php
if($tr4_img==""){ ?>
     
<?php }else{ ?>
    
    <?php if($bdayGreeting4=='Display'){ ?>
    <span style="font-size: medium; background-color: white; padding: 2px 4px 0px 4px;"><strong><i class="fa fa-birthday-cake"></i> Happy Birthday!</strong></span><br />
    <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr4_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr4_gl; ?></span></p>
    <p align="right" style="background-color: #008aff; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px;"><strong style="float: left">&nbsp;<?php echo $cf4; ?></strong><strong><?php echo $tr4_logTime; ?></strong></p>
    <?php }else{ ?>
    <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr4_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr4_gl; ?></span></p>
    <br />
    <p align="right" style="background-color: #008aff; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px;"><strong style="float: left">&nbsp;<?php echo $cf4; ?></strong><strong><?php echo $tr4_logTime; ?></strong></p>
    <?php } ?>
    

<?php } ?>
</div>
<!-- end 4th -->

<?php

$displayLog_query=null;
$dpLog_row=null;

$cc_query=null;
$cc_row=null;

$studData_query=null;
$sd_row=null;

$conn=null;

?>

 
 
 <!-- end display 2 -->
 

 