<?php
require_once 'auth.php';

header('Location: ' . (function_exists('adminUrl') ? adminUrl('settings.php#pref-notifikasi') : 'settings.php#pref-notifikasi'));
exit;
