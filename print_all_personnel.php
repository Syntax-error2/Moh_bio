<!DOCTYPE html>
<html>
<?php

 
include('session.php'); 

include('header_print.php');
  
?>

<body>


                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center>
                    <h3>LIST OF PERSONNEL</h3>
                    </center>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table style="width: 100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>SEX</th>
                              <th>CONTACT #</th>
                              <th>CARD DETAILS</th>
                              <th>CONTACT PERSON/NUMBER</th>
                              <th>OFFICE - STATUS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                          
                        <?php
                        $personCtr=0;
                        $studData_query = $conn->query("SELECT * FROM personnels WHERE (separation_date IS NULL) AND sex='Male' ORDER BY lname, fname ASC") or die(mysql_error());
                        while($staff_row=$studData_query->fetch()){
                        
                        $dept_off_query = $conn->query("SELECT * FROM dept_offices WHERE do_id='$staff_row[do_id]'") or die(mysql_error());
                        $do_row = $dept_off_query->fetch();

                        $empStat_query = $conn->query("SELECT emp_stat_name FROM emp_status WHERE empStat_id='$staff_row[empStat_id]'") or die(mysql_error());
                        $empStat_row=$empStat_query->fetch();
                        
                        $personCtr+=1;
                        
                        $mname=$staff_row['mname'];
                        
                        $suffix=$staff_row['suffix'];
                        if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
                                
                        
                        ?>
 
                          <tr>
                          <td>
                          <?php
                            if($mname=='')
                            {
                                    $finalMName=$suffix;
                                    
                                    echo $personCtr.'. '.strtoupper($staff_row['lname'].", ".$staff_row['fname']." ".$finalMName);
                                    
                            }else{
                                
                                    $finalMName=$suffix.substr($mname, 0, 1).'.';
                                    echo $personCtr.'. '.strtoupper($staff_row['lname'].", ".$staff_row['fname']." ".$finalMName);
                            }
                          ?>
                          </td>
                          
                          <td><?php echo $staff_row['sex']; ?></td>
                          
                          <td><?php echo $staff_row['personal_pnum']; ?></td>
                          
                          <td>
                          <strong>TIN: </strong><?php echo $staff_row['tin_num']; ?><br />
                          <strong>GSIS: </strong><?php echo $staff_row['gsis_num']; ?><br />
                          <strong>PAGIBIG: </strong><?php echo $staff_row['pagibig_num']; ?><br />
                          <strong>PHILHEALTH: </strong><?php echo $staff_row['philHealth_num']; ?><br />
                          </td>
                          
                          <td><?php echo $staff_row['conPerson_lname'].', '.$staff_row['conPerson_fname'].'<br />'.$staff_row['emergency_pnum']; ?></td>
                          <td>
                          <strong><?php echo $do_row['dept_office_name']; ?></strong><br />
                          <?php echo $empStat_row['emp_stat_name']; ?>
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
                    
                    <center>
                    <h3>LIST OF PERSONNEL</h3>
                    </center>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table style="width: 100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>SEX</th>
                              <th>CONTACT #</th>
                              <th>CARD DETAILS</th>
                              <th>CONTACT PERSON/NUMBER</th>
                              <th>OFFICE - STATUS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                          
                        <?php
                        $personCtr=0;
                        $studData_query = $conn->query("SELECT * FROM personnels WHERE (separation_date IS NULL) AND sex='Male' ORDER BY lname, fname ASC") or die(mysql_error());
                        while($staff_row=$studData_query->fetch()){
                        
                        $dept_off_query = $conn->query("SELECT * FROM dept_offices WHERE do_id='$staff_row[do_id]'") or die(mysql_error());
                        $do_row = $dept_off_query->fetch();
                        
                        $empStat_query = $conn->query("SELECT emp_stat_name FROM emp_status WHERE empStat_id='$staff_row[empStat_id]'") or die(mysql_error());
                        $empStat_row=$empStat_query->fetch();
                        
                        $personCtr+=1;
                        
                        $mname=$staff_row['mname'];
                        
                        $suffix=$staff_row['suffix'];
                        if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
                                
                        
                        ?>
 
                          <tr>
                          <td>
                          <?php
                            if($mname=='')
                            {
                                    $finalMName=$suffix;
                                    
                                    echo $personCtr.'. '.strtoupper($staff_row['lname'].", ".$staff_row['fname']." ".$finalMName);
                                    
                            }else{
                                
                                    $finalMName=$suffix.substr($mname, 0, 1).'.';
                                    echo $personCtr.'. '.strtoupper($staff_row['lname'].", ".$staff_row['fname']." ".$finalMName);
                            }
                          ?>
                          </td>
                          
                          <td><?php echo $staff_row['sex']; ?></td>
                          
                          <td><?php echo $staff_row['personal_pnum']; ?></td>
                          
                          <td>
                          <strong>TIN: </strong><?php echo $staff_row['tin_num']; ?><br />
                          <strong>GSIS: </strong><?php echo $staff_row['gsis_num']; ?><br />
                          <strong>PAGIBIG: </strong><?php echo $staff_row['pagibig_num']; ?><br />
                          <strong>PHILHEALTH: </strong><?php echo $staff_row['philHealth_num']; ?><br />
                          </td>
                          
                          <td><?php echo $staff_row['conPerson_lname'].', '.$staff_row['conPerson_fname'].'<br />'.$staff_row['emergency_pnum']; ?></td>
                          <td>
                          <strong><?php echo $do_row['dept_office_name']; ?></strong><br />
                          <?php echo $empStat_row['emp_stat_name']; ?>
                          </td>
                          </tr>
                      
                     
                             <?php }  ?>
                            </tbody>
                        </table> 
                        </div>
                        
                        
                        </div>
                        </div>
                        
                        <?php include('footer_print.php'); ?>
                        
</body>
</html>
       
            