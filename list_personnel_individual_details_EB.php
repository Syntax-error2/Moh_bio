<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   require_once('official_duty.php');
   
   ?>

  <?php
  
 
  $get_dept=$_GET['dept'];
  
  if(isset($_POST['filterPosition'])){
  $filterPosition=$_POST['filter'];
  }else{
  $filterPosition='All';
  } ?>
    
    
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <?php
    $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'");
    $staff_row = $staff_query->fetch();
    
    $emp_stat_query5 = $conn->query("SELECT * from shifts WHERE shift_id='$staff_row[shift_id]'");
    $es_row5=$emp_stat_query5->fetch();
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo $_GET['dept']; ?>">List of Personnel</a></li>
            <li class="breadcrumb-item active"><?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {

                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } 
                                     
                                    
                                    
                                    
                                    
                                    
                                    
                                    ?> - Educational Background</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
            
            
            <?php include('encode_daily_log_modal.php'); ?>
            <?php include('restDay_modal.php'); ?>
            <?php include('update_shift_single_modal.php'); ?>
            <?php include('updateMonthlyLog_modal.php'); ?>
            <?php include('print_monthly_attendance_modal_csf48.php'); ?>
            <?php include('print_monthly_attendance_modal.php'); ?>
            <?php include('print_monthly_LV_modal.php'); ?>
            
            
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center flex-wrap">
                    <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center;">
                      <img src="personnelImg/<?php echo $staff_row['img']; ?>" width="50" height="50" class="img-fluid" style="margin-right: 10px; border: 2px solid green;" />
                      <h4 style="margin: 0; display: inline;">
                        <?php
                        if($staff_row['suffix']=="-") {
                          echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                        } else {
                          echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                        }
                        ?>
                      </h4>
                    </a>

                    <button type="button" class="btn btn-outline-secondary btn-sm ml-2" data-toggle="modal" data-target="#updateShiftSingleModal" onclick="$('#updateShiftSingleModal').modal('show'); return false;" title="Click to change work shift / schedule..." style="cursor: pointer; font-size: 13px; padding: 3px 8px; font-weight: bold; <?php if($es_row5['type']==='Regular Shift'){ ?> color: green; border-color: green; <?php }elseif($es_row5['type']==='Night Shift'){ ?> color: blue; border-color: blue; <?php }elseif($es_row5['type']==='24 Hours Shift'){ ?> color: brown; border-color: brown; <?php }elseif($es_row5['type']==='Open Time'){ ?> color: purple; border-color: purple; <?php }else{ ?> color: red; border-color: red; <?php } ?>">
                      <i class="fa fa-pencil-square-o"></i> ( <?php echo $es_row5 ? htmlspecialchars($es_row5['shift_name']) : 'N/A'; ?> - <?php echo $es_row5 ? htmlspecialchars($es_row5['type']) : 'N/A'; ?> )
                    </button>
                  </div>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                <div class="col-lg-12" style="margin-top: 12px;">
                
                <a title="Encode log..." style="color: black !important; margin-top: 3px;" data-toggle="modal" data-target="#encodeDL<?php echo $staff_row['RFTag_id']; ?>" href="#" class="btn btn-warning btn-sm"><i class="fa fa-clock-o"></i> ENCODE DAILY LOG</a> 
                
                <a title="Rest day settings..." style="color: black !important; margin-top: 3px;" data-toggle="modal" data-target="#restDaySetup<?php echo $staff_row['RFTag_id']; ?>" href="#" class="btn btn-info btn-sm"><i class="fa fa-heartbeat"></i> SET REST DAY</a> 
                         
                <!-- REPORTS -->
                <button data-toggle="dropdown" type="button" class="btn btn-outline-primary dropdown-toggle btn-sm" style="margin-top: 3px;"><i class="fa fa-print"></i>  REPORTS <i class="caret"></i></button>
                            
                <div class="dropdown-menu">
                            
                <a title="Print Civil Service Form 48..." data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> CSForm 48</a>
                <a title="Print detailed DTR..." data-toggle="modal" data-target="#print_monthly_attendance<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> Detailed DTR <small>(Monthly)</small></a>
                <a title="Print Log Validations history..." data-toggle="modal" data-target="#print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-image"></i> Log Validation History <small>(Monthly)</small></a>
                
                </div>
                <!-- END REPORTS -->  
                </div>
                
                <div class="col-lg-12" style="margin-top: 12px;">
                <a class="btn btn-outline-primary" href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> PERSONAL INFORMATION</a>
                <a class="btn btn-primary" style="color: white; font-weight: bold;" href="list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> EDUCATIONAL BACKGROUND</a>
                <a class="btn btn-outline-primary" href="list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> SEMINARS ATTENDED</a>                  <a class="btn btn-outline-primary" href="list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> SERVICE RECORD</a>
                  <?php
                  $has_leave_credits = personnelHasLeaveCredits($conn, $staff_row);
                  if($has_leave_credits) { ?>
                    <a class="btn btn-outline-primary" href="list_personnel_individual_details_LC.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> LEAVE CREDITS</a>
                    <a href="javascript:void(0);" id="btnOpenCertModal" class="btn btn-info" style="color: white; margin-left: 2px;" title="Leave Credit Certification" data-toggle="modal" data-target="#certModal" onclick="$('#certModal').modal('show'); return false;"><i class="fa fa-certificate"></i></a>
                  <?php } ?>
                <a class="btn btn-info" style="color: white;" title="Print personnel data sheet..." href="printPersonnelDataSheet_detailed.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a>
                </div>
                
                <div class="col-lg-12" style="margin-top: 12px;">
                <a class="btn btn-primary" style="color: white;" href="add_educ_bg_modal.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-plus"></i> EDUCATIONAL BACKGROUND</a>
                <a class="btn btn-info" style="color: white;" title="Print personnel's Educational Background..." href="printPersonnelDataSheet_detailed_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a> 
                </div>
 
                        
                     
                        <div class="col-lg-12">
                        <div class="table-responsive" style="margin-top: 12px;">
                        <table id="" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>ACTION</th>
                              <th>DEGREE</th>
                              <th>COURSE</th>
                              <th>UNITS</th>
                              <th>YEAR GRADUATED</th>
                              <th>SCHOOL</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                $subjK_ctr=0;
                                
                                $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$_GET[personnel_id]' ORDER BY eb_id ASC");
                                while ($peb_row = $peb_query->fetch())
                                {
                                    ?>
                                    
     
               
                            <tr>
                            
                            <td style="width: 80px;">
                            <a title="Edit data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#editPersonnel_educ_bg<?php echo $peb_row['eb_id']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></a>
                            <a title="Delete data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deletePersonnel_educ_bg<?php echo $peb_row['eb_id']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                            </td>
                        
                            <td><?php echo $peb_row['degree']; ?></td>
                            <td><?php echo $peb_row['course_details']; ?></td>
                            <td><?php echo $peb_row['units']; ?></td>
                            <td><?php echo $peb_row['year_grad']; ?></td>
                            <td><?php echo $peb_row['school_name']; ?></td>
                            </tr> 
                            
                            <?php include('edit_Personnel_educ_bg_modal.php'); ?>
                            
                             <?php } ?>
                           
                          </tbody>
                        </table>
                        </div>
                        </div>
                        
                </div>
              </div>
              <!-- kinder End-->
             
            </div>
            
          </div>
        </div>
     
        
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>


    <?php if ($has_leave_credits) { include('leave_certification_modal.php'); } ?>
    <?php include('scripts_files.php'); ?>
    <script>
    $(document).on('click', '#btnOpenCertModal', function(e) {
        e.preventDefault();
        $('#certModal').modal('show');
    });
    </script>

  </body>
</html>