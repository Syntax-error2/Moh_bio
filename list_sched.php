<?php
include('dbcon.php');
?>

                <table class="table table-boredered table-hover">
                
                <thead>
                <tr>
                
                <th style="width: 30%;">Shift Details</th>
                <th>Shift Schedule Details</th>
                </tr>
                </thead>
                
                   
                <tr>
               
                <td>
                <p style="font-size: medium; color: black;"><?php echo $_GET['shift']; ?></p>
                <p style="color: black;"><?php echo $_GET['type']; ?></p>
                </td>

                
                <td>
                <table class="table table-bordered">
                      <thead>
                        <tr>
                        
                          
                          <th>Day</th>
                         
                          <th style="width: 100px;">IN-AM
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          [ Late ]</th>
                          
                          <th style="width: 120px;">OUT-AM</th>
                          
                          <th style="width: 100px;">IN-PM
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          [ Late ]</th>
                          
                          <th style="width: 120px;">OUT-PM</th>
                          
                          
                          <th></th>
                        </tr>
                      </thead>
                      
                      
                      <tbody>
                      
                      
                      
                      
                      <!-- MONDAY --><!-- MONDAY --><!-- MONDAY --><!-- MONDAY -->
                    
                            <?php   
                            
                            $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = 'Monday'");
                            $sched_stmt->execute([':do_id' => $_GET['do_id'] ?? '', ':shift_id' => $_GET['shift_id'] ?? '']);
                            $sched_query = $sched_stmt;
                            
                            while ($sched_row = $sched_query->fetch()) 
                            { 
                                
                                $schedule_id=$sched_row['schedule_id'];
                                
                                ?>
                            
                        <tr>
                        <td><strong>MONDAY</strong></td>
                    
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['am_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_OUT']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['pm_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_OUT']; ?>
                          </center>
                          </td>
    
                          <td style="width: 110px;">
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editTimeSched<?php echo $schedule_id; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteSched<?php echo $schedule_id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          
                          </td>
                          
                        </tr>
                        
 
                          
                        <?php include('edit_schedule_modal.php'); ?>
    
                          
                        <?php }  ?>
                        
                      <!-- END MONDAY -->  <!-- END MONDAY -->  <!-- END MONDAY -->  <!-- END MONDAY -->  
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      <!-- TUESDAY --><!-- TUESDAY --><!-- TUESDAY --><!-- TUESDAY -->
                    
                            <?php   
                            
                            $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = 'Tuesday'");
                            $sched_stmt->execute([':do_id' => $_GET['do_id'] ?? '', ':shift_id' => $_GET['shift_id'] ?? '']);
                            $sched_query = $sched_stmt;
                            
                            while ($sched_row = $sched_query->fetch()) 
                            { 
                                
                                $schedule_id=$sched_row['schedule_id'];
                                
                                ?>
                            
                        <tr>
                        <td><strong>TUESDAY</strong></td>
    
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['am_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_OUT']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['pm_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_OUT']; ?>
                          </center>
                          </td>
    
                          <td style="width: 110px;">
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editTimeSched<?php echo $schedule_id; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteSched<?php echo $schedule_id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          
                          </td>
                          
                        </tr>
                        
 
                          
                        <?php include('edit_schedule_modal.php'); ?>
                          
                          
                        <?php }  ?>
                        
                      <!-- END TUESDAY -->  <!-- END TUESDAY -->  <!-- END TUESDAY -->  <!-- END TUESDAY -->  
                      
                      
                      
                      
                      <!-- WEDNESDAY --><!-- WEDNESDAY --><!-- WEDNESDAY --><!-- WEDNESDAY -->
                    
                            <?php   
                            
                            $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = 'Wednesday'");
                            $sched_stmt->execute([':do_id' => $_GET['do_id'] ?? '', ':shift_id' => $_GET['shift_id'] ?? '']);
                            $sched_query = $sched_stmt;
                            
                            while ($sched_row = $sched_query->fetch()) 
                            { 
                                
                                $schedule_id=$sched_row['schedule_id'];
                                
                                ?>
                            
                        <tr>
                        <td><strong>WEDNESDAY</strong></td>
 
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['am_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_OUT']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['pm_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_OUT']; ?>
                          </center>
                          </td>
    
                          <td style="width: 110px;">
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editTimeSched<?php echo $schedule_id; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteSched<?php echo $schedule_id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          
                          </td>
                          
                        </tr>
                        
 
                          
                        <?php include('edit_schedule_modal.php'); ?>
                          
                          
                        <?php }  ?>
                        
                      <!-- END WEDNESDAY -->  <!-- END WEDNESDAY -->  <!-- END WEDNESDAY -->  <!-- END WEDNESDAY -->  
                      
                      
                      
                      
                      
                      <!-- THURSDAY --><!-- THURSDAY --><!-- THURSDAY --><!-- THURSDAY -->
                    
                            <?php   
                            
                            $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = 'Thursday'");
                            $sched_stmt->execute([':do_id' => $_GET['do_id'] ?? '', ':shift_id' => $_GET['shift_id'] ?? '']);
                            $sched_query = $sched_stmt;
                            
                            while ($sched_row = $sched_query->fetch()) 
                            { 
                                
                                $schedule_id=$sched_row['schedule_id'];
                                
                                ?>
                            
                        <tr>
                        <td><strong>THURSDAY</strong></td>
 
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['am_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_OUT']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['pm_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_OUT']; ?>
                          </center>
                          </td>
    
                          <td style="width: 110px;">
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editTimeSched<?php echo $schedule_id; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteSched<?php echo $schedule_id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          
                          </td>
                          
                        </tr>
                        
 
                          
                        <?php include('edit_schedule_modal.php'); ?>
                          
                          
                        <?php }  ?>
                        
                      <!-- END THURSDAY -->  <!-- END THURSDAY -->  <!-- END THURSDAY -->  <!-- END THURSDAY -->  
                      
                      
                      
                      
                      <!-- FRIDAY --><!-- FRIDAY --><!-- FRIDAY --><!-- FRIDAY -->
                    
                            <?php   
                            
                            $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = 'Friday'");
                            $sched_stmt->execute([':do_id' => $_GET['do_id'] ?? '', ':shift_id' => $_GET['shift_id'] ?? '']);
                            $sched_query = $sched_stmt;
                            
                            while ($sched_row = $sched_query->fetch()) 
                            { 
                                
                                $schedule_id=$sched_row['schedule_id'];
                                
                                ?>
                            
                        <tr>
                        <td><strong>FRIDAY</strong></td>
 
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['am_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_OUT']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['pm_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_OUT']; ?>
                          </center>
                          </td>
    
                          <td style="width: 110px;">
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editTimeSched<?php echo $schedule_id; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteSched<?php echo $schedule_id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          
                          </td>
                          
                        </tr>
                        
 
                          
                        <?php include('edit_schedule_modal.php'); ?>
                          
                          
                        <?php }  ?>
                        
                      <!-- END FRIDAY -->  <!-- END FRIDAY -->  <!-- END FRIDAY -->  <!-- END FRIDAY -->  
                      
                      
                      
                      
                      
                      <!-- SATURDAY --><!-- SATURDAY --><!-- SATURDAY --><!-- SATURDAY -->
                    
                            <?php   
                            
                            $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = 'Saturday'");
                            $sched_stmt->execute([':do_id' => $_GET['do_id'] ?? '', ':shift_id' => $_GET['shift_id'] ?? '']);
                            $sched_query = $sched_stmt;
                            
                            while ($sched_row = $sched_query->fetch()) 
                            { 
                                
                                $schedule_id=$sched_row['schedule_id'];
                                
                                ?>
                            
                        <tr>
                        <td><strong>SATURDAY</strong></td>
           
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['am_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_OUT']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['pm_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_OUT']; ?>
                          </center>
                          </td>
    
                          <td style="width: 110px;">
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editTimeSched<?php echo $schedule_id; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteSched<?php echo $schedule_id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          
                          </td>
                          
                        </tr>
                        
 
                          
                        <?php include('edit_schedule_modal.php'); ?>
                          
                          
                        <?php }  ?>
                        
                      <!-- END SATURDAY --> <!-- END SATURDAY --> <!-- END SATURDAY -->
                      
                      
                      
                      <!-- SUNDAY --><!-- SUNDAY --><!-- SUNDAY --><!-- SUNDAY -->
                    
                            <?php   
                            
                            $sched_stmt = $conn->prepare("SELECT * FROM time_schedules WHERE do_id = :do_id AND shift_id = :shift_id AND day = 'Sunday'");
                            $sched_stmt->execute([':do_id' => $_GET['do_id'] ?? '', ':shift_id' => $_GET['shift_id'] ?? '']);
                            $sched_query = $sched_stmt;
                            
                            while ($sched_row = $sched_query->fetch()) 
                            { 
                                
                                $schedule_id=$sched_row['schedule_id'];
                                
                                ?>
                            
                        <tr>
                        <td><strong>SUNDAY</strong></td>
                        
 
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['am_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['am_OUT']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_IN']; ?>
                          <hr style="margin-bottom: 2px; margin-top: 2px;" />
                          <?php echo $sched_row['pm_IN_co']; ?>
                          </center>
                          </td>
                          
                          <td>
                          <center>
                          <?php echo $sched_row['pm_OUT']; ?>
                          </center>
                          </td>
    
                          <td style="width: 110px;">
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editTimeSched<?php echo $schedule_id; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteSched<?php echo $schedule_id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                          
                          </td>
                          
                        </tr>
                        
 
                          
                        <?php include('edit_schedule_modal.php'); ?>
                          
                          
                        <?php }  ?>
                        
                      <!-- END SUNDAY --> <!-- END SUNDAY --> <!-- END SUNDAY -->
                      
                      
                      </tbody>
                      
 
                    </table>
                </td>
                </tr>            
                    
                  
                </table>