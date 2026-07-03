-- Migration: add wa_http_code and wa_response to staff_calls
-- Compatible with older MySQL versions using INFORMATION_SCHEMA

CREATE DATABASE IF NOT EXISTS `recepsionis_db`;
USE `recepsionis_db`;

-- Add wa_http_code if not exists
DELIMITER $$
DROP PROCEDURE IF EXISTS add_wa_response_columns$$
CREATE PROCEDURE add_wa_response_columns()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'wa_http_code'
  ) THEN
    ALTER TABLE `recepsionis_db`.`staff_calls` ADD COLUMN wa_http_code INT NULL;
  END IF;
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'wa_response'
  ) THEN
    ALTER TABLE `recepsionis_db`.`staff_calls` ADD COLUMN wa_response MEDIUMTEXT NULL;
  END IF;
END$$
CALL add_wa_response_columns()$$
DROP PROCEDURE IF EXISTS add_wa_response_columns$$
DELIMITER ;
