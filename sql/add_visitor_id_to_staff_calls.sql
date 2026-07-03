-- Migration: add visitor_id foreign key to staff_calls
-- This links staff call records with visitor records in Data Tamu

DELIMITER $$

-- Check and add visitor_id column if it doesn't exist
IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'visitor_id'
) THEN
    ALTER TABLE `recepsionis_db`.`staff_calls` ADD COLUMN visitor_id INT NULL;
    ALTER TABLE `recepsionis_db`.`staff_calls` ADD FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE SET NULL;
END IF$$

DELIMITER ;
