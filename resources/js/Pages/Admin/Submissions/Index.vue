<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
defineOptions({ layout: AdminLayout });
const props = defineProps<{ submissions: any; filters: any }>();
const search = ref(props.filters.search || "");
const status = ref(props.filters.status || "");
const labels: Record<string, string> = {
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
function filter() {
    router.get(
        "/admin/submissions",
        { search: search.value, status: status.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Data Pendaftar" />
    <div class="submission-page">
        <header>
            <p>ADMIN PROGRAM</p>
            <h1>Data Pendaftar</h1>
            <span>Kelola seluruh pendaftaran Original Sessions.</span>
        </header>
        <form class="filters" @submit.prevent="filter">
            <input
                v-model="search"
                placeholder="Cari nama, artis, atau lagu"
            /><select v-model="status">
                <option value="">Semua status</option>
                <option value="pending">Pending</option>
                <option value="review">Di Review</option>
                <option value="accepted">Diterima</option>
                <option value="rejected">Ditolak</option></select
            ><button>Filter</button>
        </form>
        <section class="table-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Nama Artis</th>
                            <th>Judul Lagu</th>
                            <th>Asal Kota</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in submissions.data"
                            :key="item.id"
                        >
                            <td class="number">
                                {{ submissions.from + index }}
                            </td>
                            <td>
                                <b>{{ item.applicant?.full_name || "-" }}</b
                                ><small>{{ item.registration_number }}</small>
                            </td>
                            <td>
                                {{
                                    item.song?.artist_name ||
                                    item.applicant?.stage_name ||
                                    "-"
                                }}
                            </td>
                            <td>{{ item.song?.title || "-" }}</td>
                            <td>{{ item.applicant?.city || "-" }}</td>
                            <td>
                                <span :class="['badge', item.status]">{{
                                    labels[item.status] || item.status
                                }}</span>
                            </td>
                            <td>
                                <Link
                                    :href="`/admin/submissions/${item.id}`"
                                    class="action"
                                    >Lihat Data →</Link
                                >
                            </td>
                        </tr>
                        <tr v-if="!submissions.data.length">
                            <td colspan="7" class="empty">
                                Belum ada data pendaftar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <div v-if="submissions.links?.length > 3" class="pagination">
            <Link
                v-for="link in submissions.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="{ active: link.active, disabled: !link.url }"
                v-html="link.label"
            />
        </div>
    </div>
</template>
<style scoped>
.submission-page {
    max-width: 1500px;
    margin: auto;
}
.submission-page header p {
    color: #ff7c2e;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.2em;
}
.submission-page h1 {
    margin-top: 7px;
    font-size: 36px;
}
.submission-page header span {
    display: block;
    margin-top: 5px;
    color: #748196;
    font-size: 13px;
}
.filters {
    display: flex;
    gap: 10px;
    margin-top: 28px;
}
.filters input,
.filters select {
    border: 1px solid #293549;
    border-radius: 12px;
    background: #111827;
    padding: 12px 14px;
    color: #eef2f7;
    font-size: 12px;
}
.filters input {
    width: min(360px, 100%);
}
.filters button {
    border-radius: 12px;
    background: #ff6a00;
    padding: 0 21px;
    color: #0b101c;
    font-size: 12px;
    font-weight: 800;
}
.table-panel {
    margin-top: 20px;
    overflow: hidden;
    border: 1px solid #202b3e;
    border-radius: 20px;
    background: #111827;
}
.table-wrap {
    overflow-x: auto;
}
table {
    width: 100%;
    min-width: 1050px;
    text-align: left;
}
th,
td {
    border-top: 1px solid #202a3b;
    padding: 16px 20px;
}
thead th {
    border: 0;
    color: #647086;
    font-size: 9px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
td {
    color: #b5bfce;
    font-size: 12px;
}
td b,
td small {
    display: block;
}
td b {
    color: #f1f5f9;
    font-size: 12px;
}
td small {
    margin-top: 4px;
    color: #66748a;
    font-family: monospace;
    font-size: 9px;
}
.number {
    color: #ff8440;
    font-family: monospace;
}
.badge {
    display: inline-flex;
    border-radius: 99px;
    padding: 6px 10px;
    background: #33271a;
    color: #fbbf24;
    font-size: 9px;
    font-weight: 800;
}
.badge.administrative_review,
.badge.eligible,
.badge.curation,
.badge.shortlisted,
.badge.revision_requested {
    background: #172d45;
    color: #60a5fa;
}
.badge.selected {
    background: #16372e;
    color: #4ade80;
}
.badge.not_selected,
.badge.disqualified {
    background: #3b1d25;
    color: #fb7185;
}
.action {
    display: inline-flex;
    border: 1px solid #ff6a0044;
    border-radius: 9px;
    padding: 8px 11px;
    color: #ff8a45;
    font-size: 10px;
    font-weight: 800;
}
.action:hover {
    background: #ff6a00;
    color: #111827;
}
.empty {
    padding: 55px !important;
    color: #68758a;
    text-align: center;
}
.pagination {
    display: flex;
    gap: 5px;
    margin-top: 17px;
}
.pagination a {
    border: 1px solid #263246;
    border-radius: 8px;
    padding: 7px 10px;
    color: #8290a5;
    font-size: 10px;
}
.pagination a.active {
    border-color: #ff6a00;
    background: #ff6a00;
    color: #111827;
}
.pagination a.disabled {
    pointer-events: none;
    opacity: 0.35;
}
@media (max-width: 650px) {
    .filters {
        flex-direction: column;
    }
    .filters input {
        width: 100%;
    }
    .filters button {
        padding: 12px;
    }
    .submission-page h1 {
        font-size: 30px;
    }
}
</style>
