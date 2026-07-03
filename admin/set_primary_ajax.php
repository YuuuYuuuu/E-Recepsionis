<?php
require_once 'auth.php';
requireSuperAdminPage();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
$src = isset($_POST['src']) ? trim($_POST['src']) : '';
$action = isset($_POST['action']) ? $_POST['action'] : 'set';

if ($room_id <= 0 || $src === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$res = $koneksi->query("SELECT images FROM rooms WHERE id = " . $room_id . " LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Room not found']);
    exit;
}

$row = $res->fetch_assoc();
$current = array_filter(array_map('trim', explode(',', $row['images'] ?? '')));

// Find matching index by basename or full path
$found = null;
$src_basename = basename($src);
foreach ($current as $i => $c) {
    if (basename($c) === $src_basename || $c === $src) {
        $found = $i;
        break;
    }
}

if (is_null($found)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Image not found in room']);
    exit;
}

if ($action === 'set') {
    $selected = $current[$found];
    unset($current[$found]);
    array_unshift($current, $selected);
} else {
    // unset -> move to end
    $selected = $current[$found];
    unset($current[$found]);
    $current[] = $selected;
}

$new_csv = !empty($current) ? $koneksi->real_escape_string(implode(',', $current)) : null;
$sql = is_null($new_csv) ? "UPDATE rooms SET images = NULL WHERE id = $room_id" : "UPDATE rooms SET images = '$new_csv' WHERE id = $room_id";
$ok = $koneksi->query($sql);

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'OK']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $koneksi->error]);
}

exit;

?>
