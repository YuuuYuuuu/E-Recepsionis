<?php
declare(strict_types=1);

define('API_CONTEXT', true);

header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/staff_call_routing.php';

$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw ?: 'null', true);
    $source = is_array($json) ? $json : $_POST;

    $hasNotifications = array_key_exists('notifications_enabled', $source);
    $hasSound = array_key_exists('sound_enabled', $source);

    if (!$hasNotifications && !$hasSound) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'notifications_enabled atau sound_enabled wajib'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $notificationsEnabled = null;
    $soundEnabled = null;

    if ($hasNotifications) {
        $notificationsEnabled = filter_var($source['notifications_enabled'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($notificationsEnabled === null) {
            $value = (string) $source['notifications_enabled'];
            $notificationsEnabled = in_array($value, ['1', 'true', 'on', 'yes'], true);
        }
    }

    if ($hasSound) {
        $soundEnabled = filter_var($source['sound_enabled'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($soundEnabled === null) {
            $value = (string) $source['sound_enabled'];
            $soundEnabled = in_array($value, ['1', 'true', 'on', 'yes'], true);
        }
    }

    if (!recepsionis_set_notification_preferences($koneksi, $userId, $notificationsEnabled, $soundEnabled)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan preferensi'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$prefs = recepsionis_get_notification_preferences($koneksi, $userId);
$categoryIds = recepsionis_get_admin_category_ids($koneksi, $userId);

echo json_encode(
    [
        'success' => true,
        'preferences' => [
            'notifications_enabled' => (bool) ($prefs['notifications_enabled'] ?? true),
            'sound_enabled' => (bool) ($prefs['sound_enabled'] ?? true),
        ],
        'routing' => [
            'category_ids' => $categoryIds,
            'count' => count($categoryIds),
        ],
        'user' => [
            'id' => $userId,
            'name' => $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Administrator',
        ],
    ],
    JSON_UNESCAPED_UNICODE
);
