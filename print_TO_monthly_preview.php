<!DOCTYPE html>
<html>

<?php

include('session.php');  
//error_reporting(0);

   
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
  
                 
                if($selectedMM=="01")
                {
                    
                    $mmWords="January";
                }
                
                if($selectedMM=="02")
                {
                    $mmWords="February";
                }
                
                
                if($selectedMM=="03")
                {
                    $mmWords="March"; 
                }
                
                
                if($selectedMM=="04")
                {
                    $mmWords="April"; 
                }
                
                
                if($selectedMM=="05")
                {
                    $mmWords="May";

                }
                
                
                if($selectedMM=="06")
                {
                    $mmWords="June";
                }
                
                
                
                if($selectedMM=="07")
                {
                    $mmWords="July";
                }
                
                
                if($selectedMM=="08")
                {
                    $mmWords="August";
                }
                
                
                if($selectedMM=="09")
                {
                    $mmWords="September";
                }
                
                
                if($selectedMM=="10")
                {
                    $mmWords="October";
                }
                
                
                if($selectedMM=="11")
                {
                    $mmWords="November";
                }
                
                
                if($selectedMM=="12")
                {
                    $mmWords="December";
                }
                
                $date_from=$selectedMM.'/01/'.$selectedYYYY;
                $date_to=$selectedMM.'/31/'.$selectedYYYY;

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

                <table id="myTable" style="width: 100%;"> 
                      
                      <thead>
                        <tr>
                          <th style="width: 15%;">TO CODE<br /><small>Travel Date</small></th>
                          <th style="width: 35%;">PERSONNEL</th>
                          <th style="width: 45%;">DETAILS</th>
                     
                        </tr>
                      </thead>
                      
                      <tbody>
                      
                      <?php
                      $row_ctr=0;
                               
                      $new_clearance_query = $conn->query("SELECT DISTINCT 
                      travel_code, 
                      travel_date,
                      purpose,
                      description,
                      location,
                      travel_type  FROM personnel_official_travel_logs WHERE travel_date BETWEEN '$date_from' AND '$date_to' ORDER BY travel_date ASC");
                      while($nc_row = $new_clearance_query->fetch()){
                      $row_ctr=$row_ctr+1;
                      
                      ?>
                      
                        <tr>
                          <td>
                          <?php echo $nc_row['travel_code']; ?><br />
                          <small><?php echo $nc_row['travel_date']; ?></small>
                          </td>
                          
                          <td>
                          <ul>
                          <?php
                          $pi_query = $conn->query("SELECT personnel_id FROM personnel_official_travel_logs WHERE travel_code='$nc_row[travel_code]'");
                          while($pi_row = $pi_query->fetch())
                          {
                          
                          $studData_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$pi_row[personnel_id]'") or die(mysql_error());
                          $sd_row=$studData_query->fetch();
                          
                          echo '<li>';
                          if($sd_row['suffix']=="-")
                          {
                            echo $sd_row['fname']." ".substr($sd_row['mname'], 0,1).". ".$sd_row['lname'];
                          
                          }else{
                            
                            echo $sd_row['fname']." ".substr($sd_row['mname'], 0,1).". ".$sd_row['lname']." ".$sd_row['suffix'];
                          }  
                          
                          echo '</li>';
                          
                          } ?>
                          </ul>
                          </td>
                          
                          <td>
                          <strong style="font-weight: bold;">PURPOSE:</strong> <?php echo $nc_row['purpose']; ?><br />
                          <strong style="font-weight: bold;">DESCRIPTION:</strong> <?php echo $nc_row['description']; ?><br />
                          <strong style="font-weight: bold;">LOCATION:</strong> <?php echo $nc_row['location']; ?><br />
                          <strong style="font-weight: bold;">TYPE:</strong> <?php echo $nc_row['travel_type']; ?>
                          </td>
                        
                        </tr>
                      
                      
                      <?php }?>
                      
                      </tbody>
                     
                    </table>
                    
<?php include('footer_print.php'); ?>
</body>
</html>