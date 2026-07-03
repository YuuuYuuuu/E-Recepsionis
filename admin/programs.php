<?php
require_once 'auth.php';
requireSuperAdminPage();

// Handle actions
if (isset($_POST['tambah_program'])) {
    $judul = esc($_POST['judul']);
    $deskripsi = esc($_POST['deskripsi'] ?? '');
    $kategori = esc($_POST['kategori']);
    $tanggal = esc($_POST['tanggal'] ?? '');
    $waktu_mulai = esc($_POST['waktu_mulai'] ?? '');
    $waktu_selesai = esc($_POST['waktu_selesai'] ?? '');
    $lokasi = esc($_POST['lokasi'] ?? '');
    $pengajar = esc($_POST['pengajar'] ?? '');
    $kontak = esc($_POST['kontak'] ?? '');
    
    $tanggal_sql = $tanggal ? "'$tanggal'" : 'NULL';
    $waktu_mulai_sql = $waktu_mulai ? "'$waktu_mulai'" : 'NULL';
    $waktu_selesai_sql = $waktu_selesai ? "'$waktu_selesai'" : 'NULL';
    
    $koneksi->query("INSERT INTO programs (judul, deskripsi, kategori, tanggal, waktu_mulai, waktu_selesai, lokasi, pengajar, kontak) 
                     VALUES ('$judul', '$deskripsi', '$kategori', $tanggal_sql, $waktu_mulai_sql, $waktu_selesai_sql, '$lokasi', '$pengajar', '$kontak')");
    header("Location: programs.php?success=added");
    exit;
}

if (isset($_POST['edit_program'])) {
    $id = intval($_POST['id']);
    $judul = esc($_POST['judul']);
    $deskripsi = esc($_POST['deskripsi'] ?? '');
    $kategori = esc($_POST['kategori']);
    $tanggal = esc($_POST['tanggal'] ?? '');
    $waktu_mulai = esc($_POST['waktu_mulai'] ?? '');
    $waktu_selesai = esc($_POST['waktu_selesai'] ?? '');
    $lokasi = esc($_POST['lokasi'] ?? '');
    $pengajar = esc($_POST['pengajar'] ?? '');
    $kontak = esc($_POST['kontak'] ?? '');
    
    $tanggal_sql = $tanggal ? "'$tanggal'" : 'NULL';
    $waktu_mulai_sql = $waktu_mulai ? "'$waktu_mulai'" : 'NULL';
    $waktu_selesai_sql = $waktu_selesai ? "'$waktu_selesai'" : 'NULL';
    
    $koneksi->query("UPDATE programs SET judul='$judul', deskripsi='$deskripsi', kategori='$kategori', 
                     tanggal=$tanggal_sql, waktu_mulai=$waktu_mulai_sql, waktu_selesai=$waktu_selesai_sql, 
                     lokasi='$lokasi', pengajar='$pengajar', kontak='$kontak' WHERE id=$id");
    header("Location: programs.php?success=updated");
    exit;
}

if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $status = intval($_GET['status']);
    $koneksi->query("UPDATE programs SET status_aktif=$status WHERE id=$id");
    header("Location: programs.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $koneksi->query("DELETE FROM programs WHERE id=$id");
    header("Location: programs.php?success=deleted");
    exit;
}

// Get programs
$programs = $koneksi->query("SELECT * FROM programs ORDER BY tanggal DESC, waktu_mulai ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Perkuliahan - E-Recepsionis System</title>
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
                    <h2><i class="bi bi-calendar-event"></i> Program Perkuliahan</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle"></i> Tambah Program
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> 
                        <?php
                        if ($_GET['success'] == 'added') echo 'Program berhasil ditambahkan';
                        elseif ($_GET['success'] == 'updated') echo 'Program berhasil diupdate';
                        elseif ($_GET['success'] == 'deleted') echo 'Program berhasil dihapus';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Programs Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Daftar Program
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Pengajar</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($programs && $programs->num_rows > 0): ?>
                                        <?php while ($program = $programs->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($program['judul']) ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $program['kategori'] == 'Seminar' ? 'warning' : ($program['kategori'] == 'Workshop' ? 'info' : 'success') ?>">
                                                        <?= htmlspecialchars($program['kategori']) ?>
                                                    </span>
                                                </td>
                                                <td><?= $program['tanggal'] ? date('d/m/Y', strtotime($program['tanggal'])) : '-' ?></td>
                                                <td>
                                                    <?php if ($program['waktu_mulai']): ?>
                                                        <?= date('H:i', strtotime($program['waktu_mulai'])) ?>
                                                        <?php if ($program['waktu_selesai']): ?>
                                                            - <?= date('H:i', strtotime($program['waktu_selesai'])) ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($program['lokasi'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($program['pengajar'] ?? '-') ?></td>
                                                <td>
                                                    <?php if ($program['status_aktif']): ?>
                                                        <span class="badge bg-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?toggle=<?= $program['id'] ?>&status=<?= $program['status_aktif'] ? 0 : 1 ?>" 
                                                           class="btn btn-<?= $program['status_aktif'] ? 'warning' : 'success' ?> btn-sm">
                                                            <i class="bi bi-<?= $program['status_aktif'] ? 'x-circle' : 'check-circle' ?>"></i>
                                                        </a>
                                                        <button class="btn btn-info btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editModal<?= $program['id'] ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="?delete=<?= $program['id'] ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Hapus program ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?= $program['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Program</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <input type="hidden" name="id" value="<?= $program['id'] ?>">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Judul *</label>
                                                                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($program['judul']) ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Deskripsi</label>
                                                                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($program['deskripsi'] ?? '') ?></textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Kategori *</label>
                                                                        <select name="kategori" class="form-select" required>
                                                                            <option value="Perkuliahan" <?= $program['kategori'] == 'Perkuliahan' ? 'selected' : '' ?>>Perkuliahan</option>
                                                                            <option value="Seminar" <?= $program['kategori'] == 'Seminar' ? 'selected' : '' ?>>Seminar</option>
                                                                            <option value="Workshop" <?= $program['kategori'] == 'Workshop' ? 'selected' : '' ?>>Workshop</option>
                                                                            <option value="Event" <?= $program['kategori'] == 'Event' ? 'selected' : '' ?>>Event</option>
                                                                            <option value="Lainnya" <?= $program['kategori'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Tanggal</label>
                                                                        <input type="date" name="tanggal" class="form-control" value="<?= $program['tanggal'] ? date('Y-m-d', strtotime($program['tanggal'])) : '' ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Waktu Mulai</label>
                                                                        <input type="time" name="waktu_mulai" class="form-control" value="<?= $program['waktu_mulai'] ? date('H:i', strtotime($program['waktu_mulai'])) : '' ?>">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Waktu Selesai</label>
                                                                        <input type="time" name="waktu_selesai" class="form-control" value="<?= $program['waktu_selesai'] ? date('H:i', strtotime($program['waktu_selesai'])) : '' ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Lokasi</label>
                                                                    <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($program['lokasi'] ?? '') ?>">
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Pengajar</label>
                                                                        <input type="text" name="pengajar" class="form-control" value="<?= htmlspecialchars($program['pengajar'] ?? '') ?>">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Kontak</label>
                                                                        <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($program['kontak'] ?? '') ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" name="edit_program" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                                                Tidak ada program
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Tambah Program</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul *</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori *</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="Perkuliahan">Perkuliahan</option>
                                    <option value="Seminar">Seminar</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Event">Event</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pengajar</label>
                                <input type="text" name="pengajar" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kontak</label>
                                <input type="text" name="kontak" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_program" class="btn btn-primary">Simpan</button>
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
