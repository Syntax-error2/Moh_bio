<?php

include('dbcon.php');
 
if(isset($_POST['stepTwoSignup']))
{
    
    $fname=$_POST['fname'];
    $lname=$_POST['lname'];
    $email=$_POST['email'];
   
    $username=$_POST['username'];
    $password=$_POST['password'];
    
    $safe_pass=md5($password);
    $salt="a1Bz20ydqelm8m1wql";
    $final_pass=$salt.$safe_pass;
        
    $personnel_id=$_POST['personnel_id'];
    $do_id=$_POST['do_id'];
    
    
    $perDataCHK_stmt = $conn->prepare("SELECT * FROM useraccount WHERE personnel_id = :personnel_id OR (fname = :fname AND lname = :lname) OR (username = :username AND password = :final_pass)");
    $perDataCHK_stmt->execute([
        ':personnel_id' => $personnel_id,
        ':fname' => $fname,
        ':lname' => $lname,
        ':username' => $username,
        ':final_pass' => $final_pass
    ]);
    if($perDataCHK_stmt->rowCount()>0){
        
         ?>
 
        <script>
        window.alert('User already exist...');
        window.location='index.php'; 
        </script>    
        
        <?php

    }else{ 
        
        
    $insert_stmt = $conn->prepare("INSERT INTO useraccount(school_id, personnel_id, fname, lname, email, username, password, access, do_id) VALUES(:school_id, :personnel_id, :fname, :lname, :email, :username, :password, :access, :do_id)");
    $insert_stmt->execute([
        ':school_id' => 1,
        ':personnel_id' => $personnel_id,
        ':fname' => $fname,
        ':lname' => $lname,
        ':email' => $email,
        ':username' => $username,
        ':password' => $final_pass,
        ':access' => 'User',
        ':do_id' => $do_id
    ]);
    
    
    ?>
    
        <script>
        window.alert('Success! You can login your account.');
        window.location='index.php';
        </script>
    
    
    
    <?php   } } ?>
    
<?php


    