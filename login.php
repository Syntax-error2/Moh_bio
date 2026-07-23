<?php
		include('dbcon.php');
		session_start();
		$username = $_POST['username'];
		$password = $_POST['password'];
        
        
		$safe_pass=md5($password);
        $salt="a1Bz20ydqelm8m1wql";
        $final_pass=$salt.$safe_pass;
        
		/* student */
			$query = $conn->query("SELECT * FROM useraccount WHERE username='$username' AND password='$final_pass'");
			$row = $query->fetch();
			$num_row = $query->rowcount();
		if( $num_row > 0 ) { 
		  
   
        $_SESSION['useraccess']=$row['access'];
        $_SESSION['id']=$row['user_id'];
 
        
     ?>
     
     <script>
     //window.alert('Welcome! <?php //echo substr($row['fname'], 0,1).'. '.$row['lname'].' - '.$row['access']; ?>');
    window.location = '<?php echo ($row['access'] === 'User') ? 'home_user.php' : 'home.php'; ?>';
     </script>
     
     <?php
        	
     }else{ 
        
     ?>
     <script>
     window.alert('User account not found...Check username and password and try again...');
     window.location = 'index.php';
     </script>
     
     <?php } ?>
        