<?php
include('dbcon.php');

// Fetch all registered biometric templates to load into the scanner cache
$flatTemplates = [];
$stmt = $conn->query("SELECT personnel_id, biometric_id, fingerprint_template FROM personnels WHERE fingerprint_template IS NOT NULL AND fingerprint_template != ''");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data = null;
    if (!empty($row['fingerprint_template'])) {
        $data = json_decode($row['fingerprint_template'], true);
    }
    
    if (is_array($data)) {
        // Multi-finger support
        foreach($data as $finger => $base64) {
            $flatTemplates[] = [
                'personnel_id' => $row['personnel_id'],
                'biometric_id' => $row['biometric_id'],
                'finger' => $finger,
                'fingerprint_template' => $base64
            ];
        }
    } else if (!empty($row['fingerprint_template'])) {
        // Legacy support from first version of multi-finger
        $flatTemplates[] = [
            'personnel_id' => $row['personnel_id'],
            'biometric_id' => $row['biometric_id'],
            'finger' => 'legacy',
            'fingerprint_template' => $row['fingerprint_template']
        ];
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HRMS - Biometric DTR</title>
    <link rel="shortcut icon" href="img/moh seal.jpg">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { background-color: #f1f5f9; font-family: 'Inter', system-ui, -apple-system, sans-serif; height: 100vh; overflow: hidden; margin: 0; color: #334155;}
        
        /* Navbar Modernization */
        .navbar { background: #ffffff; color: #1e293b; padding: 12px 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; border-bottom: 4px solid #059669; z-index: 100; position: relative;}
        .navbar .brand { display: flex; align-items: center; gap: 16px; }
        .navbar img.logo { height: 48px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }
        .navbar h4 { margin: 0; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; font-size: 1.1em; color: #1e293b;}
        .navbar h4 span { color: #059669; }
        .btn-outline-light { color: #475569; border-color: #cbd5e1; font-weight: 600; padding: 6px 14px; border-radius: 8px;}
        .btn-outline-light:hover { background: #f8fafc; color: #0f172a; border-color: #94a3b8; }
        
        .main-container { display: flex; height: calc(100vh - 76px); }
        
        /* Left Panel - Scanner (Modern Glass/Gradient) */
        .scanner-panel { width: 60%; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; box-shadow: 10px 0 30px rgba(0,0,0,0.1); z-index: 10; position: relative; overflow: hidden;}
        .scanner-panel::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(5,150,105,0.1) 0%, rgba(0,0,0,0) 50%); pointer-events: none;}
        
        .clock { font-size: 6em; font-weight: 800; margin-bottom: -5px; color: #ffffff; text-shadow: 0 4px 12px rgba(0,0,0,0.2); font-variant-numeric: tabular-nums; letter-spacing: -1px;}
        .clock span { font-size: 0.35em; color: #10b981; margin-left: 8px; font-weight: 700; vertical-align: middle; letter-spacing: 0;}
        .date { font-size: 1.2em; color: #94a3b8; margin-bottom: 50px; font-weight: 400; text-transform: uppercase; letter-spacing: 2px;}
        
        .scan-icon-container { position: relative; margin-bottom: 50px; }
        .scan-ring { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 350px; height: 350px; border-radius: 50%; background: radial-gradient(circle, rgba(16,185,129,0.4) 0%, rgba(16,185,129,0) 70%); animation: pulse 3s infinite;}
        .scan-icon { font-size: 12em; color: #10b981; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; z-index: 2; filter: drop-shadow(0 8px 16px rgba(16,185,129,0.3));}
        
        .scan-icon.success { color: #10b981; transform: scale(1.15); filter: drop-shadow(0 8px 24px rgba(16,185,129,0.5));}
        .scan-icon.error { color: #ef4444; animation: shake 0.5s; filter: drop-shadow(0 8px 24px rgba(239,68,68,0.5));}
        
        .status-box { background: rgba(255,255,255,0.05); padding: 18px 40px; border-radius: 100px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
        .status-msg { font-size: 1.8em; margin: 0; font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; gap: 14px;}
        
        /* Right Panel - Monitor */
        .monitor-panel { width: 40%; background: #f8fafc; padding: 40px; overflow: hidden; }
        .monitor-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;}
        .monitor-header h3 { color: #0f172a; margin: 0; font-weight: 800; font-size: 1.6em; display: flex; align-items: center; gap: 12px;}
        .monitor-header h3 i { color: #059669; }
        .real-time-badge { background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 100px; font-size: 0.85em; font-weight: 700; display: flex; align-items: center; gap: 6px;}
        .real-time-badge i { font-size: 8px; animation: pulse-opacity 2s infinite; }
        
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 50% { transform: translateX(8px); } 75% { transform: translateX(-8px); } }
        @keyframes pulse { 0% { transform: translate(-50%, -50%) scale(0.95); opacity: 0.7; } 50% { transform: translate(-50%, -50%) scale(1.1); opacity: 1.0; } 100% { transform: translate(-50%, -50%) scale(0.95); opacity: 0.7; } }
        @keyframes pulse-opacity { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Employee Overlay on Scan */
        .emp-overlay { display: none; margin-top: 30px; text-align: center; animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: rgba(255,255,255,0.05); padding: 30px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); width: 80%;}
        .emp-overlay img { width: 180px; height: 180px; border-radius: 50%; border: 4px solid #10b981; margin-bottom: 20px; object-fit: cover; box-shadow: 0 8px 24px rgba(0,0,0,0.2);}
        .emp-overlay h3 { margin: 0 0 10px 0; font-size: 2.2em; color: white; font-weight: 700; line-height: 1.2;}
        .emp-overlay p { margin: 0; font-weight: 800; font-size: 1.4em; letter-spacing: 1px; text-transform: uppercase; padding: 8px 20px; border-radius: 100px; display: inline-block; background: rgba(255,255,255,0.1);}

        /* Custom Loader */
        .loader {
          width: 50px;
          aspect-ratio: 1;
          display:grid;
          -webkit-mask: conic-gradient(from 15deg,#0000,#000);
          animation: l26 1s infinite steps(12);
        }
        .loader,
        .loader:before,
        .loader:after{
          background:
            radial-gradient(closest-side at 50% 12.5%,
             #ffffff 96%,#0000) 50% 0/20% 80% repeat-y,
            radial-gradient(closest-side at 12.5% 50%,
             #ffffff 96%,#0000) 0 50%/80% 20% repeat-x;
        }
        .loader:before,
        .loader:after {
          content: "";
          grid-area: 1/1;
          transform: rotate(30deg);
        }
        .loader:after {
          transform: rotate(60deg);
        }

        @keyframes l26 {
          100% {transform:rotate(1turn)}
        }

        /* Responsiveness */
        @media (max-width: 992px) {
            body { height: auto; overflow: visible; }
            .main-container { flex-direction: column; height: auto; min-height: 100vh; }
            .scanner-panel { width: 100%; min-height: 60vh; padding: 40px 20px; }
            .monitor-panel { width: 100%; height: auto; padding: 20px; }
            .navbar { flex-wrap: wrap; text-align: center; justify-content: center; gap: 15px; padding: 15px; }
            .navbar .brand { flex-direction: column; gap: 8px; }
            .clock { font-size: 4.5em; }
        }
        @media (max-width: 576px) {
            .clock { font-size: 3.2em; }
            .date { font-size: 0.9em; margin-bottom: 30px; }
            .scan-icon-container img { width: 100px !important; height: 100px !important; }
            .scan-ring { width: 160px; height: 160px; }
            .navbar h4 { font-size: 0.9em; line-height: 1.4; }
            .monitor-header h3 { font-size: 1.3em; }
            .carousel-inner { height: 400px !important; }
        }
    </style>
</head>
<body>
    
    <div class="navbar">
        <div class="brand">
            <img src="img/moh seal.jpg" alt="MOH Logo" class="logo">
            <h4>MUNICIPALITY OF HINOBA-AN HRMS - BIOMETRIC TIME & ATTENDANCE</h4>
        </div>
        <div>
        
            <a href="home.php" class="btn btn-outline-light ms-3" style="font-weight: 600; font-size: 0.85em; padding: 8px 16px;">Dashboard</a>
        </div>
    </div>

    <div class="main-container">
        <!-- Left Panel -->
        <div class="scanner-panel">
            <div id="greeting" style="font-size: 1.8em; font-weight: 800; margin-bottom: -15px; color: #10b981; letter-spacing: 1px; text-transform: uppercase;">Welcome!</div>
            <div class="clock" id="clock">00:00:00</div>
            <div class="date" id="date">Loading...</div>
            
            <div class="scan-icon-container" id="iconContainer">
                <div class="scan-ring"></div>
                <img src="img/biometric.png" class="scan-icon" id="scanIcon" style="width: 250px; height: 250px; object-fit: contain; animation: pulse-opacity 2s infinite;">
            </div>
            
            <div class="status-box">
                <p class="status-msg" id="statusMsg">Click Initialize to Start</p>
            </div>
            <button id="btnInit" class="btn btn-lg" style="margin-top: 15px; border-radius: 8px; padding: 10px 24px; font-weight: 700; background: #059669; color: white; border: none; box-shadow: 0 4px 12px rgba(5,150,105,0.3);" onclick="initScanner()">Initialize Scanner</button>
            
            <div class="emp-overlay" id="empOverlay">
                <img src="personnelImg/default_img.jpg" id="overlayImg">
                <h3 id="overlayName">John Doe</h3>
                <p id="overlayType">TIME IN</p>
            </div>
        </div>
        
        <!-- Right Panel -->
        <div class="monitor-panel">
            <div class="monitor-header">
                <h3>Announcements</h3>
            </div>
            
            <div id="announcementSlideshow" class="carousel slide" data-ride="carousel" data-interval="15000">
                <div class="carousel-inner" style="height: calc(100vh - 240px); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: white;">
                    <?php
                    $slides_query = $conn->query("SELECT * FROM slides ORDER BY sequence ASC");
                    $active = "active";
                    $hasSlides = false;
                    while($slide = $slides_query->fetch()) {
                        $hasSlides = true;
                        echo '<div class="carousel-item ' . $active . '" style="height: 100%;">';
                        echo '<img class="d-block w-100" src="announcement_img/' . $slide['img'] . '" alt="Announcement" style="height: 100%; width: 100%; object-fit: contain;">';
                        echo '</div>';
                        $active = "";
                    }
                    if(!$hasSlides) {
                        echo '<div class="carousel-item active" style="height: 100%; background: #e2e8f0; display: flex; align-items: center; justify-content: center;">';
                        echo '<div style="text-align: center; color: #64748b;"><h4>No Announcements</h4></div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div id="hiddenScreen" style="display:none;"></div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/howler.min.js"></script>
    <script>
        function loadLiveFeed() {
            // Disabled: User requested no live logs here. Overlay handles success popups.
        }
        
        function PopupCenterValid() {
            var sound = new Howl({ src: ['RFID_FX/gate_access.mp3'], volume: 1 });
            sound.play();
        }
        
        function PopupCenterInvalid() {
            var sound = new Howl({ src: ['RFID_FX/buzzer.mp3'], volume: 1 });
            sound.play();
        }
        
        $(document).ready(function() {
            loadLiveFeed();
            
            // Auto-sync fingerprints from DB every 10 seconds
            setInterval(function() {
                $.ajax({
                    url: 'fetch_biometric_templates.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        registeredTemplates = data;
                    }
                });
            }, 10000);
        });
        // Clock
        setInterval(() => {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            let ampm = hours >= 12 ? 'PM' : 'AM';
            
            let greeting = 'Good Evening!';
            let currentHour = now.getHours();
            if(currentHour >= 5 && currentHour < 12) greeting = 'Good Morning!';
            else if(currentHour >= 12 && currentHour < 17) greeting = 'Good Afternoon!';
            document.getElementById('greeting').innerText = greeting;
            
            hours = hours % 12;
            hours = hours ? hours : 12; 
            minutes = minutes < 10 ? '0'+minutes : minutes;
            seconds = seconds < 10 ? '0'+seconds : seconds;
            
            document.getElementById('clock').innerHTML = hours + ':' + minutes + '<span style="font-size:0.5em; margin-left:5px;">' + seconds + ' ' + ampm + '</span>';
            
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('date').innerText = now.toLocaleDateString('en-US', options);
        }, 1000);

        // ZKTeco Web API configuration
        var ports = [15270, 22081, 22082, 15271];
        var currentPortIndex = 0;
        var zkTecoUrl = "";
        var registeredTemplates = <?php echo json_encode($flatTemplates); ?>;
        var captureTimer = null;

        function updateUI(status, type, empName = '', imgSrc = '', logType = '') {
            $('#statusMsg').html(status);
            var icon = $('#scanIcon');
            icon.removeClass('success error');
            $('#statusMsg').parent().css('border-color', '#3c8dbc');
            
            if(type === 'success') {
                icon.addClass('success');
                $('#statusMsg').parent().css('border-color', '#00a65a');
            }
            if(type === 'error') {
                icon.addClass('error');
                $('#statusMsg').parent().css('border-color', '#dd4b39');
            }
            
            if(empName !== '') {
                $('#iconContainer').hide();
                $('#overlayImg').attr('src', imgSrc ? imgSrc : 'personnelImg/default_img.jpg');
                $('#overlayName').text(empName);
                $('#overlayType').text(logType);
                $('#empOverlay').show();
                
                setTimeout(() => {
                    $('#empOverlay').hide();
                    $('#iconContainer').show();
                    updateUI("<i class='fa fa-crosshairs' style='color: #10b981; animation: pulse-opacity 2s infinite;'></i> READY TO SCAN...", "info");
                }, 3500);
            }
        }

        function initScanner() {
            if (currentPortIndex >= ports.length) {
                updateUI("<i class='fa fa-warning'></i> Scanner not detected.", "error");
                currentPortIndex = 0; // reset for next manual click
                $('#btnInit').show();
                return;
            }
            
            $('#btnInit').hide();
            updateUI("<i class='fa fa-spinner fa-spin'></i> Initializing...", "info");
            zkTecoUrl = "http://127.0.0.1:" + ports[currentPortIndex] + "/ZK_Finger";
            
            $.ajax({
                url: zkTecoUrl + "/Init",
                type: "GET",
                dataType: "text",
                success: function (data) {
                    try {
                        var res = JSON.parse(data);
                        if (res.ret == 0) {
                            updateUI("<i class='fa fa-crosshairs' style='color: #10b981; animation: pulse-opacity 2s infinite;'></i> READY TO SCAN...", "info");
                            startCapture();
                        } else {
                            updateUI("<i class='fa fa-warning'></i> Scanner not initialized.", "error");
                            $('#btnInit').show();
                        }
                    } catch(e) {
                        currentPortIndex++;
                        initScanner();
                    }
                },
                error: function () {
                    // Try next port
                    currentPortIndex++;
                    initScanner();
                }
            });
        }

        function startCapture() {
            $.ajax({
                url: zkTecoUrl + "/BeginCapture",
                type: "GET",
                success: function() {
                    pollCapture();
                }
            });
        }

        function pollCapture() {
            captureTimer = setInterval(function() {
                $.ajax({
                    url: zkTecoUrl + "/GetTemplate",
                    type: "GET",
                    dataType: "text",
                    success: function (data) {
                        try {
                            var res = JSON.parse(data);
                            if (res.ret == 0 && res.template) {
                                clearInterval(captureTimer);
                                verifyFingerprint(res.template);
                            }
                        } catch (e) {}
                    }
                });
            }, 500);
        }

        function verifyFingerprint(liveTemplate) {
            updateUI("<div class='loader'></div> VERIFYING...", "info");
            
            if (registeredTemplates.length === 0) {
                updateUI("<i class='fa fa-times'></i> No templates in DB.", "error");
                setTimeout(startCapture, 3000);
                return;
            }

            var regTemplatesArray = registeredTemplates.map(function(t) { return t.fingerprint_template; });
            var regTemplatesJoined = regTemplatesArray.join('|');

            $.ajax({
                url: zkTecoUrl + "/VerifyMulti",
                type: "POST",
                data: { capTemplate: liveTemplate, regTemplates: regTemplatesJoined },
                success: function (data) {
                    try {
                        var res = typeof data === 'string' ? JSON.parse(data) : data;
                        if (res.ret == 0 && res.match_index >= 0) {
                            var target = registeredTemplates[res.match_index]; var targetId = target.biometric_id ? target.biometric_id : (target.RFTag_id ? target.RFTag_id : target.personnel_id);
                            logAttendanceToDatabase(targetId);
                        } else {
                            updateUI("<i class='fa fa-times'></i> Unrecognized Fingerprint.", "error");
                            setTimeout(() => { updateUI("<i class='fa fa-hand-pointer-o'></i> Ready to scan...", "info"); startCapture(); }, 2000);
                        }
                    } catch(e) {
                        updateUI("<i class='fa fa-times'></i> Verification error.", "error");
                        setTimeout(startCapture, 2000);
                    }
                },
                error: function() {
                    updateUI("<i class='fa fa-times'></i> Verification failed.", "error");
                    setTimeout(startCapture, 2000);
                }
            });
        }

        function logAttendanceToDatabase(targetId) {
            updateUI("<i class='fa fa-spinner fa-spin'></i> Logging DTR...", "info");
            
            $.ajax({
                url: 'process_biometric_dtr.php',
                type: 'POST',
                data: { RFTag_id: targetId, log_source: 'BIO' },
                success: function(responseStr) {
                    try {
                        var response = JSON.parse(responseStr);
                        
                        if (response.status === 'success') {
                            updateUI("<i class='fa fa-check'></i> Logged successfully!", "success");
                            
                            if (response.sound === 'valid') {
                                PopupCenterValid();
                            } else {
                                PopupCenterInvalid();
                            }
                            
                            // Fetch latest log to show in overlay
                            $.ajax({
                                url: 'get_latest_bio_log.php',
                                type: 'GET',
                                data: { RFTag_id: targetId },
                                dataType: 'json',
                                success: function(logData) {
                                    if(logData.status == 'success') {
                                        $('#overlayImg').attr('src', logData.img);
                                        $('#overlayName').text(logData.name);
                                        $('#overlayType').text(logData.logFlow + " - " + logData.time);
                                        
                                        if(logData.logFlow.indexOf('IN') !== -1) {
                                            $('#overlayType').removeClass('bg-danger').addClass('bg-success');
                                        } else {
                                            $('#overlayType').removeClass('bg-success').addClass('bg-danger');
                                        }
                                        
                                        $('#iconContainer').hide();
                                        $('#empOverlay').fadeIn();
                                        
                                        loadLiveFeed(); // Refresh the list
                                    }
                                }
                            });
                            
                        } else {
                            // Error from process script
                            updateUI("<i class='fa fa-times'></i> " + response.message, "error");
                            if (response.sound === 'invalid') {
                                PopupCenterInvalid();
                            }
                        }
                    } catch (e) {
                        console.log("JSON Parse Error on response:", responseStr);
                        console.error(e);
                        updateUI("<i class='fa fa-times'></i> Scan Error", "error");
                        PopupCenterInvalid();
                    }
                    
                    // Resume capture after 2 seconds
                    setTimeout(() => {
                        $('#empOverlay').hide();
                        $('#iconContainer').show();
                        updateUI("<i class='fa fa-hand-pointer-o'></i> Ready to scan...", "info");
                        startCapture();
                    }, 2000);
                },
                error: function() {
                    updateUI("<i class='fa fa-times'></i> Server Error", "error");
                    setTimeout(startCapture, 2000);
                }
            });
        }
        
        function loadLiveFeed() {
            $.ajax({
                url: 'fetch_live_feed.php',
                type: 'GET',
                success: function(html) {
                    $('#liveFeed').html(html);
                }
            });
        }

        $(document).ready(function() {
            loadLiveFeed();
            // Auto refresh feed every 10 seconds just in case
            setInterval(loadLiveFeed, 10000);
        });
    </script>
</body>
</html>


