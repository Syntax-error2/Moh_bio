<?php
include('session.php');

$requestedDate = $_GET['dateFrom'] ?? '';
$date = DateTime::createFromFormat('!m/d/Y', $requestedDate);
if (!$date || $date->format('m/d/Y') !== $requestedDate) {
    http_response_code(400);
    exit('Invalid date.');
}

$db_filterDate = $date->format('Y-m-d');
$pl_filterDate1 = $date->format('m/d/Y');
$pl_filterDate2 = $db_filterDate;
?>

<!DOCTYPE html>
<html>
<?php include('header_print.php'); ?>
<body onload="window.print()">
<?php include('header_print_letterHead.php'); ?>
<hr />

<center>
<h3>DAILY LOG VALIDATION REPORT</h3>
<h4><?php echo htmlspecialchars($date->format('F d, Y')); ?></h4>
</center>

<hr />
 
 
<div class="col-lg-12">

    <div class="row">
    
    <?php
    // 1. Fetch bio logs
    $LV_query = $conn->prepare("
        SELECT b.*, p.lname, p.fname, p.mname, p.suffix, p.img 
        FROM bio_dtr b 
        JOIN personnels p ON b.personnel_id = p.personnel_id 
        WHERE b.log_date = :log_date
        ORDER BY b.time_in ASC
    ");
    $LV_query->execute([':log_date' => $db_filterDate]);
    $bio_logs = $LV_query->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch manual logs
    $pl_query = $conn->prepare("
        SELECT pl.*, p.lname, p.fname, p.mname, p.suffix, p.img, p.personnel_id 
        FROM personnel_logs pl 
        JOIN personnels p ON (pl.RFTag_id = p.RFTag_id OR pl.RFTag_id = p.biometric_id) AND pl.RFTag_id != ''
        WHERE (pl.logDate = :date1 OR pl.logDate = :date2) AND (pl.captured_img != '' OR pl.logDate LIKE '%-%')
        ORDER BY pl.log_id ASC
    ");
    $pl_query->execute([':date1' => $pl_filterDate1, ':date2' => $pl_filterDate2]);
    $personnel_logs = $pl_query->fetchAll(PDO::FETCH_ASSOC);

    $merged_logs = [];
    $emptyLog = static function (array $person): array {
        return [
            'lname' => $person['lname'], 'fname' => $person['fname'],
            'mname' => $person['mname'], 'suffix' => $person['suffix'], 'img' => $person['img'],
            'am_in' => '', 'am_out' => '', 'pm_in' => '', 'pm_out' => '',
            'bio' => false, 'manual' => false,
        ];
    };

    foreach ($bio_logs as $log) {
        $pid = $log['personnel_id'];
        if (!isset($merged_logs[$pid])) {
            $merged_logs[$pid] = $emptyLog($log);
        }
        $merged_logs[$pid]['bio'] = true;
        if ($log['time_in'] && $log['time_in'] !== '00:00:00') {
            $slot = $log['time_in'] < '12:00:00' ? 'am_in' : 'pm_in';
            if ($merged_logs[$pid][$slot] === '') {
                $merged_logs[$pid][$slot] = $log['time_in'];
            }
        }
        if ($log['time_out'] && $log['time_out'] !== '00:00:00') {
            $slot = $log['time_out'] < '13:00:00' ? 'am_out' : 'pm_out';
            if ($merged_logs[$pid][$slot] === '') {
                $merged_logs[$pid][$slot] = $log['time_out'];
            }
        }
    }

    // Manual entries override the corresponding scanner time, as in the viewer.
    $manualSlots = ['AM IN' => 'am_in', 'AM OUT' => 'am_out', 'PM IN' => 'pm_in', 'PM OUT' => 'pm_out'];
    foreach ($personnel_logs as $pl) {
        if (!isset($manualSlots[$pl['logFlow']])) {
            continue;
        }
        $timestamp = strtotime($pl['logTime']);
        if ($timestamp === false) {
            continue;
        }
        $pid = $pl['personnel_id'];
        if (!isset($merged_logs[$pid])) {
            $merged_logs[$pid] = $emptyLog($pl);
        }
        $merged_logs[$pid][$manualSlots[$pl['logFlow']]] = date('H:i:s', $timestamp);
        $merged_logs[$pid]['manual'] = true;
    }

    $final_logs = array_values(array_filter($merged_logs, static function (array $log): bool {
        return $log['am_in'] !== '' || $log['am_out'] !== '' || $log['pm_in'] !== '' || $log['pm_out'] !== '';
    }));
    usort($final_logs, static function (array $a, array $b): int {
        return [$a['lname'], $a['fname']] <=> [$b['lname'], $b['fname']];
    });

    foreach($final_logs as $LV_row) {
    
                        if($LV_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            
                            if($LV_row['suffix']=='-') { $suffix=''; }else{ $suffix=$LV_row['suffix'].' '; }
                            
                            $finalMName=$suffix.substr($LV_row['mname'], 0, 1).'.';
                        }
                        
    ?>
    
    
        <div class="col-md-3" style="border: 1px dotted blue; margin: 2px 2px 2px 2px; padding: 2px 4px 2px 4px;">
        <table style="border: none; width: 98%;">
        <tr>
        <td style="border: none;">
        <center>
        <?php if ($LV_row['manual']): ?>
            <span class="badge badge-info" style="background-color: #17a2b8; color: white;"><?php echo $LV_row['bio'] ? 'Bio + Manual' : 'Manual Encode'; ?></span>
        <?php else: ?>
            <span class="badge badge-secondary">Bio Scanner</span>
        <?php endif; ?>
        </center>
        </td>
        <td style="border: none;">
        <center>
        <img src="<?php echo htmlspecialchars(empty($LV_row['img']) ? 'img/avatar-1.jpg' : 'personnelImg/'.$LV_row['img']); ?>" width="60" height="75" class="img-fluid rounded" />
        </center>
        </td>
        </tr>

        <tr>
        <td colspan="2" style="border: none;">
        <small>
        <strong>Fullname: </strong><?php echo htmlspecialchars($LV_row['lname'].", ".$LV_row['fname']." ".$finalMName); ?><br />
        <?php 
        foreach (['am_in' => 'AM IN', 'am_out' => 'AM OUT', 'pm_in' => 'PM IN', 'pm_out' => 'PM OUT'] as $slot => $label) {
            if ($LV_row[$slot] !== '') {
                echo htmlspecialchars(date('h:i a', strtotime($LV_row[$slot]))).' ( '.$label.' )<br />';
            }
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
