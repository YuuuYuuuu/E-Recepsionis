<?php
require_once 'auth.php';
require_once '../staff_call_routing.php';

checkRole('admin');

$currentUserId = (int) ($_SESSION['user_id'] ?? 0);

function usersRedirect(array $params = []): void
{
    $query = http_build_query($params);
    $target = function_exists('adminUrl') ? adminUrl('users.php') : 'users.php';
    header('Location: ' . $target . ($query !== '' ? '?' . $query : ''));
    exit;
}

function normalizeUserRole(string $role): string
{
    return in_array($role, ['admin', 'operator'], true) ? $role : 'operator';
}

function normalizeUserWhatsApp(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }
    $norm = recepsionis_normalize_phone_for_provider($raw);
    return $norm === false ? '' : (string) $norm;
}

function selectedCategoryIdsFromRequest(): array
{
    $raw = $_POST['category_ids'] ?? [];
    if (!is_array($raw)) {
        return [];
    }

    return array_values(array_unique(array_filter(array_map('intval', $raw), static function ($id) {
        return $id > 0;
    })));
}

function usernameExists(mysqli $koneksi, string $username, int $excludeId = 0): bool
{
    $sql = 'SELECT id FROM users WHERE username = ?';
    if ($excludeId > 0) {
        $sql .= ' AND id <> ?';
    }
    $sql .= ' LIMIT 1';

    $stmt = $koneksi->prepare($sql);
    if ($excludeId > 0) {
        $stmt->bind_param('si', $username, $excludeId);
    } else {
        $stmt->bind_param('s', $username);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $exists = (bool) ($res && $res->num_rows > 0);
    $stmt->close();

    return $exists;
}

if (isset($_POST['tambah_user'])) {
    $username = trim((string) ($_POST['username'] ?? ''));
    $namaLengkap = trim((string) ($_POST['nama_lengkap'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $role = normalizeUserRole((string) ($_POST['role'] ?? 'operator'));
    $password = (string) ($_POST['password'] ?? '');
    $statusAktif = isset($_POST['status_aktif']) ? 1 : 0;
    $noWa = normalizeUserWhatsApp((string) ($_POST['no_wa'] ?? ''));
    $categoryIds = selectedCategoryIdsFromRequest();

    if ($username === '' || $namaLengkap === '' || $password === '') {
        usersRedirect(['error' => 'required']);
    }
    if (strlen($password) < 6) {
        usersRedirect(['error' => 'password_short']);
    }
    if (usernameExists($koneksi, $username)) {
        usersRedirect(['error' => 'duplicate_username']);
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $koneksi->prepare(
        'INSERT INTO users (username, password, nama_lengkap, email, no_wa, role, status_aktif)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('ssssssi', $username, $passwordHash, $namaLengkap, $email, $noWa, $role, $statusAktif);
    $ok = $stmt->execute();
    $userId = (int) $stmt->insert_id;
    $stmt->close();

    if (!$ok || $userId <= 0) {
        usersRedirect(['error' => 'save_failed']);
    }

    recepsionis_save_user_category_ids($koneksi, $userId, $categoryIds);
    usersRedirect(['success' => 'added']);
}

if (isset($_POST['edit_user'])) {
    $userId = (int) ($_POST['id'] ?? 0);
    $username = trim((string) ($_POST['username'] ?? ''));
    $namaLengkap = trim((string) ($_POST['nama_lengkap'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $role = normalizeUserRole((string) ($_POST['role'] ?? 'operator'));
    $password = (string) ($_POST['password'] ?? '');
    $statusAktif = isset($_POST['status_aktif']) ? 1 : 0;
    $noWa = normalizeUserWhatsApp((string) ($_POST['no_wa'] ?? ''));
    $categoryIds = selectedCategoryIdsFromRequest();

    if ($userId <= 0 || $username === '' || $namaLengkap === '') {
        usersRedirect(['error' => 'required']);
    }
    if ($currentUserId === $userId && $statusAktif !== 1) {
        usersRedirect(['error' => 'self_deactivate']);
    }
    if ($currentUserId === $userId && $role !== 'admin') {
        usersRedirect(['error' => 'self_downgrade']);
    }
    if ($password !== '' && strlen($password) < 6) {
        usersRedirect(['error' => 'password_short']);
    }
    if (usernameExists($koneksi, $username, $userId)) {
        usersRedirect(['error' => 'duplicate_username']);
    }

    if ($password !== '') {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare(
            'UPDATE users
             SET username = ?, password = ?, nama_lengkap = ?, email = ?, no_wa = ?, role = ?, status_aktif = ?
             WHERE id = ?'
        );
        $stmt->bind_param('ssssssii', $username, $passwordHash, $namaLengkap, $email, $noWa, $role, $statusAktif, $userId);
    } else {
        $stmt = $koneksi->prepare(
            'UPDATE users
             SET username = ?, nama_lengkap = ?, email = ?, no_wa = ?, role = ?, status_aktif = ?
             WHERE id = ?'
        );
        $stmt->bind_param('sssssii', $username, $namaLengkap, $email, $noWa, $role, $statusAktif, $userId);
    }

    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        usersRedirect(['error' => 'save_failed']);
    }

    recepsionis_save_user_category_ids($koneksi, $userId, $categoryIds);
    usersRedirect(['success' => 'updated']);
}

if (isset($_GET['toggle'])) {
    $userId = (int) ($_GET['toggle'] ?? 0);
    $status = (int) ($_GET['status'] ?? 0);

    if ($userId <= 0 || !in_array($status, [0, 1], true)) {
        usersRedirect(['error' => 'invalid']);
    }
    if ($currentUserId === $userId && $status !== 1) {
        usersRedirect(['error' => 'self_deactivate']);
    }

    $stmt = $koneksi->prepare('UPDATE users SET status_aktif = ? WHERE id = ?');
    $stmt->bind_param('ii', $status, $userId);
    $stmt->execute();
    $stmt->close();

    usersRedirect(['success' => 'status_updated']);
}

$roleFilter = isset($_GET['role']) && in_array((string) $_GET['role'], ['admin', 'operator'], true)
    ? (string) $_GET['role']
    : '';
$statusFilter = isset($_GET['status']) && in_array((string) $_GET['status'], ['0', '1'], true)
    ? (string) $_GET['status']
    : '';

$usersSql = "SELECT u.*
             FROM users u
             WHERE 1 = 1";
$usersTypes = '';
$usersParams = [];

if ($roleFilter !== '') {
    $usersSql .= ' AND u.role = ?';
    $usersTypes .= 's';
    $usersParams[] = $roleFilter;
}
if ($statusFilter !== '') {
    $usersSql .= ' AND u.status_aktif = ?';
    $usersTypes .= 'i';
    $usersParams[] = (int) $statusFilter;
}

$usersSql .= " ORDER BY FIELD(u.role, 'admin', 'operator') ASC,
                      COALESCE(NULLIF(u.nama_lengkap, ''), u.username) ASC,
                      u.id ASC";

$stmtUsers = $koneksi->prepare($usersSql);
if ($usersTypes === 's') {
    $stmtUsers->bind_param('s', $usersParams[0]);
} elseif ($usersTypes === 'i') {
    $stmtUsers->bind_param('i', $usersParams[0]);
} elseif ($usersTypes === 'si') {
    $stmtUsers->bind_param('si', $usersParams[0], $usersParams[1]);
}
$stmtUsers->execute();
$usersResult = $stmtUsers->get_result();

$users = [];
$userIds = [];
while ($row = $usersResult->fetch_assoc()) {
    $users[] = $row;
    $userIds[] = (int) $row['id'];
}
$stmtUsers->close();

$categories = recepsionis_get_complaint_categories($koneksi, false);
$userCategoryIndex = recepsionis_get_user_category_index($koneksi, $userIds);
$summaryCounts = [
    'total' => count($users),
    'active' => count(array_filter($users, static function ($user) {
        return (int) ($user['status_aktif'] ?? 0) === 1;
    })),
    'admin' => count(array_filter($users, static function ($user) {
        return (string) ($user['role'] ?? '') === 'admin';
    })),
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - E-Recepsionis System</title>
    <script>
        window.originalPageTitle = 'Kelola User - E-Recepsionis System';
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <?php include 'include_staff_call_head.php'; ?>
    <style>
        .category-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.8rem;
            font-weight: 600;
            margin: 0 6px 6px 0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <div class="col-md-10 content-area">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="bi bi-person-gear"></i> Kelola User</h2>
                        <p class="text-muted mb-0">
                            Buat user admin/operator, aktifkan kategori yang ditangani, lalu gunakan halaman Panggilan Staff untuk assign ulang kasus tertentu bila diperlukan.
                        </p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-person-plus"></i> Tambah User
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i>
                        <?php
                        if ($_GET['success'] === 'added') echo 'User berhasil ditambahkan.';
                        elseif ($_GET['success'] === 'updated') echo 'User berhasil diperbarui.';
                        elseif ($_GET['success'] === 'status_updated') echo 'Status user berhasil diperbarui.';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?php
                        if ($_GET['error'] === 'required') echo 'Mohon lengkapi field yang wajib diisi.';
                        elseif ($_GET['error'] === 'duplicate_username') echo 'Username sudah digunakan user lain.';
                        elseif ($_GET['error'] === 'password_short') echo 'Password minimal 6 karakter.';
                        elseif ($_GET['error'] === 'self_deactivate') echo 'Akun Anda sendiri tidak boleh dinonaktifkan.';
                        elseif ($_GET['error'] === 'self_downgrade') echo 'Akun Anda sendiri harus tetap ber-role admin.';
                        elseif ($_GET['error'] === 'save_failed') echo 'Data user gagal disimpan.';
                        else echo 'Permintaan tidak dapat diproses.';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted small">Total user</div>
                                <div class="display-6 fw-bold"><?= $summaryCounts['total'] ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted small">User aktif</div>
                                <div class="display-6 fw-bold text-success"><?= $summaryCounts['active'] ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted small">Super Admin</div>
                                <div class="display-6 fw-bold text-primary"><?= $summaryCounts['admin'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <form class="row g-3 align-items-end" method="GET">
                            <div class="col-md-4">
                                <label class="form-label">Filter role</label>
                                <select name="role" class="form-select">
                                    <option value="">Semua role</option>
                                    <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="operator" <?= $roleFilter === 'operator' ? 'selected' : '' ?>>Operator</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Filter status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua status</option>
                                    <option value="1" <?= $statusFilter === '1' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= $statusFilter === '0' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel"></i> Terapkan
                                </button>
                                <a href="<?= htmlspecialchars(function_exists('adminUrl') ? adminUrl('users.php') : 'users.php') ?>" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-people"></i> Daftar User
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Kontak</th>
                                        <th>Role</th>
                                        <th>Kategori Tanggung Jawab</th>
                                        <th>Status</th>
                                        <th>Login Terakhir</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <?php
                                            $userId = (int) $user['id'];
                                            $selectedCategories = $userCategoryIndex[$userId]['ids'] ?? [];
                                            $categoryNames = $userCategoryIndex[$userId]['names'] ?? [];
                                            $displayName = trim((string) ($user['nama_lengkap'] ?? '')) !== ''
                                                ? (string) $user['nama_lengkap']
                                                : (string) $user['username'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($displayName) ?></strong>
                                                    <?php if ($currentUserId === $userId): ?>
                                                        <span class="badge bg-info ms-1">Anda</span>
                                                    <?php endif; ?>
                                                    <br><small class="text-muted">@<?= htmlspecialchars($user['username']) ?></small>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($user['email'] ?: '-') ?>
                                                    <?php if (!empty($user['no_wa'])): ?>
                                                        <br><small class="text-muted"><i class="bi bi-whatsapp"></i> <?= htmlspecialchars($user['no_wa']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                                                        <?= htmlspecialchars(function_exists('currentUserRoleLabel') ? currentUserRoleLabel((string) $user['role']) : ucfirst((string) $user['role'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($categoryNames)): ?>
                                                        <?php foreach ($categoryNames as $categoryName): ?>
                                                            <span class="category-pill"><?= htmlspecialchars($categoryName) ?></span>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Belum ada kategori</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ((int) $user['status_aktif'] === 1): ?>
                                                        <span class="badge bg-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['last_login'])): ?>
                                                        <?= date('d/m/Y H:i', strtotime((string) $user['last_login'])) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Belum pernah login</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?toggle=<?= $userId ?>&status=<?= (int) $user['status_aktif'] === 1 ? 0 : 1 ?>"
                                                           class="btn btn-<?= (int) $user['status_aktif'] === 1 ? 'warning' : 'success' ?>"
                                                           onclick="return confirm('<?= (int) $user['status_aktif'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?> user ini?')">
                                                            <i class="bi bi-<?= (int) $user['status_aktif'] === 1 ? 'pause-circle' : 'play-circle' ?>"></i>
                                                        </a>
                                                        <button class="btn btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editModal<?= $userId ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                                                Belum ada user yang sesuai dengan filter.
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

    <?php foreach ($users as $user): ?>
        <?php
        $userId = (int) $user['id'];
        $selectedCategories = $userCategoryIndex[$userId]['ids'] ?? [];
        ?>
        <div class="modal fade" id="editModal<?= $userId ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-person-lines-fill"></i> Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $userId ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username *</label>
                                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <input type="tel" name="no_wa" class="form-control" value="<?= htmlspecialchars($user['no_wa'] ?? '') ?>" placeholder="08xxxxxxxxxx atau 628xxxxxxxxxx">
                                    <small class="text-muted">Dipakai untuk notifikasi WA Panggil Staff sesuai kategori user.</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select">
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Super Admin</option>
                                        <option value="operator" <?= $user['role'] === 'operator' ? 'selected' : '' ?>>Operator</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" minlength="6" placeholder="Kosongkan jika tidak ingin mengubah password">
                                <small class="text-muted">Minimal 6 karakter jika diisi.</small>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="status_aktif" id="status_aktif_<?= $userId ?>" <?= (int) $user['status_aktif'] === 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_aktif_<?= $userId ?>">User aktif</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori yang Ditangani</label>
                                <div class="border rounded p-3 bg-light" style="max-height: 260px; overflow-y: auto;">
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= (int) $category['id'] ?>" id="user_<?= $userId ?>_cat_<?= (int) $category['id'] ?>" <?= in_array((int) $category['id'], $selectedCategories, true) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="user_<?= $userId ?>_cat_<?= (int) $category['id'] ?>">
                                                    <?= htmlspecialchars($category['nama_kategori']) ?>
                                                    <?php if ((int) ($category['status_aktif'] ?? 0) !== 1): ?>
                                                        <small class="text-muted">(nonaktif)</small>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">Belum ada kategori. Tambahkan kategori dulu.</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">Kategori ini menentukan routing otomatis pengaduan baru untuk user tersebut.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="edit_user" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor WhatsApp</label>
                                <input type="tel" name="no_wa" class="form-control" placeholder="08xxxxxxxxxx atau 628xxxxxxxxxx">
                                <small class="text-muted">Dipakai untuk notifikasi WA Panggil Staff sesuai kategori user.</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select">
                                    <option value="admin">Super Admin</option>
                                    <option value="operator" selected>Operator</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                            <small class="text-muted">Minimal 6 karakter.</small>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="status_aktif" id="status_aktif_new" checked>
                            <label class="form-check-label" for="status_aktif_new">User aktif</label>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Kategori yang Ditangani</label>
                            <div class="border rounded p-3 bg-light" style="max-height: 260px; overflow-y: auto;">
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= (int) $category['id'] ?>" id="new_cat_<?= (int) $category['id'] ?>">
                                            <label class="form-check-label" for="new_cat_<?= (int) $category['id'] ?>">
                                                <?= htmlspecialchars($category['nama_kategori']) ?>
                                                <?php if ((int) ($category['status_aktif'] ?? 0) !== 1): ?>
                                                    <small class="text-muted">(nonaktif)</small>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted small">Belum ada kategori. Tambahkan kategori dulu.</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">Pilih kategori agar pengaduan baru otomatis diarahkan ke user ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_user" class="btn btn-primary">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/notification-badge.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        });
    </script>
    <?php include 'include_staff_call_footer.php'; ?>
</body>
</html>
