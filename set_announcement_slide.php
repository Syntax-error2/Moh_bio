<?php
include('dbcon.php');

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')) {
        $ipaddress = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')) {
        $ipaddress = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    } elseif (getenv('HTTP_FORWARDED')) {
        $ipaddress = getenv('HTTP_FORWARDED');
    } elseif (getenv('REMOTE_ADDR')) {
        $ipaddress = getenv('REMOTE_ADDR');
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

if (get_client_ip() == '::1') {
    $machine_ip = gethostbyname(trim(`hostname`));
} else {
    $machine_ip = get_client_ip();
}

$slides_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM slides");
$slides_stmt->execute();
$slides_row = $slides_stmt->fetch(PDO::FETCH_ASSOC);
$slide_count = (int)($slides_row['total'] ?? 0);

if ($slide_count <= 0) {
    echo json_encode(['success' => false, 'message' => 'No slides available']);
    exit;
}

$cc_stmt = $conn->prepare("SELECT announcement_img FROM client_computer WHERE ipAddress = :ipAddress");
$cc_stmt->execute([':ipAddress' => $machine_ip]);
$cc_row = $cc_stmt->fetch(PDO::FETCH_ASSOC);

$announcement_img = max(0, (int)($cc_row['announcement_img'] ?? 0));
$current_idx = intdiv($announcement_img, 15);
if ($current_idx >= $slide_count) {
    $current_idx = $slide_count - 1;
}

$direction = $_POST['direction'] ?? '';
$index = isset($_POST['index']) ? (int)$_POST['index'] : null;

if ($direction === 'next') {
    $new_idx = ($current_idx + 1) % $slide_count;
} elseif ($direction === 'prev') {
    $new_idx = ($current_idx - 1 + $slide_count) % $slide_count;
} elseif ($index !== null && $index >= 0 && $index < $slide_count) {
    $new_idx = $index;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$new_announcement_img = $new_idx * 15;

$update_stmt = $conn->prepare("UPDATE client_computer SET announcement_img = :announcement_img WHERE ipAddress = :ipAddress");
$update_stmt->execute([':announcement_img' => $new_announcement_img, ':ipAddress' => $machine_ip]);

echo json_encode(['success' => true, 'slideIndex' => $new_idx]);
?>