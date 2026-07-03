<?php
require_once 'auth.php';
requireSuperAdminPage();

// Handle checkout
if (isset($_POST['checkout'])) {
    $badge_number = esc($_POST['badge_number'] ?? '');
    $visitor_id = intval($_POST['visitor_id'] ?? 0);
    
    if (empty($badge_number) && !$visitor_id) {
        $error = "Badge number atau visitor ID diperlukan";
    } else {
        // Get visitor
        if ($visitor_id) {
            $stmt = $koneksi->prepare("SELECT * FROM visitors WHERE id = ? AND status = 'checked-in'");
            $stmt->bind_param("i", $visitor_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $visitor = $result->fetch_assoc();
            $stmt->close();
        } else {
            $stmt = $koneksi->prepare("SELECT * FROM visitors WHERE badge_number = ? AND status = 'checked-in'");
            $stmt->bind_param("s", $badge_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $visitor = $result->fetch_assoc();
            $stmt->close();
        }
        
        if ($visitor) {
            $visitor_id_val = $visitor['id'];
            $host_id_val = $visitor['host_id'] ?? null;
            $visitor_nama = $visitor['nama'];
            $status_checked_out = 'checked-out';
            $status_completed = 'completed';
            $status_waiting = 'waiting';
            $status_in_progress = 'in-progress';
            $type_checkout = 'checkout';
            $title = 'Check-Out: ' . $visitor_nama;
            $message = $visitor_nama . ' telah check-out';
            
            // Update visitor status
            $stmt = $koneksi->prepare("UPDATE visitors SET status = ?, checkout_time = NOW() WHERE id = ?");
            $stmt->bind_param("si", $status_checked_out, $visitor_id_val);
            $stmt->execute();
            $stmt->close();
            
            // Update queue if exists
            $stmt = $koneksi->prepare("UPDATE queue SET status = ?, waktu_selesai = NOW() WHERE visitor_id = ? AND status IN (?, ?)");
            $stmt->bind_param("siss", $status_completed, $visitor_id_val, $status_waiting, $status_in_progress);
            $stmt->execute();
            $stmt->close();
            
            // Create notification
            $stmt = $koneksi->prepare("INSERT INTO notifications (host_id, visitor_id, type, title, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $host_id_val, $visitor_id_val, $type_checkout, $title, $message);
            $stmt->execute();
            $stmt->close();
            
            header("Location: checkout.php?success=1&visitor=" . urlencode($visitor_nama));
            exit;
        } else {
            $error = "Tamu tidak ditemukan atau sudah check-out";
        }
    }
}

// Get checked-in visitors
$checked_in = $koneksi->query("SELECT v.*, h.nama as host_nama 
                               FROM visitors v 
                               LEFT JOIN hosts h ON v.host_id = h.id 
                               WHERE v.status = 'checked-in' 
                               ORDER BY v.checkin_time DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-Out - E-Recepsionis System</title>
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

            <div class="col-md-10 content-area">
                <h2 class="mb-4"><i class="bi bi-box-arrow-right"></i> Check-Out</h2>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> 
                        Check-out berhasil untuk: <?= htmlspecialchars($_GET['visitor']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Check-Out Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-qr-code-scan"></i> Scan Badge atau Input Manual
                    </div>
                    <div class="card-body">
                        <form method="POST" id="checkoutForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Badge Number</label>
                                    <input type="text" name="badge_number" id="badge_number" class="form-control" 
                                           placeholder="Scan atau ketik badge number" autofocus>
                                    <small class="text-muted">Gunakan scanner QR code atau input manual</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Atau Pilih dari Daftar</label>
                                    <select name="visitor_id" id="visitor_id" class="form-select">
                                        <option value="">-- Pilih Tamu --</option>
                                        <?php while ($visitor = $checked_in->fetch_assoc()): ?>
                                            <option value="<?= $visitor['id'] ?>" data-badge="<?= htmlspecialchars($visitor['badge_number'] ?? '') ?>">
                                                <?= htmlspecialchars($visitor['badge_number'] ?? '') ?> - <?= htmlspecialchars($visitor['nama'] ?? '') ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" name="checkout" class="btn btn-primary">
                                <i class="bi bi-box-arrow-right"></i> Check-Out
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Checked-In Visitors List -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Tamu yang Sedang Check-In
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Badge</th>
                                        <th>Nama</th>
                                        <th>Perusahaan</th>
                                        <th>Host</th>
                                        <th>Check-In</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $checked_in->data_seek(0); // Reset pointer
                                    if ($checked_in->num_rows > 0): ?>
                                        <?php while ($visitor = $checked_in->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($visitor['badge_number'] ?? '') ?></strong></td>
                                                <td><?= htmlspecialchars($visitor['nama'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($visitor['perusahaan'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($visitor['host_nama'] ?? '-') ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($visitor['checkin_time'])) ?></td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="visitor_id" value="<?= $visitor['id'] ?>">
                                                        <button type="submit" name="checkout" class="btn btn-warning btn-sm"
                                                                onclick="return confirm('Check-out tamu ini?')">
                                                            <i class="bi bi-box-arrow-right"></i> Check-Out
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i><br>
                                                Tidak ada tamu yang sedang check-in
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/notification-badge.js"></script>
    <?php include 'include_staff_call_footer.php'; ?>
    <script>
        // Auto-fill badge number when visitor is selected
        document.getElementById('visitor_id').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (option.value) {
                document.getElementById('badge_number').value = option.getAttribute('data-badge');
            }
        });

        // Auto-select visitor when badge number is entered
        document.getElementById('badge_number').addEventListener('input', function() {
            const badge = this.value;
            const select = document.getElementById('visitor_id');
            for (let option of select.options) {
                if (option.getAttribute('data-badge') === badge) {
                    select.value = option.value;
                    break;
                }
            }
        });
    </script>
</body>
</html>
