<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
    
    if(isset($_POST['dateFilter'])){
    
    //2019-10-03
    $selectedMM=substr($_POST['dateFrom'], 5,2);
    $selectedDD=substr($_POST['dateFrom'], 8,2);
    $selectedYYYY=substr($_POST['dateFrom'], 0,4);
    $dateFrom=$selectedMM.'/'.$selectedDD.'/'.$selectedYYYY;
    $selected_date_from=$selectedYYYY."-".$selectedMM."-".$selectedDD;
    
    $selectedMMx=substr($_POST['dateTo'], 5,2);
    $selectedDDx=substr($_POST['dateTo'], 8,2);
    $selectedYYYYx=substr($_POST['dateTo'], 0,4);
    $dateTo=$selectedMMx.'/'.$selectedDDx.'/'.$selectedYYYYx;
    $selected_date_to=$selectedYYYYx."-".$selectedMMx."-".$selectedDDx;
    
    }else{
        
    $dateFrom=date('m/d/Y');
    $selected_date_from=date('m/d/Y');
    
    $dateTo=date('m/d/Y');
    $selected_date_to=date('m/d/Y');
    
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
                    <strong style="margin-left: 8px; font-size: 18px;">PERSONNEL REPORTS: SEMINARS ATTENDED</strong>
                    </td>
                    </tr>
                    </table>
                
                <form method="POST"> 
                <table>
                <tr>
                <td style="border: none; background-color: white;  text-align: right;">
                <strong style="font-weight: bold;">Filter Seminar Date:</strong>
                <td style="border: none; background-color: white;">
                <small>From</small>
                <input name="dateFrom" type="date" value="<?php echo $selected_date_from; ?>" class="form-control"> 
                </td>
                
                <td style="border: none; background-color: white;">
                <small>To</small>
                <input name="dateTo" type="date" value="<?php echo $selected_date_to; ?>" class="form-control">
                </td>
                
                <td style="border: none; background-color: white;">
                <button name="dateFilter" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                
                <a style="color: white;" target="_blank" class="btn btn-info" href="printPersonnelSeminarData.php?dateFrom=<?php echo $dateFrom; ?>&dateTo=<?php echo $dateTo; ?>"><i class="fa fa-print"></i> Print</a>
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
                              <th>PERSONNEL</th>
                              <th>TITLE</th>
                              <th>DESCRIPTION</th>
                              <th>VENUE</th>
                              <th>DATE</th>
                            </tr>
                          </thead>
                          <tbody> 
                                <?php
                                
                                //$printSeminarData_query = $conn->query("SELECT personnel_id, seminar_title, seminar_desc, seminar_venue, event_date, event_date_to FROM personnel_seminars WHERE event_date BETWEEN '$dateFrom' AND '$dateTo' ORDER BY ps_id ASC") or die(mysql_error());
                                $printSeminarData_query = $conn->query("SELECT personnel_id, seminar_title, seminar_desc, seminar_venue, event_date FROM personnel_seminars WHERE event_date BETWEEN '$dateFrom' AND '$dateTo' ORDER BY ps_id ASC") or die(mysql_error());
                                while($printSD_row=$printSeminarData_query->fetch()){  
                                
                                
                                $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$printSD_row[personnel_id]'") or die(mysql_error());
                                $staff_row = $staff_query->fetch(); ?>
     
                                    
     
               
                            <tr>
 
                            <td>
                              <?php
                              if(!empty($staff_row)){
                              if($staff_row['suffix']=="-")
                              {
                                echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                            
                              }else{
                                echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                            
                              } }else{
                                echo "<small class='badge badge-danger'>Unidentified Personnel</small>";
                              } ?>
                            </td>
                            <td><?php echo $printSD_row['seminar_title']; ?></td>
                            <td><?php echo $printSD_row['seminar_desc']; ?></td>
                            <td><?php echo $printSD_row['seminar_venue']; ?></td>
                            <td><?php echo $printSD_row['event_date']; ?> <?php //echo $printSD_row['event_date_to']; ?></td>
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