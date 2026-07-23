            
            <!-- addSubjKinder Modal -->
                  <div id="addSubjKinder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Add Announcement</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <form action="save_add_news.php" method="POST">
                        <div class="modal-body">
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Announcement Title</label>
                              <div class="col-sm-8">
                              <input name="news_title" type="text" class="form-control" style="text-transform: uppercase;" />
                              </div>
                        </div>
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Contents</label>
                              <div class="col-sm-8">
                              <textarea class="form-control" style="resize: none;" style="text-transform: uppercase;" rows="4" name="news_contents" ></textarea>
                              </div>
                        </div>
                        
                        <hr />
                        <div class="form-group row">
                             
                              
                              <div class="col-sm-12">
                              <p><strong>Select Target Client</strong></p>
                              
                              <input type="text" class="form-control" id="myInputAddSched" onkeyup="myFunctionAddSched()" placeholder="Search for client details..." title="Type a client details..." />
                  
                              <table class="table table-striped table-hover table-sm" id="myTableAddSched">
                              
                              <thead>
                              <tr>
                              <th></th>
                              <th>Client CPU Details</th>
                              </tr>
                              </thead>
                              
                              <tbody>
                              <?php
                              
                              $client2_stmt = $conn->prepare("SELECT * FROM client_computer ORDER BY client_id, compName ASC");
                              $client2_stmt->execute();
                              $client2_query = $client2_stmt;
                              while($client2_row = $client2_query->fetch()){ ?>
                               
                              <tr>
                              <td><input name="checkbox[]" type="checkbox" value="<?php echo $client2_row['ipAddress']; ?>" /></td>
                              <td><?php echo substr($client2_row['description'], 0, 15).'<br /><small>'.$client2_row['ipAddress'].'</small>'; ?></td>
                              </tr>
                               <?php } ?>
                              </tbody>
                              
                              </table>
                               
                               </div>
                            </div>
                            <hr />
  
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Posted by</label>
                              <div class="col-sm-8">
                              <input name="posted_by" type="text" class="form-control" />
                              </div>
                        </div>
                        
                        
                        </div>
                     
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="addNews" type="submit" class="btn btn-primary">Add</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end addSubjKinder Modal -->
                  
                  