<!DOCTYPE html>
<html>
<?php

include('session.php');

$degree=$_GET['degree'];
$school_name=$_GET['school_name'];

include('header_print.php');

?>
 

<body>

                    <!-- MALE LIST --><!-- MALE LIST -->
                    <?php if($_GET['print_output']==='Male Only'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <div style="text-align: center; width: 100%;">
                    <h3>MALE PERSONNELS SCHOLASTIC DATA</h3>
                    <p style="font-size: medium;"><?php echo 'Degree: '.$degree.' | School: '.$school_name.''; ?></p>
                    </div>
                    <hr />
                    
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                            
                              <th>PERSONNEL <br /><small>Status</small></th>
                              <th>SCHOLASTIC RECORDS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                $list_ctr=0;
                                $printDataAge_query = $conn->query("SELECT personnel_id, lname, fname, mname, suffix, do_id, des_id, empStat_id FROM personnels WHERE sex='Male' AND (separation_date IS NULL) ORDER BY lname, fname") or die(mysql_error());
                                while($printDA_row=$printDataAge_query->fetch()){
                                
                                $list_ctr+=1;
                                
                                ?>
                                    
     
               
                            <tr>
                            
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo $list_ctr.". ".$printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1);
                                    
                                    }else{
                                        
                                    echo $list_ctr.". ".$printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1);
                                    
                                    }
                                    
                                    $empStat_query = $conn->query("SELECT * FROM emp_status WHERE empStat_id='$printDA_row[empStat_id]'") or die(mysql_error());
                                    $empStat_row = $empStat_query->fetch(); 
                                      
                                    echo "<br /><small>".strtoupper($empStat_row['emp_stat_name'])."</small>";
                      
                                    ?>
                            </td>
                            
                            
                            <td>
                            
                            <table>
                            <tr>
                            <th style="width: 45%;">Course Details</th>
                            <th style="width: 15%;">Year Grauated</th>
                            <th style="width: 40%;">School</th>
                            </tr>
                            
                            <?php
                            if($degree==='ALL' AND $school_name==='ALL'){
                                
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                }elseif($degree!='ALL' AND $school_name==='ALL'){
                                    
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' AND degree='$degree' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                }elseif($degree==='ALL' AND $school_name!='ALL'){
                                    
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' AND school_name='$school_name' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                
                                }else{
                                    
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' AND degree='$degree' AND school_name='$school_name' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                }
                                
                                while($peb_row = $peb_query->fetch()){ ?>
                            <tr>
                            
                            <td><?php echo $peb_row['degree']." | ".$peb_row['course_details']." | ".$peb_row['units']; ?></td>
                            <td><?php echo $peb_row['year_grad']; ?></td>
                            <td><?php echo $peb_row['school_name']; ?></td>
                            
                            </tr>
                            <?php } ?>
                            
                            </table>
                            </td>
                            
                            </tr>
                              
                            
                            
                             <?php
                             
                                
                                } ?>
                           
                          </tbody>
                   </table>
                   </div>
                   </div>
                   <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- END MALE LIST --><!-- END MALE LIST -->
                    
                    
                    
                    
                    <!-- FEMALE LIST --><!-- FEMALE LIST -->
                    <?php if($_GET['print_output']==='Female Only'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center>
                    <h3>FEMALE PERSONNELS SCHOLASTIC DATA</h3>
                    <p style="font-size: medium;"><?php echo 'Degree: '.$degree.' | School: '.$school_name.''; ?></p>
                    </center>
                    <hr />
                    
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                            
                              <th>PERSONNEL <br /><small>Status</small></th>
                              <th>SCHOLASTIC RECORDS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                $list_ctr=0;
                                $printDataAge_query = $conn->query("SELECT personnel_id, lname, fname, mname, suffix, do_id, des_id, empStat_id FROM personnels WHERE sex='Female' AND (separation_date IS NULL) ORDER BY lname, fname") or die(mysql_error());
                                while($printDA_row=$printDataAge_query->fetch()){
                                
                                $list_ctr+=1;
                                
                                $empStat_query = $conn->query("SELECT * FROM emp_status WHERE empStat_id='$printDA_row[empStat_id]'") or die(mysql_error());
                                $empStat_row = $empStat_query->fetch(); 
                                      
                                echo "<br /><small>".strtoupper($empStat_row['emp_stat_name'])."</small>";
                                    
                                ?>
                                    
     
               
                            <tr>
                            
                            <td>
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo $list_ctr.". ".$printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1);
                                    
                                    }else{
                                        
                                    echo $list_ctr.". ".$printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1);
                                    
                                    } ?>
                            </td>
                            
                            
                            <td>
                            
                            <table>
                            <tr>
                            <th style="width: 45%;">Course Details</th>
                            <th style="width: 15%;">Year Grauated</th>
                            <th style="width: 40%;">School</th>
                            </tr>
                            
                            <?php
                            if($degree==='ALL' AND $school_name==='ALL'){
                                
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                }elseif($degree!='ALL' AND $school_name==='ALL'){
                                    
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' AND degree='$degree' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                }elseif($degree==='ALL' AND $school_name!='ALL'){
                                    
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' AND school_name='$school_name' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                
                                }else{
                                    
                                    $peb_query = $conn->query("SELECT * FROM personnel_educ_bg WHERE personnel_id='$printDA_row[personnel_id]' AND degree='$degree' AND school_name='$school_name' ORDER BY year_grad, school_name, degree ASC") or die(mysql_error());
                                
                                }
                                
                                while($peb_row = $peb_query->fetch()){ ?>
                            <tr>
                            
                            <td><?php echo $peb_row['degree']." | ".$peb_row['course_details']." | ".$peb_row['units']; ?></td>
                            <td><?php echo $peb_row['year_grad']; ?></td>
                            <td><?php echo $peb_row['school_name']; ?></td>
                            
                            </tr>
                            <?php } ?>
                            
                            </table>
                            </td>
                            
                            </tr>
                              
                            
                            
                             <?php
                             
                                
                                } ?>
                           
                          </tbody>
                   </table>
                   </div>
