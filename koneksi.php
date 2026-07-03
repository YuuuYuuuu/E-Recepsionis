<?php
// Koneksi Database untuk E-Recepsionis System
$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "recepsionis_db";

$koneksi = new mysqli($host, $user, $pass, $dbname);

// Set charset UTF-8
$koneksi->set_charset("utf8mb4");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Fungsi helper untuk escape string
function esc($string) {
    global $koneksi;
    return $koneksi->real_escape_string($string);
}

// Fungsi untuk mendapatkan hari ini dalam bahasa Indonesia
function getHariIni() {
    $hari_en = date('l');
    $hari_id = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];
    return $hari_id[$hari_en];
}

// Fungsi untuk format tanggal Indonesia
function formatTanggal($date) {
    $hari = getHariIni();
    $tanggal = date('d', strtotime($date));
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $bulan_str = $bulan[date('m', strtotime($date))];
    $tahun = date('Y', strtotime($date));
    return "$hari, $tanggal $bulan_str $tahun";
}

// Fungsi untuk format waktu
function formatWaktu($time) {
    return date('H:i', strtotime($time));
}
