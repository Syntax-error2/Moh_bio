<?php include('dbcon.php'); ?>

<html>
<head>

<title>MOC - HRMS</title>

<meta name="description" content="RFID Attendance Monitoring with SMS">

<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.css">

<link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">

<link rel="shortcut icon" href="img/<?php echo $sf_row['logo']; ?>">

  
<?php 
 


function get_client_ip7() {
    $ipaddress7 = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress7 = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress7 = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress7 = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress7 = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress7 = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress7 = getenv('REMOTE_ADDR');
    else
        $ipaddress7 = 'UNKNOWN';
    return $ipaddress7;
}


if(get_client_ip7()=="::1")
{
  $machine_ip7=gethostbyname(trim(`hostname`));  
}else{
  $machine_ip7=get_client_ip7();
}


    try
    {
            $blank='';
            $dataFile=fopen("\\\\".$machine_ip7."\\rfid\\TEST\\data.enr", "w") or die ();
            fwrite($dataFile, $blank);
            fclose($dataFile);
    }
    catch (Exception $e){}

$conn->query("UPDATE client_computer SET display_time=0, announcement_img=0, RFID_tag='' WHERE ipAddress='$machine_ip7'");

$conn->query("DELETE FROM personnel_logs WHERE client_ip='$machine_ip7' AND logFlow=''");
 
?>


<?php include('refLastTagCSS.php'); ?>
 
 


</head>

<body>


<div id="my_camera" style="display: none;"></div>
<input name="submitSnapshot" type="button" value="Take Snapshot" onclick="take_snapshot()" style="display: none;" />
<!-- <div id="results">Your captured image will appear here...</div> -->
     
<div id="wrapper">

<!-- LEFT PANEL -->
<div id="left">

<!-- MAIN HEADER -->
<div id="rcorners4">
<table style="width: 100%;">
<tr>


<td>

<table align="center" style="height: 100%; margin-bottom: 77px;">
<tr>
<td rowspan="3" align="center">
 
<a href="index.php"><img src="img/<?php echo $sf_row['logo']; ?>" width="100" height="100" style="border: solid 1px white; border-radius: 50px;" /></a>
 
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td align="center">
<strong style="font-size: 30px; color: white;"><?php echo $sf_row['schoolName']; ?></strong>
</td>
</tr>

<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td align="center" style="font-size: 22px; color: white;"><?php echo $sf_row['division']." District".' - '.$sf_row['address'].' '.$sf_row['deped_id']; ?></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>

<td align="center" style="font-size: 14px; color: white;">
<?php
echo $sf_row['emailAddress']."<strong>&nbsp;&nbsp;&middot;&nbsp;&nbsp;</strong>".$sf_row['contactNumber'];
?>
</td>
</tr>
 
</table>

</td>

</tr>
</table>
</div>
<!-- END MAIN HEADER -->




<div id="screenDTimer"></div>

<div id="screen"></div>

<form id="addForm">

<input type="hidden" name="image" class="image-tag" />

<div id="screen2"></div>
      
</form>
 

<!-- MAIN FOOTER -->
<div id="rcorners5">
<h2 style="margin-bottom: 0px;" align="center">GOOD DAY! PLEASE TAP RFID CARD TO LOG</h2>
<p align="center" style="font-size: small; margin: 2px;">
RFID Card problem? <a href="#" style="color: orange;">Please log your time at <strong>guard on duty</strong></a>.
</p>
</div>
<!-- END MAIN FOOTER -->

 
 


</div>
<!-- END LEFT PANEL -->

<!-- RIGHT PANEL -->
<div id="right">
 
    <div id="rcorners3" style="height: 25%;">
    
    <div align="center" style="font-size: 68px; color: red;" id="clock"> </div>
   
    <div id="screenRefDate" align="center" style="font-size: 32px; color: red;"></div>
     
    </div>

    <div id="screen22"></div>
  

</div>
<!-- END RIGHT PANEL -->

</div>

<!-- INVALID SNACK BARS -->
<div id="snackbar">TAG NOT FOUND</div>
<div id="snackbar2">CAN'T LOG AT THIS TIME</div>
<div id="snackbar4">SCHEDULE NOT FOUND</div>
<div id="snackbar5">NO AM OUT LOG</div>
<div id="snackbar6">PROCESSING...</div>
<!-- END INVALID SNACK BARS -->
 
<marquee>
<p style="margin-top: 2px; margin-bottom: 0px;">
<?php 
$news_query = $conn->query("SELECT news_title, news_contents FROM news WHERE ipAddress='$machine_ip7'") or die(mysql_error());
while($news_row = $news_query->fetch()){ ?>
<?php
if($news_row['news_title']==""){
    
}else{
echo '<strong>'.$news_row['news_title'].':</strong> ';  
}

echo $news_row['news_contents'].'&nbsp;&nbsp;<i class="fa fa-circle" style="font-size: 6px;"></i>&nbsp;&nbsp;'; ?>

<?php } ?>
</p>
</marquee>



<?php

$clientComp_query=null;
$news_query=null;
$conn=null;

?>
</body>



<?php include('scripts_files.php'); ?>

<script src="js/1.0.25_webcam.min.js"></script>

<script>
$(document).ready(function(){
	setInterval(function(){
		$("#screenDTimer").load('displayTimer.php')

    }, 1000);
});



$(document).ready(function(){
	setInterval(function(){
		$("#screenRefDate").load('refLastTag_date.php')
        
    }, 1000);
});



$(document).ready(function(){
	setInterval(function(){
		$("#screen").load('lastTag.php')
       
    }, 1000);
});



$(document).ready(function(){
	setInterval(function(){
	 
        $("#screen2").load('displayTags.php')
 
    }, 1000);
});


$(document).ready(function(){
	setInterval(function(){
	 
        $("#screen22").load('displayTags2.php')
 
    }, 1000);
});


var d = new Date(<?php echo time() * 1000 ?>);

 function digitalClock() { 
 d.setTime(d.getTime() + 1000);
  var hrs = d.getHours();
  var mins = d.getMinutes();
  var secs = d.getSeconds(); 
  mins = (mins < 10 ? "0" : "") + mins;
   secs = (secs < 10 ? "0" : "") + secs;
    var apm = (hrs < 12) ? "am" : "pm"; 
    hrs = (hrs > 12) ? hrs - 12 : hrs;
     hrs = (hrs == 0) ? 12 : hrs; 
     var ctime = hrs + ":" + mins + ":" + secs + " " + apm;
      document.getElementById("clock").firstChild.nodeValue = ctime;
       }
       
window.onload = function()
{
 
digitalClock(); 
setInterval('digitalClock()', 1000);
    
} </script>

<!-- Configure a few settings and attach camera -->
<script>
    Webcam.set({
        width: 490,
        height: 390,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
  
    Webcam.attach( '#my_camera' );
  
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            //document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        
        
        } );
    }
</script>

<script>
    $(document).ready(function(){
                      
        $('#addForm').submit(function(e){
        e.preventDefault();
        var addform = $(this).serialize();
            //console.log(addform);
                $.ajax({
                method: 'POST',
                url: 'save_base64File.php',
                data: addform,
                dataType: 'json',
                                			 
                });
            });
            //
    });
</script>
 
<script>
function myFunction() {
    var x = document.getElementById("snackbar");
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);
}
</script>


<script>
function myFunction2() {
    var x = document.getElementById("snackbar2");
    x.className = "show2";
    setTimeout(function(){ x.className = x.className.replace("show2", ""); }, 2000);
}
</script>




<script>
function myFunction4() {
    var x = document.getElementById("snackbar4");
    x.className = "show4";
    setTimeout(function(){ x.className = x.className.replace("show4", ""); }, 2000);
}
</script>


<script>
function myFunction5() {
    var x = document.getElementById("snackbar5");
    x.className = "show5";
    setTimeout(function(){ x.className = x.className.replace("show5", ""); }, 2000);
}
</script>



<script>
function myFunction6() {
    var x = document.getElementById("snackbar6");
    x.className = "show6";
    setTimeout(function(){ x.className = x.className.replace("show6", ""); }, 2000);
}
</script>

<!-- SOUND EFFECTS -->
 
<script>
 function PopupCenterValid(){
    var sound = new Howl({
      src: ['RFID_FX/gate_access.mp3'],
      volume: 1,
    });
    sound.play()
}
</script>
 
<script>
 function PopupCenterInvalid(){
    var sound = new Howl({
      src: ['RFID_FX/buzzer.mp3'],
      volume: 1,
    });
    sound.play()
}
</script>

<!-- END SOUND EFFECTS -->
 
</html>


 
 
 


 
 

