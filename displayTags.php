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

$cc_stmt = $conn->prepare("SELECT * FROM client_computer WHERE ipAddress = :ipAddress");
$cc_stmt->execute([':ipAddress' => $machine_ip]);
$cc_query = $cc_stmt;
$cc_row = $cc_query->fetch();

 

if($cc_row['display_time']==15){
$slides_stmt = $conn->prepare("SELECT sequence, img FROM slides ORDER BY sequence ASC");
$slides_stmt->execute();
$slides = $slides_stmt->fetchAll(PDO::FETCH_ASSOC);

$slide_count = count($slides);
$active_slide_idx = 0;
$si_row = ['img' => ''];

if ($slide_count > 0) {
    $announcement_img = max(0, (int)($cc_row['announcement_img'] ?? 0));
    $active_slide_idx = intdiv($announcement_img, 15);
    if ($active_slide_idx >= $slide_count) {
        $active_slide_idx = $slide_count - 1;
    }

    $si_row = $slides[$active_slide_idx];
}
?>

<div class="kiosk-stage">
    <div class="kiosk-idle-slider">
        <div class="kiosk-idle-frame">
            <img src="announcement_img/<?php echo $si_row['img']; ?>" alt="announcement image..." />
            <button type="button" class="kiosk-idle-arrow left" onclick="changeAnnouncementSlide('prev')">&#8249;</button>
            <button type="button" class="kiosk-idle-arrow right" onclick="changeAnnouncementSlide('next')">&#8250;</button>
        </div>
        <div class="kiosk-idle-dots">
            <?php for ($i = 0; $i < $slide_count; $i++) { ?>
                <button type="button" class="kiosk-idle-dot<?php echo ($i === $active_slide_idx) ? ' active' : ''; ?>" onclick="setAnnouncementSlide(<?php echo $i; ?>)"></button>
            <?php } ?>
        </div>
    </div>
</div>

<?php

$cc_query=null;
$slides_stmt=null;

}else{
 
$tr1_img="";
$tr1_lname="";
$tr1_fmname="";
$tr1_personnel_id_code="";
$tr1_gl="";
$logTime="";
 

$log_id_stmt = $conn->prepare("SELECT log_id FROM personnel_logs WHERE logDate = :logDate AND captured_img = '' AND remarks = '' AND client_ip = :client_ip ORDER BY log_id DESC");
$log_id_stmt->execute([':logDate' => $currentDateDisplay, ':client_ip' => $machine_ip]);
$log_id_query = $log_id_stmt;

if($log_id_query->rowCount()>0){

$liq_row=$log_id_query->fetch();

$displayLog_stmt = $conn->prepare("SELECT logTime, logFlow, log_id, RFTag_id, img, lname, fname, mname, suffix FROM personnel_logs WHERE log_id = :log_id AND logDate = :logDate AND client_ip = :client_ip AND remarks = '' ORDER BY log_id DESC");
$displayLog_stmt->execute([':log_id' => $liq_row['log_id'], ':logDate' => $currentDateDisplay, ':client_ip' => $machine_ip]);
$displayLog_query = $displayLog_stmt;

}else{

$displayLog_stmt = $conn->prepare("SELECT logTime, logFlow, log_id, RFTag_id, img, lname, fname, mname, suffix FROM personnel_logs WHERE logDate = :logDate AND client_ip = :client_ip AND remarks = '' ORDER BY log_id DESC");
$displayLog_stmt->execute([':logDate' => $currentDateDisplay, ':client_ip' => $machine_ip]);
$displayLog_query = $displayLog_stmt;

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
        
        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE RFTag_id = :RFTag_id");
        $studData_stmt->execute([':RFTag_id' => $RFTag_id]);
        $studData_query = $studData_stmt;
        $sd_row=$studData_query->fetch();

        $tr1_personnel_id_code = !empty($sd_row['personnel_id_code']) ? $sd_row['personnel_id_code'] : $RFTag_id;
        
        $do_idxx=$sd_row['do_id'] ?? '';
        
        $bday=($sd_row['bdMM'] ?? '').'/'.($sd_row['bdDD'] ?? '');
        if($bdChecker==$bday)
        {
            $bdayGreeting='Display';
        }else{
            $bdayGreeting='N/A';
        }
        
    }
  
} }
 
?>



<div class="kiosk-stage">

<?php if($tr1_img==""){ ?>
    <div class="kiosk-idle-slider">
        <div class="kiosk-idle-frame"></div>
        <div class="kiosk-idle-dots">
            <span class="kiosk-idle-dot active"></span>
            <span class="kiosk-idle-dot"></span>
            <span class="kiosk-idle-dot"></span>
            <span class="kiosk-idle-dot"></span>
            <span class="kiosk-idle-dot"></span>
        </div>
    </div>
<?php }else{ ?>

    <div class="kiosk-active-main">
        <div class="kiosk-active-photo">
            <img src="<?php echo $tr1_img; ?>" alt="the last tapped" />
        </div>

        <div class="kiosk-active-details">
            <?php if($tr1_personnel_id_code!=""){ ?>
                <span class="kiosk-active-id-badge"><?php echo htmlspecialchars($tr1_personnel_id_code, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php } ?>
            <h1 class="kiosk-active-lname"><?php echo $tr1_lname; ?></h1>
            <p class="kiosk-active-fname"><?php echo $tr1_fmname; ?></p>
            <?php if($tr1_gl!=""){ ?>
                <p class="kiosk-active-gl"><?php echo $tr1_gl; ?></p>
            <?php } ?>

            <?php if($bdayGreeting=='Display'){ ?>
                <p style="margin: 18px 0 0;"><img style="max-width: 70%; height: auto;" src="img/hbd.gif" alt="Happy birthday" /></p>
            <?php } ?>
        </div>
    </div>

    <?php
    $activeBarColor = ($logFlow==='AM IN' OR $logFlow==='PM IN') ? '#008eb7' : '#fe6284';
    $activeBarText = ($log_id_query->rowCount()>0) ? 'One moment please...' : $logFlow.' SUCCESS...';
    ?>
    <div class="kiosk-active-status" style="background-color: <?php echo $activeBarColor; ?>;">
        <span class="kiosk-active-status-time"><?php echo $logTime; ?></span>
        <span class="kiosk-active-status-flow"><?php echo $activeBarText; ?></span>
    </div>

    <?php if($log_id_query->rowCount()>0){ ?>
        <input type="hidden" name="log_id" value="<?php echo $log_id; ?>" />
        <input name="saveBase64File" type="submit" class="btn btn-success" style="display: none;" value="AM OUT" />

        <script>
        $(document).ready(function(){
            $("input[name=submitSnapshot]").click();
            $("input[name=saveBase64File]").click();
        });
        </script>
    <?php } ?>

<?php } ?>

</div>

<?php
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
