<?php
require_once 'auth.php';
requireSuperAdminPage();

// Handle actions
if (isset($_POST['tambah_host'])) {
    $nama = esc($_POST['nama']);
    $email = esc($_POST['email'] ?? '');
    $no_telp = esc($_POST['no_telp'] ?? '');
    $departemen = esc($_POST['departemen'] ?? '');
    $jabatan = esc($_POST['jabatan'] ?? '');
    
    $koneksi->query("INSERT INTO hosts (nama, email, no_telp, departemen, jabatan) 
                     VALUES ('$nama', '$email', '$no_telp', '$departemen', '$jabatan')");
    header("Location: hosts.php?success=added");
    exit;
}

if (isset($_POST['edit_host'])) {
    $id = intval($_POST['id']);
    $nama = esc($_POST['nama']);
    $email = esc($_POST['email'] ?? '');
    $no_telp = esc($_POST['no_telp'] ?? '');
    $departemen = esc($_POST['departemen'] ?? '');
    $jabatan = esc($_POST['jabatan'] ?? '');
    
    $koneksi->query("UPDATE hosts SET nama='$nama', email='$email', no_telp='$no_telp', 
                     departemen='$departemen', jabatan='$jabatan' WHERE id=$id");
    header("Location: hosts.php?success=updated");
    exit;
}

if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $status = intval($_GET['status']);
    $koneksi->query("UPDATE hosts SET status_aktif=$status WHERE id=$id");
    header("Location: hosts.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $koneksi->query("DELETE FROM hosts WHERE id=$id");
    header("Location: hosts.php?success=deleted");
    exit;
}

// Get hosts
$hosts = $koneksi->query("SELECT * FROM hosts ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host - E-Recepsionis System</title>
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
                    <h2><i class="bi bi-person-badge"></i> Host</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle"></i> Tambah Host
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> 
                        <?php
                        if ($_GET['success'] == 'added') echo 'Host berhasil ditambahkan';
                        elseif ($_GET['success'] == 'updated') echo 'Host berhasil diupdate';
                        elseif ($_GET['success'] == 'deleted') echo 'Host berhasil dihapus';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Hosts Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Daftar Host
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. Telp</th>
                                        <th>Departemen</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($hosts && $hosts->num_rows > 0): ?>
                                        <?php while ($host = $hosts->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($host['nama']) ?></strong></td>
                                                <td><?= htmlspecialchars($host['email'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($host['no_telp'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($host['departemen'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($host['jabatan'] ?? '-') ?></td>
                                                <td>
                                                    <?php if ($host['status_aktif']): ?>
                                                        <span class="badge bg-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?toggle=<?= $host['id'] ?>&status=<?= $host['status_aktif'] ? 0 : 1 ?>" 
                                                           class="btn btn-<?= $host['status_aktif'] ? 'warning' : 'success' ?> btn-sm">
                                                            <i class="bi bi-<?= $host['status_aktif'] ? 'x-circle' : 'check-circle' ?>"></i>
                                                        </a>
                                                        <button class="btn btn-info btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editModal<?= $host['id'] ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="?delete=<?= $host['id'] ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Hapus host ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?= $host['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Host</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <input type="hidden" name="id" value="<?= $host['id'] ?>">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nama *</label>
                                                                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($host['nama']) ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Email</label>
                                                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($host['email'] ?? '') ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">No. Telp</label>
                                                                    <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($host['no_telp'] ?? '') ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Departemen</label>
                                                                    <input type="text" name="departemen" class="form-control" value="<?= htmlspecialchars($host['departemen'] ?? '') ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Jabatan</label>
                                                                    <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($host['jabatan'] ?? '') ?>">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" name="edit_host" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                                                Tidak ada host
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
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Host</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama *</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telp</label>
                            <input type="text" name="no_telp" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" name="departemen" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_host" class="btn btn-primary">Simpan</button>
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
