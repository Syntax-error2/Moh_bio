<?php

include('session.php');


if(isset($_POST['update_pref']))
{
    
    $file = rand(1000,100000)."-".$_FILES['file']['name'];
    
    $file_loc = $_FILES['file']['tmp_name'];
 
	$folder="img/";
	
	// make file name in lower case
	$new_file_name = strtolower($file);
	// make file name in lower case
    
    
    $final_file=str_replace(' ','-',$new_file_name);
    
    
    
    $upd_deped_id=$_POST['deped_id'];
 
    $upd_region=$_POST['region'];
    $upd_division=$_POST['division'];
    $upd_schoolName=$_POST['schoolName'];
    $upd_address=$_POST['address'];
    $upd_emailAddress=$_POST['emailAddress'];
    $upd_contactNumber=$_POST['contactNumber'];
    
 
    
        
        if($_FILES['file']['name']=="")
        {
            $pref_stmt = $conn->prepare("UPDATE institution_preferences SET zip_code = :zip_code, region = :region, division = :division, institution_name = :institution_name, address = :address, emailAddress = :emailAddress, contactNumber = :contactNumber");
         $pref_stmt->execute([
                ':zip_code' => $upd_deped_id,
              ':region' => $upd_region,
              ':division' => $upd_division,
                ':institution_name' => $upd_schoolName,
              ':address' => $upd_address,
              ':emailAddress' => $upd_emailAddress,
              ':contactNumber' => $upd_contactNumber
         ]);
  
    }else{
        
        
    if(move_uploaded_file($file_loc,$folder.$final_file)){
    $pref_logo_stmt = $conn->prepare("UPDATE institution_preferences SET zip_code = :zip_code, logo = :logo, region = :region, division = :division, institution_name = :institution_name, address = :address, emailAddress = :emailAddress, contactNumber = :contactNumber");
    $pref_logo_stmt->execute([
        ':zip_code' => $upd_deped_id,
        ':logo' => $final_file,
        ':region' => $upd_region,
        ':division' => $upd_division,
        ':institution_name' => $upd_schoolName,
        ':address' => $upd_address,
        ':emailAddress' => $upd_emailAddress,
        ':contactNumber' => $upd_contactNumber
    ]);
 
    
    }else{ ?>
        
        <script>
        window.alert("Error uploading image (Invalid format/File size too large), Please try again.");
        window.location='school_preferences.php?sfp_stat=xEdit';
        </script> 
    
    <?php } } ?>

<script> window.location='school_preferences.php?sfp_stat=xEdit'; </script>

<?php } ?>
 
 


 
