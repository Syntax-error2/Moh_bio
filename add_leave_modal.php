<!-- Add Travel Modal -->
                  <div id="addLeave" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_travel_leave.php" method="POST">
                      
                      <?php
                                function randomcode() {
                                $var = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                                srand((double)microtime()*1000000);
                                $i = 0;
                                $code = '';
                                while ($i <= 9) {
                                $num = rand() % 33;
                                $tmp = substr($var, $num, 1);
                                $code = $code . $tmp;
                                $i++;
                                }
                                return $code;
                                }
                         ?>
                         
                      <input name="leave_code" type="hidden" value="<?php echo randomcode(); ?>" />
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">ADD LEAVE ENTRY</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                         
                        <div style="margin: 10px 10px 10px 12px;" class="form-group row">
                        
                        <script src="js/jquery-latest.js"></script>
       
                        
                        <div class="col-lg-12">
                            <div class="row">
                            
                            <div class="col-lg-12">
                                <div class="row">
                                  <div class="col-md-12">
                                    <select name="remarks" class="form-control" id='purpose'>
                                    <option>--Please Select--</option>
                                    <option>Vacation Leave</option>
                                    <option>Sick Leave</option>
                                    <option>Maternity Leave (RA 8282)</option>
                                    <option>Paternity Leave (RA 8187)</option>
                                    <option>Parental Leave for Solo Parents (RA 8972)</option>
                                    <option value="1">Others, please specify...</option>
                                    </select>
                                    <small class="form-text">Select Leave Type</small>
                                  </div>
                                  
                                  <div style="display:none;" id="business" class="col-md-12">
                                  <input type="text" name="leave_spec" class="form-control" />
                                  <small>Leave Specification</small>
                                  </div>
                                    
                                </div>
                              </div>
                              
                              
                              <script>
                              $(document).ready(function(){
                                $('#purpose').on('change', function() {
                                  if ( this.value == '1')
                                  {
                                    $("#business").show();
                                  }
                                  else
                                  {
                                    $("#business").hide();
                                  }
                                });
                            });
                              </script>
                              
                              
                              
                              <div class="col-md-12">
                                 <div class="my-formx">
                                  
                                        <p class="text-boxx">
                                            <label for="boxx1">Leave Applicant</label>
                                            <input type="text" class="form-control" name="jud1" placeholder="Search personnel fullname" list="perDataList" id="boxx1" required="true" />
                                            <small>Select Personnel</small>
                                        </p>
                                         
                                </div>
                                
                                <datalist id="perDataList">
                                    <?php
                                    
                                    $fnameList_stmt = $conn->prepare("SELECT DISTINCT RFTag_id, lname, fname, mname FROM personnels");
                                    $fnameList_stmt->execute();
                                    $fnameList_query = $fnameList_stmt;
                                    while($fnlq_row = $fnameList_query->fetch()){ ?>
                                    
                                    <option value="<?php echo $fnlq_row['RFTag_id'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?>"><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></small></option>
                                    
                                    <?php } ?>
                                </datalist>
                                
                                
                                 
                                
                            </div> 
                            
                            <div class="col-md-12">
                                <div class="my-form">
           
                                        <p class="text-box">
                                            <label for="box1">Date No. <span class="box-number">1</span></label>
                                            <input type="date" class="form-control" name="con1" id="box1" required="true" />
                                            
                                        </p>
                                        <p><a class="add-box" style="background-color: green; border: 1px solid blue; color: white; padding: 4px 8px 4px 8px; cursor: pointer;"><small>Add Date</small></a></p>
                                  
                                </div>
                            </div>
                           
                                <script>
                                jQuery(document).ready(function($){
                                    $('.my-form .add-box').click(function(){
                                        var n = $('.text-box').length + 1;
                                        if( 30 < n ) {
                                            alert('Maximum number of contestants reach!');
                                            return false;
                                        }
                                        var box_html = $('<p class="text-box"><label for="box' + n + '">Date No. <span class="box-number">' + n + '</span></label> <input type="date" class="form-control" name="con' + n + '" id="box' + n + '" required="true" /><a style="background-color: red; border: 1px solid black; color: white; padding: 4px 8px 4px 8px; cursor: pointer; margin-top:4px;" class="remove-box"><small>Remove</small></a></p>');
                                        box_html.hide();
                                        $('.my-form p.text-box:last').after(box_html);
                                        box_html.fadeIn('slow');
                                        return false;
                                    });
                                    $('.my-form').on('click', '.remove-box', function(){
                                        $(this).parent().css( 'background-color', '#FF6C6C' );
                                        $(this).parent().fadeOut("slow", function() {
                                            $(this).remove();
                                            $('.box-number').each(function(index){
                                                $(this).text( index + 1 );
                                            });
                                        });
                                        return false;
                                    });
                                });
                                </script>
                                
                                
                                <div class="col-md-12">
                                <input name="no_of_days" class="form-control" type="number" min="1" max="200" step="1" value="1" />
                                <small>Total Number of Days</small>
                                </div>
                                
                            </div>
                        </div>
                        
                        
                        <div class="col-sm-12">
                                <div class="row">
           
                                  <div class="col-md-12">
                                    <input name="leave_type_desc" type="text" class="form-control" />
                                    <small class="form-text">Leave Description (Optional)</small>
                                  </div>
                                  
                                  <div class="col-md-12">
                                    <input name="substitute_rfid" type="text" list="perSubstituteDataList" class="form-control" />
                                    <small class="form-text">Substitute Personnel (Optional)</small>
                                  </div>
                                  
                                  <datalist id="perSubstituteDataList">
                                    <?php
                                    
                                    $fnameList_stmt = $conn->prepare("SELECT DISTINCT RFTag_id, lname, fname, mname FROM personnels");
                                    $fnameList_stmt->execute();
                                    $fnameList_query = $fnameList_stmt;
                                    while($fnlq_row = $fnameList_query->fetch()){ ?>
                                    
                                    <option value="<?php echo $fnlq_row['RFTag_id'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?>"><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></small></option>
                                    
                                    <?php } ?>
                                  </datalist>
                                  
                                </div>
                              </div>
         
         
                            </div>
                    
             
                         
                          <div class="modal-footer">
                          <button name="add_leave" type="submit" class="btn btn-primary">SAVE LEAVE ENTRY</button>
                          </div>  
                          
                        </form>     
                      </div>
                    </div>
                  </div>
                  <!-- end Add Travel Modal -->