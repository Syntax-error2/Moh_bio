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
            <?php
            
            $do_id_name_query = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
            $do_id_name_query->execute(['do_id' => $_GET['dept']]);
            $don_row = $do_id_name_query->fetch();
            
            ?>
            <li class="breadcrumb-item active">List of Personnel - <?php if($_GET['dept'] == "All"){ echo "All"; }else{ echo $don_row['dept_office_name']; } ?></li>
          </ul>
        </div>
      </div>
 
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
                <div class="d-flex flex-wrap justify-content-center pb-3 pt-3 px-3" style="gap: 8px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 20px;">
                
                <?php 
                $isAllActive = ($_GET['dept'] ?? '') == 'All';
                $allBtnStyle = $isAllActive ? 'background-color: #28a745; color: white; border: 1px solid #28a745; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);' : 'background-color: white; color: #495057; border: 1px solid #ced4da;';
                ?>
                <a href="list_personnel.php?dept=All" 
                   class="btn btn-sm" 
                   style="border-radius: 20px; font-weight: 500; font-size: 0.85rem; padding: 5px 15px; margin: 2px; transition: all 0.2s ease; <?php echo $allBtnStyle; ?>"
                   onmouseover="if(!<?php echo $isAllActive ? 'true' : 'false'; ?>) { this.style.backgroundColor='#e2e6ea'; }"
                   onmouseout="if(!<?php echo $isAllActive ? 'true' : 'false'; ?>) { this.style.backgroundColor='white'; }">
                   All
                </a>
                
                <?php
                $dept_off_query = $conn->prepare("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
                $dept_off_query->execute();
                while ($do_row = $dept_off_query->fetch()) 
                {  
                    $isActive = ($_GET['dept'] ?? '') == $do_row['do_id'];
                    $btnStyle = $isActive ? 'background-color: #28a745; color: white; border: 1px solid #28a745; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);' : 'background-color: white; color: #495057; border: 1px solid #ced4da;';
                ?>
                
                <a href="list_personnel.php?dept=<?php echo $do_row['do_id']; ?>" 
                   class="btn btn-sm" 
                   style="border-radius: 20px; font-weight: 500; font-size: 0.85rem; padding: 5px 15px; margin: 2px; transition: all 0.2s ease; <?php echo $btnStyle; ?>"
                   onmouseover="if(!<?php echo $isActive ? 'true' : 'false'; ?>) { this.style.backgroundColor='#e2e6ea'; }"
                   onmouseout="if(!<?php echo $isActive ? 'true' : 'false'; ?>) { this.style.backgroundColor='white'; }">
                   <?php echo $do_row['dept_office_name']; ?>
                </a>
                
                <?php } ?>
                </div>
                
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  
                <?php if($_GET['dept'] == 'All'){ ?> 
                
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><h4> All Personnels</h4></a> 

                <?php }else{?>
                
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><h4><?php if($_GET['dept']!=''){
                    
                    $dept_off_name_query = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
                    $dept_off_name_query->execute(['do_id' => $_GET['dept']]);
                    $don_row = $dept_off_name_query->fetch();
                     
                    echo $don_row['dept_office_name']; ?></h4></a><?php  } ?>
                    
                <?php } ?>
                
                </h2>
                <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
 
                    <?php if($_GET['dept'] == 'All'){
                        
                    include('list_personnel_search.php'); 
                    
                    }else{ 
                    
                    include('list_personnel_table.php');
                    
                    } ?>
              
                    
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