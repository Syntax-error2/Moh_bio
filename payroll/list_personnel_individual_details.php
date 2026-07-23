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
    
    $emp_stat_query5 = $conn->query("SELECT * from shifts WHERE shift_id='$staff_row[shift_id]'");
    $es_row5=$emp_stat_query5->fetch();
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <?php if($session_access == 'Administrator') { ?>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo $_GET['dept']; ?>">List of Personnel</a></li>
            <?php } ?>
            <li class="breadcrumb-item active">Personnel Profile</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
            
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">
                  <h4>
                  <img src="../personnelImg/<?php echo $staff_row['img']; ?>" width="50" height="50" class="img-fluid" style="margin-bottom: 8px; border: 2px solid green;" />
                                    <?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } ?> <small style="<?php if($es_row5['type'] == 'Regular Shift'){ ?> color: green; <?php }elseif($es_row5['type'] == 'Night Shift'){ ?> color: blue; <?php }elseif($es_row5['type'] == '24 Hours Shift'){ ?> color: brown; <?php }elseif($es_row5['type'] == 'Open Time'){ ?> color: purple; <?php }else{ ?> color: red; <?php } ?>">( <?php echo $es_row5['shift_name']; ?> - <?php echo $es_row5['type']; ?> )</small>
                                    
                  </h4>
                  </a>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                 
                
                <div class="col-lg-12 mt-2 mb-2">
                <a class="btn btn-primary" style="color: white; font-weight: bold;" href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> PERSONNEL PROFILE</a>
                <a class="btn btn-outline-primary" href="list_personnel_income.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> INCOME</a>
                <a class="btn btn-outline-primary" href="list_personnel_deductions.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> DEDUCTIONS</a> 
                <a class="btn btn-outline-primary" href="list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"> PAY HISTORY</a>
                <a class="btn btn-info" style="color: white;" title="Print personnel data sheet..." href="printPersonnelDataSheet_detailed.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>&pDataReportType=PERSONAL INFORMATION" target="_blank"><i class="fa fa-print"></i></a>  
                </div>      
                
                    <div class="col-lg-12 mt-4 mb-4">
                      <table class="table table-striped">
                      
                      <tr>
                      <td style="border: none; font-size: medium; width: 25%;">
                      <?php echo $staff_row['fname']; ?><br />
                      <small>First Name</small>
                      </td>
                      
                      <td style="border: none; font-size: medium; width: 25%;">
                      <?php echo $staff_row['mname']; ?><br />
                      <small>Middle Name</small>
                      </td>
                      
                      <td style="border: none; font-size: medium; width: 25%;">
                      <?php echo $staff_row['lname']; ?><br />
                      <small>Last Name</small>
                      </td>
                      
                      <td style="border: none; font-size: medium; width: 25%;">
                      <?php echo $staff_row['suffix']; ?><br />
                      <small>Suffix</small></td>
                      </tr>
                      
                      <tr>
                      <td style="border: none; font-size: medium;"><small>Age:</small> <?php echo $staff_row['age']; ?></td>
                      <td style="border: none; font-size: medium;"><small>Sex:</small> <?php echo $staff_row['sex']; ?></td>
                      <td style="border: none; font-size: medium;" colspan="2"><small>Marital Status:</small> <?php echo $staff_row['marital_status']; ?></td>
                      </tr>
  
                      <tr>
                      <td style="border: none; font-size: medium;"><small>Date of Birth:</small> <?php echo $staff_row['bdMM'].'/'.$staff_row['bdDD'].'/'.$staff_row['bdYYYY']; ?></td>
                      <td colspan="3" style="border: none; font-size: medium;"><small>Place of Birth:</small> <?php echo $staff_row['birth_place']; ?></td>
                      </tr>
                      
                      <tr>
                      
                      <td colspan="2" style="border: none; font-size: medium;">
                      <?php echo $staff_row['address']; ?><br />
                      <small>Home Address</small>
                      </td>
                      
                      <td style="border: none; font-size: medium;">
                      <?php echo $staff_row['email']; ?><br />
                      <small>Email Address</small>
                      </td>
                      
                      <td style="border: none; font-size: medium;">
                      <?php echo $staff_row['personal_pnum']; ?><br />
                      <small>Contact Number</small>
                      </td>
                      
                      </tr>
                      
                      <tr>
                       
                      <td colspan="2" style="border: none; font-size: medium; width: 25%;">
                      <?php echo $staff_row['conPerson_fname'].' '.$staff_row['conPerson_mname'].' '.$staff_row['conPerson_lname']; ?><br />
                      <small>Contact Person's Fullname</small>
                      </td>
                      
                      <td style="border: none; font-size: medium; width: 25%;">
                      <?php echo $staff_row['conPerson_relationship']; ?><br />
                      <small>Relationship</small>
                      </td>
                      
                      <td style="border: none; font-size: medium; width: 25%;">
                      <?php echo $staff_row['emergency_pnum']; ?><br />
                      <small>Contact Number</small>
                      </td>
           
                      </tr>
                      
                      <?php
                       
                       $emp_stat_query = $conn->query("SELECT * from dept_offices WHERE do_id='$staff_row[do_id]'");
                       $es_row=$emp_stat_query->fetch();
                       
                       $emp_stat_query2 = $conn->query("SELECT * from designation WHERE des_id='$staff_row[des_id]'");
                       $es_row2=$emp_stat_query2->fetch();
                       
                       $emp_stat_query4 = $conn->query("SELECT * from emp_status WHERE empStat_id='$staff_row[empStat_id]'");
                       $es_row4=$emp_stat_query4->fetch();
                       
                       $salary_query = $conn->query("SELECT salary from service_record WHERE personnel_id='$_GET[personnel_id]' ORDER BY sr_id DESC");
                       $salary_row=$salary_query->fetch();
                       
                       /*$serv_date_from_query = $conn->query("SELECT serv_date_from from service_record WHERE personnel_id='$_GET[personnel_id]' ORDER BY sr_id DESC");
                       $serv_date_from_row=$serv_date_from_query->fetch(); */
                       
                       ?>
                       
                      <tr>
                    
                      <td colspan="2" style="border: none; font-size: medium;">
                      <?php echo $es_row['dept_office_name']; ?><br />
                      <small>Office / Department</small>
                      </td>
                      
                      <td colspan="2" style="border: none; font-size: medium;">
                      <?php echo $es_row2['des_name']; ?><br />
                      <small>Designation</small>
                      </td>
                      
                      </tr>
 
                      <tr>
                      
                      <td colspan="2" style="border: none; font-size: medium;">
                      <strong style="font-weight: bolder;">Salary Grade <?php echo $staff_row['sal_grade']; ?></strong> / <strong style="font-weight: bolder;">Step <?php echo $staff_row['sal_step']; ?></strong> | <strong style="font-weight: bolder;"> Level <?php echo $staff_row['sal_level']; ?></strong> | <strong style="font-weight: bolder;"><?php echo $staff_row['rate_per_day']; ?></strong><br />
                      <small>Salary Grade / Step | Level | Rate/Day</small>
                      </td>
                      
                      <td colspan="2" style="border: none; font-size: medium;">
                      <strong style="font-weight: bolder;"><?php echo $es_row4['emp_stat_name']; ?></strong> | <strong style="font-weight: bolder;"><?php echo $es_row4['position_class']; ?></strong> | <strong style="font-weight: bolder;"><?php echo $es_row4['status']; ?></strong><br />
                      <small>Status of Appointment | Class | Type</small>
                      </td>
                      
                      </tr>
                      
                      <tr>
                      
                      <td colspan="2" style="border: none; font-size: medium;">
                      <?php echo $staff_row['eligibility']; ?><br />
                      <small>Eligibiity</small>
                      </td>
                      
                      <!--
                      <td style="border: none; font-size: medium;">
                      <?php echo $staff_row['plantilla_num']; ?><br />
                      <small>Plantilla Number</small>
                      </td>
                      -->
                      
                      <td colspan="2" style="border: none; font-size: medium;">
                      <?php
                      if($es_row4['status'] == "Active"){
                        echo $staff_row['appointment_date'].' - Present';
                      }else{
                        echo $staff_row['appointment_date'].' - '.$staff_row['separation_date'];
                      }
                      ?>
                       
                       <?php
                        if($staff_row['appointment_date']=='' OR $staff_row['appointment_date']=='  /  /    '){
                            
                        }else{
                            
                            if($staff_row['separation_date']=='' OR $staff_row['separation_date']=='  /  /    '){
                                $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date("m/d/Y")));
                            }else{
                                $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date($staff_row['separation_date'])));
                            }
                            
                            echo '('.$diff->format('%y').' yrs.)';
                        }
                        ?><br />
                        <small>
                        <?php
                        if($staff_row['separation_date']=='' OR $staff_row['separation_date']=='  /  /    '){ ?>
                        Appointment Date - Present (No. of years)
                        <?php }else{ ?>
                        Appointment Date - Separation Date (No. of years)
                        <?php } ?>
                        </small>
                        </td>
                      </tr>
                      
                      <tr>
                    
                      <td style="border: none; font-size: medium;">
                      <?php echo $staff_row['tin_num']; ?><br />
                      <small>TIN</small>
                      </td>
                      
                      <td style="border: none; font-size: medium;">
                      <?php echo $staff_row['gsis_num']; ?><br />
                      <small>SSS/GSIS</small>
                      </td>
                      
                      <td style="border: none; font-size: medium;">
                      <?php echo $staff_row['pagibig_num']; ?><br />
                      <small>Pag-IBIG MID</small>
                      </td>
                      
                      <td style="border: none; font-size: medium;">
                      <?php echo $staff_row['philHealth_num']; ?><br />
                      <small>PhilHealth</small>
                      </td>
                      </tr>

                      </table>
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


    <?php include('scripts_files.php'); ?>


  </body>
</html>