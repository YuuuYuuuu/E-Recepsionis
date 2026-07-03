<?php
require_once '../config.php';

$visitor_id = intval($_GET['visitor_id'] ?? 0);
$badge_number = $_GET['badge'] ?? '';
$queue_number = $_GET['queue'] ?? '';

if (!$visitor_id) {
    header("Location: index.php");
    exit;
}

$visitor = $koneksi->query("SELECT v.*, h.nama as host_nama 
                            FROM visitors v 
                            LEFT JOIN hosts h ON v.host_id = h.id 
                            WHERE v.id = $visitor_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Berhasil - E-Recepsionis System</title>
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
        .success-card {
            background: var(--visitor-surface);
            border: 1px solid var(--visitor-border);
            border-radius: var(--visitor-radius-lg);
            padding: 40px;
            box-shadow: var(--visitor-shadow);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .success-icon {
            font-size: 4.5rem;
            color: #059669;
            margin-bottom: 20px;
        }
        .badge-display {
            background: var(--visitor-surface-soft);
            color: var(--visitor-text);
            border: 1px solid var(--visitor-border);
            padding: 20px;
            border-radius: var(--visitor-radius-md);
            margin: 20px 0;
        }
        .badge-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 10px 0;
            color: var(--visitor-primary);
        }
        .queue-number {
            font-size: 3rem;
            font-weight: 800;
            color: #f59e0b;
            margin: 20px 0;
        }
        .btn-print {
            background: var(--visitor-primary);
            border: 1px solid var(--visitor-primary);
            color: #fff;
            font-weight: 600;
        }
        .btn-print:hover {
            background: #0a3378;
            border-color: #0a3378;
            color: #fff;
        }
        @media (max-width: 768px) {
            .success-card {
                padding: 28px 18px;
            }
            .badge-number {
                font-size: 2rem;
            }
            .queue-number {
                font-size: 2.4rem;
            }
        }
    </style>
</head>
<body class="visitor-page visitor-unified-shell">
    <div class="visitor-shell-content">
    <div class="success-card">
        <i class="bi bi-check-circle-fill success-icon"></i>
        <h1 class="mb-3">Check-In Berhasil!</h1>
        <p class="text-muted mb-4">Terima kasih, <?= htmlspecialchars($visitor['nama']) ?>!</p>

        <div class="badge-display">
            <small>Badge Number</small>
            <div class="badge-number"><?= htmlspecialchars($badge_number) ?></div>
            <p class="mb-0">Host: <?= htmlspecialchars($visitor['host_nama']) ?></p>
        </div>

        <?php if ($queue_number): ?>
            <div class="alert alert-warning">
                <h5>Nomor Antrian Anda</h5>
                <div class="queue-number"><?= htmlspecialchars($queue_number) ?></div>
                <p class="mb-0">Silakan tunggu hingga dipanggil</p>
            </div>
        <?php endif; ?>

        <div class="d-grid gap-2 mt-4">
            <a href="../admin/badge.php?id=<?= $visitor_id ?>" class="btn btn-print" target="_blank">
                <i class="bi bi-printer"></i> Cetak Badge
            </a>
            <a href="index.php" class="btn btn-visitor-outline">
                <i class="bi bi-arrow-left"></i> Check-In Lagi
            </a>
        </div>
    </div>
    </div>
    <?php require __DIR__ . '/_visitor_react_chrome.php'; ?>
    <script src="../assets/js/idle-redirect.js"></script>
</body>
</html>
