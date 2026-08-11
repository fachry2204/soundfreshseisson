<?php

namespace Database\Seeders;

use App\Models\ContentSection;
use App\Models\Faq;
use App\Models\ProgramPeriod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@originalsessions.test']);
        $admin->fill(['name' => 'Super Admin', 'username' => 'fachry', 'role' => 'super_admin']);
        $admin->email_verified_at = now();
        if (! $admin->exists) {
            $admin->password = Hash::make(env('ADMIN_PASSWORD', 'ChangeMe123!'));
        }
        $admin->save();

        $curator = User::firstOrNew(['email' => 'curator@originalsessions.test']);
        $curator->fill(['name' => 'Kurator Demo', 'username' => 'curator', 'role' => 'curator']);
        $curator->email_verified_at = now();
        if (! $curator->exists) {
            $curator->password = Hash::make('ChangeMe123!');
        }
        $curator->save();
        $period = ProgramPeriod::updateOrCreate(['slug' => 'original-sessions-2026'], ['name' => 'Original Sessions 2026', 'opens_at' => now()->subDay(), 'closes_at' => now()->addMonths(2), 'timezone' => 'Asia/Jakarta', 'status' => 'open', 'settings' => ['max_upload_mb' => 250]]);
        foreach ([
            ['Beneran gratis?', 'Ya. Dari submission sampai rilis, kamu nggak dikenain biaya apa pun.'],
            ['Lagu saya harus genre tertentu, nggak?', 'Nggak ada batasan genre. Yang penting orisinal dan itu benar-benar karya kamu sendiri.'],
            ['Kalau nggak kepilih, lagu saya gimana?', 'Hak cipta tetap sepenuhnya milik kamu. Kamu bebas merilisnya sendiri kapan saja.'],
            ['Berapa lama proses seleksinya?', 'Estimasi sekitar 6–7 minggu dari penutupan submission sampai pengumuman finalis.'],
            ['Musik dan videonya harus bagus nggak?', 'Nggak. Rekaman HP dengan suara sendiri aja udah cukup — yang penting vokal & lagunya kedengeran jelas.'],
        ] as $i => $faq) {
            Faq::updateOrCreate(['question' => $faq[0]], ['answer' => $faq[1], 'sort_order' => $i, 'is_active' => true]);
        }
        foreach ([['Orisinalitas karya', 30], ['Kekuatan songwriting/lirik', 25], ['Melodi dan karakter vokal', 20], ['Potensi pengembangan produksi', 15], ['Kesesuaian program', 10]] as $i => $criterion) {
            DB::table('review_criteria')->updateOrInsert(['program_period_id' => $period->id, 'name' => $criterion[0]], ['weight' => $criterion[1], 'sort_order' => $i, 'created_at' => now(), 'updated_at' => now()]);
        }
        ContentSection::updateOrCreate(['key' => 'legal_terms'], ['content' => ['title' => 'Syarat dan Ketentuan', 'body' => 'Dokumen ini adalah placeholder yang wajib ditinjau penasihat hukum sebelum program dibuka untuk publik.'], 'revision' => 1, 'is_published' => true]);
        ContentSection::updateOrCreate(['key' => 'legal_privacy'], ['content' => ['title' => 'Kebijakan Privasi', 'body' => 'Data pendaftar digunakan untuk administrasi, kurasi, komunikasi, dan pelaksanaan Original Sessions. Detail retensi dan dasar pemrosesan wajib disahkan sebelum production.'], 'revision' => 1, 'is_published' => true]);
    }
}
