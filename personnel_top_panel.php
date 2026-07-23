<?php
$ptp_staff = $staff_row ?? [];
$ptp_shift = $es_row5 ?? [];

$ptp_img = !empty($ptp_staff['img']) ? (string)$ptp_staff['img'] : 'avatar-1.jpg';
$ptp_lname = trim((string)($ptp_staff['lname'] ?? ''));
$ptp_fname = trim((string)($ptp_staff['fname'] ?? ''));
$ptp_mi = !empty($ptp_staff['mname']) ? strtoupper(substr((string)$ptp_staff['mname'], 0, 1)) . '.' : '';
$ptp_suffix = (!empty($ptp_staff['suffix']) && (string)$ptp_staff['suffix'] !== '-') ? ' ' . trim((string)$ptp_staff['suffix']) : '';
$ptp_full_name = trim($ptp_lname . ', ' . $ptp_fname . ' ' . $ptp_mi . $ptp_suffix);
$ptp_id_code = !empty($ptp_staff['personnel_id_code']) ? (string)$ptp_staff['personnel_id_code'] : 'No ID';

$ptp_shift_name = !empty($ptp_shift['shift_name']) ? (string)$ptp_shift['shift_name'] : 'Shift not set';
$ptp_shift_type = !empty($ptp_shift['type']) ? (string)$ptp_shift['type'] : '';

if ($ptp_shift_type === '' && !empty($ptp_staff['shift_id'])) {
    $ptp_shift_stmt = $conn->prepare("SELECT shift_name, type FROM shifts WHERE shift_id = :shift_id LIMIT 1");
    $ptp_shift_stmt->execute([':shift_id' => (int)$ptp_staff['shift_id']]);
    $ptp_shift_row = $ptp_shift_stmt->fetch(PDO::FETCH_ASSOC);
    if ($ptp_shift_row) {
        $ptp_shift_name = !empty($ptp_shift_row['shift_name']) ? (string)$ptp_shift_row['shift_name'] : $ptp_shift_name;
        $ptp_shift_type = !empty($ptp_shift_row['type']) ? (string)$ptp_shift_row['type'] : '';
    }
}

$ptp_emp_status = '';
if (!empty($ptp_staff['empStat_id'])) {
    $ptp_status_stmt = $conn->prepare("SELECT emp_stat_name FROM emp_status WHERE empStat_id = :empStat_id LIMIT 1");
    $ptp_status_stmt->execute([':empStat_id' => (int)$ptp_staff['empStat_id']]);
    $ptp_status_row = $ptp_status_stmt->fetch(PDO::FETCH_ASSOC);
    if ($ptp_status_row && !empty($ptp_status_row['emp_stat_name'])) {
        $ptp_emp_status = (string)$ptp_status_row['emp_stat_name'];
    }
}

?>

<style>
.personnel-top-panel {
    border: 1px solid #d7dde8;
    background: #ffffff;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 12px;
}
.personnel-top-avatar {
    width: 62px;
    height: 62px;
    border-radius: 50%;
    border: 3px solid #24b15a;
    object-fit: cover;
}
.personnel-top-name {
    font-size: 24px;
    font-weight: 700;
    color: #152238;
    line-height: 1.2;
    margin-bottom: 6px;
}
.personnel-top-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.personnel-top-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    border: 1px solid;
    padding: 4px 10px;
    font-size: 13px;
    line-height: 1.2;
}
.personnel-badge-id {
    background: #eef5ff;
    color: #4a6fa8;
    border-color: #a9c1e4;
}
.personnel-badge-status {
    background: #eef4ff;
    color: #517fbe;
    border-color: #93b8ed;
}
.personnel-badge-shift {
    background: #eefaf2;
    color: #4f8d66;
    border-color: #9ad3ac;
}
.personnel-badge-shift-type {
    background: #fff8ea;
    color: #8c6832;
    border-color: #e2c998;
}
</style>

<div class="personnel-top-panel">
    <div class="d-flex align-items-center flex-wrap">
        <img src="personnelImg/<?php echo htmlspecialchars($ptp_img); ?>" alt="Personnel" class="personnel-top-avatar mr-3" />
        <div>
            <div class="personnel-top-name"><?php echo htmlspecialchars($ptp_full_name !== '' ? $ptp_full_name : 'Personnel'); ?></div>
            <div class="personnel-top-badges">
                <div class="personnel-top-badge personnel-badge-id"><i class="fa fa-id-card-o mr-1"></i> ID: <?php echo htmlspecialchars($ptp_id_code); ?></div>
                <?php if ($ptp_emp_status !== '') { ?>
                    <div class="personnel-top-badge personnel-badge-status"><i class="fa fa-clock-o mr-1"></i> <?php echo htmlspecialchars($ptp_emp_status); ?></div>
                <?php } ?>
                <div class="personnel-top-badge personnel-badge-shift"><i class="fa fa-briefcase mr-1"></i> <?php echo htmlspecialchars($ptp_shift_name); ?></div>
                <?php if ($ptp_shift_type !== '') { ?>
                    <div class="personnel-top-badge personnel-badge-shift-type"><i class="fa fa-calendar mr-1"></i> <?php echo htmlspecialchars($ptp_shift_type); ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
