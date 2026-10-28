-- Migration: Accelerate Time-In / Time-Out attendance verification and live feed queries
-- Created: 2026-10-20

ALTER TABLE bio_dtr 
    ADD INDEX IF NOT EXISTS idx_bio_person_date_id (personnel_id, log_date, bio_id);

ALTER TABLE bio_dtr 
    ADD INDEX IF NOT EXISTS idx_bio_date_time (log_date, time_in);
