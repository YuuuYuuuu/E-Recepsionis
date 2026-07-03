<?php
/**
 * Cek cepat: database di koneksi.php harus sama dengan DB_NAME di realtime-server/.env
 * (tanpa koneksi MySQL — aman di CLI/sandbox)
 *
 * CLI: php migrations/check_db_alignment.php
 * Web: localhost only — migrations/check_db_alignment.php
 */
declare(strict_types=1);

$isCli = php_sapi_name() === 'cli';
if (!$isCli) {
    $ok = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
    if (!$ok) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Forbidden';
        exit(1);
    }
    header('Content-Type: text/plain; charset=utf-8');
}

$kPath = dirname(__DIR__) . '/koneksi.php';
$rawK = @file_get_contents($kPath);
if ($rawK === false) {
    echo "Tidak bisa baca koneksi.php\n";
    exit(1);
}
$phpDb = 'recepsionis_db';
if (preg_match('/\$dbname\s*=\s*["\']([^"\']+)["\']\s*;/', $rawK, $m)) {
    $phpDb = $m[1];
}
echo "PHP (koneksi.php) database: {$phpDb}\n";

$envPath = dirname(__DIR__) . '/realtime-server/.env';
if (is_readable($envPath)) {
    $raw = file_get_contents($envPath);
    if ($raw !== false && preg_match('/^DB_NAME=(.*)$/m', $raw, $m)) {
        $nodeDb = trim($m[1], " \t\n\r\0\x0B\"'");
        echo "Node (.env) DB_NAME:           {$nodeDb}\n";
        if ($nodeDb !== $phpDb) {
            echo "\nPERINGATAN: Nama database berbeda — tamu (Node) dan admin sync bisa tidak sinkron!\n";
            exit(1);
        }
        echo "\nOK: nama database sama.\n";
    } else {
        echo "Node: tidak menemukan DB_NAME di realtime-server/.env\n";
        exit(1);
    }
} else {
    echo "Node: file realtime-server/.env tidak ada atau tidak terbaca.\n";
    exit(1);
}

exit(0);
