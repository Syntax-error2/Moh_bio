<!DOCTYPE html>
<html> 

<?php

 
include('session.php');  
//error_reporting(0);
 
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


                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center>
                    <h3>LIST OF SEPARATED PERSONNELS</h3>
                    <h4>BY <?php echo strtoupper($empStat_row['emp_stat_name']); ?> - Male</h4>
                    </center>
 
    
                    <table class="table table-bordered table-striped" style="margin: 0px 8px 0px 8px; width: 100%;">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                              <th>STATUS <br /><small>(APPOINTMENT DATE - SEPARATION DATE)</small></th>
                         
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                
                                $printDataAge_stmt = $conn->prepare("SELECT lname, fname, mname, suffix, age, do_id, des_id, appointment_date, separation_date FROM personnels WHERE empStat_id = :empStat_id AND sex = 'Male' ORDER BY lname, fname ASC");
                                $printDataAge_stmt->execute([':empStat_id' => $empStat_id]);
                                $printDataAge_query = $printDataAge_stmt;
                                while ($printDA_row=$printDataAge_query->fetch())
                                { ?>
                         
                            <tr>
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname'];
                                    
                                    }else{
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname']." ".$printDA_row['suffix'];
                                    
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
                              <td><?php echo $empStat_row['emp_stat_name']; ?> <br />(<?php echo $printDA_row['appointment_date']; ?> - <?php echo $printDA_row['separation_date']; ?>)</td>
                       
                            </tr>
                              
                            
                            
                             <?php }  ?>
                           
                          </tbody>
                    </table>
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    
                    <div class="pb" style="margin-top: 24px;"></div>
                    
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center>
                    <h3>LIST OF SEPARATED PERSONNELS</h3>
                    <h4>BY <?php echo strtoupper($empStat_row['emp_stat_name']); ?> - Female</h4>
                    </center>
 
    
                    <table class="table table-bordered table-striped" style="margin: 0px 8px 0px 8px; width: 100%;">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                              <th>STATUS <br /><small>(APPOINTMENT DATE - SEPARATION DATE)</small></th>
                            
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                
                                $printDataAge_stmt = $conn->prepare("SELECT lname, fname, mname, suffix, age, do_id, des_id, appointment_date, separation_date FROM personnels WHERE empStat_id = :empStat_id AND sex = 'Female' ORDER BY lname, fname ASC");
                                $printDataAge_stmt->execute([':empStat_id' => $empStat_id]);
                                $printDataAge_query = $printDataAge_stmt;
                                while ($printDA_row=$printDataAge_query->fetch())
                                { ?>
                         
                            <tr>
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname'];
                                    
                                    }else{
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname']." ".$printDA_row['suffix'];
                                    
                                    } ?>
                            </td>
                            <td><?php echo $printDA_row['age']; ?></td>
                            <td>
                             
                              <?php
                              
                              $emp_stat_query1 = $conn->query("select des_name from designation WHERE des_id='$printDA_row[des_id]'");
                              $es_row1=$emp_stat_query1->fetch();
                              
                              $emp_stat_query2 = $conn->query("select dept_office_name from dept_offices WHERE do_id='$printDA_row[do_id]'");
                              $es_row2=$emp_stat_query2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                              
                              </td>
                              <td><?php echo $empStat_row['emp_stat_name']; ?> <br />(<?php echo $printDA_row['appointment_date']; ?> - <?php echo $printDA_row['separation_date']; ?>)</td>
                    
                            </tr>
                              
                            
                            
                             <?php }  ?>
                           
                          </tbody>
                    </table>
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
 
</body>
</html>
 
 