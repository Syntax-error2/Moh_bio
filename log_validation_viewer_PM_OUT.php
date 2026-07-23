<!DOCTYPE html>
<html>

    <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
   
  <?php
 
   //include('loaderFX.php'); 
  
    $day=date("l"); //Mon-Sun
    
    if(isset($_POST['filterDate'])){
    $filterDate=$_POST['reportDate'];
     
    }else{
        
    $filterDate=date('m/d/Y');
   
    }
    
    if(isset($_POST['print_daily_LV'])){ ?>
    
    <script>
    window.open('print_daily_preview_LogValidation.php?dateFrom=<?php echo $filterDate; ?>', '_blank');
    window.location='log_validation_viewer.php';
    </script>
    
    
    <?php } ?>
    
   
    
    
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">
    
    <?php include('navbar_header.php');
    
    if($session_access==='Administrator'){ ?>
    
        <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Log Validation Viewer</li>
          </ul>
        </div>
      </div>
      
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
            
                    <div class="col-lg-12" style="margin-bottom: 12px;">
                    <a class="btn btn-outline-primary"href="log_validation_viewer.php">AM IN</a>
                    <a class="btn btn-outline-primary" href="log_validation_viewer_AM_OUT.php">AM OUT</a>
                    <a class="btn btn-outline-primary" href="log_validation_viewer_PM_IN.php">PM IN</a>
                    <a class="btn btn-primary" style="color: white; font-weight: bold;" href="log_validation_viewer_PM_OUT.php">PM OUT</a>
                    </div>
                    
                    
            <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  
                  <form method="POST">
                  <table>
                  <tr>
                  
                  <td style="border: none; background-color: white;">
                  <h4>LIST OF LOG VALIDATION</h4>
                  </td>
                  
                  <td style="border: none; background-color: white;">
                  <select name="reportDate" class="form-control">
                  <option><?php echo $filterDate; ?></option>
                   
                  <?php
                  $currentDate="";
                  $opt_query = $conn->query("SELECT DISTINCT logDate FROM personnel_logs WHERE remarks='Inserted' OR remarks='Updated' OR remarks='' ORDER BY logDate DESC") or die(mysql_error());
                  while ($opt_row = $opt_query->fetch()) 
                  { 
                    if($filterDate==$opt_row['logDate']){
                        
                    }else{ ?>
                    
                    <option><?php echo $opt_row['logDate']; ?></option>
                    
                    <?php
                    
                    $currentDate=$opt_row['logDate'];
                    
                    } } ?>
                  </select>
                  </td>
                  
                  <td style="border: none; background-color: white;">
                  <button name="filterDate" class="btn btn-primary" title="Filter Date"><i class="fa fa-filter"></i></button>
                  <button name="print_daily_LV" class="btn btn-info" style="color: white;" title="Print daily log validation list..."><i class="fa fa-print"></i></button>
                  </td>
                  </tr>
                  </table>
                  </form>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxContacts" aria-expanded="true" aria-controls="updates-boxContacts"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxContacts" role="tabpanel" class="collapse show">
                
                <div class="col-lg-12">
                <div class="table-responsive" style="margin-top: 12px;">
                <table id="" class="display" style="width:100%">
                
                      <thead>
                        <tr>
                          <th><center>VALIDATED IMAGE</center></th>
                          <th><center>IMAGE</center></th>
                          <th>DETAILS</th>
                          <th>ACTION</th>
                        </tr>
                      </thead>
                      
                      <tbody>
                      
                      <?php
                      $row_ctr=0;
                               
                      $new_clearance_query = $conn->query("SELECT * FROM personnel_logs WHERE logDate='$filterDate' AND logFlow='PM OUT' AND (remarks='Inserted' OR remarks='Updated' OR remarks='') ORDER BY log_id ASC");
                      while($nc_row = $new_clearance_query->fetch()){
                      $row_ctr=$row_ctr+1;
                      
                      if($nc_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            
                            if($nc_row['suffix']=='-') { $suffix=''; }else{ $suffix=$nc_row['suffix'].' '; }
                            
                            $finalMName=$suffix.$nc_row['mname'];
                        } ?> 
                      
                        <tr>
                          <td>
                          <center>
                          <a href="#" data-toggle="modal" data-target="#zoom_snap<?php echo $nc_row['log_id']; ?>" style="cursor: move;" title="Click to zoom image..."><img src="upload/<?php echo $nc_row['captured_img']; ?>" width="100" height="75" class="img-fluid rounded" /></a>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <img src="<?php echo $nc_row['img']; ?>" width="60" height="75" class="img-fluid rounded" />
                          </center>
                          </td>
                          
                          <td>
                          <p><strong>Fullname: </strong><?php echo $nc_row['lname'].", ".$nc_row['fname']." ".$finalMName; ?></p>
                          <p><strong>Log Time: </strong><?php echo $nc_row['logTime']; ?></p>
                          <p><strong>Log Flow: </strong><?php echo $nc_row['logFlow']; ?></p>
                          
                          </td>
                
                          <td> </td>
                          
                        </tr>
                      
                       <?php include('zoom_snapshot_modal.php'); ?>
                       
                      <?php }?>
                      
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
     
     <?php } ?>
             
  
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
 
 


    
  </body>
</html>