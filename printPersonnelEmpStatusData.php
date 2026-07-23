
<!DOCTYPE html>
<html>

<?php
include('session.php');  
include('dbcon.php');
include('header_print.php');
?>
 
<body>

                    <?php
                    $empStat_id = $_GET['empStat_id'] ?? '';
                    $empStat_stmt = $conn->prepare("SELECT * FROM emp_status WHERE empStat_id = :empStat_id");
                    $empStat_stmt->execute([':empStat_id' => $empStat_id]);
                    $empStat_query = $empStat_stmt;
                    $empStat_row = $empStat_query->fetch(); 
                    ?>
 
                    <!-- //MALE EMPLOYEES - BY JOB STATUS // --><!-- //MALE EMPLOYEES - BY JOB STATUS // -->
                    <?php if($_GET['print_output']==='Male Only'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center><h3>LIST OF MALE <?php echo strtoupper($empStat_row['emp_stat_name']); ?> PERSONNELS</h3></center>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                $printDataAge_stmt = $conn->prepare("SELECT lname, fname, mname, suffix, age, do_id, des_id FROM personnels WHERE sex = 'Male' AND empStat_id = :empStat_id AND (separation_date = '' OR separation_date = '  /  /    ') ORDER BY lname, fname ASC");
                                $printDataAge_stmt->execute([':empStat_id' => $empStat_id]);
                                $printDataAge_query = $printDataAge_stmt;
                                while ($printDA_row=$printDataAge_query->fetch())
                                { $maleCtr=$maleCtr+1; ?>
                         
                            <tr>
                            
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            
                            <td><?php echo $printDA_row['age']; ?></td>
                            
                            <td>
                              <?php
                              
                              $emp_stat_stmt1 = $conn->prepare("SELECT des_name FROM designation WHERE des_id = :des_id");
                              $emp_stat_stmt1->execute([':des_id' => $printDA_row['des_id']]);
                              $emp_stat_query1 = $emp_stat_stmt1;
                              $es_row1=$emp_stat_query1->fetch();
                              
                              $emp_stat_stmt2 = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                              $emp_stat_stmt2->execute([':do_id' => $printDA_row['do_id']]);
                              $emp_stat_query2 = $emp_stat_stmt2;
                              $es_row2=$emp_stat_query2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                            </td>
                       
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- //END MALE EMPLOYEES - BY JOB STATUS // --><!-- //END MALE EMPLOYEES - BY JOB STATUS // -->
                    
                    
                    <!-- //FEMALE EMPLOYEES - BY JOB STATUS // --><!-- //FEMALE EMPLOYEES - BY JOB STATUS // -->
                    <?php if($_GET['print_output']==='Female Only'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center><h3>LIST OF FEMALE <?php echo strtoupper($empStat_row['emp_stat_name']); ?> PERSONNELS</h3></center>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example2" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                $printDataAge_stmt = $conn->prepare("SELECT lname, fname, mname, suffix, age, do_id, des_id FROM personnels WHERE sex = 'Female' AND empStat_id = :empStat_id AND (separation_date = '' OR separation_date = '  /  /    ') ORDER BY lname, fname ASC");
                                $printDataAge_stmt->execute([':empStat_id' => $empStat_id]);
                                $printDataAge_query = $printDataAge_stmt;
                                while ($printDA_row=$printDataAge_query->fetch())
                                { $maleCtr=$maleCtr+1; ?>
                         
                            <tr>
                            
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            
                            <td><?php echo $printDA_row['age']; ?></td>
                            
                            <td>
                              <?php
                              
                              $emp_stat_stmt1 = $conn->prepare("SELECT des_name FROM designation WHERE des_id = :des_id");
                              $emp_stat_stmt1->execute([':des_id' => $printDA_row['des_id']]);
                              $es_row1=$emp_stat_stmt1->fetch();
                              
                              $emp_stat_stmt2 = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                              $emp_stat_stmt2->execute([':do_id' => $printDA_row['do_id']]);
                              $es_row2=$emp_stat_stmt2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                            </td>
                    
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                 
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- //END FEMALE EMPLOYEES - BY JOB STATUS // --><!-- //END FEMALE EMPLOYEES - BY JOB STATUS // -->
                    
                    
                    <!-- //MALE-FEMALE EMPLOYEES - BY JOB STATUS // --><!-- //MALE-FEMALE EMPLOYEES - BY JOB STATUS // -->
                    <?php if($_GET['print_output']==='Male-Female'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center><h3>LIST OF MALE <?php echo strtoupper($empStat_row['emp_stat_name']); ?> PERSONNELS</h3></center>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                $printDataAge_stmt = $conn->prepare("SELECT lname, fname, mname, suffix, age, do_id, des_id FROM personnels WHERE sex = 'Male' AND empStat_id = :empStat_id AND (separation_date = '' OR separation_date = '  /  /    ') ORDER BY lname, fname ASC");
                                $printDataAge_stmt->execute([':empStat_id' => $empStat_id]);
                                $printDataAge_query = $printDataAge_stmt;
                                while ($printDA_row=$printDataAge_query->fetch())
                                { $maleCtr=$maleCtr+1; ?>
                         
                            <tr>
                            
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            
                            <td><?php echo $printDA_row['age']; ?></td>
                            
                            <td>
                              <?php
                              
                              $emp_stat_stmt1 = $conn->prepare("SELECT des_name FROM designation WHERE des_id = :des_id");
                              $emp_stat_stmt1->execute([':des_id' => $printDA_row['des_id']]);
                              $es_row1=$emp_stat_stmt1->fetch();
                              
                              $emp_stat_stmt2 = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                              $emp_stat_stmt2->execute([':do_id' => $printDA_row['do_id']]);
                              $es_row2=$emp_stat_stmt2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                            </td>
                       
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    
                    <div class="pb" style="margin-top: 24px;"></div>
                    
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center><h3>LIST OF FEMALE <?php echo strtoupper($empStat_row['emp_stat_name']); ?> PERSONNELS</h3></center>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example2" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                $printDataAge_stmt = $conn->prepare("SELECT lname, fname, mname, suffix, age, do_id, des_id FROM personnels WHERE sex = 'Female' AND empStat_id = :empStat_id AND (separation_date = '' OR separation_date = '  /  /    ') ORDER BY lname, fname ASC");
                                $printDataAge_stmt->execute([':empStat_id' => $empStat_id]);
                                $printDataAge_query = $printDataAge_stmt;
                                while ($printDA_row=$printDataAge_query->fetch())
                                { $maleCtr=$maleCtr+1; ?>
                         
                            <tr>
                            
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            
                            <td><?php echo $printDA_row['age']; ?></td>
                            
                            <td>
                              <?php
                              
                              $emp_stat_stmt1 = $conn->prepare("SELECT des_name FROM designation WHERE des_id = :des_id");
                              $emp_stat_stmt1->execute([':des_id' => $printDA_row['des_id']]);
                              $es_row1=$emp_stat_stmt1->fetch();
                              
                              $emp_stat_stmt2 = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                              $emp_stat_stmt2->execute([':do_id' => $printDA_row['do_id']]);
                              $es_row2=$emp_stat_stmt2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                            </td>
                    
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                 
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- //END MALE-FEMALE EMPLOYEES - BY JOB STATUS // --><!-- //END MALE-FEMALE EMPLOYEES - BY JOB STATUS // -->
                    
                    
                    
                    <!-- //ALL-MIXED EMPLOYEES - BY JOB STATUS // --><!-- //ALL-MIXED EMPLOYEES - BY JOB STATUS // -->
                    <?php if($_GET['print_output']==='All-Mixed'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center><h3>LIST OF <?php echo strtoupper($empStat_row['emp_stat_name']); ?> PERSONNELS</h3></center>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>SEX</th>
                              <th>AGE</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                $printDataAge_stmt = $conn->prepare("SELECT lname, fname, mname, suffix, sex, age, do_id, des_id FROM personnels WHERE empStat_id = :empStat_id AND (separation_date = '' OR separation_date = '  /  /    ') ORDER BY lname, fname ASC");
                                $printDataAge_stmt->execute([':empStat_id' => $empStat_id]);
                                $printDataAge_query = $printDataAge_stmt;
                                while ($printDA_row=$printDataAge_query->fetch())
                                { $maleCtr=$maleCtr+1; ?>
                         
                            <tr>
                            
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            
                            <td><?php echo $printDA_row['sex']; ?></td>
                            
                            <td><?php echo $printDA_row['age']; ?></td>
                            
                            <td>
                              <?php
                              
                              $emp_stat_stmt1 = $conn->prepare("SELECT des_name FROM designation WHERE des_id = :des_id");
                              $emp_stat_stmt1->execute([':des_id' => $printDA_row['des_id']]);
                              $es_row1=$emp_stat_stmt1->fetch();
                              
                              $emp_stat_stmt2 = $conn->prepare("SELECT dept_office_name FROM dept_offices WHERE do_id = :do_id");
                              $emp_stat_stmt2->execute([':do_id' => $printDA_row['do_id']]);
                              $es_row2=$emp_stat_stmt2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                            </td>
                       
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- //END ALL-MIXED EMPLOYEES - BY JOB STATUS // --><!-- //END ALL-MIXED EMPLOYEES - BY JOB STATUS // -->
                  

                    
                    
                    


</body>
</html>
       
            