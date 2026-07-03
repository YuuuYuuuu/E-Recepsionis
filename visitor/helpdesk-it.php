<?php
require_once '../config.php';
require_once '../staff_call_routing.php';

$token = trim((string) ($_GET['k'] ?? ''));
$accessValid = recepsionis_validate_helpdesk_it_token($koneksi, $token);
$submitted = isset($_GET['sent']) && $_GET['sent'] === '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk IT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/visitor-unified.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 45%, #0c4a6e 100%);
            font-family: Inter, system-ui, sans-serif;
        }
        .hd-card {
            max-width: 480px;
            margin: 2rem auto;
            border: none;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
            overflow: hidden;
        }
        .hd-header {
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #fff;
            padding: 1.25rem 1.5rem;
        }
        .hd-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: rgba(255,255,255,.15);
            border-radius: 999px;
            padding: .25rem .75rem;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="visitor-page">
    <div class="container px-3">
        <div class="card hd-card">
            <div class="hd-header">
                <div class="hd-badge mb-2"><i class="bi bi-headset"></i> Helpdesk IT</div>
                <h1 class="h4 mb-0">Lapor Kendala Kelas</h1>
                <p class="mb-0 mt-1 small opacity-90">Isi formulir singkat, tim IT akan menindaklanjuti.</p>
            </div>
            <div class="card-body p-4">
                <?php if (!$accessValid): ?>
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-shield-x"></i>
                        Link tidak valid atau barcode sudah diganti admin. Minta barcode terbaru ke admin IT.
                    </div>
                <?php elseif ($submitted): ?>
                    <div class="text-center py-3">
                        <div class="display-4 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                        <h2 class="h5">Tiket terkirim</h2>
                        <p class="text-muted mb-4">Tim Helpdesk IT akan menghubungi Anda sesuai nomor yang diisi.</p>
                        <a href="helpdesk-it.php?k=<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-primary btn-sm">Kirim laporan lain</a>
                    </div>
                <?php else: ?>
                    <form id="helpdeskForm" class="vstack gap-3">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div>
                            <label class="form-label">Nama *</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap">
                        </div>
                        <div>
                            <label class="form-label">Nomor *</label>
                            <input type="tel" name="nomor" class="form-control" required placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="form-label">Kelas *</label>
                            <input type="text" name="kelas" class="form-control" required placeholder="Contoh: 12A / Lab Komputer 2">
                        </div>
                        <div>
                            <label class="form-label">Kendala *</label>
                            <textarea name="kendala" class="form-control" rows="4" required placeholder="Jelaskan kendala perangkat atau jaringan..."></textarea>
                        </div>
                        <div id="hdError" class="alert alert-danger py-2 small d-none"></div>
                        <button type="submit" class="btn btn-primary w-100 py-2" id="hdSubmitBtn">
                            <i class="bi bi-send"></i> Kirim Tiket
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if ($accessValid && !$submitted): ?>
    <script>
    document.getElementById('helpdeskForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('hdSubmitBtn');
        const err = document.getElementById('hdError');
        err.classList.add('d-none');
        btn.disabled = true;
        try {
            const res = await fetch('../api/helpdesk_it_submit.php', {
                method: 'POST',
                body: new FormData(e.target),
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = 'helpdesk-it.php?k=<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>&sent=1';
                return;
            }
            err.textContent = data.message || 'Gagal mengirim tiket.';
            err.classList.remove('d-none');
        } catch {
            err.textContent = 'Koneksi gagal. Coba lagi.';
            err.classList.remove('d-none');
        } finally {
            btn.disabled = false;
        }
    });
    </script>
    <?php endif; ?>
</body>
</html>
