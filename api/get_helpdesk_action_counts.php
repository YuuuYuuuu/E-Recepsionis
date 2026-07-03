<?php
define('API_CONTEXT', true);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

ob_start();
session_start();

require_once '../config.php';
require_once '../staff_call_routing.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$userRole = (string) ($_SESSION['role'] ?? '');
$isAdminUser = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$viewFilter = $isAdminUser ? ($_GET['view'] ?? 'all') : 'mine';
if (!in_array($viewFilter, ['all', 'mine'], true)) {
    $viewFilter = $isAdminUser ? 'all' : 'mine';
}

$counts = recepsionis_get_helpdesk_action_counts($koneksi, $userId, $isAdminUser, $viewFilter, $userRole);

while (ob_get_level() > 0) {
    ob_end_clean();
}

echo json_encode(
    array_merge(
        ['success' => true],
        $counts
    ),
    JSON_UNESCAPED_UNICODE
);
