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
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Employment Status List</li>
          </ul>
        </div>
      </div>

      <style>
      .page-title-block { margin-bottom: 18px; }
      .page-title-block h2 { margin-bottom: 4px; font-weight: 700; color: #243447; }
      .page-title-block p { margin-bottom: 0; color: #6b7a88; }
      </style>

      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row page-title-block align-items-center">
            <div class="col-lg-8 col-md-8">
              <h2>Employment Status</h2>
              <p>Manage appointment status and classification settings</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right">
              <a style="color: white !important;" data-toggle="modal" data-target="#addEmpStat" href="#" class="btn btn-primary"><i class="fa fa-plus"></i> Add Employment Status</a>
            </div>
          </div>
        </div>
      </section>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
     
          <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Employment Status DataTable</h5>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
               
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                      <thead>
                        <tr>
                          <th>Appointment Status</th>
                          <th>Position Class</th>
                          <th>Type</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php 
                            
                            $subjK_query = $conn->query("SELECT * FROM emp_status ORDER BY emp_stat_name ASC") or die(mysql_error());
                            while ($subjK_row = $subjK_query->fetch()) 
                            {  ?>
           
                        <tr>
                        
                          <td <?php if($subjK_row['status']==='Active'){ ?> style="color: green;" <?php }else{ ?> style="color: red;" <?php } ?>><?php echo $subjK_row['emp_stat_name']; ?></td>
                          
                          <td <?php if($subjK_row['status']==='Active'){ ?> style="color: green;" <?php }else{ ?> style="color: red;" <?php } ?>><?php echo $subjK_row['position_class']; ?></td>
                          
                          <td <?php if($subjK_row['status']==='Active'){ ?> style="color: green;" <?php }else{ ?> style="color: red;" <?php } ?>><?php echo $subjK_row['status']; ?></td>
                          
                          <td>
                          
                         
                          <a style="color: white !important;" data-toggle="modal" data-target="#editEmpStat<?php echo $subjK_row['empStat_id']; ?>" href="#" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteEmpStat<?php echo $subjK_row['empStat_id']; ?>" href="#" class="btn btn-danger"><i class="fa fa-times"></i></a>
                          
                          </td>
                        </tr>
                      
                  
                  
                  
                  <!-- delete Class Modal -->
                  <div id="deleteEmpStat<?php echo $subjK_row['empStat_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="empStat_id" value="<?php echo $subjK_row['empStat_id']; ?>" type="hidden" />
                      <input name="emp_stat_name" value="<?php echo $subjK_row['emp_stat_name']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Delete Status of Appointment</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                           
                        <h4>Are you sure you want to delete...</h4>
                        <p>Status of Appointment: <?php echo $subjK_row['emp_stat_name']; ?></p>
                        <p>Position Class: <?php echo $subjK_row['position_class']; ?></p>
                        <p>Type: <?php echo $subjK_row['status']; ?></p>
                         
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                          <button name="deleteEmpStatus" type="submit" class="btn btn-danger">Yes</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end delete Class Modal -->
                  
                  <!-- edit Class Modal -->
                  <div id="editEmpStat<?php echo $subjK_row['empStat_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="empStat_id" value="<?php echo $subjK_row['empStat_id']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Status of Appointment</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                            
                         
                            <div class="form-group row">
                            
                            <label class="col-sm-3 form-control-label">Status</label>
                              <div class="col-sm-9">
                                <input value="<?php echo $subjK_row['emp_stat_name']; ?>" name="emp_stat_name" type="text" class="form-control" placeholder="Employment status name..." />
                              </div>
                              
                            <label class="col-sm-3 form-control-label">Type</label>
                              <div class="col-sm-9">
                                <select name="status" class="form-control">
                                <option><?php echo $subjK_row['status']; ?></option>
                                <option>Active</option>
                                <option>Separated</option>
                                <option>-</option>
                                </select>
                              </div>
                               
                              <label class="col-sm-3 form-control-label">Position Class</label>
                              <div class="col-sm-9">
                                <select name="position_class" class="form-control">
                                <option><?php echo $subjK_row['position_class']; ?></option>
                                <option>Career Positions</option>
                                <option>Non-Career Positions</option>
                                <option>-</option>
                                </select>
                              </div>
                              
                            </div> 
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="updateEmpStatus" type="submit" class="btn btn-primary">Update</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end edit Class Modal -->
                  
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
        
        <?php include('add_dept_des_gass_modal.php'); ?>
                  
      </section>
      
 
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
  </body>
</html>