<?php
require_once 'auth.php';
require_once '../staff_call_routing.php';

if (currentUserIsAdmin()) {
    header('Location: ' . adminUrl('index.php'));
    exit;
}

requireComplaintOperatorPage();

$currentUserId = (int) ($_SESSION['user_id'] ?? 0);
$currentUserRole = (string) ($_SESSION['role'] ?? '');
$userName = (string) ($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'PIC');
$notifPrefs = recepsionis_get_notification_preferences($koneksi, $currentUserId);

$assignedCategoryIds = recepsionis_get_admin_category_ids($koneksi, $currentUserId);
$isHelpdeskPic = recepsionis_user_is_helpdesk_pic($koneksi, $currentUserId);
$assignedCategories = [];
foreach (recepsionis_get_complaint_categories($koneksi, true) as $category) {
    if (in_array((int) $category['id'], $assignedCategoryIds, true)) {
        $assignedCategories[] = $category;
    }
}

$pendingCalls = [];
$pendingQuery = "SELECT sc.*, cc.nama_kategori as category_name
                 FROM staff_calls sc
                 LEFT JOIN complaint_categories cc ON sc.category_id = cc.id
                 WHERE sc.status = 'pending'
                 ORDER BY sc.created_at DESC
                 LIMIT 50";
$pendingResult = $koneksi->query($pendingQuery);
if ($pendingResult) {
    while ($row = $pendingResult->fetch_assoc()) {
        if (!recepsionis_user_can_receive_staff_call(
            $koneksi,
            $currentUserId,
            (int) ($row['category_id'] ?? 0),
            (int) ($row['assigned_user_id'] ?? 0),
            $currentUserRole
        )) {
            continue;
        }
        $pendingCalls[] = $row;
        if (count($pendingCalls) >= 5) {
            break;
        }
    }
}

$pendingHelpdeskTickets = [];
if ($isHelpdeskPic && recepsionis_table_exists($koneksi, 'helpdesk_it_tickets')) {
    $helpdeskCategoryId = recepsionis_get_helpdesk_it_category_id($koneksi);
    $ticketResult = $koneksi->query(
        "SELECT * FROM helpdesk_it_tickets WHERE status = 'pending' ORDER BY created_at DESC LIMIT 50"
    );
    if ($ticketResult) {
        while ($row = $ticketResult->fetch_assoc()) {
            $assignedUserId = isset($row['assigned_user_id']) ? (int) $row['assigned_user_id'] : null;
            if ($assignedUserId !== null && $assignedUserId <= 0) {
                $assignedUserId = null;
            }
            if (!recepsionis_user_can_receive_helpdesk_it_ticket(
                $koneksi,
                $currentUserId,
                $assignedUserId,
                recepsionis_resolve_helpdesk_it_ticket_category_id($koneksi, $row)
            )) {
                continue;
            }
            $pendingHelpdeskTickets[] = $row;
            if (count($pendingHelpdeskTickets) >= 5) {
                break;
            }
        }
    }
    unset($helpdeskCategoryId);
}

$pendingItems = [];
foreach ($pendingCalls as $call) {
    $pendingItems[] = [
        'type' => 'call',
        'created_at' => $call['created_at'],
        'data' => $call,
    ];
}
foreach ($pendingHelpdeskTickets as $ticket) {
    $pendingItems[] = [
        'type' => 'ticket',
        'created_at' => $ticket['created_at'],
        'data' => $ticket,
    ];
}
usort($pendingItems, static function ($a, $b) {
    return strtotime((string) $b['created_at']) <=> strtotime((string) $a['created_at']);
});
$pendingItems = array_slice($pendingItems, 0, 5);

$actionCounts = recepsionis_get_helpdesk_action_counts(
    $koneksi,
    $currentUserId,
    false,
    'mine',
    $currentUserRole
);

$greetingHour = (int) date('G');
if ($greetingHour < 11) {
    $greeting = 'Selamat pagi';
} elseif ($greetingHour < 15) {
    $greeting = 'Selamat siang';
} elseif ($greetingHour < 18) {
    $greeting = 'Selamat sore';
} else {
    $greeting = 'Selamat malam';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PIC - E-Recepsionis System</title>
    <script>
        window.originalPageTitle = 'Dashboard PIC - E-Recepsionis System';
    </script>
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

            <div class="col-md-10 content-area pic-dash">
                <div class="pic-dash-hero">
                    <div class="pic-dash-hero-text">
                        <p class="pic-dash-greeting"><?= htmlspecialchars($greeting) ?>,</p>
                        <h1 class="pic-dash-title">
                            <?= htmlspecialchars($userName) ?>
                            <?php if ($actionCounts['total'] > 0): ?>
                                <span class="pic-dash-hero-badge helpdesk-action-badge" data-helpdesk-badge="total"><?= htmlspecialchars(recepsionis_format_action_count($actionCounts['total'])) ?> perlu ditanggapi</span>
                            <?php endif; ?>
                        </h1>
                        <p class="pic-dash-lead">
                            Satu antrian Helpdesk untuk panggilan tamu dan tiket QR kelas. Notifikasi muncul otomatis sesuai sumber panggilan.
                        </p>
                        <?php if (!empty($assignedCategories)): ?>
                            <div class="pic-dash-categories">
                                <span class="pic-dash-categories-label">Kategori Anda</span>
                                <?php foreach ($assignedCategories as $category): ?>
                                    <span class="pic-dash-category">
                                        <i class="bi <?= htmlspecialchars($category['icon'] ?: 'bi-tag') ?>"></i>
                                        <?= htmlspecialchars($category['nama_kategori']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="pic-dash-hero-icon" aria-hidden="true">
                        <i class="bi bi-headset"></i>
                    </div>
                </div>

                <?php if (!$notifPrefs['notifications_enabled'] || !$notifPrefs['sound_enabled']): ?>
                    <div class="alert alert-warning pic-dash-alert">
                        <i class="bi bi-bell-slash"></i>
                        Beberapa preferensi notifikasi Anda nonaktif.
                        <a href="<?= htmlspecialchars(adminUrl('settings.php#pref-notifikasi')) ?>" class="alert-link">Atur di sini</a>
                        agar tidak melewatkan panggilan.
                    </div>
                <?php endif; ?>

                <div class="pic-dash-section-label">Menu cepat</div>
                <div class="row g-3 pic-dash-actions mb-4">
                    <div class="col-md-4">
                        <a href="<?= htmlspecialchars(adminUrl('staff_calls.php?status=pending')) ?>" class="pic-dash-action-card" data-helpdesk-nav="dashboard-card">
                            <span class="pic-dash-action-icon pic-dash-action-icon--call">
                                <i class="bi bi-headset"></i>
                                <?php if ($actionCounts['total'] > 0): ?>
                                    <span class="pic-dash-action-badge helpdesk-action-badge" data-helpdesk-badge="total"><?= htmlspecialchars(recepsionis_format_action_count($actionCounts['total'])) ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="pic-dash-action-body">
                                <strong>Helpdesk</strong>
                                <span>Panggilan tamu & tiket QR kelas</span>
                            </span>
                            <i class="bi bi-chevron-right pic-dash-action-arrow"></i>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= htmlspecialchars(adminUrl('settings.php#pref-notifikasi')) ?>" class="pic-dash-action-card">
                            <span class="pic-dash-action-icon pic-dash-action-icon--prefs">
                                <i class="bi bi-bell-fill"></i>
                            </span>
                            <span class="pic-dash-action-body">
                                <strong>Preferensi Notifikasi</strong>
                                <span>Atur suara dan popup panggilan</span>
                            </span>
                            <i class="bi bi-chevron-right pic-dash-action-arrow"></i>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= htmlspecialchars(adminUrl('live_chat.php')) ?>" class="pic-dash-action-card">
                            <span class="pic-dash-action-icon pic-dash-action-icon--chat">
                                <i class="bi bi-chat-dots-fill"></i>
                            </span>
                            <span class="pic-dash-action-body">
                                <strong>Helpdesk IT Live Chat</strong>
                                <span>Balas percakapan tamu secara langsung</span>
                            </span>
                            <i class="bi bi-chevron-right pic-dash-action-arrow"></i>
                        </a>
                    </div>
                </div>

                <div class="card pic-dash-card mb-4">
                    <div class="card-header pic-dash-card-header">
                        <div>
                            <h5 class="mb-0">
                                <i class="bi bi-hourglass-split"></i> Helpdesk menunggu
                                <?php if ($actionCounts['total'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill ms-1 helpdesk-action-badge" data-helpdesk-badge="total"><?= htmlspecialchars(recepsionis_format_action_count($actionCounts['total'])) ?></span>
                                <?php endif; ?>
                            </h5>
                            <small class="text-muted">Panggilan staff & tiket QR — sumber berbeda, antrian sama</small>
                        </div>
                        <a href="<?= htmlspecialchars(adminUrl('staff_calls.php?status=pending')) ?>" class="btn btn-sm btn-outline-primary">
                            Lihat semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pendingItems)): ?>
                            <div class="pic-dash-empty">
                                <i class="bi bi-check2-circle"></i>
                                <p>Tidak ada antrian pending saat ini.</p>
                                <span>Anda siap menerima panggilan baru.</span>
                            </div>
                        <?php else: ?>
                            <ul class="pic-dash-pending-list">
                                <?php foreach ($pendingItems as $item): ?>
                                    <?php if ($item['type'] === 'ticket'): ?>
                                        <?php
                                        $ticket = $item['data'];
                                        $ticketName = trim((string) ($ticket['nama'] ?? 'Pelapor'));
                                        $initials = strtoupper(mb_substr($ticketName, 0, 1, 'UTF-8'));
                                        ?>
                                        <li class="pic-dash-pending-item">
                                            <div class="pic-dash-pending-main">
                                                <div class="pic-dash-pending-avatar pic-dash-pending-avatar--helpdesk"><?= htmlspecialchars($initials) ?></div>
                                                <div class="pic-dash-pending-info">
                                                    <strong><?= htmlspecialchars($ticketName) ?></strong>
                                                    <span><span class="badge bg-primary me-1">Tiket QR</span> Kelas: <?= htmlspecialchars((string) ($ticket['kelas'] ?? '-')) ?></span>
                                                    <?php if (!empty($ticket['kendala'])): ?>
                                                        <em><?= htmlspecialchars((string) $ticket['kendala']) ?></em>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="pic-dash-pending-meta">
                                                <time><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></time>
                                                <a href="<?= htmlspecialchars(adminUrl('staff_calls.php?channel=tickets&status=pending')) ?>" class="btn btn-sm btn-primary">Buka</a>
                                            </div>
                                        </li>
                                    <?php else: ?>
                                        <?php
                                        $call = $item['data'];
                                        $visitorName = trim((string) ($call['visitor_name'] ?? ''));
                                        if ($visitorName === '') {
                                            $visitorName = 'Tamu';
                                        }
                                        $initials = strtoupper(mb_substr($visitorName, 0, 1, 'UTF-8'));
                                        if (preg_match('/\s+(\S)/u', $visitorName, $m)) {
                                            $initials .= strtoupper($m[1]);
                                        }
                                        ?>
                                        <li class="pic-dash-pending-item">
                                            <div class="pic-dash-pending-main">
                                                <div class="pic-dash-pending-avatar"><?= htmlspecialchars($initials) ?></div>
                                                <div class="pic-dash-pending-info">
                                                    <strong><?= htmlspecialchars($visitorName) ?></strong>
                                                    <span>
                                                        <span class="badge bg-secondary me-1">Panggilan</span>
                                                        <?= htmlspecialchars((string) ($call['category_name'] ?? 'Umum')) ?>
                                                    </span>
                                                    <?php if (!empty($call['message'])): ?>
                                                        <em><?= htmlspecialchars((string) $call['message']) ?></em>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="pic-dash-pending-meta">
                                                <time><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></time>
                                                <a href="<?= htmlspecialchars(adminUrl('staff_calls.php?channel=calls&status=pending')) ?>" class="btn btn-sm btn-success">Buka</a>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/notification-badge.js"></script>
    <?php include 'include_staff_call_footer.php'; ?>
</body>
</html>
