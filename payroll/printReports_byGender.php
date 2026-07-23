<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
   if(isset($_POST['genderFilter'])){
        
    $gender=$_POST['gender'];
    
    }else{
        
    $gender='ALL';
   
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
                  <table style="margin: 8px;">
                    <tr>
                    <td style="border: none; background-color: white;">
                    <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="font-weight: bold !important;">PRINT REPORTS BY:</strong></a>
                  
                    </td>
                    
                    <td style="border: none; background-color: white;">
                    
                    
                    <a class="btn btn-outline-primary" href="printReports.php?crw=DTR"> DTR Reports</a>
                    <a class="btn btn-outline-primary" href="printReports_byAge.php?crw=AGE"> Age Bracket</a>
                    <a class="btn btn-outline-primary" href="printReports_byEduc.php?crw=EDUCATION"> Educational Attainment</a>
                    <a class="btn btn-primary" style="color: white; font-weight: bold;" href="printReports_byGender.php?crw=GENDER"> Sex</a>
                    <a class="btn btn-outline-primary" href="printReports_bySeminar.php?crw=SEMINAR"> Seminars</a>
                    <a class="btn btn-outline-primary" href="printReports_byService.php?crw=SERVICE"> Employee Years of Service/Date hired</a>
                    
                    </td>
                    </tr>
                    </table>
                  
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                
                <br />
                <h3 style="margin-left: 12px;">Personnels By-Gender Data Table</h3>
                <hr />
                <form method="POST"> 
                <table>
                <tr>
                <td style="border: none; background-color: white;  text-align: right;">Filter by Gender<br /><br /></td>
                <td style="border: none; background-color: white;">
                <select name="gender" class="form-control">
                <option><?php echo $gender; ?></option>
                <option>Male</option>
                <option>Female</option>
                <option>ALL</option>
                </select>
                <small>Select Gender</small>
                </td>
                 
                <td style="border: none; background-color: white;">
                <button name="genderFilter" class="btn btn-primary">Filter</button> 
                <a style="color: white;" target="_blank" class="btn btn-info" href="printPersonnelGenderData.php?gender=<?php echo $gender; ?>"><i class="fa fa-print"></i> Print</a>
                <br /><br />
                </td>
                
                </tr>
                </table>
                </form>    
               
                <hr />
                
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    
                    <?php if($gender=='ALL'){ ?>
                    
                    <h3>List of Male Personnels</h3>
                    
                    <table id="" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                              <th>SEX</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                
                                $printDataAge_query = $conn->query("SELECT lname, fname, mname, suffix, sex, do_id, des_id FROM personnels WHERE sex='Male' AND (separation_date IS NULL) ORDER BY lname, fname ASC") or die(mysql_error());
                                while ($printDA_row=$printDataAge_query->fetch())
                                { ?>
                                    
     
               
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
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                              
                              </td>
                             
                             <td><?php echo $printDA_row['sex']; ?></td>
                            </tr>
                              
                            
                            
                             <?php } ?>
                           
                          </tbody>
                        </table>
                        
                        
                        <hr />
                        
                        <h3>List of Female Personnels</h3>

                        <table id="" class="display" style="width:100%">
                           
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                              <th>SEX</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                
                                $printDataAge_query = $conn->query("SELECT lname, fname, mname, suffix, sex, do_id, des_id FROM personnels WHERE sex='Female' AND (separation_date IS NULL) ORDER BY lname, fname ASC") or die(mysql_error());
                                while ($printDA_row=$printDataAge_query->fetch())
                                { ?>
                                    
     
               
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
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                              
                              </td>
                             
                             <td><?php echo $printDA_row['sex']; ?></td>
                            </tr>
                              
                            
                            
                             <?php } ?>
                           
                          </tbody>
                        </table>
                        
                        
                <?php }else{ ?>
                        
                        <h3>List of <?php echo $gender; ?> Personnels</h3>
                        <table id="" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                              <th>GENDER</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                
                                $printDataAge_query = $conn->query("SELECT lname, fname, mname, suffix, sex, do_id, des_id FROM personnels WHERE sex='$gender' AND (separation_date IS NULL) ORDER BY lname, fname ASC") or die(mysql_error());
                                while ($printDA_row=$printDataAge_query->fetch())
                                { ?>
                                    
     
               
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
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                              
                              </td>
                             
                             <td><?php echo $printDA_row['sex']; ?></td>
                            </tr>
                              
                            
                            
                             <?php } ?>
                           
                          </tbody>
                        </table>          
                <?php } ?>
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