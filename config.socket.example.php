<?php
/**
 * Salin file ini menjadi config.socket.php di root proyek (sejajar config.php).
 * Isi secret yang SAMA dengan JWT_SECRET di realtime-server/.env
 * Jangan commit config.socket.php ke repositori publik.
 */
// Produksi: string acak panjang; harus identik dengan JWT_SECRET di realtime-server/.env
// Dev default di bawah sama dengan fallback di api/socket_token.php + .env.example Node
define('SOCKET_JWT_SECRET', 'recepsionis-dev-jwt-secret-change-in-production');
