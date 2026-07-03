-- =====================================================
-- E-RECEPSIONIS SYSTEM - COMPLETE DATABASE SCHEMA
-- Version: 1.0
-- Last Updated: 2025-01-11
-- =====================================================

-- Drop database jika sudah ada (HATI-HATI! Hapus comment jika ingin drop)
-- DROP DATABASE IF EXISTS recepsionis_db;

CREATE DATABASE IF NOT EXISTS recepsionis_db;
USE recepsionis_db;

-- =====================================================
-- TABEL USERS (Untuk Login Admin)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,              -- bcrypt hash
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'operator') DEFAULT 'operator',
    status_aktif TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL HOSTS (Data Host/Pemilik Ruangan)
-- =====================================================
CREATE TABLE IF NOT EXISTS hosts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_telp VARCHAR(20),
    departemen VARCHAR(100),
    jabatan VARCHAR(100),
    status_aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL VISITORS (Data Tamu)
-- =====================================================
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_telp VARCHAR(20),
    perusahaan VARCHAR(200),
    foto VARCHAR(255) NULL,                      -- Path foto tamu
    tujuan TEXT,                                 -- Tujuan kunjungan
    host_id INT NULL,                            -- Host yang dikunjungi
    status ENUM('pending', 'checked-in', 'checked-out') DEFAULT 'pending',
    checkin_time TIMESTAMP NULL,
    checkout_time TIMESTAMP NULL,
    badge_number VARCHAR(20) UNIQUE,             -- Nomor badge unik
    notes TEXT,                                  -- Catatan tambahan
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (host_id) REFERENCES hosts(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_badge (badge_number),
    INDEX idx_host (host_id),
    INDEX idx_checkin (checkin_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL APPOINTMENTS (Sistem Appointment)
-- =====================================================
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id INT NULL,                         -- Visitor yang booking (bisa NULL jika belum check-in)
    host_id INT NOT NULL,
    nama_visitor VARCHAR(100) NOT NULL,          -- Nama visitor (jika belum terdaftar)
    email_visitor VARCHAR(100),
    no_telp_visitor VARCHAR(20),
    perusahaan_visitor VARCHAR(200),
    tanggal DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    deskripsi TEXT,
    reminder_sent TINYINT(1) DEFAULT 0,         -- Status reminder sudah dikirim
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE SET NULL,
    FOREIGN KEY (host_id) REFERENCES hosts(id) ON DELETE CASCADE,
    INDEX idx_host (host_id),
    INDEX idx_tanggal (tanggal),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL QUEUE (Sistem Antrian)
-- =====================================================
CREATE TABLE IF NOT EXISTS queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id INT NOT NULL,
    host_id INT NOT NULL,
    nomor_antrian VARCHAR(20) NOT NULL,         -- Format: A001, A002, etc
    status ENUM('waiting', 'in-progress', 'completed', 'cancelled') DEFAULT 'waiting',
    waktu_masuk TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    waktu_dipanggil TIMESTAMP NULL,
    waktu_selesai TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
    FOREIGN KEY (host_id) REFERENCES hosts(id) ON DELETE CASCADE,
    INDEX idx_host (host_id),
    INDEX idx_status (status),
    INDEX idx_nomor (nomor_antrian)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL NOTIFICATIONS (Sistem Notifikasi)
-- =====================================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    host_id INT NULL,                            -- NULL = notifikasi untuk admin
    visitor_id INT NULL,
    type ENUM('checkin', 'appointment', 'queue', 'checkout', 'system') DEFAULT 'checkin',
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    email_sent TINYINT(1) DEFAULT 0,            -- Status email sudah dikirim
    sms_sent TINYINT(1) DEFAULT 0,               -- Status SMS sudah dikirim
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (host_id) REFERENCES hosts(id) ON DELETE CASCADE,
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE SET NULL,
    INDEX idx_host (host_id),
    INDEX idx_status (status),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL SETTINGS (Pengaturan Sistem)
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Admin User (username: admin, password: admin123)
INSERT INTO users (username, password, nama_lengkap, email, role, status_aktif) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@recepsionis.local', 'admin', 1);

-- Sample Hosts
INSERT INTO hosts (nama, email, no_telp, departemen, jabatan, status_aktif) VALUES
('Dr. Ahmad Hidayat', 'ahmad.hidayat@example.com', '081234567890', 'Teknik Informatika', 'Dosen', 1),
('Siti Nurhaliza', 'siti.nurhaliza@example.com', '081234567891', 'Manajemen', 'Kepala Departemen', 1),
('Budi Santoso', 'budi.santoso@example.com', '081234567892', 'Akuntansi', 'Dosen', 1);

-- Default Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'E-Recepsionis System'),
('site_email', 'noreply@recepsionis.local'),
('queue_enabled', '1'),
('badge_enabled', '1'),
('email_notification', '1'),
('sms_notification', '0'),
('auto_checkout_hours', '8'); -- Auto checkout setelah 8 jam

-- =====================================================
-- END OF SCHEMA
-- =====================================================
