<?php
// Safe migration runner for local environment
// Usage: open http://localhost/Recepsionis/sql/run_migration.php or run via CLI

// Prevent remote execution
$allowed_remote = ['127.0.0.1', '::1'];
if (php_sapi_name() !== 'cli') {
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remote, $allowed_remote)) {
        header('HTTP/1.1 403 Forbidden');
        echo "Forbidden. Run this script from the server (localhost) only.";
        exit;
    }
}

// Load config (this will also create $koneksi)
define('MIGRATION_CONTEXT', true);
require_once dirname(__DIR__) . '/config.php';

$sqlFile = __DIR__ . '/add_whatsapp_settings.sql';
if (!file_exists($sqlFile)) {
    echo "SQL file not found: {$sqlFile}";
    exit;
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    echo "Failed to read SQL file.";
    exit;
}

// Remove DELIMITER lines
$sql = preg_replace('/DELIMITER\s+\$\$/i', '', $sql);
$sql = preg_replace('/DELIMITER\s+;/i', '', $sql);

// Remove stored procedure definitions and their CALL lines because some MySQL server
// configurations (and mysqli->multi_query) don't accept custom delimiters.
// We'll perform the equivalent check-and-alter in PHP instead.
$sql = preg_replace('/CREATE\s+PROCEDURE[\s\S]*?END\s*\$\$/i', '', $sql);
$sql = preg_replace('/CALL\s+\w+\s*\(\s*\)\s*\$\$/i', '', $sql);

// Remove any remaining $$ markers
$sql = str_replace('$$', ';', $sql);

// Execute via multi_query
$mysqli = $koneksi; // from config.php
$errors = [];

// Split into individual statements for nicer progress (optional)
$queries = array_filter(array_map('trim', explode(';', $sql)));

if (count($queries) === 0) {
    echo "No SQL statements found to execute.";
    exit;
}

echo "Running migration...\n\n";

foreach ($queries as $i => $query) {
    if (empty($query)) continue;
    // Skip comments-only statements
    if (preg_match('/^\s*--/', $query)) continue;
    // Execute
    if ($mysqli->multi_query($query)) {
        // Drain results
        do {
            if ($res = $mysqli->store_result()) {
                $res->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());
        echo sprintf("[%d/%d] OK\n", $i+1, count($queries));
    } else {
        $errors[] = "Error executing statement #".($i+1).": " . $mysqli->error;
        echo sprintf("[%d/%d] ERROR: %s\n", $i+1, count($queries), $mysqli->error);
    }
}

if (!empty($errors)) {
    echo "\nMigration finished with errors:\n";
    foreach ($errors as $err) echo " - $err\n";
    exit(1);
}

echo "\nMigration completed successfully.\n";

// Show simple verification queries
echo "\nVerify with:\n";
echo "DESCRIBE recepsionis_db.staff_calls;\n";
echo "SELECT setting_key, setting_value FROM recepsionis_db.settings WHERE setting_key LIKE 'wa_%';\n";

// Additional: ensure whatsapp_sent column exists (perform check-and-add in PHP)
echo "\nChecking whatsapp_sent column...\n";
$checkCol = $mysqli->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'recepsionis_db' AND TABLE_NAME = 'staff_calls' AND COLUMN_NAME = 'whatsapp_sent'");
if ($checkCol && $checkCol->num_rows > 0) {
    echo "Column whatsapp_sent already exists.\n";
} else {
    echo "Column whatsapp_sent not found, attempting to add...\n";
    if ($mysqli->query("ALTER TABLE `recepsionis_db`.`staff_calls` ADD COLUMN whatsapp_sent TINYINT(1) DEFAULT 0")) {
        echo "Column whatsapp_sent added successfully.\n";
    } else {
        echo "Failed to add whatsapp_sent column: " . $mysqli->error . "\n";
    }
}
