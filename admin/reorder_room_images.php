<?php
require_once 'auth.php';
requireSuperAdminPage();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false, 'message'=>'Method not allowed']);
    exit;
}

$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
$order = isset($_POST['order']) ? $_POST['order'] : null; // expected array of filenames or paths

if ($room_id <= 0 || !is_array($order)) {
    http_response_code(400);
    echo json_encode(['success'=>false, 'message'=>'Invalid parameters']);
    exit;
}

// Load current images
$res = $koneksi->query("SELECT images FROM rooms WHERE id = " . $room_id . " LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success'=>false, 'message'=>'Room not found']);
    exit;
}

$row = $res->fetch_assoc();
$current = array_filter(array_map('trim', explode(',', $row['images'] ?? '')));

// Build new ordered list by matching basenames
$new = [];
foreach ($order as $ord) {
    $ord_basename = basename($ord);
    foreach ($current as $c) {
        if (basename($c) === $ord_basename) {
            $new[] = $c;
            break;
        }
    }
}

// Append any remaining images not included in order
foreach ($current as $c) {
    if (!in_array($c, $new)) $new[] = $c;
}

$csv = !empty($new) ? $koneksi->real_escape_string(implode(',', $new)) : null;
$sql = is_null($csv) ? "UPDATE rooms SET images = NULL WHERE id = $room_id" : "UPDATE rooms SET images = '$csv' WHERE id = $room_id";
$ok = $koneksi->query($sql);

if ($ok) {
    echo json_encode(['success'=>true, 'message'=>'Order saved']);
} else {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>$koneksi->error]);
}

exit;
?>
