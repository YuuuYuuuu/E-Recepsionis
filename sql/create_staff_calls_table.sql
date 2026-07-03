-- Create staff_calls table for recepsionis_db
-- Run this script once (phpMyAdmin SQL tab or mysql client)

CREATE DATABASE IF NOT EXISTS `recepsionis_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `recepsionis_db`;

CREATE TABLE IF NOT EXISTS `staff_calls` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `visitor_name` VARCHAR(200) NOT NULL,
  `visitor_phone` VARCHAR(50) NOT NULL,
  `host_id` INT NULL,
  `call_type` VARCHAR(50) DEFAULT 'general',
  `message` TEXT,
  `status` ENUM('pending','answered','cancelled') DEFAULT 'pending',
  `answered_by` INT NULL,
  `answered_at` TIMESTAMP NULL DEFAULT NULL,
  `whatsapp_sent` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: add foreign keys if your schema already has hosts/users tables
-- ALTER TABLE `staff_calls` ADD CONSTRAINT fk_staff_calls_host FOREIGN KEY (host_id) REFERENCES hosts(id) ON DELETE SET NULL;
-- ALTER TABLE `staff_calls` ADD CONSTRAINT fk_staff_calls_answered_by FOREIGN KEY (answered_by) REFERENCES users(id) ON DELETE SET NULL;
