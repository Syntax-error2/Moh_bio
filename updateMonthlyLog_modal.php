<!-- edit Class Modal -->
                  <div id="updateMonthlyLog<?php echo $staff_row['RFTag_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_updateMonthlyLog.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                     
                      <input name="RFTag_id" value="<?php echo $staff_row['RFTag_id']; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">ADD DTR NOTES</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        
                  
                  
                        <?php
                        /* if(date('m')=="01")
                        {
                            $mmWords="January";
                        }
                        
                        if(date('m')=="02")
                        {
                            $mmWords="February";
                        }
                        
                        if(date('m')=="03")
                        {
                            $mmWords="March";
                        }
                        
                        if(date('m')=="04")
                        {
                            $mmWords="April";
                        }
                        
                        if(date('m')=="05")
                        {
                            $mmWords="May";
                        }
                        
                        
                        if(date('m')=="06")
                        {
                            $mmWords="June";
                        }
                        
                        
                        if(date('m')=="07")
                        {
                            $mmWords="July";
                        }
                        
                        
                        if(date('m')=="08")
                        {
                            $mmWords="August";
                        }
                        
                        
                        if(date('m')=="09")
                        {
                            $mmWords="September";
                        }
                        
                        if(date('m')=="10")
                        {
                            $mmWords="October";
                        }
                        
                        
                        if(date('m')=="11")
                        {
                            $mmWords="November";
                        }
                        
                        
                        if(date('m')=="12")
                        {
                            $mmWords="December";
                        }
                         */ ?>
                        <div style="margin: 10px 10px 10px 12px;" class="form-group row">
                        
                        <script src="js/jquery-latest.js"></script>
                        
                        <div class="col-lg-12">
                            <div class="row">
                            
                            <div class="col-lg-12">
                                <div class="row">
                                  <div class="col-md-12">
                                    <select name="remarks" class="form-control">
                                    <option>-</option>
                                    <option>VACATION LEAVE</option>
                                    <option>SICK LEAVE</option>
                                    <option>MATERNITY LEAVE (RA 8282)</option>
                                    <option>PATERNITY LEAVE (RA 8187)</option>
                                    <option>PARENTAL LEAVE for SOLO PARENTS (RA 8972)</option>
                                    <option>OFFICIAL BUSINESS TRIP</option>
                                    <option>SEMINAR</option>
                                    <option value="NULL">VOID DTR NOTE</option>
                                    </select>
                                    <small class="form-text">Select Notation Type</small>
                                  </div>
                                </div>
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
                                
                                
                                <div class="col-md-12">
                                <input name="no_of_days" class="form-control" type="number" min="1" max="200" step="1" value="1" />
                                <small>Total Number of Days</small>
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

                            </div>
                        </div>
 
                               
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
                              <div class="col-sm-12">
                                <div class="row">
                                
                                <div class="col-md-12">
                                <p style="font-size: small;">Ignore form below if type of note is <strong>ON LEAVE</strong></p>
                                </div>
                                
                                <div class="col-lg-12">
                                <div class="row">
                                  <div class="col-md-6">
                                    <input type="checkbox" name="add_to" /> <small>Add as Travel Order</small><br />
                                    <small>Travel Code: <?php echo $pcf_mm.'-'.$new_pcs.'-'.date('Y'); ?></small>
                                    
                                    <input value="<?php echo $pc_sequence; ?>" name="new_tng_sequence" type="hidden" />
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
                          <button name="updateDTRLog" type="submit" class="btn btn-primary">SAVE DTR NOTE</button>
                          </div>  
                         
 
                            
                </form>     
                      </div>
                    </div>
                  </div>
                  <!-- end edit Class Modal -->