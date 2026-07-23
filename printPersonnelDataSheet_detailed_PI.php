<!DOCTYPE html>
<html>

<?php

 
include('session.php');  
//error_reporting(0);
    
$personnel_id=$_GET['personnel_id']; 

    $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'") or die(mysql_error());
    $staff_row = $staff_query->fetch();

    
include('header_print.php');

?>
 
<body>

<table style="width: 100%;">
<tr>
<td align="left" style="width: 100%; border: none;">
<?php include('header_print_letterHead.php'); ?>
</td>
 
</tr>
</table>
<br />
<center>
<h3>PERSONNEL'S DATA SHEET</h3>
<h4>PERSONAL INFORMATION</h4> 
</center>
<hr />

<?php
$studData_query = $conn->query("select * FROM personnels WHERE personnel_id='$personnel_id'") or die(mysql_error());
$studData_row=$studData_query->fetch();

?>
 
<table style="width: 98%;" align="center">


<tbody>
 
                      
                      
                      <!-- PERSONAL INFORMATION -->
                        <tr>
                        <td style="padding: 4px; background-color: lightgrey;"><strong>DETAILS</strong></td>
                        </tr>
                        <tr>
                        <td>
                          
                          <table style="width: 100%; margin: 8px;">
                          <tr>
                           
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['fname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['mname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['lname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['suffix']; ?></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">First Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Middle Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Last Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Suffix</td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['age']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['sex']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['marital_status']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">Age</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Sex</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Marital Status</td>
                          <td style="font-size: smaller; padding: 0px; border: none;"></td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['bdMM'].'/'.$staff_row['bdDD'].'/'.$staff_row['bdYYYY']; ?></td>
                          <td colspan="3" style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['birth_place']; ?></td>
                          
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">Date of Birth</td>
                          <td colspan="3" style="font-size: smaller; padding: 0px; border: none;">Place of Birth</td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['address']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['email']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['personal_pnum']; ?></td>
                          </tr> 
                          
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Home Address</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Email Address</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Contact Number</td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                           
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['conPerson_fname'].' '.$staff_row['conPerson_mname'].' '.$staff_row['conPerson_lname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['conPerson_relationship']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%;"><?php echo $staff_row['emergency_pnum']; ?></td>
               
                          </tr>
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Contact Person's Fullname</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Relationship</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Contact #</td>
                          </tr>
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <?php
                           
                           $emp_stat_query = $conn->query("SELECT * from dept_offices WHERE do_id='$staff_row[do_id]'");
                           $es_row=$emp_stat_query->fetch();
                           
                           $emp_stat_query2 = $conn->query("SELECT * from designation WHERE des_id='$staff_row[des_id]'");
                           $es_row2=$emp_stat_query2->fetch();
                           
                           $emp_stat_query3 = $conn->query("SELECT * from gass WHERE gass_id='$staff_row[gass_id]'");
                           $es_row3=$emp_stat_query3->fetch();
                           
                           $emp_stat_query4 = $conn->query("SELECT * from emp_status WHERE empStat_id='$staff_row[empStat_id]'");
                           $es_row4=$emp_stat_query4->fetch();
                           
                           ?>
                           
                          <tr>
                        
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row['dept_office_name']; ?></td>
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row2['des_name']; ?></td>
                          </tr>
                          
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Office / Department</td>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Designation</td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><strong style="font-weight: bolder;"><?php echo $es_row3['gass_name']; ?></strong>/<?php echo $es_row3['step']; ?> | <strong style="font-weight: bolder;"><?php echo $es_row3['level']; ?></strong> | <strong style="font-weight: bolder;"><?php echo $es_row3['ratePerDay']; ?></strong></td>
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><strong style="font-weight: bolder;"><?php echo $es_row4['emp_stat_name']; ?></strong> | <strong style="font-weight: bolder;"><?php echo $es_row4['position_class']; ?></strong> | <strong style="font-weight: bolder;"><?php echo $es_row4['status']; ?></strong></td>
                          </tr>
                          
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Salary Grade/Step | Level | RPD</td>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Status of Appointment | Class | Type</td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          
                          <tr>
                        
                         
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['eligibility']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['plantilla_num']; ?></td>
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['appointment_date']; ?>
                           
                           <?php
                            if($staff_row['appointment_date']=='' OR $staff_row['appointment_date']=='  /  /    '){
                                
                            }else{
                                $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date("m/d/Y ")));
                                echo '('.$diff->format('%y').' yrs.)';
                            }
                            ?></td>
                          </tr>
                          
                          <tr>
                      
                          <td style="font-size: smaller; padding: 0px; border: none;">Eligibiity</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Plantilla Number</td>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Appointment Date (No. of years)</td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['tin_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['gsis_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['pagibig_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">TIN</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">GSIS</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">PhilHealth</td>
                          <td style="font-size: smaller; padding: 0px; border: none;"></td>
                          </tr>
                          
                          </table>
                          
                          </td>
                          </tr>
                        
     
                      </tbody>
                      
                      
</table>
<?php include('footer_print.php'); ?>
</body>
</html>