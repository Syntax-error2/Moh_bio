<!DOCTYPE html>
<html>

<?php
include('session.php');
include('header_print.php');

$do_id = $_GET['do_id'];
$empStat_id = $_GET['empStat_id'];
$sex = $_GET['sex'];
?>
 
<body>

                    <?php
                    
                    if($empStat_id > 0){
                        
                        $empStat_query = $conn->query("SELECT * FROM emp_status WHERE empStat_id='$empStat_id'");
                        $empStat_row = $empStat_query->fetch();
                        
                        $status_desc = $empStat_row['emp_stat_name'];
                        
                    }else{
                        
                        $status_desc = "";
                        
                    }
                    
                    if($do_id > 0){
                        
                        $dept_off2_query = $conn->query("SELECT * FROM dept_offices WHERE do_id = '$do_id' ORDER BY dept_office_name ASC");
                        $do2_row = $dept_off2_query->fetch();
                    
                        $office_name = $do2_row['dept_office_name'];
                    
                    }else{
                        
                        $office_name = "All Department and Offices";
                        
                    }
                    
                    ?>
 
                    <!-- //MALE EMPLOYEES - BY JOB STATUS // --><!-- //MALE EMPLOYEES - BY JOB STATUS // -->
                    <?php if($sex == 'Male Only'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <div class="text-center pt-2 pb-2">
                    <h3>LIST OF MALE <?php echo strtoupper($status_desc); ?> PERSONNELS</h3>
                    <h4><?php echo strtoupper($office_name); ?></h4>
                    </div>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th style="width: 10%; font-size: 16px; text-align: center;">ID CODE</th>
                              <th style="width: 50%; font-size: 16px;">PERSONNEL</th>
                              <th style="font-size: 16px;">REMARKS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                
                                if($do_id > 0 AND $empStat_id > 0){
                                
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Male' AND do_id='$do_id' AND empStat_id='$empStat_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                
                                }elseif($do_id == 0 AND $empStat_id > 0){
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Male' AND empStat_id='$empStat_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }elseif($do_id > 0 AND $empStat_id == 0){
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Male' AND do_id='$do_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }else{
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Male' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }
                                
                                while ($printDA_row=$printDataAge_query->fetch()){
                                    
                                    $maleCtr=$maleCtr+1;
                                    
                                ?>
                         
                            <tr>
                            
                            <td style="font-size: 14px; text-align: center;"><?php echo $printDA_row['personnel_id_code'];?></td>
                            
                            <td  style="font-size: 14px;">
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            
                            <td></td>
                            
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- //END MALE EMPLOYEES - BY JOB STATUS // --><!-- //END MALE EMPLOYEES - BY JOB STATUS // -->
                    
                    
                    <!-- //FEMALE EMPLOYEES - BY JOB STATUS // --><!-- //FEMALE EMPLOYEES - BY JOB STATUS // -->
                    <?php if($sex == 'Female Only'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <div class="text-center pt-2 pb-2">
                    <h3>LIST OF FEMALE <?php echo strtoupper($status_desc); ?> PERSONNELS</h3>
                    <h4><?php echo strtoupper($office_name); ?></h4>
                    </div>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th style="width: 10%; font-size: 16px; text-align: center;">ID CODE</th>
                              <th style="width: 50%; font-size: 16px;">PERSONNEL</th>
                              <th style="font-size: 16px;">REMARKS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                
                                if($do_id > 0 AND $empStat_id > 0){
                                
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Female' AND do_id='$do_id' AND empStat_id='$empStat_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                
                                }elseif($do_id == 0 AND $empStat_id > 0){
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Female' AND empStat_id='$empStat_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }elseif($do_id > 0 AND $empStat_id == 0){
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Female' AND do_id='$do_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }else{
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix FROM personnels WHERE sex='Female' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }
                                
                                while ($printDA_row=$printDataAge_query->fetch()){
                                    
                                    $maleCtr=$maleCtr+1;
                                    
                                ?>
                         
                            <tr>
                            
                            <td style="font-size: 14px; text-align: center;"><?php echo $printDA_row['personnel_id_code'];?></td>
                            
                            <td  style="font-size: 14px;">
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            
                            <td></td> 
                            
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                 
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- //END FEMALE EMPLOYEES - BY JOB STATUS // --><!-- //END FEMALE EMPLOYEES - BY JOB STATUS // -->
                    
                    
                    <!-- //ALL-MIXED EMPLOYEES - BY JOB STATUS // --><!-- //ALL-MIXED EMPLOYEES - BY JOB STATUS // -->
                    <?php if($sex == 'All-Mixed'){ ?>
                    <div class="row">
                    <div class="col-lg-12">
                    
                    <?php include('header_print_letterHead.php'); ?>
                    
                    <div class="text-center pt-2 pb-2">
                    <h3>LIST OF <?php echo strtoupper($status_desc); ?> PERSONNELS</h3>
                    <h4><?php echo strtoupper($office_name); ?></h4>
                    </div>
                    
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="example" class="display" style="width:100%">
                          <thead>
                            <tr>
                              <th style="width: 10%; font-size: 16px; text-align: center;">ID CODE</th>
                              <th style="width: 50%; font-size: 16px;">PERSONNEL</th>
                              <th style="width: 5%; font-size: 16px; text-align: center;">SEX</th>
                              <th style="font-size: 16px;">REMARKS</th>
                            </tr>
                          </thead>
                          <tbody>
                          
                
                                    
                                <?php
                                $maleCtr=0;
                                
                                if($do_id > 0 AND $empStat_id > 0){
                                
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix, sex FROM personnels WHERE do_id='$do_id' AND empStat_id='$empStat_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                
                                }elseif($do_id == 0 AND $empStat_id > 0){
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix, sex FROM personnels WHERE empStat_id='$empStat_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }elseif($do_id > 0 AND $empStat_id == 0){
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix, sex FROM personnels WHERE do_id='$do_id' AND (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }else{
                                    
                                    $printDataAge_query = $conn->query("SELECT personnel_id_code, lname, fname, mname, suffix, sex FROM personnels WHERE (separation_date IS NULL) ORDER BY lname, fname ASC");
                                    
                                }
                                
                                while ($printDA_row=$printDataAge_query->fetch()){
                                    
                                    $maleCtr=$maleCtr+1;
                                    
                                ?>
                         
                            <tr>
                            
                            <td style="font-size: 14px; text-align: center;"><?php echo $printDA_row['personnel_id_code'];?></td>
                            
                            <td  style="font-size: 14px;">
                                    <?php
                          
                 
                                    if($printDA_row['suffix']=="-")
                                    {
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    }else{
                                        
                                    echo utf8_decode($printDA_row['lname'].", ".$printDA_row['fname']." ".$printDA_row['suffix']." ".substr($printDA_row['mname'], 0,1).".");
                                    
                                    } ?>
                            </td>
                            <td style="font-size: 14px; text-align: center;"><?php if($printDA_row['sex'] == "Male"){ echo "M"; }elseif($printDA_row['sex'] == "Female"){ echo "F"; }else{ echo "X"; } ?></td>
                            <td></td> 
                            
                            </tr>
                            
                            <?php }  ?>
                           
                          </tbody>
                        </table>
                        </div>
                        
                    </div>
                    </div>
                    <?php include('footer_print.php'); ?>
                    <?php } ?>
                    <!-- //END ALL-MIXED EMPLOYEES - BY JOB STATUS // --><!-- //END ALL-MIXED EMPLOYEES - BY JOB STATUS // -->


</body>
</html>
