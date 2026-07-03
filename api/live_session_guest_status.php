<?php
/**
 * Status sesi live untuk tamu (polling cadangan) — tanpa login.
 * Hanya UUID session + status agregat.
 */
declare(strict_types=1);

define('API_CONTEXT', true);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/config.php';

$sessionId = isset($_GET['session_id']) ? trim((string) $_GET['session_id']) : '';
if ($sessionId === '' || strlen($sessionId) > 80) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'session_id invalid'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $koneksi->prepare(
    'SELECT sc.status, sc.live_status, u.nama_lengkap, u.username
     FROM staff_calls sc
     LEFT JOIN users u ON u.id = sc.answered_by
     WHERE sc.live_session_id = ? LIMIT 1'
);
$stmt->bind_param('s', $sessionId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'phase' => 'unknown'], JSON_UNESCAPED_UNICODE);
    exit;
}

$phase = 'waiting';
if ($row['status'] === 'cancelled' || ($row['live_status'] ?? null) === 'ended') {
    $phase = 'ended';
} elseif ($row['status'] === 'answered' && ($row['live_status'] ?? null) === 'active') {
    $phase = 'chat';
} elseif ($row['status'] === 'answered') {
    $phase = 'chat';
}

$adminName = trim((string) ($row['nama_lengkap'] ?: $row['username'] ?: 'Admin'));

echo json_encode(
    [
        'success' => true,
        'phase' => $phase,
        'admin_name' => $adminName,
    ],
    JSON_UNESCAPED_UNICODE
);
