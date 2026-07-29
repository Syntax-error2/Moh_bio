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
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?>&nbsp;</strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Deductions Reference</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">


            <!-- ADD PAYROLL PROFILE MODAL -->
            <div id="add_deduction_reference" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title">Add Deductions Reference</h5>
                      <a type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="icon icon-close"></span></a>
                    </div>
                    
                    <form action="deductions_cud.php" method="POST">
                    
                    <div class="modal-body">
                    
                        <div class="form-group row">
                          <label class="col-3">Type</label>
                          <div class="col-sm-9">
                            <select name="deduction_type" class="form-control">
                                <option>Contributions</option>
                                <option>Loans</option>
                                <option>Tax</option>
                                <option>Others</option>
                            </select>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <label class="col-3">Title</label>
                          <div class="col-sm-9">
                            <input name="deduction_title" type="text" class="form-control" required="" />
                             <small>e.g., PhilHealth</small>
                          </div>
                        </div>
 
                    </div>
                    
                    <div class="modal-footer">
                      <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                      <button name="createDeduction" type="submit" class="btn btn-primary">Add</button>
                    </div>
                    </form>
                  </div>
                </div>
            </div>
            <!-- END ADD PAYROLL PROFILE MODAL -->
            
            <!-- kinder 1 -->
              <div id="new-updates" class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">
                      <i class="fa fa-minus-circle"></i> DEDUCTIONS REFERENCE
                  </h5>
                  <button data-toggle="modal" data-target="#add_deduction_reference" class="btn btn-light btn-sm text-primary">
                      <i class="fa fa-plus"></i> Add Deduction
                  </button>
                </div>
                <div id="updates-boxKinder" class="card-body">
               
                    <div class="col-lg-12">
                    <div class="table-responsive">
                    <table id="example" class="table table-striped table-hover" style="width:100%">
                  
                      <thead class="thead-dark">
                        <tr>
                          <th>Deduction Type</th>
                          <th>Deduction Title</th>
                          <th style="width: 100px;">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                        <?php
                         
                        $deduction_query = $conn->prepare("SELECT deduction_id, deduction_type, deduction_title FROM pr_tbl_deductions WHERE is_deleted = 0 ORDER BY deduction_title ASC");
                        $deduction_query->execute();
                        while ($deduction_row = $deduction_query->fetch()) 
                        {
                            
                        ?>
                            
                      <tr>
                      
                      <td><?php echo htmlspecialchars($deduction_row['deduction_type']); ?></td>
                      <td><?php echo htmlspecialchars($deduction_row['deduction_title']); ?></td>
                      
                      <td>
                          <div style="display: flex; gap: 5px;">
                              <button type="button" data-toggle="modal" data-target="#edit_deduction<?php echo $deduction_row['deduction_id']; ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-pencil"></i></button>
                              <button type="button" data-toggle="modal" data-target="#del_deduction<?php echo $deduction_row['deduction_id']; ?>" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                          </div>
                      </td>
                      
                      </tr>
                        
                          <!-- delete Class Modal -->
                          <div id="del_deduction<?php echo $deduction_row['deduction_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="deductions_cud.php" method="POST">
                              
                              <input name="deduction_id" value="<?php echo $deduction_row['deduction_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Deduction Reference</h5>
                                  <a type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="icon icon-close"></span></a>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete deduction reference <em><?php echo $deduction_row['deduction_title']; ?>?</em></h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="delDeduction" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete Class Modal -->
                          
                          <!-- edit Class Modal -->
                          <div id="edit_deduction<?php echo $deduction_row['deduction_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              
                              <form action="deductions_cud.php" method="POST">
                              
                              <input name="deduction_id" value="<?php echo $deduction_row['deduction_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Edit Deduction Reference</h5>
                                  <a type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="icon icon-close"></span></a>
                                </div>
                                
                                <div class="modal-body">
                                    
                                    <div class="form-group row">
                                      <label class="col-3">Type</label>
                                      <div class="col-sm-9">
                                        <select name="deduction_type" class="form-control">
                                            <option><?php echo $deduction_row['deduction_type']; ?></option>
                                            <option>Contributions</option>
                                            <option>Loans</option>
                                            <option>Tax</option>
                                            <option>Others</option>
                                        </select>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                      <label class="col-3">Title</label>
                                      <div class="col-sm-9">
                                        <input name="deduction_title" value="<?php echo $deduction_row['deduction_title']; ?>" type="text" class="form-control" required="" />
                                         <small>e.g., PhilHealth</small>
                                      </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="updateDeduction" type="submit" class="btn btn-primary">Update</button>
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
          
      </section>
      
 
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
  </body>
</html>