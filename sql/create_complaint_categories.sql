-- Create complaint_categories table for master kategori pengaduan
USE recepsionis_db;

CREATE TABLE IF NOT EXISTS complaint_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    icon VARCHAR(50) DEFAULT 'bi-tag',
    warna VARCHAR(20) DEFAULT '#667eea',
    status_aktif TINYINT(1) DEFAULT 1,
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default categories
INSERT INTO complaint_categories (nama_kategori, deskripsi, icon, warna, urutan) VALUES
('Program', 'Pengaduan terkait program studi, pendaftaran, atau informasi akademik', 'bi-mortarboard', '#667eea', 1),
('Help Desk', 'Bantuan teknis, informasi umum, atau pertanyaan lainnya', 'bi-headset', '#10b981', 2),
('Lainnya', 'Pengaduan atau pertanyaan lainnya yang tidak termasuk kategori di atas', 'bi-question-circle', '#f59e0b', 3);

-- Add category_id column to staff_calls table if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'recepsionis_db' 
    AND TABLE_NAME = 'staff_calls' 
    AND COLUMN_NAME = 'category_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE staff_calls ADD COLUMN category_id INT NULL AFTER room_id, ADD FOREIGN KEY (category_id) REFERENCES complaint_categories(id) ON DELETE SET NULL',
    'SELECT "Column category_id already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
