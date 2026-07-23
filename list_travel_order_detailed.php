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
                <li class="breadcrumb-item"><a href="list_travel_order.php">Employee's Travel Order Bulletin</a></li>
                <li class="breadcrumb-item active">Travel Code <?php echo $_GET['travel_code']; ?></li>
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
                  <h2>Travel Order Details</h2>
                  <p>Review personnel, dates, and travel metadata for this travel code</p>
                </div>
                <div class="col-lg-4 col-md-4 text-right page-cta-group">
                  <a href="list_travel_order.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to Bulletin</a>
                </div>
              </div>
            </div>
          </section>
 
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
      
       
      
            <?php
            
            $opt_query = $conn->query("SELECT DISTINCT travel_date FROM personnel_official_travel_logs ORDER BY travel_date DESC") or die(mysql_error());
 
            ?>
            
      
            <!-- TRAVEL ORDER BULLETIN    -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  
                  <table>
                  <tr>
                  
                  <td>
                  <h4>TRAVEL ORDER <u style="text-decoration-line: underline; font-size: larger;"><?php echo $_GET['travel_code']; ?></u></h4>
                  </td>
                  
                    
                  </tr>
                  </table>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                <br />
                <table class="table table-bordered table-striped" style="width: 95%; margin-left: 25px;"> 
                      
                      <thead>
                        <tr>
                        <th colspan="2">TRAVEL DETAILS</th>
                        </tr>
                      </thead>
                      
                      <tbody>
                      
                      <?php
                      $row_ctr=0;
                               
                      $new_clearance_query = $conn->query("SELECT DISTINCT 
                      travel_code, 
                      travel_date,
                      purpose,
                      description,
                      location,
                      travel_type,
                      numDays  FROM personnel_official_travel_logs WHERE travel_code='$_GET[travel_code]'");
                      $nc_row = $new_clearance_query->fetch();
                      $row_ctr=$row_ctr+1;
                      
                      ?>
                      
                        <tr>
                        
                          <td>
                          <table style="width: 100%;">
                          <tr>
                          <th>PURPOSE</th>
                          <th>DESCRIPTION</th>
                          <th>LOCATION</th>
                          <th>TYPE</th>
                          </tr>
                          
                          <tr>
                          <td><?php echo $nc_row['purpose']; ?></td>
                          <td><?php echo $nc_row['description']; ?></td>
                          <td><?php echo $nc_row['location']; ?></td>
                          <td><?php echo $nc_row['travel_type']; ?></td>
                          </tr>
                          </table>
                          </td>
                          
                          <td style="width: 10px;">
                          <a title="Edit travel details..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#editTO<?php echo $nc_row['travel_code']; ?>" href="#" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></a>
                          </td>
                          
                          
                        </tr>
                        
                       
                        <tr>
                          <th colspan="2">
                          <a title="Add personnel to travel entry..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#addPersonnel" href="#" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a>
                          &nbsp; PERSONNEL(S)</th>
                        </tr>
                       
                        <tr>
                          
                          
                          <td colspan="2">
                          <table style="width: 100%;">
                          <tr>
                          <th>NAME</th>
                          <th style="width: 10px;"></th>
                          </tr>
                          <?php
                          $pi_query = $conn->query("SELECT travel_log_id, personnel_id FROM personnel_official_travel_logs WHERE travel_code='$nc_row[travel_code]'");
                          while($pi_row = $pi_query->fetch()){ ?>
                          <tr>
                          <td>
                          <?php
                          $studData_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$pi_row[personnel_id]'") or die(mysql_error());
                          $sd_row=$studData_query->fetch();
                          
                          if($sd_row['suffix']=="-")
                          {
                            echo $p_name=$sd_row['fname']." ".substr($sd_row['mname'], 0,1).". ".$sd_row['lname'];
                          
                          }else{
                            
                            echo $p_name=$sd_row['fname']." ".substr($sd_row['mname'], 0,1).". ".$sd_row['lname']." ".$sd_row['suffix'];
                          
                          } ?>
                          </td>
                          
                          <td>
                          <a title="Delete personnel to travel entry..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deletePersonnel<?php echo $pi_row['travel_log_id']; ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          </td>
                          
                          </tr>
                          
                          <!-- delete PERSONNEL Modal -->
                          <div id="deletePersonnel<?php echo $pi_row['travel_log_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="cancel_travel.php?dept=<?php $sd_row['do_id']; ?>&personnel_id=<?php echo $sd_row['personnel_id']; ?>&travel_code=<?php echo $nc_row['travel_code']; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Personnel</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                <h4>Are you sure you want to delete personnel<br /><?php echo $p_name; ?>? </h4>
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deletePersonnel" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete PERSONNEL Modal -->
                          
                          <?php } ?>
                          
                          
                          
                          
                          </table>
                          </td>
                          
                          <!-- add PERSONNEL Modal -->
                          <div id="addPersonnel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="cancel_travel.php?travel_code=<?php echo $_GET['travel_code']; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Add Personnel</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                                    <div class="col-md-12">
                                    <input type="text" class="form-control" name="personnel_RFTag_id" placeholder="Search personnel fullname" list="perDataList" id="boxx1" required="true" />
                                    <small>Personnel RFID Tag | Fullname</small>
                                    <datalist id="perDataList">
                                        <?php
                                        
                                        $fnameList_query = $conn->query("SELECT DISTINCT RFTag_id, lname, fname, mname FROM personnels");
                                        while($fnlq_row = $fnameList_query->fetch()){ ?>
                                        
                                        <option value="<?php echo $fnlq_row['RFTag_id'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?>"><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></small></option>
                                        
                                        <?php } ?>
                                    </datalist>
                                    
                                    </div>
                            
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="addPersonnel" type="submit" class="btn btn-primary">Add</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end add PERSONNEL Modal -->
                          
                          </tr>
                         
                        <tr>
                          <th colspan="2">
                          <a title="Add date to travel entry..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#addDate" href="#" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a>
                          &nbsp; TRAVEL DATE(S)</th>
                        </tr>
                       
                        <tr>
                          
                          
                          <td colspan="2">
                          <table style="width: 100%;">
                          <tr>
                          <th>DATE</th>
                          <th style="width: 10px;"></th>
                          </tr>
                          <?php
                          $travelDate_query = $conn->query("SELECT DISTINCT logDate FROM personnel_logs WHERE travel_leave_code='$nc_row[travel_code]'");
                          while($travelDate_row = $travelDate_query->fetch()){ ?>
                          <tr>
                          <td>
                          <?php echo $travelDate_row['logDate']; ?>
                          </td>
                          
                          <td>
                          <a title="Delete personnel to travel entry..." style="color: white !important; margin-top: 3px;" data-toggle="modal" data-target="#deleteDate<?php echo substr($travelDate_row['logDate'], 3, 2); ?>" href="#" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          </td>
                          
                          </tr>
                          
                          <!-- delete DATE Modal -->
                          <div id="deleteDate<?php echo substr($travelDate_row['logDate'], 3, 2); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="cancel_date.php?logDate=<?php echo $travelDate_row['logDate']; ?>&travel_code=<?php echo $_GET['travel_code']; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Travel Date</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                                
                                
                                
                                <div class="col-lg-12">
                                    <div class="row">
                                    
                                    <p style="font-size: medium; font-weight: bold;">Please supply updated travel data before date deletion:</p>
                                
                                
                                    <div class="col-md-8">
                                    <input value="<?php echo $nc_row['travel_date']; ?>" name="travel_date" class="form-control" type="text"/>
                                    <small>Date Range (Format MM/DD/YYYY - MM/DD/YYYY)</small>
                                     
                                    </div>
                                    
                                    <div class="col-md-4">
                                    <input name="no_of_days" class="form-control" type="text"  value="<?php echo $nc_row['numDays']-1; ?>" readonly="true" />
                                    <small>Total Number of Days</small>
                                    </div>
                                    
                                    </div>
                                </div>
                                <hr />
                                <h4>Are you sure you want to delete date <?php echo $travelDate_row['logDate']; ?>? </h4>
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">Cancel</a>
                                  <button name="deleteDate" type="submit" class="btn btn-danger">Update Travel Data &amp; Delete</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete DATE Modal -->
                          
                          <?php } ?>
                          
                          <!-- add DATE Modal -->
                          <div id="addDate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="cancel_date.php?remarks=<?php echo $nc_row['travel_type']; ?>&travel_code=<?php echo $_GET['travel_code']; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Add Travel Date</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                                
                                
                                
                                <div class="col-lg-12">
                                    <div class="row">
                                    
                                    <p style="font-size: medium; font-weight: bold;">Please supply updated travel data before date addition:</p>
                                
                                
                                    <div class="col-md-8">
                                    <input value="<?php echo $nc_row['travel_date']; ?>" name="travel_date" class="form-control" type="text"/>
                                    <small>Date Range (Format MM/DD/YYYY - MM/DD/YYYY)</small>
                                     
                                    </div>
                                    
                                    <div class="col-md-4">
                                    <input name="no_of_days" class="form-control" type="text"  value="<?php echo $nc_row['numDays']+1; ?>" readonly="true" />
                                    <small>Total Number of Days</small>
                                    </div>
                                    
                                    </div>
                                </div>
                                <hr />
                                <input name="logDate" type="date" class="form-control" />
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="addDate" type="submit" class="btn btn-primary">Update Travel Data &amp; Add Date</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end add DATE Modal -->
                          
                          </table>
                          </td>
                          </tr>
                        
                        
                        
                        
                          <!-- edit travel Modal -->
                          <div id="editTO<?php echo $nc_row['travel_code']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_travel_leave.php?travel_code=<?php echo $nc_row['travel_code']; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Edit Travel Details</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                         
                                <div class="col-md-12">
                                    <select name="remarks" class="form-control">
                                    <option><?php echo $nc_row['travel_type']; ?></option>
                                    <option>OFFICIAL BUSINESS TRIP</option>
                                    <option>SEMINAR</option>
                                    </select>
                                    <small class="form-text">Select Notation Type</small>
                                </div>
                                <br />
                                
                                <div class="col-md-12">
                                    <input type="checkbox" name="add_201_sr" /> <small>Add to 201 Seminar Records (Applicable for Seminar only)</small>
                                    <input type="hidden" value="<?php echo substr($nc_row['travel_date'], 0, 10); ?>" name="event_date" />
                                </div>
                                <br />
                                
                                
                                <div class="col-md-12">
                                    <input value="<?php echo $nc_row['purpose']; ?>" name="purpose_title" type="text" class="form-control" />
                                    <small class="form-text">Travel Purpose / Seminar Title</small>
                                  </div>
                                  <br />
                                  <div class="col-md-12">
                                    <input value="<?php echo $nc_row['description']; ?>" name="description" type="text" class="form-control" />
                                    <small class="form-text">Description</small>
                                  </div>
                                  <br />
                                  <div class="col-md-12">
                                    <input value="<?php echo $nc_row['location']; ?>" name="location_venue" type="text" class="form-control" />
                                    <small class="form-text">Travel Location / Seminar Venue</small>
                                  </div>
                                   
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: black;" href="" data-dismiss="modal" class="btn btn-default">Cancel</a>
                                  <button name="updateTravel" type="submit" class="btn btn-success">Update</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end edit travel Modal -->
                         
                      </tbody>
                       
                    
                    </table>
 
 
  
                </div>
              </div>
              <!-- TRAVEL ORDER BULLETIN End-->
         
              
              
                </div>
            </div>
        </div>
     </section>
     
      
      
      <?php include('print_monthly_TO_modal.php'); ?>
   
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
 
 


    
  </body>
</html>