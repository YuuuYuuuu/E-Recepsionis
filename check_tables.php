<?php
require_once 'koneksi.php';

echo "Checking database tables...\n\n";

// Get all tables
$tables_result = $koneksi->query("SHOW TABLES");
if ($tables_result) {
    $tables = [];
    while ($row = $tables_result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    echo "Found tables:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    echo "\nChecking table usage...\n";
    
    // Check each table for data
    $unused_tables = [];
    foreach ($tables as $table) {
        $count_result = $koneksi->query("SELECT COUNT(*) as count FROM `$table`");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['count'];
            echo "$table: $count records\n";
            
            // Consider table unused if it has 0 records and is not a core system table
            if ($count == 0) {
                // Don't drop core system tables
                $core_tables = ['users', 'hosts', 'visitors', 'staff_calls', 'notifications', 'queue', 'settings', 'prodi', 'rooms', 'live_chat_messages', 'live_chat_admin_state', 'admin_category_routing', 'admin_notification_preferences', 'staff_call_logs', 'complaint_categories', 'programs', 'appointments'];
                if (!in_array($table, $core_tables)) {
                    $unused_tables[] = $table;
                }
            }
        }
    }
    
    if (!empty($unused_tables)) {
        echo "\nUnused tables (can be deleted):\n";
        foreach ($unused_tables as $table) {
            echo "- $table\n";
        }
        
        echo "\nSQL commands to delete unused tables:\n";
        foreach ($unused_tables as $table) {
            echo "DROP TABLE IF EXISTS `$table`;\n";
        }
    } else {
        echo "\nNo unused tables found.\n";
    }
} else {
    echo "Error: " . $koneksi->error . "\n";
}
?>
