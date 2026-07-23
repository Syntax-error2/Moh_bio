    <!-- Side Navbar -->
    <nav class="side-navbar">
      <div class="side-navbar-wrapper">
        <!-- Sidebar Header    -->
        <div class="sidenav-header d-flex align-items-center justify-content-center">
          <!-- User Info-->
          <div class="sidenav-header-inner text-center"><img src="../img/<?php echo $sf_row['logo']; ?>" alt="person" class="img-fluid rounded-circle">
            <h2 class="h5"><?php echo $name; ?></h2>
            
            <?php
            
            $DOHead_query = $conn->query("SELECT * FROM dept_offices WHERE officeHead_id='$user_personnel_id'");
            if($DOHead_query->rowCount()>0){
            
            $doh_row=$DOHead_query->fetch(); ?>
            
            <span><?php echo $session_access; ?><br /><?php echo $doh_row['dept_office_name']; ?> Head</span>
            
            <?php }else{ ?>
            
            <span><?php echo $session_access; ?></span>
            
            <?php } ?>
            
            
          </div>
          <!-- Small Brand information, appears on minimized sidebar-->
          <div class="sidenav-header-logo"><a href="home.php" class="brand-small text-center"> <strong>RD</strong><strong class="text-primary">S</strong></a></div>
        </div>
        
        
        
        <?php if($session_access == 'Administrator'){ ?>
          
        <!-- 1 Sidebar Navigation Menus-->
        
        <div class="main-menu">
          <h5 class="sidenav-heading">MENU</h5>
          <ul id="side-main-menu" class="side-menu list-unstyled">  
                          
            <li><a href="home.php"> <i class="icon-home"></i>Home</a></li>
            
            <li>
              <a href="list_personnel.php?dept=All"> <i class="icon-user"></i>Personnels
                <div class="badge badge-warning"> <?php echo $perCtr_all=$perCtr_query->rowCount(); ?></div>
              </a>
            </li>
            
            <!-- Payroll Templates Section -->
            <li><a href="#payroll_templates_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-bill"></i>Payroll Templates</a>
              <ul id="payroll_templates_dd" class="collapse list-unstyled ">
                <li><a href="list_payroll_profiles.php"> <i class="fa fa-folder-open"></i>All Templates</a></li>
                <li><a href="list_payroll_profiles.php?type=regular"> <i class="fa fa-calendar"></i>Regular Payroll</a></li>
                <li><a href="list_payroll_profiles.php?type=13th_month"> <i class="fa fa-gift"></i>13th Month</a></li>
                <li><a href="list_payroll_profiles.php?type=bonus"> <i class="fa fa-star"></i>Bonus</a></li>
                <li><a href="list_payroll_profiles.php?type=special"> <i class="fa fa-certificate"></i>Special Payroll</a></li>
              </ul>
            </li>
            
            <!-- Payroll History & Runs -->
            <li><a href="#payroll_history_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-clock"></i>Payroll History</a>
              <ul id="payroll_history_dd" class="collapse list-unstyled ">
                <li><a href="list_payroll_history.php"> <i class="fa fa-list"></i>All Payroll Runs</a></li>
                <li><a href="list_payroll_history.php?status=draft"> <i class="fa fa-pencil"></i>Draft Runs</a></li>
                <li><a href="list_payroll_history.php?status=pending"> <i class="fa fa-clock-o"></i>Pending Approval</a></li>
                <li><a href="list_payroll_history.php?status=completed"> <i class="fa fa-check-circle"></i>Completed Runs</a></li>
              </ul>
            </li>
            
            <!-- Income & Deductions Management -->
            <li><a href="#income_deductions_dd" aria-expanded="false" data-toggle="collapse"> <i class="fa fa-money"></i>Income & Deductions</a>
              <ul id="income_deductions_dd" class="collapse list-unstyled ">
                <li><a href="list_personnel.php?dept=All"> <i class="fa fa-plus-circle"></i>Personnel Income</a></li>
                <li><a href="list_personnel.php?dept=All"> <i class="fa fa-minus-circle"></i>Personnel Deductions</a></li>
                <li class="dropdown-divider" style="border-top: 1px solid #374957; margin: 8px 15px;"></li>
                <li><a href="income.php"> <i class="fa fa-list-alt"></i>Income Reference</a></li>
                <li><a href="deductions.php"> <i class="fa fa-list-alt"></i>Deduction Reference</a></li>
              </ul>
            </li>
            
            <!-- Reports -->
            <li><a href="printReports.php"> <i class="icon-page"></i>Reports</a></li>
            
          </ul>
        </div>
 
        
        <!-- end 1 Sidebar Navigation Menus-->
        
        <?php } ?>
        
        
      </div>
    </nav>