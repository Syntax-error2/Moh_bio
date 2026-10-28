<?php
/**
 * MOB BIO - DTR Synchronization & Anti-Duplication Library
 * Supports infirmary-to-new-server synchronization, auto-export to spare folder,
 * and duplicate-proof importing.
 */

if (!defined('DTR_EXPORTS_DIR')) {
    define('DTR_EXPORTS_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'dtr_exports');
}
if (!defined('DTR_IMPORTED_DIR')) {
    define('DTR_IMPORTED_DIR', DTR_EXPORTS_DIR . DIRECTORY_SEPARATOR . 'imported');
}

/**
 * Ensure the spare export folders exist.
 */
function ensureDtrFoldersExist() {
    if (!is_dir(DTR_EXPORTS_DIR)) {
        @mkdir(DTR_EXPORTS_DIR, 0777, true);
    }
    if (!is_dir(DTR_IMPORTED_DIR)) {
        @mkdir(DTR_IMPORTED_DIR, 0777, true);
    }
}

/**
 * Build the export package from database.
 * Returns array with package info, json payload, and record counts.
 */
function buildDtrExportPackage($conn, $options = []) {
    $filterType = $options['filter_type'] ?? 'unexported';
    $onlyUnexported = !empty($options['only_unexported']) || $filterType === 'unexported';
    $forceIncludeExported = !empty($options['force_include_exported']);
    $username = $options['username'] ?? 'system_auto';

    $selectedMonth = isset($options['selected_month']) ? (int)$options['selected_month'] : (int)date('m');
    $selectedYear = isset($options['selected_year']) ? (int)$options['selected_year'] : (int)date('Y');

    $dateFrom = '';
    $dateTo = '';

    if ($filterType === 'first_15') {
        $dateFrom = sprintf('%04d-%02d-01', $selectedYear, $selectedMonth);
        $dateTo = sprintf('%04d-%02d-15', $selectedYear, $selectedMonth);
    } elseif ($filterType === 'second_15') {
        $dateFrom = sprintf('%04d-%02d-16', $selectedYear, $selectedMonth);
        $dateTo = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $selectedYear, $selectedMonth)));
    } elseif ($filterType === 'whole_month') {
        $dateFrom = sprintf('%04d-%02d-01', $selectedYear, $selectedMonth);
        $dateTo = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $selectedYear, $selectedMonth)));
    } elseif ($filterType === 'last_15_days') {
        $dateFrom = date('Y-m-d', strtotime('-15 days'));
        $dateTo = date('Y-m-d');
    } elseif ($filterType === 'custom') {
        $dateFrom = trim($options['date_from'] ?? '');
        $dateTo = trim($options['date_to'] ?? '');
    }

    // 1. Fetch bio_dtr records
    $bioSql = "SELECT b.bio_id, b.personnel_id, b.biometric_id, b.log_date, b.time_in, b.time_out, b.is_exported
               FROM bio_dtr b
               WHERE 1=1 ";
    $bioParams = [];

    if ($onlyUnexported && !$forceIncludeExported) {
        $bioSql .= " AND (b.is_exported = 0 OR b.is_exported IS NULL) ";
    }
    $bioSql .= " AND b.log_date IS NOT NULL AND b.log_date != '' AND (b.time_in != '00:00:00' OR b.time_out != '00:00:00') ";

    if (!empty($dateFrom) && !empty($dateTo)) {
        $bioSql .= " AND b.log_date BETWEEN :b_from AND :b_to ";
        $bioParams[':b_from'] = $dateFrom;
        $bioParams[':b_to'] = $dateTo;
    }

    if (!empty($options['limit']) && (int)$options['limit'] > 0) {
        $bioSql .= " LIMIT " . (int)$options['limit'];
    } else {
        $bioSql .= " ORDER BY b.log_date ASC, b.time_in ASC";
    }

    $bioStmt = $conn->prepare($bioSql);
    $bioStmt->execute($bioParams);
    $bioRecords = $bioStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch personnel_logs records (direct indexed scan without slow OR join)
    $logsSql = "SELECT pl.log_id, pl.RFTag_id, pl.img, pl.captured_img, pl.lname, pl.fname, pl.mname, pl.suffix,
                       pl.do_id, pl.shift_id, pl.logDate, pl.logTime, pl.logTime_sec, pl.late_status,
                       pl.logFlow, pl.client_ip, pl.remarks, pl.travel_leave_code, pl.ref_log_id, pl.is_exported
                FROM personnel_logs pl
                WHERE 1=1 ";
    $logsParams = [];

    if ($onlyUnexported && !$forceIncludeExported) {
        $logsSql .= " AND (pl.is_exported = 0 OR pl.is_exported IS NULL) ";
    }
    $logsSql .= " AND pl.logDate IS NOT NULL AND pl.logDate != '' AND (pl.logTime != '' OR pl.logFlow != '') ";

    if (!empty($dateFrom) && !empty($dateTo)) {
        $dateStrings = [];
        $curTs = strtotime($dateFrom);
        $endTs = strtotime($dateTo);
        $maxDays = 366;
        $dayCount = 0;
        while ($curTs <= $endTs && $dayCount < $maxDays) {
            $dateStrings[] = date('m/d/Y', $curTs);
            $dateStrings[] = date('Y-m-d', $curTs);
            $curTs = strtotime('+1 day', $curTs);
            $dayCount++;
        }
        $dateStrings = array_values(array_unique($dateStrings));
        
        if (!empty($dateStrings)) {
            $inPlaceholders = implode(',', array_fill(0, count($dateStrings), '?'));
            $logsSql .= " AND pl.logDate IN ($inPlaceholders) ";
            $logsParams = $dateStrings;
        }
    }

    if (!empty($options['limit']) && (int)$options['limit'] > 0) {
        $logsSql .= " LIMIT " . (int)$options['limit'];
    } else {
        $logsSql .= " ORDER BY pl.log_id ASC";
    }

    $logsStmt = $conn->prepare($logsSql);
    $logsStmt->execute($logsParams);
    $logRecords = $logsStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($bioRecords) && empty($logRecords)) {
        return [
            'success' => true,
            'is_empty' => true,
            'bio_count' => 0,
            'logs_count' => 0,
            'message' => 'No attendance records found matching export criteria.'
        ];
    }

    // High-speed in-memory enrichment of personnel metadata
    $pMapQuery = $conn->query("SELECT personnel_id, biometric_id, RFTag_id, personnel_id_code, lname, fname, mname, suffix FROM personnels");
    $pMapRows = $pMapQuery->fetchAll(PDO::FETCH_ASSOC);
    $mapByPid = [];
    $mapByTag = [];
    foreach ($pMapRows as $pr) {
        $mapByPid[(int)$pr['personnel_id']] = $pr;
        if (!empty($pr['RFTag_id'])) $mapByTag[trim($pr['RFTag_id'])] = $pr;
        if (!empty($pr['biometric_id'])) $mapByTag[trim($pr['biometric_id'])] = $pr;
    }

    foreach ($bioRecords as &$br) {
        $bPid = (int)($br['personnel_id'] ?? 0);
        if (isset($mapByPid[$bPid])) {
            $matchedP = $mapByPid[$bPid];
            $br['RFTag_id'] = $matchedP['RFTag_id'];
            $br['personnel_id_code'] = $matchedP['personnel_id_code'];
            $br['lname'] = $matchedP['lname'];
            $br['fname'] = $matchedP['fname'];
            $br['mname'] = $matchedP['mname'];
            $br['suffix'] = $matchedP['suffix'];
        }
    }
    unset($br);

    foreach ($logRecords as &$lr) {
        $lTag = trim($lr['RFTag_id'] ?? '');
        if ($lTag !== '' && isset($mapByTag[$lTag])) {
            $lr['biometric_id'] = $mapByTag[$lTag]['biometric_id'];
            $lr['personnel_id_code'] = $mapByTag[$lTag]['personnel_id_code'];
        }
    }
    unset($lr);

    // 3. Mark exported records with is_exported = 1 in database
    $bioIds = array_column($bioRecords, 'bio_id');
    $logIds = array_column($logRecords, 'log_id');
    $now = date('Y-m-d H:i:s');

    $conn->beginTransaction();
    try {
        if (!empty($bioIds)) {
            foreach (array_chunk($bioIds, 2000) as $chunk) {
                $placeholders = implode(',', array_fill(0, count($chunk), '?'));
                $updateBio = $conn->prepare("UPDATE bio_dtr SET is_exported = 1, exported_at = ? WHERE bio_id IN ($placeholders)");
                $updateBio->execute(array_merge([$now], $chunk));
            }
        }

        if (!empty($logIds)) {
            foreach (array_chunk($logIds, 2000) as $chunk) {
                $placeholders = implode(',', array_fill(0, count($chunk), '?'));
                $updateLogs = $conn->prepare("UPDATE personnel_logs SET is_exported = 1, exported_at = ? WHERE log_id IN ($placeholders)");
                $updateLogs->execute(array_merge([$now], $chunk));
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        throw $e;
    }

    // 4. Construct payload package
    $package = [
        'system' => 'mob_bio',
        'type' => 'dtr_sync_package',
        'version' => '1.0',
        'exported_at' => $now,
        'exported_by' => $username,
        'source_hostname' => gethostname(),
        'counts' => [
            'bio_dtr' => count($bioRecords),
            'personnel_logs' => count($logRecords)
        ],
        'filter_used' => [
            'filter_type' => $filterType,
            'from' => $dateFrom ?: 'All',
            'to' => $dateTo ?: 'All',
            'only_unexported' => $onlyUnexported
        ],
        'bio_dtr' => $bioRecords,
        'personnel_logs' => $logRecords
    ];

    $jsonPayload = json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $package['checksum'] = hash('sha256', $jsonPayload);
    $finalPayload = json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $filename = 'infirmary_dtr_' . date('Ymd_His') . '.dtr';

    return [
        'success' => true,
        'is_empty' => false,
        'filename' => $filename,
        'json' => $finalPayload,
        'package' => $package,
        'bio_count' => count($bioRecords),
        'logs_count' => count($logRecords),
        'exported_at' => $now
    ];
}

/**
 * Save package payload to the dedicated spare folder.
 */
function saveDtrPackageToSpareFolder($filename, $jsonPayload) {
    ensureDtrFoldersExist();
    $targetPath = DTR_EXPORTS_DIR . DIRECTORY_SEPARATOR . $filename;
    $written = file_put_contents($targetPath, $jsonPayload);
    return ($written !== false) ? $targetPath : false;
}

/**
 * List all .dtr files available in the spare folder (sorted newest first).
 */
function listSpareFolderFiles() {
    ensureDtrFoldersExist();
    $files = glob(DTR_EXPORTS_DIR . DIRECTORY_SEPARATOR . '*.dtr');
    if (!$files) return [];

    $list = [];
    foreach ($files as $filePath) {
        $basename = basename($filePath);
        $sizeBytes = filesize($filePath);
        $fileMtime = filemtime($filePath);
        $sizeFormatted = ($sizeBytes >= 1048576) 
            ? number_format($sizeBytes / 1048576, 2) . ' MB' 
            : number_format($sizeBytes / 1024, 1) . ' KB';

        // Read minimal header to get counts without loading entire body
        $bioCount = '-';
        $logsCount = '-';
        $exportedAt = date('Y-m-d H:i:s', $fileMtime);

        $fp = @fopen($filePath, 'r');
        if ($fp) {
            $headerSnippet = fread($fp, 2048);
            fclose($fp);
            if (preg_match('/"bio_dtr"\s*:\s*(\d+)/', $headerSnippet, $m1)) {
                $bioCount = (int)$m1[1];
            }
            if (preg_match('/"personnel_logs"\s*:\s*(\d+)/', $headerSnippet, $m2)) {
                $logsCount = (int)$m2[1];
            }
            if (preg_match('/"exported_at"\s*:\s*"([^"]+)"/', $headerSnippet, $m3)) {
                $exportedAt = $m3[1];
            }
        }

        $list[] = [
            'filename' => $basename,
            'filepath' => $filePath,
            'size' => $sizeFormatted,
            'size_bytes' => $sizeBytes,
            'mtime' => $fileMtime,
            'exported_at' => $exportedAt,
            'bio_count' => $bioCount,
            'logs_count' => $logsCount
        ];
    }

    usort($list, function($a, $b) {
        return $b['mtime'] - $a['mtime'];
    });

    return $list;
}

/**
 * High-performance employee resolver map.
 */
function loadPersonnelResolutionMaps($conn) {
    $pQuery = $conn->query("SELECT personnel_id, biometric_id, RFTag_id, personnel_id_code, lname, fname, mname, suffix, do_id, shift_id, img FROM personnels");
    $allPersonnel = $pQuery->fetchAll(PDO::FETCH_ASSOC);

    $cacheByBio = [];
    $cacheByRf = [];
    $cacheByCode = [];
    $cacheByName = [];

    foreach ($allPersonnel as $p) {
        if (!empty($p['biometric_id'])) $cacheByBio[trim($p['biometric_id'])] = $p;
        if (!empty($p['RFTag_id'])) $cacheByRf[trim($p['RFTag_id'])] = $p;
        if (!empty($p['personnel_id_code'])) $cacheByCode[trim($p['personnel_id_code'])] = $p;
        $fullName = strtoupper(trim(($p['lname'] ?? '') . '|' . ($p['fname'] ?? '')));
        if ($fullName !== '|') $cacheByName[$fullName] = $p;
    }

    return [
        'by_bio' => $cacheByBio,
        'by_rf' => $cacheByRf,
        'by_code' => $cacheByCode,
        'by_name' => $cacheByName
    ];
}

function resolvePersonnelRecord($row, &$maps) {
    $bio = trim($row['biometric_id'] ?? '');
    $rf = trim($row['RFTag_id'] ?? '');
    $code = trim($row['personnel_id_code'] ?? '');
    $name = strtoupper(trim(($row['lname'] ?? '') . '|' . ($row['fname'] ?? '')));

    if ($bio !== '' && $bio !== '0' && isset($maps['by_bio'][$bio])) return $maps['by_bio'][$bio];
    if ($rf !== '' && $rf !== '0' && isset($maps['by_rf'][$rf])) return $maps['by_rf'][$rf];
    if ($code !== '' && isset($maps['by_code'][$code])) return $maps['by_code'][$code];
    if ($name !== '|' && isset($maps['by_name'][$name])) return $maps['by_name'][$name];
    return null;
}

/**
 * Hardened, zero-duplicate DTR Import Engine.
 * Guarantees no duplicate records enter bio_dtr or personnel_logs,
 * while seamlessly merging open morning sessions with afternoon clock-outs.
 */
function processDtrImportPackage($conn, $packageData, &$maps = null) {
    if (is_string($packageData)) {
        $package = json_decode($packageData, true);
    } else {
        $package = $packageData;
    }

    if (!is_array($package) || empty($package['system']) || $package['system'] !== 'mob_bio' || empty($package['type']) || $package['type'] !== 'dtr_sync_package') {
        throw new InvalidArgumentException('Invalid file format. The file is not a valid MOB BIO DTR Sync Package (.dtr).');
    }

    if ($maps === null) {
        $maps = loadPersonnelResolutionMaps($conn);
    }

    $bioInserted = 0;
    $bioUpdated = 0;
    $bioSkipped = 0;

    $logsInserted = 0;
    $logsSkipped = 0;

    $unmatched = [];

    $conn->beginTransaction();
    try {
        // -------------------------------------------------------------
        // 1. Process bio_dtr records with comprehensive duplicate prevention
        // -------------------------------------------------------------
        $bioList = isset($package['bio_dtr']) && is_array($package['bio_dtr']) ? $package['bio_dtr'] : [];
        
        $stmtBioFind = $conn->prepare("SELECT bio_id, time_in, time_out FROM bio_dtr WHERE personnel_id = :pid AND log_date = :ldate ORDER BY bio_id ASC");
        $stmtBioUpdateOut = $conn->prepare("UPDATE bio_dtr SET time_out = :tout WHERE bio_id = :bid");
        $stmtBioUpdateIn = $conn->prepare("UPDATE bio_dtr SET time_in = :tin WHERE bio_id = :bid");
        $stmtBioUpdateBoth = $conn->prepare("UPDATE bio_dtr SET time_in = :tin, time_out = :tout WHERE bio_id = :bid");
        $stmtBioInsert = $conn->prepare("INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out, is_exported) VALUES (:pid, :bid, :ldate, :tin, :tout, 1)");

        foreach ($bioList as $b) {
            $p = resolvePersonnelRecord($b, $maps);
            if (!$p) {
                $identifier = $b['biometric_id'] ?: ($b['RFTag_id'] ?: ('PID ' . $b['personnel_id']));
                $name = trim(($b['lname'] ?? '') . ', ' . ($b['fname'] ?? ''));
                $unmatched[$identifier] = $name ?: 'Unknown Staff';
                continue;
            }

            $targetPid = (int)$p['personnel_id'];
            $targetBid = !empty($p['biometric_id']) ? $p['biometric_id'] : (!empty($p['RFTag_id']) ? $p['RFTag_id'] : $b['biometric_id']);
            $logDate = trim($b['log_date'] ?? '');
            $inTime = trim($b['time_in'] ?? '');
            $outTime = trim($b['time_out'] ?? '');

            if ($inTime === '') $inTime = '00:00:00';
            if ($outTime === '') $outTime = '00:00:00';

            // Skip invalid empty punches
            if (empty($logDate) || ($inTime === '00:00:00' && $outTime === '00:00:00')) {
                $bioSkipped++;
                continue;
            }

            $stmtBioFind->execute(['pid' => $targetPid, 'ldate' => $logDate]);
            $existingRows = $stmtBioFind->fetchAll(PDO::FETCH_ASSOC);

            $matched = false;
            if (!empty($existingRows)) {
                // Step A: Exact identical match (both time_in and time_out match)
                foreach ($existingRows as $ex) {
                    if ($ex['time_in'] === $inTime && $ex['time_out'] === $outTime) {
                        $matched = true;
                        $bioSkipped++;
                        break;
                    }
                }

                // Step B: Match by same non-zero time_in
                if (!$matched && $inTime !== '00:00:00') {
                    foreach ($existingRows as $ex) {
                        if ($ex['time_in'] === $inTime) {
                            $matched = true;
                            // If existing has open time_out (00:00:00) but import has completed time_out, update it!
                            if (($ex['time_out'] === '00:00:00' || empty($ex['time_out'])) && $outTime !== '00:00:00') {
                                $stmtBioUpdateOut->execute(['tout' => $outTime, 'bid' => $ex['bio_id']]);
                                $bioUpdated++;
                            } else {
                                // Already has a clock-out or incoming has no clock-out: duplicate clock-in skipped!
                                $bioSkipped++;
                            }
                            break;
                        }
                    }
                }

                // Step C: Match by same non-zero time_out (prevents duplicate afternoon punch entries)
                if (!$matched && $outTime !== '00:00:00') {
                    foreach ($existingRows as $ex) {
                        if ($ex['time_out'] === $outTime) {
                            $matched = true;
                            // If existing had missed morning IN (00:00:00) and import has valid morning IN, update time_in!
                            if (($ex['time_in'] === '00:00:00' || empty($ex['time_in'])) && $inTime !== '00:00:00') {
                                $stmtBioUpdateIn->execute(['tin' => $inTime, 'bid' => $ex['bio_id']]);
                                $bioUpdated++;
                            } else {
                                // Clock-out already recorded: duplicate clock-out skipped!
                                $bioSkipped++;
                            }
                            break;
                        }
                    }
                }

                // Step D: Incoming is clock-out only (inTime == '00:00:00') and existing has open morning session
                if (!$matched && $inTime === '00:00:00' && $outTime !== '00:00:00') {
                    foreach ($existingRows as $ex) {
                        if (($ex['time_out'] === '00:00:00' || empty($ex['time_out'])) && !empty($ex['time_in']) && $ex['time_in'] !== '00:00:00') {
                            $stmtBioUpdateOut->execute(['tout' => $outTime, 'bid' => $ex['bio_id']]);
                            $bioUpdated++;
                            $matched = true;
                            break;
                        }
                    }
                }

                // Step E: Incoming has both inTime and outTime, and server has an open session on that date
                if (!$matched && $inTime !== '00:00:00' && $outTime !== '00:00:00') {
                    foreach ($existingRows as $ex) {
                        if ($ex['time_out'] === '00:00:00' || empty($ex['time_out'])) {
                            // Close open session with completed time_out
                            $stmtBioUpdateBoth->execute(['tin' => $inTime, 'tout' => $outTime, 'bid' => $ex['bio_id']]);
                            $bioUpdated++;
                            $matched = true;
                            break;
                        }
                    }
                }
            }

            if (!$matched) {
                $stmtBioInsert->execute([
                    'pid' => $targetPid,
                    'bid' => $targetBid,
                    'ldate' => $logDate,
                    'tin' => $inTime,
                    'tout' => $outTime
                ]);
                $bioInserted++;
            }
        }

        // -------------------------------------------------------------
        // 2. Process personnel_logs records with strict duplicate prevention
        // -------------------------------------------------------------
        $logsList = isset($package['personnel_logs']) && is_array($package['personnel_logs']) ? $package['personnel_logs'] : [];

        $stmtInsertLog = $conn->prepare("INSERT INTO personnel_logs 
            (RFTag_id, img, captured_img, lname, fname, mname, suffix, do_id, shift_id, logDate, logTime, logTime_sec, late_status, logFlow, client_ip, remarks, travel_leave_code, ref_log_id, is_exported)
            VALUES 
            (:rftag, :img, :captured_img, :lname, :fname, :mname, :suffix, :doid, :shiftid, :ldate, :ltime, :ltimesec, :late_status, :lflow, :ip, :remarks, :travel_code, :ref_log_id, 1)");

        foreach ($logsList as $l) {
            $rawDate = trim($l['logDate'] ?? '');
            $rawTime = trim($l['logTime'] ?? '');
            $rawFlow = trim($l['logFlow'] ?? '');

            // Skip invalid empty / corrupt punch records
            if (empty($rawDate) || (empty($rawTime) && empty($rawFlow))) {
                $logsSkipped++;
                continue;
            }

            $p = resolvePersonnelRecord($l, $maps);
            if (!$p) {
                $identifier = $l['RFTag_id'] ?: ($l['biometric_id'] ?? 'Unknown');
                $name = trim(($l['lname'] ?? '') . ', ' . ($l['fname'] ?? ''));
                $unmatched[$identifier] = $name ?: 'Unknown Staff';
                continue;
            }

            $parsedDate = strtotime($l['logDate']);
            $logDateMdy = date('m/d/Y', $parsedDate);
            $logDateYmd = date('Y-m-d', $parsedDate);

            // Collect all identifiers for this person to prevent tag-mismatch duplicates
            $tagList = array_values(array_unique(array_filter([
                trim($p['RFTag_id'] ?? ''),
                trim($p['biometric_id'] ?? ''),
                trim($p['personnel_id_code'] ?? ''),
                trim($l['RFTag_id'] ?? '')
            ])));
            if (empty($tagList)) {
                $tagList = ['NO_VALID_TAG'];
            }

            $logTime = trim($l['logTime'] ?? '');
            $logTimeSec = (int)($l['logTime_sec'] ?? 0);
            if ($logTimeSec <= 0 && !empty($logTime)) {
                $calcSec = strtotime($logTime) - strtotime("today");
                if ($calcSec > 0) $logTimeSec = $calcSec;
            }
            $logFlow = trim($l['logFlow'] ?? '');

            $pLname = trim($p['lname'] ?? '');
            $pFname = trim($p['fname'] ?? '');

            // Duplicate Check A: If flow is defined (e.g. AM IN, AM OUT, PM IN, PM OUT), only 1 allowed per day per employee
            $isDuplicate = false;
            $inPlaceholders = implode(',', array_fill(0, count($tagList), '?'));

            if (!empty($logFlow)) {
                $sqlCheckFlow = "SELECT log_id FROM personnel_logs 
                                 WHERE (RFTag_id IN ($inPlaceholders) OR (lname = ? AND fname = ?))
                                   AND (logDate = ? OR logDate = ?)
                                   AND logFlow = ? LIMIT 1";
                $stmtCheckFlow = $conn->prepare($sqlCheckFlow);
                $stmtCheckFlow->execute(array_merge($tagList, [$pLname, $pFname, $logDateMdy, $logDateYmd, $logFlow]));
                if ($stmtCheckFlow->fetchColumn()) {
                    $isDuplicate = true;
                }
            }

            // Duplicate Check B: Timestamp-based check (same punch time or within 60 seconds on the same date)
            if (!$isDuplicate) {
                if ($logTimeSec > 0) {
                    $minSec = max(0, $logTimeSec - 60);
                    $maxSec = $logTimeSec + 60;
                    $sqlCheckTime = "SELECT log_id FROM personnel_logs 
                                     WHERE (RFTag_id IN ($inPlaceholders) OR (lname = ? AND fname = ?))
                                       AND (logDate = ? OR logDate = ?)
                                       AND ((logTime_sec BETWEEN ? AND ?) OR logTime = ?) LIMIT 1";
                    $stmtCheckTime = $conn->prepare($sqlCheckTime);
                    $stmtCheckTime->execute(array_merge($tagList, [$pLname, $pFname, $logDateMdy, $logDateYmd, $minSec, $maxSec, $logTime]));
                    if ($stmtCheckTime->fetchColumn()) {
                        $isDuplicate = true;
                    }
                } elseif (!empty($logTime)) {
                    $sqlCheckTime = "SELECT log_id FROM personnel_logs 
                                     WHERE (RFTag_id IN ($inPlaceholders) OR (lname = ? AND fname = ?))
                                       AND (logDate = ? OR logDate = ?)
                                       AND logTime = ? LIMIT 1";
                    $stmtCheckTime = $conn->prepare($sqlCheckTime);
                    $stmtCheckTime->execute(array_merge($tagList, [$pLname, $pFname, $logDateMdy, $logDateYmd, $logTime]));
                    if ($stmtCheckTime->fetchColumn()) {
                        $isDuplicate = true;
                    }
                }
            }

            if ($isDuplicate) {
                $logsSkipped++;
            } else {
                $targetTag = !empty($p['RFTag_id']) ? $p['RFTag_id'] : (!empty($p['biometric_id']) ? $p['biometric_id'] : $l['RFTag_id']);

                $stmtInsertLog->execute([
                    'rftag' => $targetTag,
                    'img' => $p['img'] ?: '',
                    'captured_img' => $l['captured_img'] ?? '',
                    'lname' => $p['lname'],
                    'fname' => $p['fname'],
                    'mname' => $p['mname'],
                    'suffix' => $p['suffix'],
                    'doid' => $p['do_id'],
                    'shiftid' => $p['shift_id'],
                    'ldate' => $logDateMdy,
                    'ltime' => $logTime,
                    'ltimesec' => $logTimeSec,
                    'late_status' => $l['late_status'] ?: 'off',
                    'lflow' => $logFlow,
                    'ip' => $l['client_ip'] ?: '127.0.0.1',
                    'remarks' => $l['remarks'] ?? '',
                    'travel_code' => $l['travel_leave_code'] ?? '',
                    'ref_log_id' => !empty($l['ref_log_id']) ? (int)$l['ref_log_id'] : 0
                ]);
                $logsInserted++;
            }
        }

        $conn->commit();

        return [
            'success' => true,
            'source_host' => $package['source_hostname'] ?? 'Infirmary',
            'exported_at' => $package['exported_at'] ?? 'Unknown',
            'bio_inserted' => $bioInserted,
            'bio_updated' => $bioUpdated,
            'bio_skipped' => $bioSkipped,
            'logs_inserted' => $logsInserted,
            'logs_skipped' => $logsSkipped,
            'unmatched' => $unmatched
        ];

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        throw $e;
    }
}
