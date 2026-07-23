    <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <p><img width="15" height="15" src="img/<?php echo $sf_row['logo'];?>" /> <?php echo $schoolName; ?> &middot; <?php echo date("l"); ?>, <?php echo date("M".". "."d".", "."Y"); ?></p>
            </div>
            <div class="col-sm-6 text-right">
              <p>Design by <a href="https://bootstrapious.com" class="external">Bootstrapious</a></p>
              <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions and it helps me to run Bootstrapious. Thank you for understanding :)-->
            </div>
          </div>
        </div>
      </footer>
      
      <?php
      $sf_query=null;
      $conn=null;
      ?>