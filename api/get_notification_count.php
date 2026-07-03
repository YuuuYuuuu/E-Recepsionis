<?php
// API untuk mendapatkan jumlah notifikasi belum dibaca
define('API_CONTEXT', true);

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();
session_start();

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'count' => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$count = 0;
$role = (string) ($_SESSION['role'] ?? '');
if ($role === 'admin') {
    $result = $koneksi->query("SELECT COUNT(*) as count FROM notifications WHERE status = 'unread'");
    if ($result) {
        $row = $result->fetch_assoc();
        $count = (int)$row['count'];
    }
}

// Clear output buffer
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Return JSON
echo json_encode([
    'success' => true,
    'count' => $count
], JSON_UNESCAPED_UNICODE);

exit;
