<?php
$staffCallJsPath = dirname(__DIR__) . '/assets/js/staff-call-notification.js';
$staffCallJsVer = is_file($staffCallJsPath) ? (int) filemtime($staffCallJsPath) : time();
?>
<script src="../assets/js/staff-call-notification.js?v=<?= $staffCallJsVer ?>"></script>
<?php include 'include_admin_footer.php'; ?>
