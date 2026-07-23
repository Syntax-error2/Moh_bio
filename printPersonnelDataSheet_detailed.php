<!DOCTYPE html>
<html>

<?php

include('session.php');  
include('dbcon.php');
//error_reporting(0);
    
$personnel_id = $_GET['personnel_id'] ?? '';

  $staff_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
  $staff_stmt->execute([':personnel_id' => $personnel_id]);
  $staff_query = $staff_stmt;
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
</center>
<hr />

<?php
$studData_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id = :personnel_id");
$studData_stmt->execute([':personnel_id' => $personnel_id]);
$studData_query = $studData_stmt;
$studData_row=$studData_query->fetch();

?>
 
<table style="width: 98%;" align="center">

<tbody>

                      <!-- PERSONAL INFORMATION -->
                        <tr>
                        <td style="padding: 4px; background-color: lightgrey;"><strong>PERSONAL INFORMATION</strong></td>
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
                          <td colspan="2" style="padding: 0px; border: none; font-size: medium;">
                          
                          <?php
                          if($staff_row['separation_date']=='' OR $staff_row['separation_date']=='  /  /    '){
                            echo $staff_row['appointment_date'].' - '.date("m/d/Y");
                          }else{
                            echo $staff_row['appointment_date'].' - '.$staff_row['separation_date'];
                          }
                          ?>
                           
                           <?php
                            if($staff_row['appointment_date']=='' OR $staff_row['appointment_date']=='  /  /    '){
                                
                            }else{
                                
                                if($staff_row['separation_date']=='' OR $staff_row['separation_date']=='  /  /    '){
                                    $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date("m/d/Y")));
                                }else{
                                    $diff = date_diff(date_create($staff_row['appointment_date']), date_create(date($staff_row['separation_date'])));
                                }
                                
                                echo '('.$diff->format('%y').' yrs.)';
                            }
                            ?></td>
                          </tr>
                          
                          <tr>
                      
                          <td style="font-size: smaller; padding: 0px; border: none;">Eligibiity</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Plantilla Number</td>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">
                          <?php
                          if($staff_row['separation_date']=='' OR $staff_row['separation_date']=='  /  /    '){ ?>
                            Appointment Date - Present (No. of years)
                          <?php }else{ ?>
                            Appointment Date - Separation Date (No. of years)
                          <?php } ?>
                          </td>
                          </tr>
                          
                          
                          <tr><td colspan="4" style="font-size: smaller; padding: 8px; border: none;"></td></tr>
                          
                          <tr>
                        
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['tin_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['gsis_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['pagibig_num']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $staff_row['philHealth_num']; ?></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none;">TIN</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">GSIS</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Pag-IBIG MID</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">PhilHealth</td>
                          </tr>
                          
                          </table>
                          
                          </td>
                          </tr>
</tbody>

</table>  
<br />
<table style="width: 98%;" align="center">

<tbody>
                        <!-- EDUCATIONAL ATTAINMENT -->
                        <tr>
                        <td style="padding: 4px; background-color: lightgrey;"><strong>EDUCATIONAL ATTAINMENT</strong></td>
                        </tr>
                        <tr>
                        <td>
                        
                     
                        <table class="table table-bordered table-striped" style="margin: 12px 8px 12px 8px; width: 99%;" align="center">
                          <thead>
                            <tr>
                               
                              <th style="background-color: lightgray; color: black;">DEGREE</th>
                              <th style="background-color: lightgray; color: black;">COURSE</th>
                              <th style="background-color: lightgray; color: black;">UNITS</th>
                              <th style="background-color: lightgray; color: black;">YEAR GRADUATED</th>
                              <th style="background-color: lightgray; color: black;">SCHOOL</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                $subjK_ctr=0;
                                
                                $peb_stmt = $conn->prepare("SELECT * FROM personnel_educ_bg WHERE personnel_id = :personnel_id ORDER BY eb_id ASC");
                                $peb_stmt->execute([':personnel_id' => $personnel_id]);
                                $peb_query = $peb_stmt;
                                while ($peb_row = $peb_query->fetch())
                                {
                                    ?>
                                    
     
               
                            <tr>
                             
                        
                            <td><?php echo $peb_row['degree']; ?></td>
                            <td><?php echo $peb_row['course_details']; ?></td>
                            <td><?php echo $peb_row['units']; ?></td>
                            <td><?php echo $peb_row['year_grad']; ?></td>
                            <td><?php echo $peb_row['school_name']; ?></td>
                            </tr> 
                            
                             <?php } ?>
                           
                          </tbody>
                        </table>
                  
                  
                        </td>
                        </tr>
</tbody>

</table>  
<br />
<table style="width: 98%;" align="center">

<tbody>
                        <!-- SEMINARS ATTENDED -->
                      
                        <tr>
                        <td style="padding: 4px; background-color: lightgrey;"><strong>SEMINAR'S ATTENDED</strong></td>
                        </tr>
                        <tr>
                        <td>
                        
                     
                        <table class="table table-bordered table-striped" style="margin: 12px 8px 12px 8px; width: 99%;" align="center">
                          <thead>
                        <tr>
                           
                          <th style="background-color: lightgray; color: black;">TITLE</th>
                          <th style="background-color: lightgray; color: black;">DESCRIPTION</th>
                          <th style="background-color: lightgray; color: black;">VENUE</th>
                          <th style="background-color: lightgray; color: black;">DATE</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                            $subjK_ctr=0;
                            
                            $ps_stmt = $conn->prepare("SELECT * FROM personnel_seminars WHERE personnel_id = :personnel_id ORDER BY ps_id ASC");
                            $ps_stmt->execute([':personnel_id' => $personnel_id]);
                            $ps_query = $ps_stmt;
                            while ($ps_row = $ps_query->fetch())
                            {
                                ?>
                                
 
           
                        <tr>
                         
                        
                        <td><?php echo $ps_row['seminar_title']; ?></td>
                        <td><?php echo $ps_row['seminar_desc']; ?></td>
                        <td><?php echo $ps_row['seminar_venue']; ?></td>
                        <td><?php echo $ps_row['event_date']; ?></td>
                        </tr> 
                        
                         <?php } ?>
                       
                      </tbody>
                    </table>
                        </td>
                        </tr> 
                    
</tbody>

</table>  
<h1 class="pb"></h1>
<table style="width: 98%;" align="center">

<tbody>
                    <!-- SERVICE RECORD -->
                        
                        <tr>
                        <td style="padding: 4px; background-color: lightgrey;"><strong>SERVICE RECORD</strong></td>
                        </tr>
                        
                        <tr>
                        <td>
                        
                     
                        <table class="table table-bordered table-striped" style="margin: 12px 8px 12px 8px; width: 99%;" align="center">
                        <thead>
                        <tr>
                         
                          <th style="background-color: lightgray; color: black;">SERVICES<br /><small>From - To</small></th>
                          <th style="background-color: lightgray; color: black;">RECORD OF APPOINTMENT<br /><small>Designation - Status</small></th>
                          <th style="background-color: lightgray; color: black;">SALARY</th>
                          <th style="background-color: lightgray; color: black;">OFFICE OF APPOINTMENT</th>
                          <th style="background-color: lightgray; color: black;">SEPARATION<br /><small>Date - Cause</small></th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                            $subjK_ctr=0;
                            
                            $sr_stmt = $conn->prepare("SELECT * FROM service_record WHERE personnel_id = :personnel_id ORDER BY sr_id ASC");
                            $sr_stmt->execute([':personnel_id' => $personnel_id]);
                            $sr_query = $sr_stmt;
                            while ($sr_row = $sr_query->fetch())
                            {
                                ?>
                                
 
           
                        <tr>
                        
                        <td><?php echo substr($sr_row['serv_date_from'], 5, 2).'/'.substr($sr_row['serv_date_from'], 8, 2).'/'.substr($sr_row['serv_date_from'], 0, 4).' - '.substr($sr_row['serv_date_to'], 5, 2).'/'.substr($sr_row['serv_date_to'], 8, 2).'/'.substr($sr_row['serv_date_to'], 0, 4); ?></td>
                        <td><?php echo $sr_row['roa_designation'].' - '.$sr_row['roa_status']; ?></td>
                        <td><?php echo $sr_row['salary']; ?></td>
                        <td><?php echo $sr_row['office_appointment']; ?></td>
                        <td><?php echo substr($sr_row['separate_date'], 5, 2).'/'.substr($sr_row['separate_date'], 8, 2).'/'.substr($sr_row['separate_date'], 0, 4).' - '.$sr_row['separate_cause']; ?></td>
                        </tr> 
                        
                         <?php } ?>
                       
                      </tbody>
                    </table>
                        </td>
                        </tr> 
                        

                      </tbody>
                      
                      
</table>
<?php include('footer_print.php'); ?>
</body>
</html>