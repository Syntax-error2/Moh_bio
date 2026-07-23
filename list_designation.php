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
            <li class="breadcrumb-item active">List of Designations</li>
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
              <h2>Designations</h2>
              <p>Maintain position titles used in personnel records</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right">
              <a style="color: white !important;" data-toggle="modal" data-target="#addDes" href="#" class="btn btn-primary"><i class="fa fa-plus"></i> Add Designation</a>
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
                  <h5 class="mb-0">Designation DataTable</h5>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
               
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                 
                      <thead>
                        <tr>
                         
                          <th>Designation</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                             
                            $subjK_query = $conn->query("SELECT * FROM designation ORDER BY des_name ASC") or die(mysql_error());
                            while ($subjK_row = $subjK_query->fetch()) 
                            {  ?>
           
                        <tr>
                        
                          <td><?php echo $subjK_row['des_name']; ?></td>
                    
                          
                          <td>
                          
                         
                          <a style="color: white !important;" data-toggle="modal" data-target="#editDes<?php echo $subjK_row['des_id']; ?>" href="#" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteDes<?php echo $subjK_row['des_id']; ?>" href="#" class="btn btn-danger"><i class="fa fa-times"></i></a>
                          
                          </td>
                        </tr>
                      
                  
                  
                  
                  <!-- delete Class Modal -->
                  <div id="deleteDes<?php echo $subjK_row['des_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="des_id" value="<?php echo $subjK_row['des_id']; ?>" type="hidden" />
                      <input name="des_name" value="<?php echo $subjK_row['des_name']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Delete Designation</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                           
                        <h4>Are you sure you want to delete designation:<br /><br /><?php echo $subjK_row['des_name']; ?>?</h4>
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                          <button name="deleteDes" type="submit" class="btn btn-danger">Yes</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end delete Class Modal -->
                  
                  <!-- edit Class Modal -->
                  <div id="editDes<?php echo $subjK_row['des_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_dept_des_gass.php" method="POST">
                      <input name="des_id" value="<?php echo $subjK_row['des_id']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Designation</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                            
                         
                            
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Designation</label>
                              <div class="col-sm-10">
                                <input value="<?php echo $subjK_row['des_name']; ?>" name="des_name" type="text" class="form-control" placeholder="Designation name..." />
                              </div>
                            </div> 
                            
                             
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="updateDes" type="submit" class="btn btn-primary">Update</button>
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