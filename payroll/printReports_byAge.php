<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
 
    if(isset($_POST['ageFilter'])){
        
    $ageFrom=$_POST['ageFrom'];
    $ageTo=$_POST['ageTo'];
    $empStat_id=$_POST['empStat_id'];
    
    }else{
        
    $ageFrom=18;
    $ageTo=75;
    $empStat_id=0;
    
    }  
    
   ?>
  
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Personnel Reports: List by Age</li>
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
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="font-weight: bold !important;">REPORTS</strong></a>
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                    
                    <table>
                    <tr>
                    <td style="background-color: white;  border: none;">
                    
                        <div class="dropdown" style="margin-left: 8px;"><a href="printReports.php" class="dropbtn" style="color: white;">ATTENDANCE REPORTS</a></div>
                        
                        <div class="dropdown" style="margin-left: 8px;">
                        
                          <button class="dropbtn">PERSONNEL REPORTS</button>
                          
                          <div class="dropdown-content">
                            <a href="printReports_byAge.php?crw=AGE">Age with Date of Birth</a>
                            <a href="printReports_byEduc.php?crw=EDUCATION">Educational Attainment</a>
                            <a href="printReports_bySeminar.php?crw=SEMINAR">Seminars Attended</a>
                            <a href="printReports_byService.php?crw=SERVICE">Date Hired with No. of Years</a>
                          </div>
                          
                        </div>
                        
                        <div class="dropdown" style="margin-left: 8px;">
                        
                          <button class="dropbtn">COMPANY REPORTS</button>
                          
                          <div class="dropdown-content">
                            <a href="#">Calendar</a>
                          </div>
                          
                        </div>
                        
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="background-color: white;  border: none;">
                    <strong style="margin-left: 8px; font-size: 18px;">PERSONNEL REPORTS: AGE WITH DATE OF BIRTH</strong>
                    </td>
                    </tr>
                    </table>
                
                <?php
                error_reporting(0);
                $pDataAge_query = $conn->query("SELECT personnel_id, bdMM, bdDD, bdYYYY FROM personnels ORDER BY age DESC") or die(mysql_error());
                while($pDA_row=$pDataAge_query->fetch()){
                
                if($pDA_row['age']==0){
                    
                }else{
                    
                    $birthDate = $pDA_row['bdMM'].'/'.$pDA_row['bdDD'].'/'.$pDA_row['bdYYYY'];
                
                
                      //date in mm/dd/yyyy format; or it can be in other formats as well
                      
                      //explode the date to get month, day and year
                      $birthDate = explode("/", $birthDate);
                      //get age from date or birthdate
                      $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
                        ? ((date("Y") - $birthDate[2]) - 1)
                        : (date("Y") - $birthDate[2]));
                   
                    if($age<80){
                        $conn->query("UPDATE personnels SET age='$age' WHERE personnel_id='$pDA_row[personnel_id]'");
                    }else{
                        
                    }
                    
       
                }
                
                
                }
                ?>
                
                <table style="border: none;">
                <tr>
                
                <form method="POST"> 
                <td style="border: none; background-color: white;  text-align: right;">
                <strong style="font-weight: bold;">Filter Age Bracket</strong>
                </td>
                
                <td style="border: none; background-color: white;">
                <small>From Age</small>
                <input name="ageFrom" type="number" min="18" max="75" step="1" value="<?php echo $ageFrom; ?>" class="form-control"> 
                </td>
                
                <td style="border: none; background-color: white;">
                <small>To Age</small>
                <input name="ageTo" type="number" min="18" max="75" step="1" value="<?php echo $ageTo; ?>" class="form-control"> 
                </td>
                
                <td style="border: none; background-color: white;">
                <small class="form-text">Status of Appointment</small>
                <select name="empStat_id" class="form-control">
                
                <?php if($empStat_id>0){
                    $emp_stat2_query = $conn->query("SELECT * FROM emp_status WHERE empStat_id='$empStat_id'");
                    $es2_row=$emp_stat2_query->fetch();
                ?>
                    <option value="<?php echo $es2_row['empStat_id']; ?>" <?php if($es2_row['status']==='Active'){ ?> style="color: green;" <?php }else{ ?> style="color: red;" <?php } ?>><?php echo $es2_row['emp_stat_name']; ?></option>
                    <option value="0">All</option>
                <?php }else{ ?>
                    <option value="0">All</option>
                <?php } ?>
                
                <?php
                $emp_stat_query = $conn->query("SELECT * FROM emp_status ORDER BY emp_stat_name ASC");
                while($es_row=$emp_stat_query->fetch()){
                ?>
                <option value="<?php echo $es_row['empStat_id']; ?>" <?php if($es_row['status']==='Active'){ ?> style="color: green;" <?php }else{ ?> style="color: red;" <?php } ?>><?php echo $es_row['emp_stat_name']; ?></option>
                <?php } ?>
                </select>
                
                </td>
                <td style="border: none; background-color: white;">
                <br />
                <button name="ageFilter" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                
                </form>  
 
                <a data-toggle="modal" data-target="#print_filter" style="color: white;" class="btn btn-info"><i class="fa fa-print"></i> Print</a>
 
                <!-- report filter Modal -->
                  <div id="print_filter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="checkReportFilter.php?ageFrom=<?php echo $ageFrom; ?>&ageTo=<?php echo $ageTo; ?>&empStat_id=<?php echo $empStat_id; ?>" method="POST">
      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">PRINT AGE BRACKET: <?php echo $ageFrom; ?> TO <?php echo $ageTo; ?></h5>
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
                          <a href="#" data-dismiss="modal" class="btn btn-secondary" style="color: white;">Cancel</a>
                          <button name="print_filter_byAge" type="submit" class="btn btn-primary">Print</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end report filter Modal -->
                  
                </td>
                
                </tr>
                
                
                </table>
                
                  
                <hr />
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                    
                      <thead>
                        <tr>
                          <th>Personnel</th>
                          <th>Office - Designation</th>
                          <th>Sex</th>
                          <th>Date of Birth</th>
                          <th>Age</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      
                      if($empStat_id>0){
                        
                        //$printDataAge_query = $conn->query("SELECT personnel_id, lname, fname, mname, suffix, age, do_id, des_id, empStat_id, bdMM, bdDD, bdYYYY FROM personnels WHERE sex='Male' AND (age BETWEEN '$ageFrom' AND '$ageTo') AND (separation_date IS NULL) AND empStat_id='$empStat_id' ORDER BY age, lname, fname ASC") or die(mysql_error());
                        $printDataAge_query = $conn->query("SELECT personnel_id, lname, fname, mname, suffix, age, sex, do_id, des_id, empStat_id, bdMM, bdDD, bdYYYY FROM personnels WHERE (age BETWEEN '$ageFrom' AND '$ageTo') AND (separation_date IS NULL) AND empStat_id='$empStat_id' ORDER BY age, lname, fname ASC") or die(mysql_error());
                      
                      }else{
                        
                        //$printDataAge_query = $conn->query("SELECT personnel_id, lname, fname, mname, suffix, age, do_id, des_id, empStat_id, bdMM, bdDD, bdYYYY FROM personnels WHERE sex='Male' AND (age BETWEEN '$ageFrom' AND '$ageTo') AND (separation_date IS NULL) ORDER BY age, lname, fname ASC") or die(mysql_error());
                        $printDataAge_query = $conn->query("SELECT personnel_id, lname, fname, mname, suffix, age, sex, do_id, des_id, empStat_id, bdMM, bdDD, bdYYYY FROM personnels WHERE (age BETWEEN '$ageFrom' AND '$ageTo') AND (separation_date IS NULL) ORDER BY age, lname, fname ASC") or die(mysql_error());
                      
                      }
                      
                      while($printDA_row=$printDataAge_query->fetch()){ ?>
                      
                      <tr>
                      <td>
                      <?php
                      
                      if($printDA_row['mname']==='' OR $printDA_row['mname']==='-'){
                        $final_mname='';
                      }else{
                        $final_mname=substr($printDA_row['mname'], 0,1).". ";
                      }
                                    
                            
                      if($printDA_row['suffix']=="-")
                      {
                                    
                        echo $printDA_row['lname'].', '.$printDA_row['fname']." ".$final_mname;
                                    
                      }else{
                                        
                        echo $printDA_row['lname']." ".$printDA_row['suffix'].', '.$printDA_row['fname']." ".$final_mname;
                                    
                      } ?>
                      </td>
                      <td>
                      <?php
                      
                      $emp_stat_query1 = $conn->query("SELECT des_name from designation WHERE des_id='$printDA_row[des_id]'");
                      $es_row1=$emp_stat_query1->fetch();
                      
                      $emp_stat_query2 = $conn->query("SELECT dept_office_name from dept_offices WHERE do_id='$printDA_row[do_id]'");
                      $es_row2=$emp_stat_query2->fetch();
                      
                      echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                      
                      ?>
                      
                      </td>
                      
                      <td>
                      <?php echo $printDA_row['sex']; ?>
                      </td>
                      
                      <td><?php echo $printDA_row['bdMM']; ?>/<?php echo $printDA_row['bdDD']; ?>/<?php echo $printDA_row['bdYYYY']; ?></td>
                      
                      <td>
                      <?php
                      if($printDA_row['age']==0){ ?>
                        <a href="edit_completePersonnelData.php?dept=<?php echo $printDA_row['do_id']; ?>&personnel_id=<?php echo $printDA_row['personnel_id']; ?>" class="btn btn-warning btn-sm" style="color: white;">Set Up Date of Birth</a>
                      <?php }else{ echo $printDA_row['age']; } ?>
                      
                      </td>
                      </tr>
                    
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
        
        <?php include('add_client_comp_modal.php'); ?>
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

     
    
  </body>
</html>