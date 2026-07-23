<?php
include('dbcon.php');

function get_client_ip7() {
    $ipaddress7 = '';
    if (getenv('HTTP_CLIENT_IP')) {
        $ipaddress7 = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ipaddress7 = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')) {
        $ipaddress7 = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ipaddress7 = getenv('HTTP_FORWARDED_FOR');
    } elseif (getenv('HTTP_FORWARDED')) {
        $ipaddress7 = getenv('HTTP_FORWARDED');
    } elseif (getenv('REMOTE_ADDR')) {
        $ipaddress7 = getenv('REMOTE_ADDR');
    } else {
        $ipaddress7 = 'UNKNOWN';
    }
    return $ipaddress7;
}

if (get_client_ip7() == '::1') {
    $machine_ip7 = gethostbyname(trim(`hostname`));
} else {
    $machine_ip7 = get_client_ip7();
}

$news_stmt = $conn->prepare("SELECT news_title, news_contents FROM news WHERE ipAddress = :ipAddress");
$news_stmt->execute([':ipAddress' => $machine_ip7]);
$news_items = $news_stmt->fetchAll(PDO::FETCH_ASSOC);

$news_count = count($news_items);
$news_index = 0;

if ($news_count > 0) {
    $news_index = intdiv(time(), 15) % $news_count;
}

$active_news = ($news_count > 0) ? $news_items[$news_index] : ['news_title' => '', 'news_contents' => 'No announcement available.'];

$active_title = trim($active_news['news_title'] ?? '');
$active_content = trim($active_news['news_contents'] ?? '');

if ($active_content === '') {
    $active_content = 'No announcement available.';
}
?>

<div class="kiosk-idle-slider" style="padding: 14px; gap: 12px;">
    <div class="kiosk-idle-frame" style="background: #f3f6fa; display: flex; flex-direction: column; justify-content: center; padding: 20px 24px;">
        <?php if ($active_title !== '') { ?>
            <p style="margin: 0 0 10px; color: #1f2c38; font-size: 22px; font-weight: 700; line-height: 1.2;">
                <?php echo htmlspecialchars($active_title, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php } ?>

        <p style="margin: 0; color: #2a3642; font-size: 24px; font-weight: 500; line-height: 1.35;">
            <?php echo htmlspecialchars($active_content, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </div>

    <?php if ($news_count > 1) { ?>
        <div class="kiosk-idle-dots">
            <?php for ($i = 0; $i < $news_count; $i++) { ?>
                <span class="kiosk-idle-dot<?php echo ($i === $news_index) ? ' active' : ''; ?>"></span>
            <?php } ?>
        </div>
    <?php } ?>
</div>