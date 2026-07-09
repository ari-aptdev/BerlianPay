# BerlianPay — Sistem Pencatatan Pembayaran IPL Perumahan

MVP pencatatan pembayaran IPL (Iuran Pemeliharaan Lingkungan) perumahan. Pembayaran dicatat **manual** oleh admin (tanpa payment gateway/QRIS otomatis), lengkap dengan fitur reminder otomatis (email + WhatsApp opsional).

## Yang sudah ada

- Login satu form, redirect otomatis ke dashboard admin atau warga sesuai role
- Admin: CRUD data warga/rumah, CRUD tarif IPL, catat pembayaran + upload bukti transfer, kelola akun warga (buat/nonaktifkan/reset password), laporan export PDF & Excel
- Warga: dashboard status bulan ini, riwayat pembayaran (read-only, hanya rumah sendiri), download/cetak kwitansi, atur preferensi channel reminder
- Proteksi ketat: warga tidak bisa akses data rumah lain lewat manipulasi URL (`HousePolicy` & `PaymentPolicy`)
- Reminder otomatis H-3, hari-H, dan susulan tunggakan — lewat email (Laravel Mail) dan WhatsApp (stub siap diisi API Fonnte/Wablas/Twilio), dijalankan via Task Scheduling + Queue Job, dengan log anti-dobel-kirim

## ⚠️ Catatan penting sebelum mulai

Source code ini ditulis lengkap secara manual (bukan hasil `composer create-project`), karena environment tempat kode ini dibuat tidak punya akses ke Packagist untuk instalasi dependency. Artinya:

- Semua **logika aplikasi** (model, controller, policy, request, job, view) sudah lengkap dan siap pakai
- Beberapa **file skeleton bawaan Laravel** yang jarang diubah (`vendor/`, beberapa file di `public/`, `bootstrap/cache/*.php` hasil generate) belum ada dan akan otomatis dibuat saat kamu menjalankan `composer install` di komputer sendiri

## Cara menjalankan di lokal

### 1. Prasyarat
- PHP 8.2+
- Composer
- MySQL
- Node.js (opsional, hanya untuk asset build — UI di project ini pakai Tailwind CDN jadi sebenarnya tidak wajib)

### 2. Install dependency
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi database
Edit `.env`, sesuaikan:
```
DB_DATABASE=berlianpay
DB_USERNAME=root
DB_PASSWORD=
```
Buat database kosong bernama `berlianpay` di MySQL.

### 4. Migrasi & seed data dummy
```bash
php artisan migrate --seed
php artisan storage:link
```

Seeder akan membuat:
- **Admin**: `admin@berlianpay.test` / `password`
- **5 akun warga demo** (`andi@warga.test`, `siti@warga.test`, `budi@warga.test`, `dewi@warga.test`, `eko@warga.test`), semua password `password`
- 10 rumah, 3 tarif IPL, dan riwayat pembayaran 3 bulan terakhir dengan variasi status lunas/nunggak

### 5. Jalankan server
```bash
php artisan serve
```
Buka `http://localhost:8000`, login pakai akun di atas.

### 6. (Opsional) Jalankan queue worker untuk reminder
Reminder dikirim lewat queue supaya tidak memperlambat request. Jalankan di terminal terpisah:
```bash
php artisan queue:work
```

### 7. (Opsional) Aktifkan scheduler reminder harian
Reminder dicek otomatis tiap hari jam 08:00 (bisa diubah di halaman **Pengaturan → Reminder**). Di server produksi, tambahkan cron job:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
Untuk testing manual tanpa nunggu cron:
```bash
php artisan reminders:check
```

### 8. Aktifkan reminder WhatsApp (fase lanjutan, opsional)
Isi di `.env` setelah memilih provider (Fonnte/Wablas/Twilio):
```
WA_API_URL=https://api.fonnte.com/send
WA_API_KEY=isi_dengan_api_key_kamu
```
Lalu nyalakan toggle "Reminder via WhatsApp" di halaman **Pengaturan** admin.

## Struktur proyek

```
app/
  Models/            User, House, IplRate, Payment, ReminderLog, Setting
  Http/Controllers/
    Admin/           Dashboard, House, IplRate, Payment, Report, ResidentAccount, Setting, ReminderLog
    Resident/        Dashboard, Payment, Profile
    Auth/            AuthenticatedSessionController (login satu form, redirect per role)
  Http/Requests/     Validasi input per form
  Policies/          HousePolicy, PaymentPolicy — inti proteksi akses per role
  Jobs/              SendPaymentReminderJob (queue)
  Mail/              PaymentReminderMail
  Services/          WhatsAppService (stub provider WA)
  Console/Commands/  CheckPaymentReminders (dijalankan scheduler harian)
database/
  migrations/        Semua struktur tabel
  seeders/           Data dummy untuk demo ke client
resources/views/
  layouts/           admin.blade.php (sidebar), resident.blade.php (navbar mobile)
  admin/ & resident/ Semua halaman per role
routes/web.php       Semua route, dikelompokkan per role middleware
```

## Push ke GitHub sendiri

Setelah `composer install` berhasil dan aplikasi jalan normal di lokal:

```bash
git init
git add .
git commit -m "Initial commit - BerlianPay MVP"
git branch -M main
git remote add origin https://github.com/username-kamu/berlianpay.git
git push -u origin main
```

Ganti `username-kamu/berlianpay` dengan repo GitHub kamu sendiri (buat dulu repo kosong di github.com kalau belum ada).

## Roadmap lanjutan (belum termasuk di MVP ini)
- Integrasi payment gateway/QRIS otomatis
- Notifikasi push/in-app
- Multi-perumahan (saat ini diasumsikan 1 instance = 1 perumahan)
