<!-- Add Travel Modal -->
                  <div id="addTravelOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_travel_leave.php" method="POST">
                     
                       
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">ADD TRAVEL ORDER</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                         
                        <div style="margin: 10px 10px 10px 12px;" class="form-group row">
                        
                        <script src="js/jquery-latest.js"></script>
                        
                        
                        
                        <?php
                              
                              $pcfg_query = $conn->query("SELECT * FROM travel_num_generator");
                              $pcfg_row = $pcfg_query->fetch();
                         
                             
                              if(date('m')==$pcfg_row['mm']){
                                $pc_sequence=$pcfg_row['sequence']+1;
                                $pcf_mm=$pcfg_row['mm'];
                              }else{
                                $pc_sequence=1;
                                $pcf_mm=date('m');
                              }
                              
                               
                              if($pc_sequence>=0 AND $pc_sequence<=9)
                              {
                                
                                $new_pcs="00".$pc_sequence;
                             
                              }elseif($pc_sequence>9 AND $pc_sequence<=99)
                              {
                                
                                $new_pcs="0".$pc_sequence;
                              }else{
                                $new_pcs=$pc_sequence;
                              } ?>
                    
                    
                        
                        <div class="col-lg-12">
                            <div class="row">
                            
                            <div class="col-lg-12">
                                <div class="row">
                                  <div class="col-md-12">
                                    <select name="remarks" id="travelNotationSelect" class="form-control" onchange="toggleTravelNotationFields(this.value)">
                                    <option>--Please Select--</option>
                                    <option>OFFICIAL BUSINESS TRIP</option>
                                    <option>SEMINAR</option>
                                    <option>PASS SLIP</option>
                                    </select>
                                    <small class="form-text">Select Notation Type</small>
                                  </div>
                                </div>
                              </div>
                              
                              <div id="passSlipTimeSection" class="col-lg-12" style="display: none; margin-top: 5px; margin-bottom: 12px;">
                                <div style="background-color: #f0f7ff; border: 1px solid #b8daff; border-radius: 4px; padding: 10px;">
                                  <div class="row">
                                    <div class="col-md-6">
                                      <label style="font-weight: 600; font-size: 12px; color: #004085;"><i class="fa fa-clock-o text-warning"></i> Time Out (Oras Magwa):</label>
                                      <input type="time" name="pass_slip_time_out" id="ps_time_out" class="form-control" />
                                      <small class="form-text text-muted">Departure from office</small>
                                    </div>
                                    <div class="col-md-6">
                                      <label style="font-weight: 600; font-size: 12px; color: #004085;"><i class="fa fa-clock-o text-success"></i> Time In (Oras Magbalik):</label>
                                      <input type="time" name="pass_slip_time_in" id="ps_time_in" class="form-control" />
                                      <small class="form-text text-muted">Return to office</small>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                      <small class="text-info" style="font-size: 11px;"><i class="fa fa-info-circle"></i> <strong>Note:</strong> Pass Slip is for short trips during work hours. Personnel can still scan/log attendance on the DTR before leaving and after returning.</small>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              
                              <script type="text/javascript">
                              function toggleTravelNotationFields(val) {
                                  var psSec = document.getElementById('passSlipTimeSection');
                                  if (psSec) {
                                      psSec.style.display = (val === 'PASS SLIP') ? 'block' : 'none';
                                  }
                              }
                              </script>
                              
                              <div class="col-md-12">
                                 <div class="my-formx">
                                  
                                        <p class="text-boxx">
                                            <label for="boxx1">Personnel No. <span class="boxx-number">1</span></label>
                                            <input type="text" class="form-control" name="jud1" placeholder="Search personnel fullname" list="perDataList" id="boxx1" required="true" />
                                      
                                          
                                        </p>
                                        <p><a class="add-boxx" style="background-color: green; border: 1px solid blue; color: white; padding: 4px 8px 4px 8px; cursor: pointer;"><small>Add Personnel</small></a></p>
                                  
                                </div>
                                
                                <datalist id="perDataList">
                                    <?php
                                    
                                    $fnameList_query = $conn->query("SELECT personnel_id, lname, fname, mname FROM personnels");
                                    while($fnlq_row = $fnameList_query->fetch()){ ?>
                                    
                                    <option value="<?php echo $fnlq_row['personnel_id'].' | '.$fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?>"><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></option>
                                    
                                    <?php } ?>
                                </datalist>
                                
                                
                                 
                                
                            </div>
                             
                                <script type="text/javascript">
                                jQuery(document).ready(function($){
                                    $('.my-formx .add-boxx').click(function(){
                                        var m = $('.text-boxx').length + 1;
                                        if( 75 < m ) {
                                            alert('Maximum number of personnels reach!');
                                            return false;
                                        }
                                        var boxx_html = $('<p class="text-boxx"><label for="boxx' + m + '">Personnel No. <span class="boxx-number">' + m + '</span></label> <input type="text" class="form-control" placeholder="Type personnel name..." name="jud' + m + '" list="perDataList" id="boxx' + m + '" required="true" /><a style="background-color: red; border: 1px solid black; color: white; padding: 4px 8px 4px 8px; cursor: pointer; margin-top:4px;" class="remove-boxx"><small>Remove</small></a></p>');
                                        boxx_html.hide();
                                        $('.my-formx p.text-boxx:last').after(boxx_html);
                                        boxx_html.fadeIn('slow');
                                        return false;
                                    });
                                    $('.my-formx').on('click', '.remove-boxx', function(){
                                        $(this).parent().css( 'background-color', '#FF6C6C' );
                                        $(this).parent().fadeOut("slow", function() {
                                            $(this).remove();
                                            $('.boxx-number').each(function(index){
                                                $(this).text( index + 1 );
                                            });
                                        });
                                        return false;
                                    });
                                });
                                </script>
                            
                            
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
                                        if( 125 < n ) {
                                            alert('Maximum number of days reach!');
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
                                
                                
                                
                                <div class="col-md-6">
                                <input name="no_of_personnels" class="form-control" type="number" min="1" max="200" step="1" value="1" />
                                <small>Total Number of Personnels</small>
                                </div>
                                
                                <div class="col-md-6">
                                <input name="no_of_days" class="form-control" type="number" min="1" max="200" step="1" value="1" />
                                <small>Total Number of Days</small>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-sm-12">
                                <div class="row">
          
                                <div class="col-lg-12">
                                <div class="row">
                                  <div class="col-md-6">
                                    <input type="checkbox" name="add_to" checked="true" /> <small>Add as Travel Order</small><br />
                                    <small>Travel Code: <?php echo $pcf_mm.'-'.$new_pcs.'-'.date('Y'); ?></small>
                                    
                                    <input value="<?php echo $pc_sequence; ?>" name="new_tng_sequence" type="hidden" />
                                    <input value="<?php echo $pcf_mm; ?>" name="new_tng_mm" type="hidden" />
                                    <input type="hidden" name="travel_code" value="<?php echo $pcf_mm.'-'.$new_pcs.'-'.date('Y'); ?>"/>
                                    
                                  </div>
                                  
                                  <div class="col-md-6">
                                    <input type="checkbox" name="add_201_sr" /> <small>Add to 201 Seminar Records</small>
                                  </div>
                     
                                </div>
                              </div>
                              
                                  <div class="col-md-12">
                                    <input name="purpose_title" type="text" class="form-control" />
                                    <small class="form-text">Travel Purpose / Seminar Title</small>
                                  </div>
                                  
                                  <div class="col-md-12">
                                    <input name="description" type="text" class="form-control" />
                                    <small class="form-text">Description</small>
                                  </div>
                                  
                                  <div class="col-md-12">
                                    <input name="location_venue" type="text" class="form-control" />
                                    <small class="form-text">Travel Location / Seminar Venue</small>
                                  </div>  
                                </div>
                              </div>
         
         
                            </div>
                    
             
                         
                          <div class="modal-footer">
                          <button id="submitTO" onclick="disAble()" name="add_travel" type="submit" class="btn btn-primary">SAVE TRAVEL ORDER</button>
                          </div>  
                         
                        
                        
                        <script>
                        
                            function disAble() {
                                document.getElementById('submitTO').onclick = function(){
                                    this.disabled=true;
                                }
                                
                            }
                        
                        </script>
                            
                </form>     
                      </div>
                    </div>
                  </div>
                  <!-- end Add Travel Modal -->
                  
 