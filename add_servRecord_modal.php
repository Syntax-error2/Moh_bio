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
    $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'");
    $staff_row = $staff_query->fetch();
    ?>
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo $_GET['dept']; ?>">List of Personnel</a></li>
            <li class="breadcrumb-item active">Add Service Record</li>
            
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
              
              
              <form action="save_add_personnel.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
 
              <input type="hidden" name="personnel_id" value="<?php echo $_GET['personnel_id']; ?>" />
              
              <!-- PERSONNEL INFORMATION     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display"> 
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder1" aria-expanded="true" aria-controls="updates-boxKinder1"><strong style="font-weight: bold !important;">ADD SERVICE RECORD</strong>
                  
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
                              <h3><small>(If married woman, give also full maiden name. Ignore this form if otherwise.)</small></h3>
                              
                                <div class="row">
                                
                                  <div class="col-md-4">
                                    <input name="maid_lname" type="text" class="form-control" />
                                    <small class="form-text">Last Name</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="maid_fname" type="text" class="form-control" />
                                    <small class="form-text">First Name</small>
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="maid_mname" type="text" class="form-control" />
                                    <small class="form-text">Middle Name</small>
                                  </div> 
                                  
                                </div>
                                
                              </div>
                              
                              
                              <div class="col-sm-12">
                              <h3>SERVICES <small>(Inclusive Dates)</small></h3>
                              
                                <div class="row">
                                
                                  <div class="col-md-6">
                                    <input name="serv_date_from" type="date" class="form-control" required />
                                    <small class="form-text">From</small>
                                  </div>
                                  
                                  <div class="col-md-6">
                                    <input name="serv_date_to" type="date" class="form-control" />
                                    <small class="form-text">To</small>
                                  </div> 
                                  
                                </div>
                                
                              </div>
                              
                              
                              <div class="col-sm-12">
                              <h3>RECORD OF APPOINTMENT</h3>
                              
                                <div class="row">
                                
                                  <div class="col-md-8">
                                    <input name="roa_designation" list="DSGTN_list" type="text" class="form-control" />
                                    <small class="form-text">Designation</small>
                                                
                                                
                                                <datalist id="DSGTN_list">
                                                <?php
                                                
                                                $dsgtnList_query = $conn->query("SELECT des_name FROM designation");
                                                while($dsgtn_row = $dsgtnList_query->fetch()){ ?>
                                                
                                                <option value="<?php echo $dsgtn_row['des_name']; ?>"><?php echo $dsgtn_row['des_name']; ?></option>
                                                
                                                <?php } ?>
                                                </datalist>
                                                
                                  </div>
                                  
                                  <div class="col-md-4">
                                    <input name="roa_status" type="text" class="form-control" />
                                    <small class="form-text">Status</small>
                                  </div> 
                                  
                                </div>
                                
                              </div>
                              
                              
                              <div class="col-sm-12">
                              
                                <div class="row">
                                
                                  <div class="col-md-4">
                                    <input name="salary" type="text" class="form-control" />
                                    <small class="form-text">Salary</small>
                                  </div>
                                  
                                  <div class="col-md-8">
                                    <input name="office_appointment" list="OA_list" type="text" class="form-control" />
                                    <small class="form-text">Office of Appointment</small>
                                    
                                    <datalist id="OA_list">
                                    <?php
                                    
                                    $fnameList_query = $conn->query("SELECT dept_office_name FROM dept_offices");
                                    while($fnlq_row = $fnameList_query->fetch()){ ?>
                                    
                                    <option value="<?php echo $fnlq_row['dept_office_name']; ?>"><?php echo $fnlq_row['dept_office_name']; ?></option>
                                    
                                    <?php } ?>
                                    </datalist>
                          
                                  </div> 
                                  
                                </div>
                                
                              </div>
                              
                              
                              <div class="col-sm-12">
                              <h3>SEPARATION</h3>
                              
                                <div class="row">
                                
                                  <div class="col-md-4">
                                    <input name="separate_date" type="date" class="form-control" />
                                    <small class="form-text">Date</small>
                                  </div>
                                  
                                  <div class="col-md-8">
                                    <input name="separate_cause" type="text" class="form-control" />
                                    <small class="form-text">Cause</small>
                                  </div> 
                                  
                                </div>
                                
                              </div>
     
                              
                            </div>
                            
                            
                            
                        </div>
   
   
            <div class="modal-footer">                        
              <a href="list_personnel_individual_details_SR.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" class="btn btn-secondary" style="color: white; float: left; margin-left: 12px;">Cancel</a>
              <button name="add_servRecord" type="submit" class="btn btn-primary" style="float: right; margin-right: 12px;">Add</button>
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
    if (document.getElementById('date_from')) {
      new Formatter(document.getElementById('date_from'), {
        'pattern': '{{99}}/{{99}}/{{9999}}',
        'persistent': true
      });
    }
    if (document.getElementById('date_to')) {
      new Formatter(document.getElementById('date_to'), {
        'pattern': '{{99}}/{{99}}/{{9999}}',
        'persistent': true
      });
    }
    </script>
     
    
  </body>
</html>