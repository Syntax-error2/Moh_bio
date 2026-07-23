<!DOCTYPE html>
<html>

  <?php
  
   include('session.php');
   
   include('header.php');
   
   ?>
  
  <body>
  
  <?php include('menu_sidebar.php'); ?>
  

    <div class="page">

    <?php include('navbar_header.php'); ?>

    <?php
    $default_from_date = date('Y-m-01');
    $default_to_date = date('Y-m-t');

    $from_date = $_GET['from_date'] ?? $default_from_date;
    $to_date = $_GET['to_date'] ?? $default_to_date;

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date) || strtotime($from_date) === false) {
      $from_date = $default_from_date;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date) || strtotime($to_date) === false) {
      $to_date = $default_to_date;
    }

    $from_ts = strtotime($from_date . ' 00:00:00');
    $to_ts = strtotime($to_date . ' 23:59:59');

    if ($from_ts > $to_ts) {
      $temp_date = $from_date;
      $from_date = $to_date;
      $to_date = $temp_date;

      $from_ts = strtotime($from_date . ' 00:00:00');
      $to_ts = strtotime($to_date . ' 23:59:59');
    }

    $filter_badge_text = 'Showing: ' . date('F j, Y', $from_ts) . ' - ' . date('F j, Y', $to_ts);
    ?>
    
    
    <!-- Breadcrumb-->
      <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li style="color: blue"><strong style="margin-right: 4px;"><?php echo $schoolName; ?> | </strong></li>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb_home; ?>">Home</a></li>
            <li class="breadcrumb-item active">News</li>
          </ul>
        </div>
      </div>

      <style>
      .page-title-block { margin-bottom: 18px; }
      .page-title-block h2 { margin-bottom: 4px; font-weight: 700; color: #243447; }
      .page-title-block p { margin-bottom: 0; color: #6b7a88; }
      .news-filter-card { border: 1px solid #d9e2ec; border-radius: 10px; background: #f8fbff; }
      .news-filter-label { font-size: 12px; color: #627d98; margin-bottom: 4px; display: block; }
      .news-period-badge { background: #1f9d55; color: #fff; padding: 6px 10px; border-radius: 8px; font-size: 13px; }
      </style>

      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row page-title-block align-items-center">
            <div class="col-lg-8 col-md-8">
              <h2>News &amp; Announcements</h2>
              <p>Read the latest public announcements and updates</p>
            </div>
          </div>
        </div>
      </section>
      
      
      
      
      <!-- SHS Programs section Section -->
      <section class="mt-30px mb-30px">
        <div class="container-fluid">
          <div class="row mb-3">
            <div class="col-lg-12">
              <div class="news-filter-card p-3">
                <form method="GET" class="row align-items-end">
                  <div class="col-md-3 mb-2">
                    <label class="news-filter-label" for="from_date">Date From</label>
                    <input id="from_date" name="from_date" type="date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>" />
                  </div>

                  <div class="col-md-3 mb-2">
                    <label class="news-filter-label" for="to_date">Date To</label>
                    <input id="to_date" name="to_date" type="date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>" />
                  </div>

                  <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-primary btn-block">Apply Filter</button>
                  </div>

                  <div class="col-md-3 mb-2 text-md-right">
                    <span class="news-period-badge"><?php echo $filter_badge_text; ?></span>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 col-md-12">
              
              
              <!-- kinder 1     -->
              <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center">
                  <h2 class="h5 display">
                  
                  
                  <a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><strong style="font-weight: bold !important;">NEWS &amp; ANNOUNCEMENTS</strong></a>
                  
                  
                  
                  </h2><a data-toggle="collapse" data-parent="#new-updates" href="#updates-boxKinder" aria-expanded="true" aria-controls="updates-boxKinder"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="updates-boxKinder" role="tabpanel" class="collapse show">
                  <div class="card-body p-3">
                  <div class="table-responsive">
                    <table class="display" id="newsUsersTable" style="width:100%">
                     
                      <thead>
                        <tr>
                      
                          <th>Announcement Title<br /> <small>Contents</small></th>
                          <th>Announcement From<br /> <small>Date | Time posted</small></th>
                         
                        </tr>
                      </thead>
                      <tbody>
                      
                            <?php 
                            $subjK_stmt = $conn->prepare("SELECT * FROM news ORDER BY news_id DESC");
                            $subjK_stmt->execute();
                            while ($subjK_row = $subjK_stmt->fetch()) {
                            $raw_date_time = (string)($subjK_row['dateTime'] ?? '');
                            $normalized_date_time = str_replace('|', ' ', $raw_date_time);
                            $ts = strtotime($normalized_date_time);
                            $row_ts = false;

                            if ($ts !== false) {
                              $row_ts = $ts;
                            } elseif (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $raw_date_time, $m)) {
                              $row_ts = strtotime($m[3] . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($m[2], 2, '0', STR_PAD_LEFT) . ' 00:00:00');
                            }

                            if ($row_ts === false || $row_ts < $from_ts || $row_ts > $to_ts) {
                              continue;
                            }
              
                            
                            $news_id=$subjK_row['news_id'];
                            
                            ?>
           
                        <tr>
                        
                          
                          <td><?php echo "<strong>".$subjK_row['news_title']."</strong><br /><p style='word-break: break-all;'>".$subjK_row['news_contents']."</p>"; ?></td>
                           
                          <td><?php echo $subjK_row['posted_by'].' - '.$subjK_row['dateTime']; ?></td>
                           
        
                        </tr>
                       
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
        
      </section>
      
      
      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

     
    
  </body>
</html>