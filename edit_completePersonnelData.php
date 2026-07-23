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
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo htmlspecialchars($schoolName); ?> | </strong></li>
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="list_personnel.php?dept=<?php echo urlencode($_GET['dept'] ?? ''); ?>">List of Personnel</a></li>
            <li class="breadcrumb-item active">Update Complete Personnel Data</li>
          </ul>
        </div>
      </div>
      
      
      <?php
      // Sanitize and validate GET parameters
      $personnel_id = $_GET['personnel_id'] ?? '';
      $dept_id = $_GET['dept'] ?? '';
      
      // Use prepared statement to prevent SQL injection
      $personnel_query = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id LIMIT 1");
      $personnel_query->execute([':personnel_id' => $personnel_id]);
      $personnel_row = $personnel_query->fetch();
      
      // Handle case where personnel not found
      if (!$personnel_row) {
          echo "<script>alert('Personnel not found.'); window.location='list_personnel.php?dept=" . urlencode($dept_id) . "';</script>";
          exit;
      }
      ?>
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <form action="save_add_personnel.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>" method="POST" enctype="multipart/form-data">

          <input type="hidden" name="personnel_id" value="<?php echo htmlspecialchars($personnel_row['personnel_id']); ?>" />

          <div class="row">
            <div class="col-lg-12 col-md-12">
              
              <div class="personnel-form-shell">
                <div class="personnel-form-header mb-3">
                  <h4 class="mb-1">Update Personnel Profile</h4>
                  <p class="mb-0">Fields marked with <span class="text-danger">*</span> are required.</p>
                </div>

                <div class="personnel-form-body">
                  <div class="personnel-category-card mb-3">
                    <h5 class="personnel-form-title">Identity and Work Setup</h5>
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="personnel_id_code">Employee ID Code <span class="text-danger">*</span></label>
                        <input id="personnel_id_code" value="<?php echo htmlspecialchars($personnel_row['personnel_id_code']); ?>" name="personnel_id_code" type="text" class="form-control" required="true" />
                      </div>

                      <div class="form-group col-md-4">
                        <label for="RFTag_id">RFID Tag</label>
                        <input id="RFTag_id" value="<?php echo htmlspecialchars($personnel_row['RFTag_id']); ?>" name="RFTag_id" type="text" class="form-control" readonly="true" />
                      </div>

                      <div class="form-group col-md-4">
                        <label for="shift_id">Work-Hour Shift</label>
                        <?php
                        $emp_stat_query = $conn->prepare("SELECT shift_id, shift_name FROM shifts WHERE shift_id = :shift_id LIMIT 1");
                        $emp_stat_query->execute([':shift_id' => $personnel_row['shift_id']]);
                        $es_row = $emp_stat_query->fetch();
                        ?>
                        <select id="shift_id" name="shift_id" class="form-control">
                          <?php if ($es_row): ?>
                          <option value="<?php echo htmlspecialchars($es_row['shift_id']); ?>"><?php echo htmlspecialchars($es_row['shift_name']); ?></option>
                          <?php endif; ?>
                          <option value="0">-</option>
                          <?php
                          $emp_stat_query = $conn->prepare("SELECT shift_id, shift_name FROM shifts ORDER BY shift_name ASC");
                          $emp_stat_query->execute();
                          while($es_row = $emp_stat_query->fetch()){
                          ?>
                          <option value="<?php echo htmlspecialchars($es_row['shift_id']); ?>"><?php echo htmlspecialchars($es_row['shift_name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="personnel-category-card mb-3">
                    <h5 class="personnel-form-title">Basic Information</h5>
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label for="fname">First Name <span class="text-danger">*</span></label>
                        <input id="fname" value="<?php echo htmlspecialchars($personnel_row['fname']); ?>" name="fname" type="text" class="form-control" required="true" />
                      </div>

                      <div class="form-group col-md-3">
                        <label for="mname">Middle Name</label>
                        <input id="mname" value="<?php echo htmlspecialchars($personnel_row['mname']); ?>" name="mname" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-4">
                        <label for="lname">Last Name <span class="text-danger">*</span></label>
                        <input id="lname" value="<?php echo htmlspecialchars($personnel_row['lname']); ?>" name="lname" type="text" class="form-control" required="true" />
                      </div>

                      <div class="form-group col-md-2">
                        <label for="suffix">Suffix</label>
                        <select id="suffix" name="suffix" class="form-control">
                          <option><?php echo htmlspecialchars($personnel_row['suffix']); ?></option>
                          <option>-</option>
                          <option>JR.</option>
                          <option>SR.</option>
                          <option>III</option>
                          <option>IV</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="sex">Sex</label>
                        <select id="sex" name="sex" class="form-control">
                          <option><?php echo htmlspecialchars($personnel_row['sex']); ?></option>
                          <option>-</option>
                          <option>Male</option>
                          <option>Female</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="marital_status">Marital Status</label>
                        <select id="marital_status" name="marital_status" class="form-control">
                          <option><?php echo htmlspecialchars($personnel_row['marital_status']); ?></option>
                          <option>-</option>
                          <option>Single</option>
                          <option>Married</option>
                          <option>Widowed</option>
                          <option>Separated</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="bdate">Date of Birth <span class="text-danger">*</span></label>
                        <input value="<?php echo htmlspecialchars($personnel_row['bdMM'].'/'.$personnel_row['bdDD'].'/'.$personnel_row['bdYYYY']); ?>" name="birthdate" id="bdate" type="text" class="form-control" />
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="birth_place">Place of Birth <span class="text-danger">*</span></label>
                        <input id="birth_place" value="<?php echo htmlspecialchars($personnel_row['birth_place']); ?>" name="birth_place" list="search_list_pob" type="text" class="form-control" />

                        <datalist id="search_list_pob">
                        <?php
                        try {
                            $pobList_query = $conn->prepare("SELECT DISTINCT birth_place FROM personnels WHERE birth_place IS NOT NULL AND birth_place != '' ORDER BY birth_place ASC");
                            $pobList_query->execute();
                            while($poblq_row = $pobList_query->fetch()){
                                if (!empty($poblq_row['birth_place'])) {
                                    echo '<option>'.htmlspecialchars($poblq_row['birth_place']).'</option>';
                                }
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching birth places: " . $e->getMessage());
                        }
                        ?>
                        </datalist>
                      </div>

                      <div class="form-group col-md-8">
                        <label for="address">Complete Address <span class="text-danger">*</span></label>
                        <input id="address" value="<?php echo htmlspecialchars($personnel_row['address']); ?>" name="address" list="search_list_address" type="text" class="form-control" />
                        <small class="form-text text-muted">Street, Barangay, City/Municipality, Province</small>

                        <datalist id="search_list_address">
                        <?php
                        try {
                            $pAddressList_query = $conn->prepare("SELECT DISTINCT address FROM personnels WHERE address IS NOT NULL AND address != '' ORDER BY address ASC");
                            $pAddressList_query->execute();
                            while($pAlq_row = $pAddressList_query->fetch()){
                                if (!empty($pAlq_row['address'])) {
                                    echo '<option>'.htmlspecialchars($pAlq_row['address']).'</option>';
                                }
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching addresses: " . $e->getMessage());
                        }
                        ?>
                        </datalist>
                      </div>
                    </div>
                  </div>

                  <div class="personnel-category-card mb-3">
                    <h5 class="personnel-form-title">Contact Information</h5>
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="email">Email Address</label>
                        <input id="email" value="<?php echo htmlspecialchars($personnel_row['email']); ?>" type="email" name="email" class="form-control" />
                      </div>

                      <div class="form-group col-md-4">
                        <label for="contact_no">Personal Number <span class="text-danger">*</span></label>
                        <input value="<?php echo htmlspecialchars($personnel_row['personal_pnum']); ?>" name="personal_pnum" id="contact_no" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-4">
                        <label for="contact_no2">Contact Person Number</label>
                        <input value="<?php echo htmlspecialchars($personnel_row['emergency_pnum']); ?>" name="emergency_pnum" id="contact_no2" type="text" class="form-control" />
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label for="conPerson_fname">Contact Person First Name <span class="text-danger">*</span></label>
                        <input id="conPerson_fname" value="<?php echo htmlspecialchars($personnel_row['conPerson_fname']); ?>" name="conPerson_fname" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-3">
                        <label for="conPerson_mname">Contact Person Middle Name</label>
                        <input id="conPerson_mname" value="<?php echo htmlspecialchars($personnel_row['conPerson_mname']); ?>" name="conPerson_mname" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-4">
                        <label for="conPerson_lname">Contact Person Last Name <span class="text-danger">*</span></label>
                        <input id="conPerson_lname" value="<?php echo htmlspecialchars($personnel_row['conPerson_lname']); ?>" name="conPerson_lname" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-2">
                        <label for="conPerson_relationship">Relationship</label>
                        <select id="conPerson_relationship" name="conPerson_relationship" class="form-control">
                          <option><?php echo htmlspecialchars($personnel_row['conPerson_relationship']); ?></option>
                          <option>-</option>
                          <option>Parent</option>
                          <option>Spouse</option>
                          <option>Child</option>
                          <option>Relative</option>
                          <option>Neighbor</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="personnel-category-card mb-3">
                    <h5 class="personnel-form-title">Employment Details</h5>
                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="do_id">Department</label>
                        <?php
                        $emp_stat_query = $conn->prepare("SELECT do_id, dept_office_name FROM dept_offices WHERE do_id = :do_id LIMIT 1");
                        $emp_stat_query->execute([':do_id' => $personnel_row['do_id']]);
                        $es_row = $emp_stat_query->fetch();
                        ?>
                        <select id="do_id" name="do_id" class="form-control">
                          <?php if ($es_row): ?>
                          <option value="<?php echo htmlspecialchars($es_row['do_id']); ?>"><?php echo htmlspecialchars($es_row['dept_office_name']); ?></option>
                          <?php endif; ?>
                          <option>-</option>
                          <?php
                          $emp_stat_query = $conn->prepare("SELECT do_id, dept_office_name FROM dept_offices ORDER BY dept_office_name ASC");
                          $emp_stat_query->execute();
                          while($es_row = $emp_stat_query->fetch()){
                          ?>
                          <option value="<?php echo htmlspecialchars($es_row['do_id']); ?>"><?php echo htmlspecialchars($es_row['dept_office_name']); ?></option>
                          <?php } ?>
                        </select>
                      </div>

                      <div class="form-group col-md-6">
                        <label for="des_id">Designation</label>
                        <?php
                        $emp_stat_query = $conn->prepare("SELECT des_id, des_name FROM designation WHERE des_id = :des_id LIMIT 1");
                        $emp_stat_query->execute([':des_id' => $personnel_row['des_id']]);
                        $es_row = $emp_stat_query ? $emp_stat_query->fetch() : false;
                        ?>
                        <select id="des_id" name="des_id" class="form-control">
                          <?php if ($es_row): ?>
                          <option value="<?php echo htmlspecialchars($es_row['des_id']); ?>"><?php echo htmlspecialchars($es_row['des_name']); ?></option>
                          <?php else: ?>
                          <option value="">-- Select Designation --</option>
                          <?php endif; ?>
                          <option>-</option>
                          <?php
                          $emp_stat_query = $conn->prepare("SELECT des_id, des_name FROM designation ORDER BY des_name ASC");
                          $emp_stat_query->execute();
                          if ($emp_stat_query) {
                              while($es_row = $emp_stat_query->fetch()){
                          ?>
                          <option value="<?php echo htmlspecialchars($es_row['des_id']); ?>"><?php echo htmlspecialchars($es_row['des_name']); ?></option>
                          <?php } } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="gass_id">Salary Grade/Step | Level | RPD</label>
                        <?php
                        $emp_stat_query = $conn->prepare("SELECT gass_id, gass_name, step, level, ratePerDay FROM gass WHERE gass_id = :gass_id LIMIT 1");
                        $emp_stat_query->execute([':gass_id' => $personnel_row['gass_id']]);
                        $es_row = $emp_stat_query ? $emp_stat_query->fetch() : false;
                        ?>
                        <select id="gass_id" name="gass_id" class="form-control">
                          <?php if ($es_row): ?>
                          <option value="<?php echo htmlspecialchars($es_row['gass_id']); ?>">Salary Grade <?php echo htmlspecialchars($es_row['gass_name']); ?>/<?php echo htmlspecialchars($es_row['step']); ?> | <?php echo htmlspecialchars($es_row['level']); ?> | <?php echo htmlspecialchars($es_row['ratePerDay']); ?></option>
                          <?php else: ?>
                          <option value="">-- Select Salary Grade --</option>
                          <?php endif; ?>
                          <option>-</option>
                          <?php
                          $emp_stat_query = $conn->prepare("SELECT gass_id, gass_name, step, level, ratePerDay FROM gass ORDER BY gass_name ASC");
                          $emp_stat_query->execute();
                          if ($emp_stat_query) {
                              while($es_row = $emp_stat_query->fetch()){
                          ?>
                          <option value="<?php echo htmlspecialchars($es_row['gass_id']); ?>">Salary Grade <?php echo htmlspecialchars($es_row['gass_name']); ?>/<?php echo htmlspecialchars($es_row['step']); ?> | <?php echo htmlspecialchars($es_row['level']); ?> | <?php echo htmlspecialchars($es_row['ratePerDay']); ?></option>
                          <?php } } ?>
                        </select>
                      </div>

                      <div class="form-group col-md-6">
                        <label for="empStat_id">Status of Appointment | Class</label>
                        <?php
                        $emp_stat_query = $conn->prepare("SELECT empStat_id, emp_stat_name, position_class, status FROM emp_status WHERE empStat_id = :empStat_id LIMIT 1");
                        $emp_stat_query->execute([':empStat_id' => $personnel_row['empStat_id']]);
                        $es_row = $emp_stat_query ? $emp_stat_query->fetch() : false;
                        ?>
                        <select id="empStat_id" name="empStat_id" class="form-control">
                          <?php if ($es_row): ?>
                          <option value="<?php echo htmlspecialchars($es_row['empStat_id']); ?>"><?php echo htmlspecialchars($es_row['emp_stat_name']); ?> | <?php echo htmlspecialchars($es_row['position_class']); ?></option>
                          <?php else: ?>
                          <option value="">-- Select Employment Status --</option>
                          <?php endif; ?>
                          <option>-</option>

                          <?php
                          $emp_stat_query = $conn->prepare("SELECT empStat_id, emp_stat_name, position_class, status FROM emp_status ORDER BY emp_stat_name ASC");
                          $emp_stat_query->execute();
                          if ($emp_stat_query) {
                              while($es_row = $emp_stat_query->fetch()){
                          ?>
                          <option value="<?php echo htmlspecialchars($es_row['empStat_id']); ?>" <?php if($es_row['status']==='Active'){ ?> style="color: green;" <?php }else{ ?> style="color: red;" <?php } ?>><?php echo htmlspecialchars($es_row['emp_stat_name']); ?> | <?php echo htmlspecialchars($es_row['position_class']); ?></option>
                          <?php } } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="eligibility">Eligibility</label>
                        <input id="eligibility" value="<?php echo htmlspecialchars($personnel_row['eligibility']); ?>" name="eligibility" type="text" class="form-control">
                      </div>

                      <div class="form-group col-md-2">
                        <label for="plantilla_num">Plantilla Number</label>
                        <input id="plantilla_num" value="<?php echo htmlspecialchars($personnel_row['plantilla_num']); ?>" name="plantilla_num" type="text" class="form-control">
                      </div>

                      <div class="form-group col-md-3">
                        <label for="appointdate">Appointment Date <span class="text-danger">*</span></label>
                        <input value="<?php echo htmlspecialchars($personnel_row['appointment_date']); ?>" name="appointment_date" id="appointdate" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-3">
                        <label for="separatedate">Separation Date <span class="text-danger">*</span></label>
                        <input value="<?php echo htmlspecialchars($personnel_row['separation_date']); ?>" name="separation_date" id="separatedate" type="text" class="form-control" />
                      </div>
                    </div>
                  </div>

                  <div class="personnel-category-card mb-0">
                    <h5 class="personnel-form-title">Government IDs</h5>
                    <div class="form-row mb-0">
                      <div class="form-group col-md-3">
                        <label for="tin">TIN</label>
                        <input value="<?php echo htmlspecialchars($personnel_row['tin_num']); ?>" name="tin_num" id="tin" placeholder="Ex: XXX-XXX-XXX" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-3">
                        <label for="gsis">GSIS BP Number</label>
                        <input value="<?php echo htmlspecialchars($personnel_row['gsis_num']); ?>" name="gsis_num" id="gsis" placeholder="Ex: XXX-XXX-XXX" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-3">
                        <label for="pagibig">Pag-IBIG MID</label>
                        <input value="<?php echo htmlspecialchars($personnel_row['pagibig_num']); ?>" name="pagibig_num" id="pagibig" placeholder="e.g. XXX-XXX-XXX" type="text" class="form-control" />
                      </div>

                      <div class="form-group col-md-3">
                        <label for="philHealth">PhilHealth Number</label>
                        <input value="<?php echo htmlspecialchars($personnel_row['philHealth_num']); ?>" name="philHealth_num" id="philHealth" placeholder="e.g. XXX-XXX-XXX" type="text" class="form-control" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              
               
       
              
            </div>

            <div class="col-lg-12 col-md-12">
              <div class="personnel-form-actions d-flex justify-content-between align-items-center flex-wrap">
                <a href="list_personnel_individual_details.php?dept=<?php echo urlencode($dept_id); ?>&personnel_id=<?php echo urlencode($personnel_id); ?>" class="btn btn-secondary mb-2 mb-md-0">Cancel</a>
                <button name="updatePersonnelComplete" type="submit" class="btn btn-primary">Update Personnel Profile</button>
              </div>
            </div>

          </div>

          </form>
        </div>
      </section>
      
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <!-- JavaScript files-->
    
    <script src="js/formatter.js"></script>
     <?php include('scripts_files.php'); ?>
    <script src = "js/admin.js"></script>
     
    <script>
    
    var bdate = new Formatter (document.getElementById('bdate'), {
      'pattern': '{{99}}/{{99}}/{{9999}}',
      'persistent': true
      });
    
    var separatedate = new Formatter (document.getElementById('separatedate'), {
      'pattern': '{{99}}/{{99}}/{{9999}}',
      'persistent': true
      });
    
    var appointdate = new Formatter (document.getElementById('appointdate'), {
      'pattern': '{{99}}/{{99}}/{{9999}}',
      'persistent': true
      });

    function forceDateCursorToStart(inputId) {
      var el = document.getElementById(inputId);
      if (!el) {
        return;
      }

      function moveCaretToStart() {
        setTimeout(function() {
          try {
            el.setSelectionRange(0, 0);
          } catch (e) {
            // Ignore browsers/inputs that do not support selection ranges.
          }
        }, 0);
      }

      el.addEventListener('focus', moveCaretToStart);
      el.addEventListener('click', moveCaretToStart);
    }

    forceDateCursorToStart('appointdate');
    forceDateCursorToStart('separatedate');
      
        
    var contanct_no = new Formatter (document.getElementById('contact_no'), {
      'pattern': '+639{{999999999}}',
      'persistent': true
      });
      
    var contanct_no2 = new Formatter (document.getElementById('contact_no2'), {
      'pattern': '+639{{999999999}}',
      'persistent': true
      });
      
    var pagibig = new Formatter (document.getElementById('pagibig'), {
      'pattern': '{{999}}-{{999}}-{{999}}-{{999}}',
      'persistent': true
      });
      
    var philHealth = new Formatter (document.getElementById('philHealth'), {
      'pattern': '{{99}}-{{999999999}}-{{9}}',
      'persistent': true
      });
      
    var tin = new Formatter (document.getElementById('tin'), {
      'pattern': '{{999}}-{{999}}-{{999}}',
      'persistent': true
    });
    
    var gsis = new Formatter (document.getElementById('gsis'), {
      'pattern': '{{9999}}-{{999}}-{{999}}',
      'persistent': true
    });
    </script>
     
    
  </body>
</html>