<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
 
    if(isset($_POST['ageFilter'])){
    $ageFrom=$_POST['ageFrom'];
    $ageTo=$_POST['ageTo'];
    }else{
    $ageFrom=0;
    $ageTo=75;
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
            <li class="breadcrumb-item active">Print Reports</li>
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
                    <strong style="margin-left: 8px; font-size: 18px;">PERSONNEL REPORTS: DATE HIRED WITH NUMBER OF YEARS</strong>
                    </td>
                    </tr>
                    </table>
                
                
                
                <?php
                
 /*
function format_interval(DateInterval $interval) {
    $result = "";
    if ($interval->y) { $result .= $interval->format("%y years "); }
    if ($interval->m) { $result .= $interval->format("%m months "); }
    if ($interval->d) { $result .= $interval->format("%d days "); }
    //if ($interval->h) { $result .= $interval->format("%h hours "); }
    //if ($interval->i) { $result .= $interval->format("%i minutes "); }
    //if ($interval->s) { $result .= $interval->format("%s seconds "); }

    return $result;
}

$first_date = new DateTime("2012-11-30 17:03:30");
$second_date = new DateTime("2022-12-21 00:00:00");

 $difference = $first_date->diff($second_date);
//echo var_dump($difference);
echo "<hr />";
echo format_interval($difference);

echo "<hr />";
*/      
                
                //error_reporting(0);
                $pDataAge_query = $conn->query("SELECT personnel_id, appointment_date FROM personnels") or die(mysql_error());
                while($pDA_row=$pDataAge_query->fetch()){
                    
                    if(substr($pDA_row['appointment_date'], 6) < 1000){
                        $ad_date = date_create("now");
                        $appoint_date = date_format($ad_date, 'Y-m-d');
                    }else{
                        $ad_date = date_create($pDA_row['appointment_date']);
                        $appoint_date = date_format($ad_date, 'Y-m-d');
                    }
                    
                    //$appoint_date=date_create("2013-03-15");
                    //$date2=date_create("2022-12-12");
                    $diff=date_diff(date_create($appoint_date), date_create("now"));
                    $diff->format("%Y Year %M Month");
                    //echo $diff->format("%Y Year %M Month");
                    //echo "<br />";
                    $num_of_yrs = substr($diff->format("%y Year %m Month"), 0, 2);
             
                    /*
                      //date in mm/dd/yyyy format; or it can be in other formats as well
                      
                      //explode the date to get month, day and year
                   $appoint_date = explode("/", $appoint_date);
                      //get age from date or birthdate
                      
                      $num_of_yrs = (date("md", date("U", mktime(0, 0, 0, $appoint_date[0], $appoint_date[1], $appoint_date[2]))) > date("md") 
                      
                      ? ((date("Y") - $appoint_date[2]) - 1) 
                      
                      : (date("Y") - $appoint_date[2]));
                   */
                   
                    if($num_of_yrs < 60){
                        $conn->query("UPDATE personnels SET num_of_yrs='$num_of_yrs' WHERE personnel_id='$pDA_row[personnel_id]'");
                        
                    }else{
                        
                    }
                    
               }
                ?>
                
                <form method="POST"> 
                <table>
                <tr>
                
                <td style="border: none; background-color: white;  text-align: right;">
                <strong style="font-weight: bold;">Filter Years of Service</strong>
                </td>
                
                <td style="border: none; background-color: white;">
                <small>From</small>
                <input name="ageFrom" type="number" min="0" max="75" step="1" value="<?php echo $ageFrom; ?>" class="form-control"> 
                
                </td>
                
                <td style="border: none; background-color: white;">
                <small>To</small>
                <input name="ageTo" type="number" min="0" max="75" step="1" value="<?php echo $ageTo; ?>" class="form-control"> 
                
                </td>
                
                <td style="border: none; background-color: white;">
                <br />
                <button name="ageFilter" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                
                
                <a style="color: white;" target="_blank" class="btn btn-info" href="printPersonnelNumYearsData.php?ageFrom=<?php echo $ageFrom; ?>&ageTo=<?php echo $ageTo; ?>"><i class="fa fa-print"></i> Print</a>
                </td>
                
                </tr>
                </table>
                </form>  
                
                
                  
                <hr />
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                 
                      <thead>
                        <tr>
                          <th>Personnel</th>
                          <th>Office - Designation</th>
                          <th>Date Hired</th>
                          <th># of Years</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $printDataAge_query = $conn->query("SELECT personnel_id, lname, fname, mname, suffix, do_id, des_id, appointment_date, num_of_yrs FROM personnels WHERE num_of_yrs BETWEEN '$ageFrom' AND '$ageTo' ORDER BY lname, fname ASC") or die(mysql_error());
                      
                      while($printDA_row=$printDataAge_query->fetch()){ ?>
                      
                      <tr>
                      <td>
                      <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname'];
                                    
                                    }else{
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname']." ".$printDA_row['suffix'];
                                    
                                    } ?>
                      </td>
                      <td>
                      <?php
                      
                      $emp_stat_query1 = $conn->query("select des_name from designation WHERE des_id='$printDA_row[des_id]'");
                      $es_row1=$emp_stat_query1->fetch();
                      
                      $emp_stat_query2 = $conn->query("select dept_office_name from dept_offices WHERE do_id='$printDA_row[do_id]'");
                      $es_row2=$emp_stat_query2->fetch();
                      
                      if(!empty($es_row1)){
                        echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                      }else{
                        echo $es_row2['dept_office_name'].' - <small class="badge badge-danger">Designation not Set</small>';
                      }
                      
                      
                      ?>
                      
                      </td>
                      <td>
                      <?php
                      if($printDA_row['appointment_date']=='' OR $printDA_row['appointment_date']=='  /  /    '){ ?>
                        <a href="edit_completePersonnelData.php?dept=<?php echo $printDA_row['do_id']; ?>&personnel_id=<?php echo $printDA_row['personnel_id']; ?>" class="btn btn-warning btn-sm" style="color: white;">Set Up Date Hired</a>
                      <?php }else{
                        
                        if(substr($printDA_row['appointment_date'], 6) < 1000){ ?>
                        <a href="edit_completePersonnelData.php?dept=<?php echo $printDA_row['do_id']; ?>&personnel_id=<?php echo $printDA_row['personnel_id']; ?>" class="btn btn-warning btn-sm" style="color: white;">Set Up Date Hired</a>
                      
                      <small class="badge badge-danger">Wrong format: <?php echo $printDA_row['appointment_date']; ?></small>
                      <?php }else{
                        echo $printDA_row['appointment_date'];
                    }
                    
                         } ?>
                      
                      </td>
                      
                      <td>
                      <?php
                      
                      echo $printDA_row['num_of_yrs'];
                     
                      ?>
                      
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
      
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

     
    
  </body>
</html>