    <!-- Side Navbar -->
    <nav class="side-navbar">
      <div class="side-navbar-wrapper">
        <!-- Sidebar Header    -->
        <div class="sidenav-header d-flex align-items-center justify-content-center">
          <!-- User Info-->
          <div class="sidenav-header-inner text-center"><img src="img/<?php echo $sf_row['logo']; ?>" alt="person" class="img-fluid rounded-circle">
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
          <div class="sidenav-header-logo"><a href="<?php echo $breadcrumb_home; ?>" class="brand-small text-center"> <strong>HR</strong><strong class="text-primary">M</strong></a></div>
        </div>
        
        
        
        <?php if(!in_array('hris', $_SESSION['allowed_modules'])) { ?>
        <div class="main-menu">
          <h5 class="sidenav-heading">MENU</h5>
          <ul id="side-main-menu" class="side-menu list-unstyled">
            <li><a href="list_personnel_individual_details.php?dept=<?php echo $user_dept; ?>&personnel_id=<?php echo $user_personnel_id; ?>"> <i class="icon-user"></i>Profile</a></li>
            <li><a href="home_user.php"><i class="icon-clock"></i>My Logsheet</a></li>
            <li><a href="list_news_users.php"> <i class="icon-bill"></i>News &amp; Announcements</a></li>
          </ul>
        </div>
        <?php } else { ?>
            
        
        <!-- 1 Sidebar Navigation Menus-->
        
        <div class="main-menu">
          <h5 class="sidenav-heading">MENU</h5>
          <ul id="side-main-menu" class="side-menu list-unstyled">  
                          
            <li><a href="home.php"> <i class="icon-home"></i>Home</a></li>
            
            <li><a href="#company_profile_dd" aria-expanded="false" data-toggle="collapse" aria-controls="company_profile_dd"> <i class="fa fa-institution"></i>Municipality</a>
              <ul id="company_profile_dd" class="collapse list-unstyled ">
                <li><a href="school_preferences.php?sfp_stat=xEdit"> <i class="fa fa-info-circle"></i>Profile</a></li>
                <li><a href="institutional_calendar.php?mm=<?php echo date('m'); ?>&yyyy=<?php echo date('Y'); ?>"> <i class="fa fa-calendar"></i>Calendar</a></li>
                <li><a href="list_news.php"> <i class="fa fa-bell"></i>Announcement</a></li>
              </ul>
            </li>
            
            <li><a href="#personnels_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-user"></i>Personnel Mngt.</a>
              <ul id="personnels_dd" class="collapse list-unstyled ">
                
                <li><a href="list_personnel.php?dept=All"><i class="icon-user"></i> Personnels
                <div class="badge badge-warning"> <?php echo $perCtr_all=$perCtr_query->rowCount(); ?></div></a></li>
                
                <li><a href="list_dept.php"> <i class="fa fa-tasks"></i>Dept. / Offices
                <div class="badge badge-warning"><?php echo $do_TotalCtr; ?></div></a></li>
                
                <li><a href="list_designation.php"> <i class="fa fa-briefcase"></i>Designation 
                <div class="badge badge-warning"><?php echo $desTotalCtr; ?></div></a></li>
                
                <li><a href="list_gass.php"> <i class="icon-website"></i>Salary Grade 
                <div class="badge badge-warning"><?php echo $gassTotalCtr; ?></div></a></li>
                
                <li><a href="list_EStatus.php"> <i class="fa fa-drivers-license"></i>Appointment Status 
                <div class="badge badge-warning"><?php echo $ES_TotalCtr; ?></div></a></li>
                
                <li><a href="biometric_enrollment_list.php"> <i class="fa fa-hand-paper-o"></i>Biometric Enrollment </a></li>
                
              </ul>
            </li>
            
            <li><a href="#leave_travel_dd" aria-expanded="false" data-toggle="collapse"> <i class="fa fa-plane"></i>Leave/Travel Mngt. </a>
              <ul id="leave_travel_dd" class="collapse list-unstyled ">
                <li><a href="list_travel_order.php?cw=list_travel"> <i class="fa fa-plane"></i>Travel Bulletin</a></li>
                <li><a href="list_leave.php?cw=list_leave"> <i class="fa fa-arrow-circle-left"></i>Leave Bulletin</a></li>
              </ul>
            </li>
            
            <li><a href="#time_mngt_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-clock"></i>Time Management </a>
              <ul id="time_mngt_dd" class="collapse list-unstyled ">
                <li><a href="list_shift.php"> <i class="icon-presentation"></i>Shifts 
                <div class="badge badge-warning"><?php echo $shiftTotalCtr; ?></div></a></li>
                <li><a href="schedule_preferences.php?do_id=&shift_id=&shift=&type="> <i class="icon-clock"></i>Schedules</a></li>
                <li><a href="biometric_dtr.php" target="_blank"> <i class="fa fa-fingerprint"></i>Biometric DTR Terminal</a></li>
                <li><a href="log_validation_viewer.php"> <i class="fa fa-search-plus"></i>Log Validations</a></li>
              </ul>
            </li>
            
            <?php if(in_array('payroll', $_SESSION['allowed_modules'])) { ?>
            <li><a href="#payroll_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-bill"></i>Payroll System </a>
              <ul id="payroll_dd" class="collapse list-unstyled ">
                <li><a href="payroll/home.php"> <i class="icon-home"></i>Dashboard</a></li>
                <li><a href="payroll/list_personnel.php?dept=All"> <i class="icon-user"></i>Personnels</a></li>
                
                <li><a href="#main_payroll_templates_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-bill"></i>Payroll Templates</a>
                  <ul id="main_payroll_templates_dd" class="collapse list-unstyled" style="padding-left: 20px; font-size: 0.9em;">
                    <li><a href="payroll/list_payroll_profiles.php"> <i class="fa fa-folder-open"></i>All Templates</a></li>
                    <li><a href="payroll/list_payroll_profiles.php?type=regular"> <i class="fa fa-calendar"></i>Regular Payroll</a></li>
                    <li><a href="payroll/list_payroll_profiles.php?type=13th_month"> <i class="fa fa-gift"></i>13th Month</a></li>
                    <li><a href="payroll/list_payroll_profiles.php?type=bonus"> <i class="fa fa-star"></i>Bonus</a></li>
                    <li><a href="payroll/list_payroll_profiles.php?type=special"> <i class="fa fa-certificate"></i>Special Payroll</a></li>
                  </ul>
                </li>
                
                <li><a href="#main_payroll_history_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-clock"></i>Payroll History</a>
                  <ul id="main_payroll_history_dd" class="collapse list-unstyled" style="padding-left: 20px; font-size: 0.9em;">
                    <li><a href="payroll/list_payroll_history.php"> <i class="fa fa-list"></i>All Payroll Runs</a></li>
                    <li><a href="payroll/list_payroll_history.php?status=draft"> <i class="fa fa-pencil"></i>Draft Runs</a></li>
                    <li><a href="payroll/list_payroll_history.php?status=pending"> <i class="fa fa-clock-o"></i>Pending Approval</a></li>
                    <li><a href="payroll/list_payroll_history.php?status=completed"> <i class="fa fa-check-circle"></i>Completed Runs</a></li>
                  </ul>
                </li>
                
                <li><a href="#main_income_deductions_dd" aria-expanded="false" data-toggle="collapse"> <i class="fa fa-money"></i>Income & Deductions</a>
                  <ul id="main_income_deductions_dd" class="collapse list-unstyled" style="padding-left: 20px; font-size: 0.9em;">
                    <li><a href="payroll/list_personnel_income.php?dept=All"> <i class="fa fa-plus-circle"></i>Personnel Income</a></li>
                    <li><a href="payroll/list_personnel_deductions.php?dept=All"> <i class="fa fa-minus-circle"></i>Personnel Deductions</a></li>
                    <li><a href="payroll/income.php"> <i class="fa fa-list-alt"></i>Income Reference</a></li>
                    <li><a href="payroll/deductions.php"> <i class="fa fa-list-alt"></i>Deduction Reference</a></li>
                  </ul>
                </li>
                
                <li><a href="payroll/printReports.php"> <i class="icon-page"></i>Reports</a></li>
              </ul>
            </li>
            <?php } ?>
            
            <li><a href="#others_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-screen"></i>Other Settings </a>
              <ul id="others_dd" class="collapse list-unstyled ">
                
                <li><a href="list_client_comp.php"> <i class="icon-screen"></i>Client CPU 
                <div class="badge badge-warning"><?php echo $client_computerTotalCtr; ?></div></a></li>
                
                <li><a href="list_slides.php"> <i class="icon-picture"></i>Slides</a></li>
                
                <li><a href="csvFile_import.php"> <i class="fa fa-file-excel-o"></i>CSV Files</a></li>
                <li><a href="list_dbFiles_manager.php"> <i class="fa fa-database"></i>DB Files</a></li>
                <?php if ($session_access === 'Admin') { ?>
                <li><a href="manage_users.php"> <i class="fa fa-users"></i>Manage Users</a></li>
                <li><a href="audit_logs.php"> <i class="fa fa-history"></i>Audit Trail</a></li>
                <?php } ?>
              </ul>
            </li>
            
            <li><a href="printReports.php"> <i class="icon-page"></i>Reports</a></li>
            
          </ul>
        </div>
 
        
        <!-- end 1 Sidebar Navigation Menus-->
        
        <?php } ?>
        
      </div>
    </nav>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var currentPath = window.location.pathname;
        if (currentPath.endsWith('/')) currentPath += 'index.php';

        var links = document.querySelectorAll('#side-main-menu a');
        
        links.forEach(function(link) {
            var href = link.getAttribute('href');
            if (!href || href.startsWith('#')) return;
            
            try {
                var linkUrl = new URL(link.href); // link.href gets the absolute resolved URL
                if (linkUrl.pathname === currentPath) {
                    link.parentElement.classList.add('active');
                    
                    var dropdown = link.closest('ul.collapse');
                    if (dropdown) {
                        dropdown.classList.add('show');
                        var parentLink = dropdown.previousElementSibling;
                        if (parentLink) {
                            parentLink.setAttribute('aria-expanded', 'true');
                            parentLink.parentElement.classList.add('active');
                        }
                    }
                }
            } catch(e) {}
        });
    });
    </script>