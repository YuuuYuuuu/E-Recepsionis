-- Tabel untuk Program Studi (Prodi)
USE recepsionis_db;

CREATE TABLE IF NOT EXISTS prodi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_prodi VARCHAR(200) NOT NULL,
    kode_prodi VARCHAR(50) UNIQUE,
    penjelasan TEXT,
    kontak_person VARCHAR(100),
    email VARCHAR(100),
    no_telp VARCHAR(20),
    fakultas VARCHAR(100),
    jenjang ENUM('D3', 'S1', 'S2', 'S3') DEFAULT 'S1',
    foto VARCHAR(255) NULL,
`    status_aktif TINYINT(1) DEFAULT 1,
`    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data
INSERT INTO prodi (nama_prodi, kode_prodi, penjelasan, kontak_person, email, no_telp, fakultas, jenjang) VALUES
('Teknik Informatika', 'TI', 'Program studi yang mempelajari tentang teknologi komputer, pemrograman, dan sistem informasi. Fokus pada pengembangan software, database, dan jaringan komputer.', 'Dr. Ahmad Hidayat', 'ahmad.hidayat@kampus.ac.id', '081234567890', 'Fakultas Teknik', 'S1'),
('Sistem Informasi', 'SI', 'Program studi yang menggabungkan ilmu komputer dengan bisnis. Mempelajari pengembangan sistem informasi untuk mendukung proses bisnis organisasi.', 'Prof. Siti Nurhaliza', 'siti.nurhaliza@kampus.ac.id', '081234567891', 'Fakultas Teknik', 'S1'),
('Teknik Komputer', 'TK', 'Program studi yang fokus pada perangkat keras komputer, embedded system, dan robotika. Mempelajari desain dan implementasi sistem komputer.', 'Budi Santoso, S.T., M.T.', 'budi.santoso@kampus.ac.id', '081234567892', 'Fakultas Teknik', 'S1'),
('Manajemen Informatika', 'MI', 'Program studi D3 yang mempelajari aplikasi teknologi informasi dalam manajemen bisnis. Fokus pada praktik dan implementasi langsung.', 'Dewi Sartika, S.Kom., M.Kom.', 'dewi.sartika@kampus.ac.id', '081234567893', 'Fakultas Teknik', 'D3'),
('Magister Teknik Informatika', 'MTI', 'Program studi pascasarjana yang mempelajari teknologi informasi tingkat lanjut, riset, dan pengembangan inovasi di bidang IT.', 'Prof. Dr. Ir. Joko Widodo', 'joko.widodo@kampus.ac.id', '081234567894', 'Fakultas Teknik', 'S2');
