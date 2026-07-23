<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
  
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  
  <?php
   
  
  $personnel_id = $_GET['personnel_id'] ?? '';
  $travel_code = $_POST['travel_code'] ?? '';

  $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
  $studData_stmt->execute([':personnel_id' => $personnel_id]);
  $studData_query = $studData_stmt;
  $sd_row=$studData_query->fetch();
  
  $to_stmt = $conn->prepare("SELECT * FROM personnel_official_travel_logs WHERE travel_code = :travel_code");
  $to_stmt->execute([':travel_code' => $travel_code]);
  $to_query = $to_stmt;
  $to_row=$to_query->fetch();
  
 
  
  ?>   

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <?php
    $staff_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
    $staff_stmt->execute([':personnel_id' => $personnel_id]);
    $staff_query = $staff_stmt;
    $staff_row = $staff_query->fetch();
    ?>
    
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo $_GET['dept']; ?>">List of Personnel</a></li>
            <li class="breadcrumb-item"><a href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>"><?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {

                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    }
                                    ?></a></li>
          <li class="breadcrumb-item active">Bind Travel Order</li>
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
                  
               
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">BIND <strong style="font-weight: bold !important;"><?php echo $sd_row['fname'].' '.$sd_row['lname']; ?></strong> TO TRAVEL ORDER</a>
                  
                  
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="background-color: yellow; padding: 8px; font-weight: bold;">TRAVEL DATE: <?php echo $to_row['travel_date']; ?></strong> <i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show" style="padding: 14px;">

                <!-- edit Time Sched Modal -->
  
                <form action="save_updateMonthlyLog.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
               
                        <div style="margin: 10px 10px 10px 12px;" class="form-group row">
        
                              <div class="col-lg-12">
                                <div class="row">
                                  <div class="col-md-12">
                                  <input name="travel_code" type="hidden" value="<?php echo $_POST['travel_code']; ?>" />
                                  <input name="RFTag_id" type="hidden" value="<?php echo $sd_row['RFTag_id']; ?>" />
                                  <input name="travel_date" type="hidden" value="<?php echo $to_row['travel_date']; ?>" />
                                  <input name="remarks" type="hidden" value="<?php echo $to_row['travel_type']; ?>" />
                                  
                                  <input name="purpose_title" type="hidden" value="<?php echo $to_row['purpose']; ?>" />
                                  <input name="description" type="hidden" value="<?php echo $to_row['description']; ?>" />
                                  <input name="location_venue" type="hidden" value="<?php echo $to_row['location']; ?>" />
                                    
                                  </div>
                                </div>
                              </div>
                              
                              <div class="col-lg-12">
                                <div class="row">
                                
                                  
                                  <div class="col-md-6">
                                    <?php echo $to_row['travel_type']; ?>
                                    <small class="form-text">Type of Travel</small>
                                  </div>
                                  
                                  <div class="col-md-6">
                                    <?php echo $to_row['description']; ?>
                                    <small class="form-text">Description</small>
                                  
                                  </div> 
                                  
                                  
                                  
                                  <div class="col-md-6">
                                  <br />
                                    <?php echo $to_row['purpose']; ?>
                                    <small class="form-text">Travel Purpose / Seminar Title</small>
                                  <br />
                                  </div>
                                  
                                  <div class="col-md-6">
                                  <br />
                                    <?php echo $to_row['location']; ?>
                                    <small class="form-text">Travel Location / Seminar Venue</small>
                                  <br />
                                  </div> 
                                  
                                  <div class="col-md-12">
                                    <h5>Binded Personnels ( TOC: <?php echo $_POST['travel_code']; ?> )</h5>
                                    <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Dept / Office</th>
                                    <th>Designation</th>
                                    </tr>
                                    </thead>
                                    
                                    <tbody>
                                    <?php
                                    $toData_stmt = $conn->prepare("SELECT personnel_id FROM personnel_official_travel_logs WHERE travel_code = :travel_code");
                                    $toData_stmt->execute([':travel_code' => $travel_code]);
                                    $toData_query = $toData_stmt;
                                    while($toBP_row=$toData_query->fetch()){
                                    
                                    
                                    if($toBP_row['personnel_id']===$_GET['personnel_id']){
                                        $disableBtn="True";    
                                    }else{
                                        $disableBtn="False";
                                    }
                                    
                                    $staff_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
                                    $staff_stmt->execute([':personnel_id' => $toBP_row['personnel_id']]);
                                    $staff_query = $staff_stmt;
                                    $staff_row = $staff_query->fetch();
                                
                                   $emp_stat_stmt = $conn->prepare("SELECT * from dept_offices WHERE do_id = :do_id");
                                   $emp_stat_stmt->execute([':do_id' => $staff_row['do_id']]);
                                   $emp_stat_query = $emp_stat_stmt;
                                   $es_row=$emp_stat_query->fetch();
                                   
                                   $emp_stat_stmt2 = $conn->prepare("SELECT * from designation WHERE des_id = :des_id");
                                   $emp_stat_stmt2->execute([':des_id' => $staff_row['des_id']]);
                                   $emp_stat_query2 = $emp_stat_stmt2;
                                   $es_row2=$emp_stat_query2->fetch();
                                    
                           
                          
                           
                                    ?>
                                    <tr>
                                    <td style="width: 80px;">
                                    <img src="personnelImg/<?php echo $staff_row['img']; ?>" width="100%" height="40" class="img-fluid" />
                                    
                                    </td>
                                    <td>
                                    
                                    <?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } ?></td>
                                    <td><?php echo $es_row['dept_office_name']; ?></td>
                                    <td><?php echo $es_row2['des_name']; ?></td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                    </table>
                                  </div>
                                  
                                  <div class="col-md-6">
                                    <input type="checkbox" name="add_201_sr" /> Add to 201 Seminar Records
                                  </div>
                                  
                                  <div class="col-md-6">
                                    
                                    <?php if($disableBtn==="False"){ ?>
                                    
                                        <button name="bindDTRLog" type="submit" class="btn btn-primary pull-right">BIND TRAVEL ORDER</button>
                          
                                    <?php }else{ ?>
                                        
                                        <a href="cancel_travel.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id'];?>&travel_code=<?php echo $_POST['travel_code']; ?>" title="Unbind travel order..." class="btn btn-danger pull-right" style="color: white;">UNBIND TO TRAVEL ORDER</a>;
                           
                                    <?php } ?>
                                    
                                  </div> 
                                  
                                </div>
                              </div>
         
                              
                            </div>
 
                            
                </form>
                               
                          <!-- end Edit Time Sched Modal -->  

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