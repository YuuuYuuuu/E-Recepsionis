-- Live chat + Socket.io (referensi SQL manual).
-- Disarankan pakai migrasi idempoten:
--   php migrations/ensure_latest_schema.php
-- (atau buka migrations/ensure_latest_schema.php dari browser localhost)
--
-- Jika tetap pakai file ini: jalankan hanya pada DB yang belum punya kolom/tabel di bawah.
-- Duplikat kolom akan error — abaikan atau hapus baris yang sudah terpasang.

USE recepsionis_db;

ALTER TABLE staff_calls
  ADD COLUMN assigned_user_id INT NULL,
  ADD COLUMN assigned_by INT NULL,
  ADD COLUMN assigned_at TIMESTAMP NULL DEFAULT NULL,
  ADD COLUMN live_session_id VARCHAR(64) NULL,
  ADD COLUMN live_status ENUM('waiting','active','ended') NULL DEFAULT NULL,
  ADD INDEX idx_staff_calls_assigned_user (assigned_user_id),
  ADD INDEX idx_staff_calls_assigned_by (assigned_by),
  ADD INDEX idx_staff_calls_live_session (live_session_id);

CREATE TABLE IF NOT EXISTS live_chat_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  live_session_id VARCHAR(64) NOT NULL,
  staff_call_id INT NULL,
  sender ENUM('guest','admin') NOT NULL,
  admin_user_id INT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_session (live_session_id),
  INDEX idx_staff_call (staff_call_id),
  CONSTRAINT fk_lcm_staff_call FOREIGN KEY (staff_call_id) REFERENCES staff_calls(id) ON DELETE SET NULL,
  CONSTRAINT fk_lcm_admin_user FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_category_routing (
  user_id INT NOT NULL,
  category_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, category_id),
  CONSTRAINT fk_acr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_acr_cat FOREIGN KEY (category_id) REFERENCES complaint_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_notification_preferences (
  user_id INT NOT NULL,
  sound_enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_anp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS staff_call_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_call_id INT NOT NULL,
  event_type VARCHAR(50) NOT NULL,
  actor_user_id INT NULL,
  target_user_id INT NULL,
  category_id INT NULL,
  notes TEXT NULL,
  metadata_json LONGTEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_scl_staff_call (staff_call_id),
  INDEX idx_scl_event_type (event_type),
  INDEX idx_scl_actor (actor_user_id),
  INDEX idx_scl_target (target_user_id),
  CONSTRAINT fk_scl_staff_call FOREIGN KEY (staff_call_id) REFERENCES staff_calls(id) ON DELETE CASCADE,
  CONSTRAINT fk_scl_actor_user FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_scl_target_user FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_scl_category FOREIGN KEY (category_id) REFERENCES complaint_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS live_chat_admin_state (
  live_session_id VARCHAR(64) NOT NULL,
  admin_user_id INT NOT NULL,
  last_read_message_id INT NOT NULL DEFAULT 0,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (live_session_id, admin_user_id),
  INDEX idx_lcas_admin (admin_user_id),
  CONSTRAINT fk_lcas_admin_user FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
