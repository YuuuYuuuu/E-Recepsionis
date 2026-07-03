<?php
require_once 'auth.php';
requireSuperAdminPage();

// Handle actions
if (isset($_POST['tambah_appointment'])) {
    $host_id = intval($_POST['host_id']);
    $nama_visitor = esc($_POST['nama_visitor']);
    $email_visitor = esc($_POST['email_visitor'] ?? '');
    $no_telp_visitor = esc($_POST['no_telp_visitor'] ?? '');
    $perusahaan_visitor = esc($_POST['perusahaan_visitor'] ?? '');
    $tanggal = esc($_POST['tanggal']);
    $waktu_mulai = esc($_POST['waktu_mulai']);
    $waktu_selesai = esc($_POST['waktu_selesai']);
    $deskripsi = esc($_POST['deskripsi'] ?? '');
    
    $koneksi->query("INSERT INTO appointments 
                     (host_id, nama_visitor, email_visitor, no_telp_visitor, perusahaan_visitor, 
                      tanggal, waktu_mulai, waktu_selesai, deskripsi, status) 
                     VALUES ($host_id, '$nama_visitor', '$email_visitor', '$no_telp_visitor', '$perusahaan_visitor',
                             '$tanggal', '$waktu_mulai', '$waktu_selesai', '$deskripsi', 'pending')");
    header("Location: appointments.php?success=added");
    exit;
}

if (isset($_GET['confirm'])) {
    $id = intval($_GET['confirm']);
    $koneksi->query("UPDATE appointments SET status = 'confirmed' WHERE id = $id");
    header("Location: appointments.php?success=confirmed");
    exit;
}

if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $koneksi->query("UPDATE appointments SET status = 'cancelled' WHERE id = $id");
    header("Location: appointments.php?success=cancelled");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $koneksi->query("DELETE FROM appointments WHERE id = $id");
    header("Location: appointments.php?success=deleted");
    exit;
}

// Get appointments
$status_filter = $_GET['status'] ?? 'all';
$query = "SELECT a.*, h.nama as host_nama 
          FROM appointments a 
          JOIN hosts h ON a.host_id = h.id";
          
if ($status_filter != 'all') {
    $status_filter_esc = esc($status_filter);
    $query .= " WHERE a.status = '$status_filter_esc'";
}

$query .= " ORDER BY a.tanggal DESC, a.waktu_mulai ASC";
$appointments = $koneksi->query($query);

// Get hosts for dropdown
$hosts = $koneksi->query("SELECT * FROM hosts WHERE status_aktif = 1 ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment - E-Recepsionis System</title>
    <script>
        window.originalPageTitle = 'Appointment - E-Recepsionis System';
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
                    <h2><i class="bi bi-calendar-check"></i> Appointment</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle"></i> Buat Appointment
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> 
                        <?php
                        if ($_GET['success'] == 'added') echo 'Appointment berhasil dibuat';
                        elseif ($_GET['success'] == 'confirmed') echo 'Appointment dikonfirmasi';
                        elseif ($_GET['success'] == 'cancelled') echo 'Appointment dibatalkan';
                        elseif ($_GET['success'] == 'deleted') echo 'Appointment dihapus';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="?status=all" class="btn btn-sm <?= $status_filter == 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Semua
                            </a>
                            <a href="?status=pending" class="btn btn-sm <?= $status_filter == 'pending' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Pending
                            </a>
                            <a href="?status=confirmed" class="btn btn-sm <?= $status_filter == 'confirmed' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Confirmed
                            </a>
                            <a href="?status=completed" class="btn btn-sm <?= $status_filter == 'completed' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Completed
                            </a>
                            <a href="?status=cancelled" class="btn btn-sm <?= $status_filter == 'cancelled' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                Cancelled
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Daftar Appointment
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Tamu</th>
                                        <th>Host</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($appointments && $appointments->num_rows > 0): ?>
                                        <?php while ($app = $appointments->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($app['tanggal'])) ?></td>
                                                <td><?= date('H:i', strtotime($app['waktu_mulai'])) ?> - <?= date('H:i', strtotime($app['waktu_selesai'])) ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($app['nama_visitor']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($app['perusahaan_visitor'] ?? '') ?></small>
                                                </td>
                                                <td><?= htmlspecialchars($app['host_nama']) ?></td>
                                                <td>
                                                    <span class="badge-status <?= $app['status'] ?>">
                                                        <?= ucfirst($app['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?php if ($app['status'] == 'pending'): ?>
                                                            <a href="?confirm=<?= $app['id'] ?>" class="btn btn-success btn-sm">
                                                                <i class="bi bi-check"></i> Confirm
                                                            </a>
                                                            <a href="?cancel=<?= $app['id'] ?>" class="btn btn-warning btn-sm">
                                                                <i class="bi bi-x"></i> Cancel
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="?delete=<?= $app['id'] ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Hapus appointment ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                                                Tidak ada appointment
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Buat Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Host *</label>
                            <select name="host_id" class="form-select" required>
                                <option value="">Pilih Host</option>
                                <?php while ($host = $hosts->fetch_assoc()): ?>
                                    <option value="<?= $host['id'] ?>"><?= htmlspecialchars($host['nama']) ?> - <?= htmlspecialchars($host['departemen']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Tamu *</label>
                            <input type="text" name="nama_visitor" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email_visitor" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telp</label>
                                <input type="text" name="no_telp_visitor" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" name="perusahaan_visitor" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Waktu Mulai *</label>
                                <input type="time" name="waktu_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Waktu Selesai *</label>
                                <input type="time" name="waktu_selesai" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_appointment" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/notification-badge.js"></script>
    <?php include 'include_staff_call_footer.php'; ?>
</body>
</html>
