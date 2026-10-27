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
                                    <option value="Mandatory/Forced Leave">Mandatory/Forced Leave</option>
                                    <option>Sick Leave</option>
                                    <option>Wellness Leave</option>
                                    <option>Emergency Leave</option>
                                    <option>Maternity Leave (RA 8282)</option>
                                    <option>Paternity Leave (RA 8187)</option>
                                    <option>Parental Leave for Solo Parents (RA 8972)</option>
                                    <option>Monetization</option>
                                    <option>Terminal Leave</option>
                                    <option value="1">Others, please specify...</option>
                                    </select>
                                    <small class="form-text">Select Leave Type</small>
                                    <small class="form-text text-info">For official business or travel, use Add Travel Order. It appears in the DTR without using leave credits.</small>
                                  </div>
                                  
                                  <div style="display:none;" id="business" class="col-md-12">
                                  <input type="text" name="leave_spec" class="form-control" />
                                  <small>Leave Specification</small>
                                  </div>
                                  <div style="display:none; margin-top: 10px;" id="monetize_source_div" class="col-md-12">
                                    <select name="monetization_source" class="form-control" id="monetization_source">
                                      <option value="auto">Auto (Deduct from Vacation Leave first, then Sick Leave)</option>
                                      <option value="vl">Vacation Leave only</option>
                                      <option value="sl">Sick Leave only</option>
                                    </select>
                                    <small class="form-text text-muted" id="charge_source_label">Charge to</small>
                                  </div>
                                  <div style="display:none; margin-top: 10px;" id="forced_leave_info" class="col-md-12">
                                    <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.88rem; border-left: 4px solid #f39c12; background-color: #fff8e7; color: #8a6d3b;">
                                      <strong><i class="fa fa-info-circle"></i> Mandatory/Forced Leave (Up to 5 Days Only):</strong><br>
                                      &bull; Deducted 1:1 against <strong>Vacation Leave (VL)</strong> credits.<br>
                                      &bull; Strictly maximum of <strong>5 days</strong> only per application / per year (CSC Rules).
                                    </div>
                                  </div>
                                  <div style="display:none; margin-top: 10px;" id="wellness_leave_info" class="col-md-12">
                                    <div class="alert alert-success py-2 px-3 mb-0" style="font-size: 0.88rem; border-left: 4px solid #27ae60; background-color: #eafaf1; color: #1e8449;">
                                      <strong><i class="fa fa-heartbeat"></i> Wellness Leave:</strong><br>
                                      &bull; Special non-cumulative privilege &mdash; <strong>No deduction</strong> from Vacation or Sick Leave credits.
                                    </div>
                                  </div>
                                  <div style="display:none; margin-top: 10px;" id="emergency_leave_info" class="col-md-12">
                                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.88rem; border-left: 4px solid #17a2b8; background-color: #e8f4f8; color: #0c5460;">
                                      <strong><i class="fa fa-exclamation-circle"></i> Emergency Leave:</strong><br>
                                      &bull; Special emergency privilege &mdash; <strong>No deduction</strong> from Vacation or Sick Leave credits.
                                    </div>
                                  </div>
                                    
                                </div>
                              </div>
                              
                              
                              <script>
                              $(document).ready(function(){
                                function checkForcedLeave() {
                                  var val = $('#purpose').val();
                                  var isFL = (val === 'Mandatory/Forced Leave' || val === 'Forced Leave');
                                  if (isFL) {
                                    $("#forced_leave_info").slideDown();
                                    var $noDays = $('input[name="no_of_days"]');
                                    $noDays.attr('max', '5');
                                    if (parseInt($noDays.val(), 10) > 5) {
                                      $noDays.val(5);
                                    }
                                    var $boxes = $('.my-form .text-box');
                                    if ($boxes.length > 5) {
                                      alert('Mandatory/Forced Leave allows a maximum of 5 days only. Extra date fields have been removed.');
                                      $boxes.slice(5).remove();
                                      $('.box-number').each(function(index){
                                        $(this).text( index + 1 );
                                      });
                                    }
                                  } else {
                                    $("#forced_leave_info").slideUp();
                                    $('input[name="no_of_days"]').attr('max', '200');
                                  }
                                }

                                $('#purpose').on('change', function() {
                                  if ( this.value == '1')
                                  {
                                    $("#business").show();
                                  }
                                  else
                                  {
                                    $("#business").hide();
                                  }
                                  if ( this.value == 'Monetization')
                                  {
                                    $("#charge_source_label").text("Charge Monetization to");
                                    $("#monetize_source_div").show();
                                  }
                                  else if ( this.value == 'Terminal Leave')
                                  {
                                    $("#charge_source_label").text("Charge Terminal Leave to");
                                    $("#monetize_source_div").show();
                                  }
                                  else
                                  {
                                    $("#monetize_source_div").hide();
                                  }
                                  if ( this.value == 'Wellness Leave')
                                  {
                                    $("#wellness_leave_info").slideDown();
                                  }
                                  else
                                  {
                                    $("#wellness_leave_info").slideUp();
                                  }
                                  if ( this.value == 'Emergency Leave')
                                  {
                                    $("#emergency_leave_info").slideDown();
                                  }
                                  else
                                  {
                                    $("#emergency_leave_info").slideUp();
                                  }
                                  checkForcedLeave();
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
                                    
                                    $fnameList_query = $conn->query("SELECT personnel_id, lname, fname, mname FROM personnels");
                                    while($fnlq_row = $fnameList_query->fetch()){ ?>
                                    
                                    <option value="<?php echo $fnlq_row['personnel_id'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?>"><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></option>
                                    
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
                                    function isForcedLeaveActive() {
                                        var p = $('#purpose').val();
                                        return (p === 'Mandatory/Forced Leave' || p === 'Forced Leave');
                                    }

                                    $('.my-form .add-box').click(function(){
                                        var currentCount = $('.text-box').length;
                                        if (isForcedLeaveActive() && currentCount >= 5) {
                                            alert('Mandatory/Forced Leave is strictly limited to a maximum of 5 days only!');
                                            return false;
                                        }
                                        var n = currentCount + 1;
                                        if( 30 < n ) {
                                            alert('Maximum number of dates reached!');
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


                                    $('input[name="no_of_days"]').on('input change', function(){
                                        if (isForcedLeaveActive()) {
                                            var val = parseInt($(this).val(), 10);
                                            if (val > 5) {
                                                alert('Mandatory/Forced Leave is strictly limited to a maximum of 5 days only!');
                                                $(this).val(5);
                                            }
                                        }
                                    });

                                    $('#addLeave form').on('submit', function(e){
                                        if (isForcedLeaveActive()) {
                                            var daysVal = parseInt($('input[name="no_of_days"]').val(), 10) || 0;
                                            var boxLen = $('.my-form .text-box').length;
                                            if (daysVal > 5 || boxLen > 5) {
                                                alert('Error: Mandatory/Forced Leave is strictly limited to a maximum of 5 days only!');
                                                e.preventDefault();
                                                return false;
                                            }
                                        }
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
                                    
                                    $fnameList_query = $conn->query("SELECT personnel_id, lname, fname, mname FROM personnels");
                                    while($fnlq_row = $fnameList_query->fetch()){ ?>
                                    
                                    <option value="<?php echo $fnlq_row['personnel_id'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?>"><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></option>
                                    
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
