# Recepsionis realtime server (Socket.io)

Layanan Node.js untuk live chat tamu–admin. Berjalan **bersamaan** dengan PHP/Apache (tidak menggantikan).

**Popup panggilan staff (live chat):** jangan menandai panggilan live sebagai “dijawab” hanya lewat PHP — admin harus membuka halaman Live Chat (`live_chat.php`) agar Socket.io mengirim `accept_request` dan tamu menerima `request_accepted`. Setelah admin **refresh** halaman Live Chat, sesi aktif dan riwayat pesan dipulihkan dari database (`syncActiveLiveSessionsForAdmin` + `admin_fetch_messages`).

## Pesan error: «Tidak terhubung ke http://127.0.0.1:3001»

Itu artinya **browser tidak menemukan proses Node di port 3001**. PHP/MAMP saja **tidak** menyalakan Socket.io — Anda harus menjalankan server ini **di terminal terpisah** dan **membiarkan terminal tetap terbuka**:

```bash
cd realtime-server
./start-dev.sh
```

atau `npm install` lalu `npm start`. Setelah jalan, di terminal harus muncul log `listening on http://0.0.0.0:3001`. Baru form «Kirim & tunggu admin» akan berhasil.

## Persiapan

1. Salin environment:

   ```bash
   cp .env.example .env
   ```

2. Isi `DB_*` agar sama dengan `koneksi.php`, dan `JWT_SECRET` **harus sama** dengan fallback/secret di PHP (`api/socket_token.php` memakai `SOCKET_JWT_SECRET` dari `config.socket.php` atau env `SOCKET_JWT_SECRET`, atau fallback dev bawaan).

3. Jalankan migrasi SQL (sekali):

   ```bash
   mysql -u USER -p recepsionis_db < ../migrations/live_chat_socket.sql
   ```

4. Install & jalankan:

   ```bash
   npm install
   npm start
   ```

   Default: `http://127.0.0.1:3001`

## Variabel penting

| Variabel | Keterangan |
|----------|------------|
| `PORT` | Port HTTP + WebSocket |
| `DB_HOST` / `DB_PORT` | **MAMP:** biasanya `127.0.0.1` + `8889` (bukan `3306`). Cek *MAMP → Preferences → Ports*. PHP `localhost` bisa lewat *socket*; Node wajib TCP + port. |
| `JWT_SECRET` | **Wajib sama** dengan secret di `api/socket_token.php`: `config.socket.php` → `SOCKET_JWT_SECRET`, atau env `SOCKET_JWT_SECRET`, atau fallback dev `recepsionis-dev-jwt-secret-change-in-production`. Setelah ubah `.env`, restart Node. |
| `CORS_ORIGIN` | Kosong atau `*` = semua origin (dev). Daftar ketat: dipisah koma, mis. `http://localhost:8888` |

## Antrian admin kosong / tamu menunggu terus

1. **Satu database:** `DB_NAME` di `.env` harus **sama** dengan `$dbname` di `koneksi.php`. Cek cepat:

   `php migrations/check_db_alignment.php`

2. **Skema:** jalankan `php migrations/ensure_latest_schema.php` (kolom `live_session_id`, `call_type` VARCHAR, dll.).

3. **Satu proses Node** dan URL socket tamu = admin (`config.php` / halaman PHP).

4. Di MySQL, pastikan baris permintaan ada:

   `SELECT id, call_type, status, live_session_id, live_status FROM staff_calls ORDER BY id DESC LIMIT 5;`

   Yang masuk antrian live: `status = pending`, `live_session_id` terisi, `call_type` `live_chat` atau NULL.

5. Log Node saat tamu kirim: harus ada `guest_request staff_call_id=...`. Saat admin buka Live Chat: `syncPendingLiveForAdmin: N pending live row(s)`.

## Produksi (VPS)

- Jalankan dengan **PM2**: `pm2 start src/server.js --name recepsionis-realtime`
- **Nginx** (contoh) — proxy WebSocket:

```nginx
location /socket.io/ {
    proxy_pass http://127.0.0.1:3001;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
}
```

- Di `config.php` (PHP), set `LIVE_SOCKET_URL` ke URL publik Node (mis. `https://socket.domainanda.com` tanpa slash akhir).

## Build frontend

Dari folder `visitor-app`:

```bash
npm run build:all
```

Ini memperbarui bundle visitor (`visitor/assets/landing/`) dan admin live chat (`admin/assets/live-chat/`).
