<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
  
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    <?php
    $personnel_id = $_GET['personnel_id'] ?? '';
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
            <li class="breadcrumb-item active">Add Education Attainment</li>
            
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
              
              
              <form action="save_add_personnel.php?dept=<?php echo $_GET['dept']; ?>" method="POST" enctype="multipart/form-data">
 
              <input type="hidden" name="personnel_id" value="<?php echo $_GET['personnel_id']; ?>" />
              
              <!-- PERSONNEL INFORMATION     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display"> 
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder1" aria-expanded="true" aria-controls="updates-boxKinder1"><strong style="font-weight: bold !important;">ADD EDUCATIONAL ATTAINMENT</strong>
                  
                  [ <?php
                          
                 
                                    if($staff_row['suffix']=="-")
                                    {
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    echo $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
                                    
                                    } ?> ]
                  </a>
                  
                  
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder1" aria-expanded="true" aria-controls="updates-boxKinder1"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder1" role="tabpanel" class="collapse show">
                
                <div class="modal-body">
      
          
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                                <div class="row">
                                
                                 <div class="col-md-6">
                                    <select name="degree" class="form-control">
                                    <option>Elementary</option>
                                    <option>High School</option>
                                    <option>Undergrad</option>
                                    <option>Bachelors</option>
                                    <option>Masters</option>
                                    <option>Doctors</option>
                                    <option>Others</option>
                                    </select>
                                    <small class="form-text">Degree</small>
                                  </div>
                                  
                                 <div class="col-md-2">
                                    <input name="units" type="number" class="form-control"/>
                                    <small class="form-text">Units</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="year_grad" type="month" class="form-control" />
                                    <small class="form-text">Year Graduated</small>
                                  </div>
                                  
                                  <div class="col-md-12">
                                    <input name="course_details" type="text" class="form-control" />
                                    <small class="form-text">Course Description</small>
                                  </div>
                                  
                                  <div class="col-md-12">
                                    <input name="school_name" type="text" class="form-control" />
                                    <small class="form-text">School Graduated</small>
                                  </div>
                                  
                                </div>
                              </div>
                            </div>
                        </div>
   
   
            <div class="modal-footer">                        
              <a href="list_personnel_individual_details_EB.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" class="btn btn-secondary" style="color: white; float: left; margin-left: 12px;">Cancel</a>
              <button name="add_educ_bg" type="submit" class="btn btn-primary" style="float: right; margin-right: 12px;">Add</button>
           </div>
           
           </div>
         </div>
         
         </form>
            
        </div>
      </div>
      </div>        
      </section>
      
     <?php include('footer.php'); ?>
      
    </div>
    
    <!-- JavaScript files-->
    
    <script src="js/formatter.js"></script>
     <?php include('scripts_files.php'); ?>
    <script src = "js/admin.js"></script>
 
    <script>
    /*
    var mas_yg = new Formatter (document.getElementById('mas_yg'), {
      'pattern': '{{99}}/{{9999}}',
      'persistent': true
      });
    */
    </script>
     
    
  </body>
</html>