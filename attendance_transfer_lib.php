<?php
// Attendance transfer format v1. Both installations use this file unchanged.

function at_uuid(): string {
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 15) | 64);
    $bytes[8] = chr((ord($bytes[8]) & 63) | 128);
    $hex = bin2hex($bytes);
    return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20);
}

function at_device_id(): string {
    $path = __DIR__ . '/attendance_device_id.php';
    if (!is_file($path)) {
        $id = at_uuid();
        $handle = @fopen($path, 'x');
        if ($handle) {
            fwrite($handle, "<?php return '" . $id . "';\n");
            fclose($handle);
        }
    }
    $id = is_file($path) ? require $path : null;
    if (!is_string($id) || !preg_match('/^[0-9a-f-]{36}$/', $id)) {
        throw new RuntimeException('Cannot create this installation\'s device ID. Make the application folder writable.');
    }
    // A copied installation may contain the source device's local ID file.
    // Binding it to the computer name keeps the two installations distinct.
    $host = strtolower(trim((string)(gethostname() ?: php_uname('n'))));
    if ($host === '') { throw new RuntimeException('Cannot identify this computer.'); }
    $bytes = hash('sha256', $id . '|' . $host, true);
    $bytes[6] = chr((ord($bytes[6]) & 15) | 80);
    $bytes[8] = chr((ord($bytes[8]) & 63) | 128);
    $hex = bin2hex(substr($bytes, 0, 16));
    return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20);
}

function at_schema_ready(PDO $conn): bool {
    $stmt = $conn->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ('bio_dtr','attendance_transfer_origins','attendance_transfer_batches')");
    return (int)$stmt->fetchColumn() === 3;
}

function at_date(string $value): string {
    $value = trim($value);
    foreach (['!Y-m-d', '!m/d/Y', '!n/j/Y'] as $format) {
        $date = DateTimeImmutable::createFromFormat($format, $value);
        if ($date && $date->format(substr($format, 1)) === $value) {
            return $date->format('Y-m-d');
        }
    }
    throw new RuntimeException('Invalid attendance date: ' . $value);
}

function at_time($value): string {
    $value = (string)$value;
    if (!preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $value)) {
        throw new RuntimeException('Invalid attendance time.');
    }
    return $value;
}

function at_log_seconds(string $clock, $storedSeconds): int {
    $clock = trim($clock);
    if (preg_match('/^(0?[1-9]|1[0-2]):([0-5][0-9])(?::([0-5][0-9]))?\s*([ap]m)$/i', $clock, $parts)) {
        $hour = (int)$parts[1] % 12;
        if (strtolower($parts[4]) === 'pm') { $hour += 12; }
        return $hour * 3600 + (int)$parts[2] * 60 + (int)($parts[3] ?? 0);
    }
    if (preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])(?::([0-5][0-9]))?$/', $clock, $parts)) {
        return (int)$parts[1] * 3600 + (int)$parts[2] * 60 + (int)($parts[3] ?? 0);
    }
    if (filter_var($storedSeconds, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 86399]]) !== false) {
        return (int)$storedSeconds;
    }
    throw new RuntimeException('Cannot determine clock time for an attendance log.');
}

function at_hash(array $row): string {
    $fields = $row['table'] === 'bio_dtr'
        ? ['table','code','rfid','date','time_in','time_out']
        : ['table','code','rfid','date','seconds','flow','late','remarks','travel','image_hash'];
    $values = [];
    foreach ($fields as $field) {
        $values[] = (string)($row[$field] ?? '');
    }
    return hash('sha256', json_encode($values, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
}

function at_origin_maps(PDO $conn): array {
    $maps = ['bio_dtr' => [], 'personnel_logs' => []];
    foreach ($conn->query('SELECT origin_device, source_table, source_id, local_id FROM attendance_transfer_origins ORDER BY imported_at, origin_device') as $row) {
        if (isset($maps[$row['source_table']]) && !isset($maps[$row['source_table']][$row['local_id']])) {
            $maps[$row['source_table']][$row['local_id']] = [$row['origin_device'], (int)$row['source_id']];
        }
    }
    return $maps;
}

function at_export(PDO $conn, string $device, string $from, string $to): string {
    $maps = at_origin_maps($conn);
    $events = [];
    $images = [];
    $imageBytes = 0;
    $seenBio = $seenLogs = $seenSlots = [];
    $bio = $conn->prepare('SELECT b.*, p.personnel_id AS matched_personnel, p.RFTag_id AS rfid, p.personnel_id_code AS code FROM bio_dtr b LEFT JOIN personnels p ON p.personnel_id=b.personnel_id WHERE b.log_date BETWEEN ? AND ? ORDER BY b.bio_id');
    $bio->execute([$from, $to]);
    foreach ($bio as $source) {
        $id = (int)$source['bio_id'];
        if ($source['matched_personnel'] === null || (string)$source['rfid'] === '' || isset($seenBio[$id])) {
            throw new RuntimeException("Biometric row #{$id} has no unique personnel match. Export stopped without omitting it.");
        }
        $seenBio[$id] = true;
        $origin = $maps['bio_dtr'][$id] ?? [$device, $id];
        $events[] = [
            'table' => 'bio_dtr', 'device' => $origin[0], 'id' => $origin[1],
            'code' => (string)$source['code'], 'rfid' => (string)$source['rfid'],
            'date' => at_date($source['log_date']), 'time_in' => at_time($source['time_in'] ?? '00:00:00'),
            'time_out' => at_time($source['time_out'] ?? '00:00:00')
        ];
    }
    $logs = $conn->prepare("SELECT l.*, p.personnel_id AS matched_personnel, p.personnel_id_code AS code, p.RFTag_id AS rfid FROM personnel_logs l LEFT JOIN personnels p ON p.RFTag_id=l.RFTag_id WHERE l.logFlow IN ('AM IN','AM OUT','PM IN','PM OUT') AND (CASE WHEN l.logDate LIKE '%-%' THEN DATE(l.logDate) ELSE STR_TO_DATE(l.logDate,'%m/%d/%Y') END) BETWEEN ? AND ? ORDER BY l.log_id");
    $logs->execute([$from, $to]);
    foreach ($logs as $source) {
        $id = (int)$source['log_id'];
        if ($source['matched_personnel'] === null || (string)$source['rfid'] === '' || isset($seenLogs[$id])) {
            throw new RuntimeException("Attendance log #{$id} has no unique personnel match. Export stopped without omitting it.");
        }
        $seenLogs[$id] = true;
        $origin = $maps['personnel_logs'][$id] ?? [$device, $id];
        $date = at_date($source['logDate']);
        $seconds = at_log_seconds((string)$source['logTime'], $source['logTime_sec']);
        $slot = $source['rfid'] . '|' . $date . '|' . $source['logFlow'];
        if (isset($seenSlots[$slot]) && $seenSlots[$slot] !== $seconds) {
            throw new RuntimeException("Two different clock times exist for {$source['rfid']} on {$date} {$source['logFlow']}. Resolve these logs before export.");
        }
        $seenSlots[$slot] = $seconds;
        $event = [
            'table' => 'personnel_logs', 'device' => $origin[0], 'id' => $origin[1],
            'code' => (string)$source['code'], 'rfid' => (string)$source['rfid'],
            'date' => $date, 'seconds' => $seconds,
            'flow' => (string)$source['logFlow'], 'late' => (string)$source['late_status'],
            'remarks' => (string)$source['remarks'], 'travel' => (string)$source['travel_leave_code'],
            'image_hash' => '', 'image' => ''
        ];
        $imageName = basename((string)$source['captured_img']);
        $path = __DIR__ . '/upload/' . $imageName;
        if ($imageName !== '' && !is_file($path)) {
            throw new RuntimeException('Captured image is missing for log ' . $id);
        }
        if ($imageName !== '' && is_file($path)) {
            if (filesize($path) > 2 * 1024 * 1024 || !@getimagesize($path)) {
                throw new RuntimeException('Invalid or oversized captured image for log ' . $id);
            }
            $imageBytes += filesize($path);
            if ($imageBytes > 100 * 1024 * 1024) {
                throw new RuntimeException('Captured images exceed 100 MB. Select a shorter date range.');
            }
            $imagePath = 'images/' . $origin[0] . '_' . $origin[1] . '.img';
            $event['image'] = $imagePath;
            $event['image_hash'] = hash_file('sha256', $path);
            $images[$imagePath] = $path;
        }
        $events[] = $event;
    }
    if (count($events) > 10000 || count($images) > 2000) {
        throw new RuntimeException('Select a shorter date range (maximum 10,000 records and 2,000 images).');
    }
    $path = tempnam(sys_get_temp_dir(), 'attendance_export_');
    if ($path === false) { throw new RuntimeException('Unable to create a temporary export file.'); }
    $zip = new ZipArchive();
    if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
        @unlink($path);
        throw new RuntimeException('Unable to create export ZIP.');
    }
    try {
        $manifest = ['format' => 'moh-attendance-1', 'package_id' => at_uuid(), 'source_device' => $device,
            'created_at' => date(DATE_ATOM), 'from' => $from, 'to' => $to, 'events' => $events];
        if (!$zip->addFromString('manifest.json', json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
            throw new RuntimeException('Could not add manifest to the ZIP.');
        }
        foreach ($images as $name => $file) {
            if (!$zip->addFile($file, $name)) {
                throw new RuntimeException('Could not add a captured image to the ZIP.');
            }
        }
    } catch (Throwable $error) {
        $zip->close();
        @unlink($path);
        throw $error;
    }
    if (!$zip->close() || filesize($path) > 30 * 1024 * 1024) {
        @unlink($path);
        throw new RuntimeException('Export ZIP could not be finalized or exceeds 30 MB. Select a shorter date range.');
    }
    return $path;
}

function at_read_package(string $path): array {
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('This is not a valid attendance ZIP.');
    }
    try {
        if ($zip->numFiles > 2001) {
            throw new RuntimeException('Too many files in the ZIP.');
        }
        $stat = $zip->statName('manifest.json');
        if (!$stat || $stat['size'] > 10 * 1024 * 1024) {
            throw new RuntimeException('Missing or oversized manifest.json.');
        }
        $data = json_decode($zip->getFromName('manifest.json'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || ($data['format'] ?? '') !== 'moh-attendance-1' ||
            !preg_match('/^[0-9a-f-]{36}$/', (string)($data['package_id'] ?? '')) ||
            !preg_match('/^[0-9a-f-]{36}$/', (string)($data['source_device'] ?? '')) ||
            !isset($data['events']) || !is_array($data['events']) || count($data['events']) > 10000) {
            throw new RuntimeException('Unsupported or malformed attendance package.');
        }
        $data['from'] = at_date((string)($data['from'] ?? ''));
        $data['to'] = at_date((string)($data['to'] ?? ''));
        if ($data['from'] > $data['to']) {
            throw new RuntimeException('Invalid attendance package date range.');
        }
        $seen = [];
        $totalImageBytes = 0;
        foreach ($data['events'] as &$event) {
            if (!is_array($event) || !in_array($event['table'] ?? '', ['bio_dtr', 'personnel_logs'], true) ||
                !preg_match('/^[0-9a-f-]{36}$/', (string)($event['device'] ?? '')) ||
                !filter_var($event['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ||
                strlen((string)($event['code'] ?? '')) > 25 || strlen((string)($event['rfid'] ?? '')) > 25 ||
                (string)($event['rfid'] ?? '') === '') {
                throw new RuntimeException('Invalid record identity in attendance package.');
            }
            $key = $event['device'] . ':' . $event['table'] . ':' . $event['id'];
            if (isset($seen[$key])) {
                throw new RuntimeException('Duplicate source record within package.');
            }
            $seen[$key] = true;
            $event['date'] = at_date((string)($event['date'] ?? ''));
            if ($event['date'] < $data['from'] || $event['date'] > $data['to']) {
                throw new RuntimeException('A record falls outside the exported date range.');
            }
            if ($event['table'] === 'bio_dtr') {
                $event['time_in'] = at_time($event['time_in'] ?? '');
                $event['time_out'] = at_time($event['time_out'] ?? '');
            } else {
                if (!filter_var($event['seconds'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 86399]]) && ($event['seconds'] ?? null) !== 0) {
                    throw new RuntimeException('Invalid log time.');
                }
                if (!in_array($event['flow'] ?? '', ['AM IN','AM OUT','PM IN','PM OUT'], true) ||
                    strlen((string)($event['late'] ?? '')) > 3 || strlen((string)($event['remarks'] ?? '')) > 55 ||
                    strlen((string)($event['travel'] ?? '')) > 55) {
                    throw new RuntimeException('Invalid attendance log fields.');
                }
                $image = (string)($event['image'] ?? '');
                if ($image !== '') {
                    if (!preg_match('/^images\/[0-9a-f-]{36}_[1-9][0-9]*\.img$/', $image) ||
                        !preg_match('/^[0-9a-f]{64}$/', (string)($event['image_hash'] ?? ''))) {
                        throw new RuntimeException('Invalid image reference.');
                    }
                    $stat = $zip->statName($image);
                    if (!$stat || $stat['size'] > 2 * 1024 * 1024) {
                        throw new RuntimeException('Missing or oversized captured image.');
                    }
                    $totalImageBytes += $stat['size'];
                    if ($totalImageBytes > 100 * 1024 * 1024) {
                        throw new RuntimeException('Captured images exceed 100 MB.');
                    }
                    $bytes = $zip->getFromName($image);
                    if (hash('sha256', $bytes) !== $event['image_hash'] || !@getimagesizefromstring($bytes)) {
                        throw new RuntimeException('Captured image failed verification.');
                    }
                } elseif (($event['image_hash'] ?? '') !== '') {
                    throw new RuntimeException('Image hash without image.');
                }
            }
        }
        unset($event);
        return $data;
    } finally {
        $zip->close();
    }
}

function at_plan(PDO $conn, array $package): array {
    $people = $conn->query('SELECT personnel_id, personnel_id_code, RFTag_id, biometric_id, img, lname, fname, mname, suffix, do_id, shift_id FROM personnels')->fetchAll(PDO::FETCH_ASSOC);
    $byCode = $byRfid = [];
    foreach ($people as $person) {
        if ($person['personnel_id_code'] !== '') {
            $byCode[$person['personnel_id_code']][] = $person;
        }
        if ($person['RFTag_id'] !== '') {
            $byRfid[$person['RFTag_id']][] = $person;
        }
    }
    $findOrigin = $conn->prepare('SELECT local_id, payload_hash FROM attendance_transfer_origins WHERE origin_device=? AND source_table=? AND source_id=?');
    $findBio = $conn->prepare('SELECT bio_id FROM bio_dtr WHERE personnel_id=? AND log_date=? AND COALESCE(time_in,\'00:00:00\')=? AND COALESCE(time_out,\'00:00:00\')=? LIMIT 1');
    $findLogSlot = $conn->prepare("SELECT log_id, logTime, logTime_sec FROM personnel_logs WHERE RFTag_id=? AND logFlow=? AND ((logDate LIKE '%-%' AND DATE(logDate)=?) OR (logDate NOT LIKE '%-%' AND STR_TO_DATE(logDate,'%m/%d/%Y')=?))");
    $oldBio = $conn->prepare('SELECT personnel_id, log_date, time_in, time_out FROM bio_dtr WHERE bio_id=?');
    $oldLog = $conn->prepare('SELECT RFTag_id, logDate, logFlow, logTime, logTime_sec FROM personnel_logs WHERE log_id=?');
    $plan = ['add_bio' => 0, 'add_logs' => 0, 'update_bio' => 0, 'existing' => 0, 'conflicts' => [], 'actions' => []];
    $pendingNatural = [];
    $pendingSlots = [];
    foreach ($package['events'] as $event) {
        $code = (string)$event['code'];
        $rfid = (string)$event['rfid'];
        $matches = $byRfid[$rfid] ?? [];
        if (count($matches) !== 1 || ($code !== '' && (count($byCode[$code] ?? []) !== 1 || $byCode[$code][0]['personnel_id'] !== $matches[0]['personnel_id']))) {
            $plan['conflicts'][] = "Personnel mismatch: RFID {$rfid}, code {$code}.";
            continue;
        }
        $person = $matches[0];
        $hash = at_hash($event);
        $findOrigin->execute([$event['device'], $event['table'], $event['id']]);
        $origin = $findOrigin->fetch(PDO::FETCH_ASSOC);
        if ($origin) {
            if (hash_equals($origin['payload_hash'], $hash)) {
                if ($event['table'] === 'bio_dtr') {
                    $oldBio->execute([$origin['local_id']]);
                    $old = $oldBio->fetch(PDO::FETCH_ASSOC);
                    $intact = $old && (int)$old['personnel_id'] === (int)$person['personnel_id'] && $old['log_date'] === $event['date'] &&
                        ($old['time_in'] ?? '00:00:00') === $event['time_in'] && ($old['time_out'] ?? '00:00:00') === $event['time_out'];
                } else {
                    $oldLog->execute([$origin['local_id']]);
                    $old = $oldLog->fetch(PDO::FETCH_ASSOC);
                    $intact = $old && $old['RFTag_id'] === $person['RFTag_id'] && at_date($old['logDate']) === $event['date'] &&
                        $old['logFlow'] === $event['flow'] && at_log_seconds($old['logTime'], $old['logTime_sec']) === (int)$event['seconds'];
                }
                if ($intact) { ++$plan['existing']; }
                else { $plan['conflicts'][] = "Local copy of {$event['table']} #{$event['id']} was changed or removed."; }
                continue;
            }
            if ($event['table'] === 'bio_dtr') {
                $oldBio->execute([$origin['local_id']]);
                $old = $oldBio->fetch(PDO::FETCH_ASSOC);
                if ($old && (int)$old['personnel_id'] === (int)$person['personnel_id'] && $old['log_date'] === $event['date'] &&
                    ($old['time_in'] ?? '00:00:00') === $event['time_in'] && $event['time_out'] === '00:00:00' &&
                    ($old['time_out'] ?? '00:00:00') !== '00:00:00') {
                    $completedEvent = $event;
                    $completedEvent['time_out'] = $old['time_out'];
                    if (hash_equals($origin['payload_hash'], at_hash($completedEvent))) {
                        ++$plan['existing'];
                        continue;
                    }
                }
                if ($old && (int)$old['personnel_id'] === (int)$person['personnel_id'] && $old['log_date'] === $event['date'] &&
                    ($old['time_in'] ?? '00:00:00') === $event['time_in'] &&
                    ($old['time_out'] === null || $old['time_out'] === '00:00:00') && $event['time_out'] !== '00:00:00') {
                    $oldEvent = $event;
                    $oldEvent['time_out'] = '00:00:00';
                    if (hash_equals($origin['payload_hash'], at_hash($oldEvent))) {
                        ++$plan['update_bio'];
                        $plan['actions'][] = ['update_bio', $event, $person, (int)$origin['local_id'], $hash];
                        continue;
                    }
                }
            }
            $plan['conflicts'][] = "Changed source record {$event['table']} #{$event['id']} from {$event['device']}.";
            continue;
        }
        if ($event['table'] === 'bio_dtr') {
            $naturalKey = implode('|', ['bio', $person['personnel_id'], $event['date'], $event['time_in'], $event['time_out']]);
            $findBio->execute([$person['personnel_id'], $event['date'], $event['time_in'], $event['time_out']]);
            $same = $findBio->fetchColumn();
        } else {
            $naturalKey = implode('|', ['log', $person['personnel_id'], $event['date'], $event['flow'], $event['seconds']]);
            $slotKey = implode('|', [$person['personnel_id'], $event['date'], $event['flow']]);
            if (isset($pendingSlots[$slotKey]) && $pendingSlots[$slotKey] !== (int)$event['seconds']) {
                $plan['conflicts'][] = "Two different times for {$rfid} on {$event['date']} {$event['flow']} in this package.";
                continue;
            }
            $findLogSlot->execute([$person['RFTag_id'], $event['flow'], $event['date'], $event['date']]);
            $slotRows = $findLogSlot->fetchAll(PDO::FETCH_ASSOC);
            $matchingLog = false;
            $differentLog = false;
            foreach ($slotRows as $slotRow) {
                if (at_log_seconds($slotRow['logTime'], $slotRow['logTime_sec']) === (int)$event['seconds']) {
                    $matchingLog = (int)$slotRow['log_id'];
                } else {
                    $differentLog = true;
                }
            }
            if ($differentLog) {
                $plan['conflicts'][] = "Different existing time for {$rfid} on {$event['date']} {$event['flow']}.";
                continue;
            }
            $same = $matchingLog;
        }
        if ($same !== false || isset($pendingNatural[$naturalKey])) {
            ++$plan['existing'];
            continue;
        }
        $pendingNatural[$naturalKey] = true;
        if ($event['table'] === 'personnel_logs') {
            $pendingSlots[$slotKey] = (int)$event['seconds'];
        }
        ++$plan[$event['table'] === 'bio_dtr' ? 'add_bio' : 'add_logs'];
        $plan['actions'][] = ['insert', $event, $person, 0, $hash];
    }
    return $plan;
}

function at_import(PDO $conn, string $path, array $package, int $userId): array {
    $createdImages = [];
    $conn->beginTransaction();
    try {
        $plan = at_plan($conn, $package);
        if ($plan['conflicts']) {
            throw new RuntimeException('Import stopped: resolve the personnel or changed-record conflicts shown in the preview.');
        }
        $insertBio = $conn->prepare('INSERT INTO bio_dtr (personnel_id, biometric_id, log_date, time_in, time_out) VALUES (?,?,?,?,?)');
        $updateBio = $conn->prepare('UPDATE bio_dtr SET time_out=? WHERE bio_id=? AND (time_out IS NULL OR time_out=\'00:00:00\')');
        $insertLog = $conn->prepare('INSERT INTO personnel_logs (RFTag_id,img,captured_img,lname,fname,mname,suffix,do_id,shift_id,logDate,logTime,logTime_sec,late_status,logFlow,client_ip,remarks,travel_leave_code,ref_log_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0)');
        $insertOrigin = $conn->prepare('INSERT INTO attendance_transfer_origins (origin_device,source_table,source_id,local_id,payload_hash) VALUES (?,?,?,?,?)');
        $updateOrigin = $conn->prepare('UPDATE attendance_transfer_origins SET payload_hash=? WHERE origin_device=? AND source_table=? AND source_id=?');
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Cannot reopen the attendance ZIP.');
        }
        try {
            foreach ($plan['actions'] as [$action, $event, $person, $localId, $hash]) {
                if ($action === 'update_bio') {
                    $updateBio->execute([$event['time_out'], $localId]);
                    if ($updateBio->rowCount() !== 1) {
                        throw new RuntimeException('A biometric row changed while importing. Try again.');
                    }
                    $updateOrigin->execute([$hash, $event['device'], $event['table'], $event['id']]);
                    continue;
                }
                if ($event['table'] === 'bio_dtr') {
                    $insertBio->execute([$person['personnel_id'], $person['biometric_id'] ?: $person['RFTag_id'], $event['date'], $event['time_in'], $event['time_out']]);
                } else {
                    $imageFile = '';
                    if (($event['image'] ?? '') !== '') {
                        $bytes = $zip->getFromName($event['image']);
                        $info = @getimagesizefromstring($bytes);
                        $extensions = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'];
                        $extension = $extensions[$info[2] ?? 0] ?? null;
                        if (!$extension || hash('sha256', $bytes) !== $event['image_hash']) {
                            throw new RuntimeException('Captured image changed during import.');
                        }
                        $imageFile = 'attendance_' . bin2hex(random_bytes(16)) . '.' . $extension;
                        $target = __DIR__ . '/upload/' . $imageFile;
                        $createdImages[] = $target;
                        if (file_put_contents($target, $bytes, LOCK_EX) !== strlen($bytes)) {
                            throw new RuntimeException('Cannot save a captured image.');
                        }
                    }
                    $seconds = (int)$event['seconds'];
                    $clock = gmdate('h:i:s a', $seconds);
                    $insertLog->execute([$person['RFTag_id'], $person['img'], $imageFile, $person['lname'], $person['fname'], $person['mname'], $person['suffix'], $person['do_id'], $person['shift_id'], date('m/d/Y', strtotime($event['date'])), $clock, $seconds, $event['late'], $event['flow'], '', $event['remarks'], $event['travel']]);
                }
                $localId = (int)$conn->lastInsertId();
                $insertOrigin->execute([$event['device'], $event['table'], $event['id'], $localId, $hash]);
            }
            $batch = $conn->prepare('INSERT IGNORE INTO attendance_transfer_batches (package_id,source_device,imported_by,bio_added,logs_added,existing_rows) VALUES (?,?,?,?,?,?)');
            $batch->execute([$package['package_id'], $package['source_device'], $userId, $plan['add_bio'], $plan['add_logs'], $plan['existing']]);
            $conn->commit();
            return $plan;
        } finally {
            $zip->close();
        }
    } catch (Throwable $error) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        foreach ($createdImages as $image) {
            @unlink($image);
        }
        throw $error;
    }
}
