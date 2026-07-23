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
        
        
        
        <?php if($session_access==='User') { ?>
        <div class="main-menu">
          <h5 class="sidenav-heading">MENU</h5>
          <ul id="side-main-menu" class="side-menu list-unstyled">
            <li><a href="list_personnel_individual_details.php?dept=<?php echo $user_dept; ?>&personnel_id=<?php echo $user_personnel_id; ?>"> <i class="icon-user"></i>Profile</a></li>
            <li><a href="home_user.php"><i class="icon-clock"></i>My Logsheet</a></li>
            <li><a href="list_news_users.php"> <i class="icon-bill"></i>News &amp; Announcements</a></li>
          </ul>
        </div>
        <?php }elseif($session_access==='Administrator'){ ?>
            
        
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
                <li><a href="log_validation_viewer.php"> <i class="fa fa-search-plus"></i>Log Validations</a></li>
              </ul>
            </li>
            
            <li><a href="#others_dd" aria-expanded="false" data-toggle="collapse"> <i class="icon-screen"></i>Other Settings </a>
              <ul id="others_dd" class="collapse list-unstyled ">
                
                <li><a href="list_client_comp.php"> <i class="icon-screen"></i>Client CPU 
                <div class="badge badge-warning"><?php echo $client_computerTotalCtr; ?></div></a></li>
                
                <li><a href="list_slides.php"> <i class="icon-picture"></i>Slides</a></li>
                
                <li><a href="csvFile_import.php"> <i class="fa fa-file-excel-o"></i>CSV Files</a></li>
                
                <li><a href="list_dbFiles_manager.php"> <i class="fa fa-database"></i>DB Files</a></li>
                
              </ul>
            </li>
            
            <li><a href="printReports.php"> <i class="icon-page"></i>Reports</a></li>
            
          </ul>
        </div>
 
        
        <!-- end 1 Sidebar Navigation Menus-->
        
        <?php } ?>
        
        
      </div>
    </nav>