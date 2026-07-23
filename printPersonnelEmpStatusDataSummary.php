<!DOCTYPE html>
<html>
<?php
include('session.php');
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

<hr />

<center>
<h3>PERSONNELS APPOINTMENT SUMMARY</h3>
<h4>AS OF <?php echo date('m/d/Y'); ?></h4>
</center>
<hr />
 

<div class="col-lg-12">
    <div class="row">
    
    <table class="table table-bordered table-striped" style="margin: 0px 8px 0px 8px; width: 98%;">
                          <thead>
                            <tr>
                              <th>STATUS OF APPOINTMENT</th>
                              <th>TOTAL</th>
                              <th>SALARY GRADE LEVEL</th>
                               
                            </tr>
                          </thead>
                          <tbody>
                          
                          <?php
                          $printEmpStat_query = $conn->query("SELECT empStat_id, emp_stat_name FROM emp_status WHERE status='Active'") or die(mysql_error());
                          while ($printES_row=$printEmpStat_query->fetch())
                          { ?>
                         
                            <tr>
                            <td><?php echo $printES_row['emp_stat_name']; ?></td>
                            
                            <td>
                            <?php
                            $finalFirstLevelCtr=0;
                            $finalSecondLevelCtr=0;
                            $finalExManLevelCtr=0;
                            $finalThirdLevelCtr=0;
                            
                            $empStatCtr_query = $conn->query("SELECT gass_id FROM personnels WHERE empStat_id='$printES_row[empStat_id]'") or die(mysql_error());
                            while($lvlCtr_row=$empStatCtr_query->fetch()){
                                
                                
                                    $LevelCtr_query = $conn->query("SELECT level FROM gass WHERE gass_id='$lvlCtr_row[gass_id]'") or die(mysql_error());
                                    $levelCtr_row=$LevelCtr_query->fetch();
                                   
                                    
                                    if($levelCtr_row['level']==="First Level"){
                                        $finalFirstLevelCtr=$finalFirstLevelCtr+1;
                                    }elseif($levelCtr_row['level']==="Second Level"){
                                        $finalSecondLevelCtr=$finalSecondLevelCtr+1;
                                    }elseif($levelCtr_row['level']==="Executive / Managerial"){
                                        $finalExManLevelCtr=$finalExManLevelCtr+1;
                                    }elseif($levelCtr_row['level']==="Third Level"){
                                        $finalThirdLevelCtr=$finalThirdLevelCtr+1;
                                    }
                            
                            } 
                            echo $empStatCtr_query->rowCount(); ?>
                            </td>
                            
                            <td>
                          
                            <table class="table table-bordered" style="margin: 0px 8px 0px 8px; width: 99%;">
                            <thead>
                            <tr>
                            <th style="width: 25%; background-color: white;">First Level</th>
                            <th style="width: 25%; background-color: white;">Second Level</th>
                            <th style="width: 25%; background-color: white;">Executive / Managerial</th>
                            <th style="width: 25%; background-color: white;">Third Level</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            <tr>
                            <td style="background-color: white;"> <?php echo $finalFirstLevelCtr; ?> </td>
                            <td style="background-color: white;"> <?php echo $finalSecondLevelCtr; ?> </td>
                            <td style="background-color: white;"> <?php echo $finalExManLevelCtr; ?> </td>
                            <td style="background-color: white;"> <?php echo $finalThirdLevelCtr; ?> </td>
                            </tr>
                            </tbody>
                              
                              
                            
                            </table>
                            
                            </td>
                            
                            
                            
                            </tr>
                              
                            
                            
                             <?php }  ?>
                           
                          </tbody>
                        </table>
                 
    </div>
</div>

<?php include('footer_print.php'); ?>

</body>
</html>
       
            