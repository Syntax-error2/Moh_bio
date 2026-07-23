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
            <li class="breadcrumb-item active">Slide Gallery</li>
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
              <h2>Slide Gallery</h2>
              <p>Manage announcement banners and slide images</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right page-cta-group">
              <a href="#" class="btn btn-secondary" onclick="return false;" style="visibility: hidden;">&nbsp;</a>
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
                  <h5 class="mb-0">Slide Gallery DataTable</h5>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
       
                
               
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                    
                      <thead>
                        <tr>
                        <th>Seq. #</th>
                        <th>Announcement Image</th>
                         </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                             
                            $subjK_query = $conn->query("select * FROM slides ORDER BY slide_id ASC") or die(mysql_error());
                            while ($subjK_row = $subjK_query->fetch()) 
                            { 
                                
                                $slide_id=$subjK_row['slide_id'];
                                ?>
           
                        <tr>
                          
                          <td><?php echo $subjK_row['sequence']; ?></td>
                          
                          <td><a title="Click to update number <?php echo $subjK_row['sequence']; ?> slide image..." href="updateAnnouncementImg.php?slide_id=<?php echo $slide_id; ?>&seq=<?php echo $subjK_row['sequence']; ?>&path=announcement_img/<?php echo $subjK_row['img']; ?>"><img style="width: 250px; height: 150px;" src="announcement_img/<?php echo $subjK_row['img']; ?>" /></a></td>
        
                        </tr>
                        
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