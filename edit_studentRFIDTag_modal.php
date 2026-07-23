<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
  
  
    
  <?php
  
  $getDept=$_GET['dept'];
  $personnel_id=$_GET['personnel_id'];

        function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
     
    
    if(get_client_ip()=="::1")
    {
        $machine_ip=gethostbyname(trim(`hostname`));  
    }else{
        $machine_ip=get_client_ip();
    }
    
  $blank='';
  $dataFile=fopen("\\\\".$machine_ip."\\rfid\\TEST\\data.enr", "w") or die  ("Unable to open data.enr file in C: rfid!");
  fwrite($dataFile, $blank);
  fclose($dataFile);
    
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
            <li class="breadcrumb-item active">List of Personnel - Update RFID Tag
               
            
            </li>
          </ul>
        </div>
      </div>
      
      
                <?php
                
                $updRFIDTag_query = $conn->query("select * FROM personnels WHERE personnel_id='$personnel_id'") or die(mysql_error());
                $urt_row=$updRFIDTag_query->fetch();
                
                ?>
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
 
              <!-- JHS  -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">Update Personnel RFID Tag <strong>[ <?php echo $urt_row['fname']." ".$urt_row['mname']." ".$urt_row['lname']; ?> ]</strong>
                  </h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxJHS" aria-expanded="true" aria-controls="updates-boxJHS"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxJHS" role="tabpanel" class="collapse show">
                
 
                <form action="save_add_personnel.php?dept=<?php echo $getDept; ?>&personnel_id=<?php echo $personnel_id; ?>" method="POST" enctype="multipart/form-data">
                       
                       
                       <input value="<?php echo $personnel_id; ?>" name="personnel_id" type="hidden" />
                         
                        
                        <div class="modal-body">
                     
                     
                     
                     <div class="form-group row">
                     <label class="col-sm-2 form-control-label">Current RFID Tag</label>
                     <div class="col-sm-4">
                     <h3><?php echo $urt_row['RFTag_id']; ?></h3>
                     </div>
                     
                     
                     <label class="col-sm-2 form-control-label">Tapped RFID Tag</label>
                     <div class="col-sm-4">
                     <div id="screen2"> </div>
                     <small class="form-text"> </small>
                     </div>
                     
                     </div>
  
                    
                        </div>
                        <input name="currentRFIDTag" type="hidden" value="<?php echo $urt_row['RFTag_id']; ?>" />
                        <div class="modal-footer">
                          <a href="list_personnel.php?dept=<?php echo $getDept; ?>" style="color: white;" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="updateStudentRFIDTag" type="submit" class="btn btn-primary">Update</button>
                        </div>
                        
                        </form>
                </div>
              </div>
              <!-- JHS End-->
 
               
            </div>
            
          </div>
        </div>
        
        <?php include('add_student_modal.php'); ?>
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
    <script>
    $(document).ready(function(){
    setInterval(function(){
    $("#screen2").load('edit_student_tag.php')
                               
                         
    }, 250);
    });
    </script>  
  </body>
</html>   
 
                  
  