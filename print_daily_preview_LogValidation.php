<!DOCTYPE html>
<html>

<?php 
include('session.php');  
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

<body onload="window.print()">
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
    $db_filterDate = date('Y-m-d', strtotime($_GET['dateFrom']));
    $pl_filterDate1 = date('m/d/Y', strtotime($_GET['dateFrom'])); // mm/dd/yyyy
    $pl_filterDate2 = date('Y-m-d', strtotime($_GET['dateFrom'])); // YYYY-MM-DD

    // 1. Fetch bio logs
    $LV_query = $conn->query("
        SELECT b.*, p.lname, p.fname, p.mname, p.suffix, p.img 
        FROM bio_dtr b 
        JOIN personnels p ON b.personnel_id = p.personnel_id 
        WHERE b.log_date = '$db_filterDate' 
        ORDER BY b.time_in ASC
    ");
    $bio_logs = $LV_query->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch manual logs
    $pl_query = $conn->query("
        SELECT pl.*, p.lname, p.fname, p.mname, p.suffix, p.img, p.personnel_id 
        FROM personnel_logs pl 
        JOIN personnels p ON (pl.RFTag_id = p.RFTag_id OR pl.RFTag_id = p.biometric_id) AND pl.RFTag_id != ''
        WHERE (pl.logDate = '$pl_filterDate1' OR pl.logDate = '$pl_filterDate2') AND (pl.captured_img != '' OR pl.logDate LIKE '%-%')
        ORDER BY pl.log_id ASC
    ");
    $personnel_logs = $pl_query->fetchAll(PDO::FETCH_ASSOC);

    $merged_logs = [];
    
    // Process bio logs
    foreach($bio_logs as $log) {
        $pid = $log['personnel_id'];
        if (!isset($merged_logs[$pid])) { $merged_logs[$pid] = []; }
        $log['source'] = 'bio';
        $merged_logs[$pid][] = $log;
    }

    // Process manual logs
    foreach($personnel_logs as $pl) {
        $pid = $pl['personnel_id'];
        $time_24 = date('H:i:s', strtotime($pl['logTime']));
        
        if (!isset($merged_logs[$pid])) {
            $merged_logs[$pid] = [];
        }
        
        // Find if we already have an AM or PM manual log for this person to merge with
        $is_am = in_array($pl['logFlow'], ['AM IN', 'AM OUT']);
        $is_pm = in_array($pl['logFlow'], ['PM IN', 'PM OUT']);
        
        $found = false;
        foreach ($merged_logs[$pid] as &$existing_log) {
            if ($existing_log['source'] == 'manual') {
                $has_am = ($existing_log['time_in'] && $existing_log['time_in'] < '12:00:00') || ($existing_log['time_out'] && $existing_log['time_out'] < '13:00:00') || $existing_log['logFlow'] == 'AM IN' || $existing_log['logFlow'] == 'AM OUT';
                $has_pm = ($existing_log['time_in'] && $existing_log['time_in'] >= '12:00:00') || ($existing_log['time_out'] && $existing_log['time_out'] >= '13:00:00') || $existing_log['logFlow'] == 'PM IN' || $existing_log['logFlow'] == 'PM OUT';
                
                if (($is_am && $has_am) || ($is_pm && $has_pm)) {
                    // Merge into existing card
                    if ($pl['logFlow'] == 'AM IN' || $pl['logFlow'] == 'PM IN') {
                        $existing_log['time_in'] = $time_24;
                    }
                    if ($pl['logFlow'] == 'AM OUT' || $pl['logFlow'] == 'PM OUT') {
                        $existing_log['time_out'] = $time_24;
                    }
                    $found = true;
                    break;
                }
            }
        }
        
        if (!$found) {
            $log_entry = [
                'personnel_id' => $pl['personnel_id'],
                'lname' => $pl['lname'], 'fname' => $pl['fname'], 'mname' => $pl['mname'], 'suffix' => $pl['suffix'], 'img' => $pl['img'],
                'log_date' => date('Y-m-d', strtotime($pl['logDate'])),
                'time_in' => '', 'time_out' => '',
                'source' => 'manual',
                'logFlow' => $pl['logFlow']
            ];
            
            if ($pl['logFlow'] == 'AM IN' || $pl['logFlow'] == 'PM IN') { $log_entry['time_in'] = $time_24; }
            if ($pl['logFlow'] == 'AM OUT' || $pl['logFlow'] == 'PM OUT') { $log_entry['time_out'] = $time_24; }
            
            $merged_logs[$pid][] = $log_entry;
        }
    }

    // Flatten back to array
    $final_logs = [];
    foreach($merged_logs as $pid => $logs) {
        foreach($logs as $l) {
            $final_logs[] = $l;
        }
    }

    foreach($final_logs as $LV_row) {
    
                        if($LV_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            
                            if($LV_row['suffix']=='-') { $suffix=''; }else{ $suffix=$LV_row['suffix'].' '; }
                            
                            $finalMName=$suffix.substr($LV_row['mname'], 0, 1).'.';
                        }
                        
                        $printALL_row = $LV_row; // To keep compatibility with below HTML
                        
    ?>
    
    
        <div class="col-md-3" style="border: 1px dotted blue; margin: 2px 2px 2px 2px; padding: 2px 4px 2px 4px;">
        <table style="border: none; width: 98%;">
        <tr>
        <td style="border: none;">
        <center>
        <?php if (isset($LV_row['source']) && $LV_row['source'] == 'manual'): ?>
            <span class="badge badge-info" style="background-color: #17a2b8; color: white;">Manual Encode</span>
        <?php else: ?>
            <span class="badge badge-secondary">Bio Scanner</span>
        <?php endif; ?>
        </center>
        </td>
        <td style="border: none;">
        <center>
        <img src="<?php echo empty($LV_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$LV_row['img']; ?>" width="60" height="75" class="img-fluid rounded" />
        </center>
        </td>
        </tr>
        
        <tr>
        <td colspan="2" style="border: none;">
        <small>
        <strong>Fullname: </strong><?php echo $printALL_row['lname'].", ".$printALL_row['fname']." ".$finalMName; ?><br />
        <?php 
        $tIn = $LV_row['time_in'];
        if ($tIn && $tIn != '00:00:00') {
            $inStr = date('h:i a', strtotime($tIn));
            $inLbl = (strtotime($tIn) < strtotime('12:00:00')) ? 'AM IN' : 'PM IN';
            echo "{$inStr} ( {$inLbl} )<br/>";
        }
        $tOut = $LV_row['time_out'];
        if ($tOut && $tOut != '00:00:00') {
            $outStr = date('h:i a', strtotime($tOut));
            $outLbl = (strtotime($tOut) < strtotime('13:00:00')) ? 'AM OUT' : 'PM OUT';
            echo "{$outStr} ( {$outLbl} )";
        }
        ?>
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
       
            