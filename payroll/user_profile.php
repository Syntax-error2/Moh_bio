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
            
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">
                  <h4>
                  <img src="personnelImg/<?php echo $staff_row['img']; ?>" width="50" height="50" class="img-fluid" style="margin-bottom: 8px; border: 2px solid green;" />
                                    <?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } ?>
                                    
                  </h4>
                  </a>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                           
                          
                    <table class="table table-bordered" style="margin: 8px; width: 98%;">
                      
                      <tbody> 
                      
                      <!-- PERSONAL INFORMATION -->
                      <thead> <tr> <th>PERSONAL INFORMATION</th> </tr> </thead>
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
                          <td style="font-size: smaller; padding: 0px; border: none;">Contact Numbers</td>
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
                            
                            $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date("m/d/Y ")));
                            echo '('.$diff->format('%y').' yrs.)';
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
                          <td style="font-size: smaller; padding: 0px; border: none;">PAGIBIG</td>
                          <td style="font-size: smaller; padding: 0px; border: none;"></td>
                          </tr>
                          
                          <tr>
                          <td colspan="4" style="font-size: large; padding: 0px; border: none;">
                          <hr />
                          <a data-toggle="modal" data-target="#updateLoginSettings" href="#"><i class="fa fa-pencil"></i></a> LOGIN SETTINGS
                          <br /><br />
                          </td>
                          </tr>
                          <tr>
                          <td colspan="2" style="font-size: large; padding: 0px; border: none;">
                          <input type="text" readonly="true" class="form-control" value="<?php echo $user_row['username']; ?>" />
                          <small>Username</small>
                          </td>
                          
                          <td colspan="2" style="font-size: large; padding: 0px; border: none;">
                          <input type="password" readonly="true" class="form-control" value="<?php echo $user_row['username']; ?>" />
                          <small>Password</small>
                          </td>
                          </tr>
                          </table>
                          
                          </td>
                          </tr>
                        
                        
                        <!-- EDUCATIONAL BACKGROUND -->
                        <thead> <tr> <th>EDUCATIONAL BACKGROUND</th> </tr> </thead>
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
                            <td></td>
                            <td><?php echo $peb_row['degree']; ?></td>
                            <td><?php echo $peb_row['course_details']; ?></td>
                            <td><?php echo $peb_row['units']; ?></td>
                            <td><?php echo $peb_row['year_grad']; ?></td>
                            <td><?php echo $peb_row['school_name']; ?></td>
                            </tr>
                            
                            <?php //include('updateMonthlyLog_modal.php'); ?>
                            <?php //include('print_monthly_attendance_modal_csf48.php'); ?>
                            <?php //include('print_monthly_attendance_modal.php'); ?>
                            
                             <?php } ?>
                           
                          </tbody>
                        </table>
                  
                  
                        </td>
                        </tr>
                        
                        <!-- SEMINARS ATTENDED -->
                        <thead> <tr> <th>SEMINARS ATTENDED</th> </tr> </thead>
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
                        <td></td>
                        <td><?php echo $ps_row['seminar_title']; ?></td>
                        <td><?php echo $ps_row['seminar_desc']; ?></td>
                        <td><?php echo $ps_row['seminar_venue']; ?></td>
                        <td><?php echo $ps_row['event_date']; ?></td>
                        </tr>
                        
                        <?php //include('updateMonthlyLog_modal.php'); ?>
                        <?php //include('print_monthly_attendance_modal_csf48.php'); ?>
                        <?php //include('print_monthly_attendance_modal.php'); ?>
                        
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
         
                <!--update LOGIN DATA Modal -->
            
                  <div id="updateLoginSettings" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                       <form action="save_add_personnel.php?cw=UserProfile&dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST" enctype="multipart/form-data">
      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">UPDATE LOGIN SETTINGS</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
           
                      
                            <div class="form-group row">
                               
                              <div class="col-sm-12">
                              
                              <div class="row">
                              
                                <div class="col-md-12">
                                <input name="username" type="text" class="form-control" value="<?php echo $user_row['username']; ?>" required="" />
                                <small>Username</small>
                                </div>
                                
                                 <div class="col-md-12">
                                 <input name="current_password" type="password" class="form-control" required="" />
                                 <small>Password</small>
                                 </div>
                                 
                                 <div class="col-md-12">
                                 <input id="password" name="password" class="form-control" type="password" required="" />
                                 <small>New Password</small>
                                 </div>
                                 
                                 
                                 <div class="col-md-12">
                                 <input id="confirm_password" name="confirm_password" type="password" class="form-control" required="" />
                                 <small>Retype New Password</small> <br /> 
                                 <small><span id='message'></span></small>
                                 </div>
                                
                                
                              </div>
                                
                              </div>
                            </div>
                         
    
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="updateLoginSettings" type="submit" class="btn btn-primary">Update</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end update LOGIN DATA Modal -->
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
  
    </div>
    
 
    
    
 
    <?php include('scripts_files.php'); ?>
    
    
 
  </body>
</html>