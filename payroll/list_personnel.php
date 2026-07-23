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
              
                <div class="tab">
                
                <?php if($_GET['dept'] == 'All'){ ?>
                <a title="Search personnels..." href="list_personnel.php?dept=All" class="tablinks active" style="font-weight: bolder;">All</a>
                <?php }else{?>
                <a title="Search personnels..." href="list_personnel.php?dept=All" class="tablinks">All</a>
                <?php } ?>
                
                <?php
                $dept_off_query = $conn->prepare("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
                $dept_off_query->execute();
                while ($do_row = $dept_off_query->fetch()) 
                {  ?>
                
                
                
                <?php if($_GET['dept']==$do_row['do_id']){ ?>
                <a title="List of personnel in the <?php echo $do_row['dept_office_name']; ?>" href="list_personnel.php?dept=<?php echo $do_row['do_id']; ?>" class="tablinks active" style="font-weight: bolder;"><?php echo $do_row['dept_office_name']; ?></a>
                <?php }else{?>
                <a title="List of personnel in the <?php echo $do_row['dept_office_name']; ?>" href="list_personnel.php?dept=<?php echo $do_row['do_id']; ?>" class="tablinks"><?php echo $do_row['dept_office_name']; ?></a>
                <?php } ?>
                
           
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