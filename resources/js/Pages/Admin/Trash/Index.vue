<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

defineOptions({ layout: AdminLayout });
const props = defineProps<{ files: any; filters: { search?: string }; total: number }>();
const search = ref(props.filters.search || "");
const deleting = ref<string | null>(null);
function filter() { router.get("/admin/trash", { search: search.value }, { preserveState: true, replace: true }); }
function permanentlyDelete(id: string) {
    if (!window.confirm("Hapus file video ini secara permanen? Tindakan ini tidak dapat dibatalkan.")) return;
    deleting.value = id;
    router.delete(`/admin/trash/files/${id}`, { preserveScroll: true, onFinish: () => (deleting.value = null) });
}
function dateTime(value?: string) {
    if (!value) return "-";
    return new Intl.DateTimeFormat("id-ID", { dateStyle: "medium", timeStyle: "short", timeZone: "Asia/Jakarta" }).format(new Date(value));
}
function fileSize(bytes: number) { return bytes >= 1048576 ? `${(bytes / 1048576).toFixed(1)} MB` : `${Math.ceil(bytes / 1024)} KB`; }
</script>

<template>
    <Head title="Data Terhapus" />
    <div class="trash-page">
        <header class="heading"><div><p>TEMPAT SAMPAH FILE</p><h1>Data Terhapus</h1><span>File video dari pendaftaran yang ditolak disimpan sementara di sini.</span></div><div class="total"><small>Total file</small><strong>{{ total }}</strong></div></header>
        <div class="info"><span>i</span><p><b>Data pendaftaran tetap aktif.</b> Hanya file video yang dipindahkan ke tempat sampah ketika status menjadi Ditolak. Admin dapat melihat pendaftarnya atau menghapus file secara permanen.</p></div>
        <form class="search" @submit.prevent="filter"><input v-model="search" placeholder="Cari file, nomor pendaftaran, nama, email, artis, atau lagu" /><button>Cari</button></form>
        <section class="archive-list">
            <article v-for="file in files.data" :key="file.id">
                <span class="trash-icon">▾</span>
                <div class="main"><div class="meta"><b>VIDEO DITOLAK</b><time>Dipindahkan {{ dateTime(file.trashed_at) }}</time></div><h2>{{ file.original_name }}</h2><p>{{ file.submission?.song?.title || "Tanpa judul" }} — {{ file.submission?.song?.artist_name || "-" }}</p><small>{{ file.submission?.registration_number }} · {{ file.submission?.applicant?.full_name || "-" }} · {{ fileSize(file.size) }}</small><div class="reason"><b>Alasan penolakan</b><span>{{ file.trash_reason || "Tidak ada alasan yang dicatat." }}</span></div></div>
                <div class="actions"><Link :href="`/admin/submissions/${file.submission_id}`">Lihat Pendaftar</Link><button :disabled="deleting === file.id" @click="permanentlyDelete(file.id)">{{ deleting === file.id ? "Menghapus…" : "Hapus Permanen" }}</button></div>
            </article>
            <div v-if="!files.data.length" class="empty"><span>⌫</span><h2>Belum ada file terhapus</h2><p>File video dari pendaftaran yang ditolak akan muncul di sini.</p></div>
        </section>
        <nav v-if="files.links?.length > 3" class="pagination"><Link v-for="link in files.links" :key="link.label" :href="link.url || '#'" :class="{ active: link.active, disabled: !link.url }" v-html="link.label" /></nav>
    </div>
</template>

<style scoped>
.trash-page{display:grid;gap:24px;color:#edf2f7}.heading{display:flex;align-items:flex-end;justify-content:space-between;gap:24px}.heading p{margin:0 0 8px;color:#ff7620;font-size:11px;font-weight:800;letter-spacing:.2em}.heading h1{margin:0;font-family:"Space Grotesk",sans-serif;font-size:40px}.heading div>span{display:block;margin-top:7px;color:#7f8da3}.total{min-width:130px;border:1px solid #3e2931;border-radius:17px;background:#21151c;padding:15px 20px}.total small,.total strong{display:block}.total small{color:#ad7a89;font-size:10px;text-transform:uppercase;letter-spacing:.12em}.total strong{margin-top:3px;color:#ff8150;font-size:28px}.info{display:flex;align-items:flex-start;gap:14px;border:1px solid #6d4125;border-radius:16px;background:#281a12;padding:16px 18px;color:#cbb6a7}.info>span{display:grid;flex:0 0 30px;height:30px;place-items:center;border-radius:50%;background:#ff6c18;color:#111;font-weight:900}.info p{margin:0;font-size:13px;line-height:1.6}.info b{color:#fff}.search{display:grid;grid-template-columns:1fr auto;gap:10px;border:1px solid #202b3f;border-radius:15px;background:#101827;padding:13px}.search input{min-height:46px;border:1px solid #2a3549;border-radius:10px;background:#0a1220;padding:0 14px;color:#fff}.search button{border-radius:10px;background:#ff6c18;padding:0 25px;color:#111;font-weight:800}.archive-list{display:grid;gap:12px}.archive-list article{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:17px;border:1px solid #392a35;border-radius:18px;background:#121824;padding:20px}.trash-icon{display:grid;width:46px;height:46px;place-items:center;border-radius:14px;background:#411d27;color:#ff7e85;font-size:20px}.meta{display:flex;align-items:center;gap:10px}.meta b{border-radius:999px;background:#451d25;padding:5px 9px;color:#ff858c;font-size:9px;letter-spacing:.1em}.meta time{color:#66738a;font-size:10px}.main h2{margin:10px 0 4px;font-size:18px}.main p{margin:0;color:#aab5c4;font-size:13px}.main>small{display:block;margin-top:5px;color:#6d7b91}.reason{display:flex;gap:8px;margin-top:12px;border-left:2px solid #ff6c18;padding-left:10px;font-size:11px}.reason b{color:#ff9160}.reason span{color:#a48f8f}.actions{display:grid;gap:8px}.actions a,.actions button{border:1px solid #4a3540;border-radius:10px;padding:10px 13px;text-align:center;color:#ff9c74;font-size:11px;font-weight:800}.actions button{border-color:#7b2d39;background:#3b1720;color:#ff828a}.actions button:disabled{opacity:.5}.empty{border:1px dashed #354055;border-radius:18px;padding:55px;text-align:center;color:#738198}.empty h2{margin:10px 0 3px;color:#e0e6ee}.empty p{margin:0}.pagination{display:flex;justify-content:center;gap:7px}.pagination a{border:1px solid #28354a;border-radius:9px;padding:8px 12px;color:#8492a7;font-size:12px}.pagination a.active{border-color:#ff6c18;background:#ff6c18;color:#111}.pagination a.disabled{pointer-events:none;opacity:.4}@media(max-width:760px){.heading{align-items:flex-start;flex-direction:column}.archive-list article{grid-template-columns:auto 1fr}.actions{grid-column:1/-1;grid-template-columns:1fr 1fr}.search{grid-template-columns:1fr}.search button{min-height:44px}.reason{flex-direction:column}}@media(max-width:480px){.archive-list article{grid-template-columns:1fr}.trash-icon{display:none}.heading h1{font-size:32px}.actions{grid-template-columns:1fr}}
</style>
