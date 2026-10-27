 <?php

include('dbcon.php');

$dpCtr=0;

$currentDateDisplay=date('Y-m-d');
$bdChecker=date('m-d');

function get_client_ip() {
    static $cached_ip = null;
    if ($cached_ip !== null) return $cached_ip;
    $ipaddress = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(!empty($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(!empty($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(!empty($_SERVER['HTTP_FORWARDED']))
       $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(!empty($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    $cached_ip = $ipaddress;
    return $ipaddress;
}

if(get_client_ip()=="::1" || get_client_ip()=="127.0.0.1")
{
    $machine_ip=gethostbyname(gethostname());  
}else{
    $machine_ip=get_client_ip();
}

$cc_query = $conn->query("SELECT * FROM client_computer WHERE ipAddress='$machine_ip'");
$cc_row = $cc_query->fetch();

 

if($cc_row['display_time']==15){ 
    
if($cc_row['announcement_img']<=15){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=1");

}elseif($cc_row['announcement_img']<=30){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=2");

}elseif($cc_row['announcement_img']<=45){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=3");

}elseif($cc_row['announcement_img']<=60){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=4");

}elseif($cc_row['announcement_img']<=75){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=5");

}elseif($cc_row['announcement_img']<=90){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=6");

}elseif($cc_row['announcement_img']<=105){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=7");
   
}elseif($cc_row['announcement_img']<=120){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=8");

}elseif($cc_row['announcement_img']<=135){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=9");
   
}elseif($cc_row['announcement_img']<=150){
$slide_img_query = $conn->query("SELECT * FROM slides WHERE sequence=10");
   
}elseif($cc_row['announcement_img']==150){
$conn->query("UPDATE client_computer SET announcement_img=0 WHERE ipAddress='$machine_ip'");

}

$si_row = $slide_img_query->fetch();
?> 

<div style="float: left; width: 85%; height: 61%; margin-top: 0px;" class="polaroid">
  <img src="announcement_img/<?php echo $si_row['img']; ?>" alt="announcement image..." style="width:100%; height:100%; padding: 5px 5px 5px 5px;">
</div> 

<?php


$cc_query=null;
$slide_img_query=null;

}else{
 
$tr1_img="";
$tr1_lname="";
$tr1_fmname="";
$tr1_gl="";
$logTime="";
 

$currentDateDisplayMdy = date('m/d/Y');
$log_id_query = $conn->query("SELECT log_id FROM personnel_logs WHERE (logDate='$currentDateDisplay' OR logDate='$currentDateDisplayMdy') AND captured_img='' AND remarks='' AND client_ip='$machine_ip' ORDER BY log_id DESC");

if($log_id_query->rowCount()>0){

$liq_row=$log_id_query->fetch();

$displayLog_query = $conn->query("SELECT logTime, logFlow, log_id, RFTag_id, img, lname, fname, mname, suffix FROM personnel_logs WHERE log_id='$liq_row[log_id]' AND (logDate='$currentDateDisplay' OR logDate='$currentDateDisplayMdy') AND client_ip='$machine_ip' AND remarks='' ORDER BY log_id DESC");

}else{

$displayLog_query = $conn->query("SELECT logTime, logFlow, log_id, RFTag_id, img, lname, fname, mname, suffix FROM personnel_logs WHERE (logDate='$currentDateDisplay' OR logDate='$currentDateDisplayMdy') AND client_ip='$machine_ip' AND remarks='' ORDER BY log_id DESC");

}






while($dpLog_row=$displayLog_query->fetch())
{
    
    if(($dpLog_row['logTime']==='11:59 PM' AND $dpLog_row['logFlow']==='PM OUT') OR ($dpLog_row['logTime']==='12:00 AM' AND $dpLog_row['logFlow']==='AM IN')){
        
    }else{
        
    $dpCtr+=1;
     
    $log_id=$dpLog_row['log_id'];
    $RFTag_id=$dpLog_row['RFTag_id'];
 
    if($dpCtr==1)
    {
         
        $tr1_img=$dpLog_row['img'];
        $tr1_lname=$dpLog_row['lname'];
        
        if($dpLog_row['suffix']=="-")
        {
            $tr1_fmname=$dpLog_row['fname']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }else{
            $tr1_fmname=$dpLog_row['fname']." ".$dpLog_row['suffix']." ".substr($dpLog_row['mname'], 0,1)."."; 
        }
        
        //$tr1_gl=$dpLog_row['gradeLevel']." - ".$dpLog_row['section'];
        $tr1_gl="";
        
        
        $logTime=$dpLog_row['logTime'];
        $logFlow=$dpLog_row['logFlow'];
        
        $studData_query = $conn->query("SELECT * FROM personnels WHERE RFTag_id='$RFTag_id'");
        $sd_row=$studData_query->fetch();
        
        $do_idxx=$sd_row['do_id'];
        
        $bday=$sd_row['bdMM'].'/'.$sd_row['bdDD'];
        if($bdChecker==$bday)
        {
            $bdayGreeting='Display';
        }else{
            $bdayGreeting='N/A';
        }
        
    }
  
} }
 
?>



<?php
if($tr1_img==""){ ?>
<div style="float: left;">
</div>
<?php }else{ ?>
<div style="float: left;" class="polaroid">
  <img src="<?php echo $tr1_img; ?>" alt="the last tapped" style="width:100%; height:100%; padding: 5px 5px 5px 5px;">
</div> 
<?php } ?>


<div style="width: 52%; height: 45%; background-color: transparent; float: right; margin: 4% 1% 0% 2%; ">

<?php
if($tr1_img==""){ ?>
 
 
<?php }else{ ?>

<p style="margin-bottom: 0px;"><strong style="font-size: 42px; margin-bottom: 0px;"><?php echo $tr1_lname; ?></strong>

<br />
<span style="font-size: 22px;"><?php echo $tr1_fmname; ?></span>
<br />
<br />
<br />
<span style="font-size: 22px;"><?php echo $tr1_gl; ?></span>
</p>

<?php if($bdayGreeting=='Display'){ ?>
<center><p style="margin-top: 12px;"><img style="width: 70%; height: 40%;" src="img/hbd.gif" /></p></center>
<?php }?>

<?php } ?>

</div>



<?php

if($log_id_query->rowCount()>0){

if($tr1_img==""){
    
}else{ ?>

        <div style="width: 52%; height: 8%; background-color: #008aff; color: white; float: right; margin: 0% 2.5% 0% 0%;">
        <p style="padding: 10px 16px 12px 12px; font-size: x-large; float: left;"> <strong><?php echo $logTime; ?></strong> </p>
        <p style="padding: 14px 16px 12px 12px; font-size: large; float: right;"> <strong>One moment please...</strong> </p>
        
        <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
        <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="AM OUT" />
        
        </div> 
       
                <script>
                $(document).ready(function(){
                    
                        $("input[name=submitSnapshot]").click();
                        $("input[name=saveBase64File]").click();
                        
                });
                </script>
<?php }
        
}else{
    
if($tr1_img==""){ 
    
}else{ ?> 

    <div style="width: 52%; height: 8%; background-color: #008aff; color: white; float: right; margin: 0% 2.5% 0% 0%;">
      <p style="padding: 10px 16px 12px 12px; font-size: x-large; float: left;"> <strong><?php echo $logTime; ?></strong> </p>
      <p style="padding: 10px 16px 12px 12px; font-size: x-large; float: right;"> <strong><?php echo $logFlow; ?> SUCCESS...</strong> </p>
    </div>
 
 
<?php } }

$log_id_query=null;
$log_id_query=null;

$studData_query=null;
$sd_row=null;

$cc_query=null;
$cc_row=null;

$displayLog_query=null;
$dpLog_row=null;

$slide_img_query=null;
$si_row=null;

$sf_query=null;
$sf_row=null;
 
$conn=null;
} ?>
