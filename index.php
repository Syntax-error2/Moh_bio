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
            
            <p><strong>HUMAN RESOURCE INFORMATION SYSTEM</strong> [build 1.0.0]</p>

            <form method="POST" action="login.php" class="text-left form-validate">
              <div class="form-group-material">
                <input id="login-username" type="text" name="username" required data-msg="Please enter your username" class="input-material">
                <label for="login-username" class="label-material">Username</label>
              </div>
              <div class="form-group-material">
                <input id="login-password" type="password" name="password" required data-msg="Please enter your password" class="input-material">
                <label for="login-password" class="label-material">Password</label>
              </div>
              
              <div class="form-group d-flex justify-content-between mt-4" style="gap: 15px;">
                <a style="color: white; font-weight: 700; text-transform: uppercase; padding: 12px 20px; border-radius: 8px; flex: 1; font-size: 1.1rem; background-color: #17a2b8; border-color: #17a2b8;" href="refLastTag.php" class="btn btn-info">Log Keeper</a>
                <button id="login" style="font-weight: 700; text-transform: uppercase; padding: 12px 20px; border-radius: 8px; flex: 1; font-size: 1.1rem; background-color: #33b35a; border-color: #33b35a;" class="btn btn-primary">Login</button>
              </div>
            </form><a data-toggle="modal" data-target="#fua" href="#" class="forgot-pass">Forgot login data?</a><small>Account Setup? Click</small> <a href="sign_up_one.php" class="signup"><strong>here</strong></a>.
          </div>
          <div class="copyrights text-center">
            <p>Developed by <a href="https://www.facebook.com/people/Aqura-Information-Technology-Solutions/61592685632481/" class="external">Aqura Information Technology Solutions</a></p>
            <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
          </div>
        </div>
      </div>
    </div>
 
 
    <?php include('forgotUserAccount_modal.php'); ?>
    
    <?php include('scripts_files.php'); ?>
    
  </body>
</html>