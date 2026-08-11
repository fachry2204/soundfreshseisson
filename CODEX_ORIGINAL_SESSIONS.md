# Master Prompt Codex — Sistem Pendaftaran Original Sessions

## 1. Peran dan Tujuan

Kamu adalah **Senior Full Stack Developer**. Bangun aplikasi web produksi bernama **Original Sessions — SoundFresh × D'Masiv**, sebuah microsite dan sistem pendaftaran lagu orisinal untuk singer-songwriter independen Indonesia.

Hasil akhir harus mencakup landing page publik, formulir pendaftaran, pelacakan status, dashboard admin, proses kurasi, notifikasi, pengaturan konten, audit log, dokumentasi instalasi, pengujian, dan deployment.

Gunakan copywriting pada bagian landing page di dokumen ini. Jangan mengubah nada bahasa tanpa persetujuan. Jika aset logo, foto, video, atau URL media sosial belum tersedia, gunakan placeholder yang jelas dan kelola melalui CMS.

## 2. Stack Wajib

- Backend: **Laravel 12+**, PHP 8.3+
- Frontend: **Vue 3** (bukan “Viu”), TypeScript, Composition API
- Adapter: Inertia.js agar Laravel dan Vue berada dalam satu aplikasi
- Database: **MySQL 8 / MariaDB 10.6+**
- UI: Tailwind CSS, Headless UI/Radix Vue, Lucide Icons
- State/form: Pinia jika benar-benar diperlukan, Inertia Form atau VueUse
- Queue/cache: Redis direkomendasikan; database queue sebagai fallback
- Mail: SMTP yang dapat diatur melalui dashboard admin
- Storage: Laravel Filesystem; local/private atau S3-compatible
- Testing: Pest/PHPUnit untuk backend dan Vitest untuk unit frontend; Playwright untuk alur utama
- Build: Vite
- Web server: Nginx atau Apache/Plesk

**Larangan:** jangan gunakan Prisma, SQLite untuk production, Next.js, atau penyimpanan data utama di browser.

## 3. Prinsip Implementasi

1. Gunakan arsitektur modular, service layer untuk logika bisnis penting, Form Request untuk validasi, Policy untuk otorisasi, dan job queue untuk notifikasi.
2. Jangan hard-code copy, periode pendaftaran, email tujuan, batas upload, URL sosial, atau status program. Simpan di database melalui menu pengaturan/CMS.
3. Semua dokumen pendaftar bersifat privat dan hanya boleh diakses melalui controller terotorisasi atau temporary signed URL.
4. Form harus mobile-first, accessible, cepat, dan tetap nyaman pada koneksi lambat.
5. Jangan menampilkan error teknis, stack trace, token, atau kredensial kepada pengguna.
6. Gunakan Bahasa Indonesia sebagai bahasa utama dan siapkan struktur i18n agar bisa ditambah bahasa lain.

## 4. Identitas Visual

Gunakan tampilan modern, premium, berani, dekat dengan dunia musik, tetapi tetap mudah dibaca.

### Palet

- Background utama: `#080808`
- Background sekunder: `#111111`
- Surface/card: `#181818`
- Master orange: `#FF6A00`
- Orange hover: `#FF8126`
- Orange gelap: `#B84900`
- Teks utama: `#F7F7F5`
- Teks sekunder: `#A9A9A5`
- Border: `#30302E`
- Success: `#22C55E`
- Warning: `#F59E0B`
- Error: `#EF4444`

Pastikan rasio kontras minimal WCAG AA. Orange digunakan sebagai aksen dan CTA, bukan untuk paragraf panjang.

### Tipografi dan Elemen

- Heading: Space Grotesk atau Sora
- Body: Inter
- Border radius: 14–20 px pada card, pill pada badge
- Gunakan waveform, timeline proses, track list, player bar, grain/noise halus, dan glow orange secukupnya
- Animasi ringan: waveform, hover card, reveal section, serta slider demo-versus-master
- Hormati `prefers-reduced-motion`
- Hindari template SaaS generik, neon berlebihan, autoplay audio, dan animasi berat

### Layout

- Maksimum lebar konten sekitar 1280 px; landing page tidak terasa full-width kosong
- Sticky header dengan wordmark, anchor navigation, dan CTA
- Breakpoint minimal: mobile, tablet, desktop
- CTA “Kirim Lagu” selalu mudah dijangkau, termasuk sticky bottom CTA opsional pada mobile

## 5. Landing Page Publik

### 5.1 Navigasi

- Wordmark: **Original Sessions**
- Anak judul: **SoundFresh × D'Masiv**
- Menu: Cara Kerja · Manfaat · Tentang · FAQ
- CTA: **Kirim Lagu**

Header sticky, transparan saat di atas, kemudian memakai blur/surface gelap saat scroll. Menu mobile menggunakan drawer yang accessible.

### 5.2 Hero

- Eyebrow: **SoundFresh × D'Masiv — Open submission gratis**
- H1: **Kesempatan agar lagu kamu siap didengar dunia.**
- Subjudul: **Original Sessions ngajak singer-songwriter independen buat kirim lagu ciptaan sendiri. Yang terpilih diproduksi langsung bareng personel D'Masiv, lalu dirilis & dipromosikan penuh lewat SoundFresh.id — dari awal sampai akhir, tanpa biaya.**
- CTA primer: **Kirim Lagu Kamu**
- CTA sekunder: **Lihat cara kerjanya ↓**
- Trust row: **Rp0 biaya submission · Diproduksi D'Masiv · Rilis ke semua platform**

Tambahkan komponen interaktif waveform untuk membandingkan demo dan master. Sediakan tombol play/pause, seek, label Demo/Master, volume, durasi, dan satu sumber audio aktif pada satu waktu. Audio tidak boleh autoplay.

### 5.3 Pain Points

- Eyebrow: **Buat kamu yang...**
- H2: **Udah punya lagu di kepala. Tinggal bingung lanjutnya gimana.**

Tampilkan mockup Voice Memos dan tiga card:

1. **Nulis emang jago. Produksi masih meraba-raba.**  
   Kamu tahu lagunya harus kedengeran gimana enaknya, tapi begitu masuk software rekaman, semua terasa asing.
2. **Alat udah lengkap, hasil masih jauh dari standar rilis.**  
   Interface, mic, plugin — semuanya ada. Tapi kok hasil akhirnya nggak semulus lagu-lagu favorit kamu.
3. **Udah rilis sendiri, tapi nggak ada yang dengar.**  
   Upload ke Spotify gampang. Yang berat: bikin orang lain nemuin lagumu di antara jutaan rilisan lain.

### 5.4 Cara Kerja

- Eyebrow: **Cara kerjanya**
- H2: **Dari demo di HP kamu, sampai rilis resmi. Lima langkah.**

1. **Kirim Demo** — Rekam video kamu nyanyiin lagu kamu + sedikit cerita di baliknya. Nggak perlu produksi bagus-bagus pakai AI, yang penting jujur.
2. **Dikurasi** — Tim A&R SoundFresh & personel D'Masiv dengerin satu per satu submission yang masuk.
3. **Diumumin** — Kalau lagumu kepilih, kami hubungi langsung buat lanjut ke tahap produksi.
4. **Produksi Bareng D'Masiv** — Workshop bareng D'Masiv, aransemen, mixing & mastering dengan standar internasional.
5. **Rilis & Dipromosiin** — Lagu resmi rilis ke semua platform musik digital & dipromosikan penuh sama SoundFresh.id.

### 5.5 Manfaat

- Eyebrow: **Kenapa ikut Original Sessions**
- H2: **Semua yang kamu butuh buat naik level, ada di satu program.**

Tampilkan enam benefit card:

1. **Produksi Profesional, Gratis** — Rekaman, aransemen, mixing & mastering bareng D'Masiv, tanpa biaya sepeser pun.
2. **Rilis ke Semua Platform** — Lagu kamu tayang di Spotify, Apple Music, YouTube Music, dan platform lain lewat SoundFresh.id.
3. **Mentoring Langsung** — Belajar langsung dari musisi yang udah lebih dulu ada di posisi kamu sekarang.
4. **Dipromosikan Serius** — Kampanye promosi lintas kanal — bukan cuma diunggah terus didoain viral sendiri.
5. **Konten Buat Portofolio** — Dokumentasi proses produksi & behind-the-scenes yang bisa kamu pakai sendiri nanti.
6. **Masuk Circle yang Tepat** — Kenalan sama tim A&R, produser, dan sesama singer-songwriter independen lainnya.

### 5.6 Tentang Penyelenggara

- Eyebrow: **Siapa di balik ini**
- H2: **Dua nama yang saling melengkapi.**

Card D'Masiv:

- Label: **Mitra Produksi**
- Body: **Salah satu band rock paling berpengaruh di Indonesia, dengan jam terbang menulis lagu, mengaransemen, dan memproduksi karya yang diterima luas oleh publik. Di Original Sessions, mereka turun langsung sebagai produser & mentor bagi lagu-lagu terpilih.**

Card SoundFresh.id:

- Label: **Distribusi & Promosi**
- Body: **Platform yang membantu musisi independen merilis karya ke seluruh platform streaming musik utama — cepat, transparan, tanpa hambatan teknis. Original Sessions adalah cara kami membuka jalan produksi yang selama ini sulit dijangkau musisi indie.**

### 5.7 Syarat

- Eyebrow: **Syarat ikutan**
- H2: **Cek dulu sebelum kirim.**

1. Warga Negara Indonesia dan punya KTP.
2. Lagu orisinal (lirik & musik), belum pernah dirilis resmi di platform mana pun.
3. Pendaftar adalah penyanyi sekaligus penulis lagunya sendiri.
4. Submission gratis — tidak ada biaya tersembunyi.

### 5.8 FAQ

- Eyebrow: **Yang sering ditanyain**
- H2: **FAQ**

1. **Beneran gratis?** Ya. Dari submission sampai rilis, kamu nggak dikenain biaya apa pun.
2. **Lagu saya harus genre tertentu, nggak?** Nggak ada batasan genre. Yang penting orisinal dan itu benar-benar karya kamu sendiri.
3. **Kalau nggak kepilih, lagu saya gimana?** Hak cipta tetap sepenuhnya milik kamu. Kamu bebas merilisnya sendiri kapan saja.
4. **Berapa lama proses seleksinya?** Estimasi sekitar 6–7 minggu dari penutupan submission sampai pengumuman finalis.
5. **Musik dan videonya harus bagus nggak?** Nggak. Rekaman HP dengan suara sendiri aja udah cukup — yang penting vokal & lagunya kedengeran jelas.

FAQ harus berasal dari CMS, dapat diurutkan, diaktifkan/nonaktifkan, dan memakai accordion accessible.

### 5.9 CTA Akhir

- Eyebrow: **Gelombang pendaftaran dibuka sekarang**
- H2: **Lagu kamu udah nunggu buat didengar lebih banyak orang.**
- Body: **Isi form di samping, kasih tautan demo kamu, dan biarkan kami yang urus sisanya. Tim SoundFresh akan dengerin setiap submission yang masuk.**
- CTA: **Kirim Demo Sekarang**

### 5.10 Footer

- Wordmark: **Original Sessions — SoundFresh × D'Masiv**
- Program: Cara Kerja · Manfaat · Syarat & FAQ
- Terhubung: Instagram · TikTok · hello@soundfresh.id
- Copyright: **© 2026 SoundFresh.id — sebuah kolaborasi bersama D'Masiv**

URL Instagram dan TikTok dikelola dari pengaturan dan wajib diganti dengan akun resmi sebelum production.

## 6. Alur Pendaftaran

Buat formulir multi-step dengan autosave draft di server setelah email dan nomor WhatsApp diverifikasi atau minimal setelah draft token dibuat. Tampilkan progress, validasi per langkah, ringkasan sebelum submit, serta opsi kembali tanpa kehilangan data.

### Langkah 1 — Data Diri

- Nama lengkap sesuai KTP
- Nama panggung (opsional)
- NIK, 16 digit, terenkripsi saat disimpan dan dimasking di UI/log
- Tempat lahir
- Tanggal lahir
- Jenis kelamin (opsional jika tidak dibutuhkan secara legal/operasional)
- Email
- Nomor WhatsApp dengan normalisasi format Indonesia (`62...`)
- Provinsi dan kota/kabupaten
- Alamat lengkap
- Akun Instagram/TikTok (opsional)
- Upload KTP: JPG/JPEG/PNG/PDF, private, maksimum dapat diatur admin

### Langkah 2 — Data Lagu

- Judul lagu
- Genre utama dan subgenre opsional
- Bahasa lagu
- Durasi perkiraan
- Tahun penciptaan
- Cerita singkat mengenai lagu, proses, dan pesan yang ingin disampaikan
- Pernyataan bahwa pendaftar adalah penyanyi dan penulis lagu
- Pernyataan lagu belum pernah dirilis resmi
- Apakah ada co-writer; jika ya, tambahkan daftar nama legal, peran, pembagian hak, email/telepon, dan surat persetujuan
- Lirik lagu (textarea atau unggah PDF)

### Langkah 3 — Demo dan Video

- Tautan demo Google Drive/Dropbox/YouTube unlisted atau upload audio opsional sesuai pengaturan admin
- Tautan video penampilan/cerita atau upload video opsional sesuai pengaturan admin
- Validasi URL dan petunjuk agar link dapat diakses tim kurasi
- Format audio: MP3/WAV/M4A; format video: MP4/MOV; batas ukuran dan durasi diatur admin
- Gunakan chunked upload untuk file besar; progres upload, retry, cancel, checksum, dan cleanup upload gagal/kedaluwarsa

### Langkah 4 — Deklarasi dan Persetujuan

- Checkbox karya orisinal dan tidak melanggar hak pihak lain
- Checkbox seluruh co-writer/pemilik hak telah setuju
- Checkbox data benar
- Persetujuan syarat dan ketentuan serta kebijakan privasi, dengan versi dokumen yang dicatat
- Persetujuan menerima komunikasi terkait program
- CAPTCHA/Turnstile
- Tanda tangan elektronik sederhana: nama lengkap + timestamp + IP hash + user agent

### Langkah 5 — Review dan Submit

- Ringkasan seluruh data
- Tombol edit per bagian
- Status kelengkapan file/link
- Tombol **Kirim Demo Sekarang** dengan idempotency key agar double-click tidak membuat data ganda
- Setelah sukses tampilkan:
  - Judul: **Demo kamu udah masuk 🎧**
  - Body: **Tim kami bakal dengerin submission kamu. Kalau kepilih lanjut ke tahap produksi, kami hubungi lewat email yang kamu kasih.**
  - Nomor pendaftaran unik
  - Tombol simpan/cetak bukti pendaftaran PDF

## 7. Akun Pendaftar dan Pelacakan Status

Pendaftar tidak wajib membuat password di awal. Setelah submit, kirim magic link aman melalui email untuk membuka portal pendaftar. Admin dapat mengaktifkan mode akun-password bila diperlukan.

Portal pendaftar menampilkan:

- Nomor dan tanggal pendaftaran
- Data ringkas lagu
- Timeline status yang aman untuk publik
- Permintaan revisi beserta batas waktunya
- Form unggah ulang hanya untuk field yang diminta
- Riwayat pesan/notifikasi
- Bukti pendaftaran PDF
- Tombol pembatalan pendaftaran sebelum kurasi dimulai, jika diizinkan admin

Status internal jangan seluruhnya ditampilkan mentah kepada pendaftar. Mapping status publik:

- `draft` → Draft
- `submitted` / `administrative_review` → Pendaftaran Diterima
- `revision_requested` → Perlu Perbaikan
- `eligible` / `curation` / `shortlisted` → Dalam Proses Seleksi
- `selected` → Terpilih
- `not_selected` → Belum Terpilih
- `withdrawn` → Dibatalkan
- `disqualified` → Tidak Memenuhi Syarat

## 8. Dashboard Admin

### 8.1 Role dan Hak Akses

Gunakan RBAC dengan Laravel Policy/Gate. Role awal:

- Super Admin: seluruh akses dan pengaturan
- Admin Program: operasional pendaftaran dan laporan
- A&R/Curator: melihat submission yang ditugaskan dan memberi penilaian
- Reviewer Administrasi: verifikasi identitas, syarat, dan kelengkapan
- Content Editor: landing page, FAQ, aset, periode
- Viewer/Management: dashboard dan laporan read-only

Sediakan manajemen user, role, permission, invite user, reset password, revoke session, dan optional 2FA untuk admin. Akses file KTP diberikan hanya kepada role yang memerlukan.

### 8.2 Dashboard Ringkasan

- Total draft, submitted, perlu revisi, eligible, dalam kurasi, shortlisted, selected, rejected, withdrawn
- Pendaftaran hari ini/minggu ini/per periode
- Distribusi genre dan domisili
- Rasio kelengkapan dan conversion landing-to-submit bila analytics diaktifkan
- Aktivitas terbaru dan antrean notifikasi gagal
- Filter berdasarkan periode program

### 8.3 Manajemen Submission

- Tabel server-side: nomor, nama, judul lagu, genre, kota, tanggal submit, assignee, skor, status
- Search, filter, sorting, pagination, saved view, export CSV/XLSX sesuai permission
- Detail pendaftar, preview file private, cek link demo, log perubahan, dan catatan internal
- Assign reviewer secara manual atau round-robin
- Bulk action terbatas dengan confirmation dan audit log
- Minta revisi per field dengan catatan dan deadline
- Ubah status sesuai state machine; larang transisi ilegal
- Email/WhatsApp individual atau template massal setelah preview dan konfirmasi

### 8.4 Sistem Kurasi

Rubrik dan bobot dapat diatur per periode. Default:

- Orisinalitas karya: 30%
- Kekuatan songwriting/lirik: 25%
- Melodi dan karakter vokal: 20%
- Potensi pengembangan produksi: 15%
- Kesesuaian program: 10%

Fitur:

- Skor 1–10 per kriteria dan komentar
- Draft/final score
- Blind review opsional dengan menyembunyikan identitas
- Minimal jumlah reviewer per submission
- Cegah reviewer menilai submission yang memiliki konflik kepentingan
- Skor agregat, deviasi antar reviewer, ranking, shortlist, dan keputusan final
- Keputusan final tidak otomatis hanya berdasarkan ranking; wajib ada alasan dan aktor penentu

### 8.5 CMS dan Pengaturan

- Semua section landing page, CTA, FAQ, benefit, cara kerja, partner, footer
- Media library untuk logo, foto, waveform demo/master, og:image, favicon
- Preview draft sebelum publish dan revision history sederhana
- Periode: nama gelombang, waktu buka/tutup timezone Asia/Jakarta, kuota opsional, status open/closed/coming soon
- Ketika tutup: form tidak menerima submit baru tetapi draft dapat ditangani sesuai aturan
- SMTP, nama pengirim, reply-to, email admin
- WhatsApp gateway bersifat adapter/configurable; kredensial dienkripsi
- Storage, upload limits, allowed MIME, retention policy
- Template email/WhatsApp dengan variable whitelist
- SEO, analytics consent, social links, legal pages
- Maintenance mode khusus form tanpa mematikan landing page

### 8.6 Laporan

- Laporan per periode, status, genre, provinsi/kota, sumber referral, reviewer, dan hasil seleksi
- Export CSV/XLSX dan PDF ringkasan
- PII sensitif tidak ikut export secara default
- Semua export dicatat dalam audit log dan file export memiliki masa kedaluwarsa

## 9. Notifikasi

Gunakan queued jobs dengan retry, exponential backoff, failure log, dan idempotency. Template minimal:

- Draft/magic link
- Pendaftaran berhasil
- Permintaan revisi
- Revisi diterima
- Lolos administrasi
- Masuk shortlist
- Terpilih
- Belum terpilih
- Pendaftaran dibatalkan
- Pengingat deadline revisi

Email wajib. WhatsApp opsional melalui adapter gateway yang dapat diganti. Jangan blok proses submit ketika gateway notifikasi sedang gagal. Admin harus dapat retry notifikasi gagal.

## 10. Model Data Minimum

Gunakan migration, foreign key, index, soft delete hanya jika masuk akal, serta ULID/UUID untuk identifier yang terekspos publik.

- `users`
- `roles`, `permissions`, tabel pivot RBAC
- `program_periods`
- `applicants`
- `submissions`
- `songs`
- `song_contributors`
- `submission_files`
- `submission_links`
- `consents`
- `status_histories`
- `revision_requests`
- `review_assignments`
- `review_criteria`
- `review_scores`
- `review_decisions`
- `internal_notes`
- `notifications` dan `notification_deliveries`
- `content_sections`
- `faqs`
- `media_assets`
- `settings`
- `audit_logs`
- `personal_access_tokens` bila diperlukan
- `jobs`, `failed_jobs`

Constraint penting:

- Nomor pendaftaran unik, contoh `OS-2026-000001`
- Satu lagu tidak boleh tersubmit dua kali pada periode yang sama oleh pendaftar yang sama; admin dapat override dengan alasan
- Email dan WhatsApp dinormalisasi dan di-index dengan aman
- NIK disimpan encrypted; untuk deduplikasi gunakan blind index/hash terpisah, jangan index ciphertext
- File menyimpan disk, path, MIME hasil server-side detection, size, checksum, original name yang disanitasi, dan status scan
- `settings` tidak boleh mengembalikan secrets melalui endpoint frontend
- Riwayat status immutable; koreksi dilakukan dengan event baru

## 11. API dan Route

Gunakan web routes Inertia untuk UI dan endpoint JSON terpisah bila dibutuhkan upload/async. Kelompok minimum:

### Public

- `GET /`
- `GET /program/{period:slug}`
- `GET /daftar`
- `POST /registration/drafts`
- `PATCH /registration/drafts/{token}`
- `POST /registration/drafts/{token}/files/init`
- `POST /registration/drafts/{token}/files/chunk`
- `POST /registration/drafts/{token}/files/complete`
- `DELETE /registration/drafts/{token}/files/{file}`
- `POST /registration/drafts/{token}/submit`
- `GET /pendaftaran/berhasil/{publicId}` melalui signed URL
- `GET /tracking/{registrationNumber}` atau magic link
- `GET /legal/terms`, `GET /legal/privacy`

### Admin

- `/admin/dashboard`
- `/admin/submissions`
- `/admin/submissions/{submission}`
- `/admin/submissions/{submission}/status`
- `/admin/submissions/{submission}/revision-requests`
- `/admin/submissions/{submission}/assignments`
- `/admin/reviews`
- `/admin/periods`
- `/admin/content`
- `/admin/faqs`
- `/admin/reports`
- `/admin/users`, `/admin/roles`
- `/admin/settings/*`
- `/admin/audit-logs`

Gunakan route model binding, authorization pada setiap action, rate limit khusus form/login/magic link, CSRF, dan signed URL yang cepat kedaluwarsa.

## 12. Keamanan dan Privasi

- OWASP baseline: CSRF, XSS escaping, SQL injection protection, SSRF protection saat memeriksa URL demo, secure headers, rate limiting, dan brute-force protection
- Validasi MIME berdasarkan isi file, bukan extension saja
- Jangan mengeksekusi file upload; randomize storage filename; blok SVG/HTML/executable
- Integrasikan antivirus async (misalnya ClamAV) dan karantina file sampai scan selesai
- Enkripsi NIK, credential gateway, dan setting rahasia memakai Laravel encrypted casts atau secret manager
- Password admin menggunakan Argon2id/bcrypt; session cookie `Secure`, `HttpOnly`, `SameSite=Lax/Strict`
- Redact PII dari log, error monitoring, analytics, export, dan notification payload
- Audit create/read-download/update/status/export untuk data/file sensitif
- Backup database dan storage terenkripsi serta uji restore
- Retention policy: admin dapat menentukan kapan draft, file gagal, KTP, dan submission tidak terpilih dihapus/anonymize sesuai kebijakan
- Tampilkan kebijakan privasi dan dasar persetujuan yang jelas

## 13. SEO, Performa, dan Aksesibilitas

- SSR melalui Inertia SSR bila memungkinkan; metadata dan landing content harus dapat diindeks
- Title, description, canonical, Open Graph, Twitter card, sitemap.xml, robots.txt, schema.org FAQPage dan Organization
- Target Lighthouse mobile: Performance ≥ 85, Accessibility ≥ 95, Best Practices ≥ 90, SEO ≥ 95 pada kondisi produksi realistis
- Lazy-load media, responsive images WebP/AVIF, preload font kritis, self-host font jika memungkinkan
- Keyboard navigation penuh, visible focus, label/error form terkait melalui ARIA, skip link, semantic heading, dan live region untuk upload/progress
- Jangan hanya mengandalkan warna untuk status

## 14. State Machine Submission

Transisi default:

```text
draft -> submitted -> administrative_review
administrative_review -> revision_requested -> administrative_review
administrative_review -> eligible -> curation -> shortlisted
shortlisted -> selected | not_selected
submitted|administrative_review|eligible|curation|shortlisted -> withdrawn
administrative_review|curation -> disqualified
```

Setiap transisi mencatat aktor, waktu, alasan, status lama/baru, dan notifikasi yang dijadwalkan. Implementasikan dalam service/action khusus dan test seluruh transisi.

## 15. Validasi Penting

- Pendaftar harus memenuhi aturan kewarganegaraan dan KTP
- Tanggal lahir valid dan batas usia, jika ada, berasal dari setting periode
- Judul/cerita/lirik memiliki panjang minimum dan maksimum
- Link demo/video harus HTTPS, host allowlist atau pemeriksaan SSRF-safe; jangan fetch alamat private/local
- KTP wajib sebelum submit
- Demo dan video mengikuti pilihan mode upload/link pada setting
- Pernyataan hak dan versi consent wajib lengkap
- Pendaftaran hanya bisa disubmit saat periode aktif, kecuali admin override tercatat
- Submission final bersifat snapshot; revisi tidak boleh menghapus riwayat sebelumnya

## 16. Testing dan Acceptance Criteria

### Automated

- Feature test auth, RBAC, submit, duplicate prevention, period closure, signed URL, private file access, state transition, review scoring, export permission, notification retry
- Unit test nomor pendaftaran, phone normalization, score calculation, blind index, template variables
- Frontend test multi-step form, validation, upload retry, accordion, audio player, reduced motion
- E2E: pengunjung membuka landing → membuat draft → unggah KTP/demo → submit → menerima bukti; admin review → minta revisi → pendaftar revisi → curator menilai → keputusan

### Acceptance

- Tidak ada Prisma atau SQLite production
- Landing page cocok dengan copy dan visual hitam–oranye
- Semua section responsif dari 360 px hingga desktop lebar
- Pendaftaran ganda akibat refresh/double-click tidak terjadi
- File privat tidak dapat diakses tanpa otorisasi/signed URL
- Role curator tidak bisa mengubah setting atau melihat KTP jika tidak diberi permission
- Periode tutup menolak submission baru secara atomik
- Kegagalan SMTP/WA tidak menggagalkan penyimpanan submission
- Seluruh perubahan status dan export tercatat
- `php artisan test`, frontend tests, lint, typecheck, dan build lulus

## 17. Struktur Kode yang Disarankan

```text
app/
  Actions/Submission/
  Enums/
  Http/Controllers/Public/
  Http/Controllers/Admin/
  Http/Requests/
  Jobs/Notifications/
  Models/
  Notifications/
  Policies/
  Services/Files/
  Services/Reviews/
  Services/Submission/
resources/js/
  Components/
  Composables/
  Layouts/
  Pages/Public/
  Pages/Applicant/
  Pages/Admin/
  Types/
routes/
  web.php
  admin.php
tests/
  Feature/
  Unit/
  Browser/
```

## 18. Instalasi dan Deployment

Sediakan:

- `.env.example` tanpa secret asli
- README lengkap untuk local, queue worker, scheduler, storage link/private storage, Redis, mail, antivirus, build frontend, dan production
- Migration dan seeder role/permission, admin awal via command interaktif, content default landing page, FAQ, rubrik, dan periode contoh
- Docker Compose untuk development opsional, tetapi instalasi non-Docker tetap didokumentasikan
- Supervisor/systemd config contoh untuk queue worker
- Cron `php artisan schedule:run`
- Health endpoint yang tidak membocorkan detail
- Deployment checklist: backup, migrate `--force`, cache config/routes/views, build assets, restart workers, smoke test, rollback plan

## 19. Urutan Pengerjaan untuk Codex

1. Audit repository dan baca `AGENTS.md`, README, serta konfigurasi yang ada. Jangan menimpa perubahan pengguna.
2. Tulis rencana implementasi dan daftar asumsi singkat.
3. Bootstrap Laravel + Vue 3 + Inertia + TypeScript + MySQL bila repository masih kosong.
4. Bangun schema, model, enum, policy, seeder, dan state machine.
5. Bangun CMS content dan landing page sesuai copy.
6. Bangun draft registration, upload privat/chunked, submit idempotent, magic link, bukti PDF.
7. Bangun admin, review, status, notifications, reports, settings, dan audit.
8. Tambahkan security hardening, accessibility, SEO, performance optimization.
9. Tulis tests dan jalankan formatter, static analysis, typecheck, tests, build, serta smoke test browser.
10. Perbaiki semua error yang berada dalam scope. Laporkan hasil tes, file utama yang berubah, asumsi, dan pekerjaan lanjutan yang benar-benar belum dapat diselesaikan.

## 20. Definition of Done

Proyek selesai ketika aplikasi dapat dipasang dari README pada MySQL kosong; landing page tampil sesuai identitas dan copy; pendaftar dapat mengirim karya dengan aman; admin dapat mengelola periode, submission, kurasi, notifikasi, konten, user, serta laporan; semua file sensitif privat; audit tersedia; dan seluruh quality gate yang relevan lulus.

