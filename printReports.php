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
            <li class="breadcrumb-item active">Print Reports</li>
          </ul>
        </div>
      </div>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              
              
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="font-weight: bold !important;">REPORTS</strong></a>
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                    
                    <table>
                    <tr>
                    <td style="background-color: white;  border: none;">
                    
                        <div class="dropdown" style="margin-left: 8px;"><a href="printReports.php" class="dropbtn" style="color: white;">ATTENDANCE REPORTS</a></div>
                        
                        <div class="dropdown" style="margin-left: 8px;">
                        
                          <button class="dropbtn">PERSONNEL REPORTS</button>
                          
                          <div class="dropdown-content">
                            <a href="printReports_byAge.php?crw=AGE">Age with Date of Birth</a>
                            <a href="printReports_byEduc.php?crw=EDUCATION">Educational Attainment</a>
                            <a href="printReports_bySeminar.php?crw=SEMINAR">Seminars Attended</a>
                            <a href="printReports_byService.php?crw=SERVICE">Date Hired with No. of Years</a>
                          </div>
                          
                        </div>
                        
                        <div class="dropdown" style="margin-left: 8px;">
                        
                          <button class="dropbtn">COMPANY REPORTS</button>
                          
                          <div class="dropdown-content">
                            <a href="#">Calendar</a>
                          </div>
                          
                        </div>
                        
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="background-color: white;  border: none;">
                    <strong style="margin-left: 8px; font-size: 18px;">ATTENDANCE REPORTS</strong>
                    </td>
                    </tr>
                    </table>
                    
                
                <form action="checkPrintDetails.php" method="POST">
                
                <div style="margin: 10px 10px 10px 12px;" class="form-group row">
                <div class="col-lg-12">
                
                                        <div class="row">
                                          <div class="col-md-4">
                                             <label>Date ( MM/YYYY )</label>
                                            <input type="month" name="dateFrom" value="<?php echo date('Y-m'); ?>" class="form-control" />
                                          </div>
                                          
                                          
                                          <div class="col-md-4">
                                             <label>Type of Report</label>
                                            <select name="doc_type" class="form-control">
                                            <optgroup label="Log Reports"></optgroup>
                                            <option>CS Form 48 (1-15)</option>
                                            <option>CS Form 48 (16-31)</option>
                                            <option>CS Form 48</option>
                                            <option>Detailed DTR</option>
                                            <option>Log Validation History</option>
                                            <optgroup label="Leave, Travel/Seminar Reports"></optgroup>
                                            <option>Leave Application Forms</option>
                                            <option>Leave Summary</option>
                                            </select>
                                          </div>
                                          
                                          
                                          <div class="col-md-4">
                                            <label>Department / Office</label>
                                            <select name="do_id" class="form-control">
                                            <option value="print_all">All</option>
                                            <?php
                                            $emp_stat_query = $conn->query("select * from dept_offices ORDER BY dept_office_name ASC");
                                            while($es_row=$emp_stat_query->fetch()){
                                            ?>
                                            <option value="<?php echo $es_row['do_id']; ?>"><?php echo $es_row['dept_office_name']; ?></option>
                                            <?php } ?>
                                            </select>
                                            
                                          </div>
                                        </div>
                </div>
                </div>
                
                <div class="modal-footer">
                <button name="print_monthly_dtr" type="submit" class="btn btn-primary">Print Preview</button>
                </div>
                </form>
                
                 
                </div>
              </div>
              <!-- kinder End-->
              
              <!-- Custom Personnel Report -->
              <div id="custom-reports" class="card updates recent-updated mt-4">
                <div id="updates-header-custom" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  <a data-toggle="collapse" data-parent="#custom-reports" href="#updates-boxCustom" aria-expanded="true" aria-controls="updates-boxCustom"><strong style="font-weight: bold !important; color: #1a4d2e;">CUSTOM PERSONNEL REPORT</strong></a>
                  </h2><a data-toggle="collapse" data-parent="#custom-reports" href="#updates-boxCustom" aria-expanded="true" aria-controls="updates-boxCustom"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxCustom" role="tabpanel" class="collapse show">
                    
                <form action="printPersonnelCustomReport.php" method="POST" target="_blank">
                
                <div style="margin: 20px;" class="row">
                    <div class="col-lg-12">
                        <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">Report Configuration</h4>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label><strong>Group By:</strong></label>
                        <select name="group_by" class="form-control">
                            <option value="alphabetical">Mixed (Alphabetical)</option>
                            <option value="male_only">Male Only</option>
                            <option value="female_only">Female Only</option>
                            <option value="department">By Department</option>
                            <option value="employment_status">By Employment Status</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 mt-3">
                        <label><strong>Select Columns to Display:</strong></label>
                        <div class="row mt-2" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef;">
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="col_fullname" checked disabled>
                                    <input type="hidden" name="cols[]" value="fullname">
                                    <label class="custom-control-label text-success" for="col_fullname"><strong>Fullname (Default)</strong></label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="sex" class="custom-control-input" id="col_sex">
                                    <label class="custom-control-label" for="col_sex">Sex</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="age" class="custom-control-input" id="col_age">
                                    <label class="custom-control-label" for="col_age">Age</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="dob" class="custom-control-input" id="col_dob">
                                    <label class="custom-control-label" for="col_dob">Date of Birth</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="pob" class="custom-control-input" id="col_pob">
                                    <label class="custom-control-label" for="col_pob">Place of Birth</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="address" class="custom-control-input" id="col_address">
                                    <label class="custom-control-label" for="col_address">Home Address</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="contact" class="custom-control-input" id="col_contact">
                                    <label class="custom-control-label" for="col_contact">Contact Number</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="email" class="custom-control-input" id="col_email">
                                    <label class="custom-control-label" for="col_email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="civil_status" class="custom-control-input" id="col_civil">
                                    <label class="custom-control-label" for="col_civil">Civil Status</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="department" class="custom-control-input" id="col_dept">
                                    <label class="custom-control-label" for="col_dept">Department / Office</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="designation" class="custom-control-input" id="col_desig">
                                    <label class="custom-control-label" for="col_desig">Designation</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="emp_status" class="custom-control-input" id="col_stat">
                                    <label class="custom-control-label" for="col_stat">Employment Status</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="date_hired" class="custom-control-input" id="col_hired">
                                    <label class="custom-control-label" for="col_hired">Date Hired</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="sal_grade" class="custom-control-input" id="col_sg">
                                    <label class="custom-control-label" for="col_sg">Salary Grade/Step</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cols[]" value="monthly_salary" class="custom-control-input" id="col_salary">
                                    <label class="custom-control-label" for="col_salary">Monthly Salary</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer" style="border-top: none; padding: 20px;">
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelectorAll('#updates-boxCustom input[type=checkbox]:not(:disabled)').forEach(cb => cb.checked = true);">Select All Columns</button>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelectorAll('#updates-boxCustom input[type=checkbox]:not(:disabled)').forEach(cb => cb.checked = false);">Deselect All Columns</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-print"></i> Generate Custom Report</button>
                </div>
                </form>
                
                 
                </div>
              </div>
              <!-- Custom Reports End -->
              
              
              
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