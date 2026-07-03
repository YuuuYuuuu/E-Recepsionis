<?php
require_once 'config.php';

// Get visitor ID or badge number
$visitor_id = intval($_GET['id'] ?? 0);
$badge_number = $_GET['badge'] ?? '';

if (!$visitor_id && !$badge_number) {
    die("Parameter tidak valid!");
}

// Get visitor data
if ($visitor_id) {
    $visitor = $koneksi->query("SELECT v.*, h.nama as host_nama, h.departemen as host_departemen 
                                FROM visitors v 
                                LEFT JOIN hosts h ON v.host_id = h.id 
                                WHERE v.id = $visitor_id")->fetch_assoc();
} else {
    $badge_esc = esc($badge_number);
    $visitor = $koneksi->query("SELECT v.*, h.nama as host_nama, h.departemen as host_departemen 
                                FROM visitors v 
                                LEFT JOIN hosts h ON v.host_id = h.id 
                                WHERE v.badge_number = '$badge_esc'")->fetch_assoc();
}

if (!$visitor) {
    die("Data tamu tidak ditemukan!");
}

// Simple HTML badge (for printing)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge - <?= htmlspecialchars($visitor['badge_number']) ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f0f0f0;
            padding: 20px;
        }
        .badge {
            width: 85mm;
            height: 54mm;
            background: linear-gradient(135deg, #2563eb, #0369a1);
            border-radius: 8px;
            padding: 5mm;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            position: relative;
        }
        .badge-content {
            background: white;
            width: 100%;
            height: 100%;
            border-radius: 4px;
            padding: 3mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }
        .badge-title {
            font-size: 8pt;
            color: #2563eb;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
        }
        .badge-number {
            font-size: 10pt;
            font-weight: bold;
            color: #1e293b;
            text-align: center;
            margin: 2mm 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
            letter-spacing: -0.5px;
        }
        .badge-name {
            font-size: 10pt;
            font-weight: bold;
            color: #1e293b;
            text-align: center;
            margin: 1mm 0;
            text-transform: uppercase;
        }
        .badge-company {
            font-size: 7pt;
            color: #64748b;
            text-align: center;
            margin: 1mm 0;
        }
        .badge-host {
            font-size: 7pt;
            color: #1e293b;
            text-align: center;
            margin: 1mm 0;
        }
        .badge-date {
            font-size: 6pt;
            color: #94a3b8;
            text-align: center;
            margin-top: 1mm;
        }
        .qr-placeholder {
            width: 15mm;
            height: 15mm;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6pt;
            color: #666;
            position: absolute;
            right: 3mm;
            top: 3mm;
        }
        .no-print {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div>
        <div class="badge">
            <div class="badge-content">
                <div class="badge-title">VISITOR BADGE</div>
                <div class="badge-number"><?= htmlspecialchars($visitor['badge_number']) ?></div>
                <div class="badge-name"><?= htmlspecialchars($visitor['nama']) ?></div>
                <?php if ($visitor['perusahaan']): ?>
                    <div class="badge-company"><?= htmlspecialchars($visitor['perusahaan']) ?></div>
                <?php endif; ?>
                <div class="badge-host">Host: <?= htmlspecialchars($visitor['host_nama'] ?? '-') ?></div>
                <div class="badge-date"><?= date('d/m/Y H:i', strtotime($visitor['checkin_time'])) ?></div>
                <div class="qr-placeholder">QR<br><?= substr($visitor['badge_number'], -4) ?></div>
            </div>
        </div>
        
        <div class="no-print">
            <button onclick="window.print()" class="btn">Cetak Badge</button>
            <a href="../visitor/index.php" class="btn">Check-In Lagi</a>
        </div>
    </div>
</body>
</html>
