<?php
include('session.php');
include('header.php');

$pid = isset($_GET['pid']) ? $_GET['pid'] : 0;
if ($pid == 0) {
    die("Invalid Personnel ID.");
}

$query = $conn->prepare("SELECT p.*, d.dept_office_name FROM personnels p LEFT JOIN dept_offices d ON p.do_id = d.do_id WHERE p.personnel_id = :pid");
$query->execute(['pid' => $pid]);
$personnel = $query->fetch();

if (!$personnel) {
    die("Personnel not found.");
}

$fingerData = [];
if (!empty($personnel['fingerprint_template'])) {
    $decoded = json_decode($personnel['fingerprint_template'], true);
    if (is_array($decoded)) {
        $fingerData = $decoded;
    } else {
        // If it's a legacy single string, map it to generic
        $fingerData['legacy'] = $personnel['fingerprint_template'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .card { border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 16px; overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 2px solid #f1f5f9; padding: 15px 20px; }
        .card-header h4 { font-weight: 800; color: #1e293b; margin: 0; font-size: 1.2em; }
        
        .finger-box { border: 2px dashed #cbd5e1; padding: 25px 15px; text-align: center; border-radius: 16px; background: #f8fafc; margin-bottom: 15px; min-height: 235px; transition: all 0.3s ease; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: center;}
        .finger-box.scanning { border-color: #059669; background: #ecfdf5; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
        .finger-box.error { border-color: #ef4444; background: #fef2f2; }
        .finger-box i { font-size: 3.5em; color: #94a3b8; margin-bottom: 10px; transition: all 0.3s ease; }
        .finger-box.scanning i { color: #10b981; filter: drop-shadow(0 4px 12px rgba(16, 185, 129, 0.3)); animation: scan-pulse 2s infinite; }
        .finger-box.error i { color: #ef4444; }
        
        #statusText { font-weight: 700; color: #334155; margin-bottom: 5px; font-size: 1.1em;}
        #instructionText { color: #64748b; font-size: 0.9em; margin-bottom: 0;}
        
        .finger-btn { width: 100%; margin-bottom: 8px; text-align: left; padding: 8px 15px; border-radius: 12px; font-weight: 600; border: 2px solid #e2e8f0; color: #475569; background: #ffffff; transition: all 0.2s ease; display: flex; align-items: center; justify-content: space-between;}
        .finger-btn i { font-size: 1.1em; margin-right: 8px; color: #94a3b8; }
        .finger-btn:hover { border-color: #059669; color: #059669; background: #f0fdf4; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(5, 150, 105, 0.1); }
        .finger-btn:hover i { color: #059669; }
        .finger-btn .badge { padding: 5px 10px; border-radius: 6px; font-size: 0.75em; font-weight: 700; }
        .badge-success { background: #10b981; color: white; }
        .badge-secondary { background: #e2e8f0; color: #64748b; }
        
        @keyframes scan-pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
  <?php include('menu_sidebar.php'); ?>
  <div class="page">
    <?php include('navbar_header.php'); ?>
    
    <div class="breadcrumb-holder" style="padding: 10px 0;">
      <div class="container-fluid">
        <ul class="breadcrumb" style="margin-bottom: 0;">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item"><a href="biometric_enrollment_list.php">Biometric Enrollment</a></li>
          <li class="breadcrumb-item active">Manage Fingers</li>
        </ul>
      </div>
    </div>
    
    <section class="mt-2 mb-2">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-10 mx-auto">
            <div class="card" style="margin-bottom: 10px;">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fa fa-fingerprint text-success"></i> Multi-Finger Biometric Enrollment</h4>
                <button id="btnInit" class="btn btn-sm" style="border-radius: 6px; padding: 6px 16px; font-weight: 600; background: #64748b; color: white; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" onclick="initScanner()">Initialize Scanner</button>
              </div>
              <div class="card-body">
                  <div class="row mb-4 align-items-center bg-light rounded" style="padding: 20px; border: 1px solid #e2e8f0;">
                      <div class="col-md-3 text-center">
                          <img src="<?php echo empty($personnel['img']) ? 'img/default.png' : 'personnelImg/'.$personnel['img']; ?>" class="img-thumbnail rounded-circle" style="width: 110px; height: 110px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                      </div>
                      <div class="col-md-9 mt-3 mt-md-0">
                          <h5 class="mb-1" style="font-weight: 800; color: #1e293b; font-size: 1.5em;"><?php echo htmlspecialchars($personnel['fname'] . ' ' . $personnel['lname']); ?></h5>
                          <p class="mb-2" style="font-weight: 600; color: #059669; font-size: 1.05em;"><i class="fa fa-building-o"></i> <?php echo htmlspecialchars($personnel['dept_office_name'] ?? 'No Department Assigned'); ?></p>
                          <div class="d-flex flex-wrap align-items-center">
                              <span class="badge mr-2 mb-2" style="background: #ffffff; border: 1px solid #cbd5e1; font-size: 0.9em; padding: 6px 12px; color: #475569;"><i class="fa fa-id-badge text-muted"></i> ID: <?php echo $personnel['personnel_id_code']; ?></span>
                              <span class="badge mb-2" style="background: #ffffff; border: 1px solid #cbd5e1; font-size: 0.9em; padding: 6px 12px; color: #475569;"><i class="fa fa-credit-card text-muted"></i> RFID: <?php echo $personnel['RFTag_id'] ?: 'N/A'; ?></span>
                          </div>
                      </div>
                  </div>
                  
                  <hr>

                  <div class="row d-flex align-items-stretch">
                      <div class="col-md-6 d-flex mb-3 mb-md-0">
                          <div class="finger-box w-100" id="fingerBox" style="margin-bottom: 0;">
                              <i class="fa fa-hand-pointer-o"></i>
                              <h5 id="statusText">Scanner Offline</h5>
                              <p id="instructionText" class="text-muted">Click Initialize Scanner above.</p>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <p class="text-info"><strong>Select a finger to enroll:</strong></p>
                          
                          <button id="btn_lt" class="btn btn-outline-primary finger-btn" onclick="startEnroll('lt', 'Left Thumb')">
                              <span class="d-flex align-items-center">
                                  <div style="display:flex; justify-content:center; align-items:center; width:36px; height:36px; border-radius:10px; background:#e0f2fe; margin-right:12px;">
                                      <i class="fa fa-thumbs-up fa-flip-horizontal" style="margin:0; color:#0284c7; font-size:1.1em;"></i>
                                  </div> 
                                  <span style="font-size: 1.05em;">Left Thumb</span>
                              </span>
                              <div class="badge-container">
                                <?php echo isset($fingerData['lt']) ? '<span class="badge badge-success"><i class="fa fa-check"></i> Enrolled</span>' : '<span class="badge badge-secondary">Pending</span>'; ?>
                              </div>
                          </button>
                          
                          <button id="btn_li" class="btn btn-outline-primary finger-btn" onclick="startEnroll('li', 'Left Index')">
                              <span class="d-flex align-items-center">
                                  <div style="display:flex; justify-content:center; align-items:center; width:36px; height:36px; border-radius:10px; background:#e0f2fe; margin-right:12px;">
                                      <i class="fa fa-hand-pointer-o fa-rotate-270" style="margin:0; color:#0284c7; font-size:1.1em;"></i>
                                  </div> 
                                  <span style="font-size: 1.05em;">Left Index</span>
                              </span>
                              <div class="badge-container">
                                <?php echo isset($fingerData['li']) ? '<span class="badge badge-success"><i class="fa fa-check"></i> Enrolled</span>' : '<span class="badge badge-secondary">Pending</span>'; ?>
                              </div>
                          </button>

                          <button id="btn_rt" class="btn btn-outline-primary finger-btn" onclick="startEnroll('rt', 'Right Thumb')">
                              <span class="d-flex align-items-center">
                                  <div style="display:flex; justify-content:center; align-items:center; width:36px; height:36px; border-radius:10px; background:#e0f2fe; margin-right:12px;">
                                      <i class="fa fa-thumbs-up" style="margin:0; color:#0284c7; font-size:1.1em;"></i>
                                  </div> 
                                  <span style="font-size: 1.05em;">Right Thumb</span>
                              </span>
                              <div class="badge-container">
                                <?php echo isset($fingerData['rt']) ? '<span class="badge badge-success"><i class="fa fa-check"></i> Enrolled</span>' : '<span class="badge badge-secondary">Pending</span>'; ?>
                              </div>
                          </button>

                          <button id="btn_ri" class="btn btn-outline-primary finger-btn" onclick="startEnroll('ri', 'Right Index')">
                              <span class="d-flex align-items-center">
                                  <div style="display:flex; justify-content:center; align-items:center; width:36px; height:36px; border-radius:10px; background:#e0f2fe; margin-right:12px;">
                                      <i class="fa fa-hand-pointer-o fa-rotate-90" style="margin:0; color:#0284c7; font-size:1.1em;"></i>
                                  </div> 
                                  <span style="font-size: 1.05em;">Right Index</span>
                              </span>
                              <div class="badge-container">
                                <?php echo isset($fingerData['ri']) ? '<span class="badge badge-success"><i class="fa fa-check"></i> Enrolled</span>' : '<span class="badge badge-secondary">Pending</span>'; ?>
                              </div>
                          </button>

                      </div>
                  </div>
                  
                  <input type="hidden" id="pid" value="<?php echo $pid; ?>">
                  <input type="hidden" id="currentFingerType" value="">

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
      var ports = [15270, 22081, 22082, 15271];
      var currentPortIndex = 0;
      var zkTecoUrl = "";
      var enrollTimer = null;
      var isInitialized = false;
      
      function updateStatus(text, type='info') {
          $('#statusText').text(text);
          $('#fingerBox').removeClass('scanning error');
          $('#fingerBox i').attr('class', 'fa fa-hand-pointer-o');
          
          if (type == 'success') {
              $('#fingerBox').css('border-color', '#10b981');
              $('#fingerBox i').attr('class', 'fa fa-check-circle text-success');
          }
          else if (type == 'error') {
              $('#fingerBox').addClass('error');
              $('#fingerBox i').attr('class', 'fa fa-times-circle');
          }
          else if (type == 'warning') {
              $('#fingerBox').addClass('scanning');
              $('#fingerBox i').attr('class', 'fa fa-fingerprint');
          }
          else {
              $('#fingerBox').css('border-color', '#cbd5e1');
          }
      }

      function initScanner() {
          if (currentPortIndex >= ports.length) {
              updateStatus("Connection Failed", "error");
              $('#instructionText').text("Make sure ZKTeco driver is running.");
              currentPortIndex = 0; // reset for next manual click
              return;
          }
          
          zkTecoUrl = "http://127.0.0.1:" + ports[currentPortIndex] + "/ZK_Finger";
          updateStatus("Initializing Scanner on port " + ports[currentPortIndex] + "...", "warning");
          
          $.ajax({
              url: zkTecoUrl + "/Init",
              type: "GET",
              dataType: "text",
              success: function (data) {
                  try {
                      var res = JSON.parse(data);
                      if (res.ret == 0) {
                          isInitialized = true;
                          updateStatus("Scanner Ready", "success");
                          $('#instructionText').text("Select a finger from the right to enroll.");
                          $('#btnInit').css('background', '#10b981').text('Scanner Connected').prop('disabled', true);
                      } else {
                          updateStatus("Scanner not initialized. Please unplug and plug it back in.", "error");
                          setTimeout(initScanner, 5000);
                      }
                  } catch(e) {
                      currentPortIndex++;
                      initScanner();
                  }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                  // Try next port
                  currentPortIndex++;
                  initScanner();
              }
          });
      }

      function startEnroll(fingerType, fingerName) {
          if(!isInitialized) {
              alert("Please initialize the scanner first.");
              return;
          }
          
          clearInterval(enrollTimer);
          $('#currentFingerType').val(fingerType);
          updateStatus("Enrolling: " + fingerName, "warning");
          $('#instructionText').text("Please place " + fingerName + " on the scanner (0/3)");
          

                          
          function pollEnroll() {
              enrollTimer = setInterval(function() {
                  $.ajax({
                      url: zkTecoUrl + "/GetEnrollTemplate",
                      type: "GET",
                      dataType: "text",
                      success: function (data) {
                          try {
                              var res = JSON.parse(data);
                              if (res.ret == 0 && res.template) {
                                  clearInterval(enrollTimer);
                                  updateStatus(fingerName + " Captured successfully!", "success");
                                  $('#instructionText').text("Saving to database...");
                                  saveTemplate(res.template, fingerName);
                              }
                          } catch (e) {}
                      }
                  });
              }, 500);
          }

          $.ajax({
              url: zkTecoUrl + "/BeginEnroll",
              type: "GET",
              dataType: "text",
              success: function (data) {
                  pollEnroll();
              },
              error: function () {
                  updateStatus("Error starting enrollment.", "error");
              }
          });
      }

      function saveTemplate(templateBase64, fingerName) {
          var fingerType = $('#currentFingerType').val();
          $.ajax({
              url: 'save_biometric.php',
              type: 'POST',
              data: {
                  pid: $('#pid').val(),
                  finger_type: fingerType,
                  template: templateBase64
              },
              success: function(response) {
                  if($.trim(response) == "success") {
                      updateStatus(fingerName + " Saved successfully!", "success");
                      
                      // Update the button badge dynamically
                      $('#btn_' + fingerType + ' .badge-container').html('<span class="badge badge-success"><i class="fa fa-check"></i> Enrolled</span>');
                      
                      // Wait a moment then reset the scanner box to "Ready" without dropping connection
                      setTimeout(function() {
                          updateStatus("Scanner Ready", "success");
                          $('#instructionText').text("Select another finger from the right to enroll.");
                      }, 2500);
                  } else {
                      updateStatus("Database Error", "error");
                      $('#instructionText').text(response);
                  }
              }
          });
      }
  </script>
</body>
</html>
