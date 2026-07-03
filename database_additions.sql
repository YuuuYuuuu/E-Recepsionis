-- Additional tables for E-Recepsionis Visitor Features

-- Tabel Rooms (untuk daftar ruangan)
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_ruangan VARCHAR(100) NOT NULL,
    kode_ruangan VARCHAR(50) UNIQUE NOT NULL,
    lokasi VARCHAR(200),
    lantai VARCHAR(50),
    gedung VARCHAR(100),
    kapasitas INT DEFAULT 0,
    deskripsi TEXT,
    foto VARCHAR(255) NULL,
    status_aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Program Perkuliahan
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    kategori ENUM('Perkuliahan', 'Seminar', 'Workshop', 'Event', 'Lainnya') DEFAULT 'Perkuliahan',
    tanggal DATE,
    waktu_mulai TIME,
    waktu_selesai TIME,
    lokasi VARCHAR(200),
    pengajar VARCHAR(100),
    kontak VARCHAR(100),
    status_aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Staff Calls (untuk panggilan staff)
CREATE TABLE IF NOT EXISTS staff_calls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_name VARCHAR(100),
    visitor_phone VARCHAR(20),
    staff_id INT NULL, -- NULL = general call
    host_id INT NULL, -- Jika ada host spesifik
    call_type ENUM('general', 'specific', 'emergency') DEFAULT 'general',
    message TEXT,
    status ENUM('pending', 'answered', 'cancelled') DEFAULT 'pending',
    answered_by INT NULL,
    answered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (host_id) REFERENCES hosts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample rooms
INSERT INTO rooms (nama_ruangan, kode_ruangan, lokasi, lantai, gedung, kapasitas, deskripsi, status_aktif) VALUES
('Ruang Lab Komputer 1', 'LAB-01', 'Gedung A Lantai 2', '2', 'Gedung A', 40, 'Lab komputer untuk praktikum programming', 1),
('Ruang Seminar', 'SEM-01', 'Gedung B Lantai 3', '3', 'Gedung B', 100, 'Ruang seminar dengan proyektor HD', 1),
('Ruang Kelas 301', 'KLS-301', 'Gedung C Lantai 3', '3', 'Gedung C', 50, 'Ruang kelas reguler dengan AC', 1),
('Ruang Meeting', 'MTG-01', 'Gedung A Lantai 1', '1', 'Gedung A', 20, 'Ruang meeting untuk diskusi', 1);

-- Insert sample programs
INSERT INTO programs (judul, deskripsi, kategori, tanggal, waktu_mulai, waktu_selesai, lokasi, pengajar, kontak, status_aktif) VALUES
('Workshop Web Development', 'Workshop membuat website modern dengan HTML, CSS, dan JavaScript', 'Workshop', CURDATE(), '09:00:00', '12:00:00', 'Lab Komputer 1', 'Dr. Ahmad Hidayat', 'ahmad@example.com', 1),
('Seminar Teknologi AI', 'Seminar tentang Artificial Intelligence dan Machine Learning', 'Seminar', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00:00', '16:00:00', 'Ruang Seminar', 'Prof. Siti Nurhaliza', 'siti@example.com', 1),
('Kuliah Algoritma & Struktur Data', 'Perkuliahan reguler mata kuliah Algoritma', 'Perkuliahan', CURDATE(), '08:00:00', '10:00:00', 'Ruang Kelas 301', 'Budi Santoso', 'budi@example.com', 1);
