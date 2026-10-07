<?php
// A report should read bio_dtr whenever a person has biometric rows, even if
// the roster's biometric_id field is empty on the receiving device.
function attendance_has_bio(PDO $conn, int $personnelId): bool {
    static $personnelWithBio = null;
    if ($personnelWithBio === null) {
        $ids = $conn->query('SELECT DISTINCT personnel_id FROM bio_dtr')->fetchAll(PDO::FETCH_COLUMN);
        $personnelWithBio = array_fill_keys($ids, true);
    }
    return isset($personnelWithBio[$personnelId]);
}
