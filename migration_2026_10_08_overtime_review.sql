-- Run once on the server after a database backup, during the maintenance window.
-- Existing date-only requests remain pending until an administrator reviews them.
ALTER TABLE personnel_overtime_requests
  ADD COLUMN IF NOT EXISTS status VARCHAR(16) NOT NULL DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS reviewed_by INT NULL,
  ADD COLUMN IF NOT EXISTS reviewed_at DATETIME NULL;

CREATE INDEX IF NOT EXISTS idx_ot_person_date_status
  ON personnel_overtime_requests (personnel_id, ot_date, status);
