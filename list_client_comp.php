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
            <li class="breadcrumb-item active">List of Client Computers</li>
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
              <h2>Client Computers</h2>
              <p>Manage target devices for system announcements</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right page-cta-group">
              <a style="color: white !important;" data-toggle="modal" data-target="#addSubjKinder" href="#addSubjKinder" class="btn btn-primary"><i class="fa fa-plus"></i> Add Client Computer</a>
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
                  <h5 class="mb-0">Client Computer DataTable</h5>
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
             
                
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                    
                      <thead>
                        <tr>
                         
                          <th>IP Address - Computer Name</th>
                           
                          <th>Description</th>
                           
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                             
                            $subjK_query = $conn->query("select * FROM client_computer ORDER BY clientNumber ASC") or die(mysql_error());
                            while ($subjK_row = $subjK_query->fetch()) 
                            { 
                                
                                $client_id=$subjK_row['client_id'];
                                ?>
           
                        <tr>
                          
                          <td><?php echo $subjK_row['ipAddress']." - ".$subjK_row['compName']; ?></td>
                          
                          <td><?php echo $subjK_row['description']; ?></td>
                           
                          
                          <td>
                          
                           
                          <a style="color: white !important;" data-toggle="modal" data-target="#editClientComp<?php echo $client_id; ?>" class="btn btn-success"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteClientComp<?php echo $client_id; ?>" class="btn btn-danger"><i class="fa fa-times"></i></a>
                          
                          </td>
                        </tr>
 
                       <?php 
                       
                       include('edit_client_comp_modal.php'); 
                       
                       
                        } ?>
                       
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
        
        <?php include('add_client_comp_modal.php'); ?>
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

     
    
  </body>
</html>