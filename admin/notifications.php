<?php
require_once 'auth.php';
requireSuperAdminPage();

// Mark notification as read
if (isset($_GET['read'])) {
    $id = intval($_GET['read']);
    $koneksi->query("UPDATE notifications SET status = 'read' WHERE id = $id");
    header("Location: " . (function_exists('adminUrl') ? adminUrl('notifications.php') : 'notifications.php'));
    exit;
}

// Mark all as read
if (isset($_GET['read_all'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    if ($role == 'admin') {
        $koneksi->query("UPDATE notifications SET status = 'read' WHERE host_id IS NULL");
    } else {
        // For hosts, would need host_id from session
        // $koneksi->query("UPDATE notifications SET status = 'read' WHERE host_id = $host_id");
    }
    header("Location: " . (function_exists('adminUrl') ? adminUrl('notifications.php') : 'notifications.php'));
    exit;
}

// Get notifications
$status_filter = $_GET['status'] ?? 'unread';
$query = "SELECT n.*, v.nama as visitor_nama, h.nama as host_nama 
          FROM notifications n 
          LEFT JOIN visitors v ON n.visitor_id = v.id 
          LEFT JOIN hosts h ON n.host_id = h.id";

if ($status_filter != 'all') {
    $status_filter_esc = esc($status_filter);
    $query .= " WHERE n.status = '$status_filter_esc'";
}

$query .= " ORDER BY n.created_at DESC LIMIT 100";
$notifications = $koneksi->query($query);

// Count unread
$unread_count = $koneksi->query("SELECT COUNT(*) as count FROM notifications WHERE status = 'unread'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - E-Recepsionis System</title>
    <script>
        // Store original title for notification badge system
        window.originalPageTitle = 'Notifikasi - E-Recepsionis System';
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <?php include 'include_staff_call_head.php'; ?>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <div class="col-md-10 content-area">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="bi bi-bell"></i> Notifikasi
                        <?php if ($unread_count > 0): ?>
                            <span class="badge bg-danger"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </h2>
                    <?php if ($unread_count > 0): ?>
                        <a href="?read_all=1" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-all"></i> Tandai Semua Dibaca
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Filter -->
                <div class="card mb-3 adm-filter-panel">
                    <div class="card-body">
                        <div class="adm-filter-toolbar">
                            <div class="adm-filter-group">
                                <span class="adm-filter-label"><i class="bi bi-funnel"></i> Status</span>
                                <div class="adm-segment" role="group" aria-label="Filter notifikasi">
                                    <a href="?status=unread" class="adm-segment-item <?= $status_filter == 'unread' ? 'is-active' : '' ?>">
                                        <i class="bi bi-envelope"></i> Belum Dibaca
                                        <?php if ($unread_count > 0): ?>
                                            <span class="adm-segment-badge"><?= $unread_count > 99 ? '99+' : (int) $unread_count ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a href="?status=read" class="adm-segment-item <?= $status_filter == 'read' ? 'is-active' : '' ?>">
                                        <i class="bi bi-envelope-open"></i> Sudah Dibaca
                                    </a>
                                    <a href="?status=all" class="adm-segment-item <?= $status_filter == 'all' ? 'is-active' : '' ?>">
                                        <i class="bi bi-list-ul"></i> Semua
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Daftar Notifikasi
                    </div>
                    <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
                        <?php if ($notifications && $notifications->num_rows > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while ($notif = $notifications->fetch_assoc()): ?>
                                    <div class="list-group-item <?= $notif['status'] == 'unread' ? 'bg-light' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <?php if ($notif['status'] == 'unread'): ?>
                                                        <span class="badge bg-danger me-2">Baru</span>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($notif['title']) ?>
                                                </h6>
                                                <p class="mb-1"><?= htmlspecialchars($notif['message']) ?></p>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock"></i> <?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                                                    <?php if ($notif['visitor_nama']): ?>
                                                        | <i class="bi bi-person"></i> <?= htmlspecialchars($notif['visitor_nama']) ?>
                                                    <?php endif; ?>
                                                    <?php if ($notif['host_nama']): ?>
                                                        | <i class="bi bi-person-badge"></i> <?= htmlspecialchars($notif['host_nama']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <?php if ($notif['status'] == 'unread'): ?>
                                                <a href="?read=<?= $notif['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-check"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                                Tidak ada notifikasi
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/notification-badge.js"></script>
    <?php include 'include_staff_call_footer.php'; ?>
</body>
</html>
