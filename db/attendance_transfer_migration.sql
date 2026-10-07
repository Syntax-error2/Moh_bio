-- Run once on EACH installation (server and offline laptop) in the moh_bio database.
-- The same application code and schema are used on both devices.
USE moh_bio;
CREATE TABLE IF NOT EXISTS attendance_transfer_origins (
  origin_device CHAR(36) NOT NULL,
  source_table VARCHAR(20) NOT NULL,
  source_id BIGINT UNSIGNED NOT NULL,
  local_id BIGINT UNSIGNED NOT NULL,
  payload_hash CHAR(64) NOT NULL,
  imported_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (origin_device, source_table, source_id),
  KEY idx_local_record (source_table, local_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS attendance_transfer_batches (
  package_id CHAR(36) NOT NULL PRIMARY KEY,
  source_device CHAR(36) NOT NULL,
  imported_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  imported_by INT NOT NULL,
  bio_added INT NOT NULL,
  logs_added INT NOT NULL,
  existing_rows INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
