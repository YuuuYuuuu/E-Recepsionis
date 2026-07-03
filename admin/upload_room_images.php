<?php
require_once 'auth.php';
requireSuperAdminPage();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rooms.php');
    exit;
}

$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
if ($room_id <= 0 || !isset($_FILES['images'])) {
    header('Location: rooms.php');
    exit;
}

// Verify room exists
$res = $koneksi->query("SELECT id, images FROM rooms WHERE id = " . (int)$room_id . " LIMIT 1");
if (!$res || $res->num_rows === 0) {
    header('Location: rooms.php');
    exit;
}

$row = $res->fetch_assoc();
$existing_images = array_filter(array_map('trim', explode(',', $row['images'] ?? '')));

// Upload directory
$upload_dir = __DIR__ . '/../uploads/rooms/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$new_images = [];
$uploaded_count = 0;

// Process each uploaded file
foreach ($_FILES['images']['tmp_name'] as $idx => $tmp_name) {
    if (empty($tmp_name) || $_FILES['images']['error'][$idx] !== UPLOAD_ERR_OK) {
        continue;
    }

    $file_name = $_FILES['images']['name'][$idx];
    $file_type = $_FILES['images']['type'][$idx];
    
    // Validate image
    if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
        continue;
    }

    // Create safe filename with timestamp
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $safe_name = 'room_' . $room_id . '_' . time() . '_' . rand(1000, 9999) . '.' . strtolower($ext);
    $file_path = $upload_dir . $safe_name;

    if (move_uploaded_file($tmp_name, $file_path)) {
        $new_images[] = 'uploads/rooms/' . $safe_name;
        $uploaded_count++;
    }
}

// Merge with existing images
$all_images = array_merge($existing_images, $new_images);
$all_images = array_unique($all_images);
$images_csv = !empty($all_images) ? implode(',', $all_images) : NULL;

// Update DB
$img_sql = is_null($images_csv) ? 'NULL' : "'" . $koneksi->real_escape_string($images_csv) . "'";
$koneksi->query("UPDATE rooms SET images = $img_sql WHERE id = " . (int)$room_id);

// Redirect back
header('Location: room_gallery.php?room_id=' . (int)$room_id . '&uploaded=' . $uploaded_count);
exit;
?>
