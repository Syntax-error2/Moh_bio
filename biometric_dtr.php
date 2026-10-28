<?php
include('dbcon.php');

// Load templates prioritized by Primary Finger (rt, ri) and recent attendance
$recentPunches = [];
try {
    $recentPunches = $conn->query("
        SELECT personnel_id, MAX(log_date) as last_date 
        FROM bio_dtr 
        GROUP BY personnel_id
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {}

$stmt = $conn->query("SELECT p.personnel_id, p.RFTag_id, p.biometric_id, p.fingerprint_template FROM personnels p JOIN emp_status es ON es.empStat_id = p.empStat_id WHERE es.status = 'Active' AND (p.separation_date IS NULL OR p.separation_date = '' OR p.separation_date = '  /  /    ') AND p.fingerprint_template IS NOT NULL AND p.fingerprint_template != ''");

$primaryTemplates = [];
$secondaryTemplates = [];
$fingerOrder = ['rt' => 1, 'ri' => 2, 'lt' => 3, 'li' => 4];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pid = $row['personnel_id'];
    $lastLogDate = $recentPunches[$pid] ?? '1970-01-01';
    
    $data = null;
    if (!empty($row['fingerprint_template'])) {
        $data = json_decode($row['fingerprint_template'], true);
    }
    
    if (is_array($data)) {
        foreach($data as $finger => $base64) {
            $item = [
                'personnel_id' => $row['personnel_id'],
                'RFTag_id' => $row['RFTag_id'] ?? '',
                'biometric_id' => $row['biometric_id'] ?? '',
                'finger' => $finger,
                'fingerprint_template' => $base64,
                '_last_log' => $lastLogDate,
                '_priority' => $fingerOrder[$finger] ?? 99
            ];
            if ($finger === 'rt' || $finger === 'ri') {
                $primaryTemplates[] = $item;
            } else {
                $secondaryTemplates[] = $item;
            }
        }
    } else if (!empty($row['fingerprint_template'])) {
        $primaryTemplates[] = [
            'personnel_id' => $row['personnel_id'],
            'RFTag_id' => $row['RFTag_id'] ?? '',
            'biometric_id' => $row['biometric_id'] ?? '',
            'finger' => 'legacy',
            'fingerprint_template' => $row['fingerprint_template'],
            '_last_log' => $lastLogDate,
            '_priority' => 1
        ];
    }
}

usort($primaryTemplates, function($a, $b) {
    if ($a['_last_log'] !== $b['_last_log']) {
        return strcmp($b['_last_log'], $a['_last_log']);
    }
    if ($a['_priority'] !== $b['_priority']) {
        return $a['_priority'] <=> $b['_priority'];
    }
    return $a['personnel_id'] <=> $b['personnel_id'];
});

usort($secondaryTemplates, function($a, $b) {
    if ($a['_last_log'] !== $b['_last_log']) {
        return strcmp($b['_last_log'], $a['_last_log']);
    }
    return $a['_priority'] <=> $b['_priority'];
});

$flatTemplates = array_map(function($t) {
    unset($t['_last_log'], $t['_priority']);
    return $t;
}, array_merge($primaryTemplates, $secondaryTemplates));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MOH - Biometric DTR</title>
    <link rel="shortcut icon" href="img/72123-mob_seal.png">
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
            <img src="img/72123-mob_seal.png" alt="MOB Logo" class="logo">
            <h4>MUNICIPALITY OF BINALBAGAN HRMS - BIOMETRIC TIME & ATTENDANCE</h4>
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
                <p class="status-msg" id="statusMsg">Initializing Scanner...</p>
            </div>
            
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
            
            // Refresh enrollments without repeatedly downloading the full template set during a queue.
            var templateRefreshInFlight = false;
            setInterval(function() {
                if (templateRefreshInFlight) return;
                templateRefreshInFlight = true;
                $.ajax({
                    url: 'fetch_biometric_templates.php',
                    type: 'GET',
                    dataType: 'json',
                    ifModified: true,
                    cache: true,
                    timeout: 10000,
                    success: function(data) {
                        // A 304 response has no body; keep the templates already in use.
                        if (Array.isArray(data)) {
                            setRegisteredTemplates(data);
                            syncTemplatesToBridge();
                        }
                    },
                    complete: function() {
                        templateRefreshInFlight = false;
                    }
                });
            }, 30000);
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
        var joinedTemplates = '';
        var captureTimer = null;
        var captureRequestInFlight = false;
        var capturePollFailures = 0;

        var precomputedChunks = [];
        var chunkSize = 950;
        function setRegisteredTemplates(templates) {
            registeredTemplates = Array.isArray(templates) ? templates : [];
            joinedTemplates = registeredTemplates.map(function(t) { return t.fingerprint_template; }).join('|');
            precomputedChunks = [];
            for (var i = 0; i < registeredTemplates.length; i += chunkSize) {
                var chunk = registeredTemplates.slice(i, i + chunkSize);
                precomputedChunks.push({
                    templates: chunk,
                    payload: chunk.map(function(t) { return t.fingerprint_template; }).join('|')
                });
            }
        }
        setRegisteredTemplates(registeredTemplates);

        // Template array is kept static to guarantee 100% strict matching accuracy

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
                currentPortIndex = 0; // reset
                setTimeout(initScanner, 5000);
                return;
            }
            
            zkTecoUrl = "http://127.0.0.1:" + ports[currentPortIndex] + "/ZK_Finger";
            
            $.ajax({
                url: zkTecoUrl + "/Init",
                type: "GET",
                dataType: "text",
                timeout: 5000,
                success: function (data) {
                    capturePollFailures = 0;
                    updateUI("<i class='fa fa-crosshairs' style='color: #10b981; animation: pulse-opacity 2s infinite;'></i> READY TO SCAN...", "info");
                    syncTemplatesToBridge();
                    startCapture();
                },
                error: function () {
                    // Try next port
                    currentPortIndex++;
                    initScanner();
                }
            });
        }

        function syncTemplatesToBridge() {
            if (!registeredTemplates || registeredTemplates.length === 0 || !zkTecoUrl) return;
            $.ajax({
                url: zkTecoUrl + "/SyncTemplates",
                type: "POST",
                data: { templates: JSON.stringify(registeredTemplates) },
                timeout: 15000,
                success: function(res) {
                    console.debug("Templates synced to native bridge RAM:", res);
                }
            });
        }

        function startCapture() {
            clearTimeout(captureTimer);
            captureTimer = null;
            $.ajax({
                url: zkTecoUrl + "/BeginCapture",
                type: "GET",
                timeout: 10000,
                success: function() {
                    pollCapture();
                },
                error: function() {
                    updateUI("<i class='fa fa-warning'></i> Scanner reconnecting...", "error");
                    setTimeout(initScanner, 2000);
                }
            });
        }

        function pollCapture() {
            captureTimer = null;
            if (captureRequestInFlight) return;
            captureRequestInFlight = true;
            var continuePolling = true;
            $.ajax({
                url: zkTecoUrl + "/GetTemplate",
                type: "GET",
                dataType: "text",
                timeout: 30000,
                success: function (data) {
                    capturePollFailures = 0;
                    try {
                        var res = JSON.parse(data);
                        if (res.ret == 0 && res.template) {
                            continuePolling = false;
                            // Instant zero-latency native 1:N match (< 3ms) directly from bridge RAM!
                            if (res.match_index !== undefined && res.match_index >= 0 && registeredTemplates[res.match_index]) {
                                console.debug('Instant native match in RAM (Match Index: ' + res.match_index + ', Score: ' + res.score + ')');
                                var target = registeredTemplates[res.match_index];
                                var targetId = target.biometric_id ? target.biometric_id : (target.RFTag_id ? target.RFTag_id : target.personnel_id);
                                logAttendanceToDatabase(targetId, target);
                            } else {
                                // Fallback to chunked VerifyMulti if not in RAM cache or unrecognized
                                verifyFingerprint(res.template);
                            }
                        }
                    } catch (e) {}
                },
                error: function() {
                    capturePollFailures++;
                    if (capturePollFailures >= 2) {
                        continuePolling = false;
                        updateUI("<i class='fa fa-warning'></i> Scanner reconnecting...", "error");
                        setTimeout(initScanner, 2000);
                    }
                },
                complete: function () {
                    captureRequestInFlight = false;
                    if (continuePolling) captureTimer = setTimeout(pollCapture, 15);
                }
            });
        }

        function verifyFingerprint(liveTemplate) {
            updateUI("<div class='loader'></div> VERIFYING...", "info");
            
            if (registeredTemplates.length === 0) {
                updateUI("<i class='fa fa-times'></i> No templates in DB.", "error");
                setTimeout(startCapture, 2000);
                return;
            }

            var verificationStarted = performance.now();
            var chunkIndex = 0;
            var totalChunks = precomputedChunks.length;

            function verifyChunk() {
                if (chunkIndex >= totalChunks) {
                    console.debug('Fingerprint verification failed after ' + Math.round(performance.now() - verificationStarted) + ' ms');
                    updateUI("<i class='fa fa-times'></i> Unrecognized Fingerprint.", "error");
                    setTimeout(function() {
                        updateUI("<i class='fa fa-crosshairs' style='color: #10b981; animation: pulse-opacity 2s infinite;'></i> READY TO SCAN...", "info");
                        startCapture();
                    }, 1200);
                    return;
                }

                var chunkObj = precomputedChunks[chunkIndex];
                var templatesChunk = chunkObj.templates;
                var templatesPayload = chunkObj.payload;

                $.ajax({
                    url: zkTecoUrl + "/VerifyMulti",
                    type: "POST",
                    data: { capTemplate: liveTemplate, regTemplates: templatesPayload },
                    timeout: 10000,
                    success: function (data) {
                        try {
                            var res = typeof data === 'string' ? JSON.parse(data) : data;
                            // Strict matching: verify match_index is within this exact chunk
                            if (res.ret == 0 && res.match_index >= 0 && templatesChunk[res.match_index]) {
                                console.debug('Strict verification matched in ' + Math.round(performance.now() - verificationStarted) + ' ms (Score: ' + res.score + ')');
                                var target = templatesChunk[res.match_index]; 
                                var targetId = target.biometric_id ? target.biometric_id : (target.RFTag_id ? target.RFTag_id : target.personnel_id);
                                logAttendanceToDatabase(targetId, target);
                            } else {
                                chunkIndex++;
                                verifyChunk();
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

            verifyChunk();
        }

        function renderEmployeeOverlay(imgUrl, nameText, flowText) {
            $('#overlayImg').attr('src', imgUrl || 'personnelImg/default_img.jpg');
            $('#overlayName').text(nameText || '');
            var displayedFlow = flowText || 'TIME IN';
            $('#overlayType').text(displayedFlow);
            
            if (displayedFlow.indexOf('IN') !== -1) {
                $('#overlayType').removeClass('bg-danger').addClass('bg-success');
            } else {
                $('#overlayType').removeClass('bg-success').addClass('bg-danger');
            }
            
            $('#iconContainer').hide();
            $('#empOverlay').fadeIn();
        }

        function logAttendanceToDatabase(targetId, matchedTarget) {
            updateUI("<i class='fa fa-spinner fa-spin'></i> Logging DTR...", "info");
            var loggingStarted = performance.now();
            $.ajax({
                url: 'process_biometric_dtr.php',
                type: 'POST',
                data: { RFTag_id: targetId, log_source: 'BIO' },
                timeout: 15000,
                success: function(responseStr) {
                    console.debug('DTR logging took ' + Math.round(performance.now() - loggingStarted) + ' ms');
                    var restartDelay = 1000;
                    try {
                        var response = JSON.parse(responseStr);
                        
                        if (response.status === 'success') {
                            updateUI("<i class='fa fa-check'></i> Logged successfully!", "success");
                            
                            // Successful punch
                            
                            if (response.sound === 'valid') {
                                PopupCenterValid();
                            } else {
                                PopupCenterInvalid();
                            }
                            
                            // Instant overlay display if response contains img and name
                            if (response.img && response.name) {
                                renderEmployeeOverlay(response.img, response.name, response.logFlow);
                            } else {
                                // Safe fallback: query get_latest_bio_log.php if fields are missing
                                $.ajax({
                                    url: 'get_latest_bio_log.php',
                                    type: 'GET',
                                    data: { RFTag_id: targetId },
                                    dataType: 'json',
                                    success: function(logData) {
                                        if (logData && logData.status == 'success') {
                                            renderEmployeeOverlay(logData.img, logData.name, response.logFlow || logData.logFlow);
                                        }
                                    }
                                });
                            }
                            
                        } else {
                            // Error from process script
                            updateUI("<i class='fa fa-times'></i> " + response.message, "error");
                            restartDelay = 1200;
                            if (response.sound === 'invalid') {
                                PopupCenterInvalid();
                            }
                        }
                    } catch (e) {
                        console.log("JSON Parse Error on response:", responseStr);
                        console.error(e);
                        updateUI("<i class='fa fa-times'></i> Scan Error", "error");
                        restartDelay = 1200;
                        PopupCenterInvalid();
                    }
                    
                    // Re-arm scanner rapidly so the next person in queue can scan immediately
                    setTimeout(() => {
                        updateUI("<i class='fa fa-crosshairs' style='color: #10b981; animation: pulse-opacity 2s infinite;'></i> READY TO SCAN...", "info");
                        startCapture();
                    }, Math.min(restartDelay, 250));

                    setTimeout(() => {
                        $('#empOverlay').hide();
                        $('#iconContainer').show();
                    }, restartDelay);
                },
                error: function(xhr, status) {
                    // The server may have saved the scan before the request timed out.
                    // Never automatically resubmit the same attendance request.
                    updateUI(status === 'timeout'
                        ? "<i class='fa fa-warning'></i> Attendance not confirmed. Check the latest log before rescanning."
                        : "<i class='fa fa-times'></i> Server Error. Check the latest log before rescanning.", "error");
                    setTimeout(startCapture, 5000);
                }
            });
        }

        $(document).ready(function() {
            initScanner();
        });
    </script>
</body>
</html>


