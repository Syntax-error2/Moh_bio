<!DOCTYPE html>
<html>

  <?php
  
  include('dbcon.php');
  include('header.php');
  
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
              
              <div class="form-group d-flex justify-content-between mt-4" style="gap: 15px;">
                <a href="index.php" class="btn btn-outline-secondary" style="font-weight: 700; text-transform: uppercase; padding: 12px 20px; border-radius: 8px; flex: 1; font-size: 1.1rem; border-color: #ced4da; color: #495057;">Cancel</a>
                <button name="stepOneNxt" class="btn btn-primary" style="font-weight: 700; text-transform: uppercase; padding: 12px 20px; border-radius: 8px; flex: 1; font-size: 1.1rem; background-color: #33b35a; border-color: #33b35a;">Next</button>
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