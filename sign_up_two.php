<!DOCTYPE html>
<html>

  <?php
  
  include('dbcon.php');
  include('header.php');
  
  ?>
  
  <?php

  function ensure_signup_audit_schema(PDO $conn): void {
    $conn->exec("CREATE TABLE IF NOT EXISTS account_signup_audit_logs (
      audit_id INT AUTO_INCREMENT PRIMARY KEY,
      personnel_id_code VARCHAR(100) NULL,
      fname VARCHAR(120) NULL,
      lname VARCHAR(120) NULL,
      matched_personnel_id INT NULL,
      status VARCHAR(40) NOT NULL,
      remarks VARCHAR(255) NULL,
      client_ip VARCHAR(64) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  function log_signup_audit(PDO $conn, string $personnelIdCode, string $fname, string $lname, ?int $matchedPersonnelId, string $status, string $remarks): void {
    ensure_signup_audit_schema($conn);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
    $auditStmt = $conn->prepare("INSERT INTO account_signup_audit_logs
      (personnel_id_code, fname, lname, matched_personnel_id, status, remarks, client_ip)
      VALUES (:personnel_id_code, :fname, :lname, :matched_personnel_id, :status, :remarks, :client_ip)");
    $auditStmt->execute([
      ':personnel_id_code' => $personnelIdCode,
      ':fname' => $fname,
      ':lname' => $lname,
      ':matched_personnel_id' => $matchedPersonnelId,
      ':status' => $status,
      ':remarks' => $remarks,
      ':client_ip' => $clientIp
    ]);
  }
  
  $get_fname=strtoupper(trim($_POST['fname'] ?? ''));
  $get_lname=strtoupper(trim($_POST['lname'] ?? ''));
  $get_personnel_id_code=strtoupper(trim($_POST['personnel_id_code'] ?? ''));
  
  $teacher_stmt = $conn->prepare("SELECT * FROM personnels WHERE personnel_id_code = :personnel_id_code AND fname = :fname AND lname = :lname LIMIT 1");
  $teacher_stmt->execute([
    ':personnel_id_code' => $get_personnel_id_code,
    ':fname' => $get_fname,
    ':lname' => $get_lname
  ]);
  $teacher_query = $teacher_stmt;

 if($teacher_query->rowCount()<=0)
 { ?>
    <?php log_signup_audit($conn, $get_personnel_id_code, $get_fname, $get_lname, null, 'FAILED', 'No matching personnel for provided name and Personnel ID Code'); ?>
    <script>
    window.alert('Unable to register. The data entered is invalid. Please supply valid data or contact the system administrator.');
    window.location='sign_up_one.php';
    </script>
 <?php }else{
    $teacher_row = $teacher_query->fetch();
    log_signup_audit($conn, $get_personnel_id_code, $get_fname, $get_lname, (int)$teacher_row['personnel_id'], 'SUCCESS', 'Matched personnel record for signup step 2');
 }
 
 
 
  ?>
  
  <body>
    <div class="page login-page">
      <div class="container">
        <div class="form-outer text-center d-flex align-items-center">
          <div class="form-inner">
            <div class="logo text-uppercase"><span>MUNICIPALITY OF</span> <strong class="text-primary">BINALBAGAN</strong></div>
            <p><strong>HUMAN RESOURCE MANAGEMENT SYSTEM</strong> [ ver. 1.0 ]</p>
            <p>Account Setup - Step 2 of 2</p>
            <form method="POST" action="account_signup.php" class="text-left form-validate">
       
                
              <input type="hidden" name="personnel_id" value="<?php echo $teacher_row['personnel_id']; ?>" />
              <input type="hidden" name="fname" value="<?php echo $get_fname; ?>" />
              <input type="hidden" name="lname" value="<?php echo $get_lname; ?>" />
              <input type="hidden" name="do_id" value="<?php echo $teacher_row['do_id']; ?>" />
               
              
              <div class="form-group-material">
                <input id="login-username" type="text" readonly="true" class="input-material" value="<?php echo $get_fname." ".$get_lname; ?>">
                <label for="login-username" class="label-material">Name</label>
              </div>
              
             <div class="form-group-material">
                <input id="login-username" name="email" type="email" value="<?php echo $teacher_row['email']; ?>" required data-msg="Please enter your email" class="input-material">
                <label for="login-username" class="label-material">Email</label>
              </div>
              
              
              <div class="form-group-material">
                <input id="login-username" type="text" name="username" required data-msg="Please enter your username" class="input-material">
                <label for="login-username" class="label-material">Username</label>
              </div>
              <div class="form-group-material">
                <input id="password" type="password" name="password" required data-msg="Please enter your password" class="input-material">
                <label for="password" class="label-material">Password</label>
              </div>
              <div class="form-group-material">
                <input id="confirm_password" type="password" required data-msg="Please retype your password" class="input-material">
                <label for="confirm_password" class="label-material">Retype Password</label>
                <small><span id="message"></span></small>
              </div>
              <div class="form-group text-center"><button name="stepTwoSignup" class="btn btn-primary">Register</button>
                <!-- This should be submit button but I replaced it with <a> for demo purposes-->
              </div>
            </form> 
          </div>
          <div class="copyrights text-center">
            <p>Developed by <a href="https://web.facebook.com/aqsijmel" class="external">Emiloi</a></p>
          </div>
        </div>
      </div>
    </div>
    
    <?php include('scripts_files.php'); ?>
    
  </body>
</html>