<?php
/** @var array $prefs */
/** @var array $categoryIds */
/** @var string $displayName */
/** @var string $apiUrl */
?>
<div class="card mb-4" id="pref-notifikasi">
    <div class="card-header">
        <i class="bi bi-sliders"></i> Panggilan Staff — Preferensi Notifikasi
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Pengaturan untuk akun <strong><?= htmlspecialchars($displayName) ?></strong>.
            Popup dan dering hanya untuk kategori yang Anda tangani sebagai PIC.
        </p>

        <div id="prefAlert" class="alert d-none"></div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="prefNotificationsEnabled"
                   <?= ($prefs['notifications_enabled'] ?? true) ? 'checked' : '' ?>>
            <label class="form-check-label" for="prefNotificationsEnabled">
                <strong>Notifikasi popup</strong>
                <div class="small text-muted">Tampilkan popup saat ada panggilan staff baru untuk kategori Anda.</div>
            </label>
        </div>
        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" id="prefSoundEnabled"
                   <?= ($prefs['sound_enabled'] ?? true) ? 'checked' : '' ?>>
            <label class="form-check-label" for="prefSoundEnabled">
                <strong>Suara dering</strong>
                <div class="small text-muted">Bunyikan dering saat panggilan masuk (klik sekali di halaman admin agar browser mengizinkan audio).</div>
            </label>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary btn-sm" id="prefSaveBtn">
                <i class="bi bi-save"></i> Simpan preferensi
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="prefTestSoundBtn">
                <i class="bi bi-volume-up"></i> Tes dering
            </button>
        </div>

        <hr class="my-4">

        <div class="small">
            <div class="fw-semibold mb-1"><i class="bi bi-tags"></i> Kategori Anda</div>
            <?php if (!empty($categoryIds)): ?>
                <p class="text-muted mb-0">Anda menerima notifikasi untuk kategori yang ditugaskan di menu Kelola User.</p>
            <?php else: ?>
                <p class="text-warning mb-0">Belum ada kategori ditugaskan. Minta Super Admin menambahkan kategori di Kelola User.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
