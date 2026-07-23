<!DOCTYPE html>
<html>

  <?php
   include('session.php');
   include('header.php'); 
   
    $day=date("l"); //Mon-Sun
    
    if(isset($_POST['filterDate'])){
    $filterDate=$_POST['reportDate'];
     
    }else{
        
    $filterDate=date('m/d/Y');
   
    } ?>
    
    
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">
    
    <?php include('navbar_header.php');
    
    if($session_access==='User') { ?>
    <script>
        window.location = 'list_personnel_individual_details.php?dept=<?php echo $user_dept; ?>&personnel_id=<?php echo $user_personnel_id; ?>';
    </script>
    <?php }elseif($session_access==='Administrator'){
    
    include('quick_count.php');
    
    } ?>
    
    <?php if($session_access==='User'){ ?>
    
    <?php }elseif($session_access==='Administrator'){  ?>
    
    <?php 
    // Display notification if monthly leave credits were just processed
    if (isset($_SESSION['monthly_credits_processed'])) {
        $result = $_SESSION['monthly_credits_processed'];
        if ($result['success'] && $result['count'] > 0) {
            echo '<div class="container-fluid mt-3">';
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<strong><i class="fa fa-check-circle"></i> Monthly Leave Credits Processed!</strong><br>';
            echo htmlspecialchars($result['message']);
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button>';
            echo '</div>';
            echo '</div>';
        }
        unset($_SESSION['monthly_credits_processed']); // Clear the notification
    }
    ?>
    
        <section class="statistics">
         <div class="container-fluid">
          <div class="row d-flex">
             
            <?php
            $dept_off_query = $conn->query("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
            while ($do_row = $dept_off_query->fetch()) 
            { 
            
            $per_ctr_stmt = $conn->prepare("SELECT COUNT(*) FROM personnels WHERE do_id = :do_id AND separation_date IS NULL");
            $per_ctr_stmt->execute([':do_id' => $do_row['do_id']]);
            $per_ctr_count = (int)$per_ctr_stmt->fetchColumn();

            $male_per_ctr_stmt = $conn->prepare("SELECT COUNT(*) FROM personnels WHERE do_id = :do_id AND sex = 'Male' AND separation_date IS NULL");
            $male_per_ctr_stmt->execute([':do_id' => $do_row['do_id']]);
            $male_per_ctr_count = (int)$male_per_ctr_stmt->fetchColumn();

            $female_per_ctr_stmt = $conn->prepare("SELECT COUNT(*) FROM personnels WHERE do_id = :do_id AND sex = 'Female' AND separation_date IS NULL");
            $female_per_ctr_stmt->execute([':do_id' => $do_row['do_id']]);
            $female_per_ctr_count = (int)$female_per_ctr_stmt->fetchColumn();
            ?>
            
            <div class="col-lg-4" style="margin-bottom: 16px;">
              <!-- User Actibity-->
              
              <div class="card user-activity" style="border-bottom: 2px solid green; border-top: 1px solid green; border-left: 1px solid green; border-right: 1px solid green;">
                <h2 class="display h4">
                <button title="Click for Office/Dept options..." data-toggle="dropdown" type="button" class="btn btn-outline-primary btn-sm" style="margin-right: 12px;">&nbsp;<i class="fa fa-ellipsis-v"></i>&nbsp;</button>
                
                <div class="dropdown-menu">
                <a title="Click to print list of <?php echo $do_row['dept_office_name']; ?> personnels..." href="printPersonnelPerDept.php?do_id=<?php echo $do_row['do_id']; ?>" target="_blank" class="dropdown-item"><i class="fa fa-users"></i> List of Personnels</a>
                </div>

                  
                <a href="list_personnel.php?dept=<?php echo $do_row['do_id']; ?>" style="text-decoration-line: none;" title="Proceed to <?php echo $do_row['dept_office_name']; ?> personnel's list..."><?php echo substr($do_row['dept_office_name'], 0, 15); ?> &raquo;</a></h2>
                
                <div class="page-statistics-left">
                <span><a href="#" data-toggle="modal" data-target="#setDOHead<?php echo $do_row['do_id']; ?>" title="Set Department/Office Head"><i class="fa fa-pencil"></i></a>
                 
                 <small><strong>
                 <?php
                 $officeHead_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
                 $officeHead_stmt->execute([':personnel_id' => $do_row['officeHead_id']]);
                 $oh_row=$officeHead_stmt->fetch();
                 
                    if (!empty($oh_row)){
     
                        if($oh_row['suffix']=="-")
                        {
                            
                        echo $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname'];
                        
                        }else{
                            
                        echo $oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname']." ".$oh_row['suffix'];
                        
                        } }else{
                            echo "No Assigned Personnel";
                        }
                 ?>
                 </strong></small></span><br />
                 
                <small>&nbsp;&nbsp;Department/Office Head</small>
                </div>
                
                
               <div class="page-statistics d-flex justify-content-between">
                  <div class="page-statistics-left"><span>Male</span><strong><?php echo $male_per_ctr_count; ?></strong></div>
                  <div class="page-statistics-center"><span>Female</span><strong><?php echo $female_per_ctr_count; ?></strong></div>
                  <div class="page-statistics-right"><span>Total</span><strong><?php echo $per_ctr_count; ?></strong></div>
                </div>
                
              </div>
            </div>
            
            
            <?php include('setDOHead_modal.php'); ?>
            
            
            
            <?php } ?>
            
          </div>
        </div>
      </section>
      
      
      
      <?php } ?>
      
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>
    
    
    
 


    
  </body>
</html>