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
                        $studData_query = $conn->query("SELECT personnels.*, dept_offices.dept_office_name, emp_status.emp_stat_name FROM personnels LEFT JOIN dept_offices ON dept_offices.do_id = personnels.do_id LEFT JOIN emp_status ON emp_status.empStat_id = personnels.empStat_id WHERE personnels.(separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ') AND personnels.sex='Male' ORDER BY personnels.lname, personnels.fname ASC") or die(mysql_error());
                        while($staff_row=$studData_query->fetch()){
                        
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
                            <strong><?php echo $staff_row['dept_office_name'] ?? 'Unassigned Office'; ?></strong><br />
                            <?php echo $staff_row['emp_stat_name'] ?? 'No Employment Status'; ?>
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
                        $studData_query = $conn->query("SELECT personnels.*, dept_offices.dept_office_name, emp_status.emp_stat_name FROM personnels LEFT JOIN dept_offices ON dept_offices.do_id = personnels.do_id LEFT JOIN emp_status ON emp_status.empStat_id = personnels.empStat_id WHERE personnels.(separation_date IS NULL OR separation_date = '' OR separation_date = '  /  /    ') AND personnels.sex='Male' ORDER BY personnels.lname, personnels.fname ASC") or die(mysql_error());
                        while($staff_row=$studData_query->fetch()){
                        
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
                            <strong><?php echo $staff_row['dept_office_name'] ?? 'Unassigned Office'; ?></strong><br />
                            <?php echo $staff_row['emp_stat_name'] ?? 'No Employment Status'; ?>
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
       
            