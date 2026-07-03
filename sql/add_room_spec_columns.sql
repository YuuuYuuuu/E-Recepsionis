-- Migration: add perangkat, mode_ruangan, and images to rooms
CREATE DATABASE IF NOT EXISTS `recepsionis_db`;
USE `recepsionis_db`;

DELIMITER $$
DROP PROCEDURE IF EXISTS add_room_spec_columns$$
CREATE PROCEDURE add_room_spec_columns()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'rooms' AND COLUMN_NAME = 'perangkat'
  ) THEN
    ALTER TABLE `recepsionis_db`.`rooms` ADD COLUMN perangkat TEXT NULL;
  END IF;
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'rooms' AND COLUMN_NAME = 'mode_ruangan'
  ) THEN
    ALTER TABLE `recepsionis_db`.`rooms` ADD COLUMN mode_ruangan VARCHAR(100) NULL;
  END IF;
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'rooms' AND COLUMN_NAME = 'images'
  ) THEN
    ALTER TABLE `recepsionis_db`.`rooms` ADD COLUMN images TEXT NULL;
  END IF;
END$$
CALL add_room_spec_columns()$$
DROP PROCEDURE IF EXISTS add_room_spec_columns$$
DELIMITER ;
