<!DOCTYPE html>
<html>

  <?php
   include('session.php');
   include('header.php'); 
   
    $day=date("l"); //Mon-Sun
    
    if(isset($_POST['filterDate'])){
    $filterDate=$_POST['reportDate'];
     
    }else{
        
    $filterDate=date('m/d/Y');
   
    } ?>
    
    
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">
    
    <?php include('navbar_header.php');
    
    if($session_access==='User') { ?>
    <script>
        window.location = 'list_personnel_individual_details.php?dept=<?php echo $user_dept; ?>&personnel_id=<?php echo $user_personnel_id; ?>';
    </script>
    <?php }elseif($session_access==='Admin'){
    
    // quick_count removed as per user request to clean up layout
    
    } ?>
    
    <?php if($session_access==='User'){ ?>
    
    <?php }elseif($session_access==='Admin'){  ?>
    
    <?php 
    // Display notification if monthly leave credits were just processed
    if (isset($_SESSION['monthly_credits_processed'])) {
        $result = $_SESSION['monthly_credits_processed'];
        if ($result['success'] && $result['count'] > 0) {
            echo '<div class="container-fluid mt-3">';
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<strong><i class="fa fa-check-circle"></i> Monthly Leave Credits Processed!</strong><br>';
            echo htmlspecialchars($result['message']);
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button>';
            echo '</div>';
            echo '</div>';
        }
        unset($_SESSION['monthly_credits_processed']); // Clear the notification
    }
    ?>
    
    <style>
    .dashboard-heading {
        font-size: 1.25rem;
        font-weight: bold;
        color: #1a4d2e;
        border-left: 5px solid #28a745;
        padding-left: 10px;
        margin-bottom: 20px;
        margin-top: 30px;
    }
    .bwd-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 24px;
        border: none;
        margin-bottom: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .bwd-card-title {
        font-size: 1.1rem;
        font-weight: bold;
        color: #1a4d2e;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .bwd-dept-head {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px 15px;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .bwd-dept-head .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #d4edda;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .bwd-dept-head-info {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .bwd-dept-head-label {
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .bwd-dept-head-name {
        font-size: 0.85rem;
        font-weight: bold;
        color: #dc3545;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bwd-stats-row {
        display: flex;
        justify-content: space-between;
        text-align: center;
        margin-bottom: 15px;
        margin-top: auto;
    }
    .bwd-stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 33%;
    }
    .bwd-stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1a4d2e;
        line-height: 1.2;
    }
    .bwd-stat-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 5px;
    }
    .bwd-stat-label i {
        color: #28a745;
    }
    .bwd-progress-bar {
        height: 6px;
        background: #e9ecef;
        border-radius: 4px;
        display: flex;
        overflow: hidden;
    }
    .bwd-progress-male {
        background: #1a4d2e;
        height: 100%;
    }
    .bwd-progress-female {
        background: #28a745;
        height: 100%;
    }
    </style>

    <div class="container-fluid mt-3">
        <h3 class="dashboard-heading">System Overview</h3>
        <div class="row">
            <!-- Card 1: Registered Employees -->
            <div class="col-lg-3 col-md-6 mb-4">
                 <div class="bwd-card pb-3">
                     <div class="bwd-card-title text-uppercase" style="color: #6c757d; font-size: 0.8rem; letter-spacing: 0.5px;">
                         Registered Employees
                         <a href="print_all_personnel.php" target="_blank" title="Click to print all employees..." class="text-secondary" style="font-size: 1.2em; text-decoration: none;"><i class="fa fa-print" style="color: #28a745;"></i></a>
                     </div>
                     <h2 class="display-4" style="color: #28a745; font-weight: 700; margin-bottom: 20px;"><?php echo $perCtr_all; ?></h2>
                     <div class="mt-auto">
                         <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px dashed #e9ecef;">
                             <span style="color: #6c757d; font-size: 0.9rem;"><i class="fa fa-male" style="color: #28a745; width: 16px; text-align: center;"></i> Male</span>
                             <strong style="color: #28a745;"><?php echo $perCtrM_all; ?></strong>
                         </div>
                         <div class="d-flex justify-content-between">
                             <span style="color: #6c757d; font-size: 0.9rem;"><i class="fa fa-female" style="color: #28a745; width: 16px; text-align: center;"></i> Female</span>
                             <strong style="color: #28a745;"><?php echo $perCtrF_all; ?></strong>
                         </div>
                     </div>
                 </div>
            </div>

            <!-- Card 2: Active Job Status -->
            <div class="col-lg-3 col-md-6 mb-4">
                 <div class="bwd-card pb-3">
                     <div class="bwd-card-title text-uppercase" style="color: #6c757d; font-size: 0.8rem; letter-spacing: 0.5px;">
                         Active Job Status
                         <a href="printPersonnelEmpStatusDataSummary.php" target="_blank" title="Print personnels appointment summary..." class="text-secondary" style="font-size: 1.2em; text-decoration: none;"><i class="fa fa-print" style="color: #28a745;"></i></a>
                     </div>
                     <div class="mt-2">
                        <?php
                        $empStat_query = $conn->query("SELECT * FROM emp_status WHERE status='Active' ORDER BY emp_stat_name ASC");
                        while ($empStat_row = $empStat_query->fetch()) {
                            $empStatCtr_query = $conn->query("SELECT COUNT(*) FROM personnels WHERE empStat_id='".$empStat_row['empStat_id']."' AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')");
                            $count = $empStatCtr_query->fetchColumn();
                            ?>
                            <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px dashed #e9ecef;">
                                <span style="color: #6c757d; font-size: 0.9rem;">
                                    <a data-toggle="modal" data-target="#print_emp_status<?php echo $empStat_row['empStat_id']; ?>" href="#" title="Print list of <?php echo $empStat_row['emp_stat_name']; ?> personnels..." style="text-decoration: none; color: inherit;">
                                        <i class="fa fa-print" style="color: #28a745; margin-right: 8px;"></i> <?php echo $empStat_row['emp_stat_name']; ?>
                                    </a>
                                </span>
                                <strong style="color: #28a745;"><?php echo $count; ?></strong>
                            </div>
                            
                            <!-- report filter Modal -->
                            <div id="print_emp_status<?php echo $empStat_row['empStat_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                                <div role="document" class="modal-dialog">
                                    <div class="modal-content">
                                    <form action="checkReportFilter.php?empStat_id=<?php echo $empStat_row['empStat_id']; ?>" method="POST">
                                        <div class="modal-header">
                                            <h5 id="exampleModalLabel" class="modal-title">PRINT JOB STATUS: <?php echo strtoupper($empStat_row['emp_stat_name']); ?></h5>
                                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group row">
                                                <label class="col-sm-4 form-control-label">Print Output:</label>
                                                <div class="col-sm-8">
                                                    <select name="print_output" class="form-control">
                                                        <option>Male Only</option>
                                                        <option>Female Only</option>
                                                        <option>Male-Female</option>
                                                        <option>All-Mixed</option>
                                                    </select>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="modal-footer">
                                            <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                            <button name="print_filter_emp_status" type="submit" class="btn btn-primary">Print</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                            <!-- end report filter Modal -->
                            
                        <?php } ?>
                     </div>
                 </div>
            </div>

            <!-- Card 3: Separated Records -->
            <div class="col-lg-3 col-md-6 mb-4">
                 <div class="bwd-card pb-3">
                     <div class="bwd-card-title text-uppercase" style="color: #6c757d; font-size: 0.8rem; letter-spacing: 0.5px;">
                         Separated Records
                         <a href="printPersonnelEmpStatusDataSummarySeparated.php" target="_blank" title="Print separated personnels appointment summary..." class="text-secondary" style="font-size: 1.2em; text-decoration: none;"><i class="fa fa-print" style="color: #dc3545;"></i></a>
                     </div>
                     <div class="mt-2">
                        <?php
                        $empStat_query = $conn->query("SELECT * FROM emp_status WHERE status='Separated' ORDER BY emp_stat_name ASC");
                        while ($empStat_row = $empStat_query->fetch()) {
                            $empStatCtr_query = $conn->query("SELECT COUNT(*) FROM personnels WHERE empStat_id='".$empStat_row['empStat_id']."'");
                            $count = $empStatCtr_query->fetchColumn();
                            ?>
                            <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px dashed #e9ecef;">
                                <span style="color: #6c757d; font-size: 0.9rem;">
                                    <a href="printPersonnelEmpStatusDataSeparated.php?empStat_id=<?php echo $empStat_row['empStat_id']; ?>" target="_blank" title="Print list of <?php echo $empStat_row['emp_stat_name']; ?> personnels..." style="text-decoration: none; color: inherit;">
                                        <i class="fa fa-print" style="color: #dc3545; margin-right: 8px;"></i> <?php echo $empStat_row['emp_stat_name']; ?>
                                    </a>
                                </span>
                                <strong style="color: #dc3545;"><?php echo $count; ?></strong>
                            </div>
                        <?php } ?>
                     </div>
                 </div>
            </div>

            <!-- Card 4: System Configurations -->
            <div class="col-lg-3 col-md-6 mb-4">
                 <div class="bwd-card pb-3">
                     <div class="bwd-card-title text-uppercase" style="color: #6c757d; font-size: 0.8rem; letter-spacing: 0.5px;">System Configurations</div>
                     <div class="mt-2">
                         <div class="d-flex justify-content-between mb-3 pb-3" style="border-bottom: 1px dashed #e9ecef;">
                             <span style="color: #6c757d; font-size: 0.9rem;"><i class="fa fa-clock-o" style="color: #28a745; margin-right: 8px;"></i> Work Shifts Configured</span>
                             <strong style="color: #28a745;"><?php echo $shiftTotalCtr ?? '0'; ?></strong>
                         </div>
                         <div class="d-flex justify-content-between mb-3 pb-3" style="border-bottom: 1px dashed #e9ecef;">
                             <span style="color: #6c757d; font-size: 0.9rem;"><i class="fa fa-desktop" style="color: #28a745; margin-right: 8px;"></i> Registered Client CPUs</span>
                             <strong style="color: #28a745;"><?php echo $client_computerTotalCtr ?? '0'; ?></strong>
                         </div>
                     </div>
                 </div>
            </div>
        </div>

        <h3 class="dashboard-heading mt-2">Department Breakdown</h3>
        <div class="row d-flex align-items-stretch">
             
            <?php
            $dept_off_query = $conn->query("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
            while ($do_row = $dept_off_query->fetch()) 
            { 
            
            $per_ctr_stmt = $conn->prepare("SELECT COUNT(*) FROM personnels WHERE do_id = :do_id AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')");
            $per_ctr_stmt->execute([':do_id' => $do_row['do_id']]);
            $per_ctr_count = (int)$per_ctr_stmt->fetchColumn();

            $male_per_ctr_stmt = $conn->prepare("SELECT COUNT(*) FROM personnels WHERE do_id = :do_id AND sex = 'Male' AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')");
            $male_per_ctr_stmt->execute([':do_id' => $do_row['do_id']]);
            $male_per_ctr_count = (int)$male_per_ctr_stmt->fetchColumn();

            $female_per_ctr_stmt = $conn->prepare("SELECT COUNT(*) FROM personnels WHERE do_id = :do_id AND sex = 'Female' AND (separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ')");
            $female_per_ctr_stmt->execute([':do_id' => $do_row['do_id']]);
            $female_per_ctr_count = (int)$female_per_ctr_stmt->fetchColumn();
            ?>
            
            <div class="col-lg-4 mb-4">
              <div class="bwd-card">
                <div class="bwd-card-title">
                    <a href="list_personnel.php?dept=<?php echo $do_row['do_id']; ?>" style="text-decoration: none; color: #1a4d2e;" title="Proceed to <?php echo htmlspecialchars($do_row['dept_office_name']); ?> personnel's list...">
                        <?php echo htmlspecialchars($do_row['dept_office_name']); ?>
                    </a>
                    
                    <div class="dropdown">
                        <button title="Click for Office/Dept options..." data-toggle="dropdown" type="button" class="btn btn-link text-secondary p-0" style="text-decoration: none; box-shadow: none;"><i class="fa fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a title="Click to print list of <?php echo htmlspecialchars($do_row['dept_office_name']); ?> personnels..." href="printPersonnelPerDept.php?do_id=<?php echo $do_row['do_id']; ?>" target="_blank" class="dropdown-item"><i class="fa fa-users"></i> List of Personnels</a>
                            <a href="#" data-toggle="modal" data-target="#setDOHead<?php echo $do_row['do_id']; ?>" class="dropdown-item"><i class="fa fa-pencil"></i> Set Dept/Office Head</a>
                        </div>
                    </div>
                </div>
                
                <div class="bwd-dept-head">
                    <div class="avatar"></div>
                    <div class="bwd-dept-head-info">
                        <span class="bwd-dept-head-label">Department Head</span>
                        <?php
                         $officeHead_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
                         $officeHead_stmt->execute([':personnel_id' => $do_row['officeHead_id']]);
                         $oh_row=$officeHead_stmt->fetch();
                         
                            if (!empty($oh_row)){
                                $name_str = "";
                                if($oh_row['suffix']=="-") {
                                    $name_str = $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname'];
                                } else {
                                    $name_str = $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname']." ".$oh_row['suffix'];
                                }
                                echo '<span class="bwd-dept-head-name" style="color: #333;">'.htmlspecialchars($name_str).'</span>';
                            } else {
                                echo '<span class="bwd-dept-head-name">No Assigned Personnel</span>';
                            }
                        ?>
                    </div>
                </div>
                
                <div class="bwd-stats-row">
                    <div class="bwd-stat-item">
                        <span class="bwd-stat-value" style="color: #28a745;"><?php echo $male_per_ctr_count; ?></span>
                        <span class="bwd-stat-label"><i class="fa fa-male"></i> Male</span>
                    </div>
                    <div class="bwd-stat-item">
                        <span class="bwd-stat-value" style="color: #28a745;"><?php echo $female_per_ctr_count; ?></span>
                        <span class="bwd-stat-label"><i class="fa fa-female"></i> Female</span>
                    </div>
                    <div class="bwd-stat-item">
                        <span class="bwd-stat-value" style="color: #333;"><?php echo $per_ctr_count; ?></span>
                        <span class="bwd-stat-label"><i class="fa fa-users" style="color: #6c757d;"></i> Total</span>
                    </div>
                </div>
                
                <?php 
                $male_pct = 0; $female_pct = 0;
                if ($per_ctr_count > 0) {
                    $male_pct = ($male_per_ctr_count / $per_ctr_count) * 100;
                    $female_pct = ($female_per_ctr_count / $per_ctr_count) * 100;
                }
                ?>
                <div class="bwd-progress-bar mt-2">
                    <div class="bwd-progress-male" style="width: <?php echo $male_pct; ?>%;"></div>
                    <div class="bwd-progress-female" style="width: <?php echo $female_pct; ?>%;"></div>
                </div>
                
              </div>
            </div>
            
            <?php include('setDOHead_modal.php'); ?>
            
            <?php } ?>
            
        </div>
    </div>
      
      
      
      <?php } ?>
      
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
    
    
 


    
  </body>
</html>