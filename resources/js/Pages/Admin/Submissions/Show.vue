<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
defineOptions({ layout: AdminLayout });
const props = defineProps<{
    submission: any;
    statuses: { value: string; label: string }[];
}>();
const statusForm = useForm({ status: props.submission.status, reason: "" });
const statusSuccess = ref(false);
const savedStatus = ref({ label: "", reason: "" });
const editing = ref(false);
const confirmingDelete = ref(false);
const deleting = ref(false);
const registrationCopied = ref(false);
const flash = computed(() => (usePage().props.flash as any)?.success);
const canEdit = computed(() => (usePage().props.auth as any)?.user?.role !== "viewer");
const canDelete = computed(() =>
    ["super_admin", "admin"].includes((usePage().props.auth as any)?.user?.role),
);
const isRejectedSelection = computed(() =>
    ["not_selected", "disqualified"].includes(statusForm.status),
);
const genres = ["Alternative/Indie", "Latin", "Classical", "Country", "Blues", "Electronic", "Folk", "Hip Hop/Rap", "Jazz", "New Age", "Pop", "R&B/Soul", "Reggae", "Rock", "World", "Childhood", "Devotional/Inspirational", "Dance", "Soundtrack"];
const editForm = useForm({
    full_name: props.submission.applicant?.full_name || "",
    nik: props.submission.applicant?.nik || "",
    birth_place: props.submission.applicant?.birth_place || "",
    birth_date: String(props.submission.applicant?.birth_date || "").slice(0, 10),
    email: props.submission.applicant?.email || "",
    whatsapp: props.submission.applicant?.whatsapp || "",
    province: props.submission.applicant?.province || "",
    city: props.submission.applicant?.city || "",
    district: props.submission.applicant?.district || "",
    village: props.submission.applicant?.village || "",
    postal_code: props.submission.applicant?.postal_code || "",
    address: props.submission.applicant?.address || "",
    title: props.submission.song?.title || "",
    artist_name: props.submission.song?.artist_name || "",
    artist_social_url: props.submission.song?.artist_social_url || "",
    artist_spotify_url: props.submission.song?.artist_spotify_url || "",
    songwriters: (props.submission.song?.songwriters || [{ name: "", role: "composer_author" }]).map((item: any) => ({ ...item })),
    genre: props.submission.song?.genre || "",
    language: props.submission.song?.language || "",
    creation_year: props.submission.song?.creation_year || new Date().getFullYear(),
    story: props.submission.song?.story || "",
    lyrics: props.submission.song?.lyrics || "",
    video_url: props.submission.links?.find((item: any) => item.type === "video")?.url || "",
});
function addWriter() { editForm.songwriters.push({ name: "", role: "composer_author" }); }
function removeWriter(index: number) { if (editForm.songwriters.length > 1) editForm.songwriters.splice(index, 1); }
function saveDetails() {
    editForm.put(`/admin/submissions/${props.submission.id}/details`, {
        preserveScroll: true,
        onSuccess: () => { editing.value = false; editForm.clearErrors(); },
    });
}
function saveStatus() {
    statusForm.clearErrors();
    if (isRejectedSelection.value && statusForm.reason.trim().length < 10) {
        statusForm.setError(
            "reason",
            statusForm.reason.trim()
                ? "Alasan penolakan harus ditulis dengan jelas, minimal 10 karakter."
                : "Alasan wajib diisi ketika status pendaftaran diubah menjadi Ditolak.",
        );
        return;
    }
    const label = statusLabels[statusForm.status] || statusForm.status;
    const reason = statusForm.reason.trim();
    statusForm.patch(`/admin/submissions/${props.submission.id}/status`, {
        preserveScroll: true,
        onSuccess: () => {
            savedStatus.value = { label, reason };
            statusSuccess.value = true;
            statusForm.reason = "";
        },
    });
}
function deleteSubmission() {
    deleting.value = true;
    router.delete(`/admin/submissions/${props.submission.id}`, {
        onFinish: () => { deleting.value = false; },
    });
}
const statusLabels: Record<string, string> = {
    submitted: "Pending",
    administrative_review: "Di Review",
    revision_requested: "Di Review",
    eligible: "Di Review",
    curation: "Di Review",
    shortlisted: "Di Review",
    selected: "Diterima",
    not_selected: "Ditolak",
    disqualified: "Ditolak",
};
const currentStatusHistory = computed(() =>
    [...(props.submission.status_histories || [])]
        .filter((history: any) => history.to_status === props.submission.status)
        .sort(
            (a: any, b: any) =>
                new Date(b.created_at).getTime() -
                new Date(a.created_at).getTime(),
        )[0],
);
const currentStatusReason = computed(
    () => currentStatusHistory.value?.reason?.trim() || "Belum ada alasan atau catatan untuk status ini.",
);
const currentStatusActor = computed(
    () => currentStatusHistory.value?.actor?.name || "Sistem",
);
const writerRole = (role: string) =>
    ({
        composer: "Composer",
        author: "Author",
        composer_author: "Composer & Author",
    })[role] || role;
const fileSize = (bytes: number) =>
    bytes >= 1048576
        ? (bytes / 1048576).toFixed(1) + " MB"
        : Math.ceil(bytes / 1024) + " KB";
const fileTypeLabel = (type: string) =>
    ({
        ktp: "Dokumen KTP",
        video: "Video Penampilan",
        demo: "Demo Lagu",
        revision: "File Revisi",
    })[type] || "File Lainnya";
const scanLabel = (status: string) =>
    ({
        pending: "Menunggu pemeriksaan",
        clean: "Aman",
        infected: "Bermasalah",
        trashed: "Dipindahkan ke Data Terhapus",
        failed: "Gagal diperiksa",
    })[status] || status;
const canDownloadFile = (status: string) =>
    status === "clean" || status === "pending";
const fileStatusLabel = (file: any) =>
    file.downloaded_at ? "File Didownload" : scanLabel(file.scan_status);
const markFileDownloaded = (file: any) => {
    if (canDownloadFile(file.scan_status)) {
        file.downloaded_at = new Date().toISOString();
    }
};
const formatDate = (value?: string) => {
    if (!value) return "-";
    const datePart = String(value).slice(0, 10);
    const [year, month, day] = datePart.split("-");
    return year && month && day ? `${day}/${month}/${year}` : "-";
};
const whatsappUrl = (phone?: string) => {
    const digits = String(phone || "").replace(/\D/g, "").replace(/^0/, "62");
    return `https://wa.me/${digits}`;
};
const linkTypeLabel = (type: string) =>
    ({ video: "Video", demo: "Demo Lagu", social: "Sosial Media" })[type] || type;
async function copyRegistrationNumber() {
    await navigator.clipboard.writeText(props.submission.registration_number);
    registrationCopied.value = true;
    window.setTimeout(() => (registrationCopied.value = false), 1800);
}
</script>
<template>
    <Head :title="`Detail ${submission.registration_number}`" />
    <div class="detail-page">
        <Link href="/admin/submissions" class="back"
            >← Kembali ke Data Pendaftar</Link
        >
        <div v-if="flash" class="flash-message">{{ flash }}</div>
        <header>
            <div>
                <p>DETAIL PENDAFTARAN</p>
                <h1>{{ submission.song?.title || "Tanpa Judul" }}</h1>
                <div class="registration-meta">
                    <button type="button" class="registration-number" :aria-label="`Salin nomor pendaftaran ${submission.registration_number}`" @click="copyRegistrationNumber">
                        <small>{{ registrationCopied ? "TERSALIN" : "NOMOR PENDAFTARAN" }}</small>
                        <strong>{{ submission.registration_number }}</strong>
                        <span aria-hidden="true">{{ registrationCopied ? "✓" : "⧉" }}</span>
                    </button>
                    <span class="submitted-date">Dikirim
                        {{
                        submission.submitted_at
                            ? new Date(submission.submitted_at).toLocaleString(
                                  "id-ID",
                              )
                            : "-"
                        }}
                    </span>
                </div>
            </div>
            <section :class="['current-status-card', submission.status]" aria-label="Status pendaftaran saat ini">
                <div class="current-status-heading">
                    <span class="status-indicator" aria-hidden="true"></span>
                    <div>
                        <small>STATUS SAAT INI</small>
                        <strong>{{ statusLabels[submission.status] || submission.status }}</strong>
                    </div>
                </div>
                <div class="current-status-reason">
                    <small>ALASAN / CATATAN STATUS</small>
                    <p>{{ currentStatusReason }}</p>
                </div>
                <span v-if="currentStatusHistory" class="current-status-meta">
                    Diperbarui oleh {{ currentStatusActor }} ·
                    {{ new Date(currentStatusHistory.created_at).toLocaleString("id-ID") }}
                </span>
            </section>
            <div class="header-actions">
                <button v-if="canEdit" type="button" class="edit-button" @click="editing = true">Edit Data</button>
                <button v-if="canDelete" type="button" class="delete-button" @click="confirmingDelete = true">Hapus Pendaftaran</button>
                <span :class="['main-status', submission.status]">{{
                    statusLabels[submission.status] || submission.status
                }}</span>
            </div>
        </header>
        <div class="layout">
            <main class="data-stack">
                <section class="panel">
                    <div class="panel-title">
                        <span>01</span>
                        <div>
                            <h2>Data Pendaftar</h2>
                            <p>Identitas dan alamat lengkap.</p>
                        </div>
                    </div>
                    <dl class="data-grid">
                        <div>
                            <dt>Nama lengkap</dt>
                            <dd>{{ submission.applicant?.full_name }}</dd>
                        </div>
                        <div>
                            <dt>Nama artis</dt>
                            <dd>
                                {{
                                    submission.song?.artist_name ||
                                    submission.applicant?.stage_name ||
                                    "-"
                                }}
                            </dd>
                        </div>
                        <div>
                            <dt>NIK</dt>
                            <dd>{{ submission.applicant?.nik || "-" }}</dd>
                        </div>
                        <div>
                            <dt>Tempat, tanggal lahir</dt>
                            <dd>
                                {{ submission.applicant?.birth_place }},
                                {{ formatDate(submission.applicant?.birth_date) }}
                            </dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd>{{ submission.applicant?.email }}</dd>
                        </div>
                        <div>
                            <dt>WhatsApp</dt>
                            <dd class="value-action">
                                <span>{{ submission.applicant?.whatsapp }}</span>
                                <a :href="whatsappUrl(submission.applicant?.whatsapp)" target="_blank" rel="noopener" class="action-link whatsapp-link">Chat WhatsApp ↗</a>
                            </dd>
                        </div>
                        <div class="wide">
                            <dt>Wilayah</dt>
                            <dd>
                                {{
                                    [
                                        submission.applicant?.village,
                                        submission.applicant?.district,
                                        submission.applicant?.city,
                                        submission.applicant?.province,
                                        submission.applicant?.postal_code,
                                    ]
                                        .filter(Boolean)
                                        .join(", ")
                                }}
                            </dd>
                        </div>
                        <div class="wide">
                            <dt>Alamat lengkap</dt>
                            <dd>{{ submission.applicant?.address }}</dd>
                        </div>
                    </dl>
                </section>
                <section class="panel">
                    <div class="panel-title">
                        <span>02</span>
                        <div>
                            <h2>Data Lagu</h2>
                            <p>Informasi karya yang didaftarkan.</p>
                        </div>
                    </div>
                    <dl class="data-grid">
                        <div>
                            <dt>Judul lagu</dt>
                            <dd>{{ submission.song?.title }}</dd>
                        </div>
                        <div>
                            <dt>Nama artis</dt>
                            <dd>{{ submission.song?.artist_name }}</dd>
                        </div>
                        <div>
                            <dt>Sosial media artis</dt>
                            <dd class="value-action">
                                <a
                                    :href="submission.song?.artist_social_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="action-link"
                                    >Buka Sosial Media ↗</a
                                >
                            </dd>
                        </div>
                        <div>
                            <dt>Spotify artis</dt>
                            <dd class="value-action">
                                <a
                                    v-if="submission.song?.artist_spotify_url"
                                    :href="submission.song.artist_spotify_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="action-link spotify-link"
                                    >Buka profil Spotify ↗</a
                                ><span v-else>-</span>
                            </dd>
                        </div>
                        <div>
                            <dt>Genre</dt>
                            <dd>{{ submission.song?.genre }}</dd>
                        </div>
                        <div>
                            <dt>Bahasa</dt>
                            <dd>{{ submission.song?.language }}</dd>
                        </div>
                        <div>
                            <dt>Tahun penciptaan</dt>
                            <dd>{{ submission.song?.creation_year }}</dd>
                        </div>
                        <div class="wide">
                            <dt>Songwriter</dt>
                            <dd>
                                <span
                                    v-for="writer in submission.song
                                        ?.songwriters || []"
                                    :key="writer.name"
                                    class="writer"
                                    >{{ writer.name }} ·
                                    {{ writerRole(writer.role) }}</span
                                >
                            </dd>
                        </div>
                        <div class="wide">
                            <dt>Cerita di balik lagu</dt>
                            <dd class="long">{{ submission.song?.story }}</dd>
                        </div>
                        <div class="wide">
                            <dt>Lirik lagu</dt>
                            <dd class="long lyrics">
                                {{ submission.song?.lyrics || "-" }}
                            </dd>
                        </div>
                    </dl>
                </section>
                <section class="panel">
                    <div class="panel-title">
                        <span>03</span>
                        <div>
                            <h2>Tautan & File Upload</h2>
                            <p>Seluruh berkas yang dikirim pendaftar.</p>
                        </div>
                    </div>
                    <div class="links">
                        <a
                            v-for="link in submission.links"
                            :key="link.id"
                            :href="link.url"
                            target="_blank"
                            rel="noopener"
                            ><span>↗</span>
                            <div>
                                <b>Link {{ linkTypeLabel(link.type) }}</b
                                ><small>{{ link.url }}</small>
                            </div><strong class="open-action">Buka Link ↗</strong></a
                        >
                    </div>
                    <div class="files">
                        <a
                            v-for="file in submission.files"
                            :key="file.id"
                            :href="`/admin/files/${file.id}`"
                            :class="{ disabled: !canDownloadFile(file.scan_status) }"
                            :aria-disabled="!canDownloadFile(file.scan_status)"
                            @click="canDownloadFile(file.scan_status) ? markFileDownloaded(file) : $event.preventDefault()"
                            ><span>↓</span>
                            <div>
                                <em :class="['file-type', file.type]">{{
                                    fileTypeLabel(file.type)
                                }}</em>
                                <b>{{ file.original_name }}</b
                                ><small
                                    >{{ file.type }} · {{ file.mime }} ·
                                    {{ fileSize(file.size) }} · Status:
                                    {{ fileStatusLabel(file) }}</small
                                >
                            </div><strong class="download-action">{{ canDownloadFile(file.scan_status) ? "Download File ↓" : "File Diblokir" }}</strong></a
                        >
                        <p
                            v-if="
                                !submission.files?.length &&
                                !submission.links?.length
                            "
                            class="empty"
                        >
                            Tidak ada file atau tautan.
                        </p>
                    </div>
                </section>
                <section class="panel">
                    <div class="panel-title">
                        <span>04</span>
                        <div>
                            <h2>Persetujuan & Riwayat</h2>
                            <p>Catatan persetujuan dan perubahan status.</p>
                        </div>
                    </div>
                    <div class="consents">
                        <p
                            v-for="consent in submission.consents"
                            :key="consent.id"
                        >
                            <i>✓</i> Menyetujui {{ consent.type }} versi
                            {{ consent.document_version }}
                            <small>{{
                                new Date(consent.accepted_at).toLocaleString(
                                    "id-ID",
                                )
                            }}</small>
                        </p>
                    </div>
                    <div class="timeline">
                        <div
                            v-for="history in submission.status_histories"
                            :key="history.id"
                        >
                            <i></i
                            ><span
                                ><b>{{
                                    statusLabels[history.to_status] ||
                                    history.to_status
                                }}</b
                                ><small
                                    >{{ history.reason || "Tanpa catatan" }} ·
                                    {{
                                        history.actor?.name || "Pendaftar"
                                    }}</small
                                ></span
                            >
                        </div>
                    </div>
                </section>
            </main>
            <aside>
                <form
                    class="panel status-panel"
                    @submit.prevent="saveStatus"
                >
                    <p>STATUS PENDAFTARAN</p>
                    <h2>Ubah Status</h2>
                    <label
                        >Status<select v-model="statusForm.status" required>
                            <option
                                v-for="item in statuses"
                                :key="item.value"
                                :value="item.value"
                            >
                                {{ item.label }}
                            </option>
                        </select></label
                    ><label
                        >{{ isRejectedSelection ? "Alasan penolakan (wajib)" : "Catatan perubahan (opsional)" }}<textarea
                            v-model="statusForm.reason"
                            :required="isRejectedSelection"
                            :aria-invalid="Boolean(statusForm.errors.reason)"
                            :placeholder="isRejectedSelection ? 'Jelaskan alasan pendaftaran ditolak' : 'Tambahkan catatan status jika diperlukan'"
                        ></textarea>
                    </label>
                    <div v-if="isRejectedSelection" class="rejection-guidance">
                        <span aria-hidden="true">!</span>
                        <div>
                            <strong>Alasan ini akan disampaikan kepada peserta</strong>
                            <p>Tuliskan alasan yang jelas, spesifik, dan mudah dipahami. Hindari kata singkat seperti “tidak lolos” tanpa penjelasan.</p>
                            <small>{{ statusForm.reason.trim().length }}/2000 karakter · minimal 10 karakter</small>
                        </div>
                    </div>
                    <p
                        v-for="error in statusForm.errors"
                        :key="error"
                        class="error"
                    >
                        {{ error }}
                    </p>
                    <button :disabled="statusForm.processing">
                        Simpan Status
                    </button>
                </form>
            </aside>
        </div>
        <div v-if="statusSuccess" class="status-success-overlay" role="dialog" aria-modal="true" aria-label="Status berhasil diperbarui" @click.self="statusSuccess = false">
            <section class="status-success-modal">
                <button class="success-close" type="button" aria-label="Tutup" @click="statusSuccess = false">×</button>
                <div class="success-check" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12.5 4.2 4.2L19 7" /></svg></div>
                <p class="success-eyebrow">PERUBAHAN BERHASIL</p>
                <h2>Status pendaftaran diperbarui</h2>
                <p class="success-copy">Perubahan sudah tersimpan dan tercatat dalam riwayat pendaftaran.</p>
                <div class="status-result"><small>STATUS BARU</small><strong>{{ savedStatus.label }}</strong><span>{{ submission.registration_number }}</span></div>
                <div v-if="savedStatus.reason" class="reason-result"><small>CATATAN ADMIN</small><p>{{ savedStatus.reason }}</p></div>
                <button class="success-action" type="button" @click="statusSuccess = false">Selesai</button>
            </section>
        </div>
        <div v-if="editing" class="edit-overlay" role="dialog" aria-modal="true" @click.self="editing = false">
            <form class="edit-modal" @submit.prevent="saveDetails">
                <header class="edit-header">
                    <div><p>REVISI ADMIN</p><h2>Edit Data Pendaftaran</h2><span>Perubahan disimpan dan dicatat dalam audit sistem.</span></div>
                    <button type="button" aria-label="Tutup" @click="editing = false">×</button>
                </header>
                <div class="edit-scroll">
                    <section class="edit-section"><h3>Data Pendaftar</h3><div class="edit-grid">
                        <label>Nama lengkap<input v-model="editForm.full_name" required /></label><label>NIK<input v-model="editForm.nik" inputmode="numeric" minlength="16" maxlength="16" required /></label>
                        <label>Tempat lahir<input v-model="editForm.birth_place" required /></label><label>Tanggal lahir<input v-model="editForm.birth_date" type="date" required /></label>
                        <label>Email<input v-model="editForm.email" type="email" required /></label><label>WhatsApp<input v-model="editForm.whatsapp" required /></label>
                        <label>Provinsi<input v-model="editForm.province" required /></label><label>Kota/Kabupaten<input v-model="editForm.city" required /></label>
                        <label>Kecamatan<input v-model="editForm.district" required /></label><label>Kelurahan/Desa<input v-model="editForm.village" required /></label>
                        <label>Kode Pos<input v-model="editForm.postal_code" inputmode="numeric" maxlength="5" required /></label><label class="wide">Alamat lengkap<textarea v-model="editForm.address" required></textarea></label>
                    </div></section>
                    <section class="edit-section"><h3>Data Lagu & Artis</h3><div class="edit-grid">
                        <label>Judul lagu<input v-model="editForm.title" required /></label><label>Nama artis<input v-model="editForm.artist_name" required /></label>
                        <label>Sosial media artis<input v-model="editForm.artist_social_url" type="url" required /></label><label>Spotify artis (opsional)<input v-model="editForm.artist_spotify_url" type="url" /></label>
                        <label>Genre<select v-model="editForm.genre" required><option v-for="genre in genres" :key="genre" :value="genre">{{ genre }}</option></select></label><label>Bahasa<input v-model="editForm.language" required /></label>
                        <label>Tahun penciptaan<input v-model="editForm.creation_year" type="number" min="1900" :max="new Date().getFullYear()" required /></label><label class="wide">Link video (opsional)<input v-model="editForm.video_url" type="url" /></label>
                        <label class="wide">Cerita di balik lagu<textarea v-model="editForm.story" required></textarea></label><label class="wide">Lirik lagu<textarea v-model="editForm.lyrics" class="lyrics-editor"></textarea></label>
                    </div></section>
                    <section class="edit-section"><div class="writer-heading"><h3>Songwriter</h3><button type="button" @click="addWriter">+ Tambah</button></div>
                        <div v-for="(writer, index) in editForm.songwriters" :key="index" class="writer-row"><input v-model="writer.name" placeholder="Nama songwriter" required /><select v-model="writer.role" required><option value="composer">Composer</option><option value="author">Author</option><option value="composer_author">Composer & Author</option></select><button type="button" :disabled="editForm.songwriters.length === 1" @click="removeWriter(index)">Hapus</button></div>
                    </section>
                    <div v-if="Object.keys(editForm.errors).length" class="edit-errors"><b>Periksa kembali data:</b><p v-for="(error, field) in editForm.errors" :key="field">{{ error }}</p></div>
                </div>
                <footer class="edit-footer"><button type="button" class="cancel" @click="editing = false">Batal</button><button type="submit" class="save-edit" :disabled="editForm.processing">{{ editForm.processing ? "Menyimpan..." : "Simpan Hasil Edit" }}</button></footer>
            </form>
        </div>
        <div v-if="confirmingDelete" class="delete-overlay" role="dialog" aria-modal="true" aria-label="Konfirmasi hapus pendaftaran" @click.self="confirmingDelete = false">
            <section class="delete-modal">
                <button class="delete-close" type="button" aria-label="Tutup" @click="confirmingDelete = false">×</button>
                <div class="delete-icon" aria-hidden="true">!</div>
                <p>HAPUS PENDAFTARAN</p>
                <h2>Yakin ingin menghapus data ini?</h2>
                <span>Pendaftaran <b>{{ submission.registration_number }}</b>, data lagu, riwayat, dan seluruh file upload akan dihapus permanen.</span>
                <div class="delete-actions">
                    <button type="button" class="delete-cancel" :disabled="deleting" @click="confirmingDelete = false">Batal</button>
                    <button type="button" class="delete-confirm" :disabled="deleting" @click="deleteSubmission">{{ deleting ? "Menghapus..." : "Ya, Hapus Permanen" }}</button>
                </div>
            </section>
        </div>
    </div>
</template>
<style scoped>
.detail-page {
    max-width: 1450px;
    margin: auto;
}
.back {
    color: #8793a6;
    font-size: 11px;
}
.detail-page > header {
    display: grid;
    grid-template-columns: minmax(270px, .8fr) minmax(380px, 1.35fr) auto;
    align-items: end;
    gap: 20px;
    margin-top: 18px;
}
.detail-page header p {
    color: #ff7c2e;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.2em;
}
.detail-page h1 {
    margin-top: 7px;
    font-size: 36px;
}
.detail-page header div > span {
    display: block;
    margin-top: 5px;
    color: #748196;
    font-size: 11px;
}
.registration-meta { display: flex; align-items: center; gap: 12px; margin-top: 10px; }
.registration-number { position: relative; display: grid; min-width: 245px; padding: 12px 48px 12px 15px; border: 1px solid #ff76205c; border-radius: 13px; text-align: left; color: #fff; background: linear-gradient(135deg, #241712, #111827); box-shadow: inset 0 1px #ffffff0d; transition: .2s ease; }
.registration-number:hover { border-color: #ff7620; transform: translateY(-2px); box-shadow: 0 10px 28px #0005, inset 0 1px #ffffff0d; }
.registration-number small { color: #ff7c2e; font-size: 8px; font-weight: 800; letter-spacing: .17em; }
.registration-number strong { margin-top: 4px; font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: clamp(19px, 2vw, 25px); letter-spacing: .035em; }
.registration-number > span { position: absolute; top: 50%; right: 16px; margin: 0; color: #ff8a3d; font-size: 18px; transform: translateY(-50%); }
.submitted-date { margin-top: 0 !important; }
.main-status {
    border-radius: 99px;
    padding: 9px 14px;
    background: #33271a;
    color: #fbbf24;
    font-size: 10px;
    font-weight: 800;
}
.main-status.selected {
    background: #16372e;
    color: #4ade80;
}
.main-status.not_selected,
.main-status.disqualified {
    background: #3b1d25;
    color: #fb7185;
}
.current-status-card { display: grid; gap: 13px; min-width: 0; border: 1px solid #d79b303d; border-radius: 18px; background: linear-gradient(135deg, #211b12, #111827 68%); padding: 19px 21px; box-shadow: 0 18px 55px #0004, inset 0 1px #ffffff0b; }
.current-status-heading { display: flex; align-items: center; gap: 12px; }
.current-status-heading > div { display: grid; gap: 3px; }
.current-status-heading small, .current-status-reason small { color: #8995a8; font-size: 8px; font-weight: 900; letter-spacing: .16em; }
.current-status-heading strong { color: #fbbf24; font-size: 21px; line-height: 1.1; }
.status-indicator { width: 12px; height: 12px; flex: none; border-radius: 50%; background: #fbbf24; box-shadow: 0 0 0 7px #fbbf2415, 0 0 22px #fbbf2466; }
.current-status-reason { border-left: 3px solid #fbbf2455; padding-left: 13px; }
.current-status-reason p { margin-top: 5px; color: #e5e9ef; font-size: 13px; font-weight: 650; line-height: 1.55; overflow-wrap: anywhere; }
.current-status-meta { margin: 0 !important; color: #6f7c90 !important; font-size: 9px !important; }
.current-status-card.selected { border-color: #34d3994a; background: linear-gradient(135deg, #10251f, #111827 68%); }
.current-status-card.selected .current-status-heading strong { color: #6ee7b7; }
.current-status-card.selected .status-indicator { background: #34d399; box-shadow: 0 0 0 7px #34d39915, 0 0 22px #34d39966; }
.current-status-card.selected .current-status-reason { border-left-color: #34d39966; }
.current-status-card.not_selected, .current-status-card.disqualified { border-color: #fb71855c; background: linear-gradient(135deg, #2c151c, #111827 70%); }
.current-status-card.not_selected .current-status-heading strong, .current-status-card.disqualified .current-status-heading strong { color: #fda4af; }
.current-status-card.not_selected .status-indicator, .current-status-card.disqualified .status-indicator { background: #fb7185; box-shadow: 0 0 0 7px #fb718515, 0 0 22px #fb718566; }
.current-status-card.not_selected .current-status-reason, .current-status-card.disqualified .current-status-reason { border-left-color: #fb718577; }
.layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 330px;
    align-items: start;
    gap: 20px;
    margin-top: 26px;
}
.data-stack {
    display: grid;
    gap: 20px;
}
.panel {
    border: 1px solid #202b3e;
    border-radius: 20px;
    background: #111827;
    padding: 26px;
}
.panel-title {
    display: flex;
    gap: 14px;
}
.panel-title > span {
    display: grid;
    width: 38px;
    height: 38px;
    flex: none;
    place-items: center;
    border-radius: 11px;
    background: #2e1c1c;
    color: #ff7c2e;
    font-family: monospace;
    font-weight: 700;
}
.panel-title h2 {
    font-size: 17px;
}
.panel-title p {
    margin-top: 4px;
    color: #718096;
    font-size: 11px;
}
.data-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 24px;
    margin-top: 25px;
}
.data-grid .wide {
    grid-column: 1/-1;
}
.data-grid dt {
    color: #647086;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
.data-grid dd {
    margin-top: 7px;
    color: #e7ebf1;
    font-size: 12px;
    line-height: 1.65;
    word-break: break-word;
}
.value-action { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; }
.action-link { display: inline-flex; align-items: center; width: fit-content; border: 1px solid #ff7c2e55; border-radius: 99px; background: #ff6a0014; padding: 6px 10px; color: #ff9a5b; font-size: 9px; font-weight: 800; }
.action-link:hover { border-color: #ff7c2e; background: #ff6a00; color: #101827; }
.whatsapp-link { border-color: #34d39955; background: #10b98117; color: #6ee7b7; }
.whatsapp-link:hover { border-color: #34d399; background: #10b981; color: #07140f; }
.spotify-link { border-color: #4ade8055; background: #22c55e17; color: #86efac; }
.data-grid dd.long {
    border-left: 2px solid #ff6a0044;
    padding-left: 14px;
    color: #adb7c6;
}
.lyrics {
    white-space: pre-line;
}
.writer {
    display: inline-flex;
    margin: 0 7px 7px 0;
    border-radius: 99px;
    background: #242033;
    padding: 6px 9px;
    color: #d8b4fe;
    font-size: 10px;
}
.links,
.files {
    display: grid;
    gap: 9px;
    margin-top: 22px;
}
.links a,
.files a {
    display: grid;
    grid-template-columns: 38px minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    border: 1px solid #253044;
    border-radius: 13px;
    padding: 12px;
    background: #0c1421;
}
.open-action, .download-action { border: 1px solid #ff7c2e55; border-radius: 9px; padding: 8px 10px; color: #ff9a5b; font-size: 9px; white-space: nowrap; }
.files a.disabled { cursor: not-allowed; opacity: .62; }
.files a.disabled .download-action { border-color: #64748b55; color: #94a3b8; }
.links a > span,
.files a > span {
    display: grid;
    width: 36px;
    height: 36px;
    place-items: center;
    border-radius: 10px;
    background: #2e1c1c;
    color: #ff7c2e;
}
.links b,
.links small,
.files b,
.files small {
    display: block;
}
.links b,
.files b {
    font-size: 11px;
}
.file-type {
    display: inline-flex;
    width: fit-content;
    margin-bottom: 7px;
    border: 1px solid #ff7c2e55;
    border-radius: 99px;
    background: #ff6a0014;
    padding: 4px 8px;
    color: #ff9a5b;
    font-size: 8px;
    font-style: normal;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.file-type.video {
    border-color: #60a5fa55;
    background: #2563eb17;
    color: #93c5fd;
}
.file-type.demo {
    border-color: #c084fc55;
    background: #9333ea17;
    color: #d8b4fe;
}
.file-type.revision {
    border-color: #34d39955;
    background: #05966917;
    color: #6ee7b7;
}
.links small,
.files small {
    overflow: hidden;
    margin-top: 4px;
    color: #66748a;
    font-size: 9px;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.consents {
    display: grid;
    gap: 8px;
    margin-top: 22px;
}
.consents p {
    color: #b8c1ce;
    font-size: 11px;
}
.consents i {
    margin-right: 7px;
    color: #4ade80;
}
.consents small {
    display: block;
    margin: 4px 0 0 22px;
    color: #66748a;
}
.timeline {
    display: grid;
    gap: 0;
    margin-top: 22px;
}
.timeline > div {
    display: grid;
    grid-template-columns: 20px 1fr;
    gap: 8px;
    padding-bottom: 17px;
}
.timeline i {
    position: relative;
    width: 9px;
    height: 9px;
    margin-top: 3px;
    border-radius: 50%;
    background: #ff6a00;
}
.timeline i:after {
    position: absolute;
    top: 12px;
    left: 4px;
    width: 1px;
    height: 31px;
    background: #293548;
    content: "";
}
.timeline > div:last-child i:after {
    display: none;
}
.timeline b,
.timeline small {
    display: block;
}
.timeline b {
    font-size: 11px;
}
.timeline small {
    margin-top: 4px;
    color: #68758a;
    font-size: 9px;
}
.status-panel {
    position: sticky;
    top: 94px;
}
.status-panel > p {
    color: #ff7c2e;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.16em;
}
.status-panel h2 {
    margin-top: 6px;
    font-size: 20px;
}
.status-panel label {
    display: grid;
    gap: 7px;
    margin-top: 18px;
    color: #8f9bad;
    font-size: 10px;
    font-weight: 700;
}
select,
textarea {
    width: 100%;
    border: 1px solid #2a3548;
    border-radius: 11px;
    background: #0b1220;
    padding: 12px;
    color: #edf2f7;
    font-size: 11px;
}
textarea {
    min-height: 110px;
    resize: vertical;
}
.status-panel button {
    width: 100%;
    margin-top: 18px;
    border-radius: 11px;
    background: #ff6a00;
    padding: 12px;
    color: #111827;
    font-size: 11px;
    font-weight: 800;
}
.status-panel button:disabled {
    opacity: 0.5;
}
.error {
    margin-top: 8px;
    color: #fb7185;
    font-size: 10px;
}
.rejection-guidance { display: grid; grid-template-columns: 30px 1fr; gap: 11px; margin-top: 13px; border: 1px solid #fb71854d; border-radius: 13px; background: #351820; padding: 13px; }
.rejection-guidance > span { display: grid; width: 30px; height: 30px; place-items: center; border-radius: 50%; background: #e11d48; color: white; font-weight: 900; }
.rejection-guidance strong { color: #fecdd3; font-size: 10px; }
.rejection-guidance p { margin-top: 5px; color: #d9a8b2; font-size: 9px; line-height: 1.55; }
.rejection-guidance small { display: block; margin-top: 7px; color: #a86f7a; font-size: 8px; }
.empty {
    padding: 25px;
    color: #66748a;
    text-align: center;
    font-size: 10px;
}
.flash-message { margin-top: 14px; border: 1px solid #34d39955; border-radius: 12px; background: #123126; padding: 12px 15px; color: #6ee7b7; font-size: 11px; }
.header-actions { display: flex; align-items: center; gap: 10px; }
.edit-button { border: 1px solid #ff7c2e; border-radius: 99px; padding: 9px 15px; color: #ff9a5b; font-size: 10px; font-weight: 800; }
.edit-button:hover { background: #ff6a00; color: #101827; }
.delete-button { border: 1px solid #ef444477; border-radius: 99px; padding: 9px 15px; color: #f87171; font-size: 10px; font-weight: 800; transition: .2s ease; }
.delete-button:hover { background: #dc2626; border-color: #dc2626; color: white; }
.delete-overlay { position: fixed; inset: 0; z-index: 100; display: grid; place-items: center; padding: 20px; background: #02050be0; backdrop-filter: blur(9px); }
.delete-modal { position: relative; width: min(520px, 100%); padding: 34px; border: 1px solid #ef444455; border-radius: 24px; background: #111a2a; box-shadow: 0 28px 80px #000a; text-align: center; }
.delete-close { position: absolute; top: 15px; right: 17px; color: #94a3b8; font-size: 22px; }
.delete-icon { display: grid; place-items: center; width: 56px; height: 56px; margin: 0 auto 17px; border-radius: 50%; background: #dc2626; color: white; font-size: 25px; font-weight: 900; }
.delete-modal > p { color: #f87171; font-size: 10px; font-weight: 900; letter-spacing: .2em; }
.delete-modal h2 { margin: 9px 0 12px; color: white; font-size: 25px; }
.delete-modal > span { display: block; color: #aab6c8; font-size: 13px; line-height: 1.7; }
.delete-modal > span b { color: white; }
.delete-actions { display: grid; grid-template-columns: 1fr 1.5fr; gap: 10px; margin-top: 26px; }
.delete-actions button { min-height: 47px; border-radius: 12px; font-size: 11px; font-weight: 900; }
.delete-cancel { border: 1px solid #334155; color: #cbd5e1; }
.delete-confirm { background: #dc2626; color: white; }
.delete-actions button:disabled { cursor: wait; opacity: .55; }
.edit-overlay { position: fixed; inset: 0; z-index: 80; display: grid; place-items: center; padding: 20px; background: #02050bcc; backdrop-filter: blur(8px); }
.edit-modal { display: flex; width: min(980px, 100%); max-height: calc(100dvh - 40px); overflow: hidden; border: 1px solid #303b4e; border-radius: 20px; flex-direction: column; background: #111827; box-shadow: 0 30px 100px #000d; }
.edit-header { display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 1px solid #263044; padding: 22px 26px; }
.edit-header p { color: #ff7c2e; font-size: 9px; font-weight: 800; letter-spacing: .18em; }
.edit-header h2 { margin-top: 5px; font-size: 22px; }
.edit-header span { display: block; margin-top: 5px; color: #718096; font-size: 10px; }
.edit-header > button { color: #94a3b8; font-size: 26px; line-height: 1; }
.edit-scroll { overflow-y: auto; padding: 4px 26px 24px; }
.edit-section { padding-top: 22px; }
.edit-section + .edit-section { margin-top: 22px; border-top: 1px solid #263044; }
.edit-section h3 { margin-bottom: 15px; font-size: 15px; }
.edit-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.edit-grid .wide { grid-column: 1/-1; }
.edit-grid label { display: grid; gap: 7px; color: #9ca8ba; font-size: 10px; font-weight: 700; }
.edit-grid input, .edit-grid select, .edit-grid textarea, .writer-row input, .writer-row select { width: 100%; border: 1px solid #2a3548; border-radius: 10px; background: #0b1220; padding: 11px 12px; color: #edf2f7; font-size: 11px; }
.edit-grid input:focus, .edit-grid select:focus, .edit-grid textarea:focus, .writer-row input:focus, .writer-row select:focus { border-color: #ff7c2e; outline: 0; box-shadow: 0 0 0 3px #ff6a0017; }
.edit-grid textarea { min-height: 90px; resize: vertical; }
.edit-grid .lyrics-editor { min-height: 150px; }
.writer-heading { display: flex; align-items: center; justify-content: space-between; }
.writer-heading button { border-radius: 9px; background: #2e1c1c; padding: 7px 10px; color: #ff9a5b; font-size: 10px; font-weight: 800; }
.writer-row { display: grid; grid-template-columns: 1fr 220px auto; gap: 10px; margin-top: 10px; }
.writer-row button { border: 1px solid #fb718544; border-radius: 9px; padding: 0 11px; color: #fb7185; font-size: 10px; }
.writer-row button:disabled { opacity: .35; }
.edit-errors { margin-top: 20px; border: 1px solid #fb718544; border-radius: 12px; background: #3c2025; padding: 13px; color: #fda4af; font-size: 10px; }
.edit-errors p { margin-top: 4px; }
.edit-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #263044; padding: 16px 26px; }
.edit-footer button { border-radius: 11px; padding: 11px 18px; font-size: 11px; font-weight: 800; }
.edit-footer .cancel { border: 1px solid #344054; color: #aeb8c8; }
.edit-footer .save-edit { background: #ff6a00; color: #101827; }
.edit-footer .save-edit:disabled { opacity: .5; }
.status-success-overlay { position: fixed; inset: 0; z-index: 100; display: grid; place-items: center; padding: 20px; background: #02050bd1; backdrop-filter: blur(9px); }
.status-success-modal { position: relative; width: min(460px, 100%); overflow: hidden; border: 1px solid #34d39955; border-radius: 24px; background: linear-gradient(150deg, #14211e, #111827 62%); padding: 34px; text-align: center; box-shadow: 0 35px 110px #000d, 0 0 70px #10b98117; animation: status-arrive .3s ease-out both; }
.status-success-modal:before { position: absolute; inset: 0 0 auto; height: 5px; background: linear-gradient(90deg, #10b981, #4ade80, #ff7c2e); content: ""; }
.success-close { position: absolute; top: 17px; right: 19px; color: #7e8b9e; font-size: 23px; line-height: 1; }
.success-check { display: grid; width: 64px; height: 64px; margin: 4px auto 20px; place-items: center; border-radius: 50%; background: linear-gradient(135deg, #34d399, #059669); box-shadow: 0 0 0 10px #10b98113, 0 15px 35px #10b98133; }
.success-check svg { width: 34px; fill: none; stroke: white; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
.success-eyebrow { color: #6ee7b7; font-size: 9px; font-weight: 900; letter-spacing: .2em; }
.status-success-modal h2 { margin-top: 8px; font-size: 24px; }
.success-copy { max-width: 340px; margin: 9px auto 0; color: #8491a4; font-size: 11px; line-height: 1.6; }
.status-result { display: grid; gap: 6px; margin-top: 22px; border: 1px solid #34d39933; border-radius: 15px; background: #0b1715; padding: 16px; }
.status-result small, .reason-result small { color: #66758a; font-size: 8px; font-weight: 800; letter-spacing: .14em; }
.status-result strong { color: #6ee7b7; font-size: 18px; }
.status-result span { color: #718096; font-family: ui-monospace, monospace; font-size: 10px; }
.reason-result { margin-top: 10px; border-radius: 13px; background: #ffffff08; padding: 13px; text-align: left; }
.reason-result p { margin-top: 6px; color: #b7c0ce; font-size: 11px; line-height: 1.55; }
.success-action { width: 100%; margin-top: 18px; border-radius: 12px; background: linear-gradient(90deg, #10b981, #34d399); padding: 12px; color: #062b20; font-size: 11px; font-weight: 900; }
@keyframes status-arrive { from { opacity: 0; transform: translateY(18px) scale(.96); } to { opacity: 1; transform: none; } }
@media (max-width: 1050px) {
    .detail-page > header { grid-template-columns: 1fr 1fr; align-items: start; }
    .current-status-card { grid-column: 1/-1; grid-row: 2; }
    .header-actions { justify-content: flex-end; }
    .layout {
        grid-template-columns: 1fr;
    }
    .status-panel {
        position: static;
    }
}
@media (max-width: 650px) {
    .detail-page > header {
        grid-template-columns: 1fr;
    }
    .current-status-card { grid-column: auto; grid-row: auto; width: 100%; }
    .detail-page h1 {
        font-size: 29px;
    }
    .registration-meta { align-items: stretch; flex-direction: column; }
    .registration-number { width: 100%; min-width: 0; }
    .data-grid {
        grid-template-columns: 1fr;
    }
    .data-grid .wide {
        grid-column: auto;
    }
    .panel {
        padding: 20px;
    }
    .header-actions { align-items: flex-start; flex-direction: column-reverse; }
    .edit-overlay { padding: 0; }
    .edit-modal { max-height: 100dvh; border-radius: 0; }
    .edit-header, .edit-scroll, .edit-footer { padding-left: 16px; padding-right: 16px; }
    .edit-grid, .writer-row { grid-template-columns: 1fr; }
    .edit-grid .wide { grid-column: auto; }
    .writer-row button { padding: 10px; }
    .links a, .files a { grid-template-columns: 38px minmax(0, 1fr); }
    .open-action, .download-action { grid-column: 1/-1; text-align: center; }
}
</style>
