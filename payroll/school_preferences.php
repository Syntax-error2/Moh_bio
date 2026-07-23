<!DOCTYPE html>
<html>

  <?php
  
  include('session.php');
  include('header.php');
  
  ?>
  
  
  <?php $sfp_stat=$_GET['sfp_stat']; ?>
  
  
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
            <li class="breadcrumb-item active">Municipal Information</li>
          </ul>
        </div>
      </div>
      
    
     
      
      <!-- Header Section-->
      
      <br />
      <div class="col-lg-12">
      <div class="row">
      
      <div class="col-lg-12">
              <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h4>MUNICIPAL INFORMATION</h4>
                </div>
                <div class="card-body">
                
                 
                  <form action="activateSY.php" method="POST" class="form-horizontal" enctype="multipart/form-data"> 
                    

                              <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Official Seal</label>
                              <div class="col-sm-10">
                              
                              <?php if($sfp_stat=="xEdit"){ ?>
                              <center>
                                <img width="150" height="150" class="img-fluid rounded" src="img/<?php echo $sf_row['logo']; ?>" alt="current image image" />
                              </center>
                              <?php }else{ ?>
                                
                              
                              <div class="row">
                              
                                <div class="col-md-6">
                                <input class="form-control" type="file" name="file" id="imgInp" />
                                </div>
                                
                                <div class="col-md-3" style="text-align: center;">
                                <img width="150" height="150" class="img-fluid rounded" src="img/<?php echo $sf_row['logo']; ?>" alt="current image image" />
                                <small class="form-text">Current Image</small>
                                </div>
                                
                                <div class="col-md-3" style="text-align: center;">
                                <img width="150" height="150" class="img-fluid rounded" id="blah" src="#" alt="your image" />
                                <small class="form-text">Image preview</small>
                                </div>
                                
                                 
                                
                              </div>
                              
                              <?php } ?>
                              
                              </div>
                            </div>
                            
                     
                    
                    <div class="line"></div>
                    
                    <div class="form-group row">
                      <label class="col-sm-2 form-control-label">Zip Code</label>
                      <div class="col-sm-10">
                      <?php if($sfp_stat=="xEdit"){ ?>
                        <input type="text" class="form-control" readonly="true" value="<?php echo $deped_id; ?>">
                      <?php }else{ ?>
                        <input name="deped_id" type="text" placeholder="Enter Zip Code..." class="form-control" value="<?php echo $deped_id; ?>" required="">
                      <?php } ?>
                        
                      </div>
                    </div>
                    
                    <div class="line"></div>
                    
                    <div class="form-group row">
                      <label class="col-sm-2 form-control-label">Name of Employer</label>
                      <div class="col-sm-10">
                      <?php if($sfp_stat=="xEdit"){ ?>
                        <input type="text" class="form-control" readonly="true" value="<?php echo $schoolName; ?>">
                      <?php }else{ ?>
                        <input name="schoolName" type="text" placeholder="Enter Name of Municipality..." class="form-control" value="<?php echo $schoolName; ?>" required="">
                      <?php } ?>
                      </div>
                    </div>
                    
                    <div class="line"></div>
                    
                    <div class="form-group row">
                      <label class="col-sm-2 form-control-label">Address</label>
                      <div class="col-sm-10">
                      <?php if($sfp_stat=="xEdit"){ ?>
                        <input type="text" class="form-control" readonly="true" value="<?php echo $sf_row['address']; ?>">
                      <?php }else{ ?>
                        <input name="address" type="text" placeholder="Enter school address..." class="form-control" value="<?php echo $sf_row['address']; ?>" required="">
                      <?php } ?>
                      </div>
                    </div>
                    
                    <div class="line"></div>
                    
                    <div class="form-group row">
                      <label class="col-sm-2 form-control-label">Other Info</label>
                      <div class="col-sm-10">
                      
                      <?php if($sfp_stat=="xEdit"){ ?>
                      
                       <div class="row">
                               
                                <div class="col-md-6">
                                <input type="text" class="form-control" readonly="true" value="<?php echo $sf_row['emailAddress']; ?>">
                                <small class="form-text">Email Address</small>
                                </div>
                                
                                <div class="col-md-6">
                                <input type="text" class="form-control" readonly="true" value="<?php echo $sf_row['contactNumber']; ?>">
                                <small class="form-text">Contact Number</small>
                                </div>
                                
                                 
                                
                       </div>
                      
                      <?php }else{ ?>
                      <div class="row">
                               
                                <div class="col-md-6">
                                <input name="emailAddress" type="email" placeholder="Enter school email address..." class="form-control" value="<?php echo $sf_row['emailAddress']; ?>" required="">
                                <small class="form-text">Email Address</small>
                                </div>
                                
                                <div class="col-md-6">
                                <input name="contactNumber" type="text" placeholder="Enter school contact number..." class="form-control" value="<?php echo $sf_row['contactNumber']; ?>" required="">
                                <small class="form-text">Contact Number</small>
                                </div>
                                
                                 
                                
                       </div>
                      <?php } ?>
                      </div>
                    </div>
                    
                    <div class="line"></div>
                    
                    <div class="form-group row">
                      <label class="col-sm-2 form-control-label">Region</label>
                      <div class="col-sm-10">
                      <?php if($sfp_stat=="xEdit"){ ?>
                        <input type="text" class="form-control" readonly="true" value="<?php echo $region; ?>">
                      <?php }else{ ?>
                        <input name="region" type="text" placeholder="Enter school region..." class="form-control" value="<?php echo $region; ?>" required="">
                      <?php } ?>
                        
                      </div>
                    </div>
                    
                    <div class="line"></div>
                    
                    <div class="form-group row">
                      <label class="col-sm-2 form-control-label">District</label>
                      <div class="col-sm-10">
                      <?php if($sfp_stat=="xEdit"){ ?>
                        <input type="text" class="form-control" readonly="true" value="<?php echo $division; ?>">
                      <?php }else{ ?>
                        <input name="division" type="text" placeholder="Enter Municipal District..." class="form-control" value="<?php echo $division; ?>" required="">
                      <?php } ?>
                        
                      </div>
                    </div>
                    
                    
                    <div class="line"></div>
                    
                    <div class="form-group row">
                      <div class="col-sm-12 offset-sm-2">
                      <?php if($sfp_stat=="xEdit"){ ?>
                        <a href="school_preferences.php?sfp_stat=Edit" class="btn btn-secondary">Change Information</a>
                      <?php }else{ ?>
                        <a href="school_preferences.php?sfp_stat=xEdit" class="btn btn-secondary">Cancel</a>
                        <button name="update_pref" type="submit" class="btn btn-primary">Update Information</button>
                      <?php } ?>
                      
                        
                      </div>
                    </div>
 
                  </form>
                    
                  
                </div>
              </div>
            </div>
            </div>
            </div>
 

      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
        
        <script>
    
        $('#blah').attr('src', 'img/avatar-1.jpg');
        
        function readURL(input) {
    
      if (input.files && input.files[0]) {
        var reader = new FileReader();
    
        reader.onload = function(e) {
          $('#blah').attr('src', e.target.result);
        }
    
        reader.readAsDataURL(input.files[0]);
      }
    }
    
    $("#imgInp").change(function() {
      readURL(this);
    });
        </script>
        
  </body>
</html>
