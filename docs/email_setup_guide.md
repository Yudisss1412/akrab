## Panduan Setting Gmail SMTP untuk Reset Password

### 1. Aktifkan 2-Factor Authentication
- Buka Google Account (https://myaccount.google.com/)
- Pilih "Security"
- Aktifkan "2-Step Verification" jika belum aktif

### 2. Buat App Password
- Setelah 2-Factor Authentication aktif, di bagian "Security" pilih "2-Step Verification" lagi
- Cari "App passwords"
- Pilih "Mail" dan perangkat Anda (atau "Other/custom name")
- Gmail akan memberikan 16 karakter kode (App Password)
- Contoh format: `abcd efgh ijkl mnop` (tanpa spasi saat dimasukkan di .env)

### 3. Update File .env
Ganti file `.env` dengan pengaturan berikut (ganti dengan email dan App Password Anda):

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_yang_16_karakter
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="email_anda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

CATATAN: Gunakan App Password (16 karakter), BUKAN password Gmail biasa.

### 4. Clear Cache Config
Jalankan perintah:
```
php artisan config:clear
```

### 5. Uji Pengiriman Email
- Buka halaman `/password/reset`
- Masukkan email Anda
- Klik "Kirim Link Atur Ulang"
- Periksa folder inbox atau spam di email Anda

### Jika Masih Gagal
- Pastikan App Password sudah benar (16 karakter acak, tanpa spasi)
- Coba ganti MAIL_PORT menjadi 465 dan MAIL_ENCRYPTION menjadi ssl:
```
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```
- Pastikan Anda menggunakan akun Gmail pribadi, bukan akun Google Workspace dengan pembatasan tambahan