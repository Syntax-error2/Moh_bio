<?php
// Backward-compatible redirect for legacy links.
header('Location: institutional_calendar.php?mm=' . urlencode($_GET['mm'] ?? date('m')) . '&yyyy=' . urlencode($_GET['yyyy'] ?? date('Y')));
exit;
?>
