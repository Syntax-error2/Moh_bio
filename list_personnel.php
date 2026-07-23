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

    <?php $dept = $_GET['dept'] ?? 'All'; ?>
    
    
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <?php
            if ($dept !== 'All') {
                // Fetch specific department name using prepared statement
                try {
                    $do_id_name_query = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                $do_id_name_query->execute([':do_id' => $dept]);
                    $don_row = $do_id_name_query->fetch();
                    
                    if ($don_row) {
                        $dept_name = htmlspecialchars($don_row['dept_office_name']);
                    } else {
                        $dept_name = 'Unknown Department';
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching department name: " . $e->getMessage());
                    $dept_name = 'Department';
                }
            } else {
                $dept_name = 'All Departments';
            }
            ?>
            <li class="breadcrumb-item active">List of Personnel - <?php echo $dept_name; ?></li>
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
              <h2>Personnel List</h2>
              <p>Browse, manage, and print personnel records by department</p>
            </div>
            <div class="col-lg-4 col-md-4 text-right page-cta-group">
              <?php if ($dept !== 'All') { ?>
                <a style="color: white !important;" data-toggle="modal" data-target="#addPersonnel" href="#" class="btn btn-primary"><i class="fa fa-plus"></i> Add Personnel</a>
              <?php } ?>
            </div>
          </div>
        </div>
      </section>

      <style>
      .personnel-table td,
      .personnel-table th {
        vertical-align: middle;
      }

      .personnel-cell {
        gap: 12px;
      }

      .personnel-avatar {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border: 1px solid #d9e1ea;
      }

      .personnel-meta {
        min-width: 0;
      }

      .personnel-code-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        flex-wrap: wrap;
      }

      .personnel-rfid-link {
        color: #0a8d43;
        font-size: 12px;
        font-weight: 600;
      }

      .personnel-name {
        font-size: 15px;
        font-weight: 700;
        color: #25313d;
        line-height: 1.3;
      }

      .shift-chip {
        display: inline-block;
        background: #f7f9fb;
        border: 1px solid #dbe3ea;
        border-radius: 18px;
        padding: 6px 12px;
        font-weight: 600;
      }
      </style>
 
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
              <!-- Department Selection Dropdown -->
              <div class="card mb-3">
                <div class="card-body">
                  <form method="GET" action="list_personnel.php" class="form-inline">
                    <label class="mr-2">Select Department:</label>
                    <select name="dept" id="departmentSelect" class="form-control" style="width: 500px;" required>
                      <option value="All" <?php echo ($dept === 'All') ? 'selected' : ''; ?>>All Departments</option>
                      <?php
                      try {
                          $dept_off_query = $conn->prepare("SELECT do_id, dept_office_name FROM dept_offices ORDER BY dept_office_name ASC");
                          $dept_off_query->execute();
                          while ($do_row = $dept_off_query->fetch()) {
                              $selected = ($dept == $do_row['do_id']) ? 'selected' : '';
                              echo '<option value="'.htmlspecialchars($do_row['do_id']).'" '.$selected.'>'.htmlspecialchars($do_row['dept_office_name']).'</option>';
                          }
                      } catch (PDOException $e) {
                          error_log("Error fetching departments: " . $e->getMessage());
                      }
                      ?>
                    </select>
                  </form>
                </div>
              </div>
              
              
              
                
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  
                <?php if($dept==='All'){ ?> 
                
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><h4> All Personnels</h4></a> 

                <?php }else{?>
                
                  <table>
                  <tr>
                  
                  <td style="border: none;">
                  <a style="color: white !important;" data-toggle="modal" data-target="#addPersonnel" href="#" class="btn btn-primary"><i class="fa fa-plus"></i></a> 
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><h4><?php 
                    if ($dept !== '' && $dept !== 'All') {
                        try {
                            $dept_off_name_query = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                        $dept_off_name_query->execute([':do_id' => $dept]);
                            $don_row = $dept_off_name_query->fetch();
                            
                            if ($don_row) {
                                echo htmlspecialchars($don_row['dept_office_name']);
                            } else {
                                echo 'Department';
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching department name: " . $e->getMessage());
                            echo 'Department';
                        }
                    } elseif ($dept === 'All') {
                        echo 'All Departments';
                    }
                    ?></h4></a>
                  
                  </td> 
                  </tr>
                  </table>
                
                <?php } ?>
                
                </h2>
                <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
 
                    <?php if($dept==='All'){
                        
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
        
        <?php include('add_student_modal.php'); ?>
        <?php include('edit_personnel_modal.php'); ?>
                  
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    
    
    <?php include('scripts_files.php'); ?>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
    $(document).ready(function(){
        // Initialize Select2 for department dropdown with search
        $('#departmentSelect').select2({
            placeholder: 'Search or select a department...',
            allowClear: false,
            width: '500px'
        }).on('select2:select', function (e) {
            // Auto-submit form when department is selected
            $(this).closest('form').submit();
        });
        
    	setInterval(function(){
    		$("#screen").load('add_student_tag.php')
        }, 250);
    });
    </script>
    
    
 
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