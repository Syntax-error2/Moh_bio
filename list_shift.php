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
            <li class="breadcrumb-item active">Shift List</li>
          </ul>
        </div>
      </div>

      <style>
      .page-title-block { margin-bottom: 18px; }
      .page-title-block h2 { margin-bottom: 4px; font-weight: 700; color: #243447; }
      .page-title-block p { margin-bottom: 0; color: #6b7a88; }
      .page-cta-group .btn { margin-left: 8px; }
      </style>

      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row page-title-block align-items-center">
            <div class="col-lg-8 col-md-8">
              <h2>Shift List</h2>
              <p>Manage shift types and assignments</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right page-cta-group">
              <a style="color: white !important;" data-toggle="modal" data-target="#addShift" href="#" class="btn btn-primary"><i class="fa fa-plus"></i> Add Shift</a>
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
                  <h5 class="mb-0">Shift DataTables</h5>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
               
                    <div class="col-lg-12">
                    
                    <div class="tab">
                      <a class="tablinks" onclick="openCity(event, 'London')">REGULAR</a>
                      <a class="tablinks" onclick="openCity(event, 'Paris')">NIGHT</a>
                      <a class="tablinks" onclick="openCity(event, 'Tokyo')">24 HOURS</a>
                    </div>
                    
                    <hr />
                    
                    <div id="London" class="tabcontent">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                    
                      <thead>
                        <tr>
                         
                          <th>Shift</th>
                          <th>Time Scope</th>
                          <th>Dept / Office</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                             
                            $subjK_query = $conn->query("SELECT * FROM shifts WHERE type='Regular Shift' ORDER BY shift_name ASC") or die(mysql_error());
                            while ($subjK_row = $subjK_query->fetch()) 
                            {
                                
                            $emp_stat_query = $conn->query("SELECT dept_office_name from dept_offices WHERE do_id='$subjK_row[do_id]'");
                            $es_row=$emp_stat_query->fetch();
                            
                            ?>
                        <tr>
                        
                          <td><?php echo $subjK_row['shift_name']; ?></td>
                          <td><?php echo $subjK_row['type']; ?></td>
                          <td><?php if($emp_stat_query->rowCount()>0){ echo $es_row['dept_office_name']; }else{ echo "All dept &amp; offices"; }  ?></td>
                    
                          
                          <td>
                          
                         
                          <a style="color: white !important;" data-toggle="modal" data-target="#editShift<?php echo $subjK_row['shift_id']; ?>" href="#" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteShift<?php echo $subjK_row['shift_id']; ?>" href="#" class="btn btn-danger"><i class="fa fa-times"></i></a>
                          
                          </td>
                        </tr>
                      
                  
                  
                  
                  <!-- delete Class Modal -->
                  <div id="deleteShift<?php echo $subjK_row['shift_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="shift_id" value="<?php echo $subjK_row['shift_id']; ?>" type="hidden" />
                      <input name="shift_name" value="<?php echo $subjK_row['shift_name']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Delete Shift</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                           
                        <h4>Are you sure you want to delete shift:<br /><br /><?php echo $subjK_row['shift_name']; ?>?</h4>
                        <small>Time Scope: <?php echo $subjK_row['type']; ?></small>
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                          <button name="deleteShift" type="submit" class="btn btn-danger">Yes</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end delete Class Modal -->
                  
                  <!-- edit Class Modal -->
                  <div id="editShift<?php echo $subjK_row['shift_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="shift_id" value="<?php echo $subjK_row['shift_id']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Shift</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                            
                         
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Shift</label>
                              <div class="col-sm-9">
                                <input value="<?php echo $subjK_row['shift_name']; ?>" name="shift_name" type="text" class="form-control" placeholder="Shift name..." />
                              </div>
                            </div> 
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Type</label>
                              <div class="col-sm-9">
                                <select class="form-control" name="type">
                              <option><?php echo $subjK_row['type']; ?></option>
                              <option>Regular Shift</option>
                              <option>Night Shift</option>
                              <option>24 Hours Shift</option>
                              </select>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Dept / Office</label>
                              <div class="col-sm-9">
                              <select name="do_id" class="form-control">
                              <option value="<?php echo $subjK_row['do_id']; ?>"><?php if($emp_stat_query->rowCount()>0){ echo $es_row['dept_office_name']; }else{ echo "All"; }  ?></option>
                              <option value="0">All</option>
                              <?php
                              $emp_stat_query = $conn->query("SELECT * from dept_offices ORDER BY dept_office_name ASC");
                              while($es_row=$emp_stat_query->fetch()){
                              ?>
                              <option value="<?php echo $es_row['do_id']; ?>"><?php echo $es_row['dept_office_name']; ?></option>
                              <?php } ?>
                              </select>
                              </div>
                            </div>
                            
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="updateShift" type="submit" class="btn btn-primary">Update</button>
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
                    
                    
                    
                    <div id="Paris" class="tabcontent">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                    
                      <thead>
                        <tr>
                         
                          <th>Shift</th>
                          <th>Time Scope</th>
                          <th>Dept / Office</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                             
                            $subjK_query = $conn->query("SELECT * FROM shifts WHERE type='Night Shift' ORDER BY shift_name ASC") or die(mysql_error());
                            while ($subjK_row = $subjK_query->fetch()) 
                            {
                                
                            $emp_stat_query = $conn->query("SELECT dept_office_name from dept_offices WHERE do_id='$subjK_row[do_id]'");
                            $es_row=$emp_stat_query->fetch();
                            
                            ?>
                        <tr>
                        
                          <td><?php echo $subjK_row['shift_name']; ?></td>
                          <td><?php echo $subjK_row['type']; ?></td>
                          <td><?php if($emp_stat_query->rowCount()>0){ echo $es_row['dept_office_name']; }else{ echo "All dept &amp; offices"; }  ?></td>
                    
                          
                          <td>
                          
                         
                          <a style="color: white !important;" data-toggle="modal" data-target="#editShift<?php echo $subjK_row['shift_id']; ?>" href="#" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteShift<?php echo $subjK_row['shift_id']; ?>" href="#" class="btn btn-danger"><i class="fa fa-times"></i></a>
                          
                          </td>
                        </tr>
                      
                  
                  
                  
                  <!-- delete Class Modal -->
                  <div id="deleteShift<?php echo $subjK_row['shift_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="shift_id" value="<?php echo $subjK_row['shift_id']; ?>" type="hidden" />
                      <input name="shift_name" value="<?php echo $subjK_row['shift_name']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Delete Shift</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                           
                        <h4>Are you sure you want to delete shift:<br /><br /><?php echo $subjK_row['shift_name']; ?>?</h4>
                        <small>Time Scope: <?php echo $subjK_row['type']; ?></small>
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                          <button name="deleteShift" type="submit" class="btn btn-danger">Yes</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end delete Class Modal -->
                  
                  <!-- edit Class Modal -->
                  <div id="editShift<?php echo $subjK_row['shift_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="shift_id" value="<?php echo $subjK_row['shift_id']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Shift</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                            
                         
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Shift</label>
                              <div class="col-sm-9">
                                <input value="<?php echo $subjK_row['shift_name']; ?>" name="shift_name" type="text" class="form-control" placeholder="Shift name..." />
                              </div>
                            </div> 
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Type</label>
                              <div class="col-sm-9">
                                <select class="form-control" name="type">
                              <option><?php echo $subjK_row['type']; ?></option>
                              <option>Regular Shift</option>
                              <option>Night Shift</option>
                              <option>24 Hours Shift</option>
                              </select>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Dept / Office</label>
                              <div class="col-sm-9">
                              <select name="do_id" class="form-control">
                              <option value="<?php echo $subjK_row['do_id']; ?>"><?php if($emp_stat_query->rowCount()>0){ echo $es_row['dept_office_name']; }else{ echo "All"; }  ?></option>
                              <option value="0">All</option>
                              <?php
                              $emp_stat_query = $conn->query("SELECT * from dept_offices ORDER BY dept_office_name ASC");
                              while($es_row=$emp_stat_query->fetch()){
                              ?>
                              <option value="<?php echo $es_row['do_id']; ?>"><?php echo $es_row['dept_office_name']; ?></option>
                              <?php } ?>
                              </select>
                              </div>
                            </div>
                            
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="updateShift" type="submit" class="btn btn-primary">Update</button>
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
                    
                    
                    
                    <div id="Tokyo" class="tabcontent">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                    
                      <thead>
                        <tr>
                         
                          <th>Shift</th>
                          <th>Time Scope</th>
                          <th>Dept / Office</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                             
                            $subjK_query = $conn->query("SELECT * FROM shifts WHERE type='24 Hours Shift' ORDER BY shift_name ASC") or die(mysql_error());
                            while ($subjK_row = $subjK_query->fetch()) 
                            {
                                
                            $emp_stat_query = $conn->query("SELECT dept_office_name from dept_offices WHERE do_id='$subjK_row[do_id]'");
                            $es_row=$emp_stat_query->fetch();
                            
                            ?>
                        <tr>
                        
                          <td><?php echo $subjK_row['shift_name']; ?></td>
                          <td><?php echo $subjK_row['type']; ?></td>
                          <td><?php if($emp_stat_query->rowCount()>0){ echo $es_row['dept_office_name']; }else{ echo "All dept &amp; offices"; }  ?></td>
                    
                          
                          <td>
                          
                         
                          <a style="color: white !important;" data-toggle="modal" data-target="#editShift<?php echo $subjK_row['shift_id']; ?>" href="#" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteShift<?php echo $subjK_row['shift_id']; ?>" href="#" class="btn btn-danger"><i class="fa fa-times"></i></a>
                          
                          </td>
                        </tr>
                      
                  
                  
                  
                  <!-- delete Class Modal -->
                  <div id="deleteShift<?php echo $subjK_row['shift_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="shift_id" value="<?php echo $subjK_row['shift_id']; ?>" type="hidden" />
                      <input name="shift_name" value="<?php echo $subjK_row['shift_name']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Delete Shift</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                           
                        <h4>Are you sure you want to delete shift:<br /><br /><?php echo $subjK_row['shift_name']; ?>?</h4>
                        <small>Time Scope: <?php echo $subjK_row['type']; ?></small>
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                          <button name="deleteShift" type="submit" class="btn btn-danger">Yes</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end delete Class Modal -->
                  
                  <!-- edit Class Modal -->
                  <div id="editShift<?php echo $subjK_row['shift_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="shift_id" value="<?php echo $subjK_row['shift_id']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Shift</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                            
                         
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Shift</label>
                              <div class="col-sm-9">
                                <input value="<?php echo $subjK_row['shift_name']; ?>" name="shift_name" type="text" class="form-control" placeholder="Shift name..." />
                              </div>
                            </div> 
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Type</label>
                              <div class="col-sm-9">
                                <select class="form-control" name="type">
                              <option><?php echo $subjK_row['type']; ?></option>
                              <option>Regular Shift</option>
                              <option>Night Shift</option>
                              <option>24 Hours Shift</option>
                              </select>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <label class="col-sm-3 form-control-label">Dept / Office</label>
                              <div class="col-sm-9">
                              <select name="do_id" class="form-control">
                              <option value="<?php echo $subjK_row['do_id']; ?>"><?php if($emp_stat_query->rowCount()>0){ echo $es_row['dept_office_name']; }else{ echo "All"; }  ?></option>
                              <option value="0">All</option>
                              <?php
                              $emp_stat_query = $conn->query("SELECT * from dept_offices ORDER BY dept_office_name ASC");
                              while($es_row=$emp_stat_query->fetch()){
                              ?>
                              <option value="<?php echo $es_row['do_id']; ?>"><?php echo $es_row['dept_office_name']; ?></option>
                              <?php } ?>
                              </select>
                              </div>
                            </div>
                            
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="updateShift" type="submit" class="btn btn-primary">Update</button>
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