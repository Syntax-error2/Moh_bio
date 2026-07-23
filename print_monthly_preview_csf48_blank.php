<!DOCTYPE html>
<html> 

<?php

 
include('session.php');  
//error_reporting(0);

  $get_RFTag_id=$_GET['RFTag_id'];
  $selectedMM=substr($_GET['dateFrom'], 5,2);
  $selectedYYYY=substr($_GET['dateFrom'], 0,4);
 
  $grandTotalamLateMin=0;
  $grandTotalpmLateMin=0;
  
  $grandTotalamUTimeMin=0;
  $grandTotalpmUTimeMin=0;
  
  
  
                 
                if($selectedMM=="01")
                {
                    
                    $mmWords="January";
                    $MMmaxDay=32;
                }
                
                if($selectedMM=="02")
                {
                    $mmWords="February";
                    
                    $leap = date('L', mktime(0, 0, 0, 1, 1, $selectedYYYY));
            
                    if($leap==0)
                    {
                    $MMmaxDay=29;    
                    }else{
                    $MMmaxDay=30;        
                    }
                    
                }
                
                
                if($selectedMM=="03")
                {
                    $mmWords="March";
                    $MMmaxDay=32;    
                }
                
                
                if($selectedMM=="04")
                {
                    $mmWords="April";
                    $MMmaxDay=31;    
                }
                
                
                if($selectedMM=="05")
                {
                    $mmWords="May";
                    $MMmaxDay=32;  

                }
                
                
                if($selectedMM=="06")
                {
                    $mmWords="June";
                    $MMmaxDay=31;
                }
                
                
                
                if($selectedMM=="07")
                {
                    $mmWords="July";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="08")
                {
                    $mmWords="August";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="09")
                {
                    $mmWords="September";
                    $MMmaxDay=31;
                }
                
                
                if($selectedMM=="10")
                {
                    $mmWords="October";
                    $MMmaxDay=32;
                }
                
                
                if($selectedMM=="11")
                {
                    $mmWords="November";
                    $MMmaxDay=31;
                }
                
                
                if($selectedMM=="12")
                {
                    $mmWords="December";
                    $MMmaxDay=32;
                }
  
            $classData="<strong>Class - Type:</strong> ";
        
include('header_print.php');

?>
 
 




<body>

 
 


<table style="width: 45%;">
<tr>
<td align="left" style="width: 100%; border: none;">
<table style="width: 100%;"  >

<tr>
<td style="border: none;">CIVIL SERVICE FORM No. <strong>48</strong></td>
</tr>

<tr>
<td colspan="2" style="font-size: x-large; border: none;"><center>DAILY TIME RECORD</center></td>
</tr>

<tr>
<td style="border: none;" colspan="2">

    <center>
    <p style="font-size: 14px; margin-bottom: 4px;">(Name)</p>
    <p style="font-size: 24px; font-variant-caps: all-petite-caps; margin-top: 4px;">
    ______________________________________
    </p>
    </center>
</td>
</tr>

<tr>
<td style="border: none;">For the month of ________________________ -- ________________________</td>
</tr>

<tr>
<td style="border: none;">Official hours for arrival
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
( Regular days.......</td>
</tr>

<tr>
<td style="border: none;">and departure
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
( Saturdays.......</td>
</tr>

</table>
</td>
 
</tr>
</table>

<br />
 
 

<table id="myTable" style="width: 45%;">
    
  <tr style="font-weight: light; font-size: 14px">
    
    <td rowspan="2" style="width:6%;"><center><strong>DAY</strong></center></td>
    <td colspan="2" style="width:42%;"><center><strong>AM</strong></center></td>
    <td colspan="2" style="width:42%;"><center><strong>PM</strong></center></td>
    <td colspan="2" style="width:10%;"><center><strong><small>LATE &amp; UNDERTIME</small></strong></center></td>
    
  </tr>
  
  <tr style="font-weight: light; font-size: 14px">
    
     
    <td style="width:16%;"><center>&nbsp;&nbsp;Arrival&nbsp;&nbsp;</center></td>
    <td style="width:16%;"><center>Departure</center></td>
    <td style="width:16%;"><center>&nbsp;&nbsp;Arrival&nbsp;&nbsp;</center></td>
    <td style="width:16%;"><center>Departure</center></td>
    <td style="width:8%;"><center>Hours</center></td>
    <td style="width:8%;"><center>Min.</center></td>
  </tr>
 
<?php
 
    for($d=1; $d<$MMmaxDay; $d++){
        
        $dailyLate=0;
        $dailyUTime=0;
        
        if($d<10){
        $logDateCtr=$selectedMM.'/0'.$d.'/'.$selectedYYYY;
        }else{
        $logDateCtr=$selectedMM.'/'.$d.'/'.$selectedYYYY;
        }
 
    ?>
    
  <tr>
 
    <td>
    <?php
    
    $timestamp = strtotime($logDateCtr);
    $dayName=date('l', $timestamp);
    $dayName2=substr($dayName, 0,3);
    echo substr($logDateCtr, 3, 2);
    
    ?>
    </td>
    
    <!-- AM IN -->
    <td> </td>
    
    
    <!-- AM OUT -->
    <td> </td>
    
    
    <!-- PM IN -->
    <td> </td>
    
    
    <!-- PM OUT -->
    <td> </td>
    
    <td> </td>
    
    <td> </td>
     
</tr>
<?php } ?>


<tr>
<td colspan="4" style="padding: 8px 8px 8px 8px; text-align: right;">
 
<strong style="font-size: small; "> TOTAL Late and Undertime:</strong>

</td>
 

<td style="background-color: lightgoldenrodyellow; font-size: small;"><strong> __ hr.</strong></td>
<td colspan="2" style="background-color: lightgoldenrodyellow; font-size: small;"><strong> __ min.</strong></td>



    
</tr>

<tr>
<td colspan="7" style="padding: 8px 8px 8px 8px;">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY on my honor that the above is true and correct
 report of the hours of work performed, record of which was made daily at the time of
  arrival at and departure from office. <br /> 
  
  <p style="float: right; text-decoration-line: underline; font-size: 18px; font-variant: all-petite-caps;">
  ______________________________________
  </p> 
  
</td>
</tr>


<tr>
<td colspan="7" style="padding: 8px 8px 8px 8px;">
 
Verified as to the prescribed office hours. <br /> 

  <p style="float: right; text-decoration-line: underline; font-size: 18px; font-variant: all-petite-caps; margin: 0px;">
  
  <table>
  <tr>
  <td style="text-align: right; border: none;">_______________________________</td>
  </tr>
  
  <tr>
  <td style="text-align: right; border: none;">In-charge</td>
  </tr>
  
  </table>
  </p>
  
</td>
</tr>
 
 
</table>

</body>
</html>
       
            