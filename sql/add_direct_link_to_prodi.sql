-- Add direct_link column to prodi table
USE recepsionis_db;

ALTER TABLE prodi 
ADD COLUMN direct_link VARCHAR(500) NULL 
AFTER no_telp;

-- Update existing records with sample direct links (optional)
UPDATE prodi SET direct_link = 'https://www.kampus.ac.id/teknik-informatika' WHERE kode_prodi = 'TI';
UPDATE prodi SET direct_link = 'https://www.kampus.ac.id/sistem-informasi' WHERE kode_prodi = 'SI';
UPDATE prodi SET direct_link = 'https://www.kampus.ac.id/teknik-komputer' WHERE kode_prodi = 'TK';
UPDATE prodi SET direct_link = 'https://www.kampus.ac.id/manajemen-informatika' WHERE kode_prodi = 'MI';
UPDATE prodi SET direct_link = 'https://www.kampus.ac.id/magister-teknik-informatika' WHERE kode_prodi = 'MTI';
