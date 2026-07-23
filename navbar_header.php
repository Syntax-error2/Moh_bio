      <!-- navbar-->
      
 


      <header class="header">
        <nav class="navbar">
          <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
              <div class="navbar-header"><a id="toggle-btn" href="#" class="menu-btn"><i class="icon-bars"> </i></a><a href="home.php" class="navbar-brand">
                  <div class="brand-text d-none d-md-inline-block"><strong class="text-primary">HUMAN RESOURCE MANAGEMENT SYSTEM</strong><span>&nbsp;&nbsp;&nbsp;<?php echo date("l"); ?>, <?php echo date("M".". "."d".", "."Y"); ?></span></div></a> </div>
              <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
               
              
              <?php if($session_access==='User'){ ?>
              
                <?php }elseif($session_access==='Administrator'){
                    
                    $mm= date("m"); //month
                    $dd=date("d"); //day
                    
                    $studBday_query = $conn->query("select * from personnels WHERE bdMM='$mm' AND bdDD='$dd'");
                    
                    ?>
                    
                <li title="Search student..." class="nav-item dropdown"><a id="notifications" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i class="fa fa-search"></i></strong></a>
                  <ul aria-labelledby="notifications" class="dropdown-menu">
                  
                  <li>
                
                 <form method="POST" action="list_personnel.php?dept=All">
                
                         
                           
                          <input name="searchStudent" list="search_list" placeholder="Search for personnel..." class="form-control" required="true" style="width: 300px;" />
                          
                          <datalist id="search_list">
                                    <?php
                                    
                                    $fnameList_query = $conn->query("SELECT DISTINCT personnel_id_code, lname, fname, mname FROM personnels");
                                    while($fnlq_row = $fnameList_query->fetch()){ ?>
                                    
                                    <option value="<?php echo $fnlq_row['personnel_id_code']; ?>"><?php echo $fnlq_row['personnel_id_code']; ?> | <small><?php echo $fnlq_row['lname'].', '.$fnlq_row['fname'].' '.$fnlq_row['mname']; ?></small></option>
                                    
                                    <?php } ?>
                          </datalist>
                          
                            
                             
                              <button name="search" class="btn btn-primary" style="display: none;"><i class="fa fa-search"></i></button>
                          
                        
                 </form> 
                 

                </li>
                
                  </ul>
                </li>
              
              <?php } ?>  
                
                
                <li class="nav-item dropdown"> <a id="messages" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i class="fa fa-user-circle"></i><span class="badge badge-info"><i class="fa fa-cog fa-sm"></i></span></a>
                  <ul aria-labelledby="notifications" class="dropdown-menu">
                    <li><a rel="nofollow" href="user_profile.php?cw=UserProfile&dept=<?php echo $user_dept; ?>&personnel_id=<?php echo $user_personnel_id; ?>" class="dropdown-item d-flex"> 
                        <div class="msg-profile"> <img src="img/<?php echo $sf_row['logo']; ?>" alt="..." class="img-fluid rounded-circle" style="width: 100px !important; height:  50px !important;"></div>
                        <div class="msg-body">
                          <h3 class="h5"><?php echo $user_row['fname']; ?>'s Profile</h3><span><?php echo $name; ?></span><small><?php echo $session_access; ?></small>
                        </div></a></li>
                    
                    
                    <li><a rel="nofollow" href="logout.php" class="dropdown-item all-notifications text-center"> <strong> <i class="fa fa-sign-out"></i>Logout</strong></a></li>
                  </ul>
                </li>
                 
              </ul>
            </div>
          </div>
        </nav>
      </header>