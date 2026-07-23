<!DOCTYPE html>
<html>

<?php
include('session.php');
include('header.php');

if (!isset($conn) || !($conn instanceof PDO)) {
  echo '<div class="container-fluid mt-3"><div class="alert alert-danger">Unable to load user dashboard: database connection is not available.</div></div>';
  exit;
}

    $schoolName = $schoolName ?? 'HRMS';
    $school_id = isset($school_id) ? (int)$school_id : 0;
    $user_personnel_id = isset($user_personnel_id) ? (int)$user_personnel_id : 0;
  
  
    $day=date("l"); //Mon-Sun
    
    $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
    $studData_stmt->execute([':personnel_id' => $user_personnel_id]);
    $studData_query = $studData_stmt;
    $studData_row = $studData_query->fetch(PDO::FETCH_ASSOC);
    $hasPersonnelData = is_array($studData_row) && !empty($studData_row);
    if (!$hasPersonnelData) {
      $studData_row = [
        'RFTag_id' => '',
        'do_id' => 0,
        'shift_id' => 0
      ];
    }
                    
    if(isset($_POST['filterDate'])){
    $filterDate=$_POST['reportDate'];
     
    }else{
        
    $filterDate=date('m/Y');
   
    }

    $selectedMM = substr((string)$filterDate, 0, 2);
    $selectedYYYY = substr((string)$filterDate, 3, 4);

    $monthStartDate = sprintf('%04d-%02d-01', (int)$selectedYYYY, (int)$selectedMM);
    $monthEndDate = date('Y-m-t', strtotime($monthStartDate));

    $dateFrom = $_POST['dateFrom'] ?? $monthStartDate;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$dateFrom)) {
      $dateFrom = $monthStartDate;
    }

    $dateTo = $_POST['dateTo'] ?? $monthEndDate;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$dateTo)) {
      $dateTo = $monthEndDate;
    }

    $dayTypeFilter = $_POST['dayTypeFilter'] ?? 'all';
    $allowedDayTypeFilter = ['all', 'weekday', 'weekend'];
    if (!in_array($dayTypeFilter, $allowedDayTypeFilter, true)) {
      $dayTypeFilter = 'all';
    }

    $statusFilter = $_POST['statusFilter'] ?? 'all';
    $allowedStatusFilter = ['all', 'complete', 'missing', 'late', 'undertime', 'leave_event'];
    if (!in_array($statusFilter, $allowedStatusFilter, true)) {
      $statusFilter = 'all';
    }

    $keywordFilter = trim((string)($_POST['keywordFilter'] ?? ''));
    
    if(isset($_POST['print_daily_LV'])){ ?>
    
    <script>
    window.open('print_daily_preview_LogValidation.php?dateFrom=<?php echo $filterDate; ?>', '_blank');
    window.location='home.php';
    </script>
    
    
    <?php } ?>
    
    <style>

     
    
    * {
      box-sizing: border-box;
    }
    
    
    .dtr-table {
      border-collapse: collapse;
      width: 100%;
      border: 1px solid #ddd;
      font-size: 12px;
    }
    
    .dtr-table th, .dtr-table td {
      text-align: left;
      padding: 6px;
    }
    
    .dtr-table tr, .dtr-table td {
      border: 1px solid #ddd;
      
    }
    
    .dtr-table tr.header, .dtr-table tr:hover {
      background-color: #f1f1f1;
    }
    
    .pb{
        page-break-after: always;
         
    }
    </style>

    <body>

    <?php include('menu_sidebar.php'); ?>

    <div class="page">

    <?php include('navbar_header.php'); ?>

    
    
      <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
             
            <li class="breadcrumb-item active">Home</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
            <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header">
                  <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-2 mb-md-0">MONTHLY LOG DATA TABLE</h4>
                    <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a>
                  </div>

                  <form method="POST" class="mt-3">
                    <div class="row align-items-end">
                      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="mb-1"><strong>Month / Year</strong></label>
                        <select name="reportDate" class="form-control form-control-sm">
                          <option><?php echo htmlspecialchars((string)$filterDate, ENT_QUOTES, 'UTF-8'); ?></option>
                          <?php
                          $currentDate="";
                          $opt_stmt = $conn->prepare("SELECT DISTINCT logDate FROM personnel_logs WHERE RFTag_id = :RFTag_id ORDER BY logDate DESC");
                          $opt_stmt->execute([':RFTag_id' => (string)$studData_row['RFTag_id']]);
                          $opt_query = $opt_stmt;
                          while ($opt_row = $opt_query->fetch())
                          {
                            $monthOption = substr($opt_row['logDate'], 0,2).'/'.substr($opt_row['logDate'], 6,4);
                            if($filterDate !== $monthOption){ ?>
                              <option><?php echo htmlspecialchars($monthOption, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php
                              $currentDate = $opt_row['logDate'];
                            }
                          } ?>
                        </select>
                      </div>

                      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="mb-1"><strong>From Date</strong></label>
                        <input type="date" id="dateFrom" name="dateFrom" class="form-control form-control-sm" value="<?php echo htmlspecialchars((string)$dateFrom, ENT_QUOTES, 'UTF-8'); ?>">
                      </div>

                      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="mb-1"><strong>To Date</strong></label>
                        <input type="date" id="dateTo" name="dateTo" class="form-control form-control-sm" value="<?php echo htmlspecialchars((string)$dateTo, ENT_QUOTES, 'UTF-8'); ?>">
                      </div>

                      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="mb-1"><strong>Day Type</strong></label>
                        <select id="dayTypeFilter" name="dayTypeFilter" class="form-control form-control-sm">
                          <option value="all" <?php echo $dayTypeFilter === 'all' ? 'selected' : ''; ?>>All Days</option>
                          <option value="weekday" <?php echo $dayTypeFilter === 'weekday' ? 'selected' : ''; ?>>Weekdays</option>
                          <option value="weekend" <?php echo $dayTypeFilter === 'weekend' ? 'selected' : ''; ?>>Weekends</option>
                        </select>
                      </div>

                      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="mb-1"><strong>Status</strong></label>
                        <select id="statusFilter" name="statusFilter" class="form-control form-control-sm">
                          <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Entries</option>
                          <option value="complete" <?php echo $statusFilter === 'complete' ? 'selected' : ''; ?>>Complete Logs</option>
                          <option value="missing" <?php echo $statusFilter === 'missing' ? 'selected' : ''; ?>>Missing Logs</option>
                          <option value="late" <?php echo $statusFilter === 'late' ? 'selected' : ''; ?>>With Tardiness</option>
                          <option value="undertime" <?php echo $statusFilter === 'undertime' ? 'selected' : ''; ?>>With Undertime</option>
                          <option value="leave_event" <?php echo $statusFilter === 'leave_event' ? 'selected' : ''; ?>>Leave / Event</option>
                        </select>
                      </div>

                      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="mb-1"><strong>Keyword</strong></label>
                        <input type="text" id="keywordFilter" name="keywordFilter" class="form-control form-control-sm" placeholder="Search notes/event..." value="<?php echo htmlspecialchars((string)$keywordFilter, ENT_QUOTES, 'UTF-8'); ?>">
                      </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center mt-2">
                      <button type="submit" name="filterDate" class="btn btn-primary btn-sm mr-2 mb-2"><i class="fa fa-calendar-check-o"></i> Load Month</button>
                      <button type="button" id="applyLogFilters" class="btn btn-info btn-sm mr-2 mb-2"><i class="fa fa-filter"></i> Apply Filters</button>
                      <button type="button" id="resetLogFilters" class="btn btn-light btn-sm mb-2">Reset</button>
                      <small class="ml-md-3 text-muted mb-2">Advanced filters affect visible rows only; monthly totals remain based on the selected month.</small>
                    </div>
                  </form>
                </div>
                
                
                
                
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">

                    <?php if (!$hasPersonnelData) { ?>
                    <div class="alert alert-warning m-3">
                      No linked personnel profile found for this user account. Please contact HR/Admin to link your account to a personnel record.
                    </div>
                    <?php } else { ?>
                
                    <?php
                    
                      
                      $selectedMM=substr($filterDate, 0,2);
                      $selectedYYYY=substr($filterDate, 3,4);
                      $grandTotalTRHr=0;
                      $grandTotalTRMin=0;
                      
                      $grandTotalamLateMin=0;
                      $grandTotalpmLateMin=0;
                      
                      $grandTotalamUTimeMin=0;
                      $grandTotalpmUTimeMin=0;
                      
                      
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
                
                ?>
                    
   
                    <table id="monthlyLogTable" class="dtr-table">
                    
                      <tr style="font-weight: light; font-size: 14px">
                        
                        <td style="width:8%;"><center><strong>DATE</strong></center></td>
                        
                        <td style="width:18%;"><center><strong>AM IN</strong></center></td>
                        <td style="width:18%;"><center><strong>AM OUT</strong></center></td>
                        <td style="width:18%;"><center><strong>PM IN</strong></center></td>
                        <td style="width:18%;"><center><strong>PM OUT</strong></center></td>
                        <td style="width:10%;"><center><strong>TARDINESS</strong></center></td>
                        <td style="width:10%;"><center><strong>UNDERTIME</strong></center></td>
                      </tr>
                     
                    <?php
                     
                        $RFTag_id=$studData_row['RFTag_id'];
                     
                        $amPresentCtr=0;
                        $pmPresentCtr=0;
                        
                        $amLateCtr=0;
                        $pmLateCtr=0;
                        
                        $amUTimeCtr=0;
                        $pmUTimeCtr=0;
                        
                        $amAbsentCtr=0;
                        $pmAbsentCtr=0;
                        
                        $leaveCtr=0;
                        
                        
                        for($d=1; $d<$MMmaxDay; $d++){
                            
                            $dailyLate=0;
                            $dailyUTime=0;
                            if($d<10){
                            $logDateCtr=$selectedMM.'/0'.$d.'/'.$selectedYYYY;
                            }else{
                            $logDateCtr=$selectedMM.'/'.$d.'/'.$selectedYYYY;
                            }
                     
                        ?>
                        
                      <tr class="log-row" data-log-date="<?php echo htmlspecialchars($logDateCtr, ENT_QUOTES, 'UTF-8'); ?>">
                     
                        
                        <?php
                      $SC_stmt3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
                      $SC_stmt3->execute([':completeDate' => $logDateCtr]);
                      $SC_query3 = $SC_stmt3;
                          
                          if($SC_query3->rowCount()>0){
                          
                      ?>
                      <td rowspan="2">
                        <?php
                        
                        $timestamp = strtotime($logDateCtr);
                        $dayName=date('l', $timestamp);
                        $dayName2=substr($dayName, 0,3);
                        echo substr($logDateCtr, 0, 6).substr($logDateCtr, 8, 2)." <sup>".$dayName2."</sup>";
                        
                        ?>
                        </td>
                      <?php }else{?>
                      <td>
                        <?php
                        
                        $timestamp = strtotime($logDateCtr);
                        $dayName=date('l', $timestamp);
                        $dayName2=substr($dayName, 0,3);
                        echo substr($logDateCtr, 0, 6).substr($logDateCtr, 8, 2)." <sup>".$dayName2."</sup>";
                        
                        ?>
                        </td>
                      <?php } ?>
                      
                         
                        
                        
                        <?php
                        
                        $studLogs_remarks_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate AND (remarks != '' AND remarks != 'Updated' AND remarks != 'Inserted')");
                        $studLogs_remarks_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_remarks_query = $studLogs_remarks_stmt;
                        if($studLogs_remarks_query->rowCount()>0){ 
                        $SRQ_row=$studLogs_remarks_query->fetch();
                        $leaveCtr=$leaveCtr+1;
                        
                        ?> 
                        <td colspan="7" style="background-color: #b8ffd9;"><center><strong><?php echo $SRQ_row['remarks']; ?></strong></center></td>
                         
                          <?php }else{
                            
                        $studLogs_sat_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logDate = :logDate");
                        $studLogs_sat_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_sat_query = $studLogs_sat_stmt;
                        if($studLogs_sat_query->rowCount()==0 AND ($dayName2=='Sat' OR $dayName2=='Sun')){ ?> 
                        
                        <td colspan="6" style="background-color: #ececec;"><center><strong><?php if($dayName2=='Sat'){ echo "S A T U R D A Y"; } if($dayName2=='Sun'){ echo "S U N D A Y"; } ?></strong></center></td>
                         
                          <?php }else{
                         
                          $SC_stmt = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != 'Display to DTR'");
                          $SC_stmt->execute([':completeDate' => $logDateCtr]);
                          $SC_query = $SC_stmt;
                          
                          if($SC_query->rowCount()>0){
                          $SC_row=$SC_query->fetch();
                          ?>
                            
                          <td colspan="6" style="background-color: #ffbac5;"><center><strong><?php echo $SC_row['event_title'].'</strong> [ '.$SC_row['act_type'].' ]'; ?></strong></center></td>
                          
                          <?php }else{ ?> 
                     
                        
                        
                        <!-- AM IN -->
                        <td>
                        <?php
                        $studLogs_query_AM_IN_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'AM IN' AND logDate = :logDate");
                        $studLogs_query_AM_IN_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_query_AM_IN = $studLogs_query_AM_IN_stmt;
                        $studLogs_AM_IN_row=$studLogs_query_AM_IN->fetch();
                        ?>
                        
                        <?php
                        if($studLogs_query_AM_IN->rowCount()>0){
                        
                        $str_time_am_in= date("H:i:s", strtotime($studLogs_AM_IN_row['logTime']));
                        $str_time_am_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_in);
                        sscanf($str_time_am_in, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_time_am_in = ($hours * 3600) + $minutes * 60 + $seconds;
                            
                        ?>
                        
                        
                        <?php
                        if($studLogs_AM_IN_row['late_status']==='on'){
                            
                            $sched_stmt = $conn->prepare("SELECT am_IN_co FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
                            $sched_stmt->execute([
                              ':school_id' => $school_id,
                              ':do_id' => $studData_row['do_id'],
                              ':shift_id' => $studData_row['shift_id'],
                              ':day' => $dayName
                            ]);
                            $sched_query = $sched_stmt;
                            $sq_row=$sched_query->fetch();
                     
                            $str_time_sched_am_in_late= date("H:i:s", strtotime($sq_row['am_IN_co']));
                            $str_time_sched_am_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_in_late);
                            sscanf($str_time_sched_am_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
                            $time_seconds_time_am_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
                            
                            $am_in_late_min=($time_seconds_time_am_in-$time_seconds_time_am_in_late)/60;
                            
                            $grandTotalamLateMin=$grandTotalamLateMin+$am_in_late_min;
                            
                            $amLateCtr=$amLateCtr+1;
                            $amPresentCtr=$amPresentCtr+1;
                            
                            $dailyLate=$dailyLate+$am_in_late_min;
                            ?>
                            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo $studLogs_AM_IN_row['logTime']; ?> ]</p>
                        <?php }else{ 
                            
                            $dailyLate=$dailyLate+0;
                            $amPresentCtr=$amPresentCtr+1;
                            
                            ?>
                            <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_AM_IN_row['logTime']; ?> ]</p>
                        <?php } ?>
                    
                        <!-- time in seconds AM_IN -->
                        <?php
                        
                        
                        
                        
                        
                        ?>
                        
                        <?php }else{ $time_seconds_time_am_in=0;  
                        
                           $amAbsentCtr=$amAbsentCtr+1; ?>
                           
                            <p style="margin: 0px;">--:--</p>   
                        <?php } ?>
                        
                        </td>
                        
                        
                        <!-- AM OUT -->
                        <td>
                        <?php
                        $studLogs_query_AM_OUT_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'AM OUT' AND logDate = :logDate");
                        $studLogs_query_AM_OUT_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_query_AM_OUT = $studLogs_query_AM_OUT_stmt;
                        $studLogs_AM_OUT_row=$studLogs_query_AM_OUT->fetch();
                        ?>
                        
                        <?php
                        if($studLogs_query_AM_OUT->rowCount()>0){ 
                            
                        $str_time_am_out= date("H:i:s", strtotime($studLogs_AM_OUT_row['logTime']));
                        $str_time_am_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_am_out);
                        sscanf($str_time_am_out, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_time_am_out = ($hours * 3600) + $minutes * 60 + $seconds;
                        
                        ?>
                        
                        <?php
                        if($studLogs_AM_OUT_row['late_status']==='on'){
                            
                            $sched_stmt = $conn->prepare("SELECT am_OUT FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
                            $sched_stmt->execute([
                              ':school_id' => $school_id,
                              ':do_id' => $studData_row['do_id'],
                              ':shift_id' => $studData_row['shift_id'],
                              ':day' => $dayName
                            ]);
                            $sched_query = $sched_stmt;
                            $sq_row=$sched_query->fetch();
                            
                            $str_time_sched_am_out_utime= date("H:i:s", strtotime($sq_row['am_OUT']));
                            $str_time_sched_am_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_am_out_utime);
                            sscanf($str_time_sched_am_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
                            $time_seconds_time_am_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
                            
                            $am_out_utime_min=($time_seconds_time_am_out_utime-$time_seconds_time_am_out)/60;
                            
                            $grandTotalamUTimeMin=$grandTotalamUTimeMin+$am_out_utime_min;
                            
                            $amUTimeCtr=$amUTimeCtr+1;
                            
                            $dailyUTime=$dailyUTime+$am_out_utime_min;
                                
                        ?>
                            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo $studLogs_AM_OUT_row['logTime']; ?> ]</p>
                        <?php }else{
                            
                            $dailyUTime=$dailyUTime+0; ?>
                            
                            <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_AM_OUT_row['logTime']; ?> ]</p>
                        
                        <?php } ?>
                    
                        <!-- time in seconds AM_OUT -->
                        
                        
                        <?php }else{ $time_seconds_time_am_out=0; 
                        
                        $studLogs_query_PM_OUT_chk_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM OUT' AND logDate = :logDate");
                        $studLogs_query_PM_OUT_chk_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_query_PM_OUT_chk = $studLogs_query_PM_OUT_chk_stmt;
                        
                        if($studLogs_query_PM_OUT_chk->rowCount()>0 AND $studLogs_query_AM_IN->rowCount()>0){ }else{ ?>
                        
                        <p style="margin: 0px;">--:--</p>   
                        
                        <?php } } ?>
                        
                        
                        </td>
                        
                        
                        <!-- PM IN -->
                        <td>
                        <?php
                        $studLogs_query_PM_IN_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM IN' AND logDate = :logDate");
                        $studLogs_query_PM_IN_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_query_PM_IN = $studLogs_query_PM_IN_stmt;
                        $studLogs_PM_IN_row=$studLogs_query_PM_IN->fetch();
                        ?>
                        
                        <?php
                        if($studLogs_query_PM_IN->rowCount()>0){ ?>
                        
                        <!-- time in seconds PM_IN -->
                        <?php
                        
                        $str_time_pm_in= date("H:i:s", strtotime($studLogs_PM_IN_row['logTime']));
                        $str_time_pm_in = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_in);
                        sscanf($str_time_pm_in, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_time_pm_in = ($hours * 3600) + $minutes * 60 + $seconds;
                        
                        ?>
                        
                        <?php
                        if($studLogs_PM_IN_row['late_status']==='on'){
                            
                            $sched_stmt = $conn->prepare("SELECT pm_IN_co FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
                            $sched_stmt->execute([
                              ':school_id' => $school_id,
                              ':do_id' => $studData_row['do_id'],
                              ':shift_id' => $studData_row['shift_id'],
                              ':day' => $dayName
                            ]);
                            $sched_query = $sched_stmt;
                            $sq_row=$sched_query->fetch();
                     
                            $str_time_sched_pm_in_late= date("H:i:s", strtotime($sq_row['pm_IN_co']));
                            $str_time_sched_pm_in_late = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_in_late);
                            sscanf($str_time_sched_pm_in_late, "%d:%d:%d", $hours, $minutes, $seconds);
                            $time_seconds_time_pm_in_late = ($hours * 3600) + $minutes * 60 + $seconds;
                            
                            $pm_in_late_min=($time_seconds_time_pm_in-$time_seconds_time_pm_in_late)/60;
                            
                            $grandTotalpmLateMin=$grandTotalpmLateMin+$pm_in_late_min;
                            
                            $pmLateCtr=$pmLateCtr+1;
                            $pmPresentCtr=$pmPresentCtr+1;
                            
                            
                            $dailyLate=$dailyLate+$pm_in_late_min;
                            ?>
                            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Late [ <?php echo $studLogs_PM_IN_row['logTime']; ?> ]</p>
                        <?php }else{ 
                            $dailyLate=$dailyLate+0;
                            $pmPresentCtr=$pmPresentCtr+1;
                            
                            ?>
                            <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_PM_IN_row['logTime']; ?> ]</p>
                        <?php } ?>
                     
                        <?php }else{ $time_seconds_time_pm_in=0; ?>
                        
                        <?php
                        
                        $studLogs_query_PM_OUT_chk_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM OUT' AND logDate = :logDate");
                        $studLogs_query_PM_OUT_chk_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_query_PM_OUT_chk = $studLogs_query_PM_OUT_chk_stmt;
                        
                        if($studLogs_query_PM_OUT_chk->rowCount()>0 AND $studLogs_query_AM_IN->rowCount()>0){ $pmPresentCtr=$pmPresentCtr+1; }else{ 
                            
                            $pmAbsentCtr=$pmAbsentCtr+1;
                            
                            ?>
                        
                        <p style="margin: 0px;">--:--</p>   
                        
                        <?php } } ?>
                        
                        </td>
                        
                        
                        <!-- PM OUT -->
                        <td>
                        <?php
                        $studLogs_query_PM_OUT_stmt = $conn->prepare("SELECT * FROM personnel_logs WHERE RFTag_id = :RFTag_id AND logFlow = 'PM OUT' AND logDate = :logDate");
                        $studLogs_query_PM_OUT_stmt->execute([':RFTag_id' => $RFTag_id, ':logDate' => $logDateCtr]);
                        $studLogs_query_PM_OUT = $studLogs_query_PM_OUT_stmt;
                        $studLogs_PM_OUT_row=$studLogs_query_PM_OUT->fetch();
                        ?>
                        
                        <?php
                        if($studLogs_query_PM_OUT->rowCount()>0){ 
                        
                        $str_time_pm_out= date("H:i:s", strtotime($studLogs_PM_OUT_row['logTime']));
                        $str_time_pm_out = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_pm_out);
                        sscanf($str_time_pm_out, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds_time_pm_out = ($hours * 3600) + $minutes * 60 + $seconds;
                         
                        if($studLogs_PM_OUT_row['late_status']=="on"){
                            
                            $sched_stmt = $conn->prepare("SELECT pm_OUT FROM time_schedules WHERE school_id = :school_id AND do_id = :do_id AND shift_id = :shift_id AND day = :day");
                            $sched_stmt->execute([
                              ':school_id' => $school_id,
                              ':do_id' => $studData_row['do_id'],
                              ':shift_id' => $studData_row['shift_id'],
                              ':day' => $dayName
                            ]);
                            $sched_query = $sched_stmt;
                            $sq_row=$sched_query->fetch();
                            
                            $str_time_sched_pm_out_utime= date("H:i:s", strtotime($sq_row['pm_OUT']));
                            $str_time_sched_pm_out_utime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time_sched_pm_out_utime);
                            sscanf($str_time_sched_pm_out_utime, "%d:%d:%d", $hours, $minutes, $seconds);
                            $time_seconds_time_pm_out_utime = ($hours * 3600) + $minutes * 60 + $seconds;
                            
                            $pm_out_utime_min=($time_seconds_time_pm_out_utime-$time_seconds_time_pm_out)/60;
                            
                            $grandTotalpmUTimeMin=$grandTotalpmUTimeMin+$pm_out_utime_min;
                            
                            $pmUTimeCtr=$pmUTimeCtr+1;
                            
                            $dailyUTime=$dailyUTime+$pm_out_utime_min;
                                
                        ?>
                            <p style="background-color: #ffe57e; margin: 0px;">&nbsp;<i class="fa fa-check"></i>&nbsp;&nbsp;Undertime [ <?php echo $studLogs_PM_OUT_row['logTime']; ?> ]</p>
                        <?php }else{
                            
                        $dailyUTime=$dailyUTime+0;
                        
                        ?>
                            <p style="background-color: white; margin: 0px;"><i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ <?php echo $studLogs_PM_OUT_row['logTime']; ?> ]</p>
                        <?php } ?>
                        
                        
                        <!-- time in seconds PM_OUT -->
                        <?php }else{ $time_seconds_time_pm_out=0; ?>
                        
                            <p style="margin: 0px;">--:--</p>  
                             
                        <?php } ?>
                        
                        </td>
                        
                        
                        <?php
                        $SC_stmt3 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
                        $SC_stmt3->execute([':completeDate' => $logDateCtr]);
                        $SC_query3 = $SC_stmt3;
                        if($SC_query3->rowCount()>0){
                            
                        ?>
                        <!-- Late -->
                        <td rowspan="2">
                        
                        <?php echo $dailyLate.' minute(s)'; ?>
                    
                        </td>
                        <!-- end Late -->
                        
                        
                        <!-- Undertime -->
                        <td rowspan="2">
                        <?php echo $dailyUTime.' minute(s)'; ?>
                        </td>
                        <!-- end Undertime -->
                      <?php }else{?>
                        <!-- Late -->
                        <td>
                        
                        <?php echo $dailyLate.' minute(s)'; ?>
                    
                        </td>
                        <!-- end Late -->
                        
                        
                        <!-- Undertime -->
                        <td>
                        <?php echo $dailyUTime.' minute(s)'; ?>
                        </td>
                        <!-- end Undertime -->
                      <?php } ?>
                      
                        
                        <?php } } } ?>
                        
                      <?php
                      $SC_stmt4 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
                      $SC_stmt4->execute([':completeDate' => $logDateCtr]);
                      $SC_query4 = $SC_stmt4;
                          
                          if($SC_query4->rowCount()>0){
                         
                      ?>
                        
                     
                      <?php }else{ ?>
                      
                       
                      
                      <?php } ?>
                      </tr>
                      
                      <?php
                      $SC_stmt2 = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status = 'Display to DTR'");
                      $SC_stmt2->execute([':completeDate' => $logDateCtr]);
                      $SC_query2 = $SC_stmt2;
                          
                          if($SC_query2->rowCount()>0){
                          $SC_row2=$SC_query2->fetch();
                      ?>
                      <tr class="log-row-extra" data-parent-log-date="<?php echo htmlspecialchars($logDateCtr, ENT_QUOTES, 'UTF-8'); ?>">
                       
                        <td colspan="4" style="background-color: #b8ffd9;"><center><small><?php echo strtoupper($SC_row2['event_title']); ?></small></center></td>
                     
                      </tr>
                      <?php } ?>
                    <?php } ?>
                    
                    
                    <?php
                    
                    $grandTotalLateMin = $grandTotalamLateMin + $grandTotalpmLateMin;
                    $totalLateMinInt = (int)round($grandTotalLateMin);
                    $final_lateHr = str_pad((string)floor($totalLateMinInt / 60), 2, '0', STR_PAD_LEFT);
                    $final_lateMin = str_pad((string)($totalLateMinInt % 60), 2, '0', STR_PAD_LEFT);
                     
                    
                    
                    $grandTotalUTimeMin = $grandTotalamUTimeMin + $grandTotalpmUTimeMin;
                    $totalUTimeMinInt = (int)round($grandTotalUTimeMin);
                    $final_uTimeHr = str_pad((string)floor($totalUTimeMinInt / 60), 2, '0', STR_PAD_LEFT);
                    $final_uTimeMin = str_pad((string)($totalUTimeMinInt % 60), 2, '0', STR_PAD_LEFT);
                     
                    
                    
                    ?>
                    
                    
                    <tr>
                    <td colspan="5"><strong class="pull-right">TOTAL</strong></td>
                    <td style="background-color: lightgoldenrodyellow;"><strong><?php echo $grandTotalLateMin; ?> minute(s)</strong></td>
                    <td style="background-color: lightgoldenrodyellow;"><strong><?php echo $grandTotalUTimeMin; ?> minute(s)</strong></td>
                    </tr>
                    
                    
                    
                    
                    
                    
                    <tr>
                    <td colspan="7">
                    <table class="dtr-table">
                    <thead>
                    <tr>
                    <th colspan="14"><center>M O N T H L Y &nbsp;&nbsp;&nbsp; S U M M A R Y</center></th>
                    </tr>
                    </thead>
                     
                    
                     
                    
                    <tbody>
                    
                    <tr>
                    <td colspan="3"><strong>Days Present</strong></td>
                    <td colspan="4"><strong>Late</strong></td>
                    <td colspan="4"><strong>Undertime</strong></td>
                    <td colspan="3"><strong>Days Absent</strong></td>
                    </tr>
                    
                    
                    <tr>
                    <td style="width: 7%;">AM</td>
                    <td style="width: 7%;">PM</td>
                    <td style="width: 7%;">Total</td>
                    
                    <td style="width: 4%;">AM</td>
                    <td style="width: 4%;">PM</td>
                    <td style="width: 4%;">Total #</td>
                    <td style="width: 12%;">Total Time</td>
                    
                    <td style="width: 4%;">AM</td>
                    <td style="width: 4%;">PM</td>
                    <td style="width: 4%;">Total #</td>
                    <td style="width: 12%;">Total Time</td>
                    
                    <td style="width: 7%;">AM</td>
                    <td style="width: 7%;">PM</td>
                    <td style="width: 7%;">Total</td>
                    </tr>
                    
                    
                    <tr>
                    <td><?php echo $amPresentCtr; ?></td>
                    <td><?php echo $pmPresentCtr; ?></td>
                    <td><?php echo ($amPresentCtr+$pmPresentCtr)/2 ?></td>
                    
                    <td><?php echo $amLateCtr; ?></td>
                    <td><?php echo $pmLateCtr; ?></td>
                    <td><?php echo ($amLateCtr+$pmLateCtr); ?></td>
                    <td><?php echo  $grandTotalLateMin.' min(s) or <br /> '.$final_lateHr.':'.$final_lateMin; ?> hr(s) </td>
                    
                    <td><?php echo $amUTimeCtr; ?></td>
                    <td><?php echo $pmUTimeCtr; ?></td>
                    <td><?php echo ($amUTimeCtr+$pmUTimeCtr); ?></td>
                    <td><?php echo  $grandTotalUTimeMin.' min(s) or <br /> '.$final_uTimeHr.':'.$final_uTimeMin; ?> hr(s) </td>
                    
                    <td><?php echo $amAbsentCtr; ?></td>
                    <td><?php echo $pmAbsentCtr; ?></td>
                    <td><?php echo ($amAbsentCtr+$pmAbsentCtr)/2; ?></td>
                    
                    
                    
                    <?php
                    
                    $tot_num_present=($amPresentCtr+$pmPresentCtr)/2;
                    
                    $tot_num_late=($amLateCtr+$pmLateCtr)/2;
                    $tot_TimeLate=$final_lateHr.':'.$final_lateMin;
                    
                    $tot_num_uTime=($amUTimeCtr+$pmUTimeCtr)/2;
                    $totTimeUtime=$final_lateHr.':'.$final_lateMin;
                    
                    $tot_num_absent=($amAbsentCtr+$pmAbsentCtr)/2;
                    
                    
                    
                    $YDS_stmt = $conn->prepare("SELECT yDTRs_id FROM yearly_dtr_summary WHERE personnel_id = :personnel_id AND ys_month = :ys_month AND ys_year = :ys_year");
                    $YDS_stmt->execute([':personnel_id' => $user_personnel_id, ':ys_month' => $selectedMM, ':ys_year' => $selectedYYYY]);
                    $YDS_query = $YDS_stmt;
                    
                    if($YDS_query->rowCount()>0){
                    
                    $YDS_row=$YDS_query->fetch();
                    
                    $update_yds_stmt = $conn->prepare("UPDATE yearly_dtr_summary SET
                    personnel_id = :personnel_id,
                    ys_month = :ys_month,
                    ys_year = :ys_year,
                    day_present_AM = :day_present_AM,
                    day_present_PM = :day_present_PM,
                    day_present_Total = :day_present_Total,
                    late_AM = :late_AM,
                    late_PM = :late_PM,
                    late_Total_num = :late_Total_num,
                    late_Total_mins = :late_Total_mins,
                    late_Total_time = :late_Total_time,
                    uTime_AM = :uTime_AM,
                    uTime_PM = :uTime_PM,
                    uTime_Total_num = :uTime_Total_num,
                    uTime_Total_mins = :uTime_Total_mins,
                    uTime_Total_time = :uTime_Total_time,
                    day_absent_AM = :day_absent_AM,
                    day_absent_PM = :day_absent_PM,
                    day_absent_Total = :day_absent_Total,
                    total_num_leave = :total_num_leave
                    WHERE yDTRs_id = :yDTRs_id");
                    $update_yds_stmt->execute([
                      ':personnel_id' => $user_personnel_id,
                      ':ys_month' => $selectedMM,
                      ':ys_year' => $selectedYYYY,
                      ':day_present_AM' => $amPresentCtr,
                      ':day_present_PM' => $pmPresentCtr,
                      ':day_present_Total' => $tot_num_present,
                      ':late_AM' => $amLateCtr,
                      ':late_PM' => $pmLateCtr,
                      ':late_Total_num' => $tot_num_late,
                      ':late_Total_mins' => $grandTotalLateMin,
                      ':late_Total_time' => $tot_TimeLate,
                      ':uTime_AM' => $amUTimeCtr,
                      ':uTime_PM' => $pmUTimeCtr,
                      ':uTime_Total_num' => $tot_num_uTime,
                      ':uTime_Total_mins' => $grandTotalUTimeMin,
                      ':uTime_Total_time' => $totTimeUtime,
                      ':day_absent_AM' => $amAbsentCtr,
                      ':day_absent_PM' => $pmAbsentCtr,
                      ':day_absent_Total' => $tot_num_absent,
                      ':total_num_leave' => $leaveCtr,
                      ':yDTRs_id' => $YDS_row['yDTRs_id']
                    ]);
                    
                    
                    }else{
                     
                    $insert_yds_stmt = $conn->prepare("INSERT INTO yearly_dtr_summary
                    
                    (
                    personnel_id,
                    ys_month,
                    ys_year,
                    
                    day_present_AM,
                    day_present_PM,
                    day_present_Total,
                    
                    late_AM,
                    late_PM,
                    late_Total_num,
                    late_Total_mins,
                    late_Total_time,
                    
                   	uTime_AM,
                   	uTime_PM,
                   	uTime_Total_num,
                   	uTime_Total_mins,
                   	uTime_Total_time,
                    
                    day_absent_AM,
                    day_absent_PM,
                    day_absent_Total,
                    
                    total_num_leave
                    )
                    
                    VALUES
                    
                    (
                    :personnel_id,
                    :ys_month,
                    :ys_year,
                    :day_present_AM,
                    :day_present_PM,
                    :day_present_Total,
                    :late_AM,
                    :late_PM,
                    :late_Total_num,
                    :late_Total_mins,
                    :late_Total_time,
                    :uTime_AM,
                    :uTime_PM,
                    :uTime_Total_num,
                    :uTime_Total_mins,
                    :uTime_Total_time,
                    :day_absent_AM,
                    :day_absent_PM,
                    :day_absent_Total,
                    :total_num_leave
                    )");
                    $insert_yds_stmt->execute([
                      ':personnel_id' => $user_personnel_id,
                      ':ys_month' => $selectedMM,
                      ':ys_year' => $selectedYYYY,
                      ':day_present_AM' => $amPresentCtr,
                      ':day_present_PM' => $pmPresentCtr,
                      ':day_present_Total' => $tot_num_present,
                      ':late_AM' => $amLateCtr,
                      ':late_PM' => $pmLateCtr,
                      ':late_Total_num' => $tot_num_late,
                      ':late_Total_mins' => $grandTotalLateMin,
                      ':late_Total_time' => $tot_TimeLate,
                      ':uTime_AM' => $amUTimeCtr,
                      ':uTime_PM' => $pmUTimeCtr,
                      ':uTime_Total_num' => $tot_num_uTime,
                      ':uTime_Total_mins' => $grandTotalUTimeMin,
                      ':uTime_Total_time' => $totTimeUtime,
                      ':day_absent_AM' => $amAbsentCtr,
                      ':day_absent_PM' => $pmAbsentCtr,
                      ':day_absent_Total' => $tot_num_absent,
                      ':total_num_leave' => $leaveCtr
                    ]);
                    
                    }
                    
                    
                    ?>
                
                 
                    </tr>
 
                     
                    </tbody>
                    </table>
                    
                    </td>
                    </tr>
                    </table>

                    <?php } ?>
              
 
 
  
                </div>
              </div>
              <!-- kinder End-->
                </div>
            </div>
        </div>
     </section>
      
     <?php
     
     $total_yearly_present_AM=0;
     $total_yearly_present_PM=0;
     $total_yearly_present=0;
     
     $total_yearly_late_AM=0;
     $total_yearly_late_PM=0;
     $total_yearly_late_num=0;
     $total_yearly_late_min=0;
     
     $total_yearly_uTime_AM=0;
     $total_yearly_uTime_PM=0;
     $total_yearly_uTime_num=0;
     $total_yearly_uTime_min=0;
     
     $total_yearly_absent_AM=0;
     $total_yearly_absent_PM=0;
     $total_yearly_absent=0;
     
    $YDSummary_stmt = $conn->prepare("SELECT * FROM yearly_dtr_summary WHERE personnel_id = :personnel_id AND ys_year = :ys_year");
    $YDSummary_stmt->execute([':personnel_id' => $user_personnel_id, ':ys_year' => $selectedYYYY]);
    $YDSummary_query = $YDSummary_stmt;
     while($ydsSummary_row=$YDSummary_query->fetch()){
        
        $total_yearly_present_AM=$total_yearly_present_AM+$ydsSummary_row['day_present_AM'];
        $total_yearly_present_PM=$total_yearly_present_PM+$ydsSummary_row['day_present_PM'];
        $total_yearly_present=$total_yearly_present+$ydsSummary_row['day_present_Total'];
        
        
        $total_yearly_late_AM=$total_yearly_late_AM+$ydsSummary_row['late_AM'];
        $total_yearly_late_PM=$total_yearly_late_PM+$ydsSummary_row['late_PM'];
        $total_yearly_late_min=$total_yearly_late_min+$ydsSummary_row['late_Total_mins'];
        
        
        $total_yearly_uTime_AM=$total_yearly_uTime_AM+$ydsSummary_row['uTime_AM'];
        $total_yearly_uTime_PM=$total_yearly_uTime_PM+$ydsSummary_row['uTime_PM'];
        $total_yearly_uTime_min=$total_yearly_uTime_min+$ydsSummary_row['uTime_Total_mins'];
 
        
        $total_yearly_absent_AM=$total_yearly_absent_AM+$ydsSummary_row['day_absent_AM'];
        $total_yearly_absent_PM=$total_yearly_absent_PM+$ydsSummary_row['day_absent_PM'];
        $total_yearly_absent=$total_yearly_absent+$ydsSummary_row['day_absent_Total'];
        
     }
     
     $total_yearly_late_num=$total_yearly_late_AM+$total_yearly_late_PM;
     $total_yearly_uTime_num=$total_yearly_uTime_AM+$total_yearly_uTime_PM;
     
     
     $late_in_hr=$total_yearly_late_min/60;
     
     if($late_in_hr<10){
        $late_in_hr='0'.substr($late_in_hr, 0,1);
     }else{
        $late_in_hr=substr($late_in_hr, 0,2);
     }
     
     
     $late_in_min=$total_yearly_late_min-($late_in_hr*60);
     if($late_in_min<10){
        $late_in_min='0'.substr($late_in_min, 0,1);
     }else{
        $late_in_min=substr($late_in_min, 0,2);
     }
     
     $uTime_in_hr=$total_yearly_uTime_min/60;
     
     if($uTime_in_hr<10){
        $uTime_in_hr='0'.substr($uTime_in_hr, 0,1);
     }else{
        $uTime_in_hr=substr($uTime_in_hr, 0,2);
     }
     
     
     $uTime_in_min=$total_yearly_uTime_min-($uTime_in_hr*60);
     if($uTime_in_min<10){
        $uTime_in_min='0'.substr($uTime_in_min, 0,1);
     }else{
        $uTime_in_min=substr($uTime_in_min, 0,2);
     }
     
    include('quick_count_user.php'); ?>

  <script>
    (function () {
      function parseLogDate(mmddyyyy) {
        if (!mmddyyyy || mmddyyyy.indexOf('/') === -1) {
          return null;
        }
        var parts = mmddyyyy.split('/');
        if (parts.length !== 3) {
          return null;
        }
        var month = parseInt(parts[0], 10);
        var day = parseInt(parts[1], 10);
        var year = parseInt(parts[2], 10);
        if (!month || !day || !year) {
          return null;
        }
        return new Date(year, month - 1, day);
      }

      function parseInputDate(yyyymmdd, isEndDate) {
        if (!yyyymmdd || yyyymmdd.indexOf('-') === -1) {
          return null;
        }
        var parts = yyyymmdd.split('-');
        if (parts.length !== 3) {
          return null;
        }
        var year = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10);
        var day = parseInt(parts[2], 10);
        if (!year || !month || !day) {
          return null;
        }
        if (isEndDate) {
          return new Date(year, month - 1, day, 23, 59, 59, 999);
        }
        return new Date(year, month - 1, day, 0, 0, 0, 0);
      }

      function applyMonthlyLogFilters() {
        var dateFromEl = document.getElementById('dateFrom');
        var dateToEl = document.getElementById('dateTo');
        var dayTypeEl = document.getElementById('dayTypeFilter');
        var statusEl = document.getElementById('statusFilter');
        var keywordEl = document.getElementById('keywordFilter');

        if (!dateFromEl || !dateToEl || !dayTypeEl || !statusEl || !keywordEl) {
          return;
        }

        var fromDate = parseInputDate(dateFromEl.value, false);
        var toDate = parseInputDate(dateToEl.value, true);
        var dayType = dayTypeEl.value;
        var status = statusEl.value;
        var keyword = (keywordEl.value || '').toLowerCase().trim();

        var rows = document.querySelectorAll('#monthlyLogTable .log-row');
        rows.forEach(function (row) {
          var logDateRaw = row.getAttribute('data-log-date') || '';
          var rowDate = parseLogDate(logDateRaw);
          var rowText = (row.textContent || '').toLowerCase();
          var hasMissing = rowText.indexOf('--:--') !== -1;
          var hasLate = rowText.indexOf('late [') !== -1;
          var hasUndertime = rowText.indexOf('undertime [') !== -1;
          var isWeekend = rowText.indexOf('s a t u r d a y') !== -1 || rowText.indexOf('s u n d a y') !== -1;
          var hasMergedCell = row.querySelector('td[colspan="6"], td[colspan="7"]') !== null;
          var isLeaveOrEvent = hasMergedCell && !isWeekend;

          var showRow = true;

          if (fromDate && rowDate && rowDate < fromDate) {
            showRow = false;
          }
          if (toDate && rowDate && rowDate > toDate) {
            showRow = false;
          }

          if (dayType === 'weekday' && isWeekend) {
            showRow = false;
          }
          if (dayType === 'weekend' && !isWeekend) {
            showRow = false;
          }

          if (status === 'complete' && (hasMissing || isLeaveOrEvent)) {
            showRow = false;
          }
          if (status === 'missing' && !hasMissing) {
            showRow = false;
          }
          if (status === 'late' && !hasLate) {
            showRow = false;
          }
          if (status === 'undertime' && !hasUndertime) {
            showRow = false;
          }
          if (status === 'leave_event' && !isLeaveOrEvent) {
            showRow = false;
          }

          if (keyword && rowText.indexOf(keyword) === -1) {
            showRow = false;
          }

          row.style.display = showRow ? '' : 'none';

          var extraRow = document.querySelector('#monthlyLogTable .log-row-extra[data-parent-log-date="' + logDateRaw + '"]');
          if (extraRow) {
            extraRow.style.display = showRow ? '' : 'none';
          }
        });
      }

      var applyBtn = document.getElementById('applyLogFilters');
      if (applyBtn) {
        applyBtn.addEventListener('click', applyMonthlyLogFilters);
      }

      var resetBtn = document.getElementById('resetLogFilters');
      if (resetBtn) {
        resetBtn.addEventListener('click', function () {
          var dateFromEl = document.getElementById('dateFrom');
          var dateToEl = document.getElementById('dateTo');
          var dayTypeEl = document.getElementById('dayTypeFilter');
          var statusEl = document.getElementById('statusFilter');
          var keywordEl = document.getElementById('keywordFilter');

          if (dateFromEl) {
            dateFromEl.value = '<?php echo htmlspecialchars((string)$monthStartDate, ENT_QUOTES, 'UTF-8'); ?>';
          }
          if (dateToEl) {
            dateToEl.value = '<?php echo htmlspecialchars((string)$monthEndDate, ENT_QUOTES, 'UTF-8'); ?>';
          }
          if (dayTypeEl) {
            dayTypeEl.value = 'all';
          }
          if (statusEl) {
            statusEl.value = 'all';
          }
          if (keywordEl) {
            keywordEl.value = '';
          }

          applyMonthlyLogFilters();
        });
      }

      applyMonthlyLogFilters();
    })();
  </script>

  <?php include('footer.php'); ?>

  </div>

  <?php include('scripts_files.php'); ?>

  </body>
  </html>