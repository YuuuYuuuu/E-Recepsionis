<?php
require_once 'auth.php';
requireSuperAdminPage();

// Handle actions
if (isset($_GET['call'])) {
    $id = intval($_GET['call']);
    $koneksi->query("UPDATE queue SET status = 'in-progress', waktu_dipanggil = NOW() WHERE id = $id");
    header("Location: queue.php?success=called");
    exit;
}

if (isset($_GET['complete'])) {
    $id = intval($_GET['complete']);
    $koneksi->query("UPDATE queue SET status = 'completed', waktu_selesai = NOW() WHERE id = $id");
    header("Location: queue.php?success=completed");
    exit;
}

if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $koneksi->query("UPDATE queue SET status = 'cancelled' WHERE id = $id");
    header("Location: queue.php?success=cancelled");
    exit;
}

// Get queue
$status_filter = $_GET['status'] ?? 'active';
$query = "SELECT q.*, v.nama as visitor_nama, v.badge_number, h.nama as host_nama 
          FROM queue q 
          JOIN visitors v ON q.visitor_id = v.id 
          JOIN hosts h ON q.host_id = h.id";
          
if ($status_filter == 'active') {
    $query .= " WHERE q.status IN ('waiting', 'in-progress')";
} elseif ($status_filter != 'all') {
    $status_filter_esc = esc($status_filter);
    $query .= " WHERE q.status = '$status_filter_esc'";
}

$query .= " ORDER BY q.waktu_masuk ASC";
$queue = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian - E-Recepsionis System</title>
    <script>
        window.originalPageTitle = 'Antrian - E-Recepsionis System';
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
                    <h2><i class="bi bi-list-ol"></i> Antrian</h2>
                    <a href="../visitor/queue.php" class="btn btn-primary" target="_blank">
                        <i class="bi bi-display"></i> Tampilan Antrian
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> 
                        <?php
                        if ($_GET['success'] == 'called') echo 'Antrian dipanggil';
                        elseif ($_GET['success'] == 'completed') echo 'Antrian selesai';
                        elseif ($_GET['success'] == 'cancelled') echo 'Antrian dibatalkan';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="?status=active" class="btn btn-sm <?= $status_filter == 'active' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Aktif
                            </a>
                            <a href="?status=waiting" class="btn btn-sm <?= $status_filter == 'waiting' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Waiting
                            </a>
                            <a href="?status=in-progress" class="btn btn-sm <?= $status_filter == 'in-progress' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                In Progress
                            </a>
                            <a href="?status=completed" class="btn btn-sm <?= $status_filter == 'completed' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Completed
                            </a>
                            <a href="?status=all" class="btn btn-sm <?= $status_filter == 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Semua
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Queue Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Daftar Antrian
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Antrian</th>
                                        <th>Tamu</th>
                                        <th>Badge</th>
                                        <th>Host</th>
                                        <th>Waktu Masuk</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($queue && $queue->num_rows > 0): ?>
                                        <?php while ($q = $queue->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <strong style="font-size: 1.2rem;"><?= htmlspecialchars($q['nomor_antrian']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($q['visitor_nama']) ?></td>
                                                <td><code><?= htmlspecialchars($q['badge_number']) ?></code></td>
                                                <td><?= htmlspecialchars($q['host_nama']) ?></td>
                                                <td><?= date('H:i', strtotime($q['waktu_masuk'])) ?></td>
                                                <td>
                                                    <span class="badge-status <?= $q['status'] ?>">
                                                        <?= ucfirst(str_replace('-', ' ', $q['status'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?php if ($q['status'] == 'waiting'): ?>
                                                            <a href="?call=<?= $q['id'] ?>" class="btn btn-success btn-sm">
                                                                <i class="bi bi-megaphone"></i> Panggil
                                                            </a>
                                                        <?php elseif ($q['status'] == 'in-progress'): ?>
                                                            <a href="?complete=<?= $q['id'] ?>" class="btn btn-primary btn-sm">
                                                                <i class="bi bi-check-circle"></i> Selesai
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="?cancel=<?= $q['id'] ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Batalkan antrian ini?')">
                                                            <i class="bi bi-x-circle"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                                                Tidak ada antrian
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
