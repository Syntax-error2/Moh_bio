                            <?php
                            require_once('personnel_files_lib.php');
                            pfm_ensure_schema($conn);

                            $session_access = $session_access ?? ($_SESSION['access'] ?? '');
                            $user_personnel_id = $user_personnel_id ?? (int)($_SESSION['user_personnel_id'] ?? 0);
                            $personnel_id = $personnel_id ?? ($_GET['personnel_id'] ?? 0);

                            
                            $dept = $_GET['dept'] ?? '';
                            $searched = $_GET['searched'] ?? '';
                            if($dept === 'All'){
                            
                            $editPer_query_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id_code LIKE :searched_code OR lname LIKE :searched_lname ORDER BY lname, fname ASC");
                            $editPer_query_stmt->execute([
                              ':searched_code' => '%' . $searched . '%',
                              ':searched_lname' => '%' . $searched . '%'
                            ]);
                            $editPer_query = $editPer_query_stmt;
                             
                             
                            }else{
                                
                            $editPer_query_stmt = $conn->prepare("SELECT * FROM personnels WHERE do_id = :dept ORDER BY lname, fname ASC");
                            $editPer_query_stmt->execute([':dept' => $dept]);
                            $editPer_query = $editPer_query_stmt;
                             
                            }
                            
                            
                            while ($editPer_row = $editPer_query->fetch()) 
                            { 
                                $personnel_id=$editPer_row['personnel_id'];
                              $defaultFolderId = pfm_ensure_default_201_folder($conn, (int)$personnel_id);

                              $folder_stmt = $conn->prepare("SELECT folder_id, folder_name, is_system_201 FROM personnel_file_folders WHERE personnel_id = :personnel_id ORDER BY is_system_201 DESC, folder_name ASC");
                              $folder_stmt->execute([':personnel_id' => $personnel_id]);
                              $folder_rows = $folder_stmt->fetchAll(PDO::FETCH_ASSOC);
                              $canManageFolders = pfm_can_manage_personnel_files($session_access, $user_personnel_id, $personnel_id);
                                
                                
                        if($editPer_row['mname']=='')
                        {
                            $finalMName='';
                            
                        }else{
                            
                            if($editPer_row['suffix']=='-') { $suffix=''; }else{ $suffix=$editPer_row['suffix'].' '; }
                            
                            $finalMName=$suffix.substr($editPer_row['mname'], 0,1).'.';
                        } ?>

                        
                        
                        
                        
                <!--Add 201 Files Modal -->
            
                  <div id="add201Files<?php echo $personnel_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                       <form action="save_add_personnel.php?dept=<?php echo $editPer_row['do_id']; ?>" method="POST" enctype="multipart/form-data">
                       <input value="<?php echo $personnel_id; ?>" name="personnel_id" type="hidden" />
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add 201 File [ <?php echo $editPer_row['lname'].', '.$editPer_row['fname'].' '.$finalMName; ?> ]</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
           
                      
                            <div class="form-group row">
                               
                              <div class="col-sm-12">
                              
                              <div class="row">
                                <div class="col-md-12">
                                <input name="RFTag_id" type="hidden" value="<?php echo $editPer_row['RFTag_id']; ?>" />
                                <label>Select Folder</label>
                                <select class="form-control" name="folder_id" required>
                                  <?php foreach($folder_rows as $folder_row){ ?>
                                    <option value="<?php echo $folder_row['folder_id']; ?>" <?php if((int)$folder_row['folder_id'] === (int)$defaultFolderId){ ?>selected<?php } ?>>
                                      <?php echo htmlspecialchars($folder_row['folder_name']); ?><?php if((int)$folder_row['is_system_201']===1){ ?> (default)<?php } ?>
                                    </option>
                                  <?php } ?>
                                </select>
                                <small class="form-text text-muted">Only Administrator / HR Head can upload in 201-files folder.</small>
                                <hr />
                                <input class="form-control" name="per_file" type="file" />
                                <small>Browse Local Files</small>
                                </div>
                              </div>

                              <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                  <label>Create New Folder</label>
                                  <div class="input-group">
                                    <input class="form-control" name="folder_name" type="text" placeholder="Folder name" />
                                    <div class="input-group-append">
                                      <button name="save_createFileFolder" type="submit" class="btn btn-outline-secondary">Create</button>
                                    </div>
                                  </div>
                                  <small class="form-text text-muted">Reserved name: 201-files</small>
                                </div>
                              </div>
                                
                              </div>
                            </div>
                         
    
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="save_add201File" type="submit" class="btn btn-primary">Upload File</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end Add 201 Files Modal -->



                <!--download 201 Files Modal -->
            
                  <div id="download201Files<?php echo $personnel_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                        
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Download 201 File [ <?php echo $editPer_row['lname'].', '.$editPer_row['fname'].' '.$finalMName; ?> ]</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
           
                      
                            <div class="form-group row">
                              
                              <div class="col-sm-12">
                              
                              <div class="row">
                                <div class="col-md-12">

                                    <h6 style="margin-bottom: 10px;">Folders</h6>
                                    <table cellspacing="0" class="table table-bordered table-sm">
                                        <thead>
                                          <th style="width: 45%;">Folder</th>
                                          <th style="width: 35%;">Action</th>
                                          <th style="width: 20%;">Status</th>
                                        </thead>
                                        <tbody>
                                        <?php foreach($folder_rows as $folder_row){ ?>
                                            <tr>
                                              <td>
                                                <?php if($canManageFolders && (int)$folder_row['is_system_201'] !== 1){ ?>
                                                <form action="save_add_personnel.php?dept=<?php echo $editPer_row['do_id']; ?>" method="POST" class="form-inline" style="display: flex; gap: 6px;">
                                                  <input type="hidden" name="personnel_id" value="<?php echo $personnel_id; ?>" />
                                                  <input type="hidden" name="folder_id" value="<?php echo $folder_row['folder_id']; ?>" />
                                                  <input type="text" name="folder_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($folder_row['folder_name']); ?>" required style="max-width: 220px;" />
                                                  <button name="save_renameFileFolder" type="submit" class="btn btn-outline-primary btn-sm">Rename</button>
                                                </form>
                                                <?php }else{ ?>
                                                  <small><?php echo htmlspecialchars($folder_row['folder_name']); ?></small>
                                                <?php } ?>
                                              </td>
                                              <td>
                                                <?php if($canManageFolders && (int)$folder_row['is_system_201'] !== 1){ ?>
                                                  <form action="save_add_personnel.php?dept=<?php echo $editPer_row['do_id']; ?>" method="POST" onsubmit="return confirm('Delete this folder? It must be empty.');">
                                                    <input type="hidden" name="personnel_id" value="<?php echo $personnel_id; ?>" />
                                                    <input type="hidden" name="folder_id" value="<?php echo $folder_row['folder_id']; ?>" />
                                                    <button name="save_deleteFileFolder" type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                                  </form>
                                                <?php }else{ ?>
                                                  <small>-</small>
                                                <?php } ?>
                                              </td>
                                              <td>
                                                <?php if((int)$folder_row['is_system_201'] === 1){ ?>
                                                  <small class="text-muted">Protected</small>
                                                <?php }else{ ?>
                                                  <small class="text-muted">Custom</small>
                                                <?php } ?>
                                              </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>

                                    <hr />
                                    <h6 style="margin-bottom: 10px;">Files</h6>

                                    <table cellspacing="0" class="table table-bordered">
                                        <thead>
                                          <th>Folder</th>
                                          <th>File Name</th>
                                          <th>Date Uploaded</th>
                                          <th></th>
                                        </thead>
                                        <tbody>
                                        <?php

                                        $dl_201_stmt = $conn->prepare("SELECT f.file_id, f.file_name, f.date_time_uploaded, ff.folder_name, ff.is_system_201
                                                        FROM files f
                                                        LEFT JOIN personnel_file_folders ff ON f.folder_id = ff.folder_id
                                                        WHERE f.personnel_id = :personnel_id
                                                        ORDER BY ff.is_system_201 DESC, ff.folder_name ASC, f.file_id DESC");
                                        $dl_201_stmt->execute([':personnel_id' => $personnel_id]);
                                        $currentFolderName = '';
                                        while($dl201_row=$dl_201_stmt->fetch()){ ?>
                                            <?php $rowFolderName = (string)($dl201_row['folder_name'] ?? '201-files'); ?>
                                            <?php if($rowFolderName !== $currentFolderName){ $currentFolderName = $rowFolderName; ?>
                                            <tr>
                                              <td colspan="4" style="background: #f8f9fa;"><strong><?php echo htmlspecialchars($currentFolderName); ?></strong></td>
                                            </tr>
                                            <?php } ?>
                                        
                                            <tr>
                                          <td><small><?php echo htmlspecialchars($rowFolderName); ?></small></td>
                                          <td><small><?php echo htmlspecialchars(basename((string)$dl201_row['file_name'])); ?></small></td>
                                            <td><small><?php echo $dl201_row['date_time_uploaded']; ?></small></td>
                                            <td>
                                            <center>
                                           
                                          <a title="Download file..." href="download_201.php?file_id=<?php echo $dl201_row['file_id']; ?>"><span class="fa fa-download" aria-hidden="true"></span></a> 
                                            <br />
                                            <?php if(pfm_is_admin($session_access) || ((int)$dl201_row['is_system_201'] !== 1 && (int)$personnel_id === (int)$user_personnel_id)){ ?>
                                            <a title="Remove file..." href="delete201Files.php?dept=<?php echo $editPer_row['do_id']; ?>&file_id=<?php echo $dl201_row['file_id']; ?>" style="color: red;"><span class="fa fa-times" aria-hidden="true"></span></a>
                                          <?php } ?>
                                            </center>
                                            </td>
                                            </tr>
                                            
                                        <?php } ?>
                                        
                                        </tbody>
                                    </table>
                                    
                                </div>
                              </div>
                                
                              </div>
                            </div>
                         
    
                        </div>
                        
                       
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end download 201 Files Modal -->
                  
                  <!--update Shift Modal -->
            
                  <div id="updateShift<?php echo $personnel_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                       <form action="save_add_personnel.php?dept=<?php echo $editPer_row['do_id']; ?>" method="POST" enctype="multipart/form-data">
                       
                       
                       <input value="<?php echo $personnel_id; ?>" name="personnel_id" type="hidden" />
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Set Shift [ <?php echo $editPer_row['lname'].', '.$editPer_row['fname'].' '.$finalMName; ?> ]</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
           
                      
                            <div class="form-group row">
                               
                              <div class="col-sm-12">
                              
                              <div class="row">
                                <div class="col-md-12">
                                <?php
                                  $shift_stmt = $conn->prepare("SELECT * FROM shifts WHERE shift_id = :shift_id");
                                  $shift_stmt->execute([':shift_id' => $editPer_row['shift_id']]);
                                  $emp_stat_query = $shift_stmt;
                                  $es_row=$emp_stat_query->fetch();
                                  ?>
                                    <select name="shift_id" class="form-control">
                                    <option value="<?php echo $es_row['shift_id']; ?>"><?php echo $es_row['shift_name'].' ( '.$es_row['type'].' )'; ?></option>
                                    <option value="0">-</option>
                                    <?php
                                    $dept = $_GET['dept'] ?? '';
                                    $shift_list_stmt = $conn->prepare("SELECT * FROM shifts WHERE do_id = :dept OR do_id = 0 ORDER BY shift_name ASC");
                                    $shift_list_stmt->execute([':dept' => $dept]);
                                    $emp_stat_query = $shift_list_stmt;
                                    while($es_row=$emp_stat_query->fetch()){
                                    ?>
                                    <option value="<?php echo $es_row['shift_id']; ?>"><?php echo $es_row['shift_name'].' ( '.$es_row['type'].' )'; ?></option>
                                    <?php } ?>
                                    
                                    </select>
                                    <small class="form-text">Work-Hour Shift</small>
                                </div>
                              </div>
                                
                              </div>
                            </div>
                         
    
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="set_shift" type="submit" class="btn btn-primary">Update</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end update Shift Modal -->
                  
                        <!-- delete student Modal -->
                          <div id="deletePersonnel<?php echo $personnel_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_personnel.php?dept=<?php echo $editPer_row['do_id']; ?>" method="POST">
                              <input name="personnel_id" value="<?php echo $personnel_id; ?>" type="hidden" />
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete Personnel</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete personnel:<br /><br /><?php echo $editPer_row['lname'].", ".$editPer_row['fname']." ".$finalMName; ?>?
                                <br />
                                <small class="form-text"> </small>
                                </h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deleteStudent" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete student Modal -->
                          
                          
                          
    
         
                  
   <?php } ?>               