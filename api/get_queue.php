<?php
require_once '../config.php';

header('Content-Type: application/json');

$host_id = intval($_GET['host_id'] ?? 0);
$status = $_GET['status'] ?? 'active';

$query = "SELECT q.*, v.nama as visitor_nama, v.badge_number, h.nama as host_nama 
          FROM queue q 
          JOIN visitors v ON q.visitor_id = v.id 
          JOIN hosts h ON q.host_id = h.id";

if ($status == 'active') {
    $query .= " WHERE q.status IN ('waiting', 'in-progress')";
} elseif ($status != 'all') {
    $status_esc = esc($status);
    $query .= " WHERE q.status = '$status_esc'";
}

if ($host_id > 0) {
    $query .= ($status == 'active' || $status != 'all') ? " AND q.host_id = $host_id" : " WHERE q.host_id = $host_id";
}

$query .= " ORDER BY q.waktu_masuk ASC";

$result = $koneksi->query($query);
$queue = [];

while ($row = $result->fetch_assoc()) {
    $queue[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $queue,
    'count' => count($queue)
]);
?>
