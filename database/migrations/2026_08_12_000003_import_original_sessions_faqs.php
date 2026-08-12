<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $faqs = [
            ['Apa itu Original Sessions?', "Original Sessions adalah program submission lagu gratis dari SoundFresh.id bekerja sama dengan D'MASIV. Singer-songwriter independen dapat mengirim lagu ciptaan sendiri; sejumlah lagu terpilih akan diproduksi ulang secara profesional bersama personel D'MASIV, lalu dirilis dan dipromosikan penuh lewat SoundFresh.id ke seluruh platform musik digital."],
            ['Siapa yang menyelenggarakan program ini?', "Program ini diselenggarakan oleh SoundFresh.id sebagai platform distribusi musik digital, bekerja sama dengan D'MASIV sebagai mitra produksi dan mentor bagi lagu-lagu terpilih."],
            ['Apa yang membedakan Original Sessions dari kompetisi musik lain?', 'Original Sessions bukan sekadar kompetisi—lagu yang terpilih benar-benar diproduksi ulang secara profesional melalui rekaman, aransemen, mixing, dan mastering. Setelah produksi, lagu juga dirilis resmi dan dipromosikan, bukan hanya diberi sertifikat atau hadiah.'],
            ['Apakah program ini benar-benar gratis?', 'Ya. Tidak ada biaya pendaftaran, biaya produksi, maupun biaya distribusi yang dibebankan kepada peserta, mulai dari submission hingga lagu dirilis.'],
            ['Siapa yang boleh mendaftar?', 'Warga Negara Indonesia berusia 17 tahun ke atas, yang merupakan penyanyi sekaligus penulis lagu dari karya yang didaftarkan (singer-songwriter).'],
            ['Saya belum genap 17 tahun, apakah masih bisa ikut?', 'Saat ini program ditujukan untuk peserta berusia 17 tahun ke atas. Jika ada penyesuaian syarat usia di kemudian hari, informasi akan diumumkan resmi melalui kanal SoundFresh.id.'],
            ['Apakah ada batasan genre musik?', 'Tidak ada batasan genre. Yang terpenting, lagu yang didaftarkan adalah karya orisinal milik peserta sendiri.'],
            ['Apakah lagu harus berbahasa Indonesia?', 'Tidak wajib. Lagu boleh menggunakan bahasa apa pun, selama merupakan karya orisinal peserta.'],
            ['Apakah saya bisa mendaftar sebagai duo atau band, atau harus solo?', 'Program ini difokuskan untuk singer-songwriter individu yang menulis dan menyanyikan lagunya sendiri. Jika lagu ditulis bersama pihak lain (co-writer), sertakan keterangan tersebut di formulir pendaftaran agar dapat ditinjau lebih lanjut oleh tim kurasi.'],
            ['Lagu saya sudah pernah diunggah sendiri ke Spotify secara mandiri, apakah masih bisa ikut?', 'Lagu yang didaftarkan harus belum pernah dirilis secara resmi di platform digital mana pun. Jika lagu tersebut sudah pernah dirilis, silakan daftarkan lagu lain yang belum pernah dirilis.'],
            ['Bagaimana cara mendaftar?', 'Pendaftaran dilakukan melalui microsite resmi Original Sessions. Peserta mengisi formulir, mengunggah atau menautkan demo lagu, lirik, dan sedikit cerita di balik lagu tersebut.'],
            ['Apa saja yang perlu disiapkan sebelum mendaftar?', 'Siapkan rekaman demo, teks lirik lengkap, tautan video penampilan dari Google Drive, Dropbox, atau platform penyimpanan lain yang didukung, serta cerita singkat mengenai lagu dan proses di baliknya.'],
            ['Apakah demo saya harus rekaman studio?', 'Tidak. Rekaman menggunakan ponsel atau home recording sudah cukup, selama vokal dan elemen lagunya terdengar jelas.'],
            ['Format file apa yang diterima untuk materi submission?', 'Materi video yang diunggah langsung harus menggunakan format video yang didukung, seperti MP4, MOV, atau WebM. Tautan materi dapat berasal dari Google Drive, Dropbox, atau layanan penyimpanan lain yang dapat diakses tim.'],
            ['Bolehkah saya mengirim lebih dari satu lagu?', 'Formulir pendaftaran ditujukan untuk satu lagu per pengiriman. Jika ingin mendaftarkan lebih dari satu lagu, silakan mengisi formulir secara terpisah untuk masing-masing lagu.'],
            ['Kapan periode submission dibuka dan ditutup?', 'Jadwal submission diumumkan melalui microsite dan kanal media sosial resmi SoundFresh.id. Pastikan mengikuti akun resmi agar tidak ketinggalan informasi pembukaan dan penutupan pendaftaran.'],
            ['Setelah mendaftar, apakah saya akan menerima konfirmasi?', 'Ya. Peserta akan menerima notifikasi bahwa pendaftaran telah diterima melalui email yang didaftarkan. Pastikan alamat email yang diisi aktif dan benar.'],
            ['Bagaimana proses kurasinya?', "Setiap submission yang masuk didengarkan dan disaring oleh tim A&R SoundFresh.id menjadi daftar shortlist. Shortlist tersebut kemudian dinilai bersama personel D'MASIV untuk menentukan lagu-lagu yang lanjut ke tahap produksi."],
            ['Apa saja kriteria penilaiannya?', 'Penilaian mempertimbangkan orisinalitas komposisi, kekuatan lirik dan penceritaan, kualitas vokal, serta potensi lagu tersebut di platform digital.'],
            ['Berapa lama proses seleksi berlangsung?', 'Estimasi proses seleksi berlangsung sekitar 6–7 minggu, dihitung sejak submission ditutup hingga pengumuman finalis.'],
            ['Bagaimana saya tahu kalau lagu saya terpilih?', "Peserta yang terpilih akan dihubungi langsung oleh tim SoundFresh.id melalui email atau kontak yang didaftarkan, untuk melanjutkan ke tahap produksi bersama D'MASIV."],
            ['Kalau saya tidak dihubungi, apakah artinya saya tidak lolos?', 'Karena jumlah submission yang masuk bisa sangat banyak, tim hanya akan menghubungi peserta yang lolos ke tahap berikutnya secara langsung. Pantau juga email dan media sosial resmi untuk informasi pengumuman umum.'],
            ['Apa yang terjadi kalau lagu saya terpilih?', "Lagu yang terpilih akan melalui proses produksi ulang secara profesional bersama personel D'MASIV—meliputi rekaman, aransemen, mixing, dan mastering—di studio rekaman profesional."],
            ['Apakah saya harus datang langsung ke studio?', 'Untuk sesi rekaman vokal dan proses produksi utama, peserta terpilih akan diundang untuk terlibat langsung. Detail lokasi dan jadwal akan dikoordinasikan oleh tim SoundFresh.id setelah pengumuman finalis.'],
            ['Apakah saya bisa memberi masukan soal aransemen final?', "Ya. Peserta dilibatkan dalam diskusi arah produksi bersama personel D'MASIV. Keputusan aransemen final tetap mempertimbangkan standar produksi dan kelayakan rilis di platform digital."],
            ['Berapa lama proses produksinya?', 'Estimasi proses produksi, mulai dari rekaman hingga mastering, berlangsung sekitar 4–6 minggu, tergantung kompleksitas aransemen masing-masing lagu.'],
            ['Apakah saya mendapat dokumentasi dari proses produksi ini?', "Ya. Proses produksi dan sesi bersama D'MASIV akan didokumentasikan sebagai konten behind-the-scenes, yang juga dapat digunakan peserta untuk portofolio pribadi."],
            ['Siapa pemilik hak cipta lagu setelah mengikuti program ini?', 'Hak cipta atas komposisi lagu, termasuk lirik dan musik, tetap menjadi milik peserta sebagai pencipta. Ketentuan lebih rinci mengenai hak produksi, distribusi, dan pembagian terkait akan dituangkan dalam perjanjian kerja sama tertulis sebelum proses produksi dimulai.'],
            ['Apakah saya tetap mendapat royalti dari lagu yang dirilis?', 'Peserta tetap berhak atas royalti dari lagu ciptaannya sesuai ketentuan dalam perjanjian kerja sama. Rincian skema royalti akan dijelaskan secara transparan kepada peserta terpilih sebelum penandatanganan perjanjian.'],
            ['Apakah ada kontrak yang harus saya tanda tangani?', 'Ya. Peserta yang lolos ke tahap produksi akan diminta menandatangani perjanjian kerja sama yang mengatur hak dan kewajiban masing-masing pihak, termasuk produksi, distribusi, dan royalti, sebelum proses produksi dimulai.'],
            ['Kalau lagu saya tidak terpilih, apakah saya masih bebas merilisnya sendiri?', 'Ya. Selama lagu tidak terpilih dan tidak masuk ke perjanjian kerja sama dengan SoundFresh.id, hak cipta sepenuhnya tetap di tangan peserta dan bebas dirilis sendiri kapan saja.'],
            ['Apakah demo yang saya kirim bisa digunakan pihak lain tanpa izin saya?', "Tidak. Materi yang dikirimkan hanya digunakan untuk keperluan kurasi internal tim SoundFresh.id dan D'MASIV, serta tidak akan digunakan atau didistribusikan tanpa persetujuan peserta."],
            ['Di platform apa saja lagu saya akan dirilis?', 'Lagu yang telah selesai diproduksi akan dirilis ke seluruh platform musik digital utama, termasuk Spotify, Apple Music, dan YouTube Music, melalui SoundFresh.id.'],
            ['Apakah nama saya akan dicantumkan sebagai artis?', 'Ya. Peserta dicantumkan sebagai artis utama pada lagu yang dirilis, sesuai dengan nama artis atau nama panggung yang didaftarkan.'],
            ['Bagaimana bentuk promosi yang akan saya dapatkan?', "Promosi mencakup kampanye di media sosial SoundFresh.id dan D'MASIV, kerja sama dengan media atau partner PR, pengajuan ke playlist editorial, serta kemungkinan sesi showcase atau listening party pada saat peluncuran."],
            ['Apakah saya juga perlu mempromosikan lagu saya sendiri?', 'Promosi utama menjadi tanggung jawab SoundFresh.id, namun peserta sangat dianjurkan turut membagikan lagunya melalui akun media sosial pribadi agar jangkauannya makin luas.'],
            ['Apakah ada batasan jumlah peserta yang bisa mendaftar?', 'Tidak ada batasan jumlah pendaftar. Program ini terbuka untuk seluruh singer-songwriter independen di Indonesia yang memenuhi syarat.'],
            ['Apakah saya bisa mendaftar lagi di gelombang berikutnya kalau kali ini tidak terpilih?', 'Bisa. Jika Original Sessions membuka gelombang submission berikutnya, peserta yang sebelumnya tidak terpilih tetap dapat mendaftar kembali dengan lagu yang sama atau lagu baru.'],
            ['Ke mana saya bertanya kalau ada hal yang belum terjawab di FAQ ini?', 'Silakan hubungi tim SoundFresh.id melalui kanal resmi atau microsite Original Sessions.'],
        ];

        DB::table('faqs')->delete();
        $now = now();
        DB::table('faqs')->insert(array_map(fn ($faq, $index) => [
            'question' => $faq[0],
            'answer' => $faq[1],
            'sort_order' => $index + 1,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], $faqs, array_keys($faqs)));
    }

    public function down(): void
    {
        DB::table('faqs')->delete();
    }
};
