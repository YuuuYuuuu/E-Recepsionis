<?php
require_once '../config.php';

// Get active hosts
$hosts = $koneksi->query("SELECT * FROM hosts WHERE status_aktif = 1 ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In - E-Recepsionis System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="assets/landing/assets/visitor-landing.css" rel="stylesheet">
    <link href="../assets/css/visitor-unified.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .checkin-container {
            max-width: 700px;
            width: 100%;
        }
        .checkin-card {
            background: var(--visitor-surface);
            border: 1px solid var(--visitor-border);
            border-radius: var(--visitor-radius-lg);
            padding: 40px;
            box-shadow: var(--visitor-shadow);
        }
        .checkin-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .checkin-header i {
            font-size: 3.2rem;
            color: var(--visitor-primary);
            margin-bottom: 10px;
        }
        .checkin-header h1 {
            font-weight: 800;
            color: var(--visitor-text);
            margin: 0;
        }
        .form-label {
            font-weight: 600;
            color: var(--visitor-text);
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: var(--visitor-radius-sm);
            border: 1px solid var(--visitor-border);
            padding: 12px 16px;
        }
        .form-control:focus, .form-select:focus {
            border-color: rgba(11, 59, 140, 0.45);
            box-shadow: 0 0 0 4px rgba(11, 59, 140, 0.10);
        }
        .btn-checkin {
            background: var(--visitor-primary);
            border: 1px solid var(--visitor-primary);
            color: white;
            font-weight: 700;
            padding: 14px;
            border-radius: var(--visitor-radius-sm);
            width: 100%;
            font-size: 1.1rem;
            margin-top: 20px;
            transition: all 0.25s ease;
        }
        .btn-checkin:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(11, 59, 140, 0.22);
            background: #0a3378;
            border-color: #0a3378;
            color: white;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--visitor-primary);
            text-decoration: none;
            font-weight: 600;
        }
        .back-link a:hover {
            color: var(--visitor-accent);
        }
        @media (max-width: 768px) {
            .checkin-card {
                padding: 26px 18px;
            }
        }
    </style>
</head>
<body class="visitor-page visitor-unified-shell">
    <div class="visitor-shell-content">
    <div class="checkin-container">
        <div class="checkin-card">
            <div class="checkin-header">
                <i class="bi bi-person-plus-fill"></i>
                <h1>Check-In Tamu</h1>
                <p class="text-muted">Silakan isi form di bawah ini</p>
            </div>

            <form id="checkinForm" method="POST" action="checkin_process.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control" required autofocus>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telepon *</label>
                        <input type="text" name="no_telp" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Perusahaan/Instansi</label>
                    <input type="text" name="perusahaan" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Host *</label>
                    <select name="host_id" class="form-select" required>
                        <option value="">-- Pilih Host --</option>
                        <?php while ($host = $hosts->fetch_assoc()): ?>
                            <option value="<?= $host['id'] ?>">
                                <?= htmlspecialchars($host['nama']) ?> - <?= htmlspecialchars($host['departemen']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tujuan Kunjungan *</label>
                    <textarea name="tujuan" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Foto (Opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG, GIF, WEBP (Max 5MB)</small>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="add_to_queue" value="1" id="add_to_queue" checked>
                        <label class="form-check-label" for="add_to_queue">
                            Tambahkan ke antrian
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-checkin">
                    <i class="bi bi-check-circle"></i> Check-In
                </button>
            </form>

            <div class="back-link">
                <a href="index.php">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Menu Utama
                </a>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('checkinForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
        });
    </script>
    <?php require __DIR__ . '/_visitor_react_chrome.php'; ?>
    <script src="../assets/js/idle-redirect.js"></script>
</body>
</html>
