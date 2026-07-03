<?php
require_once 'auth.php';
requireSuperAdminPage();

$upload_dir = __DIR__ . '/../uploads/floor_plans/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function floor_plan_image_url(string $path): string
{
    if (preg_match('~^(https?://|/)~', $path)) {
        return $path;
    }
    return '../' . ltrim($path, '/');
}

if (isset($_POST['tambah_denah'])) {
    $gedung = trim($_POST['gedung'] ?? '');
    $lantai = trim($_POST['lantai'] ?? '');
    if ($gedung === '' || $lantai === '') {
        header('Location: floor_plans.php?error=empty_gedung_lantai');
        exit;
    }
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        header('Location: floor_plans.php?error=no_image');
        exit;
    }
    $file_type = $_FILES['gambar']['type'] ?? '';
    if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
        header('Location: floor_plans.php?error=invalid_image');
        exit;
    }
    if ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
        header('Location: floor_plans.php?error=file_too_large');
        exit;
    }
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $safe = 'fp_' . preg_replace('/[^a-z0-9]+/i', '_', $gedung . '_' . $lantai) . '_' . time() . '.' . $ext;
    $dest = $upload_dir . $safe;
    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        header('Location: floor_plans.php?error=upload_failed');
        exit;
    }
    $gambar = 'uploads/floor_plans/' . $safe;
    $stmt = $koneksi->prepare('INSERT INTO floor_plans (gedung, lantai, gambar) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $gedung, $lantai, $gambar);
    if (!$stmt->execute()) {
        @unlink($dest);
        header('Location: floor_plans.php?error=duplicate');
        exit;
    }
    $stmt->close();
    header('Location: floor_plans.php?success=added');
    exit;
}

if (isset($_POST['update_denah'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $gedung = trim($_POST['gedung'] ?? '');
    $lantai = trim($_POST['lantai'] ?? '');
    if ($id <= 0 || $gedung === '' || $lantai === '') {
        header('Location: floor_plans.php?error=invalid');
        exit;
    }
    $res = $koneksi->query('SELECT gambar FROM floor_plans WHERE id = ' . $id . ' LIMIT 1');
    if (!$res || $res->num_rows === 0) {
        header('Location: floor_plans.php?error=not_found');
        exit;
    }
    $row = $res->fetch_assoc();
    $gambar = $row['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_type = $_FILES['gambar']['type'] ?? '';
        if (in_array($file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)
            && $_FILES['gambar']['size'] <= 5 * 1024 * 1024) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $safe = 'fp_' . $id . '_' . time() . '.' . $ext;
            $dest = $upload_dir . $safe;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
                $old_path = __DIR__ . '/../' . $gambar;
                if (is_file($old_path)) {
                    @unlink($old_path);
                }
                $gambar = 'uploads/floor_plans/' . $safe;
            }
        }
    }

    $stmt = $koneksi->prepare('UPDATE floor_plans SET gedung=?, lantai=?, gambar=? WHERE id=?');
    $stmt->bind_param('sssi', $gedung, $lantai, $gambar, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: floor_plans.php?edit=' . $id . '&success=updated');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $res = $koneksi->query('SELECT gambar FROM floor_plans WHERE id = ' . $id . ' LIMIT 1');
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $old_path = __DIR__ . '/../' . $row['gambar'];
            if (is_file($old_path)) {
                @unlink($old_path);
            }
        }
        $koneksi->query('DELETE FROM floor_plans WHERE id = ' . $id);
    }
    header('Location: floor_plans.php?success=deleted');
    exit;
}

$edit_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$edit_plan = null;

if ($edit_id > 0) {
    $res = $koneksi->query('SELECT * FROM floor_plans WHERE id = ' . $edit_id . ' LIMIT 1');
    if ($res && $res->num_rows > 0) {
        $edit_plan = $res->fetch_assoc();
    }
}

$plans_list = $koneksi->query('SELECT * FROM floor_plans ORDER BY gedung, lantai');

$gedung_options = [];
$lantai_options = [];
$rooms_meta = $koneksi->query("SELECT DISTINCT gedung, lantai FROM rooms WHERE gedung IS NOT NULL AND gedung != '' ORDER BY gedung, lantai");
if ($rooms_meta) {
    while ($m = $rooms_meta->fetch_assoc()) {
        if (!empty($m['gedung'])) {
            $gedung_options[$m['gedung']] = true;
        }
        if (!empty($m['lantai'])) {
            $lantai_options[$m['lantai']] = true;
        }
    }
}
$gedung_options = array_keys($gedung_options);
$lantai_options = array_keys($lantai_options);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Lantai - E-Recepsionis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <?php include 'include_staff_call_head.php'; ?>
    <style>
        .fp-preview img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-10 content-area">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="mb-0"><i class="bi bi-map"></i> Denah Lantai</h2>
                    <?php if ($edit_plan): ?>
                        <a href="floor_plans.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Daftar Denah</a>
                    <?php else: ?>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFloorPlanModal">
                            <i class="bi bi-plus-circle"></i> Tambah Denah
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php
                        $s = $_GET['success'];
                        if ($s === 'added') echo 'Denah berhasil diunggah.';
                        elseif ($s === 'updated') echo 'Denah berhasil diperbarui.';
                        elseif ($s === 'deleted') echo 'Denah berhasil dihapus.';
                        else echo 'Berhasil disimpan.';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php
                        $e = $_GET['error'];
                        if ($e === 'duplicate') echo 'Denah untuk gedung/lantai ini sudah ada.';
                        elseif ($e === 'no_image') echo 'Gambar denah wajib diunggah.';
                        elseif ($e === 'invalid_image') echo 'Format gambar tidak didukung (JPG, PNG, WebP, GIF).';
                        elseif ($e === 'file_too_large') echo 'Ukuran file maksimal 5 MB.';
                        else echo 'Terjadi kesalahan. Coba lagi.';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($edit_plan): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #0d9488, #0369a1);">
                            <i class="bi bi-pencil-square"></i>
                            Edit Denah: <?= htmlspecialchars($edit_plan['gedung']) ?> — Lantai <?= htmlspecialchars($edit_plan['lantai']) ?>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data" class="row g-3 mb-4">
                                <input type="hidden" name="update_denah" value="1">
                                <input type="hidden" name="id" value="<?= (int) $edit_plan['id'] ?>">
                                <div class="col-md-4">
                                    <label class="form-label">Gedung</label>
                                    <input type="text" name="gedung" class="form-control" value="<?= htmlspecialchars($edit_plan['gedung']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Lantai</label>
                                    <input type="text" name="lantai" class="form-control" value="<?= htmlspecialchars($edit_plan['lantai']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ganti gambar (opsional)</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Simpan</button>
                                </div>
                            </form>
                            <p class="text-muted small">Pratinjau denah yang ditampilkan ke pengunjung:</p>
                            <div class="fp-preview">
                                <img src="<?= htmlspecialchars(floor_plan_image_url($edit_plan['gambar'])) ?>" alt="Denah">
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #2563eb, #0369a1);">
                            <i class="bi bi-list"></i> Daftar Denah
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Gedung</th>
                                            <th>Lantai</th>
                                            <th>Preview</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($plans_list && $plans_list->num_rows > 0): ?>
                                            <?php while ($fp = $plans_list->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($fp['gedung']) ?></td>
                                                    <td><?= htmlspecialchars($fp['lantai']) ?></td>
                                                    <td>
                                                        <img src="<?= htmlspecialchars(floor_plan_image_url($fp['gambar'])) ?>" alt="" style="height:48px;width:auto;border-radius:6px;object-fit:cover;">
                                                    </td>
                                                    <td>
                                                        <a href="?edit=<?= (int) $fp['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Edit</a>
                                                        <a href="?delete=<?= (int) $fp['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus denah ini?')"><i class="bi bi-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada denah. Klik Tambah Denah.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addFloorPlanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="tambah_denah" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-upload"></i> Tambah Denah Lantai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Unggah gambar denah per kombinasi gedung dan lantai. Pengunjung akan melihat denah ini di halaman detail ruangan.</p>
                        <div class="mb-3">
                            <label class="form-label">Gedung *</label>
                            <input type="text" name="gedung" class="form-control" list="gedungList" required placeholder="Gedung A">
                            <datalist id="gedungList">
                                <?php foreach ($gedung_options as $g): ?>
                                    <option value="<?= htmlspecialchars($g) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lantai *</label>
                            <input type="text" name="lantai" class="form-control" list="lantaiList" required placeholder="12">
                            <datalist id="lantaiList">
                                <?php foreach ($lantai_options as $l): ?>
                                    <option value="<?= htmlspecialchars($l) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar denah *</label>
                            <input type="file" name="gambar" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" required>
                            <small class="text-muted">JPG/PNG/WebP, maks. 5 MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Unggah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'include_admin_footer.php'; ?>
</body>
</html>
