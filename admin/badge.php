<?php
require_once 'auth.php';
requireSuperAdminPage();

// Get visitor ID
$visitor_id = intval($_GET['id'] ?? 0);

if (!$visitor_id) {
    header("Location: visitors.php?error=invalid_id");
    exit;
}

// Get visitor data
$visitor = $koneksi->query("SELECT v.*, h.nama as host_nama, h.departemen as host_departemen 
                            FROM visitors v 
                            LEFT JOIN hosts h ON v.host_id = h.id 
                            WHERE v.id = $visitor_id")->fetch_assoc();

if (!$visitor) {
    header("Location: visitors.php?error=not_found");
    exit;
}

// Redirect to badge display page
header("Location: ../badge.php?id=" . $visitor_id);
exit;
?>
