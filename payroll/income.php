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
            <li class="breadcrumb-item active">Income Reference</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">


            <!-- ADD PAYROLL PROFILE MODAL -->
            <div id="add_income_reference" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title">Add Income Reference</h5>
                      <a type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="icon icon-close"></span></a>
                    </div>
                    
                    <form action="income_cud.php" method="POST">
                    
                    <div class="modal-body">
                    
                        <div class="form-group row">
                          <label class="col-3">Type</label>
                          <div class="col-sm-9">
                            <select name="income_type" class="form-control">
                                <option>Compensation</option>
                                <option>Allowance</option>
                                <option>Others</option>
                            </select>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <label class="col-3">Title</label>
                          <div class="col-sm-9">
                            <input name="income_title" type="text" class="form-control" required="" />
                             <small>e.g., Basic Salary</small>
                          </div>
                        </div>
 
                    </div>
                    
                    <div class="modal-footer">
                      <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                      <button name="createIncome" type="submit" class="btn btn-primary">Add</button>
                    </div>
                    </form>
                  </div>
                </div>
            </div>
            <!-- END ADD PAYROLL PROFILE MODAL -->
            
            <!-- kinder 1 -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  
                  <a style="color: white !important;" data-toggle="modal" data-target="#add_income_reference" href="#" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></a>
                  
                  &nbsp;&nbsp;<a style="font-weight: bold;" data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">INCOME REFERENCE</a>
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
               
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                 
                      <thead>
                        <tr>
                          <th>Income Type</th>
                          <th>Income Title</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                        <?php
                         
                        $income_query = $conn->prepare("SELECT income_id, income_type, income_title FROM pr_tbl_income WHERE is_deleted = 0 ORDER BY income_title ASC");
                        $income_query->execute();
                        while ($income_row = $income_query->fetch()) 
                        {
                            
                        ?>
                            
                      <tr>
                      
                      <td><?php echo htmlspecialchars($income_row['income_type']); ?></td>
                      <td><?php echo htmlspecialchars($income_row['income_title']); ?></td>
                      
                      <td>
                      <button type="button" data-toggle="modal" data-target="#edit_income<?php echo $income_row['income_id']; ?>" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></button>
                      <button type="button" data-toggle="modal" data-target="#del_income<?php echo $income_row['income_id']; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>
                      </td>
                      
                      </tr>
                        
                          <!-- delete Class Modal -->
                          <div id="del_income<?php echo $income_row['income_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="income_cud.php" method="POST">
                              
                              <input name="income_id" value="<?php echo $income_row['income_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Income Reference</h5>
                                  <a type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="icon icon-close"></span></a>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete income reference <em><?php echo $income_row['income_title']; ?>?</em></h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="delIncome" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete Class Modal -->
                          
                          <!-- edit Class Modal -->
                          <div id="edit_income<?php echo $income_row['income_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              
                              <form action="income_cud.php" method="POST">
                              
                              <input name="income_id" value="<?php echo $income_row['income_id']; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Edit Income Reference</h5>
                                  <a type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="icon icon-close"></span></a>
                                </div>
                                
                                <div class="modal-body">
                                    
                                    <div class="form-group row">
                                      <label class="col-3">Type</label>
                                      <div class="col-sm-9">
                                        <select name="income_type" class="form-control">
                                            <option><?php echo $income_row['income_title']; ?></option>
                                            <option>Compensation</option>
                                            <option>Allowance</option>
                                            <option>Hazard Pay</option>
                                            <option>Others</option>
                                        </select>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                      <label class="col-3">Title</label>
                                      <div class="col-sm-9">
                                        <input name="income_title" value="<?php echo $income_row['income_title']; ?>" type="text" class="form-control" required="" />
                                         <small>e.g., Basic Salary</small>
                                      </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="updateIncome" type="submit" class="btn btn-primary">Update</button>
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