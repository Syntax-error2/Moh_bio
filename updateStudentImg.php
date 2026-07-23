<!DOCTYPE html>
<html>

  <?php
  
  include('session.php');
  
  include('header.php');
  
  ?>
  
    <?php
    $personnel_id = $_GET['personnel_id'] ?? '';
    $dept = $_GET['dept'] ?? '';
  
    $subjK_row = null;
    try {
      $subjK_query = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
      $subjK_query->execute([':personnel_id' => $personnel_id]);
      $subjK_row = $subjK_query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Error fetching personnel in updateStudentImg.php: " . $e->getMessage());
    }
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
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo htmlspecialchars($dept); ?>">List of Personnel</a></li>
            <li class="breadcrumb-item active">Update Personnel Image</li>
          </ul>
        </div>
      </div>

      <style>
      .image-update-card .card-header {
        background: linear-gradient(120deg, #2f9e44, #2b8a3e);
        color: #fff;
      }

      .image-panel {
        border: 1px solid #e1e8ef;
        border-radius: 10px;
        padding: 14px;
        background: #fafcfe;
        height: 100%;
      }

      .image-panel h6 {
        font-weight: 700;
        color: #2b3a49;
      }

      .image-frame {
        width: 100%;
        max-height: 340px;
        object-fit: contain;
        border: 1px solid #d8e1ea;
        border-radius: 8px;
        background: #fff;
        padding: 6px;
      }

      .help-note {
        font-size: 12px;
        color: #6b7a88;
      }

      .personnel-identity-card {
        border: 1px solid #dfe7ef;
        border-radius: 10px;
        background: #ffffff;
        margin-bottom: 16px;
      }

      .personnel-identity-body {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
      }

      .personnel-identity-avatar {
        width: 76px;
        height: 76px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #d4e3f3;
      }

      .personnel-identity-name {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #243447;
      }

      .personnel-identity-meta {
        margin: 0;
        color: #607286;
        font-size: 13px;
      }

      .personnel-identity-badge {
        display: inline-block;
        margin-top: 6px;
        margin-right: 6px;
        padding: 4px 8px;
        border-radius: 14px;
        background: #f4f8fc;
        border: 1px solid #dbe6f2;
        font-size: 12px;
        color: #3d556d;
        font-weight: 600;
      }
      </style>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
 
              <!-- JHS           -->
              <?php if ($subjK_row) { ?>
              <div class="personnel-identity-card">
                <div class="personnel-identity-body">
                  <img class="personnel-identity-avatar" src="personnelImg/<?php echo htmlspecialchars($subjK_row['img']); ?>" alt="Personnel avatar" />
                  <div>
                    <p class="personnel-identity-name">
                      <?php
                      $mname = trim((string)($subjK_row['mname'] ?? ''));
                      $suffix = trim((string)($subjK_row['suffix'] ?? ''));
                      $middle_initial = $mname !== '' ? substr($mname, 0, 1) . '. ' : '';
                      $suffix_part = ($suffix !== '' && $suffix !== '-') ? ' ' . $suffix : '';
                      echo htmlspecialchars(($subjK_row['fname'] ?? '') . ' ' . $middle_initial . ($subjK_row['lname'] ?? '') . $suffix_part);
                      ?>
                    </p>
                    <p class="personnel-identity-meta">Verify this personnel before uploading a new profile image.</p>
                    <span class="personnel-identity-badge">Personnel ID: <?php echo htmlspecialchars($subjK_row['personnel_id']); ?></span>
                    <span class="personnel-identity-badge">RFID: <?php echo htmlspecialchars($subjK_row['RFTag_id']); ?></span>
                    <span class="personnel-identity-badge">Department: <?php echo htmlspecialchars($subjK_row['do_id']); ?></span>
                  </div>
                </div>
              </div>
              <?php } ?>
              
              <div id="new-updates" class="card image-update-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 mb-0"><i class="fa fa-camera"></i> Update Personnel Image</h2>
                </div>

                <div class="card-body">
                  <?php if (!$subjK_row) { ?>
                    <div class="alert alert-danger mb-0">
                      Personnel record not found.
                    </div>
                  <?php } else { ?>
                    <form action="save_add_personnel.php?dept=<?php echo htmlspecialchars($dept); ?>" method="POST" enctype="multipart/form-data">
                      <input value="<?php echo htmlspecialchars($personnel_id); ?>" type="hidden" name="personnel_id" />
                      <input value="<?php echo htmlspecialchars($subjK_row['RFTag_id']); ?>" type="hidden" name="RFTag_id" />

                      <div class="row mb-3">
                        <div class="col-md-8">
                          <label for="imgInp" class="form-control-label font-weight-bold">Choose New Image</label>
                          <input class="form-control" type="file" name="file" id="imgInp" accept="image/*" />
                          <small class="help-note d-block mt-2">Tip: Use a clear, front-facing photo for best profile display quality.</small>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="image-panel">
                            <h6>Current Image</h6>
                            <img class="image-frame" src="personnelImg/<?php echo htmlspecialchars($subjK_row['img']); ?>" alt="Current personnel image" />
                          </div>
                        </div>

                        <div class="col-md-6 mb-3">
                          <div class="image-panel">
                            <h6>New Image Preview</h6>
                            <img class="image-frame" id="blah" src="personnelImg/<?php echo htmlspecialchars($subjK_row['img']); ?>" alt="Selected image preview" />
                          </div>
                        </div>
                      </div>

                      <div class="d-flex justify-content-end mt-2">
                        <a href="list_personnel.php?dept=<?php echo htmlspecialchars($dept); ?>" class="btn btn-secondary mr-2">Cancel</a>
                        <button name="updateStudentImg" type="submit" class="btn btn-primary">
                          <i class="fa fa-save"></i> Update Image
                        </button>
                      </div>
                    </form>
                  <?php } ?>
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