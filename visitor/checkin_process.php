<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Get form data
$nama = esc($_POST['nama']);
$email = esc($_POST['email'] ?? '');
$no_telp = esc($_POST['no_telp']);
$perusahaan = esc($_POST['perusahaan'] ?? '');
$host_id = intval($_POST['host_id']);
$tujuan = esc($_POST['tujuan']);
$add_to_queue = isset($_POST['add_to_queue']) ? 1 : 0;

// Validate host
$host_check = $koneksi->query("SELECT * FROM hosts WHERE id = $host_id AND status_aktif = 1");
if ($host_check->num_rows == 0) {
    die("Host tidak valid!");
}

// Handle photo upload
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $file = $_FILES['foto'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (in_array($ext, ALLOWED_IMAGE_EXT) && $file['size'] <= MAX_UPLOAD_SIZE) {
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $destination = UPLOAD_PATH . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $foto = $filename;
        }
    }
}

// Generate badge number
$badge_number = generateBadgeNumber();

// Insert visitor
$foto_sql = $foto ? "'$foto'" : 'NULL';
$koneksi->query("INSERT INTO visitors 
                 (nama, email, no_telp, perusahaan, foto, tujuan, host_id, status, checkin_time, badge_number) 
                 VALUES ('$nama', '$email', '$no_telp', '$perusahaan', $foto_sql, '$tujuan', $host_id, 'checked-in', NOW(), '$badge_number')");

$visitor_id = $koneksi->insert_id;

// Add to queue if requested
$queue_number = null;
if ($add_to_queue) {
    $queue_number = generateQueueNumber($host_id);
    $koneksi->query("INSERT INTO queue (visitor_id, host_id, nomor_antrian, status) 
                     VALUES ($visitor_id, $host_id, '$queue_number', 'waiting')");
}

// Create notification for host
$host = $host_check->fetch_assoc();
$notification_title = "Tamu Baru: " . $nama;
$notification_message = "$nama telah check-in untuk bertemu dengan Anda. Tujuan: $tujuan";
$koneksi->query("INSERT INTO notifications (host_id, visitor_id, type, title, message) 
                 VALUES ($host_id, $visitor_id, 'checkin', '$notification_title', '$notification_message')");

// Send email notification if enabled
if (!empty($host['email'])) {
    require_once '../api/notify.php';
    if (function_exists('sendEmailNotification')) {
        $email_body = "<h2>Tamu Baru</h2>";
        $email_body .= "<p><strong>Nama:</strong> $nama</p>";
        $email_body .= "<p><strong>Perusahaan:</strong> " . ($perusahaan ?: '-') . "</p>";
        $email_body .= "<p><strong>Tujuan:</strong> $tujuan</p>";
        $email_body .= "<p><strong>Badge Number:</strong> $badge_number</p>";
        if ($add_to_queue && $queue_number) {
            $email_body .= "<p><strong>Nomor Antrian:</strong> $queue_number</p>";
        }
        sendEmailNotification($host['email'], $notification_title, $email_body);
    }
}

// Redirect to success page
header("Location: success.php?visitor_id=$visitor_id&badge=$badge_number" . ($add_to_queue && $queue_number ? "&queue=$queue_number" : ""));
exit;
?>
