<!DOCTYPE html>
<html>
<?php

 
include('session.php'); 
include('dbcon.php');

include('header_print.php');


$do_id = $_GET['do_id'] ?? '';
$dept_off_stmt = $conn->prepare("SELECT * FROM dept_offices WHERE do_id = :do_id");
$dept_off_stmt->execute([':do_id' => $do_id]);
$dept_off_query = $dept_off_stmt;
$do_row = $dept_off_query->fetch();
       
$officeHead_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
$officeHead_stmt->execute([':personnel_id' => $do_row['officeHead_id']]);
$officeHead_query = $officeHead_stmt;
$oh_row=$officeHead_query->fetch();
if($oh_row['suffix']=="-")
{
                                                            
$offHead=$oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname'];
                                                        
}else{
                                                            
$offHead=$oh_row['fname']." ".substr($oh_row['mname'], 0,1).". ".$oh_row['lname']." ".$oh_row['suffix'];
                                                        
}  

?>
 
<body>


                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center>
                    <h3>LIST OF PERSONNEL</h3>
                    <p>
                    <strong style="font-size: large;"><?php echo strtoupper($do_row['dept_office_name']); ?></strong><br />
                    <strong style="font-size: medium; font-weight: normal;">OFFICE HEAD: <?php echo $offHead; ?></strong>
                    </p>
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
                              <th>STATUS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                          
                        <?php
                        $personCtr=0;
                        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE do_id = :do_id AND (separation_date = '' OR separation_date = '  /  /    ') AND sex = 'Male' ORDER BY lname, fname ASC");
                        $studData_stmt->execute([':do_id' => $do_id]);
                        $studData_query = $studData_stmt;
                        while($staff_row=$studData_query->fetch()){
                        
                                                $empStat_stmt = $conn->prepare("SELECT emp_stat_name FROM emp_status WHERE empStat_id = :empStat_id");
                                                $empStat_stmt->execute([':empStat_id' => $staff_row['empStat_id']]);
                                                $empStat_query = $empStat_stmt;
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
                          <td><?php echo $empStat_row['emp_stat_name']; ?></td>
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
                    <p>
                    <strong style="font-size: large;"><?php echo strtoupper($do_row['dept_office_name']); ?></strong><br />
                    <strong style="font-size: medium; font-weight: normal;">OFFICE HEAD: <?php echo $offHead; ?></strong>
                    </p>
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
                              <th>STATUS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                          
                        <?php
                        $personCtr=0;
                        $studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE do_id = :do_id AND (separation_date = '' OR separation_date = '  /  /    ') AND sex = 'Female' ORDER BY lname, fname ASC");
                        $studData_stmt->execute([':do_id' => $do_id]);
                        $studData_query = $studData_stmt;
                        while($staff_row=$studData_query->fetch()){
                        
                                                $empStat_stmt = $conn->prepare("SELECT emp_stat_name FROM emp_status WHERE empStat_id = :empStat_id");
                                                $empStat_stmt->execute([':empStat_id' => $staff_row['empStat_id']]);
                                                $empStat_query = $empStat_stmt;
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
                          <td><?php echo $empStat_row['emp_stat_name']; ?></td>
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
       
            