<?php
require_once 'auth.php';
requireSuperAdminPage();

// Handle image deletion
if (isset($_GET['delete_image']) && isset($_GET['room_id'])) {
    $room_id = (int)$_GET['room_id'];
    $image_name = basename($_GET['delete_image']); // security: use basename
    
    // Get current images
    $res = $koneksi->query("SELECT images FROM rooms WHERE id = " . (int)$room_id . " LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $images = array_filter(array_map('trim', explode(',', $row['images'] ?? '')));
        
        // Remove the image from array
        $images = array_filter($images, function($img) use ($image_name) {
            return basename($img) !== $image_name;
        });
        
        // Delete file from disk
        $file_path = __DIR__ . '/../' . $image_name;
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
        
        // Update DB
        $new_images_csv = !empty($images) ? implode(',', $images) : NULL;
        $img_sql = is_null($new_images_csv) ? 'NULL' : "'" . $koneksi->real_escape_string($new_images_csv) . "'";
        $koneksi->query("UPDATE rooms SET images = $img_sql WHERE id = " . (int)$room_id);
        
        // Redirect back
        header('Location: room_gallery.php?room_id=' . (int)$room_id . '&success=deleted');
        exit;
    }
}

// Handle set primary image (make chosen image first in images list)
if (isset($_GET['set_primary']) && isset($_GET['room_id'])) {
    $room_id = (int)$_GET['room_id'];
    // Expecting full path like 'uploads/rooms/xxx.jpg' or just filename
    $requested = $_GET['set_primary'];
    $requested_basename = basename($requested);

    $res = $koneksi->query("SELECT images FROM rooms WHERE id = " . (int)$room_id . " LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $images = array_filter(array_map('trim', explode(',', $row['images'] ?? '')));

        // Find the matching image index by basename or full path
        $found = null;
        foreach ($images as $idx => $img) {
            if (basename($img) === $requested_basename || $img === $requested) {
                $found = $idx;
                break;
            }
        }

        if (!is_null($found)) {
            // Move selected image to front
            $selected = $images[$found];
            unset($images[$found]);
            array_unshift($images, $selected);

            $new_images_csv = !empty($images) ? implode(',', $images) : NULL;
            $img_sql = is_null($new_images_csv) ? 'NULL' : "'" . $koneksi->real_escape_string($new_images_csv) . "'";
            $koneksi->query("UPDATE rooms SET images = $img_sql WHERE id = " . (int)$room_id);
        }
    }

    header('Location: room_gallery.php?room_id=' . (int)$room_id . '&success=primary_set');
    exit;
}

// Get room ID from query
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
if ($room_id <= 0) {
    header('Location: rooms.php');
    exit;
}

// Get room details
$room = null;
$res = $koneksi->query("SELECT id, nama_ruangan, images FROM rooms WHERE id = " . (int)$room_id . " LIMIT 1");
if ($res && $res->num_rows > 0) {
    $room = $res->fetch_assoc();
} else {
    header('Location: rooms.php');
    exit;
}

// Parse images
$images = [];
if (!empty($room['images'])) {
    $images = array_filter(array_map('trim', explode(',', $room['images'])));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: #e2e8f0;
            aspect-ratio: 4/3;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        .gallery-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-overlay a, .gallery-overlay button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-images"></i> Gallery - <?= htmlspecialchars($room['nama_ruangan']) ?></h2>
        <div>
            <a href="rooms.php" class="btn btn-secondary">Kembali</a>
            <a href="normalize_image_paths.php?room_id=<?= (int)$room['id'] ?>" class="btn btn-outline-primary ms-2">Normalize Paths</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i>
            <?php
                if ($_GET['success'] == 'deleted') echo 'Gambar berhasil dihapus';
                elseif ($_GET['success'] == 'primary_set') echo 'Gambar dipilih sebagai preview utama';
                elseif (isset($_GET['uploaded'])) echo intval($_GET['uploaded']) . ' gambar berhasil diupload';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row" id="gallery-row">
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $idx => $img): ?>
                <?php 
                    // Normalize path - add ../ prefix if it's a relative path from admin folder
                    $display_img = $img;
                    if (!preg_match('~^(https?://|/|\.\./)~', $img)) {
                        $display_img = '../' . $img;
                    }
                ?>
                <div class="col-md-4 mb-4 gallery-col">
                    <div class="gallery-item" draggable="true" data-src="<?= htmlspecialchars($img) ?>">
                        <img src="<?= htmlspecialchars($display_img) ?>" alt="<?= htmlspecialchars($room['nama_ruangan']) ?> - Image <?= $idx+1 ?>" onerror="this.style.display='none'">
                        <div class="gallery-overlay">
                            <a href="<?= htmlspecialchars($display_img) ?>" target="_blank" class="btn btn-info" title="Buka di tab baru">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-success btn-toggle-primary" data-src="<?= htmlspecialchars($img) ?>" data-action="<?= $idx === 0 ? 'unset' : 'set' ?>" title="Toggle primary">
                                <i class="bi bi-star<?= $idx === 0 ? '-fill' : '' ?>"></i>
                            </button>
                            <a href="?room_id=<?= (int)$room_id ?>&delete_image=<?= urlencode($img) ?>" 
                               class="btn btn-danger" 
                               title="Hapus gambar"
                               onclick="return confirm('Hapus gambar ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2" style="word-break: break-all;">
                        <?= htmlspecialchars(basename($img)) ?>
                    </small>
                </div>
            <?php endforeach; ?>
            <div id="gallery-controls" class="d-flex gap-2 w-100 mb-3">
                <button id="save-order-btn" class="btn btn-primary">Simpan Urutan Gambar</button>
                <div id="save-feedback" class="text-success align-self-center" style="display:none">Tersimpan</div>
            </div>
            <script>
                (function(){
                    const galleryRow = document.getElementById('gallery-row');
                    let dragEl = null;
                    let placeholder = null;

                    function createPlaceholder() {
                        const col = document.createElement('div');
                        col.className = 'col-md-4 mb-4 gallery-col placeholder';
                        col.innerHTML = '<div class="gallery-item" style="background:#f8fafc;border:2px dashed #cbd5e1;min-height:120px"></div>';
                        return col;
                    }

                    function onDragStart(e) {
                        dragEl = this.parentNode; // .gallery-col
                        this.style.opacity = '0.5';
                        e.dataTransfer.effectAllowed = 'move';
                    }

                    function onDragEnd() {
                        if (dragEl) dragEl.querySelector('.gallery-item').style.opacity = '';
                        if (placeholder && placeholder.parentNode) placeholder.parentNode.removeChild(placeholder);
                        dragEl = null;
                    }

                    function onDragOver(e) {
                        e.preventDefault();
                        const targetCol = this.parentNode; // .gallery-col
                        if (!placeholder) {
                            placeholder = createPlaceholder();
                        }
                        if (targetCol && dragEl && targetCol !== dragEl) {
                            galleryRow.insertBefore(placeholder, targetCol.nextSibling);
                        }
                    }

                    function onDrop(e) {
                        e.preventDefault();
                        const targetCol = this.parentNode;
                        if (!dragEl || dragEl === targetCol) return;
                        galleryRow.insertBefore(dragEl, targetCol.nextSibling);
                        if (placeholder && placeholder.parentNode) placeholder.parentNode.removeChild(placeholder);
                    }

                    document.querySelectorAll('.gallery-item').forEach(item => {
                        item.addEventListener('dragstart', onDragStart);
                        item.addEventListener('dragend', onDragEnd);
                        item.addEventListener('dragover', onDragOver);
                        item.addEventListener('drop', onDrop);
                    });

                    // Save order via AJAX
                    document.getElementById('save-order-btn').addEventListener('click', async () => {
                        const roomId = <?= (int)$room['id'] ?>;
                        const items = document.querySelectorAll('.gallery-item');
                        const order = Array.from(items).map(it => it.dataset.src);
                        const fd = new FormData();
                        fd.append('room_id', roomId);
                        order.forEach(o => fd.append('order[]', o));

                        const btn = document.getElementById('save-order-btn');
                        btn.disabled = true;
                        btn.textContent = 'Menyimpan...';

                        try {
                            const res = await fetch('reorder_room_images.php', { method: 'POST', body: fd });
                            const data = await res.json();
                            if (data.success) {
                                const fb = document.getElementById('save-feedback');
                                fb.style.display = 'block';
                                setTimeout(() => fb.style.display = 'none', 2000);
                            } else {
                                alert('Gagal menyimpan urutan: ' + (data.message||'error'));
                            }
                        } catch (err) {
                            alert('Terjadi kesalahan: ' + err.message);
                        } finally {
                            btn.disabled = false;
                            btn.textContent = 'Simpan Urutan Gambar';
                        }
                    });

                    // Toggle primary via AJAX
                    async function togglePrimaryHandler(e) {
                        const btn = this;
                        const src = btn.dataset.src;
                        const action = btn.dataset.action || 'set';
                        const roomId = <?= (int)$room['id'] ?>;

                        btn.disabled = true;
                        try {
                            const fd = new FormData();
                            fd.append('room_id', roomId);
                            fd.append('src', src);
                            fd.append('action', action);
                            const res = await fetch('set_primary_ajax.php', { method: 'POST', body: fd });
                            const data = await res.json();
                            if (data.success) {
                                // Move DOM element accordingly
                                const cols = Array.from(document.querySelectorAll('.gallery-col'));
                                const srcMap = cols.map(c => c.querySelector('.gallery-item').dataset.src);
                                const idx = srcMap.indexOf(src);
                                if (idx !== -1) {
                                    const col = cols[idx];
                                    if (action === 'set') {
                                        galleryRow.insertBefore(col, galleryRow.firstChild);
                                    } else {
                                        galleryRow.appendChild(col);
                                    }
                                }
                                // update star icons/actions
                                document.querySelectorAll('.btn-toggle-primary').forEach((b, i) => {
                                    if (i === 0) {
                                        b.dataset.action = 'unset';
                                        b.innerHTML = '<i class="bi bi-star-fill"></i>';
                                    } else {
                                        b.dataset.action = 'set';
                                        b.innerHTML = '<i class="bi bi-star"></i>';
                                    }
                                });
                            } else {
                                alert('Gagal: ' + (data.message||'error'));
                            }
                        } catch (err) {
                            alert('Error: ' + err.message);
                        } finally {
                            btn.disabled = false;
                        }
                    }

                    document.querySelectorAll('.btn-toggle-primary').forEach(b => b.addEventListener('click', togglePrimaryHandler));
                })();
            </script>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> Tidak ada gambar untuk ruangan ini.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4 p-3 bg-white rounded">
        <h5><i class="bi bi-cloud-arrow-up"></i> Upload Gambar Baru</h5>
        <form action="upload_room_images.php" method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="room_id" value="<?= (int)$room_id ?>">
            <div class="mb-3">
                <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
                <small class="text-muted">Pilih satu atau lebih gambar</small>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
