<?php

include('dbcon.php');

$dpCtr=0;

$currentDateDisplay=date('m/d/Y');
$bdChecker=date('m/d');

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
$tr2_name="";
$bdayGreeting2='N/A';

$tr3_img="";
$tr3_gl="";
$tr3_logTime="";
$cf3="";
$tr3_name="";
$bdayGreeting3='N/A';

$tr4_img="";
$tr4_gl="";
$tr4_logTime="";
$cf4="";
$tr4_name="";
$bdayGreeting4='N/A';
 
$displayLog_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE logDate = :logDate AND client_ip = :client_ip AND remarks = '' ORDER BY log_id DESC");
$displayLog_stmt->execute([':logDate' => $currentDateDisplay, ':client_ip' => $machine_ip]);
$displayLog_query = $displayLog_stmt;

$cc_stmt = $conn->prepare("SELECT * FROM client_computer WHERE ipAddress = :ipAddress");
$cc_stmt->execute([':ipAddress' => $machine_ip]);
$cc_query = $cc_stmt;
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
        
        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE lname = :lname AND fname = :fname AND mname = :mname");
        $studData_stmt->execute([':lname' => $dpLog_row['lname'], ':fname' => $dpLog_row['fname'], ':mname' => $dpLog_row['mname']]);
        $studData_query = $studData_stmt;
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
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
        
        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE lname = :lname AND fname = :fname AND mname = :mname");
        $studData_stmt->execute([':lname' => $dpLog_row['lname'], ':fname' => $dpLog_row['fname'], ':mname' => $dpLog_row['mname']]);
        $studData_query = $studData_stmt;
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
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

        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE lname = :lname AND fname = :fname AND mname = :mname");
        $studData_stmt->execute([':lname' => $dpLog_row['lname'], ':fname' => $dpLog_row['fname'], ':mname' => $dpLog_row['mname']]);
        $studData_query = $studData_stmt;
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting4='Display';
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
        
        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE lname = :lname AND fname = :fname AND mname = :mname");
        $studData_stmt->execute([':lname' => $dpLog_row['lname'], ':fname' => $dpLog_row['fname'], ':mname' => $dpLog_row['mname']]);
        $studData_query = $studData_stmt;
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
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
        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE lname = :lname AND fname = :fname AND mname = :mname");
        $studData_stmt->execute([':lname' => $dpLog_row['lname'], ':fname' => $dpLog_row['fname'], ':mname' => $dpLog_row['mname']]);
        $studData_query = $studData_stmt;
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
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

        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE lname = :lname AND fname = :fname AND mname = :mname");
        $studData_stmt->execute([':lname' => $dpLog_row['lname'], ':fname' => $dpLog_row['fname'], ':mname' => $dpLog_row['mname']]);
        $studData_query = $studData_stmt;
        $sd_row=$studData_query->fetch();
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting4='Display';
        }else{
            $bdayGreeting4='N/A';
        }
        
    }
 
     
}

} }

$inGradient = 'linear-gradient(135deg, rgba(0, 142, 183, 0.22) 0%, rgba(0, 142, 183, 0.06) 100%)';
$outGradient = 'linear-gradient(135deg, rgba(254, 98, 132, 0.24) 0%, rgba(254, 98, 132, 0.06) 100%)';
$neutralGradient = 'linear-gradient(135deg, rgba(120, 120, 120, 0.10) 0%, rgba(120, 120, 120, 0.04) 100%)';

$card2Gradient = ($cf2 === 'AM IN' || $cf2 === 'PM IN') ? $inGradient : ((($cf2 === 'AM OUT' || $cf2 === 'PM OUT') ? $outGradient : $neutralGradient));
$card3Gradient = ($cf3 === 'AM IN' || $cf3 === 'PM IN') ? $inGradient : ((($cf3 === 'AM OUT' || $cf3 === 'PM OUT') ? $outGradient : $neutralGradient));
$card4Gradient = ($cf4 === 'AM IN' || $cf4 === 'PM IN') ? $inGradient : ((($cf4 === 'AM OUT' || $cf4 === 'PM OUT') ? $outGradient : $neutralGradient));

 
?>




<!--  display 2 -->


<!-- start 2nd -->
<div style="height: 30%; min-height: 90px; width: 94%; clear: both; margin-left: 3%; margin-top: 3%; background: <?php echo $card2Gradient; ?>; border-radius: 14px; border-left: 5px solid <?php echo ($cf2==='AM IN' OR $cf2==='PM IN') ? '#008eb7' : '#fe6284'; ?>; padding: 7px; box-sizing: border-box; animation: slideIn 0.3s ease-out;">

<div style="height: 100%; width: 20%; background-color: transparent; float: left;">

<?php
if($tr2_img==""){ ?>

<?php }else{ ?>
<div class="polaroid2">
    <img src="<?php echo $tr2_img; ?>" alt="second to the last tapped" style="width:100%; height:100%; border: solid white 1px;">
</div>
<?php } ?>

</div>

<div style="height: 100%; width: 78%; background-color: transparent; float: right;">
<?php
if($tr2_img==""){ ?>

<?php }else{ ?>

        <?php if($bdayGreeting2=='Display'){ ?>
        <span style="font-size: medium; background-color: transparent; padding: 2px 4px 0px 4px;"><strong><i class="fa fa-birthday-cake"></i> Happy Birthday!</strong></span><br />
        <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr2_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr2_gl; ?></span></p>
        <p align="right" style="background-color: <?php if($cf2==='AM IN' OR $cf2==='PM IN'){ ?> #008eb7 <?php }else{ ?> #fe6284 <?php } ?>; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px; font-weight: 700;"><strong style="float: left">&nbsp;<?php echo $cf2; ?></strong><strong><?php echo $tr2_logTime; ?></strong></p>
        <?php }else{ ?>
        <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr2_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr2_gl; ?></span></p>
        <p align="right" style="background-color: <?php if($cf2==='AM IN' OR $cf2==='PM IN'){ ?> #008eb7 <?php }else{ ?> #fe6284 <?php } ?>; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px; font-weight: 700;"><strong style="float: left">&nbsp;<?php echo $cf2; ?></strong><strong><?php echo $tr2_logTime; ?></strong></p>
        <?php } ?>

<?php } ?>
</div>
</div>
<!-- end 2nd -->


<!-- start 3rd -->
<div style="height: 30%; min-height: 90px; width: 94%; clear: both; margin-left: 3%; margin-top: 2.4%; background: <?php echo $card3Gradient; ?>; border-radius: 14px; border-left: 5px solid <?php echo ($cf3==='AM IN' OR $cf3==='PM IN') ? '#008eb7' : '#fe6284'; ?>; padding: 7px; box-sizing: border-box; animation: slideIn 0.3s ease-out;">

<div style="height: 100%; width: 20%; background-color: transparent; float: left;">

<?php
if($tr3_img==""){ ?>

<?php }else{ ?>
<div class="polaroid2">
    <img src="<?php echo $tr3_img; ?>" alt="second to the last tapped" style="width:100%; height:100%; border: solid white 1px;">
</div>
<?php } ?>

</div>

<div style="height: 100%; width: 78%; background-color: transparent; float: right;">
<?php
if($tr3_img==""){ ?>

<?php }else{ ?>

        <?php if($bdayGreeting3=='Display'){ ?>
        <span style="font-size: medium; background-color: transparent; padding: 2px 4px 0px 4px;"><strong><i class="fa fa-birthday-cake"></i> Happy Birthday!</strong></span><br />
        <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr3_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr3_gl; ?></span></p>
        <p align="right" style="background-color: <?php if($cf3==='AM IN' OR $cf3==='PM IN'){ ?> #008eb7 <?php }else{ ?> #fe6284 <?php } ?>; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px; font-weight: 700;"><strong style="float: left">&nbsp;<?php echo $cf3; ?></strong><strong><?php echo $tr3_logTime; ?></strong></p>
        <?php }else{ ?>
        <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr3_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr3_gl; ?></span></p>
        <p align="right" style="background-color: <?php if($cf3==='AM IN' OR $cf3==='PM IN'){ ?> #008eb7 <?php }else{ ?> #fe6284 <?php } ?>; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px; font-weight: 700;"><strong style="float: left">&nbsp;<?php echo $cf3; ?></strong><strong><?php echo $tr3_logTime; ?></strong></p>
        <?php } ?>

<?php } ?>
</div>
</div>
<!-- end 3rd -->


<!-- start 4th -->
<div style="height: 30%; min-height: 90px; width: 94%; clear: both; margin-left: 3%; margin-top: 2.4%; background: <?php echo $card4Gradient; ?>; border-radius: 14px; border-left: 5px solid <?php echo ($cf4==='AM IN' OR $cf4==='PM IN') ? '#008eb7' : '#fe6284'; ?>; padding: 7px; box-sizing: border-box; animation: slideIn 0.3s ease-out;">

<div style="height: 100%; width: 20%; background-color: transparent; float: left;">

<?php
if($tr4_img==""){ ?>

<?php }else{ ?>
<div class="polaroid2">
    <img src="<?php echo $tr4_img; ?>" alt="second to the last tapped" style="width:100%; height:100%; border: solid white 1px;">
</div>
<?php } ?>

</div>

<div style="height: 100%; width: 78%; background-color: transparent; float: right;">
<?php
if($tr4_img==""){ ?>

<?php }else{ ?>

        <?php if($bdayGreeting4=='Display'){ ?>
        <span style="font-size: medium; background-color: transparent; padding: 2px 4px 0px 4px;"><strong><i class="fa fa-birthday-cake"></i> Happy Birthday!</strong></span><br />
        <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr4_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr4_gl; ?></span></p>
        <p align="right" style="background-color: <?php if($cf4==='AM IN' OR $cf4==='PM IN'){ ?> #008eb7 <?php }else{ ?> #fe6284 <?php } ?>; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px; font-weight: 700;"><strong style="float: left">&nbsp;<?php echo $cf4; ?></strong><strong><?php echo $tr4_logTime; ?></strong></p>
        <?php }else{ ?>
        <p style="margin-bottom: 0px;"><strong style="font-size: 18px; margin-bottom: 0px;"><?php echo $tr4_name; ?></strong><br /><span style="font-size: medium;"><?php echo $tr4_gl; ?></span></p>
        <p align="right" style="background-color: <?php if($cf4==='AM IN' OR $cf4==='PM IN'){ ?> #008eb7 <?php }else{ ?> #fe6284 <?php } ?>; color: white; padding: 2px 16px 2px 2px; margin-bottom: 5px; font-weight: 700;"><strong style="float: left">&nbsp;<?php echo $cf4; ?></strong><strong><?php echo $tr4_logTime; ?></strong></p>
        <?php } ?>

<?php } ?>
</div>
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
 

 