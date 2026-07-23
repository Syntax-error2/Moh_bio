<?php include('dbcon.php'); ?>

<html>
<head>

<title><?php echo $sf_row['institution_name'] ?? 'Institution Name'; ?></title>

<meta name="description" content="Human Resource Management System">

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
 
$chk_comp_ip_query = $conn->prepare('SELECT client_id FROM client_computer WHERE ipAddress = :ipAddress');
$chk_comp_ip_query->execute(['ipAddress' => $machine_ip7]);

if($chk_comp_ip_query->rowCount()>0){
    
$conn->query("UPDATE client_computer SET display_time=0, announcement_img=0, RFID_tag='' WHERE ipAddress='$machine_ip7'");

$conn->query("DELETE FROM personnel_logs WHERE client_ip='$machine_ip7' AND logFlow=''");

}

?>


<?php include('refLastTagCSS.php'); ?>

</head>

<body>


<div class="kiosk-shell">
    <div id="rcorners4" class="kiosk-card kiosk-header-card">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: middle; width: 5%; text-align:center;">
                    <div id="my_camera"></div>
                    <input name="submitSnapshot" type="button" value="Take Snapshot" onclick="take_snapshot()" style="display: none;" />
                </td>
                <td>
                    <table align="center" style="height: 100%;">
                        <tr>
                            <td rowspan="3" align="center">
                                <a href="index.php"><img src="img/<?php echo $sf_row['logo']; ?>" width="75" height="75" style="border: solid 1px #fff; border-radius: 50px;" /></a>
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td align="center">
                                <strong style="font-size: 30px; color: #1a1a1a; font-weight: 700;"><?php echo $sf_row['institution_name'] ?? 'Institution Name'; ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td align="center" style="font-size: 18px; color: #1a1a1a;"><?php echo ($sf_row['division'] ?? '').' District'.' - '.($sf_row['address'] ?? '').' '.($sf_row['zip_code'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td align="center" style="font-size: 14px; color: #1a1a1a;">
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

    <div class="kiosk-main-grid">
        <div class="kiosk-left-col">

            <div class="kiosk-card kiosk-activity-card">
                <div id="screenDTimer"></div>
                <div id="screen"></div>

                <form id="addForm" style="height: 100%; margin: 0;">
                    <input type="hidden" name="image" class="image-tag" />
                    <div id="screen2"></div>
                </form>
            </div>

            <?php if($chk_comp_ip_query->rowCount()>0){ ?>
            <div id="rcorners5" class="kiosk-card">
                <div class="input-group barcode-log-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text barcode-log-icon"><i class="fa fa-barcode"></i></span>
                    </div>

                    <input id="profile_id" name="profile_id" required="" autocomplete="off" class="form-control barcode-log-input" placeholder="Enter ID code and press Enter Key to log..." />

                    <div class="input-group-append">
                        <button id="search_btn" type="button" onclick="checkLRNStatus();" class="btn barcode-log-btn"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <?php }else{ ?>
            <div id="rcorners5" class="kiosk-card" style="justify-content: center;">
                <h5 style="font-weight: 400; color: gray; margin: 0;">This computer is not registered as Log Client</h5>
            </div>
            <?php } ?>

        </div>

        <div class="kiosk-right-col">
            <div id="rcorners3" class="kiosk-card">
                <div id="clock"></div>
                <div id="screenRefDate"></div>
            </div>

            <div class="kiosk-card kiosk-announcement-card">
                <h1>News & Announcements</h1>
                <div id="screenAnnouncements"></div>
            </div>
        </div>

    </div>
</div>

 

<!-- INVALID SNACK BARS -->
<div id="snackbar">ID CODE NOT FOUND</div>
<div id="snackbar2">CAN'T LOG AT THIS TIME</div>
<div id="snackbar4">SCHEDULE NOT FOUND</div>
<div id="snackbar5">NO AM OUT LOG</div>
<div id="snackbar6">PROCESSING...</div>
<!-- END INVALID SNACK BARS -->

<!--
<marquee>
<p style="margin-top: 2px; margin-bottom: 0px;">
<?php 
$news_query = $conn->query("SELECT news_title, news_contents FROM news WHERE ipAddress='$machine_ip7'");
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

-->

<?php $conn=null; ?>

</body>



<?php include('scripts_files.php'); ?>

<script src="js/1.0.25_webcam.min.js"></script>





<script>

    function changeAnnouncementSlide(direction){
        $.ajax({
            type: 'POST',
            url: 'set_announcement_slide.php',
            dataType: 'json',
            data: { direction: direction },
            success: function(){
                $("#screen2").load('displayTags.php');
            }
        });
    }

    function setAnnouncementSlide(index){
        $.ajax({
            type: 'POST',
            url: 'set_announcement_slide.php',
            dataType: 'json',
            data: { index: index },
            success: function(){
                $("#screen2").load('displayTags.php');
            }
        });
    }

    function checkLRNStatus(){
        
    var profile_id=$("#profile_id").val();// value in field profile_id
    
    $.ajax({
        type:'POST',
            url:'check_id_code.php',// put your real file name 
            data:{profile_id: profile_id},
            success:function(){
                document.getElementById('profile_id').value = '';
                document.getElementById('search_btn').style.display = 'inline-block';
                //document.getElementById('search_load').style.display = 'none';
            }
     });
    }
    
    
    //Press Enter Ker to trigger function checkLRNStatus()
    var input = document.getElementById("profile_id");
    input.addEventListener("keyup", function(event) {
      if (event.keyCode === 13) {
       event.preventDefault();
       document.getElementById("search_btn").click();
      }
    });
    
    
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
    function loadAnnouncements(){
        $("#screenAnnouncements").load('displayAnnouncements.php');
    }

    loadAnnouncements();
    setInterval(loadAnnouncements, 15000);
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
      var clockEl = document.getElementById("clock");
      if (clockEl) {
          clockEl.textContent = ctime;
      }
       }
       
window.onload = function()
{
 
digitalClock(); 
setInterval('digitalClock()', 1000);
    
} </script>

<!-- Configure a few settings and attach camera -->
<script>
    Webcam.set({
        width: 90,
        height: 90,
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
//can't log at this time
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










