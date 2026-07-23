<!DOCTYPE html>
<html>

<?php

include('session.php');

    
$personnel_id=$_GET['personnel_id']; 

    $staff_query = $conn->query("SELECT * FROM personnels WHERE personnel_id='$_GET[personnel_id]'") or die(mysql_error());
    $staff_row = $staff_query->fetch();
    
    $staff_sr_query = $conn->query("SELECT * FROM service_record WHERE personnel_id='$_GET[personnel_id]'") or die(mysql_error());
    $staff_sr_row = $staff_sr_query->fetch();

    // Default fallback signatory
    $sr_certified_name = 'AIREEN B. VARON';
    $sr_certified_position = 'MGADHI';

    // Load configurable signatories when available
    try {
      $table_check = $conn->query("SHOW TABLES LIKE 'signatories_settings'");
      if ($table_check && $table_check->rowCount() > 0) {
        $signatories_stmt = $conn->prepare("SELECT recommending_name, recommending_position FROM signatories_settings LIMIT 1");
        $signatories_stmt->execute();
        $signatories = $signatories_stmt->fetch(PDO::FETCH_ASSOC);

        if ($signatories) {
          if (!empty($signatories['recommending_name'])) {
            $sr_certified_name = (string)$signatories['recommending_name'];
          }
          if (!empty($signatories['recommending_position'])) {
            $sr_certified_position = (string)$signatories['recommending_position'];
          }
        }
      }
    } catch (PDOException $e) {
      error_log('Error loading SR print signatories: ' . $e->getMessage());
    }

    

?>


<head>
<title>MOB - DTR v. 1.0</title>
<meta name="description" content="RFID Attendance Monitoring with SMS">
<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
<link rel="shortcut icon" href="img/<?php echo $sf_row['logo']; ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- data table -->
     <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
     <link rel="stylesheet" type="text/css" href="DataTables/Buttons-1.6.1/css/buttons.dataTables.min.css"/>
     
    <style>
    div.dataTables_wrapper {
        margin-bottom: 3em;
    }
    
    table {
      font-family: Arial Narrow, sans-serif;
      border-collapse: collapse;
      width: 100%;
      page-break-inside:auto;
    }
    
    table td, table th {
      border: 1px solid #ddd;
      padding: 4px;
    }
    
    table th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #fff;
      color: black;
    }
    
    body{
        font-family: Arial Narrow, sans-serif;
        font-size: 14px;
    }
    
    .pb{
       page-break-after: always; 
    }
    
    </style>
    
    <style>
      table {
        page-break-inside: auto;
      }
      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }
      thead {
        display: table-header-group;
      }
      tfoot {
        display: table-footer-group;
      }
    </style>
 
</head>

<style>
/* Flexible page size support for Letter, Folio, and Legal paper */
@page {
   size: auto;
   margin: 0.5in;
}

@media print {
   body {
      margin: 0;
      padding: 20px;
      max-width: 7.5in;
      margin: 0 auto;
   }
   
   table tr { 
      page-break-inside: avoid; 
      page-break-after: auto; 
   }
    
   table tr:nth-child(even) {
      background-color: #f2f2f2;
   }
    
   table tr:hover {
      background-color: #fff;
   } 
 
   thead { 
      display: table-header-group; 
   }
   
   tfoot { 
      display: table-footer-group; 
   }
}
</style>

<body>

            <table style="width: 99%; margin: 4px;">
            
            <tr>
            <td style="padding: 0px; border: none;">
            
            
                          
                          <table style="width: 100%; margin: 4px;">
                            <tr>
                            <td style="width: 100%; border: none; text-align: center;">

<table style="width: 100%; margin-bottom: 8px; text-align: center;">

<tr >
<td style="width: 80px; border: none; background-color: white; padding: 0px;"><img width="40" src="img/<?php echo $sf_row['logo'];?>" /></td>
</tr>
 
<tr>
<td style="font-size: 18px; border: none; background-color: white; padding: 0px 0px 0px 12px; font-weight: bold;"> <?php echo $schoolName; ?></td>
</tr>

<tr>
<td style="border: none; font-size: 14px; background-color: white; padding: 0px 0px 0px 12px; font-weight: bold;">Human Resource Management Office</td>
</tr>

<tr>
<td style="border: none; font-size: 12px; background-color: white; padding: 0px 0px 0px 12px; font-weight: lighter;"><?php echo $sf_row['address']; ?></td>
</tr>

</table>

                            </td>
                            </tr>
                          </table>
                          
                          <center><h4 style="margin: 0px;">SERVICE RECORD</h4></center>
            
            </td>
            </tr>
            
            <tr>
            <td style="padding: 0px; border: none;">
                          <!-- PERSONAL INFORMATION -->
                          <table style="width: 100%; margin-left: 8px;">
                          <tr>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%; font-weight: bold; background-color: white;"><?php echo $staff_row['fname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%; font-weight: bold; background-color: white;"><?php echo $staff_row['mname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%; font-weight: bold; background-color: white;"><?php echo $staff_row['lname']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%; font-weight: bold; background-color: white;"><?php echo $staff_row['suffix']; ?></td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none; background-color: white;">First Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none; background-color: white;">Middle Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none; background-color: white;">Last Name</td>
                          <td style="font-size: smaller; padding: 0px; border: none; background-color: white;">Suffix</td>
                          </tr>
         
                          <tr><td colspan="4" style="font-size: smaller; padding-top: 0px; border: none; background-color: white;"></td></tr>
 
                          <tr>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%; font-weight: bold; background-color: white;"><?php echo $staff_row['bdMM'].'/'.$staff_row['bdDD'].'/'.$staff_row['bdYYYY']; ?></td>
                          <td style="padding: 0px; border: none; font-size: medium; width: 25%; font-weight: bold; background-color: white;"><?php echo $staff_row['birth_place']; ?></td>
                          <td colspan="2" rowspan="2" style="padding: 0px; border: none; font-size: small; background-color: white;">
                          Data herein should be checked from birth or baptismal certificate or some other reliable documents.
                          </td>
                          </tr>
                          
                          <tr>
                          <td style="font-size: smaller; padding: 0px; border: none; background-color: white;">Date of Birth</td>
                          <td style="font-size: smaller; padding: 0px; border: none; background-color: white;">Place of Birth</td>
                          </tr>
                           
                          </table>
                          
            
                        
                        <p style="padding: 8px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is to certify that the employee named above actually rendered service
                        in this office as shown by the service record below, each line of which is 
                        supported by the appointment and other papers actually issued by this office 
                        and approved by the authorized concerned.
                        </p>
                      
                        
                        <table class="table table-bordered table-striped" style="margin: 12px 8px 12px 8px; width: 100%;" align="center">
                        
                        <thead>
                        <tr>
                          <th style="background-color: lightgray; color: black; width: 15%; ">SERVICES<br /><small>From - To</small></th>
                          <th style="background-color: lightgray; color: black; width: 15%;">RECORD OF APPOINTMENT<br /><small>Designation - Status</small></th>
                          <th style="background-color: lightgray; color: black; width: 15%;">SALARY</th>
                          <th style="background-color: lightgray; color: black; width: 40%;">OFFICE OF APPOINTMENT</th>
                          <th style="background-color: lightgray; color: black; width: 15%;">SEPARATION<br /><small>Date (Cause)</small></th>
                        </tr>
                        </thead>
                        
                      <tbody>
                      
                            <?php
                            $subjK_ctr=0;
                            
                            $sr_query = $conn->query("SELECT * FROM service_record WHERE personnel_id='$_GET[personnel_id]' ORDER BY sr_id ASC") or die(mysql_error());
                            while ($sr_row = $sr_query->fetch())
                            {
                                ?>
                                
 
           
                        <tr>
                        
                        <td style="background-color: white; padding: 2px; vertical-align: middle;"><?php
                        
                        if($sr_row['serv_date_to']==""){
                            echo substr($sr_row['serv_date_from'], 5, 2).'/'.substr($sr_row['serv_date_from'], 8, 2).'/'.substr($sr_row['serv_date_from'], 0, 4).' to Present';
                        }else{
                            echo substr($sr_row['serv_date_from'], 5, 2).'/'.substr($sr_row['serv_date_from'], 8, 2).'/'.substr($sr_row['serv_date_from'], 0, 4).' - '.substr($sr_row['serv_date_to'], 5, 2).'/'.substr($sr_row['serv_date_to'], 8, 2).'/'.substr($sr_row['serv_date_to'], 0, 4);
                        }
                        ?></td>
                        <td style="font-size: small; background-color: white; padding: 2px; vertical-align: middle; width: 25%"><?php echo $sr_row['roa_designation'].' - '.$sr_row['roa_status']; ?></td>
                        <td style="background-color: white; padding: 2px; vertical-align: middle;"><?php echo isset($sr_row['salary']) ? $sr_row['salary'] : (isset($sr_row['monthly_salary']) ? $sr_row['monthly_salary'] : '0.00'); ?></td>
                        <td style="font-size: small; background-color: white; padding: 2px; vertical-align: middle; width: 25%;"><?php echo $sr_row['office_appointment']; ?></td>
                        <td style="background-color: white; padding: 2px; vertical-align: middle;">
                        <?php
                        if($sr_row['separate_date']==='' OR $sr_row['separate_date']==='  /  /    '){
                           
                        }else{
                            echo substr($sr_row['separate_date'], 5, 2).'/'.substr($sr_row['separate_date'], 8, 2).'/'.substr($sr_row['separate_date'], 0, 4).'<br />( '.$sr_row['separate_cause'].' )';
                        }
                        ?>
                        </td>
                        </tr> 
                        
                         <?php } ?>
                       <tr>
                       <td colspan="5" style="vertical-align: central; padding: 4px; text-align: center;"><strong>Note:</strong> No leave without pay</td>
                       </tr>
                       
                       <tr>
                       <td colspan="5" style="vertical-align: central; padding: 4px; text-align: left;">
                       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Approved in compliance with Executive Order No. 54 dated August 01, 1954 
                       and in accordance with Circular No. 58, dated August 01, 1954 of the system.
                       </td>
                       </tr>
                       
                      </tbody>
                    </table>
                   
                        <table style="width: 100%; margin-left: 8px;">
                        <tr>
                        <td style="width: 100%; border: none;">Certified correct:</td>
                        </tr>
                        <tr>
                        <td style="border: none;"><br /></td>
                        </tr>
                        <tr>
                        <td style="width: 100%; border: none;"><strong><?php echo htmlspecialchars($sr_certified_name); ?></strong> &nbsp;&nbsp;&nbsp;&nbsp; Date: ___/___/______</td>
                        </tr>
                        <tr>
                        <td style="width: 100%; border: none;"><?php echo htmlspecialchars($sr_certified_position); ?></td>
                        </tr>
                        </table>
                        
            </td>
            </tr>
         
            </table>
            
<?php //include('footer_print.php'); ?>
</body>
</html> 