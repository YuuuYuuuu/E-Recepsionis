<?php
require_once 'auth.php';
requireSuperAdminPage();

$checks = [
    'setting_type' => "ALTER TABLE settings ADD COLUMN `setting_type` VARCHAR(50) DEFAULT 'string'",
    'description' => "ALTER TABLE settings ADD COLUMN `description` VARCHAR(255) DEFAULT NULL",
    'created_at' => "ALTER TABLE settings ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
    'updated_at' => "ALTER TABLE settings ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

echo "<pre>Checking settings table columns...\n\n";

foreach ($checks as $col => $alterSql) {
    $sql = "SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = '" . addslashes($col) . "'";
    $res = $koneksi->query($sql);
    if (!$res) {
        echo "Error checking column {$col}: " . $koneksi->error . "\n";
        continue;
    }
    $row = $res->fetch_assoc();
    if ((int)$row['c'] === 0) {
        echo "Column '{$col}' missing — adding...\n";
        if ($koneksi->query($alterSql)) {
            echo "  ✅ Added {$col}\n";
        } else {
            echo "  ❌ Failed to add {$col}: " . $koneksi->error . "\n";
        }
    } else {
        echo "Column '{$col}' already exists — OK\n";
    }
}

// If setting_type column was missing and we added it, attempt to run the default inserts if some defaults don't exist
$defaults = [
    ['thumbnail_height','180','number','Tinggi thumbnail preview (px)'],
    ['thumbnail_border_radius','12','number','Border radius thumbnail (px)'],
    ['thumbnail_bg_color','#e2e8f0','color','Warna background placeholder thumbnail'],
    ['thumbnail_margin_bottom','15','number','Margin bawah thumbnail (px)']
];

foreach ($defaults as $d) {
    list($key, $value, $type, $desc) = $d;
    $keyEsc = $koneksi->real_escape_string($key);
    $valueEsc = $koneksi->real_escape_string($value);
    $typeEsc = $koneksi->real_escape_string($type);
    $descEsc = $koneksi->real_escape_string($desc);

    $check = $koneksi->query("SELECT COUNT(*) AS c FROM settings WHERE setting_key = '".$keyEsc."'");
    if ($check) {
        $r = $check->fetch_assoc();
        if ((int)$r['c'] === 0) {
            $ins = "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES ('".$keyEsc."', '".$valueEsc."', '".$typeEsc."', '".$descEsc."')";
            if ($koneksi->query($ins)) {
                echo "Inserted default setting: {$key}\n";
            } else {
                echo "Failed to insert {$key}: " . $koneksi->error . "\n";
            }
        } else {
            echo "Default setting '{$key}' already present\n";
        }
    } else {
        echo "Failed to check default setting '{$key}': " . $koneksi->error . "\n";
    }
}

echo "\nDone.\n</pre>";
?>