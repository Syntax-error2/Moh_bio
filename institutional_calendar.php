<!DOCTYPE html>
<html>

  <?php
  
  include('session.php');
  include('header.php');
  
  ?>

  <?php
  $month_names = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
  ];

  $mm = isset($_GET['mm']) ? str_pad((string)(int)$_GET['mm'], 2, '0', STR_PAD_LEFT) : date('m');
  if (!isset($month_names[$mm])) {
    $mm = date('m');
  }

  $selected_yyyy = isset($_GET['yyyy']) ? (int)$_GET['yyyy'] : (int)date('Y');
  if ($selected_yyyy < 2019 || $selected_yyyy > 2035) {
    $selected_yyyy = (int)date('Y');
  }

  if (isset($_POST['selectYear'])) {
    $posted_year = (int)($_POST['selected_yyyy'] ?? $selected_yyyy);
    if ($posted_year < 2019 || $posted_year > 2035) {
      $posted_year = $selected_yyyy;
    }
    ?>
    <script>
    window.location='institutional_calendar.php?mm=<?php echo $mm; ?>&yyyy=<?php echo $posted_year; ?>'
    </script>
    <?php
    exit;
  }

  $mmWords = $month_names[$mm];
  ?>
        
        
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>
    
    
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item active">Institutional Calendar</li>
          </ul>
        </div>
      </div>

      <style>
      .calendar-title-block { margin-bottom: 18px; }
      .calendar-title-block h2 { margin-bottom: 4px; font-weight: 700; color: #243447; }
      .calendar-title-block p { margin-bottom: 0; color: #6b7a88; }
      .calendar-filter-card { border: 1px solid #d9e2ec; border-radius: 10px; background: #f8fbff; }
      .calendar-month-nav { display: flex; flex-wrap: wrap; gap: 8px; }
      .calendar-month-nav .month-link { border: 1px solid #d5dbe5; border-radius: 999px; padding: 6px 12px; color: #3a4b5c; background: #ffffff; text-decoration: none; }
      .calendar-month-nav .month-link.active { background: #1f9d55; border-color: #1f9d55; color: #ffffff; font-weight: 700; }
      </style>
      
      
      
      
      <!-- Calendar section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-8 col-md-8">
              <div class="calendar-title-block">
                <h2>Institutional Calendar</h2>
                <p>Manage monthly activities, holidays, and work-day display settings</p>
              </div>
            </div>

            <div class="col-lg-4 col-md-4 text-right">
              <a style="color: white !important;" data-toggle="modal" data-target="#addSubjKinder" href="#addSubjKinder" class="btn btn-primary"><i class="fa fa-plus"></i> Add Activity</a>
            </div>

            <div class="col-lg-12 col-md-12 mb-3">
              <div class="calendar-filter-card p-3">
                <form method="POST" class="form-inline">
                  <label class="mr-2 mb-0">Year</label>
                  <select name="selected_yyyy" class="form-control mr-2">
                    <?php for ($y = 2019; $y <= 2035; $y++) { ?>
                      <option value="<?php echo $y; ?>" <?php if ((int)$selected_yyyy === $y) { echo 'selected'; } ?>><?php echo $y; ?></option>
                    <?php } ?>
                  </select>
                  <button name="selectYear" class="btn btn-success">Browse Calendar Year</button>
                </form>
              </div>
            </div>

            <div class="col-lg-12 col-md-12 mb-3">
              <div class="calendar-month-nav">
                <?php foreach ($month_names as $month_num => $month_title) { ?>
                  <a href="institutional_calendar.php?mm=<?php echo $month_num; ?>&yyyy=<?php echo (int)$selected_yyyy; ?>" class="month-link <?php if ($mm === $month_num) { echo 'active'; } ?>"><?php echo substr($month_title, 0, 3); ?></a>
                <?php } ?>
              </div>
            </div>
                
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="font-weight: bolder;">Activities for the Month of <?php echo $mmWords; ?> - <?php echo (int)$selected_yyyy; ?></strong></a>
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                  
                    <div class="col-lg-12">
                    <div class="table-responsive" style="margin-top: 12px;">
                    <table id="" class="display" style="width:100%">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Title</th>
                          <th>Description</th>
                          <th>Type</th>
                          <th>Add to work days</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php
                            $subjK_ctr=0;
                 
                                $calendar_stmt = $conn->prepare("SELECT * FROM activity_calendar WHERE actMM = :actMM AND actYYYY = :actYYYY ORDER BY actDD, activity_id ASC");
                                $calendar_stmt->execute([':actMM' => $mm, ':actYYYY' => $selected_yyyy]);
                                while ($cal_row = $calendar_stmt->fetch()) 
                                { 
                                    
                                $activity_id=$cal_row['activity_id'];
                                ?>
           
                        <tr>
                    
                          <td><?php echo $cal_row['completeDate']; ?></td>
                          <td><?php echo $cal_row['event_title']; ?></td>
                          <td><?php echo $cal_row['event_description']; ?></td>
                          <td><?php echo $cal_row['act_type']; ?></td>
                          
                          <td>
                          <?php if($cal_row['status']==='Display to DTR') { echo 'YES'; }else{ echo 'NO'; } ?></td>
                           
                          <td>
                          
                          <a style="color: white !important;" data-toggle="modal" data-target="#editActivity<?php echo $activity_id; ?>" href="#editTeacher<?php echo $activity_id; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                          <a style="color: white !important;" data-toggle="modal" data-target="#deleteActivity<?php echo $activity_id; ?>" href="#deleteTeacher<?php echo $activity_id; ?>" class="btn btn-danger"><i class="fa fa-times"></i></a>
                          
                          </td>
                        </tr>
                 
                <!-- delete activity Modal -->
                  <div id="deleteActivity<?php echo $activity_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                      <form action="save_add_activity.php?mm=<?php echo $mm; ?>&yyyy=<?php echo (int)$selected_yyyy; ?>" method="POST">
                      <input name="activity_id" value="<?php echo $activity_id; ?>" type="hidden" />
                      
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Delete Activity</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                        
                        <div class="modal-body">
                           
                        <h4>Are you sure you want to delete activity:<br /><br /><?php echo $cal_row['event_title'].'?'; ?></h4>
                        <small><?php echo $cal_row['event_description'].' [ '.$cal_row['completeDate'].' ]'; ?></small> 
                        </div>
                        
                        <div class="modal-footer">
                          <a style="color: white;" href="" data-dismiss="modal" class="btn btn-primary">No</a>
                          <button name="deleteActivity" type="submit" class="btn btn-danger">Yes</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end delete activity Modal -->
                  
                        
            
            <!-- edit activity Modal -->
                  <div id="editActivity<?php echo $activity_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                      <div class="modal-content">
                        
                        <div class="modal-header">
                          <h5 id="exampleModalLabel" class="modal-title">Edit Activity</h5>
                          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" class="fa fa-times"></span></button>
                        </div>
                       
                        
                        <form action="save_add_activity.php?mm=<?php echo $mm; ?>&yyyy=<?php echo (int)$selected_yyyy; ?>&activity_id=<?php echo $activity_id; ?>" method="POST">
                        <div class="modal-body">
                        
                        
                            <div class="form-group row">
                              <div class="col-sm-12">
                                <input value="<?php echo $cal_row['event_title']; ?>" name="event_title" type="text" class="form-control">
                                 <small>Activity / Event Title</small>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                              <div class="col-sm-12">
                                <input name="event_description" value="<?php echo $cal_row['event_description']; ?>" type="text" class="form-control"> 
                                 <small>Activity / Event Description</small>
                              </div>
                            </div>
             
                            <div class="form-group row">
                              <div class="col-sm-12">
                                <input value="<?php echo $mmWords; ?>" class="form-control" readonly="true">
                              </div>
                            </div>
                            
                            <div class="form-group row">
                   
                              <div class="col-sm-12">
                                <div class="row">
                                   
                                  <div class="col-md-6">
                                  
                                    <input type="hidden" name="actMM" value="<?php echo $mm; ?>" class="form-control">
                        
                                    <select name="actDD" class="form-control">
                                    <option><?php echo substr($cal_row['completeDate'], 3,2); ?></option>
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    <option>13</option>
                                    <option>14</option>
                                    <option>15</option>
                                    <option>16</option>
                                    <option>17</option>
                                    <option>18</option>
                                    <option>19</option>
                                    <option>20</option>
                                    <option>21</option>
                                    <option>22</option>
                                    <option>23</option>
                                    <option>24</option>
                                    <option>25</option>
                                    <option>26</option>
                                    <option>27</option>
                                    <option>28</option>
                                    <option>29</option>
                                    <option>30</option>
                                    <option>31</option>
                                    </select>
                                    <small class="form-text">Day</small>
                                  </div>
                                  <div class="col-md-6">
                                     
                                    <input value="<?php echo substr($cal_row['completeDate'], 6,4); ?>" name="actYYYY" type="text" class="form-control">
                                    <small class="form-text">Year</small>
                                    
                                  </div>
                                  
                                </div>
                              </div>
                            </div>
                            
                            <div class="form-group row">
                   
                              <div class="col-sm-12">
                                <div class="row">
                                   
                                  <div class="col-md-12">
                                    <select name="act_type" class="form-control">
                                    <option><?php echo $cal_row['act_type']; ?></option>
                                    <option>-</option>
                                    <option>Regular Holiday</option>
                                    <option>Special Holiday</option>
                                    <option>Regular Working Holiday</option>
                                    <option>Special Working Holiday</option>
                                    <option>City/Municipal Activity</option>
                                    <option>Work Suspension</option>
                                    </select>
                                    <small class="form-text">Type</small>
                                  </div>
                                  
                                  <div class="col-md-12">
                                    <select name="status" class="form-control">
                                    <option><?php echo $cal_row['status']; ?></option>
                                    <option>-</option>
                                    <option>Display to DTR</option>
                                    </select>
                                    <small class="form-text">Status</small>
                                  </div>
                         
                                  
                                </div>
                              </div>
                            </div>
                            
                            
                             
                        </div>
                        
                        <div class="modal-footer">
                          <a href="" data-dismiss="modal" class="btn btn-secondary" style="color: white;">Cancel</a>
                          <button name="editActivity" type="submit" class="btn btn-primary">Update</button>
                        </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <!-- end edit activity Modal -->
                  
                  
                  
                      
                            <?php } ?>
                       
                      </tbody>
                    </table>
                    </div>
                    </div>
                   
                </div>
              </div>
              <!-- kinder End-->
             
            </div>
            
          </div>
        </div>
        
        <?php include('add_activity_modal.php'); ?>
                  
      </section> 
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

 
    
  </body>
</html>