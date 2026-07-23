 <!-- Counts Section -->
      <section class="dashboard-counts section-padding">
        <div class="container-fluid">
          <div class="row">
          
            <!-- Count item widget-->
            <div class="col-xl-3 col-md-8 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-user"></i></div>
                <div class="name"><strong class="text-uppercase">PERSONNELS</strong>
                
                <span>Registered Personnels</span>
                
                  <div class="count-number"><?php echo $perCtr_all; ?></div>
                  
                  <div class="page-statistics d-flex justify-content-between">
                  <div class="page-statistics-left"><span>Male</span><strong>  <?php echo $perCtrM_all; ?></strong></div>
                  <div class="page-statistics-right"><span>Female</span><strong> <?php echo $perCtrF_all; ?></strong></div>
                </div>
                
                </div>
              </div>
            </div>
            
            <!-- Count item widget-->
            <div class="col-xl-3 col-md-8 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name"><strong class="text-uppercase">PAYROLL HISTORY</strong>
                
                <span>Total payroll generated</span>
                
                  <div class="count-number"><?php echo $shiftTotalCtr; ?></div>
                  
                   
                  
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <!--
            <div class="col-xl-2 col-md-8 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-list-1"></i></div>
                <div class="name"><strong class="text-uppercase">Client CPU</strong>
                
                <span>Registered Client from the Server</span>
                
                  <div class="count-number"><?php //echo $client_computerTotalCtr; ?></div>
                </div>
              </div>
            </div>
            -->
            
            <!-- Count item widget-->
            <div class="col-xl-3 col-md-8 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-line-chart"></i></div>
                <div class="name"><strong class="text-uppercase">JOB STATUS</strong>
                 
                <span>Active</span>
                 
                  
                <?php
                $empStatTotalCtr=0;
                $empStat_query = $conn->query("SELECT * FROM emp_status WHERE status='Active' ORDER BY emp_stat_name ASC") or die(mysql_error());
                while ($empStat_row = $empStat_query->fetch()) 
                { ?>
                <div class="page-statistics d-flex justify-content-between">
                <div class="page-statistics-left">
                <a data-toggle="modal" data-target="#print_emp_status<?php echo $empStat_row['empStat_id']; ?>" href="#" title="Print list of <?php echo $empStat_row['emp_stat_name']; ?> personnels..." style="text-decoration-line: none;"><small><small><i class="fa fa-print"></i></small> <?php echo $empStat_row['emp_stat_name']; ?></small></a>
                
                
                <!-- report filter Modal -->
                  <div id="print_emp_status<?php echo $empStat_row['empStat_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      
                      <form action="checkReportFilter.php?empStat_id=<?php echo $empStat_row['empStat_id']; ?>" method="POST">
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">PRINT JOB STATUS: <?php echo strtoupper($empStat_row['emp_stat_name']); ?></h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
  
                            <div class="form-group row">
                              <label class="col-sm-4 form-control-label">Print Output:</label>
                              <div class="col-sm-8">
                                <select name="print_output" class="form-control">
                                <option>Male Only</option>
                                <option>Female Only</option>
                                <option>Male-Female</option>
                                <option>All-Mixed</option>
                                </select>
                              </div>
                            </div> 
                          
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
                          <button name="print_filter_emp_status" type="submit" class="btn btn-primary">Print</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end report filter Modal -->
             
                </div>
                
                <?php
                $empStatCtr_query = $conn->query("SELECT * FROM personnels WHERE empStat_id='$empStat_row[empStat_id]' AND (separation_date IS NULL)") or die(mysql_error());
                
                $empStatTotalCtr=$empStatTotalCtr+$empStatCtr_query->rowCount();
                ?>
                <div class="page-statistics-right"><strong style="margin-left: 8px;"> <?php echo $empStatCtr_query->rowCount(); ?></strong></div>
                </div>
                <?php } ?>
                <div class="page-statistics d-flex justify-content-between">
                
                <div class="page-statistics-left"><a href="printPersonnelEmpStatusDataSummary.php" target="_blank" title="Print personnels apppointment summary..." style="text-decoration-line: none;"><small><small><i class="fa fa-print"></i></small> Total</small></a></div>
              
                <div class="page-statistics-right"><strong> <?php echo $empStatTotalCtr; ?></strong></div>
                </div>
                  
                
                
                </div>
              </div>
            </div>
            
            
            <!-- Count item widget-->
            <div class="col-xl-3 col-md-8 col-12">
              <div class="wrapper count-title d-flex">
                <div class="icon">&nbsp;</div>
                <div class="name"><strong class="text-uppercase">&nbsp;</strong>
                 
                <span>Separated</span>
                 
                  
                  <?php
                $empStatTotalCtr=0;
                $empStat_query = $conn->query("SELECT * FROM emp_status WHERE status='Separated' ORDER BY emp_stat_name ASC") or die(mysql_error());
                while ($empStat_row = $empStat_query->fetch()) 
                { ?>
                <div class="page-statistics d-flex justify-content-between">
                <div class="page-statistics-left"><a href="printPersonnelEmpStatusDataSeparated.php?empStat_id=<?php echo $empStat_row['empStat_id']; ?>" target="_blank" title="Print list of <?php echo $empStat_row['emp_stat_name']; ?> personnels..." style="text-decoration-line: none; color: red;"><small><small><i class="fa fa-print"></i></small> <?php echo $empStat_row['emp_stat_name']; ?></small></a></div>
                
                <?php
                $empStatCtr_query = $conn->query("SELECT * FROM personnels WHERE empStat_id='$empStat_row[empStat_id]'") or die(mysql_error());
                
                $empStatTotalCtr=$empStatTotalCtr+$empStatCtr_query->rowCount();
                ?>
                <div class="page-statistics-right"><strong style="margin-left: 8px;"> <?php echo $empStatCtr_query->rowCount(); ?></strong></div>
                </div>
                <?php } ?>
                <div class="page-statistics d-flex justify-content-between">
                
                <div class="page-statistics-left"><a href="printPersonnelEmpStatusDataSummarySeparated.php" target="_blank" title="Print personnels apppointment summary..." style="text-decoration-line: none; color: red;"><small><small><i class="fa fa-print"></i></small> Total</small></a></div>
              
                <div class="page-statistics-right"><strong> <?php echo $empStatTotalCtr; ?></strong></div>
                </div>
                  
                
                
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>