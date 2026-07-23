<!DOCTYPE html>
<html>
<?php

 
include('session.php');  
//error_reporting(0);
 
include('header_print.php');

?>
 
<body>


<script>
$(document).ready(function() {
    $('#example').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                title: '<?php include('header_print_letterHead.php'); ?>',
                messageTop: '<center><h3>LIST OF SEPARATED PERSONNELS</h3></center><hr />',
                messageBottom: '<center>Municipality of Binalbagan - Human Resource Management Office</center>'
            }
        ]
    } );
} );
</script>
 
 

                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center>
                    <h3>LIST OF SEPARATED PERSONNELS - MALE</h3>
                    </center>
 
    
                    <table class="table table-bordered table-striped" style="margin: 0px 8px 0px 8px; width: 100%;">
                    
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DETAILS</th>
                              <th>STATUS</th>
                              
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $empStat_query = $conn->query("SELECT empStat_id, emp_stat_name FROM emp_status WHERE status='Separated' ORDER BY emp_stat_name ASC") or die(mysql_error());
                                while($empStat_row = $empStat_query->fetch()){
                                    
                                $printDataAge_query = $conn->query("SELECT lname, fname, mname, suffix, age, do_id, des_id, appointment_date, separation_date FROM personnels WHERE empStat_id='$empStat_row[empStat_id]' AND sex='Male' ORDER BY lname, fname ASC") or die(mysql_error());
                                while($printDA_row=$printDataAge_query->fetch()){
                                
                                ?>
                         
                            <tr>
                            <td style="vertical-align: middle; padding: 4px;">
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname'];
                                    
                                    }else{
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname']." ".$printDA_row['suffix'];
                                    
                                    } ?>
                            </td>
                            <td style="vertical-align: middle; padding: 4px;"><?php echo $printDA_row['age']; ?></td>
                            <td style="vertical-align: middle; padding: 4px;">
                              <?php
                              
                              $emp_stat_query1 = $conn->query("select des_name from designation WHERE des_id='$printDA_row[des_id]'");
                              $es_row1=$emp_stat_query1->fetch();
                              
                              $emp_stat_query2 = $conn->query("select dept_office_name from dept_offices WHERE do_id='$printDA_row[do_id]'");
                              $es_row2=$emp_stat_query2->fetch();
                              
                              echo '<strong>Dept / Office:</strong> '.$es_row2['dept_office_name'].'<br /> <strong>Designation:</strong> '.$es_row1['des_name'];
                              
                              ?>
                              </td>
                              <td style="vertical-align: middle; padding: 4px;"><?php echo $empStat_row['emp_stat_name']; ?> <br />(<?php echo $printDA_row['appointment_date']; ?> - <?php echo $printDA_row['separation_date']; ?>)</td>
                           
                            </tr>
                              
                            
                            
                             <?php } }  ?>
                           
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
                    <h3>LIST OF SEPARATED PERSONNELS - FEMALE</h3>
                    </center>
 
    
                    <table class="table table-bordered table-striped" style="margin: 0px 8px 0px 8px; width: 100%;">
                    
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>AGE</th>
                              <th>DETAILS</th>
                              <th>STATUS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $empStat_query = $conn->query("SELECT empStat_id, emp_stat_name FROM emp_status WHERE status='Separated' ORDER BY emp_stat_name ASC") or die(mysql_error());
                                while($empStat_row = $empStat_query->fetch()){
                                    
                                $printDataAge_query = $conn->query("SELECT lname, fname, mname, suffix, age, do_id, des_id, appointment_date, separation_date FROM personnels WHERE empStat_id='$empStat_row[empStat_id]' AND sex='Female' ORDER BY lname, fname ASC") or die(mysql_error());
                                while($printDA_row=$printDataAge_query->fetch()){
                                
                                ?>
                         
                            <tr>
                            <td style="vertical-align: middle; padding: 4px;">
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname'];
                                    
                                    }else{
                                        
                                    echo $printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).". ".$printDA_row['lname']." ".$printDA_row['suffix'];
                                    
                                    } ?>
                            </td>
                            <td style="vertical-align: middle; padding: 4px;"><?php echo $printDA_row['age']; ?></td>
                            <td style="vertical-align: middle; padding: 4px;">
                              <?php
                              
                              $emp_stat_query1 = $conn->query("select des_name from designation WHERE des_id='$printDA_row[des_id]'");
                              $es_row1=$emp_stat_query1->fetch();
                              
                              $emp_stat_query2 = $conn->query("select dept_office_name from dept_offices WHERE do_id='$printDA_row[do_id]'");
                              $es_row2=$emp_stat_query2->fetch();
                              
                              echo '<strong>Dept / Office:</strong> '.$es_row2['dept_office_name'].'<br /> <strong>Designation:</strong> '.$es_row1['des_name'];
                              
                              ?>
                              </td>
                              <td style="vertical-align: middle; padding: 4px;"><?php echo $empStat_row['emp_stat_name']; ?> <br />(<?php echo $printDA_row['appointment_date']; ?> - <?php echo $printDA_row['separation_date']; ?>)</td>
                    
                            </tr>
                              
                            
                            
                             <?php } }  ?>
                           
                          </tbody>
                        </table>
                    
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
</body>
</html>