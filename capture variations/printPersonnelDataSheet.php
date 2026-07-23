<?php

 
include('session.php');  
//error_reporting(0);
    
$personnel_id=$_GET['personnel_id']; 

include('header_print.php');

?>
 
</head>






<body>

<table style="width: 100%;">

<tr>
<td style="width: 100%; border: none;">
<center><h1>PRINT PREVIEW</h1></center>
</td>
</tr>

<tr>
<td align="right" style="width: 100%; border: none;">
<button style="background-color: skyblue; color: white; padding: 8px; border: 2px solid blue; cursor: pointer;" onclick="myFunction()">Print this page</button>
</td>
</tr>
</table>

<script>
function myFunction() {
  window.print();
}
</script>

<div id="section-to-print">

<table style="width: 100%;">
<tr>
<td align="left" style="width: 100%; border: none;">
<table style="width: 100%;">
<tr>

    <td style="width: 80px; border: none;" rowspan="2">
     <img class="pull-right" width="75" height="75" src="img/<?php echo $sf_row['logo'];?>" />
    </td>
    
    <td style="border: none;">&nbsp;</td>
    
    <td style="font-size: x-large; border: none;"> <?php echo $schoolName; ?> </td>

</tr>

<tr>
    <td style="border: none;">&nbsp;</td>
    <td style="border: none; font-size: larger;">
    <?php echo $sf_row['address']; ?>
    </td>
</tr>
 

</table>
</td>
 
</tr>
</table>

<hr />

<?php
$studData_query = $conn->query("select * FROM personnels WHERE personnel_id='$personnel_id'") or die(mysql_error());
$studData_row=$studData_query->fetch();

?>

 


<table style="width: 100%;">

  <tr style="font-size: large;" style="border: none;">
    
    
    <td style="width: 40%; border: none;" colspan="2">
    <small>Employment Status</small><br />
    <strong><?php
    $emp_stat_query = $conn->query("select * from emp_status WHERE empStat_id='$studData_row[empStat_id]'");
    $es_row=$emp_stat_query->fetch();
    echo strtoupper($es_row['emp_stat_name']);?></strong>
    
    </td>
    
    <td style="width: 40%; border: none;" colspan="2">
    <small>Department / Office</small><br />
    <strong><?php
    $emp_stat_query = $conn->query("select * from dept_offices WHERE do_id='$studData_row[do_id]'");
    $es_row=$emp_stat_query->fetch();
    echo strtoupper($es_row['dept_office_name']); ?></strong> 
    
    </td>
     
  </tr>
  
  <tr>
  <td style="width: 70%; border: none;" colspan="2">
    <small>Employee</small><br />
    <strong><?php
    $mname=$studData_row['mname'];
    
    $suffix=$studData_row['suffix'];
    if($suffix === '-') { $suffix=''; }else{ $suffix=$suffix.' '; }
            
    if($mname=='')
    {
            $finalMName=$suffix;
            
            echo strtoupper($studData_row['lname'].", ".$studData_row['fname']." ".$finalMName);
            
    }else{
            
            
            
            $finalMName=$suffix.substr($mname, 0, 1).'.';

            echo strtoupper($studData_row['lname'].", ".$studData_row['fname']." ".$finalMName);

    }
    ?></strong> 
    
   
  </td>
  
  <td style="width: 30%; border: none;">
  <small>Month Covered</small><br />
  <strong><?php echo $_GET['pDataReportType']; ?></strong>
  
  
  </td>
  </tr>
</table>
 <hr />

<table id="myTable">
 
</table>
 
<footer style="margin-top: 8px;">

<div style="float: left;">
<table style="width: 100%;"  >
<tr>

    <td style="width: 80px; border: none;" rowspan="3">
     <img class="pull-right" width="25" height="25" src="img/<?php echo $sf_row['logo'];?>" />
    </td>
    
    <td style="border: none;">&nbsp;</td>
    
    <td style="font-size: small; border: none;"> <?php echo $schoolName; ?> </td>

</tr>
</table>
</div>

<div style="float: right;"><small>Date - Time Printed: <?php echo date('m/d/Y').' - '.date('h:i:s A'); ?></small></div>

</footer>
</div>
</body>
</html>
       
            