<?php
require_once '../config.php';

// Get queue data
$host_id = intval($_GET['host_id'] ?? 0);
$queue = $koneksi->query("SELECT q.*, v.nama as visitor_nama, v.badge_number, h.nama as host_nama 
                          FROM queue q 
                          JOIN visitors v ON q.visitor_id = v.id 
                          JOIN hosts h ON q.host_id = h.id 
                          WHERE q.status IN ('waiting', 'in-progress')" . 
                          ($host_id > 0 ? " AND q.host_id = $host_id" : "") . 
                          " ORDER BY q.waktu_masuk ASC");

// Get hosts for filter
$hosts = $koneksi->query("SELECT * FROM hosts WHERE status_aktif = 1 ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampilan Antrian - E-Recepsionis System</title>
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
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .queue-header {
            background: var(--visitor-surface);
            color: var(--visitor-text);
            padding: 24px;
            text-align: center;
            border-radius: var(--visitor-radius-md);
            margin-bottom: 30px;
            border: 1px solid var(--visitor-border);
            box-shadow: var(--visitor-shadow-soft);
        }
        .queue-header h1 {
            font-weight: 800;
            margin: 0;
        }
        .queue-item {
            background: var(--visitor-surface);
            border-radius: var(--visitor-radius-md);
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: var(--visitor-shadow-soft);
            transition: transform 0.3s;
            border: 1px solid var(--visitor-border);
        }
        .queue-item:hover {
            transform: translateY(-2px);
        }
        .queue-item.waiting {
            border-left: 5px solid #f59e0b;
        }
        .queue-item.in-progress {
            border-left: 5px solid var(--visitor-accent);
            background: linear-gradient(90deg, #ecf4ff 0%, #ffffff 12%);
        }
        .queue-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--visitor-primary);
        }
        .current-call {
            background: #0f172a;
            color: #ffffff;
            padding: 30px;
            border-radius: var(--visitor-radius-md);
            text-align: center;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.22);
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .current-call .queue-number {
            font-size: 4rem;
            color: white;
        }
        .auto-refresh {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255,255,255,0.96);
            padding: 10px 16px;
            border-radius: 999px;
            box-shadow: var(--visitor-shadow-soft);
            border: 1px solid var(--visitor-border);
            font-size: 0.82rem;
            color: var(--visitor-text-muted);
        }
        @media (max-width: 768px) {
            .queue-header {
                padding: 18px;
                margin-bottom: 20px;
            }
            .queue-number {
                font-size: 2rem;
            }
            .current-call .queue-number {
                font-size: 3rem;
            }
            .auto-refresh {
                left: 12px;
                right: 12px;
                text-align: center;
            }
        }
    </style>
</head>
<body class="visitor-page visitor-unified-shell">
    <div class="visitor-shell-content">
    <div class="container-fluid py-4">
        <div class="queue-header">
            <h1><i class="bi bi-list-ol"></i> ANTRIAN</h1>
            <p class="mb-0"><?= date('d F Y, H:i') ?></p>
        </div>

        <?php
        // Get current call (in-progress)
        $current_call = $koneksi->query("SELECT q.*, v.nama as visitor_nama, h.nama as host_nama 
                                          FROM queue q 
                                          JOIN visitors v ON q.visitor_id = v.id 
                                          JOIN hosts h ON q.host_id = h.id 
                                          WHERE q.status = 'in-progress' 
                                          ORDER BY q.waktu_dipanggil DESC 
                                          LIMIT 1")->fetch_assoc();
        ?>

        <?php if ($current_call): ?>
            <div class="current-call">
                <h3>SEDANG DIPANGGIL</h3>
                <div class="queue-number"><?= htmlspecialchars($current_call['nomor_antrian']) ?></div>
                <p class="mb-0"><?= htmlspecialchars($current_call['visitor_nama']) ?> - <?= htmlspecialchars($current_call['host_nama']) ?></p>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <h3 class="mb-3">Daftar Antrian</h3>
                
                <div id="queueList">
                    <?php if ($queue->num_rows > 0): ?>
                        <?php 
                        $queue->data_seek(0);
                        while ($q = $queue->fetch_assoc()): 
                        ?>
                            <div class="queue-item <?= $q['status'] ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <div class="queue-number"><?= htmlspecialchars($q['nomor_antrian']) ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-1"><?= htmlspecialchars($q['visitor_nama']) ?></h5>
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-person-badge"></i> <?= htmlspecialchars($q['host_nama']) ?><br>
                                            <small>Masuk: <?= date('H:i', strtotime($q['waktu_masuk'])) ?></small>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge-status <?= $q['status'] ?>">
                                            <?= ucfirst(str_replace('-', ' ', $q['status'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                            Tidak ada antrian
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="auto-refresh">
            <i class="bi bi-arrow-clockwise"></i> Auto-refresh setiap 5 detik
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh queue every 5 seconds
        setInterval(function() {
            fetch('../api/get_queue.php?status=active')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateQueueDisplay(data.data);
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 5000);

        function updateQueueDisplay(queueData) {
            const queueList = document.getElementById('queueList');
            let html = '';

            // Find current call
            const currentCall = queueData.find(q => q.status === 'in-progress');
            if (currentCall) {
                const currentCallDiv = document.querySelector('.current-call');
                if (currentCallDiv) {
                    currentCallDiv.innerHTML = `
                        <h3>SEDANG DIPANGGIL</h3>
                        <div class="queue-number">${currentCall.nomor_antrian}</div>
                        <p class="mb-0">${currentCall.visitor_nama} - ${currentCall.host_nama}</p>
                    `;
                }
            }

            // Filter waiting queue
            const waitingQueue = queueData.filter(q => q.status === 'waiting');
            
            if (waitingQueue.length > 0) {
                waitingQueue.forEach(q => {
                    html += `
                        <div class="queue-item ${q.status}">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <div class="queue-number">${q.nomor_antrian}</div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-1">${q.visitor_nama}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-person-badge"></i> ${q.host_nama}<br>
                                        <small>Masuk: ${new Date(q.waktu_masuk).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</small>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge-status ${q.status}">
                                        ${q.status.replace('-', ' ')}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = `
                    <div class="alert alert-info text-center">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                        Tidak ada antrian
                    </div>
                `;
            }

            queueList.innerHTML = html;
        }
    </script>
    <?php require __DIR__ . '/_visitor_react_chrome.php'; ?>
    <script src="../assets/js/idle-redirect.js"></script>
</body>
</html>
