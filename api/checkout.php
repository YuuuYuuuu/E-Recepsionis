<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

$badge_number = esc($data['badge_number'] ?? '');
$visitor_id = intval($data['visitor_id'] ?? 0);

if (empty($badge_number) && !$visitor_id) {
    echo json_encode(['success' => false, 'message' => 'Badge number atau visitor ID diperlukan']);
    exit;
}

// Get visitor
if ($visitor_id) {
    $visitor = $koneksi->query("SELECT * FROM visitors WHERE id = $visitor_id AND status = 'checked-in'")->fetch_assoc();
} else {
    $badge_esc = esc($badge_number);
    $visitor = $koneksi->query("SELECT * FROM visitors WHERE badge_number = '$badge_esc' AND status = 'checked-in'")->fetch_assoc();
}

if (!$visitor) {
    echo json_encode(['success' => false, 'message' => 'Tamu tidak ditemukan atau sudah check-out']);
    exit;
}

// Update visitor status
$koneksi->query("UPDATE visitors SET status = 'checked-out', checkout_time = NOW() WHERE id = {$visitor['id']}");

// Update queue if exists
$koneksi->query("UPDATE queue SET status = 'completed', waktu_selesai = NOW() 
                 WHERE visitor_id = {$visitor['id']} AND status IN ('waiting', 'in-progress')");

// Create notification
$koneksi->query("INSERT INTO notifications (host_id, visitor_id, type, title, message) 
                 VALUES ({$visitor['host_id']}, {$visitor['id']}, 'checkout', 
                         'Check-Out: {$visitor['nama']}', 
                         '{$visitor['nama']} telah check-out')");

echo json_encode([
    'success' => true,
    'message' => 'Check-out berhasil',
    'data' => [
        'visitor_id' => $visitor['id'],
        'nama' => $visitor['nama'],
        'checkout_time' => date('Y-m-d H:i:s')
    ]
]);
?>
