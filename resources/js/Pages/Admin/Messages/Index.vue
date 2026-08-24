<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    messages: any;
    counts: { sent: number; pending: number; failed: number };
    filters: { status?: string; search?: string };
}>();
const page = usePage();
const search = ref(props.filters.search || "");
const status = ref(props.filters.status || "");
const retrying = ref<number | null>(null);
const flash = computed(() => page.props.flash as any);

function filter() {
    router.get(
        "/admin/messages",
        { search: search.value, status: status.value },
        { preserveState: true, replace: true },
    );
}
function selectStatus(value: string) {
    status.value = status.value === value ? "" : value;
    filter();
}
function retry(id: number) {
    retrying.value = id;
    router.post(
        `/admin/messages/${id}/retry`,
        {},
        {
            preserveScroll: true,
            onFinish: () => (retrying.value = null),
        },
    );
}
function dateTime(value?: string) {
    if (!value) return "-";
    return new Intl.DateTimeFormat("id-ID", {
        dateStyle: "medium",
        timeStyle: "short",
        timeZone: "Asia/Jakarta",
    }).format(new Date(value));
}
</script>

<template>
    <Head title="Pesan Terkirim" />
    <div class="messages-page">
        <header class="page-heading">
            <div><p>MONITORING EMAIL</p><h1>Pesan Terkirim</h1><span>Pantau seluruh notifikasi email pendaftar dan tindak lanjuti kegagalan pengiriman.</span></div>
            <div class="pulse"><i></i> SMTP Monitoring</div>
        </header>

        <div v-if="flash?.success" class="notice success">✓ {{ flash.success }}</div>
        <div v-if="flash?.error" class="notice error">! {{ flash.error }}</div>

        <section class="stats">
            <button :class="{ active: status === 'sent' }" @click="selectStatus('sent')"><span class="icon sent">✓</span><small>Terkirim</small><strong>{{ counts.sent }}</strong><em>Email berhasil diterima server tujuan</em></button>
            <button :class="{ active: status === 'pending' }" @click="selectStatus('pending')"><span class="icon pending">◷</span><small>Pending</small><strong>{{ counts.pending }}</strong><em>Menunggu atau sedang diproses</em></button>
            <button :class="{ active: status === 'failed' }" @click="selectStatus('failed')"><span class="icon failed">!</span><small>Gagal</small><strong>{{ counts.failed }}</strong><em>Perlu diperiksa atau dikirim ulang</em></button>
        </section>

        <form class="filters" @submit.prevent="filter">
            <input v-model="search" placeholder="Cari nama, email, nomor pendaftaran, atau lagu" />
            <select v-model="status"><option value="">Semua status</option><option value="sent">Terkirim</option><option value="pending">Pending</option><option value="failed">Gagal</option></select>
            <button>Filter</button>
        </form>

        <section class="message-list">
            <article v-for="message in messages.data" :key="message.id" :class="['message-card', message.display_status]">
                <div class="status-mark"><span>{{ message.display_status === 'sent' ? '✓' : message.display_status === 'failed' ? '!' : '◷' }}</span></div>
                <div class="message-copy">
                    <div class="message-top"><span :class="['badge', message.display_status]">{{ message.display_status === 'sent' ? 'Terkirim' : message.display_status === 'failed' ? 'Gagal Terkirim' : 'Pending' }}</span><time>{{ dateTime(message.updated_at) }}</time></div>
                    <h2>{{ message.subject }}</h2>
                    <p><b>{{ message.full_name || 'Pendaftar tidak tersedia' }}</b> · {{ message.email || 'Email tidak tersedia' }}</p>
                    <small>{{ message.song_title || 'Data lagu tidak tersedia' }}<template v-if="message.artist_name"> — {{ message.artist_name }}</template></small>
                    <div v-if="message.display_status === 'failed'" class="failure">
                        <strong>Penyebab kegagalan</strong>
                        <p>{{ message.last_error || 'Server email tidak memberikan informasi kegagalan.' }}</p>
                        <span>Percobaan pengiriman: {{ message.attempts }}</span>
                    </div>
                </div>
                <div class="actions">
                    <Link v-if="message.submission_id" :href="`/admin/submissions/${message.submission_id}`">Lihat Pendaftar</Link>
                    <button v-if="message.display_status === 'failed'" :disabled="retrying === message.id" @click="retry(message.id)">{{ retrying === message.id ? 'Mengirim…' : 'Kirim Ulang ↻' }}</button>
                </div>
            </article>
            <div v-if="!messages.data.length" class="empty"><span>✉</span><h2>Tidak ada pesan</h2><p>Belum ada email yang sesuai dengan filter ini.</p></div>
        </section>

        <nav v-if="messages.links?.length > 3" class="pagination">
            <Link v-for="link in messages.links" :key="link.label" :href="link.url || '#'" :class="{ active: link.active, disabled: !link.url }" v-html="link.label" />
        </nav>
    </div>
</template>

<style scoped>
.messages-page{display:grid;gap:24px;color:#edf2f7}.page-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:24px}.page-heading p{margin:0 0 8px;color:#ff7620;font-size:11px;font-weight:800;letter-spacing:.2em}.page-heading h1{margin:0;font-family:"Space Grotesk",sans-serif;font-size:40px;line-height:1.05}.page-heading div>span{display:block;margin-top:10px;color:#7f8da3;font-size:14px}.pulse{display:flex;align-items:center;gap:9px;border:1px solid #253149;border-radius:999px;background:#101827;padding:10px 15px;color:#91a0b7;font-size:12px}.pulse i{width:8px;height:8px;border-radius:50%;background:#35d07f;box-shadow:0 0 0 6px #35d07f18}.notice{border-radius:14px;padding:14px 18px;font-size:14px;font-weight:700}.notice.success{border:1px solid #1d8055;background:#0d3227;color:#5ce6a3}.notice.error{border:1px solid #913d42;background:#36181e;color:#ff8e92}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.stats button{position:relative;display:grid;grid-template-columns:auto 1fr;grid-template-rows:auto auto auto;column-gap:14px;border:1px solid #222e43;border-radius:18px;background:#111a2a;padding:20px;text-align:left;transition:.2s}.stats button:hover,.stats button.active{border-color:#ff742e;background:#172033;transform:translateY(-2px)}.stats .icon{grid-row:1/4;display:grid;width:46px;height:46px;place-items:center;border-radius:14px;font-size:20px;font-weight:900}.icon.sent{background:#123c2d;color:#45dc91}.icon.pending{background:#3c3117;color:#ffc556}.icon.failed{background:#451d25;color:#ff7b83}.stats small{color:#8c99ad;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.12em}.stats strong{margin-top:2px;font-size:27px}.stats em{color:#637086;font-size:11px;font-style:normal}.filters{display:grid;grid-template-columns:1fr 210px auto;gap:12px;border:1px solid #202b3f;border-radius:16px;background:#0f1726;padding:14px}.filters input,.filters select{min-height:46px;border:1px solid #2a3549;border-radius:11px;background:#0a1220;padding:0 14px;color:#eef2f7}.filters button{border-radius:11px;background:#ff6c18;padding:0 24px;color:#111;font-weight:800}.message-list{display:grid;gap:12px}.message-card{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:17px;border:1px solid #202c40;border-radius:18px;background:#101827;padding:20px}.message-card.failed{border-color:#633039}.status-mark span{display:grid;width:44px;height:44px;place-items:center;border-radius:14px;background:#182336;color:#94a2b8;font-size:19px;font-weight:900}.message-card.sent .status-mark span{background:#123c2d;color:#43dc8f}.message-card.failed .status-mark span{background:#451d25;color:#ff7b83}.message-top{display:flex;align-items:center;gap:12px}.message-top time{color:#65728a;font-size:11px}.badge{border-radius:999px;padding:5px 9px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.08em}.badge.sent{background:#123c2d;color:#52e39b}.badge.pending{background:#3c3117;color:#ffc556}.badge.failed{background:#451d25;color:#ff858c}.message-copy h2{margin:10px 0 6px;font-size:17px}.message-copy>p{margin:0;color:#a9b3c2;font-size:13px}.message-copy>small{display:block;margin-top:5px;color:#6f7d93}.failure{margin-top:15px;border:1px solid #66333b;border-radius:12px;background:#32171d;padding:13px;color:#ff9ca1}.failure strong{font-size:11px;text-transform:uppercase;letter-spacing:.1em}.failure p{margin:7px 0;white-space:pre-wrap;overflow-wrap:anywhere;font-size:12px;line-height:1.55}.failure span{color:#c9797d;font-size:10px}.actions{display:flex;flex-direction:column;align-items:stretch;justify-content:center;gap:9px}.actions a,.actions button{min-width:132px;border:1px solid #344057;border-radius:10px;padding:10px 13px;text-align:center;color:#b8c3d4;font-size:11px;font-weight:800}.actions button{border-color:#ff6c18;background:#ff6c18;color:#111}.actions button:disabled{opacity:.5}.empty{border:1px dashed #2a3549;border-radius:18px;padding:52px;text-align:center;color:#718097}.empty span{font-size:34px}.empty h2{margin:10px 0 4px;color:#dbe2eb}.empty p{margin:0}.pagination{display:flex;justify-content:center;gap:7px}.pagination a{border:1px solid #28354a;border-radius:9px;padding:8px 12px;color:#8492a7;font-size:12px}.pagination a.active{border-color:#ff6c18;background:#ff6c18;color:#111}.pagination a.disabled{pointer-events:none;opacity:.4}@media(max-width:900px){.stats{grid-template-columns:1fr}.page-heading{align-items:flex-start;flex-direction:column}.filters{grid-template-columns:1fr}.message-card{grid-template-columns:auto 1fr}.actions{grid-column:1/-1;flex-direction:row}.actions>*{flex:1}}@media(max-width:560px){.page-heading h1{font-size:32px}.message-card{grid-template-columns:1fr}.status-mark{display:none}.actions{flex-direction:column}.message-top{align-items:flex-start;flex-direction:column}.filters button{min-height:46px}}
</style>
