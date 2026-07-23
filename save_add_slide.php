<?php

include('session.php');

if(isset($_POST['updateAnnouncementImg']))
{
    
 
    $file = rand(1000,100000)."-".$_FILES['file']['name'];
    
    $file_loc = $_FILES['file']['tmp_name'];
 
	$folder="announcement_img/";
	
	// make file name in lower case
	$new_file_name = strtolower($file);
	// make file name in lower case
    
    
    $final_file=str_replace(' ','-',$new_file_name);
        
        
     
    if(move_uploaded_file($file_loc,$folder.$final_file)){
        
        $conn->query("UPDATE slides SET img='$final_file' WHERE slide_id='$_GET[slide_id]'");

?>
    <script> window.location='list_slides.php'; </script>    

    <?php }else{ ?>
        
        <script>
        window.alert("Error uploading image. Please try again.");
        window.location='updateAnnouncementImg.php?client_id=<?php echo $_GET['client_id']; ?>';
        </script> 
    
    <?php } } ?>
