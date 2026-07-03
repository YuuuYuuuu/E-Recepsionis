-- Migration: create rooms table if not exists
-- Creates comprehensive rooms table used by visitor/admin features

CREATE DATABASE IF NOT EXISTS `recepsionis_db`;
USE `recepsionis_db`;

CREATE TABLE IF NOT EXISTS `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_ruangan` VARCHAR(200) NOT NULL,
  `kode_ruangan` VARCHAR(100) UNIQUE NOT NULL,
  `lokasi` VARCHAR(255) DEFAULT NULL,
  `lantai` VARCHAR(50) DEFAULT NULL,
  `gedung` VARCHAR(100) DEFAULT NULL,
  `kapasitas` INT DEFAULT 0,
  `deskripsi` TEXT DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `images` TEXT DEFAULT NULL,
  `perangkat` TEXT DEFAULT NULL,
  `mode_ruangan` VARCHAR(100) DEFAULT NULL,
  `status_aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data (optional)
INSERT INTO `rooms` (`nama_ruangan`,`kode_ruangan`,`lokasi`,`lantai`,`gedung`,`kapasitas`,`deskripsi`,`status_aktif`) VALUES
('Ruang Meeting','MTG-01','Gedung A Lantai 1','1','Gedung A',20,'Ruang meeting untuk diskusi',1),
('Ruang Seminar','SEM-01','Gedung B Lantai 3','3','Gedung B',100,'Ruang seminar dengan proyektor HD',1);
