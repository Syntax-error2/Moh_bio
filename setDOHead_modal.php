<!-- edit Class Modal -->
                  <div id="setDOHead<?php echo $do_row['do_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_setDOHead.php?do_id=<?php echo $do_row['do_id']; ?>" method="POST">
                     
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">SET <?php echo $do_row['dept_office_name']; ?> HEAD</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        
                  
                  
                    
                        <div style="margin: 10px 10px 10px 12px;" class="form-group row">
                               
                              <div class="col-lg-12">
                                        <div class="row">
                                          <div class="col-md-12">
                                          
                     
                                            <input type="text" class="form-control" name="personnel_id_code" placeholder="Search personnel by ID code" list="perDataList" id="boxx1" required="true" />
                                  
                                            <datalist id="perDataList">
                                                <?php
                                                
                                                $fnameList_query = $conn->query("SELECT DISTINCT personnel_id_code, lname, fname, mname FROM personnels WHERE personnel_id_code IS NOT NULL AND personnel_id_code != '' ORDER BY lname, fname, mname ASC");
                                                while($fnlq_row = $fnameList_query->fetch()){ ?>
                                                
                                                <option value="<?php echo htmlspecialchars($fnlq_row['personnel_id_code'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($fnlq_row['personnel_id_code'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname'], ENT_QUOTES, 'UTF-8'); ?></option>
                                                
                                                <?php } ?>
                                            </datalist>
                                
                                
                                 
                                
                                            </div> 
                                        </div>
                                </div>
                                
                     </div>
                    
             
                         
                          <div class="modal-footer">
                          <a data-dismiss="modal" aria-label="Close" class="btn btn-default" style="color: white;">Cancel</a>
                          <button name="setDH" class="btn btn-primary" style="color: white;">Set Dept / Office Head</button>
                          
                          </div>  
                         
 
                            
                </form>     
                      </div>
                    </div>
                  </div>
                  <!-- end edit Class Modal -->