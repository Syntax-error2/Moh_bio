<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   include('dbcon2.php');
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
            <li class="breadcrumb-item active">Database File Manager</li>
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
              <h2>Database File Manager</h2>
              <p>Create and download database backup files</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right page-cta-group">
              <button type="submit" form="fullBackupForm" class="btn btn-success"><i class="fa fa-database"></i> Full Back-up</button>
            </div>
          </div>
        </div>
      </section>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
     
          <!-- kinder 1 -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder">DATABASE FILE MANAGER</a>
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
               
                    
                    <form id="fullBackupForm" action="list_dbFiles_manager_backup.php" method="post" style="margin-left: 55px; margin-top: 12px;">
                        
                        <?php
                        $idStmt = $conn2->query("SELECT MAX(ID) as max_cusid FROM backup_dbname");                                       
                        $row = $idStmt->fetch(PDO::FETCH_ASSOC);
                        $nextId = $row['max_cusid'] ?? 0;
                        ?>
                        <input type="hidden" id="ID" name="ID" value="<?php echo $nextId + 1; ?>" />
                                               
                     <button type="submit" class="btn btn-success"><i class="fa fa-database"></i> FULL BACK-UP</button>
                     
                    </form>
                    
                     <hr />
                    
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                     
                        <thead>
                            <tr>
                            
                          <th>DATABASE BACK-UP FILENAME</th>
                          <th>BACK-UP DATE | TIME</th>
                          <th>DOWNLOAD</th>
                            </tr>
                          </thead>
                          
                          <tbody>
                          <?php
                          
                          $user_query = $conn2->query("SELECT * FROM backup_dbname");
                          while($row = $user_query->fetch(PDO::FETCH_ASSOC)){
                            
                                  ?>
                                  <tr>
                                                             
                                  <td><?php echo $row['Name']; ?></td>
                                  <td><?php echo $row['Date']; ?></td>
                                  <td style="width: 10px;">
                                  
                                  <form action="Backup_Data/download.php" method="POST">
                                  <input type="hidden" name="file" value="<?php echo $row['Name'].'.sql'; ?>" />
                                  <button class="btn btn-primary"><i class="fa fa-download"></i></button>
                                  </form>
                                  
                                  </td>
                                                   
                         
                               </tr>
                                  <?php  }  ?>
                          </tbody>
                </table>
                </div>
                </div>
    
   <!-- iframe src="print_database.php" height="500px" width="100%"></iframe-->
                 
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
 
 