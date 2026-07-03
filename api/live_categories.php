<?php
/**
 * Daftar kategori pengaduan untuk form live chat tamu (JSON publik).
 */
declare(strict_types=1);

define('API_CONTEXT', true);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/koneksi.php';
require_once dirname(__DIR__) . '/staff_call_routing.php';

$rows = [];
if (recepsionis_table_exists($koneksi, 'admin_category_routing')) {
    $res = $koneksi->query(
        'SELECT cc.id, cc.nama_kategori, cc.deskripsi, cc.icon, cc.warna, cc.urutan,
                COUNT(DISTINCT u.id) AS available_admin_count
         FROM complaint_categories cc
         INNER JOIN admin_category_routing acr ON acr.category_id = cc.id
         INNER JOIN users u ON u.id = acr.user_id AND u.status_aktif = 1
         WHERE cc.status_aktif = 1
         GROUP BY cc.id, cc.nama_kategori, cc.deskripsi, cc.icon, cc.warna, cc.urutan
         ORDER BY cc.urutan ASC, cc.nama_kategori ASC'
    );
} else {
    $res = $koneksi->query(
        'SELECT id, nama_kategori, deskripsi, icon, warna, urutan, 0 AS available_admin_count
         FROM complaint_categories
         WHERE status_aktif = 1
         ORDER BY urutan ASC, nama_kategori ASC'
    );
}

if ($res === false) {
    http_response_code(500);
    echo json_encode(
        [
            'success' => false,
            'error' => 'query_failed',
            'message' => 'Tabel kategori tidak tersedia atau query gagal.',
            'categories' => [],
        ],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

while ($row = $res->fetch_assoc()) {
    $rows[] = [
        'id' => (int) $row['id'],
        'nama_kategori' => $row['nama_kategori'],
        'deskripsi' => $row['deskripsi'],
        'icon' => $row['icon'],
        'warna' => $row['warna'],
        'urutan' => (int) $row['urutan'],
        'available_admin_count' => (int) ($row['available_admin_count'] ?? 0),
    ];
}

echo json_encode(['success' => true, 'categories' => $rows], JSON_UNESCAPED_UNICODE);
