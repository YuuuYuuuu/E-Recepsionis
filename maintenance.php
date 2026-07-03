<?php
/**
 * Halaman pemeliharaan — file ini sengaja tidak memuat config.php
 * agar tetap bisa ditampilkan meski ada masalah koneksi DB.
 */
$msgFile = __DIR__ . '/maintenance_message.txt';
$custom = '';
if (is_file($msgFile)) {
    $custom = trim((string) file_get_contents($msgFile));
}
$bgCandidates = [
    'assets/image/itblogo.png',
    'assets/images/maintenance-bg.jpg',
    'assets/images/maintenance-bg.jpeg',
    'assets/images/maintenance-bg.png',
    'assets/images/maintenance-bg.webp',
    'assets/maintenance-bg.jpg',
    'assets/maintenance-bg.jpeg',
    'assets/maintenance-bg.png',
    'assets/maintenance-bg.webp',
    'visitor-app/src/assets/receptionist.svg',
];
$bgImageUrl = '';
foreach ($bgCandidates as $relPath) {
    $full = __DIR__ . '/' . $relPath;
    if (is_file($full)) {
        $bgImageUrl = $relPath;
        break;
    }
}
if ($bgImageUrl === '') {
    // Fallback: ambil image terbaru dari folder aset lampiran Cursor lalu embed.
    // Ini berguna saat user upload gambar lewat chat tapi belum dipindah ke web root.
    $cursorAssetsDir = rtrim((string) getenv('HOME'), '/') . '/.cursor/projects/Applications-MAMP-htdocs-Recepsionis/assets';
    if (is_dir($cursorAssetsDir)) {
        $candidates = glob($cursorAssetsDir . '/*.{png,jpg,jpeg,webp,gif,svg}', GLOB_BRACE);
        if (is_array($candidates) && count($candidates) > 0) {
            usort($candidates, static function ($a, $b) {
                return filemtime($b) <=> filemtime($a);
            });
            $picked = $candidates[0];
            $ext = strtolower((string) pathinfo($picked, PATHINFO_EXTENSION));
            $mime = 'image/png';
            if ($ext === 'jpg' || $ext === 'jpeg') {
                $mime = 'image/jpeg';
            } elseif ($ext === 'webp') {
                $mime = 'image/webp';
            } elseif ($ext === 'gif') {
                $mime = 'image/gif';
            } elseif ($ext === 'svg') {
                $mime = 'image/svg+xml';
            }
            $raw = @file_get_contents($picked);
            if ($raw !== false) {
                $bgImageUrl = 'data:' . $mime . ';base64,' . base64_encode($raw);
            }
        }
    }
}
if ($bgImageUrl === '') {
    $bgImageUrl = 'visitor-app/src/assets/receptionist.svg';
}
$lang = 'id';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang dalam pemeliharaan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --accent: #38bdf8;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -2;
            background:
                radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.35), transparent 45%),
                radial-gradient(circle at 80% 0%, rgba(56, 189, 248, 0.22), transparent 30%),
                linear-gradient(160deg, rgba(15, 23, 42, 0.86), rgba(15, 23, 42, 0.92));
            background-image:
                linear-gradient(160deg, rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.92)),
                var(--bg-image);
            background-size: cover, cover;
            background-position: center, center;
            background-repeat: no-repeat, no-repeat;
            transform: scale(1.04);
            filter: saturate(1.1) contrast(1.05);
        }
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            background: radial-gradient(ellipse 80% 60% at 50% 120%, rgba(56, 189, 248, 0.18), transparent 70%);
            backdrop-filter: blur(2px);
        }
        .wrap {
            max-width: 28rem;
            text-align: center;
            background: rgba(15, 23, 42, 0.58);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 1rem;
            padding: 1.5rem 1.25rem;
            box-shadow: 0 18px 48px rgba(2, 8, 23, 0.45);
        }
        .icon {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1.25rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.25), rgba(56, 189, 248, 0.08));
            border: 1px solid rgba(56, 189, 248, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon svg { width: 2rem; height: 2rem; color: var(--accent); }
        h1 {
            font-size: 1.375rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
            letter-spacing: -0.02em;
        }
        p {
            margin: 0;
            color: var(--muted);
            font-size: 0.9375rem;
            line-height: 1.6;
        }
        .custom {
            margin-top: 1.25rem;
            padding: 1rem 1.125rem;
            background: var(--card);
            border-radius: 0.75rem;
            border: 1px solid rgba(148, 163, 184, 0.12);
            text-align: left;
            color: var(--text);
            font-size: 0.875rem;
            line-height: 1.55;
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.8125rem;
            color: var(--muted);
        }
    </style>
</head>
<body style="--bg-image: url('<?= htmlspecialchars($bgImageUrl, ENT_QUOTES, 'UTF-8') ?>');">
    <div class="wrap">
        <div class="icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h1>Situs sedang dalam pemeliharaan</h1>
        <p>Kami sedang melakukan perbaikan singkat. Silakan kembali beberapa saat lagi.</p>
        <?php if ($custom !== ''): ?>
            <div class="custom"><?= nl2br(htmlspecialchars($custom, ENT_QUOTES, 'UTF-8')) ?></div>
        <?php endif; ?>
        <p class="footer">E-Recepsionis</p>
    </div>
</body>
</html>
