<!DOCTYPE html>
<html>

<?php

 
include('session.php');  
//error_reporting(0);

  $gender=$_GET['gender']; 
   
        
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
                messageTop: '<center><h3>LIST OF PERSONNELS BY GENDER</h3><h4><?php echo 'Gender: '.$gender; ?></h4></center><hr />',
                messageBottom: '<center>Municipality of Binalbagan - Human Resource Management Office</center>'
            }
        ]
    } );
} );
</script>
 
 
 
                    <div class="row">
                    <div class="col-lg-12">
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <center><h3>LIST OF PERSONNELS BY GENDER</h3><h4><?php echo 'Gender: '.$gender; ?></h4></center><hr />
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th>PERSONNEL</th>
                              <th>DEPT / OFFICE - DESIGNATION</th>
                              <th>GENDER</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                                <?php
                                if($gender=='ALL'){
                                $printDataMale_query = $conn->query("SELECT lname, fname, mname, suffix, sex, do_id, des_id FROM personnels WHERE sex='Male' AND (separation_date IS NULL) ORDER BY lname, fname ASC") or die(mysql_error());
                                while ($printDM_row=$printDataMale_query->fetch())
                                { ?>
                                <tr>
                            <td>
                                    <?php
                          
                 
                                    if($printDM_row['suffix']=="-")
                                    {
                                        
                                    echo $printDM_row['fname']." ".substr($printDM_row['mname'], 0,1).". ".$printDM_row['lname'];
                                    
                                    }else{
                                        
                                    echo $printDM_row['fname']." ".substr($printDM_row['mname'], 0,1).". ".$printDM_row['lname']." ".$printDM_row['suffix'];
                                    
                                    } ?>
                            </td>
                       
                            <td>
                              <?php
                              
                              $emp_stat_query1 = $conn->query("select des_name from designation WHERE des_id='$printDM_row[des_id]'");
                              $es_row1=$emp_stat_query1->fetch();
                              
                              $emp_stat_query2 = $conn->query("select dept_office_name from dept_offices WHERE do_id='$printDM_row[do_id]'");
                              $es_row2=$emp_stat_query2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                              
                              </td>
                             
                             <td><?php echo $printDM_row['sex']; ?></td>
                            </tr>
                                <?php } ?>
                                
                                
                                
                                <?php $printDataFemale_query = $conn->query("SELECT lname, fname, mname, suffix, sex, do_id, des_id FROM personnels WHERE sex='Female' AND (separation_date IS NULL) ORDER BY lname, fname ASC") or die(mysql_error());
                                while ($printDF_row=$printDataFemale_query->fetch())
                                { ?>
                                    
                                    <tr>
                            <td>
                                    <?php
                          
                 
                                    if($printDF_row['suffix']=="-")
                                    {
                                        
                                    echo $printDF_row['fname']." ".substr($printDF_row['mname'], 0,1).". ".$printDF_row['lname'];
                                    
                                    }else{
                                        
                                    echo $printDF_row['fname']." ".substr($printDF_row['mname'], 0,1).". ".$printDF_row['lname']." ".$printDF_row['suffix'];
                                    
                                    } ?>
                            </td>
                       
                            <td>
                              <?php
                              
                              $emp_stat_query1 = $conn->query("select des_name from designation WHERE des_id='$printDF_row[des_id]'");
                              $es_row1=$emp_stat_query1->fetch();
                              
                              $emp_stat_query2 = $conn->query("select dept_office_name from dept_offices WHERE do_id='$printDF_row[do_id]'");
                              $es_row2=$emp_stat_query2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                              
                              </td>
                             
                             <td><?php echo $printDF_row['sex']; ?></td>
                            </tr>
                            
                                <?php } ?>
                                
                                <?php }else{ ?>
                                    
                                <?php
                                
                                $printDataAge_query = $conn->query("SELECT lname, fname, mname, suffix, sex, do_id, des_id FROM personnels WHERE sex='$gender' AND (separation_date IS NULL) ORDER BY lname, fname ASC") or die(mysql_error());
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
                       
                            <td>
                              <?php
                              
                              $emp_stat_query1 = $conn->query("select des_name from designation WHERE des_id='$printDA_row[des_id]'");
                              $es_row1=$emp_stat_query1->fetch();
                              
                              $emp_stat_query2 = $conn->query("select dept_office_name from dept_offices WHERE do_id='$printDA_row[do_id]'");
                              $es_row2=$emp_stat_query2->fetch();
                              
                              echo $es_row2['dept_office_name'].' - '.$es_row1['des_name'];
                              
                              ?>
                              
                              </td>
                             
                             <td><?php echo $printDA_row['sex']; ?></td>
                            </tr>
                              
                            
                            
                             <?php } } ?>
                           
                          </tbody>
                        </table>
                 
    </div>
</div>

<?php include('footer_print.php'); ?>

</body>
</html>
       
            