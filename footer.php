                <!-- UNIVERSAL REPORT FILTER Modal -->
                  <div id="universal_report" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog modal-lg">
                      <div class="modal-content">
                      
                      <form action="checkReportFilter.php" method="POST">
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">PRINT LIST OF PERSONNEL <div class="badge badge-info" style="border-radius: 50px;">ADVANCED FILTER</div></h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                            
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label pt-2" style="text-align: right;">Dept/Office</label>
                              <div class="col-sm-10">
                                <select name="do_id" class="form-control">
                                <option value="0">All Department &amp; Office</option>
                                <?php
                                $dept_off2_query = $conn->query("SELECT * FROM dept_offices ORDER BY dept_office_name ASC");
                                while ($do2_row = $dept_off2_query->fetch()) 
                                {  ?>
                                
                                <option value="<?php echo $do2_row['do_id']; ?>"><?php echo $do2_row['dept_office_name']; ?></option>
                                
                                <?php } ?>
                                </select>
                              </div>
                            </div>
                            
                              
                
                            <div class="form-group row">
                              <label class="col-sm-2 form-control-label pt-2" style="text-align: right;">Status</label>
                              <div class="col-sm-5">
                                <select name="empStat_id" class="form-control">
                                <option value="0">All Job Status</option>
                                <?php
                                $emp_stat_query = $conn->query("SELECT * FROM emp_status WHERE status='Active' ORDER BY status ASC");
                                while ($estat_row = $emp_stat_query->fetch()) 
                                { ?>
                                
                                <option value="<?php echo $estat_row['empStat_id']; ?>"><?php echo $estat_row['emp_stat_name']; ?></option>
                                
                                <?php } ?>
                                </select>
                              </div>
                            
                              <label class="col-sm-2 form-control-label pt-2" style="text-align: right;">Sex</label>
                              <div class="col-sm-3">
                                <select name="sex" class="form-control">
                                <option>Male Only</option>
                                <option>Female Only</option>
                                <option>All-Mixed</option>
                                </select>
                              </div>
                            </div> 
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="print_general_reports" type="submit" class="btn btn-primary">Print</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- END UNIVERSAL REPORT FILTER Modal -->
                  



    <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <p><img width="15" height="15" src="img/<?php echo $sf_row['logo'];?>" /> <?php echo $schoolName; ?> &middot; <?php echo date("l"); ?>, <?php echo date("M".". "."d".", "."Y"); ?></p>
            </div>
            <div class="col-sm-6 text-right">
              <p>Design by <a href="https://bootstrapious.com" class="external">Bootstrapious</a></p>
              <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions and it helps me to run Bootstrapious. Thank you for understanding :)-->
            </div>
          </div>
        </div>
      </footer>
      
      <?php
      $sf_query=null;
      $conn=null;
      ?>