# Original Sessions — SoundFresh × D'MASIV

Microsite dan fondasi sistem pendaftaran lagu orisinal berbasis Laravel 12, Inertia, Vue 3, TypeScript, Tailwind, dan MySQL.

## Kebutuhan

- PHP 8.3+ dengan `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, dan `intl`
- Composer 2, Node.js 20+, npm 10+
- MySQL 8 / MariaDB 10.6+
- Redis direkomendasikan untuk queue/cache; database queue dapat digunakan sebagai fallback

## Instalasi lokal

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Buat database `original_sessions`, sesuaikan kredensial `.env`, lalu:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
php artisan queue:work --tries=3 --backoff=10,60,300
```

Admin seed lokal: `admin@originalsessions.test` / `ChangeMe123!`. Ganti password segera dan jangan gunakan akun seed di production.

## Storage, queue, dan scheduler

Dokumen sensitif harus disimpan di disk private (`storage/app/private`) atau bucket S3 private. Jangan membuat storage link untuk KTP/demo. Jalankan worker queue sebagai service dan cron berikut setiap menit:

```cron
* * * * * cd /var/www/original-sessions && php artisan schedule:run >> /dev/null 2>&1
```

Contoh Supervisor:

```ini
[program:original-sessions-worker]
command=php /var/www/original-sessions/artisan queue:work --sleep=3 --tries=3 --backoff=10,60,300
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/original-sessions-worker.log
```

## Pemeriksaan kualitas

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

Test menggunakan SQLite in-memory hanya sebagai database terisolasi; production wajib MySQL/MariaDB.

## Deployment

1. Backup database dan private storage; pastikan restore terakhir pernah diuji.
2. Aktifkan maintenance khusus form/program bila perlu.
3. Deploy release, jalankan `composer install --no-dev --optimize-autoloader` dan `npm ci && npm run build`.
4. Jalankan `php artisan migrate --force`, `php artisan optimize`, lalu `php artisan queue:restart`.
5. Smoke test `/up`, landing, submit, signed URL, login admin, queue email, dan akses file private.
6. Bila gagal, rollback release dan database sesuai runbook backup—jangan rollback migration destruktif secara otomatis.

## Catatan keamanan

NIK memakai encrypted cast dan blind index HMAC. URL bukti pendaftaran bertanda tangan dan kedaluwarsa. Consent menyimpan versi dokumen, waktu, IP hash, dan user agent. Sebelum production, konfigurasi Turnstile, SMTP, antivirus/ClamAV, secret manager, CSP/security headers, backup terenkripsi, retention policy, dan akun sosial resmi.

Portal pendaftar memakai magic link signed berumur 30 menit dengan respons yang identik agar nomor/email tidak dapat dienumerasi. KTP disimpan pada disk private, diberi nama acak, MIME dideteksi server-side, checksum dicatat, dan hanya role administratif yang dapat mengunduh. Status scan awal tetap `pending` sampai worker antivirus diaktifkan.

Upload demo/video besar menggunakan chunk 2 MB dengan capability token yang hanya dikirim ke browser pemilik sesi. Server memvalidasi urutan, ukuran, MIME berdasarkan isi, ukuran akhir, dan SHA-256 sebelum file dapat diklaim oleh submission. Jalankan scheduler agar `uploads:cleanup` membersihkan sesi kedaluwarsa setiap jam.
