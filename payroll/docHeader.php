
<table style="width: 100%;">
<tr>
<td align="left" style="width: 50%; border: none;">
<table style="width: 500px;"  >
<tr>

    <td style="width: 80px; border: none;" rowspan="3">
     <img class="pull-right" width="75" height="75" src="img/<?php echo $sf_row['logo'];?>" />
    </td>
    
    <td style="border: none;">&nbsp;</td>
    
    <td style="font-size: x-large; border: none;"> <?php echo $schoolName; ?> </td>

</tr>

<tr>
    <td style="border: none;">&nbsp;</td>
    <td style="border: none;">
    <?php echo $sf_row['address']; ?>
    </td>
</tr>

<tr>
    <td style="border: none;">&nbsp;</td>
    <td style="border: none;">
    S.Y. <?php echo $activeSchoolYear;?> &middot; <?php echo $activeSemester; ?>
    </td>
</tr>

</table>
</td>









<!--
<td align="right" style="width: 50%; border: none;">
<table style="width: 500px;"  >
<tr>

    <td style="width: 80px; border: none;" rowspan="3">
     <img class="pull-right" width="75" height="75" src="img/ras_logo.png" />
    </td>
    
    <td style="border: none;">&nbsp;</td>
    
    <td style="font-size: x-large; border: none;">RFID Attendance System</td>

</tr>

<tr>
    <td style="border: none;">&nbsp;</td>
    <td style="border: none;">
    with SMS Notification Ver. 1.0
    </td>
</tr>

<tr>
    <td style="border: none;">&nbsp;</td>
    <td style="border: none;">
    By: T. Taroma &amp; E. Magtolis Jr.
    </td>
</tr>

</table>
</td>
-->
</tr>
</table>



<br />
<table id="myTable">

  <tr style="font-size: large;">
    
    <td style="width: 20%;"><strong>Date: </strong><?php echo $printDate; ?></td>
    <td style="width: 40%;" colspan="2"><?php echo $classData; ?></td>
    <td style="width: 40%;" colspan="2"><strong>Adviser: </strong><?php echo $setClass_row['adviser']; ?></td>
     
  </tr>
  
</table>
 