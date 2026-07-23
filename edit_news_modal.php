            
            <!-- addSubjKinder Modal -->
                  <div id="editNews<?php echo $news_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Announcement</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <form action="save_add_news.php?news_id=<?php echo $news_id; ?>" method="POST">
                        
                        <div class="modal-body">
                 
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Announcement Title</label>
                              <div class="col-sm-8">
                              <input value="<?php echo $subjK_row['news_title']; ?>" name="news_title" type="text" class="form-control" />
                              </div>
                        </div>
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Contents</label>
                              <div class="col-sm-8">
                              <textarea class="form-control" style="resize: none;" rows="4" name="news_contents"><?php echo $subjK_row['news_contents']; ?></textarea>
                              </div>
                        </div>
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Target Client</label>
                              <div class="col-sm-8">
                              <select name="ipAddress" class="form-control">
                              <option value="<?php echo $subjK_row['ipAddress']; ?>"><?php echo $subjK_row['ipAddress']; ?></option>
                              <?php 
                              $client2_stmt = $conn->prepare("SELECT * FROM client_computer ORDER BY client_id, compName ASC");
                              $client2_stmt->execute();
                              $client2_query = $client2_stmt;
                              while($client2_row = $client2_query->fetch()){ ?>
                              <option value="<?php echo $client2_row['ipAddress']; ?>"><?php echo $client2_row['ipAddress'].' - '.substr($client2_row['description'], 0, 15); ?></option>
                              <?php } ?>
                              </select>
                              <small class="form-text">IP Address - Description</small>
                              </div> 
                        </div>
                        
                        <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Posted by</label>
                              <div class="col-sm-8">
                              <input value="<?php echo $subjK_row['posted_by']; ?>" name="posted_by" type="text" class="form-control" />
                              </div>
                        </div>
                        
                        </div>
                     
                        
                        <div class="modal-footer">
                          <a style="color: white;" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="editNews" type="submit" class="btn btn-primary">Update</button>
                        </div>
                        
                        </form>
                        
                      </div>
                    </div>
                  </div>
                  <!-- end addSubjKinder Modal -->
                  
                  <!-- delete student Modal -->
                          <div id="deleteNews<?php echo $news_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="save_add_news.php?news_id=<?php echo $news_id; ?>" method="POST">
                               
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Delete News</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                   
                                <h4>Are you sure you want to delete announcement:<br /><br /><?php echo $subjK_row['news_title']; ?>?
                                <br />
                                <small class="form-text">
                                <?php echo "Posted by: ".$subjK_row['posted_by']; ?>
                                </small>
                                </h4>
                                  
                                </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                                  <button name="deleteNews" type="submit" class="btn btn-danger">Yes</button>
                                </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!-- end delete student Modal -->