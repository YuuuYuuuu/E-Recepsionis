<?php
require_once 'auth.php';
requireSuperAdminPage();

$perangkat_options = ['Smartboard', 'Microphone', 'Kamera', 'Proyektor'];

// Handle actions
if (isset($_POST['tambah_ruangan'])) {
    $nama = esc($_POST['nama_ruangan']);
    $kode = esc($_POST['kode_ruangan']);
    $lokasi = esc($_POST['lokasi']);
    $lantai = esc($_POST['lantai'] ?? '');
    $gedung = esc($_POST['gedung'] ?? '');
    $kapasitas = intval($_POST['kapasitas'] ?? 0);
    $deskripsi = esc($_POST['deskripsi'] ?? '');
    $perangkat_raw = $_POST['perangkat_list'] ?? [];
    $perangkat_selected = [];
    if (is_array($perangkat_raw)) {
        foreach ($perangkat_raw as $item) {
            $item = trim((string)$item);
            if (in_array($item, $perangkat_options, true)) {
                $perangkat_selected[] = $item;
            }
        }
    }
    $perangkat = implode("\n", array_values(array_unique($perangkat_selected)));
    $mode_ruangan = esc($_POST['mode_ruangan'] ?? '');
    $koneksi->query("INSERT INTO rooms (nama_ruangan, kode_ruangan, lokasi, lantai, gedung, kapasitas, deskripsi, perangkat, mode_ruangan) 
                     VALUES ('$nama', '$kode', '$lokasi', '$lantai', '$gedung', $kapasitas, '$deskripsi', '" . $koneksi->real_escape_string($perangkat) . "', '" . $koneksi->real_escape_string($mode_ruangan) . "')");
    header("Location: rooms.php?success=added");
    exit;
}

if (isset($_POST['edit_ruangan'])) {
    $id = intval($_POST['id']);
    $nama = esc($_POST['nama_ruangan']);
    $kode = esc($_POST['kode_ruangan']);
    $lokasi = esc($_POST['lokasi']);
    $lantai = esc($_POST['lantai'] ?? '');
    $gedung = esc($_POST['gedung'] ?? '');
    $kapasitas = intval($_POST['kapasitas'] ?? 0);
    $deskripsi = esc($_POST['deskripsi'] ?? '');
    $perangkat_raw = $_POST['perangkat_list'] ?? [];
    $perangkat_selected = [];
    if (is_array($perangkat_raw)) {
        foreach ($perangkat_raw as $item) {
            $item = trim((string)$item);
            if (in_array($item, $perangkat_options, true)) {
                $perangkat_selected[] = $item;
            }
        }
    }
    $perangkat = implode("\n", array_values(array_unique($perangkat_selected)));
    $mode_ruangan = esc($_POST['mode_ruangan'] ?? '');
    $koneksi->query("UPDATE rooms SET nama_ruangan='$nama', kode_ruangan='$kode', lokasi='$lokasi', 
                     lantai='$lantai', gedung='$gedung', kapasitas=$kapasitas, deskripsi='$deskripsi', 
                     perangkat='" . $koneksi->real_escape_string($perangkat) . "', mode_ruangan='" . $koneksi->real_escape_string($mode_ruangan) . "' 
                     WHERE id=$id");
    header("Location: rooms.php?success=updated");
    exit;
}

if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $status = intval($_GET['status']);
    $koneksi->query("UPDATE rooms SET status_aktif=$status WHERE id=$id");
    header("Location: rooms.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $koneksi->query("DELETE FROM rooms WHERE id=$id");
    header("Location: rooms.php?success=deleted");
    exit;
}

// Get rooms
$rooms = $koneksi->query("SELECT * FROM rooms ORDER BY gedung, lantai, nama_ruangan");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ruangan - E-Recepsionis System</title>
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
                    <h2><i class="bi bi-door-open"></i> Daftar Ruangan</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle"></i> Tambah Ruangan
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> 
                        <?php
                        if ($_GET['success'] == 'added') echo 'Ruangan berhasil ditambahkan';
                        elseif ($_GET['success'] == 'updated') echo 'Ruangan berhasil diupdate';
                        elseif ($_GET['success'] == 'deleted') echo 'Ruangan berhasil dihapus';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Rooms Table -->
                <div class="card shadow-sm">
                    <div class="card-header" style="background: linear-gradient(135deg, #2563eb, #0369a1); color: white;">
                        <i class="bi bi-list"></i> Daftar Ruangan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Ruangan</th>
                                        <th>Lokasi</th>
                                        <th>Gedung</th>
                                        <th>Lantai</th>
                                        <th>Kapasitas</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($rooms && $rooms->num_rows > 0): ?>
                                        <?php while ($room = $rooms->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($room['kode_ruangan']) ?></strong></td>
                                                <td><?= htmlspecialchars($room['nama_ruangan']) ?></td>
                                                <td><?= htmlspecialchars($room['lokasi']) ?></td>
                                                <td><?= htmlspecialchars($room['gedung'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($room['lantai'] ?? '-') ?></td>
                                                <td><?= $room['kapasitas'] > 0 ? $room['kapasitas'] . ' orang' : '-' ?></td>
                                                <td>
                                                    <?php if ($room['status_aktif']): ?>
                                                        <span class="badge bg-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="room_gallery.php?room_id=<?= $room['id'] ?>" 
                                                           class="btn btn-secondary btn-sm"
                                                           title="Kelola Gambar">
                                                            <i class="bi bi-images"></i>
                                                        </a>
                                                        <a href="?toggle=<?= $room['id'] ?>&status=<?= $room['status_aktif'] ? 0 : 1 ?>" 
                                                           class="btn btn-<?= $room['status_aktif'] ? 'warning' : 'success' ?> btn-sm"
                                                           title="<?= $room['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                            <i class="bi bi-<?= $room['status_aktif'] ? 'x-circle' : 'check-circle' ?>"></i>
                                                        </a>
                                                        <button class="btn btn-info btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editModal<?= $room['id'] ?>"
                                                                title="Edit Ruangan">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="?delete=<?= $room['id'] ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Yakin ingin menghapus ruangan <?= htmlspecialchars($room['nama_ruangan']) ?>?')"
                                                           title="Hapus Ruangan">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?= $room['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background: linear-gradient(135deg, #2563eb, #0369a1); color: white;">
                                                            <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Ruangan</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <input type="hidden" name="id" value="<?= $room['id'] ?>">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nama Ruangan *</label>
                                                                    <input type="text" name="nama_ruangan" class="form-control" value="<?= htmlspecialchars($room['nama_ruangan']) ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Kode Ruangan *</label>
                                                                    <input type="text" name="kode_ruangan" class="form-control" value="<?= htmlspecialchars($room['kode_ruangan']) ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Lokasi *</label>
                                                                    <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($room['lokasi']) ?>" required>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Gedung</label>
                                                                        <input type="text" name="gedung" class="form-control" value="<?= htmlspecialchars($room['gedung'] ?? '') ?>">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Lantai</label>
                                                                        <input type="text" name="lantai" class="form-control" value="<?= htmlspecialchars($room['lantai'] ?? '') ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Kapasitas</label>
                                                                    <input type="number" name="kapasitas" class="form-control" value="<?= $room['kapasitas'] ?>" min="0">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Deskripsi</label>
                                                                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($room['deskripsi'] ?? '') ?></textarea>
                                                                </div>
                                                                <?php
                                                                    $perangkat_existing = str_replace(["\\r\\n", "\\n", "\r"], "\n", (string)($room['perangkat'] ?? ''));
                                                                    $perangkat_existing = array_filter(array_map('trim', explode("\n", $perangkat_existing)));
                                                                ?>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Perangkat</label>
                                                                    <div class="border rounded p-3 bg-light">
                                                                        <?php foreach ($perangkat_options as $opt): ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" name="perangkat_list[]" value="<?= $opt ?>" id="perangkat_<?= $room['id'] ?>_<?= strtolower($opt) ?>"
                                                                                    <?= in_array($opt, $perangkat_existing, true) ? 'checked' : '' ?>>
                                                                                <label class="form-check-label" for="perangkat_<?= $room['id'] ?>_<?= strtolower($opt) ?>">
                                                                                    <?= $opt ?>
                                                                                </label>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Mode Ruangan</label>
                                                                    <input type="text" name="mode_ruangan" class="form-control" value="<?= htmlspecialchars($room['mode_ruangan'] ?? '') ?>">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="bi bi-x-circle"></i> Batal
                                                                </button>
                                                                <button type="submit" name="edit_ruangan" class="btn btn-primary">
                                                                    <i class="bi bi-check-circle"></i> Simpan Perubahan
                                                                </button>
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
                                                Tidak ada ruangan
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
                <div class="modal-header" style="background: linear-gradient(135deg, #2563eb, #0369a1); color: white;">
                    <h5 class="modal-title"><i class="bi bi-door-open"></i> Tambah Ruangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Ruangan *</label>
                            <input type="text" name="nama_ruangan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Ruangan *</label>
                            <input type="text" name="kode_ruangan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi *</label>
                            <input type="text" name="lokasi" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gedung</label>
                                <input type="text" name="gedung" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lantai</label>
                                <input type="text" name="lantai" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kapasitas</label>
                            <input type="number" name="kapasitas" class="form-control" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Perangkat</label>
                            <div class="border rounded p-3 bg-light">
                                <?php foreach ($perangkat_options as $opt): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="perangkat_list[]" value="<?= $opt ?>" id="add_perangkat_<?= strtolower($opt) ?>">
                                        <label class="form-check-label" for="add_perangkat_<?= strtolower($opt) ?>">
                                            <?= $opt ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mode Ruangan</label>
                            <input type="text" name="mode_ruangan" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" name="tambah_ruangan" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Ruangan
                        </button>
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
