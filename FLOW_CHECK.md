# Flow Check - E-Recepsionis System

## ✅ Flow Visitor Check-In

1. **Visitor** → `visitor/index.php` (Landing Page)
2. **Klik "Check-In"** → `visitor/checkin.php` (Form)
3. **Submit Form** → `visitor/checkin_process.php`
   - Validasi host
   - Upload foto (opsional)
   - Generate badge number
   - Insert ke tabel `visitors`
   - Insert ke tabel `queue` (jika add_to_queue = 1)
   - Insert notifikasi ke host
   - Kirim email ke host (jika enabled)
4. **Redirect** → `visitor/success.php` (Tampilkan badge & queue number)

## ✅ Flow Panggil Staff

1. **Visitor** → `visitor/index.php` (Landing Page)
2. **Klik "Panggil Staff"** → Modal form
3. **Submit Form** → `api/call_staff.php` (AJAX)
   - Validasi input
   - Insert ke tabel `staff_calls` (host_id = NULL)
   - Insert notifikasi ke admin (host_id = NULL)
   - Kirim email ke admin (jika enabled)
4. **Response JSON** → Success/Error message

## ✅ Flow Admin - Panggilan Staff

1. **Admin** → `admin/staff_calls.php`
2. **Lihat Daftar Panggilan** → Filter: pending/answered/cancelled
3. **Aksi**:
   - Klik nomor telepon → Hubungi visitor
   - Klik "Jawab" → Update status = answered
   - Klik "Batalkan" → Update status = cancelled

## ✅ Flow Check-Out

1. **Admin** → `admin/checkout.php`
2. **Input Badge Number** atau **Pilih dari Daftar**
3. **Submit** → Update `visitors.status` = 'checked-out'
4. **Update Queue** → Update `queue.status` = 'completed'
5. **Create Notification** → Notifikasi ke host

## ✅ Flow Queue Management

1. **Visitor Check-In** → Auto insert ke queue (jika add_to_queue = 1)
2. **Admin** → `admin/queue.php`
   - Lihat daftar antrian
   - Klik "Panggil" → Update status = 'in-progress'
   - Klik "Selesai" → Update status = 'completed'
3. **Visitor** → `visitor/queue.php` (Display real-time)
   - Auto-refresh setiap 5 detik
   - Tampilkan nomor antrian yang sedang dipanggil

## ✅ Flow Notification

1. **Check-In** → Notifikasi ke host
2. **Panggil Staff** → Notifikasi ke admin
3. **Check-Out** → Notifikasi ke host
4. **Admin** → `admin/notifications.php`
   - Lihat semua notifikasi
   - Tandai sebagai dibaca

## ✅ Database Flow

### Tabel `visitors`
- Status: pending → checked-in → checked-out
- Auto generate badge_number
- Record checkin_time & checkout_time

### Tabel `queue`
- Status: waiting → in-progress → completed
- Auto generate nomor_antrian
- Record waktu_masuk, waktu_dipanggil, waktu_selesai

### Tabel `staff_calls`
- Status: pending → answered/cancelled
- host_id = NULL (selalu ke admin)
- Record answered_by & answered_at

### Tabel `notifications`
- Status: unread → read
- host_id = NULL untuk notifikasi admin
- Type: checkin, checkout, system, queue

## ✅ Error Handling

1. **Database Connection** → Semua file require `config.php` yang include `koneksi.php`
2. **Function sendEmailNotification** → Menggunakan `global $koneksi`
3. **Badge Generation** → Redirect ke `badge.php` (HTML version, tidak perlu FPDF)
4. **API Response** → Selalu return JSON dengan success/error message

## ✅ Security

1. **Input Validation** → Function `esc()` untuk escape string
2. **SQL Injection** → Prepared statements (via esc() dan intval())
3. **File Upload** → Validasi extension & size
4. **Session** → Auth check di admin panel
5. **XSS Prevention** → htmlspecialchars() untuk output
