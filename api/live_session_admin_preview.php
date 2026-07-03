<?php
/**
 * Ringkasan staff_calls untuk admin (session login) — isi panel Live Chat dari ?accept_session=
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

define('API_CONTEXT', true);
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/staff_call_routing.php';

$sessionId = isset($_GET['session_id']) ? trim((string) $_GET['session_id']) : '';
if ($sessionId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'session_id wajib'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $koneksi->prepare(
    'SELECT sc.id AS staff_call_id, sc.live_session_id, sc.visitor_name, sc.visitor_phone, sc.message,
            sc.category_id, sc.assigned_user_id, sc.status, sc.live_status, cc.nama_kategori AS category
     FROM staff_calls sc
     LEFT JOIN complaint_categories cc ON cc.id = sc.category_id
     WHERE sc.live_session_id = ? LIMIT 1'
);
$stmt->bind_param('s', $sessionId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Sesi tidak ditemukan'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$categoryId = (int) ($row['category_id'] ?? 0);
$assignedUserId = (int) ($row['assigned_user_id'] ?? 0);
if (!recepsionis_user_can_receive_staff_call($koneksi, $userId, $categoryId, $assignedUserId, $_SESSION['role'] ?? null)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Anda tidak ditugaskan untuk kategori ini.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(
    [
        'success' => true,
        'session_id' => $row['live_session_id'],
        'staff_call_id' => (int) $row['staff_call_id'],
        'guest_name' => $row['visitor_name'],
        'visitor_phone' => $row['visitor_phone'],
        'message_preview' => (function_exists('mb_substr') ? mb_substr((string) $row['message'], 0, 200) : substr((string) $row['message'], 0, 200)),
        'category' => $row['category'] ?: '—',
        'category_id' => (int) ($row['category_id'] ?? 0),
        'status' => $row['status'],
        'live_status' => $row['live_status'],
    ],
    JSON_UNESCAPED_UNICODE
);
