<?php
// Helper script to update visitor/index.php with thumbnail settings
require_once 'auth.php';
requireSuperAdminPage();

$file = 'visitor/index.php';
$content = file_get_contents($file);

// Find the line after "require_once '../config.php';" and add the new code
$needle = "require_once '../config.php';\n\n// Get rooms";
$replacement = "require_once '../config.php';\n\n// Function to get setting from database\nfunction get_thumbnail_setting(\$key, \$default) {\n    global \$koneksi;\n    \$res = \$koneksi->query(\"SELECT setting_value FROM settings WHERE setting_key = '\" . \$koneksi->real_escape_string(\$key) . \"' LIMIT 1\");\n    if (\$res && \$res->num_rows > 0) {\n        \$row = \$res->fetch_assoc();\n        return \$row['setting_value'];\n    }\n    return \$default;\n}\n\n// Get thumbnail settings\n\$thumb_height = get_thumbnail_setting('thumbnail_height', '180');\n\$thumb_border_radius = get_thumbnail_setting('thumbnail_border_radius', '12');\n\$thumb_bg_color = get_thumbnail_setting('thumbnail_bg_color', '#e2e8f0');\n\$thumb_margin_bottom = get_thumbnail_setting('thumbnail_margin_bottom', '15');\n\n// Get rooms";

if (strpos($content, $needle) !== false) {
    $new_content = str_replace($needle, $replacement, $content);
    file_put_contents($file, $new_content);
    echo "✓ Added thumbnail settings function to visitor/index.php\n";
} else {
    echo "✗ Could not find needle in file\n";
}

// Now update the thumbnail rendering part
$file = 'visitor/index.php';
$content = file_get_contents($file);

$old_thumb = '                                    <?php if ($first_img): ?>' . "\n" .
             '                                        <div style="width: 100%; height: 180px; border-radius: 12px; overflow: hidden; margin-bottom: 15px; background: #e2e8f0;">' . "\n" .
             '                                            <img src="<?= htmlspecialchars($first_img) ?>" alt="<?= htmlspecialchars($room[\'nama_ruangan\']) ?>" ' . "\n" .
             '                                                 style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display=\'none\'">' . "\n" .
             '                                        </div>' . "\n" .
             '                                    <?php endif; ?>';

$new_thumb = '                                    <?php if ($first_img): ?>' . "\n" .
             '                                        <div style="width: 100%; height: <?= htmlspecialchars($thumb_height) ?>px; border-radius: <?= htmlspecialchars($thumb_border_radius) ?>px; overflow: hidden; margin-bottom: <?= htmlspecialchars($thumb_margin_bottom) ?>px; background: <?= htmlspecialchars($thumb_bg_color) ?>;">' . "\n" .
             '                                            <img src="<?= htmlspecialchars($first_img) ?>" alt="<?= htmlspecialchars($room[\'nama_ruangan\']) ?>" ' . "\n" .
             '                                                 style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display=\'none\'">' . "\n" .
             '                                        </div>' . "\n" .
             '                                    <?php endif; ?>';

if (strpos($content, $old_thumb) !== false) {
    $new_content = str_replace($old_thumb, $new_thumb, $content);
    file_put_contents($file, $new_content);
    echo "✓ Updated thumbnail rendering to use settings\n";
} else {
    echo "✗ Could not find thumbnail code in file\n";
}

echo "\n✓ Visitor index.php updated with dynamic thumbnail settings!\n";
?>
