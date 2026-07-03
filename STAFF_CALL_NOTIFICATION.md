# Fitur Notifikasi Real-Time dengan Suara untuk Panggilan Staff

## ✅ Fitur yang Ditambahkan

Ketika visitor mengisi form "Panggil Staff", admin akan mendapat:
1. **Notifikasi Popup** - Popup muncul di pojok kanan atas dengan informasi panggilan
2. **Suara Notifikasi** - Bunyi beep 3 kali yang berulang setiap 5 detik
3. **Tombol Aksi**:
   - **Terima** - Menandai panggilan sebagai terjawab dan menghentikan suara
   - **Hentikan Suara** - Hanya menghentikan suara, panggilan tetap pending

## 📁 File yang Ditambahkan

### File Baru:
1. **`api/get_pending_staff_calls.php`**
   - API endpoint untuk mendapatkan daftar panggilan staff yang pending
   - Return JSON dengan informasi panggilan

2. **`api/answer_staff_call.php`**
   - API endpoint untuk menandai panggilan sebagai terjawab
   - Update status dari 'pending' ke 'answered'

3. **`assets/js/staff-call-notification.js`**
   - JavaScript untuk real-time notification
   - Polling setiap 5 detik untuk cek panggilan baru
   - Play sound notification menggunakan Web Audio API
   - Menampilkan popup notification

4. **`assets/css/staff-call-notification.css`**
   - Styling untuk notification popup
   - Animasi slide-in dan pulse

## 🔄 Cara Kerja

1. **Polling**: Script melakukan polling setiap 5 detik ke API `get_pending_staff_calls.php`
2. **Deteksi Baru**: Jika ada panggilan baru (belum pernah ditampilkan), maka:
   - Play sound notification (3 beep)
   - Tampilkan popup notification
3. **Repeat Sound**: Suara akan berulang setiap 5 detik selama panggilan masih pending
4. **Stop Sound**: Admin bisa stop suara dengan tombol "Hentikan Suara"
5. **Answer Call**: Admin bisa terima panggilan dengan tombol "Terima", yang akan:
   - Update status ke 'answered'
   - Stop suara
   - Hapus notification
   - Reload halaman

## 🎨 Fitur Visual

### Notification Popup
- **Lokasi**: Pojok kanan atas (fixed position)
- **Desain**: Gradient purple dengan backdrop blur
- **Animasi**: Slide-in dari kanan
- **Konten**:
  - Icon telepon dengan animasi pulse
  - Nama visitor
  - Nomor telepon (clickable)
  - Pesan/keperluan
  - Tombol aksi

### Sound Notification
- **Format**: HTML5 Audio (MP3)
- **File**: `assets/nada.mp3`
- **Volume**: 70% (0.7)
- **Repeat**: Setiap 5 detik selama pending
- **Auto-stop**: Berhenti otomatis saat panggilan dijawab atau tombol "Hentikan Suara" diklik

## ⚙️ Konfigurasi

### Polling Interval
Default: 5 detik
Untuk mengubah, edit di `assets/js/staff-call-notification.js`:
```javascript
checkInterval = setInterval(checkStaffCalls, 5000); // 5 detik
```

### Sound Repeat Interval
Default: 5 detik
Untuk mengubah, edit di `assets/js/staff-call-notification.js`:
```javascript
const soundInterval = setInterval(() => {
    // ...
}, 5000); // 5 detik
```

### Notification Auto-remove
Default: 30 detik
Untuk mengubah, edit di `assets/js/staff-call-notification.js`:
```javascript
setTimeout(() => {
    notification.remove();
}, 30000); // 30 detik
```

## 🧪 Testing

1. Buka halaman admin (Dashboard atau Panggilan Staff)
2. Di tab lain, buka visitor interface
3. Isi form "Panggil Staff" dan submit
4. Kembali ke tab admin - harus ada:
   - Popup notification muncul
   - Suara beep 3 kali
   - Suara berulang setiap 5 detik
5. Klik "Hentikan Suara" - suara harus berhenti, popup tetap ada
6. Klik "Terima" - panggilan ditandai terjawab, suara stop, popup hilang

## 📝 Catatan

- Script hanya berjalan di halaman yang include `staff-call-notification.js`
- Saat ini sudah diinclude di:
  - `admin/index.php` (Dashboard)
  - `admin/staff_calls.php` (Panggilan Staff)
- Suara menggunakan Web Audio API, tidak perlu file audio
- Browser mungkin meminta permission untuk play audio (user interaction required)
- Notification popup bisa multiple jika ada beberapa panggilan pending

## 🔧 Troubleshooting

### Suara tidak berbunyi
- Pastikan browser support Web Audio API
- Browser mungkin memerlukan user interaction pertama sebelum play audio
- Cek console browser untuk error

### Notification tidak muncul
- Cek console browser untuk error
- Pastikan API `get_pending_staff_calls.php` mengembalikan data yang benar
- Cek network tab untuk request ke API

### Notification tidak hilang setelah di-answer
- Cek apakah API `answer_staff_call.php` berhasil
- Cek console untuk error
- Reload manual jika perlu

---

**Last Updated**: 2025-01-11
