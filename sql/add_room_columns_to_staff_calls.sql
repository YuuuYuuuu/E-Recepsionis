-- Migration: add room_id and room_name to staff_calls
-- Compatible with MySQL versions without ADD COLUMN IF NOT EXISTS

CREATE DATABASE IF NOT EXISTS `recepsionis_db`;
USE `recepsionis_db`;

-- Add room_id if not exists
DELIMITER $$
DROP PROCEDURE IF EXISTS add_room_columns$$
CREATE PROCEDURE add_room_columns()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'room_id'
  ) THEN
    ALTER TABLE `recepsionis_db`.`staff_calls` ADD COLUMN room_id INT NULL;
  END IF;
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'room_name'
  ) THEN
    ALTER TABLE `recepsionis_db`.`staff_calls` ADD COLUMN room_name VARCHAR(200) NULL;
  END IF;
END$$
CALL add_room_columns()$$
DROP PROCEDURE IF EXISTS add_room_columns$$
DELIMITER ;
