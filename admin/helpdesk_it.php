<?php
require_once 'auth.php';
require_once '../staff_call_routing.php';

requireSuperAdminPage();

if (isset($_POST['regenerate_token'])) {
    recepsionis_regenerate_helpdesk_it_token($koneksi);
    header('Location: ' . adminUrl('helpdesk_it.php?success=token'));
    exit;
}

$access = recepsionis_get_helpdesk_it_access($koneksi);
$helpdeskCategoryId = recepsionis_get_helpdesk_category_id($koneksi);
$helpdeskCategoryName = '';
$helpdeskPicUsers = [];

if ($helpdeskCategoryId > 0) {
    foreach (recepsionis_get_complaint_categories($koneksi, true) as $category) {
        if ((int) $category['id'] === $helpdeskCategoryId) {
            $helpdeskCategoryName = (string) $category['nama_kategori'];
            break;
        }
    }
    $helpdeskPicUsers = recepsionis_get_active_category_admins($koneksi, $helpdeskCategoryId);
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin'));
$parentDir = dirname($scriptDir);
$visitorBase = ($parentDir === '/' || $parentDir === '\\' || $parentDir === '.') ? '' : $parentDir;
$publicUrl = $access
    ? $scheme . '://' . $httpHost . $visitorBase . '/visitor/helpdesk-it.php?k=' . urlencode((string) $access['public_token'])
    : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Tiket Kelas - E-Recepsionis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <?php include 'include_staff_call_head.php'; ?>
    <style>
        #helpdeskQr canvas, #helpdeskQr img { max-width: 180px; height: auto !important; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-10 content-area">
                <h2 class="mb-1"><i class="bi bi-qr-code"></i> QR Tiket Kelas</h2>
                <p class="text-muted small mb-4">
                    Form QR ini masuk ke antrian <strong>Helpdesk</strong> yang sama dengan panggilan staff kategori Helpdesk.
                    Kelola tiket di menu <a href="<?= htmlspecialchars(adminUrl('staff_calls.php')) ?>">Helpdesk</a>.
                </p>

                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="card shadow-sm h-100">
                            <div class="card-header text-white" style="background: linear-gradient(135deg, #2563eb, #0ea5e9);">
                                <i class="bi bi-qr-code"></i> Barcode Global Kelas
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Cetak QR ini dan tempel di kelas. Siswa/guru scan lalu isi form tiket.</p>
                                <?php if ($publicUrl): ?>
                                    <div class="d-flex flex-column align-items-center mb-3">
                                        <div id="helpdeskQr" class="p-2 border rounded bg-white"></div>
                                    </div>
                                    <label class="form-label small text-muted">URL publik</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-control-sm" id="helpdeskUrl" readonly value="<?= htmlspecialchars($publicUrl) ?>">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('helpdeskUrl').value)">Salin</button>
                                    </div>
                                    <form method="post" onsubmit="return confirm('Ganti barcode? Link/QR lama tidak akan berfungsi lagi.');">
                                        <input type="hidden" name="regenerate_token" value="1">
                                        <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-arrow-repeat"></i> Regenerate Barcode</button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning mb-0">Token belum tersedia. Jalankan migrasi database.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light"><i class="bi bi-info-circle"></i> PIC Helpdesk</div>
                            <div class="card-body small text-muted">
                                <ul class="mb-3">
                                    <li>Kategori Helpdesk di <strong>Panggilan Staff</strong> dan tiket dari QR ini adalah satu antrian yang sama.</li>
                                    <li>PIC ditetapkan lewat <strong>Kelola User → Kategori Tanggung Jawab → Help Desk</strong>.</li>
                                    <li>Live chat operator tetap di menu <strong>Helpdesk IT Live Chat</strong>.</li>
                                </ul>
                                <p class="mb-1"><strong>Kategori terhubung:</strong>
                                    <?= $helpdeskCategoryName !== '' ? htmlspecialchars($helpdeskCategoryName) . ' (#' . (int) $helpdeskCategoryId . ')' : 'Belum dikonfigurasi' ?>
                                </p>
                                <?php if (empty($helpdeskPicUsers)): ?>
                                    <div class="alert alert-warning py-2 mb-0">Belum ada PIC aktif untuk kategori Helpdesk.</div>
                                <?php else: ?>
                                    <p class="mb-1"><strong>PIC aktif:</strong></p>
                                    <ul class="mb-0">
                                        <?php foreach ($helpdeskPicUsers as $picUser): ?>
                                            <li><?= htmlspecialchars($picUser['nama_lengkap'] ?: $picUser['username']) ?> (<?= htmlspecialchars($picUser['role']) ?>)</li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        if (params.get('success') === 'token') {
            params.delete('success');
            window.history.replaceState({}, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
        }
        <?php if ($publicUrl): ?>
        const el = document.getElementById('helpdeskQr');
        if (el && window.QRCode) {
            el.innerHTML = '';
            new QRCode(el, {
                text: <?= json_encode($publicUrl, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                width: 180,
                height: 180,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        }
        <?php endif; ?>
    });
    </script>
    <?php include 'include_admin_footer.php'; ?>
</body>
</html>
