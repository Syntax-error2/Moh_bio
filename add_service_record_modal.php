<!-- ADD SERVICE RECORD Modal -->
<div id="addService_record" tabindex="-1" role="dialog" aria-labelledby="addServiceRecordLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="save_add_personnel.php?dept=<?php echo $_GET['dept']; ?>" method="POST">
      
        <input type="hidden" name="personnel_id" value="<?php echo $_GET['personnel_id']; ?>" />
        
        <div class="modal-header">
          <h5 id="addServiceRecordLabel" class="modal-title">
            <strong>ADD SERVICE RECORD</strong>
            <?php
            if($staff_row['suffix']=="-") {
              echo ' - ' . $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
            } else {
              echo ' - ' . $staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'];
            } 
            ?>
          </h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close">
            <span aria-hidden="true" class="fa fa-times"></span>
          </button>
        </div>
        
        <div class="modal-body">
        
          <div class="form-group row">
            
            <div class="col-sm-12">
              <h6><small>(If married woman, give also full maiden name. Ignore this form if otherwise.)</small></h6>
              
              <div class="row">
              
                <div class="col-md-4">
                  <input name="maid_lname" type="text" class="form-control" />
                  <small class="form-text">Last Name</small>
                </div>
                
                <div class="col-md-4">
                  <input name="maid_fname" type="text" class="form-control" />
                  <small class="form-text">First Name</small>
                </div>
                
                <div class="col-md-4">
                  <input name="maid_mname" type="text" class="form-control" />
                  <small class="form-text">Middle Name</small>
                </div> 
                
              </div>
              
            </div>
            
            
            <div class="col-sm-12 mt-3">
              <h6>SERVICES <small>(Inclusive Dates)</small></h6>
              
              <div class="row">
              
                <div class="col-md-6">
                  <input name="serv_date_from" type="date" class="form-control" />
                  <small class="form-text">From</small>
                </div>
                
                <div class="col-md-6">
                  <input name="serv_date_to" type="date" class="form-control" />
                  <small class="form-text">To</small>
                </div> 
                
              </div>
              
            </div>
            
            
            <div class="col-sm-12 mt-3">
              <h6>RECORD OF APPOINTMENT</h6>
              
              <div class="row">
              
                <div class="col-md-8">
                  <input name="roa_designation" list="DSGTN_list" type="text" class="form-control" />
                  <small class="form-text">Designation</small>
                  
                  <datalist id="DSGTN_list">
                  <?php
                  $dsgtnList_stmt = $conn->prepare("SELECT des_name FROM designation");
                  $dsgtnList_stmt->execute();
                  $dsgtnList_query = $dsgtnList_stmt;
                  while($dsgtn_row = $dsgtnList_query->fetch()){ ?>
                    <option value="<?php echo $dsgtn_row['des_name']; ?>"><?php echo $dsgtn_row['des_name']; ?></option>
                  <?php } ?>
                  </datalist>
                  
                </div>
                
                <div class="col-md-4">
                  <input name="roa_status" list="STATUS_list" type="text" class="form-control" />
                  <small class="form-text">Status</small>
                  
                  <datalist id="STATUS_list">
                  <?php
                  $statusList_stmt = $conn->prepare("SELECT emp_stat_name FROM emp_status ORDER BY emp_stat_name");
                  $statusList_stmt->execute();
                  $statusList_query = $statusList_stmt;
                  while($status_row = $statusList_query->fetch()){ ?>
                    <option value="<?php echo $status_row['emp_stat_name']; ?>"><?php echo $status_row['emp_stat_name']; ?></option>
                  <?php } ?>
                  </datalist>
                </div> 
                
              </div>
              
            </div>
            
            
            <div class="col-sm-12 mt-3">
              
              <div class="row">
              
                <div class="col-md-6">
                  <input name="monthly_salary" id="monthly_salary_add" type="number" step="0.001" min="0" class="form-control" />
                  <small class="form-text">Monthly Salary</small>
                </div>
                
                <div class="col-md-6">
                  <input name="annual_salary" id="annual_salary_add" type="number" step="0.001" min="0" class="form-control" readonly style="background-color: #f0f0f0;" />
                  <small class="form-text">Annual Salary (Auto-computed)</small>
                </div>
                
              </div>
              
            </div>
            
            
            <div class="col-sm-12 mt-3">
              
              <div class="row">
              
                <div class="col-md-12">
                  <input name="office_appointment" list="OA_list" type="text" class="form-control" />
                  <small class="form-text">Office of Appointment</small>
                  
                  <datalist id="OA_list">
                  <?php
                  $fnameList_stmt = $conn->prepare("SELECT dept_office_name FROM dept_offices");
                  $fnameList_stmt->execute();
                  $fnameList_query = $fnameList_stmt;
                  while($fnlq_row = $fnameList_query->fetch()){ ?>
                    <option value="<?php echo $fnlq_row['dept_office_name']; ?>"><?php echo $fnlq_row['dept_office_name']; ?></option>
                  <?php } ?>
                  </datalist>
        
                </div> 
                
              </div>
              
            </div>
            
            
            <div class="col-sm-12 mt-3">
              <h6>SEPARATION</h6>
              
              <div class="row">
              
                <div class="col-md-4">
                  <input name="separate_date" type="date" class="form-control" />
                  <small class="form-text">Date</small>
                </div>
                
                <div class="col-md-8">
                  <input name="separate_cause" type="text" class="form-control" />
                  <small class="form-text">Cause</small>
                </div> 
                
              </div>
              
            </div>
            
          </div>
          
        </div>
        
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
          <button name="add_servRecord" type="submit" class="btn btn-primary">Add Service Record</button>
        </div>
        
      </form>
    </div>
  </div>
</div>
<!-- end ADD SERVICE RECORD Modal -->
