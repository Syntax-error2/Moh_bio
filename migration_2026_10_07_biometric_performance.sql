-- Run once on each MariaDB installation during a quiet period.
-- These indexes speed up personnel lookup and same-day scan checks.
ALTER TABLE personnels
    ADD INDEX IF NOT EXISTS idx_personnels_biometric_id (biometric_id);

ALTER TABLE bio_dtr
    ADD INDEX IF NOT EXISTS idx_bio_person_date_time (personnel_id, log_date, time_in);
