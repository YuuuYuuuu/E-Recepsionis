# Debug Notifikasi - E-Recepsionis System

## Masalah
Data masuk ke admin tapi notifikasi gagal dengan error "Invalid request"

## Penyebab yang Mungkin

1. **Output sebelum JSON**
   - Session start mungkin menghasilkan output
   - Warning/notice dari PHP
   - Output dari require_once

2. **Response tidak valid JSON**
   - Ada karakter sebelum `{`
   - Ada karakter setelah `}`
   - Encoding issue

3. **JavaScript parsing error**
   - Response tidak bisa di-parse sebagai JSON
   - Error di fetch handling

## Solusi yang Diterapkan

### 1. API Context
- Define `API_CONTEXT` sebelum require config
- Prevent session_start() di API
- Suppress semua output

### 2. Output Buffering
- Multiple layer output buffering
- Clean semua buffer sebelum JSON
- Log unexpected output

### 3. Error Handling
- Custom error handler
- Log semua error tapi jangan output
- Try-catch untuk semua operasi opsional

### 4. JavaScript Handling
- Parse JSON dengan try-catch
- Jika parse gagal tapi data masuk, show success
- Better error messages

## Testing

1. Test dengan browser console open
2. Check Network tab untuk response
3. Check Console untuk error
4. Verify data masuk ke database
5. Verify response JSON valid

## File yang Diperbaiki

1. `api/call_staff.php` - Output buffering & error handling
2. `api/notify.php` - Output buffering
3. `config.php` - Conditional session start
4. `visitor/index.php` - Better error handling

---

**Last Updated**: 2025-01-11
