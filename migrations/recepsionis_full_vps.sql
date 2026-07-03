-- =============================================================================
-- E-RECEPSIONIS — skema database lengkap untuk migrasi ke VPS (fresh install)
-- =============================================================================
-- Cara pakai (contoh):
--   mysql -u USER -p < migrations/recepsionis_full_vps.sql
-- Atau di phpMyAdmin: Import file ini.
--
-- Setelah import, sesuaikan kredensial di koneksi.php (host, user, pass, dbname).
-- Default admin: username admin / password admin123  (ganti segera di produksi)
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `recepsionis_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `recepsionis_db`;

-- -----------------------------------------------------------------------------
-- Hapus tabel (urutan aman untuk FK) — hanya untuk database BARU / full reset
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `staff_calls`;
DROP TABLE IF EXISTS `queue`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `visitors`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `complaint_categories`;
DROP TABLE IF EXISTS `prodi`;
DROP TABLE IF EXISTS `hosts`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `settings`;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- USERS
-- =============================================================================
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `role` ENUM('admin', 'operator') DEFAULT 'operator',
    `status_aktif` TINYINT(1) DEFAULT 1,
    `last_login` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- HOSTS
-- =============================================================================
CREATE TABLE `hosts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `no_telp` VARCHAR(20) DEFAULT NULL,
    `departemen` VARCHAR(100) DEFAULT NULL,
    `jabatan` VARCHAR(100) DEFAULT NULL,
    `status_aktif` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- SETTINGS (thumbnail + WA + umum — kolom extended untuk kompatibilitas kode)
-- =============================================================================
CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` LONGTEXT,
    `setting_type` VARCHAR(50) DEFAULT 'string',
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- COMPLAINT CATEGORIES (kategori panggilan staff)
-- =============================================================================
CREATE TABLE `complaint_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_kategori` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT,
    `icon` VARCHAR(50) DEFAULT 'bi-tag',
    `warna` VARCHAR(20) DEFAULT '#667eea',
    `status_aktif` TINYINT(1) DEFAULT 1,
    `urutan` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- ROOMS
-- =============================================================================
CREATE TABLE `rooms` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_ruangan` VARCHAR(200) NOT NULL,
    `kode_ruangan` VARCHAR(100) NOT NULL UNIQUE,
    `lokasi` VARCHAR(255) DEFAULT NULL,
    `lantai` VARCHAR(50) DEFAULT NULL,
    `gedung` VARCHAR(100) DEFAULT NULL,
    `kapasitas` INT DEFAULT 0,
    `deskripsi` TEXT,
    `foto` VARCHAR(255) DEFAULT NULL,
    `images` TEXT DEFAULT NULL,
    `perangkat` TEXT DEFAULT NULL,
    `mode_ruangan` VARCHAR(100) DEFAULT NULL,
    `denah_pin_x` DECIMAL(5,2) NULL DEFAULT NULL,
    `denah_pin_y` DECIMAL(5,2) NULL DEFAULT NULL,
    `status_aktif` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- FLOOR PLANS (denah lokasi per gedung + lantai)
-- =============================================================================
CREATE TABLE `floor_plans` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gedung` VARCHAR(100) NOT NULL,
    `lantai` VARCHAR(50) NOT NULL,
    `gambar` VARCHAR(255) NOT NULL,
    `resepsionis_x` DECIMAL(5,2) NULL DEFAULT NULL,
    `resepsionis_y` DECIMAL(5,2) NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_floor_plans_gedung_lantai` (`gedung`, `lantai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- PROGRAMS (jadwal / acara)
-- =============================================================================
CREATE TABLE `programs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `judul` VARCHAR(200) NOT NULL,
    `deskripsi` TEXT,
    `kategori` ENUM('Perkuliahan', 'Seminar', 'Workshop', 'Event', 'Lainnya') DEFAULT 'Perkuliahan',
    `tanggal` DATE DEFAULT NULL,
    `waktu_mulai` TIME DEFAULT NULL,
    `waktu_selesai` TIME DEFAULT NULL,
    `lokasi` VARCHAR(200) DEFAULT NULL,
    `pengajar` VARCHAR(100) DEFAULT NULL,
    `kontak` VARCHAR(100) DEFAULT NULL,
    `status_aktif` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- PRODI
-- =============================================================================
CREATE TABLE `prodi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_prodi` VARCHAR(200) NOT NULL,
    `kode_prodi` VARCHAR(50) DEFAULT NULL UNIQUE,
    `penjelasan` TEXT,
    `kontak_person` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `no_telp` VARCHAR(20) DEFAULT NULL,
    `direct_link` VARCHAR(500) DEFAULT NULL,
    `fakultas` VARCHAR(100) DEFAULT NULL,
    `jenjang` ENUM('D3', 'S1', 'S2', 'S3') DEFAULT 'S1',
    `foto` VARCHAR(255) DEFAULT NULL,
    `status_aktif` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- VISITORS
-- =============================================================================
CREATE TABLE `visitors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `no_telp` VARCHAR(20) DEFAULT NULL,
    `perusahaan` VARCHAR(200) DEFAULT NULL,
    `foto` VARCHAR(255) DEFAULT NULL,
    `tujuan` TEXT,
    `host_id` INT DEFAULT NULL,
    `status` ENUM('pending', 'checked-in', 'checked-out') DEFAULT 'pending',
    `checkin_time` TIMESTAMP NULL DEFAULT NULL,
    `checkout_time` TIMESTAMP NULL DEFAULT NULL,
    `badge_number` VARCHAR(20) DEFAULT NULL UNIQUE,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_visitors_host` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_badge` (`badge_number`),
    INDEX `idx_host` (`host_id`),
    INDEX `idx_checkin` (`checkin_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- APPOINTMENTS
-- =============================================================================
CREATE TABLE `appointments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `visitor_id` INT DEFAULT NULL,
    `host_id` INT NOT NULL,
    `nama_visitor` VARCHAR(100) NOT NULL,
    `email_visitor` VARCHAR(100) DEFAULT NULL,
    `no_telp_visitor` VARCHAR(20) DEFAULT NULL,
    `perusahaan_visitor` VARCHAR(200) DEFAULT NULL,
    `tanggal` DATE NOT NULL,
    `waktu_mulai` TIME NOT NULL,
    `waktu_selesai` TIME NOT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    `deskripsi` TEXT,
    `reminder_sent` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_appt_visitor` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_appt_host` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE,
    INDEX `idx_host` (`host_id`),
    INDEX `idx_tanggal` (`tanggal`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- QUEUE
-- =============================================================================
CREATE TABLE `queue` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `visitor_id` INT NOT NULL,
    `host_id` INT NOT NULL,
    `nomor_antrian` VARCHAR(20) NOT NULL,
    `status` ENUM('waiting', 'in-progress', 'completed', 'cancelled') DEFAULT 'waiting',
    `waktu_masuk` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `waktu_dipanggil` TIMESTAMP NULL DEFAULT NULL,
    `waktu_selesai` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_queue_visitor` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_queue_host` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE,
    INDEX `idx_host` (`host_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_nomor` (`nomor_antrian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- NOTIFICATIONS
-- =============================================================================
CREATE TABLE `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `host_id` INT DEFAULT NULL,
    `visitor_id` INT DEFAULT NULL,
    `type` ENUM('checkin', 'appointment', 'queue', 'checkout', 'system') DEFAULT 'checkin',
    `title` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('unread', 'read') DEFAULT 'unread',
    `email_sent` TINYINT(1) DEFAULT 0,
    `sms_sent` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notif_host` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_notif_visitor` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE SET NULL,
    INDEX `idx_host` (`host_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- STAFF CALLS (panggilan dari visitor + integrasi WA)
-- =============================================================================
CREATE TABLE `staff_calls` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `visitor_name` VARCHAR(200) NOT NULL,
    `visitor_phone` VARCHAR(50) NOT NULL,
    `visitor_id` INT DEFAULT NULL,
    `host_id` INT DEFAULT NULL,
    `room_id` INT DEFAULT NULL,
    `room_name` VARCHAR(200) DEFAULT NULL,
    `category_id` INT DEFAULT NULL,
    `assigned_user_id` INT DEFAULT NULL,
    `assigned_by` INT DEFAULT NULL,
    `assigned_at` TIMESTAMP NULL DEFAULT NULL,
    `call_type` VARCHAR(50) DEFAULT 'general',
    `message` TEXT,
    `status` ENUM('pending', 'answered', 'cancelled') DEFAULT 'pending',
    `answered_by` INT DEFAULT NULL,
    `answered_at` TIMESTAMP NULL DEFAULT NULL,
    `whatsapp_sent` TINYINT(1) DEFAULT 0,
    `wa_http_code` INT DEFAULT NULL,
    `wa_response` MEDIUMTEXT,
    `live_session_id` VARCHAR(64) DEFAULT NULL,
    `live_status` ENUM('waiting', 'active', 'ended') DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_host` (`host_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_staff_calls_assigned_user` (`assigned_user_id`),
    INDEX `idx_staff_calls_assigned_by` (`assigned_by`),
    INDEX `idx_staff_calls_live_session` (`live_session_id`),
    CONSTRAINT `fk_sc_host` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sc_visitor` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sc_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sc_assigned_user` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sc_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sc_answered` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sc_category` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Live chat message log (Socket.io)
CREATE TABLE `live_chat_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `live_session_id` VARCHAR(64) NOT NULL,
    `staff_call_id` INT DEFAULT NULL,
    `sender` ENUM('guest', 'admin') NOT NULL,
    `admin_user_id` INT DEFAULT NULL,
    `body` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_session` (`live_session_id`),
    INDEX `idx_staff_call` (`staff_call_id`),
    CONSTRAINT `fk_lcm_staff_call` FOREIGN KEY (`staff_call_id`) REFERENCES `staff_calls` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_lcm_admin_user` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admin_category_routing` (
    `user_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `category_id`),
    CONSTRAINT `fk_acr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_acr_cat` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- DATA AWAL
-- =============================================================================

-- Admin default: admin / admin123 (bcrypt)
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `email`, `role`, `status_aktif`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@recepsionis.local', 'admin', 1);

INSERT INTO `hosts` (`nama`, `email`, `no_telp`, `departemen`, `jabatan`, `status_aktif`) VALUES
('Dr. Ahmad Hidayat', 'ahmad.hidayat@example.com', '081234567890', 'Teknik Informatika', 'Dosen', 1),
('Siti Nurhaliza', 'siti.nurhaliza@example.com', '081234567891', 'Manajemen', 'Kepala Departemen', 1),
('Budi Santoso', 'budi.santoso@example.com', '081234567892', 'Akuntansi', 'Dosen', 1);

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'E-Recepsionis System', 'string', NULL),
('site_email', 'noreply@recepsionis.local', 'string', NULL),
('queue_enabled', '1', 'string', NULL),
('badge_enabled', '1', 'string', NULL),
('email_notification', '1', 'string', NULL),
('sms_notification', '0', 'string', NULL),
('auto_checkout_hours', '8', 'string', NULL),
('thumbnail_height', '180', 'number', 'Tinggi thumbnail preview (px)'),
('thumbnail_border_radius', '12', 'number', 'Border radius thumbnail (px)'),
('thumbnail_bg_color', '#e2e8f0', 'color', 'Warna background placeholder thumbnail'),
('thumbnail_margin_bottom', '15', 'number', 'Margin bawah thumbnail (px)'),
('wa_enabled', '0', 'string', NULL),
('wa_api_url', '', 'string', NULL),
('wa_api_token', '', 'string', NULL),
('wa_admin_phones', '', 'string', NULL);

INSERT INTO `complaint_categories` (`nama_kategori`, `deskripsi`, `icon`, `warna`, `urutan`) VALUES
('Program', 'Pengaduan terkait program studi, pendaftaran, atau informasi akademik', 'bi-mortarboard', '#667eea', 1),
('Help Desk', 'Bantuan teknis, informasi umum, atau pertanyaan lainnya', 'bi-headset', '#10b981', 2),
('Lainnya', 'Pengaduan atau pertanyaan lainnya yang tidak termasuk kategori di atas', 'bi-question-circle', '#f59e0b', 3);

INSERT INTO `rooms` (`nama_ruangan`, `kode_ruangan`, `lokasi`, `lantai`, `gedung`, `kapasitas`, `deskripsi`, `status_aktif`) VALUES
('Ruang Lab Komputer 1', 'LAB-01', 'Gedung A Lantai 2', '2', 'Gedung A', 40, 'Lab komputer untuk praktikum programming', 1),
('Ruang Seminar', 'SEM-01', 'Gedung B Lantai 3', '3', 'Gedung B', 100, 'Ruang seminar dengan proyektor HD', 1),
('Ruang Kelas 301', 'KLS-301', 'Gedung C Lantai 3', '3', 'Gedung C', 50, 'Ruang kelas reguler dengan AC', 1),
('Ruang Meeting', 'MTG-01', 'Gedung A Lantai 1', '1', 'Gedung A', 20, 'Ruang meeting untuk diskusi', 1);

INSERT INTO `programs` (`judul`, `deskripsi`, `kategori`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `lokasi`, `pengajar`, `kontak`, `status_aktif`) VALUES
('Workshop Web Development', 'Workshop membuat website modern dengan HTML, CSS, dan JavaScript', 'Workshop', CURDATE(), '09:00:00', '12:00:00', 'Lab Komputer 1', 'Dr. Ahmad Hidayat', 'ahmad@example.com', 1),
('Seminar Teknologi AI', 'Seminar tentang Artificial Intelligence dan Machine Learning', 'Seminar', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00:00', '16:00:00', 'Ruang Seminar', 'Prof. Siti Nurhaliza', 'siti@example.com', 1),
('Kuliah Algoritma & Struktur Data', 'Perkuliahan reguler mata kuliah Algoritma', 'Perkuliahan', CURDATE(), '08:00:00', '10:00:00', 'Ruang Kelas 301', 'Budi Santoso', 'budi@example.com', 1);

INSERT INTO `prodi` (`nama_prodi`, `kode_prodi`, `penjelasan`, `kontak_person`, `email`, `no_telp`, `direct_link`, `fakultas`, `jenjang`) VALUES
('Teknik Informatika', 'TI', 'Program studi yang mempelajari tentang teknologi komputer, pemrograman, dan sistem informasi.', 'Dr. Ahmad Hidayat', 'ahmad.hidayat@kampus.ac.id', '081234567890', 'https://www.kampus.ac.id/teknik-informatika', 'Fakultas Teknik', 'S1'),
('Sistem Informasi', 'SI', 'Program studi yang menggabungkan ilmu komputer dengan bisnis.', 'Prof. Siti Nurhaliza', 'siti.nurhaliza@kampus.ac.id', '081234567891', 'https://www.kampus.ac.id/sistem-informasi', 'Fakultas Teknik', 'S1'),
('Teknik Komputer', 'TK', 'Program studi yang fokus pada perangkat keras komputer dan embedded system.', 'Budi Santoso, S.T., M.T.', 'budi.santoso@kampus.ac.id', '081234567892', 'https://www.kampus.ac.id/teknik-komputer', 'Fakultas Teknik', 'S1'),
('Manajemen Informatika', 'MI', 'Program studi D3 aplikasi TI dalam manajemen bisnis.', 'Dewi Sartika, S.Kom., M.Kom.', 'dewi.sartika@kampus.ac.id', '081234567893', 'https://www.kampus.ac.id/manajemen-informatika', 'Fakultas Teknik', 'D3'),
('Magister Teknik Informatika', 'MTI', 'Program studi pascasarjana teknologi informasi tingkat lanjut.', 'Prof. Dr. Ir. Joko Widodo', 'joko.widodo@kampus.ac.id', '081234567894', 'https://www.kampus.ac.id/magister-teknik-informatika', 'Fakultas Teknik', 'S2');

-- =============================================================================
-- SELESAI
-- =============================================================================
