<?php include 'include_admin_head.php'; ?>
<?php
$staffCallCssPath = dirname(__DIR__) . '/assets/css/staff-call-notification.css';
$staffCallCssVer = is_file($staffCallCssPath) ? (int) filemtime($staffCallCssPath) : time();
?>
<link href="../assets/css/staff-call-notification.css?v=<?= $staffCallCssVer ?>" rel="stylesheet">
<style>
    /* Sembunyikan toolbar notifikasi lama agar tidak menutupi tombol aksi halaman */
    .staff-call-toolbar,
    .staff-call-pref-toggle,
    .staff-call-sound-toggle {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
</style>
<script>
    (function () {
        function removeStaffCallToolbar() {
            document.querySelectorAll('.staff-call-toolbar, .staff-call-pref-toggle, .staff-call-sound-toggle').forEach(function (el) {
                el.remove();
            });
        }
        removeStaffCallToolbar();
        document.addEventListener('DOMContentLoaded', removeStaffCallToolbar);
        window.addEventListener('load', removeStaffCallToolbar);
    })();
</script>
<script>
    window.__RECEPSIONIS_ADMIN_BASE_URL__ = <?= json_encode(function_exists('adminUrl') ? adminUrl('') : (rtrim(BASE_URL, '/') . '/admin/'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.__RECEPSIONIS_API_BASE_URL__ = <?= json_encode(function_exists('apiUrl') ? apiUrl('') : (rtrim(BASE_URL, '/') . '/api/'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.__RECEPSIONIS_ASSETS_BASE_URL__ = <?= json_encode(rtrim(BASE_URL, '/') . '/assets/', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.__LIVE_SOCKET_URL__ = <?= json_encode(recepsionis_live_socket_url_for_browser(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.__SOCKET_TOKEN_URL__ = <?= json_encode(function_exists('apiUrl') ? apiUrl('socket_token.php') : (rtrim(BASE_URL, '/') . '/api/socket_token.php'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
