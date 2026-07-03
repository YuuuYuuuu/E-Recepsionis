<?php
$adminMobileJsPath = dirname(__DIR__) . '/assets/js/admin-mobile.js';
$adminMobileJsVer = is_file($adminMobileJsPath) ? (int) filemtime($adminMobileJsPath) : time();
?>
<script src="../assets/js/admin-mobile.js?v=<?= $adminMobileJsVer ?>"></script>
