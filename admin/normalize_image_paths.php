<?php
require_once 'auth.php';
requireSuperAdminPage();

// Normalize images paths in rooms table: remove leading '../' from stored paths
$res = $koneksi->query("SELECT id, images FROM rooms");
if (!$res) {
    echo 'DB error: ' . $koneksi->error;
    exit;
}

$count = 0;
while ($row = $res->fetch_assoc()) {
    $id = (int)$row['id'];
    $imgs = array_filter(array_map('trim', explode(',', $row['images'] ?? '')));
    $changed = false;
    $new = [];
    foreach ($imgs as $img) {
        $orig = $img;
        // remove ../ prefix if exists
        $img = preg_replace('#^\.\./+#', '', $img);
        // remove leading slash to keep relative 'uploads/...'
        $img = preg_replace('#^/+#', '', $img);
        if ($img !== $orig) $changed = true;
        if ($img !== '') $new[] = $img;
    }
    if ($changed) {
        $csv = !empty($new) ? $koneksi->real_escape_string(implode(',', $new)) : null;
        $sql = is_null($csv) ? "UPDATE rooms SET images = NULL WHERE id = $id" : "UPDATE rooms SET images = '$csv' WHERE id = $id";
        if ($koneksi->query($sql)) $count++;
    }
}

header('Location: room_gallery.php?room_id=' . ($_GET['room_id'] ?? '') . '&normalized=' . $count);
exit;
?>
