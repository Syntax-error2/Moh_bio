<!DOCTYPE html>
<html>
  <?php
   include('session.php');
   include('header.php');
   require_once('dtr_sync_lib.php');

   // 1. Fetch live unexported counts
   $bioUnexportedStmt = $conn->query("SELECT COUNT(*) FROM bio_dtr WHERE is_exported = 0 OR is_exported IS NULL");
   $bioUnexportedCount = (int)$bioUnexportedStmt->fetchColumn();

   $logsUnexportedStmt = $conn->query("SELECT COUNT(*) FROM personnel_logs WHERE is_exported = 0 OR is_exported IS NULL");
   $logsUnexportedCount = (int)$logsUnexportedStmt->fetchColumn();

   // 2. Date span of unexported logs
   $dateSpanStmt = $conn->query("SELECT MIN(log_date) AS min_date, MAX(log_date) AS max_date FROM bio_dtr WHERE is_exported = 0 OR is_exported IS NULL");
   $dateSpan = $dateSpanStmt->fetch(PDO::FETCH_ASSOC);
   $unexportedDateRange = "None";
   if (!empty($dateSpan['min_date']) && !empty($dateSpan['max_date'])) {
       $unexportedDateRange = date('m/d/Y', strtotime($dateSpan['min_date'])) . ' - ' . date('m/d/Y', strtotime($dateSpan['max_date']));
   }

   // 3. Scan files in the dedicated spare folder
   $spareFiles = listSpareFolderFiles();
   $spareFilesCount = count($spareFiles);

   // 4. Live Windows Task Scheduler status for 15th & 30th Auto-Export
   $is15thActive = false;
   $is30thActive = false;
   $out15 = [];
   @exec('schtasks /query /tn "MOB_BIO_DTR_Export_15th" /fo CSV /nh 2>nul', $out15);
   if (!empty($out15[0]) && (stripos($out15[0], 'Ready') !== false || stripos($out15[0], 'Running') !== false)) {
       $is15thActive = true;
   }
   $out30 = [];
   @exec('schtasks /query /tn "MOB_BIO_DTR_Export_30th" /fo CSV /nh 2>nul', $out30);
   if (!empty($out30[0]) && (stripos($out30[0], 'Ready') !== false || stripos($out30[0], 'Running') !== false)) {
       $is30thActive = true;
   }
   $autoExportStatus = ($is15thActive && $is30thActive) ? 'ACTIVE' : (($is15thActive || $is30thActive) ? 'PARTIAL' : 'OFF');

   $flash = null;
   if (isset($_SESSION['dtr_sync_flash'])) {
       $flash = $_SESSION['dtr_sync_flash'];
       unset($_SESSION['dtr_sync_flash']);
   }
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
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="log_validation_viewer.php">Time Management</a></li>
            <li class="breadcrumb-item active">DTR Export &amp; Import Manager</li>
          </ul>
        </div>
      </div>
      
      <section class="statistics">
         <div class="container-fluid">
          <div class="row d-flex">
          
          <legend style="margin-top: 24px; margin-bottom: 24px; margin-left: 12px;">DTR LOGS EXPORT &amp; IMPORT (INFIRMARY &rarr; NEW SERVER SYNC)</legend>

          <?php if ($flash): ?>
            <div class="col-lg-12">
              <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert" style="margin-bottom: 20px;">
                <strong><i class="fa fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'info-circle'; ?>"></i> <?php echo htmlspecialchars($flash['title'] ?? 'Notice:'); ?></strong>
                <?php if (!empty($flash['message'])): ?>
                  <div><?php echo htmlspecialchars($flash['message']); ?></div>
                <?php endif; ?>
                <?php if (!empty($flash['details'])): ?>
                  <div style="margin-top: 8px; font-size: 13px;">
                    <div><strong>Target:</strong> <?php echo htmlspecialchars($flash['details']['file_name'] ?? ''); ?> | <strong>Exported:</strong> <?php echo htmlspecialchars($flash['details']['exported_at'] ?? ''); ?></div>
                    <div style="margin-top: 4px;">
                      <?php if (isset($flash['details']['logs_inserted'])): ?>
                        <span class="badge badge-success">+<?php echo $flash['details']['logs_inserted']; ?> Form 48 Punches Inserted</span>
                      <?php endif; ?>
                      <?php if (isset($flash['details']['bio_inserted'])): ?>
                        <span class="badge badge-info">+<?php echo $flash['details']['bio_inserted']; ?> Biometric Sessions Added</span>
                      <?php endif; ?>
                      <?php if (!empty($flash['details']['bio_updated'])): ?>
                        <span class="badge badge-primary"><?php echo $flash['details']['bio_updated']; ?> Afternoon Clock-Outs Merged</span>
                      <?php endif; ?>
                      <?php if (isset($flash['details']['logs_skipped'])): ?>
                        <span class="badge badge-secondary"><?php echo $flash['details']['logs_skipped']; ?> Duplicate Punches Skipped</span>
                      <?php endif; ?>
                      <?php if (isset($flash['details']['bio_skipped'])): ?>
                        <span class="badge badge-secondary"><?php echo $flash['details']['bio_skipped']; ?> Duplicate Bio Sessions Skipped</span>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endif; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          <?php endif; ?>

            <!-- LEFT CARD: EXPORT -->
            <div class="col-lg-6" style="margin-bottom: 16px;">
              <div class="card user-activity" style="border-bottom: 2px solid green; border-top: 1px solid green; border-left: 1px solid green; border-right: 1px solid green;">
                <h2 class="display h4"><i class="fa fa-arrow-up"></i> Export .DTR File (Infirmary Server)</h2>
                <div class="page-statistics-left">
                <form class="form-horizontal" method="POST">
                  
                  <div class="form-group">
                    <label class="control-label" style="font-weight: bold;">Export Filter Preset:</label>
                    <select name="filter_type" id="filter_type" class="form-control" onchange="updateFilterUI()">
                      <option value="unexported" selected>All Unexported Records Only (Pending: <?php echo ($bioUnexportedCount + $logsUnexportedCount); ?>)</option>
                      <option value="first_15">1st - 15th Cut-off (15 Days)</option>
                      <option value="second_15">16th - End of Month Cut-off (15 Days)</option>
                      <option value="whole_month">1 Whole Month</option>
                      <option value="last_15_days">Last 15 Days (Recent)</option>
                      <option value="custom">Custom Date Range (From - To)</option>
                    </select>
                  </div>

                  <!-- Month & Year Selector for 15-day and Whole Month presets -->
                  <div id="monthYearRow" class="form-group" style="display: none;">
                    <div class="row">
                      <div class="col-md-6">
                        <label class="small font-weight-bold">Select Month:</label>
                        <select name="selected_month" id="selected_month" class="form-control" onchange="updateDatePreview()">
                          <?php
                          $months = [
                              1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                              5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                              9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                          ];
                          $currentM = (int)date('m');
                          foreach ($months as $num => $mName) {
                              $sel = ($num === $currentM) ? 'selected' : '';
                              echo "<option value='$num' $sel>$mName</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="small font-weight-bold">Select Year:</label>
                        <select name="selected_year" id="selected_year" class="form-control" onchange="updateDatePreview()">
                          <?php
                          $currentY = (int)date('Y');
                          for ($y = $currentY - 2; $y <= $currentY + 1; $y++) {
                              $sel = ($y === $currentY) ? 'selected' : '';
                              echo "<option value='$y' $sel>$y</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Custom Date Range Row -->
                  <div id="customDateRow" class="form-group" style="display: none;">
                    <div class="row">
                      <div class="col-md-6">
                        <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo date('Y-m-01'); ?>" onchange="updateDatePreview()" />
                        <small>From</small>
                      </div>
                      <div class="col-md-6">
                        <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo date('Y-m-d'); ?>" onchange="updateDatePreview()" />
                        <small>To</small>
                      </div>
                    </div>
                  </div>

                  <!-- Preview Box -->
                  <div class="alert alert-light border" style="font-size: 13px; margin-bottom: 15px;">
                    <i class="fa fa-info-circle text-info"></i>
                    <span id="datePreviewText">Exporting all unexported attendance logs.</span>
                  </div>

                  <div class="form-group">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="only_unexported" name="only_unexported" value="1" checked>
                      <label class="form-check-label small" for="only_unexported" style="cursor: pointer;">
                        <strong>Only export unexported records (is_exported = 0)</strong>
                      </label>
                    </div>
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="force_include_exported" name="force_include_exported" value="1">
                      <label class="form-check-label text-muted small" for="force_include_exported" style="cursor: pointer;">
                        Include already-exported records (emergency re-export)
                      </label>
                    </div>
                  </div>

                  <small class="text-muted d-block mb-3">
                    <i class="fa fa-tag"></i> Exported records are automatically stamped with <strong>1</strong> so they will not be re-exported next time.
                  </small>

                  <div class="form-group">
                    <button type="submit" formaction="dtr_export_download.php" class="btn btn-primary btn-block">
                      <i class="fa fa-download"></i> EXPORT &amp; DOWNLOAD .DTR (FOR USB)
                    </button>
                    <button type="submit" formaction="auto_export_dtr.php" class="btn btn-success btn-block" style="margin-top: 8px;">
                      <i class="fa fa-bolt"></i> AUTO-EXPORT TO SPARE FOLDER (<code>dtr_exports/</code>)
                    </button>
                  </div>
                </form>
                </div>
              </div>
            </div>

            <!-- RIGHT CARD: IMPORT -->
            <div class="col-lg-6" style="margin-bottom: 16px;">
              <div class="card user-activity" style="border-bottom: 2px solid green; border-top: 1px solid green; border-left: 1px solid green; border-right: 1px solid green;">
                <h2 class="display h4"><i class="fa fa-arrow-down"></i> Import .DTR File (Main / New Server)</h2>
                <div class="page-statistics-left">
                <form class="form-horizontal" action="dtr_import_process.php" method="POST" enctype="multipart/form-data">
                  <fieldset>
                    
                    <div class="form-group">
                      <label class="control-label" for="dtr_file" style="font-weight: bold;">Select .DTR File from USB:</label>
                      <input type="file" name="dtr_file" id="dtr_file" class="form-control" accept=".dtr,.json" required />
                      <small class="text-muted">Select the file exported from the infirmary server.</small>
                    </div>

                    <div class="alert alert-light border" style="font-size: 13px; margin-top: 15px; margin-bottom: 15px;">
                      <strong><i class="fa fa-shield text-success"></i> Zero Duplicate Guarantee &amp; Smart Merging:</strong>
                      <ul style="padding-left: 18px; margin-bottom: 0; margin-top: 6px;">
                        <li><strong>Duplicate Immunity:</strong> Existing punches matching flow or timestamp are safely skipped.</li>
                        <li><strong>Smart Session Merging:</strong> Afternoon clock-outs automatically complete open morning sessions.</li>
                        <li><strong>Multi-Identifier Match:</strong> Resolves employees via biometric ID, RFID tag, ID code, or full name.</li>
                        <li><strong>Form 48 Integrity:</strong> Guarantees clean 1-punch-per-flow display on CSC Form 48.</li>
                      </ul>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                      <button type="submit" name="Import" class="btn btn-primary btn-block">
                        <i class="fa fa-arrow-down"></i> IMPORT DTR FILE TO DATABASE
                      </button>
                    </div>

                    <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed #ddd;">
                      <label class="control-label" style="font-weight: bold;"><i class="fa fa-server text-success"></i> New Server / Auto-Import:</label>
                      <p class="small text-muted" style="margin-bottom: 8px;">
                        If files are placed directly into the spare folder (via LAN network share or USB drop), click below to import them all automatically with zero duplicates:
                      </p>
                      <a href="dtr_import_folder.php" class="btn btn-outline-success btn-block" onclick="return confirm('Process and import all pending .dtr files found in the spare folder? Duplicates will be safely skipped.');">
                        <i class="fa fa-folder-open"></i> AUTO-IMPORT ALL PENDING FROM SPARE FOLDER (<?php echo $spareFilesCount; ?> PENDING)
                      </a>
                    </div>

                  </fieldset>
                </form>
                </div>
              </div>
            </div>

            <!-- BOTTOM CARD: SPARE FOLDER ARCHIVES FOR NEW SERVER -->
            <div class="col-lg-12" style="margin-bottom: 24px;">
              <div class="card user-activity" style="border-bottom: 2px solid green; border-top: 1px solid green; border-left: 1px solid green; border-right: 1px solid green;">
                <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 12px;">
                  <h2 class="display h4" style="margin-bottom: 0;">
                    <i class="fa fa-folder-open text-success"></i> DTR Spare Folder Archives (<code>c:\xampp\htdocs\mob_bio\dtr_exports\</code>)
                  </h2>
                  <a href="dtr_import_folder.php" class="btn btn-sm btn-success" onclick="return confirm('Process and import all pending files from the spare folder?');">
                    <i class="fa fa-refresh"></i> Process &amp; Auto-Import All (<?php echo $spareFilesCount; ?>)
                  </a>
                </div>

                <div class="page-statistics-left" style="padding-top: 0;">
                  <!-- Live Windows Schedule Status Banner -->
                  <div class="d-flex align-items-center justify-content-between p-2 mb-3 border rounded <?php echo $autoExportStatus === 'ACTIVE' ? 'bg-light border-success' : 'bg-light border-secondary'; ?>">
                    <div>
                      <strong><i class="fa fa-calendar-check-o text-<?php echo $autoExportStatus === 'ACTIVE' ? 'success' : 'muted'; ?>"></i> Auto-Export Schedule (15th &amp; 30th):</strong>
                      <?php if ($autoExportStatus === 'ACTIVE'): ?>
                        <span class="badge badge-success ml-1"><i class="fa fa-check-circle"></i> ON / ACTIVE (15th &amp; End-of-Month at 5:00 PM)</span>
                        <span class="small text-muted ml-2">&bull; Infirmary Server is scheduled to automatically export to <code>dtr_exports/</code></span>
                      <?php elseif ($autoExportStatus === 'PARTIAL'): ?>
                        <span class="badge badge-warning ml-1"><i class="fa fa-exclamation-triangle"></i> PARTIALLY SCHEDULED</span>
                        <span class="small text-muted ml-2">&bull; Run <code>toggle_auto_export.bat</code> to re-synchronize both cutoff tasks.</span>
                      <?php else: ?>
                        <span class="badge badge-secondary ml-1"><i class="fa fa-power-off"></i> OFF / DISABLED</span>
                        <span class="small text-muted ml-2">&bull; Run <code>toggle_auto_export.bat</code> to turn ON for the Infirmary Server only.</span>
                      <?php endif; ?>
                    </div>
                    <div>
                      <a href="dtr_view_log.php" target="_blank" class="btn btn-xs btn-outline-info" title="View Auto-Export Log">
                        <i class="fa fa-file-text-o"></i> View Log
                      </a>
                      <span class="badge badge-light border ml-1" title="Batch file controller"><i class="fa fa-terminal"></i> toggle_auto_export.bat</span>
                    </div>
                  </div>

                  <div class="alert alert-light border" style="font-size: 13px; margin-bottom: 16px;">
                    <i class="fa fa-info-circle text-info"></i>
                    <strong>New Server Integration:</strong> This spare folder holds all generated <code>.dtr</code> synchronization packages. 
                    You can share this folder on the local network (LAN) so the new server can read from it directly, or use <code>toggle_auto_export.bat</code> to manage automated exports on the 15th and 30th of every month.
                  </div>

                  <?php if (!empty($spareFiles)): ?>
                    <div class="table-responsive">
                      <table class="table table-striped table-hover table-bordered small">
                        <thead class="thead-light">
                          <tr>
                            <th>Package File Name</th>
                            <th>Exported Date &amp; Time</th>
                            <th>File Size</th>
                            <th class="text-center">Biometric Sessions</th>
                            <th class="text-center">Punch Logs</th>
                            <th style="width: 220px; text-align: center;">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($spareFiles as $f): ?>
                            <tr>
                              <td>
                                <i class="fa fa-file-text-o text-success mr-1"></i>
                                <strong><?php echo htmlspecialchars($f['filename']); ?></strong>
                              </td>
                              <td><?php echo htmlspecialchars($f['exported_at']); ?></td>
                              <td><span class="badge badge-light border"><?php echo $f['size']; ?></span></td>
                              <td class="text-center"><span class="badge badge-info"><?php echo $f['bio_count']; ?></span></td>
                              <td class="text-center"><span class="badge badge-success"><?php echo $f['logs_count']; ?></span></td>
                              <td class="text-center">
                                <a href="dtr_download_exported.php?file=<?php echo urlencode($f['filename']); ?>" class="btn btn-xs btn-outline-primary" title="Download to USB">
                                  <i class="fa fa-download"></i> Download
                                </a>
                                <a href="dtr_import_folder.php?file=<?php echo urlencode($f['filename']); ?>" class="btn btn-xs btn-outline-success" onclick="return confirm('Import <?php echo addslashes($f['filename']); ?> into the database? Duplicates will be safely skipped.');" title="Import directly to database">
                                  <i class="fa fa-arrow-down"></i> Import to DB
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-light border text-center" style="padding: 24px; color: #666;">
                      <i class="fa fa-folder-open-o fa-2x text-muted" style="margin-bottom: 8px; display: block;"></i>
                      The spare folder is currently empty. Use the <strong>Auto-Export to Spare Folder</strong> button above or run <code>auto_export_dtr.php</code> to generate packages for the new server.
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <?php include('footer.php'); ?>
      
    </div>
    
    <?php include('scripts_files.php'); ?>

    <script>
    function updateFilterUI() {
        var fType = document.getElementById('filter_type').value;
        var myRow = document.getElementById('monthYearRow');
        var cdRow = document.getElementById('customDateRow');

        if (fType === 'first_15' || fType === 'second_15' || fType === 'whole_month') {
            myRow.style.display = 'block';
            cdRow.style.display = 'none';
        } else if (fType === 'custom') {
            myRow.style.display = 'none';
            cdRow.style.display = 'block';
        } else {
            myRow.style.display = 'none';
            cdRow.style.display = 'none';
        }
        updateDatePreview();
    }

    function updateDatePreview() {
        var fType = document.getElementById('filter_type').value;
        var preview = document.getElementById('datePreviewText');
        var mSelect = document.getElementById('selected_month');
        var mName = mSelect.options[mSelect.selectedIndex].text;
        var yVal = document.getElementById('selected_year').value;

        if (fType === 'unexported') {
            preview.innerHTML = 'Exporting all fresh unexported attendance logs (Pending date span: <strong><?php echo $unexportedDateRange; ?></strong>).';
        } else if (fType === 'first_15') {
            preview.innerHTML = 'Exporting 1st Half Cut-off (15 Days): <strong>' + mName + ' 01, ' + yVal + ' to ' + mName + ' 15, ' + yVal + '</strong>.';
        } else if (fType === 'second_15') {
            preview.innerHTML = 'Exporting 2nd Half Cut-off (15 Days): <strong>' + mName + ' 16, ' + yVal + ' to End of ' + mName + ' ' + yVal + '</strong>.';
        } else if (fType === 'whole_month') {
            preview.innerHTML = 'Exporting 1 Whole Month: <strong>All of ' + mName + ' ' + yVal + '</strong>.';
        } else if (fType === 'last_15_days') {
            preview.innerHTML = 'Exporting <strong>Last 15 Days</strong> from today.';
        } else if (fType === 'custom') {
            var dFrom = document.getElementById('date_from').value;
            var dTo = document.getElementById('date_to').value;
            preview.innerHTML = 'Exporting Custom Range: <strong>' + dFrom + ' to ' + dTo + '</strong>.';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        updateFilterUI();
    });
    </script>
  </body>
</html>
