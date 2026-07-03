# E-Recepsionis System

Sistem manajemen kunjungan tamu digital dengan fitur lengkap: check-in/out, appointment, antrian, badge, dan notifikasi.

## Fitur Utama

### ✅ Admin Panel
- **Dashboard**: Statistik dan overview sistem
- **Data Tamu**: Kelola data tamu, check-in/out
- **Appointment**: Sistem booking appointment
- **Antrian**: Kelola antrian tamu
- **Host**: Kelola data host/pemilik ruangan
- **Notifikasi**: Lihat dan kelola notifikasi
- **Settings**: Konfigurasi sistem
- **Live Chat**: Obrolan real-time tamu–admin (Socket.io), menu **Live Chat** di sidebar

### ✅ Visitor Interface
- **Check-In**: Form check-in tamu dengan upload foto
- **Tampilan Antrian**: Display antrian real-time untuk tamu
- **Badge**: Cetak badge dengan QR code

### ✅ Fitur Utama
1. **Check-In/Out**: Sistem check-in dan check-out tamu
2. **Badge System**: Generate badge dengan QR code
3. **Queue Management**: Sistem antrian otomatis
4. **Appointment**: Booking appointment dengan host
5. **Notification**: Notifikasi email dan in-app
6. **Real-time Updates**: Update real-time untuk antrian

## Instalasi

### 1. Requirements
- PHP 7.4+ (disarankan PHP 8.0+)
- MySQL 5.7+ / MariaDB 10.3+
- Web Server: Apache/Nginx (MAMP/XAMPP/WAMP)
- Browser: Chrome, Firefox, Safari, Edge
- **Live chat (opsional):** Node.js 18+ untuk `realtime-server/` — lihat [realtime-server/README.md](realtime-server/README.md)

### 2. Setup Database

```bash
# Import database
mysql -u root -p < database.sql
```

Atau via phpMyAdmin:
1. Buka phpMyAdmin
2. Import file `database.sql`

### 3. Konfigurasi

Edit file `koneksi.php`:
```php
$host = "localhost";
$user = "root";
$pass = "root";  // Sesuaikan password MySQL
$dbname = "recepsionis_db";
```

Untuk **live chat**, set URL server Node di `config.php` (konstanta `LIVE_SOCKET_URL`, default `http://127.0.0.1:3001`). **Samakan skema database** (idempoten, aman diulang):

```bash
cd /path/ke/Recepsionis
php migrations/ensure_latest_schema.php
```

*(MAMP: gunakan PHP biner MAMP, mis. `/Applications/MAMP/bin/php/php8.x.x/bin/php`.)*  
Atau buka dari browser (localhost saja): `.../migrations/ensure_latest_schema.php`.  
Cek **PHP vs Node memakai database yang sama:** `php migrations/check_db_alignment.php`.  
Alternatif SQL mentah: `migrations/live_chat_socket.sql` (bisa error duplicate jika kolom sudah ada).

Lalu `cd realtime-server && cp .env.example .env && npm install && npm start`. Detail: [realtime-server/README.md](realtime-server/README.md).

Build bundle React (visitor + admin live chat):

```bash
cd visitor-app && npm install && npm run build:all
```

### 4. Set Permissions

```bash
chmod 777 uploads/
```

### 5. Akses Aplikasi

- **Admin Login**: `http://localhost/Recepsionis/admin/`
- **Check-In**: `http://localhost/Recepsionis/visitor/`
- **Tampilan Antrian**: `http://localhost/Recepsionis/visitor/queue.php`

### 6. Default Login

```
Username: admin
Password: admin123
```

⚠️ **PENTING**: Segera ganti password setelah login pertama!

## Struktur Folder

```
Recepsionis/
├── admin/              # Admin panel
│   ├── index.php       # Dashboard
│   ├── visitors.php    # Kelola tamu
│   ├── appointments.php
│   ├── queue.php
│   ├── hosts.php
│   ├── notifications.php
│   ├── settings.php
│   ├── checkout.php
│   └── badge.php
├── visitor/            # Interface tamu
│   ├── index.php       # Form check-in
│   ├── checkin.php     # Proses check-in
│   ├── success.php     # Halaman sukses
│   └── queue.php       # Tampilan antrian
├── api/                # API endpoints
│   ├── checkin.php
│   ├── checkout.php
│   ├── get_queue.php
│   └── notify.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── uploads/            # Foto tamu, badge
├── koneksi.php         # Database connection
├── config.php          # Konfigurasi
├── badge.php           # Generate badge
└── database.sql        # Schema database
```

## Database Schema

### Tabel Utama
- **users**: Admin/operator
- **hosts**: Data host/pemilik ruangan
- **visitors**: Data tamu
- **appointments**: Booking appointment
- **queue**: Sistem antrian
- **notifications**: Notifikasi sistem
- **settings**: Pengaturan sistem

## Penggunaan

### Check-In Tamu
1. Buka halaman check-in: `http://localhost/Recepsionis/visitor/`
2. Isi form: nama, email, no telp, perusahaan, pilih host, tujuan
3. Upload foto (opsional)
4. Pilih "Tambahkan ke antrian" jika perlu
5. Klik "Check-In"
6. Cetak badge jika diperlukan

### Check-Out Tamu
1. Buka admin panel → Check-Out
2. Scan badge atau pilih dari daftar
3. Klik "Check-Out"

### Kelola Appointment
1. Admin panel → Appointment
2. Klik "Buat Appointment"
3. Isi form dan simpan
4. Konfirmasi/cancel appointment sesuai kebutuhan

### Kelola Antrian
1. Admin panel → Antrian
2. Lihat daftar antrian aktif
3. Klik "Panggil" untuk memanggil antrian
4. Klik "Selesai" setelah selesai

## API Endpoints

### Check-In
```
POST /api/checkin.php
Body: {
    "nama": "John Doe",
    "email": "john@example.com",
    "no_telp": "081234567890",
    "perusahaan": "PT Example",
    "host_id": 1,
    "tujuan": "Meeting",
    "add_to_queue": true
}
```

### Check-Out
```
POST /api/checkout.php
Body: {
    "badge_number": "202501110001"
}
```

### Get Queue
```
GET /api/get_queue.php?status=active&host_id=1
```

## Security

- Password hashing dengan bcrypt
- Session management dengan timeout
- Input validation & sanitization
- SQL injection prevention (prepared statements)
- XSS prevention
- File upload security

## Troubleshooting

### Database Connection Error
- Cek konfigurasi di `koneksi.php`
- Pastikan database sudah diimport
- Cek username/password MySQL

### Upload Error
- Cek permission folder `uploads/` (chmod 777)
- Cek `upload_max_filesize` di PHP.ini
- Cek `post_max_size` di PHP.ini

### Badge Tidak Muncul
- Cek file badge.php ada
- Cek permission folder uploads
- Cek browser console untuk error

## Support

Untuk pertanyaan atau bantuan, silakan hubungi developer.

---

**Version**: 1.0  
**Last Updated**: 2025-01-11
