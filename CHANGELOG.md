# Changelog - E-Recepsionis System

## Update 2025-01-11 - Landing Page Visitor

### Fitur Baru

#### 1. Landing Page Visitor yang User-Friendly
- **Tampilan Baru**: Landing page dengan card-based design yang mudah digunakan
- **Hero Section**: Header dengan greeting dan waktu real-time
- **Quick Actions**: 5 card utama untuk akses cepat ke fitur:
  - Check-In
  - Daftar Ruangan
  - Program Perkuliahan
  - Panggil Staff
  - Lihat Antrian

#### 2. Daftar Ruangan
- **Modal Display**: Tampilkan daftar ruangan dalam modal yang mudah dibaca
- **Informasi Lengkap**: Kode, nama, lokasi, gedung, lantai, kapasitas
- **Admin Panel**: Halaman admin untuk mengelola ruangan (`admin/rooms.php`)
- **Database**: Tabel `rooms` dengan field lengkap

#### 3. Program Perkuliahan
- **Preview Hari Ini**: Tampilkan program hari ini di landing page
- **Modal Detail**: Modal untuk melihat semua program
- **Kategori**: Perkuliahan, Seminar, Workshop, Event, Lainnya
- **Admin Panel**: Halaman admin untuk mengelola program (`admin/programs.php`)
- **Database**: Tabel `programs` dengan informasi lengkap

#### 4. Panggil Staff
- **Form Panggilan**: Form untuk visitor memanggil staff
- **Jenis Panggilan**: Umum, Spesifik, Darurat
- **Notifikasi**: Auto-notifikasi ke staff yang dipilih atau admin
- **Admin Panel**: Halaman untuk melihat dan mengelola panggilan (`admin/staff_calls.php`)
- **Database**: Tabel `staff_calls` untuk tracking panggilan

### Perubahan File

#### File Baru
- `visitor/index.php` - Landing page baru dengan card design
- `visitor/checkin.php` - Form check-in yang lebih baik
- `visitor/checkin_process.php` - Proses check-in terpisah
- `admin/rooms.php` - Kelola ruangan
- `admin/programs.php` - Kelola program perkuliahan
- `admin/staff_calls.php` - Kelola panggilan staff
- `api/call_staff.php` - API untuk panggilan staff
- `database_additions.sql` - Schema tambahan untuk fitur baru

#### File yang Diupdate
- `admin/sidebar.php` - Menambahkan menu baru
- `visitor/success.php` - Link kembali ke menu utama

### Database Changes

Jalankan file `database_additions.sql` untuk menambahkan tabel:
- `rooms` - Daftar ruangan
- `programs` - Program perkuliahan
- `staff_calls` - Panggilan staff

### Cara Update

1. **Import Database Additions**:
   ```bash
   mysql -u root -p recepsionis_db < database_additions.sql
   ```

2. **File sudah otomatis terupdate**, tidak perlu perubahan manual

3. **Akses Landing Page**:
   - Visitor: `http://localhost/Recepsionis/visitor/`
   - Admin: `http://localhost/Recepsionis/admin/`

### Fitur Landing Page

1. **Check-In Card**: Langsung ke form check-in
2. **Daftar Ruangan Card**: Modal dengan daftar ruangan lengkap
3. **Program Perkuliahan Card**: Modal dengan program hari ini dan semua program
4. **Panggil Staff Card**: Form untuk memanggil staff dengan notifikasi
5. **Lihat Antrian Card**: Link ke tampilan antrian real-time

### UI/UX Improvements

- **Card-Based Design**: Setiap fitur dalam card terpisah yang jelas
- **Color Coding**: Setiap card memiliki warna berbeda untuk identifikasi cepat
- **Responsive**: Design responsive untuk tablet dan mobile
- **Modal Windows**: Informasi detail dalam modal untuk tidak membingungkan
- **Clear Navigation**: Tombol kembali ke menu utama di setiap halaman

### Next Steps

Untuk menggunakan fitur baru:
1. Import `database_additions.sql`
2. Tambah data ruangan via admin panel
3. Tambah program perkuliahan via admin panel
4. Visitor dapat langsung menggunakan landing page baru

---

**Version**: 1.1  
**Date**: 2025-01-11
