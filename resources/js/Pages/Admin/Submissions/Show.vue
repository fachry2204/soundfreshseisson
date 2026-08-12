<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
defineOptions({ layout: AdminLayout });
const props = defineProps<{
    submission: any;
    statuses: { value: string; label: string }[];
}>();
const statusForm = useForm({ status: props.submission.status, reason: "" });
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
</script>
<template>
    <Head :title="`Detail ${submission.registration_number}`" />
    <div class="detail-page">
        <Link href="/admin/submissions" class="back"
            >← Kembali ke Data Pendaftar</Link
        >
        <header>
            <div>
                <p>DETAIL PENDAFTARAN</p>
                <h1>{{ submission.song?.title || "Tanpa Judul" }}</h1>
                <span
                    >{{ submission.registration_number }} · Dikirim
                    {{
                        submission.submitted_at
                            ? new Date(submission.submitted_at).toLocaleString(
                                  "id-ID",
                              )
                            : "-"
                    }}</span
                >
            </div>
            <span :class="['main-status', submission.status]">{{
                statusLabels[submission.status] || submission.status
            }}</span>
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
                                {{ submission.applicant?.birth_date }}
                            </dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd>{{ submission.applicant?.email }}</dd>
                        </div>
                        <div>
                            <dt>WhatsApp</dt>
                            <dd>{{ submission.applicant?.whatsapp }}</dd>
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
                        <div>
                            <dt>Instagram</dt>
                            <dd>
                                {{ submission.applicant?.instagram || "-" }}
                            </dd>
                        </div>
                        <div>
                            <dt>TikTok</dt>
                            <dd>{{ submission.applicant?.tiktok || "-" }}</dd>
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
                            <dd>
                                <a
                                    :href="submission.song?.artist_social_url"
                                    target="_blank"
                                    rel="noopener"
                                    >{{
                                        submission.song?.artist_social_url ||
                                        "-"
                                    }}</a
                                >
                            </dd>
                        </div>
                        <div>
                            <dt>Spotify artis</dt>
                            <dd>
                                <a
                                    v-if="submission.song?.artist_spotify_url"
                                    :href="submission.song.artist_spotify_url"
                                    target="_blank"
                                    rel="noopener"
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
                                <b>Link {{ link.type }}</b
                                ><small>{{ link.url }}</small>
                            </div></a
                        >
                    </div>
                    <div class="files">
                        <a
                            v-for="file in submission.files"
                            :key="file.id"
                            :href="`/admin/files/${file.id}`"
                            ><span>↓</span>
                            <div>
                                <b>{{ file.original_name }}</b
                                ><small
                                    >{{ file.type }} · {{ file.mime }} ·
                                    {{ fileSize(file.size) }} · Scan:
                                    {{ file.scan_status }}</small
                                >
                            </div></a
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
                    @submit.prevent="
                        statusForm.patch(
                            `/admin/submissions/${submission.id}/status`,
                            { preserveScroll: true },
                        )
                    "
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
                        >Catatan perubahan<textarea
                            v-model="statusForm.reason"
                            required
                            placeholder="Tuliskan alasan atau catatan status"
                        ></textarea>
                    </label>
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
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
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
    grid-template-columns: 38px 1fr;
    align-items: center;
    gap: 12px;
    border: 1px solid #253044;
    border-radius: 13px;
    padding: 12px;
    background: #0c1421;
}
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
.empty {
    padding: 25px;
    color: #66748a;
    text-align: center;
    font-size: 10px;
}
@media (max-width: 1050px) {
    .layout {
        grid-template-columns: 1fr;
    }
    .status-panel {
        position: static;
    }
}
@media (max-width: 650px) {
    .detail-page > header {
        align-items: flex-start;
        flex-direction: column;
    }
    .detail-page h1 {
        font-size: 29px;
    }
    .data-grid {
        grid-template-columns: 1fr;
    }
    .data-grid .wide {
        grid-column: auto;
    }
    .panel {
        padding: 20px;
    }
}
</style>
