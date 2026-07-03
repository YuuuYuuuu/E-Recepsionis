# Fitur Notifikasi di Tab Browser

## ✅ Fitur yang Ditambahkan

Sistem notifikasi sekarang menampilkan jumlah notifikasi belum dibaca di:
1. **Tab Title Browser** - Menampilkan `(5) Nama Halaman` jika ada 5 notifikasi
2. **Favicon** - Menampilkan badge merah dengan angka di favicon
3. **Sidebar Badge** - Badge merah di menu "Notifikasi" di sidebar

## 📁 File yang Ditambahkan/Dimodifikasi

### File Baru:
1. **`api/get_notification_count.php`**
   - API endpoint untuk mendapatkan jumlah notifikasi belum dibaca
   - Return JSON: `{success: true, count: 5}`

2. **`assets/js/notification-badge.js`**
   - JavaScript untuk update tab title dan favicon secara real-time
   - Polling setiap 30 detik
   - Update otomatis saat tab menjadi visible/focus

### File yang Dimodifikasi:
1. **`admin/sidebar.php`**
   - Menambahkan badge notifikasi di menu "Notifikasi"
   - Badge muncul jika ada notifikasi belum dibaca

2. **Semua halaman admin** (`index.php`, `visitors.php`, `appointments.php`, dll)
   - Menambahkan script `notification-badge.js`
   - Menambahkan `window.originalPageTitle` untuk menyimpan title asli

## 🔄 Cara Kerja

1. **Initial Load**: Script mengambil jumlah notifikasi saat halaman dimuat
2. **Auto Update**: Script melakukan polling setiap 30 detik ke API
3. **Tab Visibility**: Update otomatis saat user kembali ke tab
4. **Window Focus**: Update saat window mendapat focus

## 🎨 Fitur Visual

### Tab Title
- **Tanpa notifikasi**: `Dashboard - E-Recepsionis System`
- **Dengan notifikasi**: `(5) Dashboard - E-Recepsionis System`

### Favicon
- **Tanpa notifikasi**: Favicon normal
- **Dengan notifikasi**: Favicon dengan badge merah berisi angka

### Sidebar
- Badge merah muncul di samping menu "Notifikasi"
- Menampilkan angka (maksimal 99+)

## ⚙️ Konfigurasi

### Interval Polling
Default: 30 detik
Untuk mengubah, edit di `assets/js/notification-badge.js`:
```javascript
updateInterval = setInterval(fetchNotificationCount, 30000); // 30 detik
```

### API Endpoint
URL: `/Recepsionis/api/get_notification_count.php`
Method: GET
Response: `{"success": true, "count": 5}`

## 🧪 Testing

1. Buka halaman admin manapun
2. Buat notifikasi baru (misal: visitor check-in atau staff call)
3. Cek tab browser - title harus menampilkan `(1) Nama Halaman`
4. Cek favicon - harus ada badge merah dengan angka
5. Cek sidebar - badge merah di menu "Notifikasi"

## 📝 Catatan

- Script hanya berjalan di halaman admin (yang menggunakan sidebar)
- Notifikasi dihitung untuk admin (host_id IS NULL)
- Badge favicon dibuat menggunakan Canvas API
- Polling interval bisa disesuaikan sesuai kebutuhan

---

**Last Updated**: 2025-01-11
