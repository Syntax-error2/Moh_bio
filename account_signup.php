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

if(isset($_POST['sendFUA']))
{

        function randomcode() {
        $var = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $code = '';
        while ($i <= 9) {
        $num = rand() % 33;
        $tmp = substr($var, $num, 1);
        $code = $code . $tmp;
        $i++;
        }
        return $code;
        }
         
    $conf_code=randomcode();
        
    $mobileNumber=$_POST['mobileNumber'];
    $access=$_POST['access'];
    
    $staff_stmt = $conn->prepare("SELECT * FROM staff WHERE mobileNumber = :mobileNumber ORDER BY lname, fname ASC");
    $staff_stmt->execute([':mobileNumber' => $mobileNumber]);
    $staff_query = $staff_stmt;
    
    
    if($staff_query->rowCount()>0){
    $staff_row = $staff_query->fetch();
    
    $chk_user_stmt = $conn->prepare("SELECT * FROM useraccount WHERE staff_id = :staff_id AND access = :access");
    $chk_user_stmt->execute([
        ':staff_id' => $staff_row['staff_id'],
        ':access' => $access
    ]);
    $chk_user_query = $chk_user_stmt;
    if($chk_user_query->rowCount()>0){
        
    
    
    $chk_user_row = $chk_user_query->fetch();
    
    
                                if($staff_row['extension']=="")
                                {
                                    if($staff_row['suffix']=="-")
                                    {
                                        
                                    $classAdviser=$staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'];
                                    
                                    }else{
                                        
                                    $classAdviser=$staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'].", ".$staff_row['extension'];
                                    
                                    }
                                
                                
                              
                                }else{
                                    
                                    
                                    if($staff_row['suffix']=="-")
                                    {
                                        
                                    $classAdviser=$staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname'].", ".$staff_row['extension'];
                                    
                                    }else{
                                        
                                    $classAdviser=$staff_row['fname']." ".substr($staff_row['mname'], 0,1).". ".$staff_row['lname']." ".$staff_row['suffix'].", ".$staff_row['extension'];
                                    
                                    }
                                     
                                
                                }
                                
    $messageText='STC BAUAN '."\r\r".'Good day! '.$classAdviser.', please take note your RAS login data below.'."\r\r".'Username: '.$chk_user_row['username']."\r\r".'Password: '.$chk_user_row['password']."\r\r".'Happy to serve you!'."\r\r".'Regards,'."\r".'RAS Account Helper'."\r\r".'Please do not reply.'."\r".'Ref: RASAH'.substr($conf_code, 0,5);
    
    //save to sms server =x=x=x=x=x=x=x=x=x=x=x

    $msg_stmt = $conn->prepare("INSERT INTO messageout(MessageTo, MessageText) VALUES(:MessageTo, :MessageText)");
    $msg_stmt->execute([
        ':MessageTo' => $mobileNumber,
        ':MessageText' => $messageText
    ]);
    
    ?>
    
    <script>
    
    window.alert('Request sent! Please wait for the SMS from RAS Account Helper.');
    window.location='index.php';
    
    </script>
    
    <?php
    
    }else{
    ?>
    
    <script>
    
    window.alert('User access not matched... Please try again.');
    window.location='index.php';
    
    </script>
    
    <?php
    }
    
    
    }else{
?>

<script>

window.alert('Mobile Number not found/Incorrect format... Please try again with registered & valid format mobile number.');
window.location='index.php';

</script>

<?php
    }

}
    