<?php
require_once '../config.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header("Location: " . rtrim(BASE_URL, '/') . '/admin/login.php?logged_out=1');
exit;
?>
