<?php
require_once 'auth.php';
requireSuperAdminPage();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $thumbnail_height = intval($_POST['thumbnail_height'] ?? 180);
    $thumbnail_border_radius = intval($_POST['thumbnail_border_radius'] ?? 12);
    $thumbnail_bg_color = esc($_POST['thumbnail_bg_color'] ?? '#e2e8f0');
    $thumbnail_margin_bottom = intval($_POST['thumbnail_margin_bottom'] ?? 15);

    // Validate values
    if ($thumbnail_height < 50) $thumbnail_height = 50;
    if ($thumbnail_height > 500) $thumbnail_height = 500;
    if ($thumbnail_border_radius < 0) $thumbnail_border_radius = 0;
    if ($thumbnail_border_radius > 50) $thumbnail_border_radius = 50;
    if ($thumbnail_margin_bottom < 0) $thumbnail_margin_bottom = 0;
    if ($thumbnail_margin_bottom > 100) $thumbnail_margin_bottom = 100;

    // Update settings using INSERT ... ON DUPLICATE KEY UPDATE
    $koneksi->query("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES 
                    ('thumbnail_height', '$thumbnail_height', 'number')
                    ON DUPLICATE KEY UPDATE setting_value = '$thumbnail_height'");
    
    $koneksi->query("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES 
                    ('thumbnail_border_radius', '$thumbnail_border_radius', 'number')
                    ON DUPLICATE KEY UPDATE setting_value = '$thumbnail_border_radius'");
    
    $koneksi->query("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES 
                    ('thumbnail_bg_color', '" . $koneksi->real_escape_string($thumbnail_bg_color) . "', 'color')
                    ON DUPLICATE KEY UPDATE setting_value = '" . $koneksi->real_escape_string($thumbnail_bg_color) . "'");
    
    $koneksi->query("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES 
                    ('thumbnail_margin_bottom', '$thumbnail_margin_bottom', 'number')
                    ON DUPLICATE KEY UPDATE setting_value = '$thumbnail_margin_bottom'");

    header('Location: thumbnail_settings.php?success=updated');
    exit;
}

// Get current settings
function get_setting($key, $default) {
    global $koneksi;
    $res = $koneksi->query("SELECT setting_value FROM settings WHERE setting_key = '" . $koneksi->real_escape_string($key) . "' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return $row['setting_value'];
    }
    return $default;
}

$thumbnail_height = get_setting('thumbnail_height', '180');
$thumbnail_border_radius = get_setting('thumbnail_border_radius', '12');
$thumbnail_bg_color = get_setting('thumbnail_bg_color', '#e2e8f0');
$thumbnail_margin_bottom = get_setting('thumbnail_margin_bottom', '15');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Thumbnail - E-Recepsionis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <?php include 'include_staff_call_head.php'; ?>
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #0369a1;
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
                    <h2><i class="bi bi-image"></i> Pengaturan Thumbnail Preview</h2>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> Pengaturan thumbnail berhasil diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Settings Form -->
                    <div class="col-lg-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                                <i class="bi bi-sliders"></i> Pengaturan Visual
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-4">
                                        <label class="form-label"><strong>Tinggi Thumbnail</strong></label>
                                        <div class="d-flex gap-3 align-items-center">
                                            <input type="range" class="form-range flex-grow-1" name="thumbnail_height" id="heightRange" 
                                                   min="50" max="500" step="10" value="<?= htmlspecialchars($thumbnail_height) ?>"
                                                   onchange="updatePreview()">
                                            <div style="min-width: 80px; text-align: right;">
                                                <span id="heightValue" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($thumbnail_height) ?></span>
                                                <span style="color: #999;">px</span>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-2">Range: 50px - 500px</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><strong>Border Radius (Sudut)</strong></label>
                                        <div class="d-flex gap-3 align-items-center">
                                            <input type="range" class="form-range flex-grow-1" name="thumbnail_border_radius" id="radiusRange" 
                                                   min="0" max="50" step="2" value="<?= htmlspecialchars($thumbnail_border_radius) ?>"
                                                   onchange="updatePreview()">
                                            <div style="min-width: 80px; text-align: right;">
                                                <span id="radiusValue" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($thumbnail_border_radius) ?></span>
                                                <span style="color: #999;">px</span>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-2">0 = Tajam, 50 = Sangat Bulat</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><strong>Warna Background Placeholder</strong></label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" name="thumbnail_bg_color" id="bgColorInput"
                                                   value="<?= htmlspecialchars($thumbnail_bg_color) ?>"
                                                   onchange="updatePreview()" style="max-width: 80px;">
                                            <input type="text" class="form-control" id="bgColorText" 
                                                   value="<?= htmlspecialchars($thumbnail_bg_color) ?>" readonly>
                                        </div>
                                        <small class="text-muted d-block mt-2">Warna yang ditampilkan saat gambar belum di-load</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><strong>Margin Bawah (Jarak ke Konten)</strong></label>
                                        <div class="d-flex gap-3 align-items-center">
                                            <input type="range" class="form-range flex-grow-1" name="thumbnail_margin_bottom" id="marginRange" 
                                                   min="0" max="100" step="5" value="<?= htmlspecialchars($thumbnail_margin_bottom) ?>"
                                                   onchange="updatePreview()">
                                            <div style="min-width: 80px; text-align: right;">
                                                <span id="marginValue" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($thumbnail_margin_bottom) ?></span>
                                                <span style="color: #999;">px</span>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-2">Jarak antara gambar dan informasi ruangan</small>
                                    </div>

                                    <hr>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                                            <i class="bi bi-save"></i> Simpan Pengaturan
                                        </button>
                                        <a href="rooms.php" class="btn btn-light border">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <div class="col-lg-6">
                        <div class="card shadow-sm">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                                <i class="bi bi-eye-fill"></i> Live Preview
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4"><small>Lihat perubahan secara real-time saat mengatur slider</small></p>
                                
                                <div class="p-4 border rounded" style="background: #f8f9fa;">
                                    <!-- Simulate room card -->
                                    <div id="previewBox" style="width: 100%; height: 180px; border-radius: 12px; overflow: hidden; background: #e2e8f0; margin-bottom: 15px;">
                                        <svg xmlns='http://www.w3.org/2000/svg' width='100%' height='100%' viewBox='0 0 400 180' preserveAspectRatio='none' style="display: block;">
                                            <rect width='400' height='180' fill='currentColor' opacity='0.3'/>
                                            <text x='50%' y='40%' font-family='Arial' font-size='14' fill='#999' text-anchor='middle' dominant-baseline='middle'>📷 Thumbnail Gambar</text>
                                            <text x='50%' y='60%' font-family='Arial' font-size='12' fill='#bbb' text-anchor='middle' dominant-baseline='middle'>Ruangan</text>
                                        </svg>
                                    </div>

                                    <div style="border-top: 2px solid #ddd; padding-top: 15px;">
                                        <h6 class="text-secondary mb-3"><i class="bi bi-info-circle"></i> Nilai Saat Ini</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-2">
                                                    <strong>Tinggi:</strong><br>
                                                    <span id="infoHeight" style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">180</span>px
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Border:</strong><br>
                                                    <span id="infoBorderRadius" style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">12</span>px
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2">
                                                    <strong>Warna BG:</strong><br>
                                                    <span id="infoBgColor" style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">#e2e8f0</span>
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Margin:</strong><br>
                                                    <span id="infoMargin" style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">15</span>px
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4 mb-0">
                                    <i class="bi bi-lightbulb"></i> <strong>Tips:</strong> Pengaturan ini akan otomatis diterapkan ke semua kartu ruangan di halaman daftar ruangan pengunjung.
                                </div>
                            </div>
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
        function updatePreview() {
            const height = document.getElementById('heightRange').value;
            const radius = document.getElementById('radiusRange').value;
            const bgColor = document.getElementById('bgColorInput').value;
            const margin = document.getElementById('marginRange').value;

            // Update preview box
            const previewBox = document.getElementById('previewBox');
            previewBox.style.height = height + 'px';
            previewBox.style.borderRadius = radius + 'px';
            previewBox.style.marginBottom = margin + 'px';
            previewBox.style.background = bgColor;

            // Update info display
            document.getElementById('infoHeight').textContent = height;
            document.getElementById('infoBorderRadius').textContent = radius;
            document.getElementById('infoBgColor').textContent = bgColor;
            document.getElementById('infoMargin').textContent = margin;

            // Update color text input
            document.getElementById('bgColorText').value = bgColor;
        }

        // Initialize preview
        updatePreview();
    </script>
</body>
</html>
