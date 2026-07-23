
                    <div class="col-lg-12" style="margin-bottom: 12px;">
                    <div class="table-responsive" style="margin-top: 12px;">
                    
                    <table id="" class="display personnel-table" style="width:100%">
                      
                      <thead>
                        <tr>
                          <th>Personnel</th>
                          <th>Current Shift</th>
                          <th style="text-align: center;">Actions</th>
                        </tr>
                      </thead>
                      
                      <tbody>
                      
                            <?php
                            
                            $dept_id = $_GET['dept'] ?? '';
                            $staff_query = $conn->prepare("SELECT * FROM personnels WHERE do_id = :dept_id AND separation_date IS NULL ORDER BY lname, fname ASC");
                            $staff_query->execute([':dept_id' => $dept_id]);
                            while ($staff_row = $staff_query->fetch()){
                                
                            $personnel_id=$staff_row['personnel_id'];
                            
                            ?>
           
                            <tr>
                            <td>
                            <div class="personnel-cell d-flex align-items-center">
                              <a href="updateStudentImg.php?personnel_id=<?php echo $personnel_id; ?>&dept=<?php echo $_GET['dept']; ?>">
                                <img src="personnelImg/<?php echo $staff_row['img']; ?>" width="64" height="64" class="personnel-avatar img-fluid rounded" />
                              </a>
                              <div class="personnel-meta">
                                <div class="personnel-code-row">
                                  <small class="badge badge-light border">ID: <?php echo htmlspecialchars($staff_row['personnel_id_code']); ?></small>
                                  <i class="fa fa-barcode"></i> <?php echo htmlspecialchars($staff_row['RFTag_id']); ?>
                                </div>
                                <div class="personnel-name">
                                <?php
                                if($staff_row['suffix']=="-")
                                {
                                  echo htmlspecialchars($staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']);
                                }else{
                                  echo htmlspecialchars($staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix']);
                                }
                                ?>
                                </div>
                              </div>
                            </div>
                            </td>

                            <td>
                              <?php
                              $emp_stat_query5 = $conn->prepare("SELECT * from shifts WHERE shift_id = :shift_id");
                              $emp_stat_query5->execute([':shift_id' => $staff_row['shift_id']]);
                              $es_row5=$emp_stat_query5->fetch();
                              ?>
                              <a title="Shift settings..." class="shift-chip" style="<?php if($es_row5['type'] == 'Regular Shift'){ ?> color: green; <?php }elseif($es_row5['type'] == 'Night Shift'){ ?> color: blue; <?php }elseif($es_row5['type'] == '24 Hours Shift'){ ?> color: brown; <?php }elseif($es_row5['type'] == 'Open Time'){ ?> color: purple; <?php }else{ ?> color: red; <?php } ?>" data-toggle="modal" data-target="#updateShift<?php echo $personnel_id; ?>" href="#"><i class="fa fa-clock-o"></i> <?php if(!empty($es_row5)){ echo htmlspecialchars($es_row5['shift_name']); }else{ echo "Not Set"; } ?> <small <?php if(empty($es_row5)){?> style="color: red;" <?php } ?>>( <?php if(!empty($es_row5)){ echo htmlspecialchars($es_row5['type']); }else{ echo "Not Set"; } ?> )</small></a>
                            </td>
                           
                           <td style="text-align: center;">
                             <a title="View complete personnel data..." href="list_personnel_individual_details.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $personnel_id; ?>" class="btn btn-info btn-sm mr-1 text-white"><i class="fa fa-info-circle"></i></a>
                             <button data-toggle="dropdown" type="button" class="btn btn-outline-primary btn-sm">&nbsp;<i class="fa fa-ellipsis-v"></i>&nbsp;</button>
                            <div class="dropdown-menu">

                            <a title="Edit personnel..." href="edit_completePersonnelData.php?dept=<?php echo $staff_row['do_id']; ?>&personnel_id=<?php echo $personnel_id; ?>" class="dropdown-item"><i class="fa fa-pencil"></i> Edit Profile</a>
                            <a title="Archive personnel..." data-toggle="modal" data-target="#deletePersonnel<?php echo $personnel_id; ?>" href="#" class="dropdown-item text-danger"><i class="fa fa-archive"></i> Archive Personnel</a>
                            <div class="dropdown-divider"></div>
                            
                            <a title="Print Civil Service Form 48..." data-toggle="modal" data-target="#print_monthly_attendance_csf48<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> CSForm 48</a>
                            <a title="Print detailed DTR..." data-toggle="modal" data-target="#print_monthly_attendance<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-print"></i> Detailed DTR <small>(Monthly)</small></a>
                            <a title="Print Log Validations history..." data-toggle="modal" data-target="#print_monthly_LV<?php echo $staff_row['RFTag_id']; ?>" href="#" class="dropdown-item"><i class="fa fa-image"></i> Log Validation History <small>(Monthly)</small></a>
                            
                            <?php
                            
                            $emp_stat_query = $conn->prepare("SELECT emp_stat_name FROM emp_status WHERE empStat_id = :empStat_id");
                            $emp_stat_query->execute([':empStat_id' => $staff_row['empStat_id']]);
                            if ($emp_stat_query) {
                                $empstat_row = $emp_stat_query->fetch();
                                
                                if($empstat_row && ($empstat_row['emp_stat_name'] == "Casual" OR $empstat_row['emp_stat_name'] == "Permanent")){ ?>
                                <div class="dropdown-divider"></div>
                                
                                <!-- Leave Management Dropdown -->
                                <h6 class="dropdown-header"><i class="fa fa-calendar"></i> Leave Management</h6>
                                <a href="#" data-toggle="modal" data-target="#add_leave_application" class="dropdown-item" onclick="setPersonnelForLeaveApp(<?php echo $personnel_id; ?>, '<?php echo htmlspecialchars($staff_row['lname'] . ', ' . $staff_row['fname'], ENT_QUOTES); ?>')">
                                    <i class="fa fa-file-text"></i> Leave Application
                                </a>
                                <a href="leave_card.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $personnel_id; ?>" class="dropdown-item">
                                    <i class="fa fa-book"></i> Leave Card
                                </a>
                                <?php } else { ?>
                                <div class="dropdown-divider"></div>
                                
                                <!-- Leave Management - Not Available -->
                                <h6 class="dropdown-header"><i class="fa fa-calendar"></i> Leave Management</h6>
                                <a href="edit_completePersonnelData.php?dept=<?php echo $staff_row['do_id']; ?>&personnel_id=<?php echo $personnel_id; ?>" class="dropdown-item text-muted">
                                    <i class="fa fa-exclamation-triangle"></i> Set Employment Status
                                </a>
                                <?php }
                            } ?>
                            
                            <div class="dropdown-divider"></div>
                            
                            <a title="View 201 files..." data-toggle="modal" data-target="#download201Files<?php echo $personnel_id; ?>" href="#" class="dropdown-item"><i class="fa fa-search"></i>  201 File Archive</a>
                            <a title="Add/Upload 201 files..." data-toggle="modal" data-target="#add201Files<?php echo $personnel_id; ?>" href="#" class="dropdown-item"><i class="fa fa-plus"></i> 201 Files</a>
                           
                            </div>
                            
                           </td>
                           
                        </tr>
                        
                        <?php include('print_monthly_attendance_modal_csf48.php'); ?>
                        <?php include('print_monthly_attendance_modal.php'); ?>
                        <?php include('print_monthly_LV_modal.php'); ?>
                        <?php include('print_monthly_DTRNotes_modal.php'); ?>
                        <?php include('print_yearly_DTRSummary_modal.php'); ?>
                        
                         <?php } ?>
                         
                      </tbody>
                      
                    </table>
                    
                    </div>
                    </div>
                    
                    <!-- Leave Application Modal (Outside Loop) -->
                    <?php include('add_leave_application_modal_list.php'); ?>