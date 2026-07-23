                        <!-- edit Time Sched Modal -->
                          <div id="encodeDL<?php echo $staff_row['RFTag_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                              <div class="modal-content">
                              <form action="encode_daily_log.php?dept=<?php echo $_GET['dept']; ?>&personnel_id=<?php echo $_GET['personnel_id']; ?>" method="POST">
                              
                              
                                <div class="modal-header">
                                  <h5 id="exampleModalLabel" class="modal-title">Encode Daily Log</h5>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                                </div>
                                
                                <div class="modal-body">
                                
                               
                                    <?php
                                    if(date('m')=="01")
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
                                    ?>
                                    
                                    <div class="col-lg-12">
                                        <div class="row">
                                        <h3>Set Date</h3>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                           
                                          <div class="col-lg-12">
                                            <div class="row">
                                              <div class="col-md-6">
                                                <select name="selectedMM" class="form-control">
                                                <option value="<?php echo date('m'); ?>"><?php echo $mmWords; ?></option>
                                                <option value="01">January</option>
                                                <option value="02">February</option>
                                                <option value="03">March</option>
                                                <option value="04">April</option>
                                                <option value="05">May</option>
                                                <option value="06">June</option>
                                                <option value="07">July</option>
                                                <option value="08">August</option>
                                                <option value="09">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                                
                                                </select>
                                                <small class="form-text">Select Month</small>
                                              </div>
                                              
                                              <div class="col-md-3">
                                                 
                                                <select name="selectedDD" class="form-control">
                                                <option><?php echo date('d'); ?></option>
                                                <option>01</option>
                                                <option>02</option>
                                                <option>03</option>
                                                <option>04</option>
                                                <option>05</option>
                                                <option>06</option>
                                                <option>07</option>
                                                <option>08</option>
                                                <option>09</option>
                                                <option>10</option>
                                                <option>11</option>
                                                <option>12</option>
                                                <option>13</option>
                                                <option>14</option>
                                                <option>15</option>
                                                <option>16</option>
                                                <option>17</option>
                                                <option>18</option>
                                                <option>19</option>
                                                <option>20</option>
                                                <option>21</option>
                                                <option>22</option>
                                                <option>23</option>
                                                <option>24</option>
                                                <option>25</option>
                                                <option>26</option>
                                                <option>27</option>
                                                <option>28</option>
                                                <option>29</option>
                                                <option>30</option>
                                                <option>31</option>
                                                
                                                </select>
                                                <small class="form-text">Select Day</small>
                                              </div>
                                              
                                              <div class="col-md-3">
                                                 
                                                <select name="selectedYYYY" class="form-control">
                                                <option><?php echo date('Y'); ?></option>
                                                <option>2020</option>
                                                <option>2021</option>
                                                <option>2022</option>
                                                <option>2023</option>
                                                <option>2024</option>
                                                <option>2025</option>
                                                
                                                </select>
                                                <small class="form-text">Select Year</small>
                                              </div>
                                              
                                            </div>
                                          </div> 
                              </div>
                             
                              </div>
                                
                                <div class="modal-footer">
                                  <a style="color: white;" href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                                  <button name="updateTimeSched" type="submit" class="btn btn-primary">Proceed</button>
                                </div>
                                </form>

                              </div>
                            </div>
                          </div>
                          <!-- end Edit Time Sched Modal -->  
                          