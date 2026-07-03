<?php
/**
 * Migration: Add visitor_id column to staff_calls table
 * This script should be run once to enable syncing between staff calls and visitor data
 * Access: http://localhost:8888/Recepsionis/sql/migrate_add_visitor_id.php
 */

require_once '../config.php';

echo "<h2>Migration: Add visitor_id to staff_calls</h2>";

// Check if column already exists
$checkCol = $koneksi->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'visitor_id'");

if ($checkCol && $checkCol->num_rows > 0) {
    echo "<p><strong>✓ visitor_id column sudah ada di staff_calls table.</strong></p>";
} else {
    echo "<p>Menambahkan visitor_id column...</p>";
    
    // Add the column
    if ($koneksi->query("ALTER TABLE `staff_calls` ADD COLUMN visitor_id INT NULL")) {
        echo "<p style='color: green;'>✓ Column visitor_id berhasil ditambahkan.</p>";
    } else {
        echo "<p style='color: red;'>✗ Gagal menambahkan column: " . $koneksi->error . "</p>";
        exit;
    }
    
    // Try to add foreign key
    $checkFK = $koneksi->query("SELECT 1 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'visitor_id' AND REFERENCED_TABLE_NAME = 'visitors'");
    
    if ($checkFK && $checkFK->num_rows > 0) {
        echo "<p>✓ Foreign key untuk visitor_id sudah ada.</p>";
    } else {
        if ($koneksi->query("ALTER TABLE `staff_calls` ADD FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE SET NULL")) {
            echo "<p style='color: green;'>✓ Foreign key berhasil ditambahkan.</p>";
        } else {
            // FK might fail if visitors table structure is different, but column is still added
            echo "<p style='color: orange;'>⚠ Foreign key tidak bisa ditambahkan (tapi column sudah ada): " . $koneksi->error . "</p>";
        }
    }
}

echo "<p><strong>Migration selesai!</strong></p>";
echo "<p><a href='../admin/staff_calls.php'>Kembali ke Staff Calls</a></p>";

?>
