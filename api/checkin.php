<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

$nama = esc($data['nama'] ?? '');
$email = esc($data['email'] ?? '');
$no_telp = esc($data['no_telp'] ?? '');
$perusahaan = esc($data['perusahaan'] ?? '');
$host_id = intval($data['host_id'] ?? 0);
$tujuan = esc($data['tujuan'] ?? '');

if (empty($nama) || empty($no_telp) || empty($host_id) || empty($tujuan)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Validate host
$host_check = $koneksi->query("SELECT * FROM hosts WHERE id = $host_id AND status_aktif = 1");
if ($host_check->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Host tidak valid']);
    exit;
}

// Generate badge number
$badge_number = generateBadgeNumber();

// Insert visitor
$koneksi->query("INSERT INTO visitors 
                 (nama, email, no_telp, perusahaan, tujuan, host_id, status, checkin_time, badge_number) 
                 VALUES ('$nama', '$email', '$no_telp', '$perusahaan', '$tujuan', $host_id, 'checked-in', NOW(), '$badge_number')");

$visitor_id = $koneksi->insert_id;

// Add to queue if requested
$queue_number = null;
if (isset($data['add_to_queue']) && $data['add_to_queue']) {
    $queue_number = generateQueueNumber($host_id);
    $koneksi->query("INSERT INTO queue (visitor_id, host_id, nomor_antrian, status) 
                     VALUES ($visitor_id, $host_id, '$queue_number', 'waiting')");
}

// Create notification
$host = $host_check->fetch_assoc();
$notification_title = "Tamu Baru: " . $nama;
$notification_message = "$nama telah check-in untuk bertemu dengan Anda. Tujuan: $tujuan";
$koneksi->query("INSERT INTO notifications (host_id, visitor_id, type, title, message) 
                 VALUES ($host_id, $visitor_id, 'checkin', '$notification_title', '$notification_message')");

echo json_encode([
    'success' => true,
    'message' => 'Check-in berhasil',
    'data' => [
        'visitor_id' => $visitor_id,
        'badge_number' => $badge_number,
        'queue_number' => $queue_number
    ]
]);
?>
