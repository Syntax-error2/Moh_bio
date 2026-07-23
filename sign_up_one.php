<!DOCTYPE html>
<html>

  <?php
  
  include('dbcon.php');
  include('header.php');
  
  ?>
  
  <body>
    <div class="page login-page">
      <div class="container">
        <div class="form-outer text-center d-flex align-items-center">
          <div class="form-inner">
            <div class="logo text-uppercase"><span>MUNICIPALITY OF</span> <strong class="text-primary">BINALBAGAN</strong></div>
            <p><strong>HUMAN RESOURCE MANAGEMENT SYSTEM</strong> [ ver. 1.0 ]</p>
            <p>Account Setup - Step 1 of 2</p>
            <form method="POST" action="sign_up_two.php" class="text-left form-validate">
 
              <div class="form-group-material">
                <input id="login-username" type="text" name="fname" required data-msg="Please enter your first name" class="input-material">
                <label for="login-username" class="label-material">First Name</label>
              </div>
              
              <div class="form-group-material">
                <input id="login-password" type="text" name="lname" required data-msg="Please enter your last name" class="input-material">
                <label for="login-password" class="label-material">Last Name</label>
              </div>
              
              <div class="form-group-material">
                <input id="personnel-id-code" type="text" name="personnel_id_code" required data-msg="Please enter your Personnel ID Code" class="input-material">
                <label for="personnel-id-code" class="label-material">Personnel ID Code</label>
              <a href="#" title="Get your Personnel ID Code from the HR office or system administrator..." class="forgot-pass">What is a <strong>Personnel ID Code</strong>?</a>
              </div>
              
              <div class="form-group text-center">
              <a href="index.php" class="btn btn-default" style="color: black;">Cancel</a>
              <button name="stepOneNxt" class="btn btn-primary">Next</button>
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