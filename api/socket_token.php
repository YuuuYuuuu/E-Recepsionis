<?php
/**
 * Token JWT singkat untuk koneksi Socket.io admin (HS256).
 * Membutuhkan session admin aktif + secret yang sama dengan JWT_SECRET di Node.
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

const SOCKET_JWT_PLACEHOLDER = 'ganti-dengan-string-acak-minimal-32-karakter-sama-dengan-node-env';
const SOCKET_JWT_DEV_FALLBACK = 'recepsionis-dev-jwt-secret-change-in-production';

$socketSecret = null;
$secretFile = dirname(__DIR__) . '/config.socket.php';
if (is_file($secretFile)) {
    require $secretFile;
    if (defined('SOCKET_JWT_SECRET')) {
        $socketSecret = SOCKET_JWT_SECRET;
    }
}

if (
    $socketSecret === null
    || $socketSecret === ''
    || $socketSecret === SOCKET_JWT_PLACEHOLDER
) {
    $envSecret = getenv('SOCKET_JWT_SECRET');
    if ($envSecret !== false && $envSecret !== '') {
        $socketSecret = $envSecret;
    }
}

if ($socketSecret === null || $socketSecret === '') {
    $socketSecret = SOCKET_JWT_DEV_FALLBACK;
}

require_once dirname(__DIR__) . '/koneksi.php';
require_once dirname(__DIR__) . '/staff_call_routing.php';

$userId = (int) $_SESSION['user_id'];
$stmt = $koneksi->prepare('SELECT id, nama_lengkap, username, role FROM users WHERE id = ? AND status_aktif = 1 LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'User tidak valid'], JSON_UNESCAPED_UNICODE);
    exit;
}

$display = $row['nama_lengkap'] ?: $row['username'];
$prefs = recepsionis_get_notification_preferences($koneksi, $userId);
$categoryIds = recepsionis_get_admin_category_ids($koneksi, $userId);

/**
 * @param array<string,mixed> $payload
 */
function recepsionis_jwt_hs256_encode(array $payload, string $secret, int $ttlSeconds = 28800): string
{
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256'], JSON_UNESCAPED_SLASHES);
    $payload['iat'] = time();
    $payload['exp'] = time() + $ttlSeconds;
    $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

    $b64 = static function (string $raw): string {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    };

    $h = $b64($header);
    $p = $b64($payloadJson);
    $signing = $h . '.' . $p;
    $sig = $b64(hash_hmac('sha256', $signing, $secret, true));

    return $signing . '.' . $sig;
}

$token = recepsionis_jwt_hs256_encode([
    'sub' => (string) $row['id'],
    'name' => $display,
    'role' => $row['role'],
], $socketSecret);

echo json_encode([
    'success' => true,
    'token' => $token,
    'user' => [
        'id' => (int) $row['id'],
        'name' => $display,
        'role' => $row['role'],
    ],
    'preferences' => [
        'notifications_enabled' => (bool) ($prefs['notifications_enabled'] ?? true),
        'sound_enabled' => (bool) $prefs['sound_enabled'],
    ],
    'routing' => [
        'category_ids' => $categoryIds,
    ],
], JSON_UNESCAPED_UNICODE);
