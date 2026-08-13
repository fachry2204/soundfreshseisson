<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            'Apa itu Original Sessions?' => "Original Sessions adalah program submission lagu dari SoundFresh.id bekerja sama dengan D'MASIV. Singer-songwriter independen dapat mengirim lagu ciptaan sendiri, sejumlah lagu terpilih akan diproduksi ulang secara profesional bersama personel D'MASIV, lalu dirilis dan dipromosikan penuh lewat SoundFresh.id ke seluruh platform musik digital.",
            'Apa yang membedakan Original Sessions dari kompetisi musik lain?' => 'Original Sessions bukan sekadar kompetisi. Lagu yang terpilih benar-benar diproduksi ulang secara profesional (rekaman, aransemen, mixing, mastering), bukan hanya dinilai lalu selesai. Setelah produksi, lagu juga dirilis resmi dan dipromosikan, bukan hanya diberi sertifikat atau hadiah.',
            'Siapa yang boleh mendaftar?' => 'Warga Negara Indonesia berusia 17 tahun ke atas yang memiliki KTP, yang merupakan penyanyi sekaligus penulis lagu dari karya yang didaftarkan (singer-songwriter).',
            'Lagu saya sudah pernah diunggah sendiri ke Spotify secara mandiri, apakah masih bisa ikut?' => 'Lagu yang didaftarkan harus belum pernah dirilis secara resmi di platform digital mana pun. Jika lagu tersebut sudah pernah dirilis, tidak dapat didaftarkan lagi.',
            'Apa saja yang perlu disiapkan sebelum mendaftar?' => 'Rekaman video peserta menyanyikan lagu, teks lirik lengkap, tautan demo (misalnya dari Google Drive atau Dropbox), serta cerita singkat mengenai lagu dan proses di baliknya.',
            'Kapan periode submission dibuka dan ditutup?' => 'Submission dibuka tanggal 14 Agustus sampai 28 Agustus 2026. Jadwal submission juga diumumkan melalui microsite dan kanal sosial media resmi SoundFresh.id.',
            'Apa saja kriteria penilaiannya?' => 'Penilaian mempertimbangkan orisinalitas komposisi, kekuatan lirik dan penceritaan, kualitas vokal dan performance, serta potensi lagu tersebut di platform digital.',
            'Apakah saya harus datang langsung ke studio?' => 'Kalau peserta tidak dapat hadir untuk sesi rekaman, proses produksi bisa dilakukan secara online. Detail teknis mengenai lokasi dan jadwal akan dikoordinasikan oleh tim SoundFresh.id setelah pengumuman finalis.',
            'Apakah saya bisa memberi masukan soal aransemen final?' => "Ya, peserta dilibatkan dalam diskusi arah produksi bersama personel D'MASIV. Keputusan aransemen final tetap mempertimbangkan standar produksi dan arah kreatif yang disepakati bersama.",
            'Siapa pemilik hak cipta lagu setelah mengikuti program ini?' => 'Hak cipta atas komposisi lagu (lirik dan notasi) tetap menjadi milik peserta sebagai pencipta dan akan dikelola oleh SoundFresh Publishing. Ketentuan lebih rinci mengenai hak produksi, distribusi, dan pembagian terkait akan dituangkan dalam perjanjian kerja sama tertulis yang disepakati bersama peserta terpilih sebelum proses produksi dimulai.',
            'Apakah ada kontrak yang harus saya tanda tangani?' => 'Ya. Peserta yang lolos ke tahap produksi akan diminta menandatangani perjanjian kerja sama yang mengatur hak dan kewajiban masing-masing pihak. Perjanjian mencakup kerja sama produksi, distribusi master sebagai artis, dan kerja sama publishing sebagai penulis lagu. Kontrak harus disepakati sebelum proses produksi dimulai.',
        ];

        $newFaqs = [
            ['Format file apa yang diterima untuk demo?', 'Format video umum dari HP. File dapat diunggah ke penyimpanan cloud (Google Drive atau Dropbox) dan tautannya dicantumkan pada formulir pendaftaran.'],
            ['Apakah ada proses rekaman dokumentasi untuk program ini?', "Ya, proses produksi dan sesi bersama D'MASIV akan didokumentasikan sebagai konten behind-the-scenes. Konten yang dibuat menjadi collab post atau diunggah di akun peserta."],
            ['Apa saja benefit yang didapatkan dari program Original Sessions SoundFresh x D’MASIV?', 'Peserta akan mendapatkan berbagai benefit, mulai dari pengalaman produksi bersama personel D’MASIV, distribusi musik ke berbagai platform digital, dukungan promosi, hingga pelaporan streaming dan pendapatan yang transparan. Seluruh benefit program ini diberikan secara gratis.'],
            ['Apakah distribusi musik dilakukan melalui satu dashboard?', 'Ya. Peserta dapat melakukan proses distribusi melalui satu dashboard SoundFresh, sehingga proses pengelolaan rilisan menjadi lebih praktis dan terintegrasi.'],
            ['Apakah peserta akan mendapatkan dukungan promosi?', 'Ya. Peserta dan karya yang dihasilkan dalam program akan mendapatkan dukungan promosi melalui berbagai kanal, seperti playlist, sosial media, dan kanal media atau partner SoundFresh.'],
            ['Apakah program ini cocok untuk musisi yang baru memulai karier?', 'Ya. Program ini dirancang dengan proses onboarding yang sederhana, sehingga cocok untuk singer-songwriter maupun musisi yang baru memulai perjalanan karier di dunia rilisan digital.'],
            ['Apakah peserta dapat melihat laporan streaming dan pendapatan?', 'Ya. SoundFresh menyediakan pelaporan streaming dan pendapatan yang transparan bagi setiap musisi yang bergabung, sehingga peserta dapat mengetahui perkembangan performa karya mereka.'],
            ['Apa pengalaman khusus yang didapatkan peserta dalam program ini?', 'Peserta berkesempatan mendapatkan pengalaman produksi dan berkolaborasi langsung bersama personel D’MASIV, mulai dari proses pengembangan karya hingga tahap produksi.'],
            ['Apakah peserta dikenakan biaya untuk mengikuti program?', 'Tidak. Program Original Sessions SoundFresh x D’MASIV dapat diikuti secara gratis.'],
            ['Siapa yang cocok mengikuti program Original Sessions?', 'Program ini cocok bagi singer-songwriter, grup musik, dan musisi yang ingin mengembangkan karya, mendapatkan pengalaman produksi bersama D’MASIV, serta mulai memperkenalkan musik mereka melalui platform digital secara lebih profesional.'],
            ['Apa saja strategi promosi yang dilakukan untuk program Original Sessions SoundFresh x D’MASIV?', 'Promosi program dilakukan melalui beberapa kanal, yaitu sosial media, PR dan media partner, playlist placement, live showcase, konten eksklusif, serta email dan komunitas. Strategi ini dirancang untuk membangun awareness sejak tahap submission hingga lagu peserta dirilis.'],
            ['Bagaimana promosi dilakukan melalui sosial media?', 'Promosi dilakukan melalui Instagram Reels dan TikTok dengan konten yang menampilkan proses di balik layar (behind the scenes) selama sesi rekaman bersama D’MASIV. Konten ini bertujuan memberikan gambaran mengenai proses kreatif dan pengalaman peserta selama mengikuti Original Sessions.'],
            ['Apakah program ini akan mendapatkan liputan dari media?', 'Ya. SoundFresh akan mengupayakan kerja sama dengan media musik, radio, dan kanal media partner untuk mendukung publikasi program. Liputan dapat dilakukan sejak tahap submission, proses produksi, hingga perilisan lagu.'],
            ['Apakah lagu peserta akan mendapatkan dukungan playlist?', 'Ya. Lagu hasil Original Sessions akan mendapatkan kesempatan untuk diajukan dalam playlist editorial SoundFresh serta playlist dari partner DSP (Digital Streaming Platform). Penempatan playlist tetap mengikuti proses kurasi dan kebijakan masing-masing platform.'],
            ['Apakah akan ada acara khusus untuk peluncuran lagu?', 'Sebagai bagian dari puncak kampanye promosi, SoundFresh dapat menyelenggarakan live showcase, seperti mini-konser atau listening party. Kegiatan ini bertujuan memperkenalkan hasil karya peserta sekaligus memberikan pengalaman langsung kepada audiens.'],
            ['Apakah proses kolaborasi peserta dengan D’MASIV akan dibuat menjadi konten?', 'Ya. Akan dibuat konten eksklusif yang dapat berupa mini-dokumenter dan wawancara. Konten akan mengangkat cerita mengenai proses kreatif, pengalaman peserta, serta perjalanan kolaborasi bersama personel D’MASIV.'],
            ['Apakah semua peserta submission akan mendapatkan seluruh bentuk promosi tersebut?', 'Dukungan promosi dapat disesuaikan dengan tahapan program, materi yang tersedia, serta strategi kampanye SoundFresh. Bentuk promosi utama akan difokuskan pada peserta dan karya yang masuk ke tahap produksi hingga perilisan.'],
            ['Kapan promosi program mulai dilakukan?', 'Promosi dirancang berlangsung secara bertahap, dimulai dari fase submission, dilanjutkan dengan proses produksi dan kolaborasi bersama D’MASIV, hingga tahap perilisan dan kampanye lanjutan setelah lagu dirilis.'],
            ['Apa tujuan utama dari strategi promosi Original Sessions?', 'Strategi promosi bertujuan untuk meningkatkan awareness program, memperluas jangkauan karya peserta, membangun engagement komunitas SoundFresh, serta memberikan exposure yang lebih luas bagi peserta dan lagu yang dihasilkan melalui Original Sessions x D’MASIV.'],
        ];

        DB::transaction(function () use ($updates, $newFaqs): void {
            DB::table('faqs')->whereIn('question', [
                'Format file apa yang diterima untuk materi submission?',
                'Apakah saya mendapat dokumentasi dari proses produksi ini?',
            ])->update(['is_active' => false, 'updated_at' => now()]);

            foreach ($updates as $question => $answer) {
                DB::table('faqs')->where('question', $question)->update([
                    'answer' => $answer,
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            }

            foreach ($newFaqs as $offset => [$question, $answer]) {
                DB::table('faqs')->updateOrInsert(
                    ['question' => $question],
                    [
                        'answer' => $answer,
                        'sort_order' => 40 + $offset,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            }
        });
    }

    public function down(): void
    {
        // Konten FAQ tidak dihapus saat rollback agar perubahan admin tetap aman.
    }
};
