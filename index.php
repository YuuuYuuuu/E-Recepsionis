<?php
/**
 * Index - E-Recepsionis
 * Redirect ke halaman visitor (check-in)
 */
$root = __DIR__;
if (is_file($root . '/maintenance.flag')) {
    if (!headers_sent()) {
        header('HTTP/1.1 503 Service Unavailable');
        header('Retry-After: 3600');
    }
    require $root . '/maintenance.php';
    exit;
}
header('Location: visitor/');
exit;
