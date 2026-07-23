

<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
   include('csvFile_functions.php');

   
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
            <li class="breadcrumb-item active">Personnel Logs CSV File Manager</li>
          </ul>
        </div>
      </div>
      
      
      
      
      
      
      <section class="statistics">
         <div class="container-fluid">
          <div class="row d-flex">
          
          <legend style="margin-top: 24px; margin-bottom: 24px; margin-left: 12px;">PERSONNEL LOGS CSV FILE MANAGER</legend>
       
            <div class="col-lg-6" style="margin-bottom: 16px;">
              <!-- User Actibity-->
              
              <div class="card user-activity" style="border-bottom: 2px solid green; border-top: 1px solid green; border-left: 1px solid green; border-right: 1px solid green;">
                <h2 class="display h4">Import .CSV File</h2>
                <div class="page-statistics-left">
                <form class="form-horizontal" action="csvFile_functions.php" method="post" name="upload_excel" enctype="multipart/form-data">
                    <fieldset>
                        
                        <!-- File Button -->
                        <div class="form-group">
                            <label class="col-md-12 control-label" for="filebutton">Select File</label>
                            <div class="col-md-12">
                                <input type="file" name="file" id="file" class="input-large" required="" />
                            </div>
                        </div>
                        <br />
                        <!-- Button -->
                        <div class="form-group">
                             
                            <div class="col-md-12">
                                <button type="submit" id="submit" name="Import" class="btn btn-primary"><i class="fa fa-arrow-down"></i> IMPORT CSV FILE TO DATABASE</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
                </div>
                 
                 
                
              </div>
            </div>
            
            <div class="col-lg-6" style="margin-bottom: 16px;">
              <!-- User Actibity-->
              
              <div class="card user-activity" style="border-bottom: 2px solid green; border-top: 1px solid green; border-left: 1px solid green; border-right: 1px solid green;">
                <h2 class="display h4">Export .CSV File</h2>
                <div class="page-statistics-left">
                <form class="form-horizontal" action="csvFile_functions.php" method="POST" name="upload_excel" enctype="multipart/form-data">
                  <div class="form-group">
                  
                  <div class="row">
                  
                            <div class="col-md-6 col-md-offset-12">
                                <input type="date" name="logDateFrom" class="form-control" />
                                <small>From</small>
                            </div>
                            
                            <div class="col-md-6 col-md-offset-12">
                                <input type="date" name="logDateTo" class="form-control" /> 
                                <small>To</small>
                            </div>
                            
                            
                  </div>
                  <br />
                            <div class="col-md-12 col-md-offset-12">
                                <button type="submit" name="Export" class="btn btn-primary"><i class="fa fa-arrow-up"></i> EXPORT CSV FILE FROM DATABASE</button>
                            </div>
                   </div>                    
                </form>
                </div>
                 
                 
                
              </div>
            </div>
             
      
            
 
            
          </div>
        </div>
      </section>
      
 
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
  </body>
</html>
 
  