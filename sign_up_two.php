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
  
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap');
  .login-page, .login-page *, .register-page, .register-page * {
      font-family: 'Montserrat', sans-serif !important;
  }
  .form-inner {
      border-radius: 15px !important;
  }
  .form-group-material label {
      font-weight: 500;
  }
  </style>
  <body>
    <div class="page login-page">
      <div class="container">
        <div class="form-outer text-center d-flex align-items-center" style="max-width: 500px;">
          <div class="form-inner" style="width: 100%; padding: 40px 40px;">
            
            <div class="logo text-center mb-4">
              <div class="d-flex justify-content-center align-items-center" style="gap: 15px; margin-bottom: 20px;">
                  <img src="img/moh seal.jpg" style="height: 95px; width: auto;" alt="MOH Seal">
                  <img src="img/one-hinoba-an.png" style="height: 75px; width: auto;" alt="One Hinoba-an">
              </div>
              <span style="letter-spacing: 2px; font-size: 1.1rem; color: #555; font-weight: 500;">MUNICIPALITY OF</span><br/>
              <strong class="text-primary" style="font-size: 2rem; letter-spacing: 1px;">HINOBA-AN</strong>
            </div>
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
              <div class="form-group d-flex justify-content-between mt-4" style="gap: 15px;">
                <button name="stepTwoSignup" class="btn btn-primary" style="font-weight: 700; text-transform: uppercase; padding: 12px 20px; border-radius: 8px; flex: 1; font-size: 1.1rem; background-color: #33b35a; border-color: #33b35a;">Register</button>
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