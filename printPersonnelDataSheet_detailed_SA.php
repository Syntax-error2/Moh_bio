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
<h4>SEMINARS ATTENDED</h4> 
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
                        <td style="padding: 4px; background-color: lightgrey;"><strong>PERSONNEL DETAILS</strong></td>
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
                          
                          </table>
                          
                          <table style="width: 100%; margin: 8px;">
                          
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
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row2['des_name']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium;"><?php echo $es_row4['emp_stat_name']; ?></td>
                          </tr>
                          
                          <tr>
                          <td colspan="2" style="font-size: smaller; padding: 0px; border: none;">Office / Department</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Designation</td>
                          <td style="font-size: smaller; padding: 0px; border: none;">Employment Status</td>
                          </tr>
                           
                          </table>
                          
                          </td>
                          </tr>
                        
                        
                         
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
                            
                            $ps_query = $conn->query("SELECT * FROM personnel_seminars WHERE personnel_id='$_GET[personnel_id]' ORDER BY ps_id ASC") or die(mysql_error());
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
<?php include('footer_print.php'); ?>
</body>
</html> 