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
            <li class="breadcrumb-item"><a href="list_slides.php">News Slides</a></li>
            <li class="breadcrumb-item active">Update Slide Image</li>
            
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
 
              <!-- JHS           -->
              
                  
                   
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display"> Update Slide Image Sequence # <?php echo $_GET['seq']; ?></h2>
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxJHS" aria-expanded="true" aria-controls="updates-boxJHS"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxJHS" role="tabpanel" class="collapse show">
                  <ul>
                    <!-- Item-->
                    <li class="d-flex justify-content-between"> 
                     
                    
                    
                            <form action="save_add_slide.php?slide_id=<?php echo $_GET['slide_id']; ?>&seq=<?php echo $_GET['seq']; ?>" method="POST" enctype="multipart/form-data">
                                
                              <div class="form-group row">
                              <label class="col-sm-2 form-control-label">Student Image</label>
                              <div class="col-sm-10">
                              
                              
                              <div class="row">
                              
                                <div class="col-md-6">
                                <input class="form-control" type="file" name="file" id="imgInp" />
                                
                                <br />
                                <img width="100%" height="100%" class="img-fluid rounded" src="<?php echo $_GET['path']; ?>" alt="current image image" />
                                <small class="form-text pull-right">Current Image <i class="fa fa-arrow-up"></i> change to <i class="fa fa-arrow-right"></i></small>
                                </div>
                                
                                <div class="col-md-6">
                                <br /><br /><br />
                                <img width="100%" height="100%" class="img-fluid rounded" id="blah" src="#" alt="your image" />
                                <small class="form-text pull-right">Image preview <i class="fa fa-arrow-up"></i></small>
                                </div>
                                
                                 
                                
                              </div>
                              
                         
  
  
                                
                                
                              </div>
                            </div>
                            
                            <div class="modal-footer">
                          <a style="color: white;" href="list_slides.php" class="btn btn-secondary">Cancel</a>
                          <button name="updateAnnouncementImg" type="submit" class="btn btn-primary">Update Image</button>
                            </div>
                        
                            </form>
                    
                    
                    
                    
                     
                    </li>

                  </ul>
                </div>
              </div>
              <!-- JHS End-->
 
               
            </div>
            
          </div>
        </div>
         
                  
      </section>
      
      
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