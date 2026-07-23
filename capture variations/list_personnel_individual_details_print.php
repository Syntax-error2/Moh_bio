<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
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
    $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'") or die(mysql_error());
    $staff_row = $staff_query->fetch();
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
                                     
                                    
                                    
                                    
                                    
                                    
                                    
                                    ?></li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
            
            
            <?php include('encode_daily_log_modal.php'); ?>
            <?php include('updateMonthlyLog_modal.php'); ?>
            <?php include('print_monthly_attendance_modal_csf48.php'); ?>
            <?php include('print_monthly_attendance_modal.php'); ?>
            <?php include('print_monthly_LV_modal.php'); ?>
            <?php include('print_monthly_DTRNotes_modal.php'); ?>
            <?php include('print_yearly_DTRSummary_modal.php'); ?>
                        
            
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                           
                          
                    <table class="table table-bordered" style="margin: 8px; width: 98%;">
                      
                      <tbody>
                      <!-- ACTION BUTTONS -->
                      <thead> <tr> <th>
                      
                           
                          <!-- <a title="Update DTR notes..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#updateMonthlyLog<?php echo $staff_row['RFTag_id']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil-square-o"></i> ADD DTR NOTES</a>--> 
                         
                          <a title="Bind Travel Order..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#bindTO<?php echo $staff_row['RFTag_id']; ?>" href="#" class="btn btn-info btn-sm"><i class="fa fa-chain"></i> BIND TRAVEL ORDER</a> 
                         
                          <a title="Encode daily log..." style="color: black !important; margin-top: 3px;" data-toggle="modal" data-target="#encodeDL<?php echo $staff_row['RFTag_id']; ?>" href="#" class="btn btn-warning btn-sm"><i class="fa fa-clock-o"></i> ENCODE DAILY LOG</a> 
                          
                            
                            
                            <!-- REPORTS -->
                            <button data-toggle="dropdown" type="button" class="btn btn-outline-primary dropdown-toggle btn-sm" style="margin-top: 3px;"><i class="fa fa-print"></i>  REPORTS <i class="caret"></i></button>
                            
                            <div class="dropdown-menu">
                            
                            <a title="Print Civil Service Form 48..." data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> CSForm 48 <small>(Monthly)</small></a>
                            <a title="Print detailed DTR..." data-toggle="modal" data-target="#print_monthly_attendance<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> Detailed DTR <small>(Monthly)</small></a>
                            <a title="Print Log Validations history..." data-toggle="modal" data-target="#print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-image"></i> Log Validation History <small>(Monthly)</small></a>
                            <a title="Print DTR notes history..." data-toggle="modal" data-target="#print_monthly_DTRNotes<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-list"></i> DTR Notes History <small>(Monthly)</small></a>
                            <a title="Print detailed DTR..." data-toggle="modal" data-target="#print_DTRSummary<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-list"></i> DTR Summary <small>(Yearly)</small></a>
                           
                             
                            </div>
                            <!-- END REPORTS -->
                      </th> </tr> </thead>
                      
                      <thead>
                      <tr>
                      <th>
                      <a class="btn btn-outline-primary" href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> PERSONAL INFORMATION</a>
                      <a class="btn btn-outline-primary" href="list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> EDUCATIONAL BACKGROUND</a>
                      <a class="btn btn-primary" style="color: white; font-weight: bold;" href="list_personnel_individual_details_SA.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> SEMINARS ATTENDED</a> 
                      <a class="btn btn-outline-primary" href="list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> SERVICE RECORD</a>
                      <a class="btn btn-info" style="color: white;" title="Print personnel data sheet..." href="printPersonnelDataSheet.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-print"></i></a>
                      </th>
                      </tr> 
                      </thead>
                      
                      
                      <!-- PERSONAL INFORMATION -->
                      <thead> <tr> <th><a class="btn btn-primary" style="color: white;" href="edit_completePersonnelData.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-pencil"></i> PERSONAL INFORMATION</a></th> </tr> </thead>
                        <tr>
                        <td>
                          
                          <table style="width: 100%; margin: 8px;">
                          <tr>
                           
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['fname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['mname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['lname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['suffix']; ?></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">First Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Middle Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Last Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Suffix</td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['age']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['sex']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['marital_status']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">Age</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Sex</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Marital Status</td>
                          <td style="font-size: smaller; padding: 0px; border: none;"></td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['bdMM'].'/'.$staff_row['bdDD'].'/'.$staff_row['bdYYYY']; ?></td>
                          <td colspan="3" style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['birth_place']; ?></td>
                          
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">Date of Birth</td>
                          <td colspan="3" style="font-size: smaller; padding: 0px; border: none;">Place of Birth</td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['address']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['email']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['personal_pnum']; ?></td>
                          </tr> 
                          
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Home Address</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Email Address</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Contact Number</td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                           
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['conPerson_fname'].' '.$staff_row['conPerson_mname'].' '.$staff_row['conPerson_lname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['conPerson_relationship']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['emergency_pnum']; ?></td>
               
                          </tr>
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Contact Person's Fullname</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Relationship</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Contact #</td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <?php
                           
                           $emp_stat_query = $conn->query("SELECT * from dept_offices WHERE do_id='$staff_row[do_id]'");
                           $es_row=$emp_stat_query->fetch();
                           
                           $emp_stat_query2 = $conn->query("SELECT * from designation WHERE des_id='$staff_row[des_id]'");
                           $es_row2=$emp_stat_query2->fetch();
                           
                           $emp_stat_query3 = $conn->query("SELECT * from gass WHERE gass_id='$staff_row[gass_id]'");
                           $es_row3=$emp_stat_query3->fetch();
                           
                           $emp_stat_query4 = $conn->query("SELECT * from emp_status WHERE empStat_id='$staff_row[empStat_id]'");
                           $es_row4=$emp_stat_query4->fetch();
                           
                           ?>
                           
                          <tr>
                        
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row['dept_office_name']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row2['des_name']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row4['emp_stat_name']; ?></td>
                          </tr>
                          
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Office / Department</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Designation</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Employment Status</td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row3['gass_name']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['eligibility']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['plantilla_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['appointment_date']; ?>
                           
                           <?php
                            if($staff_row['appointment_date']=='' OR $staff_row['appointment_date']=='  /  /    '){
                                
                            }else{
                                $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date("m/d/Y ")));
                                echo '('.$diff->format('%y').' yrs.)';
                            }
                            ?></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">GASS</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Eligibiity</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Plantilla Number</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Appointment Date (No. of years)</td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['tin_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['gsis_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['pagibig_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">TIN</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">GSIS</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">PhilHealth</td>
                          <td style="font-size: smaller; padding: 0px; border: none;"></td>
                          </tr>
                          
                          </table>
                          
                          </td>
                          </tr>
                        
                        
                        <!-- EDUCATIONAL BACKGROUND -->
                        <thead> <tr> <th><a class="btn btn-primary" style="color: white;" href="add_educ_bg_modal.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-plus"></i> EDUCATIONAL BACKGROUND</a></th> </tr> </thead>
                        <tr>
                        <td>
                        
                     
                        <table class="table table-bordered table-striped" id="example" style="margin: 0px 8px 0px 8px; width: 98%;">
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
                                
                                $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$_GET[personnel_id]' ORDER BY eb_id ASC") or die(mysql_error());
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
                  
                  
                        </td>
                        </tr>
                        
                        <!-- SEMINARS ATTENDED -->
                        <thead> <tr> <th><a class="btn btn-primary" style="color: white;" href="add_seminars_modal.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-plus"></i> SEMINARS ATTENDED</a></th> </tr> </thead>
                        <tr>
                        <td>
                        <table class="table table-bordered table-striped" id="example2" style="margin: 0px 8px 0px 8px; width: 98%;">
                      <thead>
                        <tr>
                          <th>ACTION</th>
                          <th>TITLE</th>
                          <th>DESCRIPTION</th>
                          <th>VENUE</th>
                          <th>DATE</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                            $subjK_ctr=0;
                            
                            $ps_query = $conn->query("SELECT * FROM personnel_seminars WHERE personnel_id='$_GET[personnel_id]' ORDER BY ps_id ASC") or die(mysql_error());
                            while ($ps_row = $ps_query->fetch())
                            {
                                ?>
                                
 
           
                        <tr>
                        
                            <td style="width: 80px;">
                            <a title="Edit data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#editPersonnel_seminars<?php echo $ps_row['ps_id']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></a>
                            <a title="Delete data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deletePersonnel_seminars<?php echo $ps_row['ps_id']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                            </td>
                        
                        
                        <td><?php echo $ps_row['seminar_title']; ?></td>
                        <td><?php echo $ps_row['seminar_desc']; ?></td>
                        <td><?php echo $ps_row['seminar_venue']; ?></td>
                        <td><?php echo $ps_row['event_date']; ?></td>
                        </tr>
                        
                        <?php include('edit_personnel_seminars_modal.php'); ?>
                        
                         <?php } ?>
                       
                      </tbody>
                    </table>
                        </td>
                        </tr> 
                    
                    
                    <!-- SERVICE RECORD -->
                        <thead> <tr> <th><a class="btn btn-primary" style="color: white;" href="add_servRecord_modal.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><i class="fa fa-plus"></i> SERVICE RECORD</a></th> </tr> </thead>
                        <tr>
                        <td>
                        <table class="table table-bordered table-striped" id="example3" style="margin: 0px 8px 0px 8px; width: 98%;">
                      <thead>
                        <tr>
                          <th>ACTION</th>
                          <th>SERVICES<br /><small>From - To</small></th>
                          <th>RECORD OF APPOINTMENT<br /><small>Designation - Status</small></th>
                          <th>SALARY</th>
                          <th>OFFICE OF APPOINTMENT</th>
                          <th>SEPARATION<br /><small>Date - Cause</small></th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                            $subjK_ctr=0;
                            
                            $sr_query = $conn->query("SELECT * FROM service_record WHERE personnel_id='$_GET[personnel_id]' ORDER BY sr_id ASC") or die(mysql_error());
                            while ($sr_row = $sr_query->fetch())
                            {
                                ?>
                                
 
           
                        <tr>
                        
                            <td style="width: 80px;">
                            <a title="Edit data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#editService_record<?php echo $sr_row['sr_id']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></a>
                            <a title="Delete data..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deleteService_record<?php echo $sr_row['sr_id']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                            </td>
                        
                        <td><?php echo substr($sr_row['serv_date_from'], 5, 2).'/'.substr($sr_row['serv_date_from'], 8, 2).'/'.substr($sr_row['serv_date_from'], 0, 4).' - '.substr($sr_row['serv_date_to'], 5, 2).'/'.substr($sr_row['serv_date_to'], 8, 2).'/'.substr($sr_row['serv_date_to'], 0, 4); ?></td>
                        <td><?php echo $sr_row['roa_designation'].' - '.$sr_row['roa_status']; ?></td>
                        <td><?php echo $sr_row['salary']; ?></td>
                        <td><?php echo $sr_row['office_appointment']; ?></td>
                        <td><?php echo substr($sr_row['separate_date'], 5, 2).'/'.substr($sr_row['separate_date'], 8, 2).'/'.substr($sr_row['separate_date'], 0, 4).' - '.$sr_row['separate_cause']; ?></td>
                        </tr>
                        
                        <?php include('edit_service_record_modal.php'); ?>
                        
                         <?php } ?>
                       
                      </tbody>
                    </table>
                        </td>
                        </tr> 
                        

                      </tbody>
                    </table>
                  
                </div>
              </div>
              <!-- kinder End-->
             
            </div>
            
          </div>
        </div>
     
        
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
 
    
    
 
    <?php include('scripts_files.php'); ?>
    
    <script>
    $(document).ready(function(){
    	setInterval(function(){
    		$("#screen").load('add_student_tag.php')
        }, 250);
    });
    </script>
    
 
    <script>
    
    $('#blah').attr('src', 'img/avatar-1.jpg');
    
    function readURL(input) {

      if (input.files && input.files[0]) {
        var reader = new FileReader();
    
        reader.onload = function(e) {
          $('#blah').attr('src', e.target.result);
        }
    
        reader.readAsDataURL(input.files[0]);
      }
    }
    
    $("#imgInp").change(function() {
      readURL(this);
    });
    </script>
    
  </body>
</html>