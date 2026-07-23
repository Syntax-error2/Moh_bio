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
    
    
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li style="color: blue"><strong><?php echo $activeSchoolYear; ?></strong> | <strong><?php echo $activeSemester; ?></strong> &nbsp;</li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Complete Personnel Data</li>
          </ul>
        </div>
      </div>
      
      
      <?php
      $personnel_id_code = $_GET['personnel_id_code'] ?? '';
      $personnel_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id_code = :personnel_id_code");
      $personnel_stmt->execute([':personnel_id_code' => $personnel_id_code]);
      $personnel_query = $personnel_stmt;
      $personnel_row=$personnel_query->fetch();
      ?>
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
              
              <form action="save_add_personnel.php?dept=<?php echo $_GET['dept']; ?>" method="POST" enctype="multipart/form-data" class="standardized-form">
 
              <input type="hidden" name="personnel_id" value="<?php echo $personnel_row['personnel_id']; ?>" />
              
              <!-- PERSONNEL INFORMATION     -->
              <div id="new-updates" class="card updates recent-updated personnel-form-shell">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center personnel-form-header">
                  <h2 class="h5 display">
                  
               
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder1" aria-expanded="true" aria-controls="updates-boxKinder1"><strong style="font-weight: bold !important;">PERSONNEL INFORMATION</strong></a>
                  
                  
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder1" aria-expanded="true" aria-controls="updates-boxKinder1"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder1" role="tabpanel" class="collapse show">
 
                        <div class="modal-body personnel-category-card">
             
                        <div class="form-group row">
                            
                              <div class="col-sm-12">
                              
                              <div class="row">
                                <div class="col-md-4">
                                <input value="<?php echo $personnel_row['personnel_id_code']; ?>" name="personnel_id_code" type="text" class="form-control" required="true" />
                                <small>*Employee ID Code</small>
                                </div>
                                
                                <div class="col-md-4">
                                <input value="<?php echo $personnel_row['RFTag_id']; ?>" name="RFTag_id" type="text" class="form-control" readonly="true" />
                                <small>RFID Tag</small>
                                </div>
                                
                                
                                <div class="col-md-4">
                                     
                                    <select name="shift_id" class="form-control">
                                   
                                    <option value="0">-</option>
                                    <?php
                                    $shift_stmt = $conn->prepare("SELECT * FROM shifts ORDER BY shift_name ASC");
                                    $shift_stmt->execute();
                                    $emp_stat_query = $shift_stmt;
                                    while($es_row=$emp_stat_query->fetch()){
                                    ?>
                                    <option value="<?php echo $es_row['shift_id']; ?>"><?php echo $es_row['shift_name']; ?></option>
                                    <?php } ?>
                                    
                                    </select>
                                    <small class="form-text">Work-Hour Shift</small>
                                  </div>
                                  
                              </div>
                                
                              </div>
                            </div>
                            
          
                        
                            <div class="form-group row">
                             
                              <div class="col-sm-12">
                              
                              <div class="row">
    
                                <div class="col-md-3">
                                <input value="<?php echo $personnel_row['fname']; ?>" name="fname" type="text" class="form-control" required="true" />
                                <small class="form-text">*First Name</small>
                                </div>
                                 
                                <div class="col-md-3">
                                <input value="<?php echo $personnel_row['mname']; ?>" name="mname" type="text" class="form-control" />
                                <small class="form-text">Middle Name</small>
                                </div>
                                
               
                                <div class="col-md-4">
                                <input value="<?php echo $personnel_row['lname']; ?>" name="lname" type="text" class="form-control" required="true" />
                                <small class="form-text">*Last Name</small>
                                </div>
                                
                                
                                  <div class="col-md-2">
                                     
                                    <select name="suffix" class="form-control">
                                    <option><?php echo $personnel_row['suffix']; ?></option>
                                    <option>-</option>
                                    <option>Jr.</option>
                                    <option>III</option>
                                    <option>IV</option>
                                    </select>
                                    <small class="form-text">Suffix</small>
                                  </div>
                              </div>
                                
                              </div>
                            </div>
                            
                         
                            
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                                <div class="row">
                                  <div class="col-md-4">
                                     
                                    <select name="sex" class="form-control">
                                   
                                    <option>-</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    
                                    </select>
                                    <small class="form-text">Sex</small>
                                  </div>
                                  
                                  
                                  <div class="col-md-4">
                                    
                                    <select name="marital_status" class="form-control">
                                
                                    <option>-</option>
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Widowed</option>
                                    <option>Separated</option>
                                    
                                    </select>
                                    <small class="form-text">Satus</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="birthdate" id="bdate" type="text" class="form-control" required=""/>
                                    <small class="form-text">*Date of Birth</small>
                                  </div>
                                  
                                </div>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                                <div class="row">
                                  <div class="col-md-4">
                                    <input name="birth_place" type="text" class="form-control" required="" />
                                    <small class="form-text">*Place of Birth</small>
                                  </div>
                                  
                                  <div class="col-md-8">
                                    <input name="address" type="text" class="form-control" required="" />
                                    <small class="form-text">*Complete Address [ Street, Barangay, City/Municipality, Province ]</small>
                                  </div>
                                  
                                </div>
                              </div>
                            </div>
                            
                            
                            
                            
                            
                            
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                                <div class="row">
                                  <div class="col-md-4">
                                    <input type="email" name="email" class="form-control" />
                                    <small class="form-text">Email Address</small>
                                  </div>
                                  
                                  
                                  <div class="col-md-4">
                                    <input name="personal_pnum" id="contact_no" type="text" class="form-control" required="" />
                                    <small class="form-text">*Personal No.</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="emergency_pnum" id="contact_no2" type="text" class="form-control" />
                                    <small class="form-text">Incase of Emergency No.</small>
                                  </div>
                                  
                                </div>
                              </div>
                            </div>
                            
                            
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                                <div class="row">
                                  <div class="col-md-3">
                                     
                                    <select name="do_id" class="form-control">
                                    <?php
                                    $dept_stmt = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
                                    $dept_stmt->execute([':do_id' => $personnel_row['do_id']]);
                                    $emp_stat_query = $dept_stmt;
                                    while($es_row=$emp_stat_query->fetch()){
                                    ?>
                                    <option value="<?php echo $es_row['do_id']; ?>"><?php echo $es_row['dept_office_name']; ?></option>
                                    <?php } ?>
                                    
                                    </select>
                                    <small class="form-text">Department</small>
                                  </div>
                                  
                                  
                                  <div class="col-md-3">
                                     
                                    <select name="des_id" class="form-control">
                                    
                                    <option>-</option>
                                    <?php
                                    $designation_stmt = $conn->prepare("SELECT * FROM designation ORDER BY des_name ASC");
                                    $designation_stmt->execute();
                                    $emp_stat_query = $designation_stmt;
                                    while($es_row=$emp_stat_query->fetch()){
                                    ?>
                                    <option value="<?php echo $es_row['des_id']; ?>"><?php echo $es_row['des_name']; ?></option>
                                    <?php } ?>
                                    
                                    </select>
                                    <small class="form-text">Designation</small>
                                  </div>
                                  
                                  <div class="col-md-3">
                                    
                                    <select name="gass_id" class="form-control">
                                     
                                    <option>-</option>
                                    <?php
                                    $gass_stmt = $conn->prepare("SELECT * FROM gass ORDER BY gass_name ASC");
                                    $gass_stmt->execute();
                                    $emp_stat_query = $gass_stmt;
                                    while($es_row=$emp_stat_query->fetch()){
                                    ?>
                                    <option value="<?php echo $es_row['gass_id']; ?>"><?php echo $es_row['gass_name']; ?></option>
                                    <?php } ?>
                                    
                                    </select>
                                    <small class="form-text">GASS Rank</small>
                                  </div>
                                  
                                  
                                  <div class="col-md-3">
                                     
                                    <select name="empStat_id" class="form-control">
                                     
                                    <option>-</option>
                                    
                                    <?php
                                    $emp_status_stmt = $conn->prepare("SELECT * FROM emp_status ORDER BY emp_stat_name ASC");
                                    $emp_status_stmt->execute();
                                    $emp_stat_query = $emp_status_stmt;
                                    while($es_row=$emp_stat_query->fetch()){
                                    ?>
                                    <option value="<?php echo $es_row['empStat_id']; ?>"><?php echo $es_row['emp_stat_name']; ?></option>
                                    <?php } ?>
                                    </select>
                                    <small class="form-text">Employment Status</small>
                                  </div>
                                  
                                  
                                </div>
                              </div>
                            </div>
                            
                            
                            
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                                <div class="row">
                                
                                  <div class="col-md-4">
                                    <input name="eligibility" type="text"  class="form-control">
                                    <small class="form-text">Eligibility</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="plantilla_num" type="text"  class="form-control">
                                    <small class="form-text">Plantilla No.</small>
                                  </div> 
                                  
                                  <div class="col-md-4">
                                    <input name="appointment_date" id="appointdate" type="text"  class="form-control" required="" />
                                    <small class="form-text">*Appointment Date</small>
                                  </div> 
                                  
                                </div>
                              </div>
                            </div>
                            
                             
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                                <div class="row">
                                   
                                  <div class="col-md-4">
                                    <input name="tin_num" id="tin" placeholder="Ex: XXX-XXX-XXX" type="text" class="form-control" required="" />
                                    <small class="form-text">TIN</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="gsis_num" id="gsis" placeholder="Ex: XXX-XXX-XXX" type="text"  class="form-control" required="" />
                                    <small class="form-text">GSIS BP No.</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="pagibig_num" id="pagibig" placeholder="Ex: XXX-XXX-XXX" type="text"  class="form-control" required="" />
                                    <small class="form-text">PAGIBIG No.</small>
                                  </div>
                                  
                                  
                                </div>
                              </div>
                            </div>
                            
  
                </div>
              </div>
              <!-- End PERSONNEL INFORMATION -->
     
            </div>
            
            <div class="personnel-form-actions d-flex justify-content-between align-items-center flex-wrap">
              <a href="list_personnel.php?dept=<?php echo $_GET['dept']; ?>" class="btn btn-secondary mb-2 mb-md-0">Cancel</a>
              <button name="updatePersonnelComplete" type="submit" class="btn btn-primary">Update</button>
            </div>
            
            </form>
            
          </div>
        </div>
              
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <!-- JavaScript files-->
    
    <script src="js/formatter.js"></script>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="vendor/popper.js/umd/popper.min.js"> </script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/grasp_mobile_progress_circle-1.0.0.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"> </script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/charts-home.js"></script>
    <script src = "js/admin.js"></script>
    
    <!-- Main File-->
    <script src="js/front.js"></script>
 
    <script>
    
    var bdate = new Formatter (document.getElementById('bdate'), {
      'pattern': '{{99}}/{{99}}/{{9999}}',
      'persistent': true
      });
    
    var appointdate = new Formatter (document.getElementById('appointdate'), {
      'pattern': '{{99}}/{{99}}/{{9999}}',
      'persistent': true
      });
        
    var contanct_no = new Formatter (document.getElementById('contact_no'), {
      'pattern': '+639 {{999999999}}',
      'persistent': true
      });
      
    var contanct_no2 = new Formatter (document.getElementById('contact_no2'), {
      'pattern': '+639 {{999999999}}',
      'persistent': true
      });
      
    var pagibig = new Formatter (document.getElementById('pagibig'), {
      'pattern': '{{999}}-{{999}}-{{999}}',
      'persistent': true
      });
      
    var tin = new Formatter (document.getElementById('tin'), {
      'pattern': '{{999}}-{{999}}-{{999}}',
      'persistent': true
    });
    
    var gsis = new Formatter (document.getElementById('gsis'), {
      'pattern': '{{999}}-{{999}}-{{999}}',
      'persistent': true
    });
     
    </script>
     
    
  </body>
</html>