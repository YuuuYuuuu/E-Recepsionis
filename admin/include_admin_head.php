<?php
$adminThemePath = dirname(__DIR__) . '/assets/css/admin-theme.css';
$adminThemeVer = is_file($adminThemePath) ? (int) filemtime($adminThemePath) : time();
?>
<link href="../assets/css/admin-theme.css?v=<?= $adminThemeVer ?>" rel="stylesheet">
