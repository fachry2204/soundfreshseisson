<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import axios from "axios";
import FileDropzone from "@/Components/FileDropzone.vue";
defineProps<{
    period?: { name: string; status: string; closes_at: string };
    flash?: { error?: string };
}>();
const step = ref(1);
const genres = [
    "Alternative/Indie",
    "Latin",
    "Classical",
    "Country",
    "Blues",
    "Electronic",
    "Folk",
    "Hip Hop/Rap",
    "Jazz",
    "New Age",
    "Pop",
    "R&B/Soul",
    "Reggae",
    "Rock",
    "World",
    "Childhood",
    "Devotional/Inspirational",
    "Dance",
    "Soundtrack",
];
const form = useForm({
    full_name: "",
    nik: "",
    birth_place: "",
    birth_date: "",
    email: "",
    whatsapp: "",
    province: "",
    city: "",
    district: "",
    village: "",
    postal_code: "",
    address: "",
    ktp: null as File | null,
    title: "",
    genre: "",
    language: "Indonesia",
    creation_year: new Date().getFullYear(),
    story: "",
    lyrics: "",
    video_url: "",
    upload_tokens: [] as { id: string; token: string; type: "video" }[],
    terms: false,
    idempotency_key: crypto.randomUUID(),
});
const fields: Record<number, (keyof typeof form)[]> = {
    1: [
        "full_name",
        "nik",
        "birth_place",
        "birth_date",
        "email",
        "whatsapp",
        "province",
        "city",
        "district",
        "village",
        "postal_code",
        "address",
        "ktp",
    ],
    2: ["title", "genre", "language", "creation_year", "story", "video_url"],
    3: ["terms"],
};
const fieldSteps: Record<string, number> = {
    full_name: 1,
    nik: 1,
    birth_place: 1,
    birth_date: 1,
    email: 1,
    whatsapp: 1,
    province: 1,
    city: 1,
    district: 1,
    village: 1,
    postal_code: 1,
    address: 1,
    ktp: 1,
    title: 2,
    genre: 2,
    language: 2,
    creation_year: 2,
    story: 2,
    lyrics: 2,
    video_url: 2,
    upload_tokens: 2,
    terms: 3,
};
const errorStep = (field: string) => fieldSteps[field.split(".")[0]] || 4;
const stepErrors = computed(() =>
    Object.entries(form.errors).filter(
        ([field]) => errorStep(field) === step.value,
    ),
);
type RegionOption = { id: string; name: string };
const provinces = ref<RegionOption[]>([]),
    cities = ref<RegionOption[]>([]),
    districts = ref<RegionOption[]>([]),
    villages = ref<RegionOption[]>([]),
    postalCodes = ref<RegionOption[]>([]);
const provinceId = ref(""),
    cityId = ref(""),
    districtId = ref(""),
    villageId = ref("");
const regionLoading = ref("provinces");
const regionError = ref("");
async function getRegions(path: string) {
    const response = await axios.get<{ data: RegionOption[] }>(
        `/api/regions/${path}`,
    );
    return response.data.data;
}
async function loadProvinces() {
    try {
        provinces.value = await getRegions("provinces");
    } catch {
        regionError.value =
            "Pilihan wilayah gagal dimuat. Muat ulang halaman untuk mencoba lagi.";
    } finally {
        regionLoading.value = "";
    }
}
async function changeProvince() {
    regionError.value = "";
    form.province =
        provinces.value.find((item) => item.id === provinceId.value)?.name ||
        "";
    cityId.value = "";
    districtId.value = "";
    villageId.value = "";
    form.city = form.district = form.village = form.postal_code = "";
    cities.value = [];
    districts.value = [];
    villages.value = [];
    postalCodes.value = [];
    if (!provinceId.value) return;
    regionLoading.value = "cities";
    try {
        cities.value = await getRegions(`cities/${provinceId.value}`);
    } catch {
        regionError.value = "Kota/Kabupaten gagal dimuat.";
    } finally {
        regionLoading.value = "";
    }
}
async function changeCity() {
    regionError.value = "";
    form.city =
        cities.value.find((item) => item.id === cityId.value)?.name || "";
    districtId.value = "";
    villageId.value = "";
    form.district = form.village = form.postal_code = "";
    districts.value = [];
    villages.value = [];
    postalCodes.value = [];
    if (!cityId.value) return;
    regionLoading.value = "districts";
    try {
        districts.value = await getRegions(`districts/${cityId.value}`);
    } catch {
        regionError.value = "Kecamatan gagal dimuat.";
    } finally {
        regionLoading.value = "";
    }
}
async function changeDistrict() {
    regionError.value = "";
    form.district =
        districts.value.find((item) => item.id === districtId.value)?.name ||
        "";
    villageId.value = "";
    form.village = form.postal_code = "";
    villages.value = [];
    postalCodes.value = [];
    if (!districtId.value) return;
    regionLoading.value = "villages";
    try {
        villages.value = await getRegions(`villages/${districtId.value}`);
    } catch {
        regionError.value = "Kelurahan/Desa gagal dimuat.";
    } finally {
        regionLoading.value = "";
    }
}
async function changeVillage() {
    regionError.value = "";
    form.village =
        villages.value.find((item) => item.id === villageId.value)?.name || "";
    form.postal_code = "";
    postalCodes.value = [];
    if (!villageId.value) return;
    regionLoading.value = "postal";
    try {
        postalCodes.value = await getRegions(`postal-codes/${villageId.value}`);
        if (postalCodes.value.length === 1)
            form.postal_code = postalCodes.value[0].name;
    } catch {
        regionError.value = "Kode pos gagal dimuat.";
    } finally {
        regionLoading.value = "";
    }
}
onMounted(loadProvinces);
const consentError = ref("");
function next() {
    const missing = (fields[step.value] || []).some((k) => !form[k]);
    const uploadMissing =
        step.value === 2 && !form.upload_tokens.some((v) => v.type === "video");
    let videoHost = "";
    try {
        videoHost = new URL(form.video_url).hostname.toLowerCase();
    } catch {
        // Native URL validation handles malformed URLs.
    }
    const isYoutube =
        videoHost === "youtu.be" ||
        videoHost === "youtube.com" ||
        videoHost.endsWith(".youtube.com");
    if (step.value === 3 && !form.terms) {
        consentError.value =
            "Persetujuan ketentuan dan persyaratan wajib dicentang.";
        return;
    }
    if (step.value === 2 && isYoutube) {
        form.setError(
            "video_url",
            "Link video tidak boleh berasal dari YouTube.",
        );
        return;
    }
    if (uploadMissing) {
        form.setError("upload_tokens", "Upload file video wajib dilakukan.");
        return;
    }
    if (!missing) {
        form.clearErrors();
        step.value++;
    }
}
function submit() {
    form.post("/registration/drafts", {
        preserveScroll: true,
        onError: (errors) => {
            const errorSteps = Object.keys(errors).map(errorStep);
            if (errorSteps.length) step.value = Math.min(...errorSteps);
        },
    });
}
type UploadState = {
    id?: string;
    token?: string;
    progress: number;
    status: string;
    error?: string;
};
const uploads = ref<Record<"video", UploadState>>({
    video: { progress: 0, status: "idle" },
});
const ktpMeta = ref<{ name: string; size: number }>({ name: "", size: 0 });
const mediaMeta = ref<Record<"video", { name: string; size: number }>>({
    video: { name: "", size: 0 },
});
function selectKtp(file: File) {
    form.ktp = file;
    ktpMeta.value = { name: file.name, size: file.size };
}
function removeKtp() {
    form.ktp = null;
    ktpMeta.value = { name: "", size: 0 };
}
async function sha256(file: File) {
    const hash = await crypto.subtle.digest(
        "SHA-256",
        await file.arrayBuffer(),
    );
    return [...new Uint8Array(hash)]
        .map((v) => v.toString(16).padStart(2, "0"))
        .join("");
}
async function uploadLarge(file: File, type: "video") {
    const state = uploads.value[type];
    const allowedVideoTypes = ["video/mp4", "video/quicktime", "video/webm"];
    if (!allowedVideoTypes.includes(file.type)) {
        mediaMeta.value[type] = { name: file.name, size: file.size };
        state.status = "failed";
        state.progress = 0;
        state.error = "File harus berformat video MP4, MOV, atau WebM.";
        form.upload_tokens = form.upload_tokens.filter(
            (item) => item.type !== type,
        );
        return;
    }
    mediaMeta.value[type] = { name: file.name, size: file.size };
    state.status = "preparing";
    state.progress = 0;
    state.error = "";
    // Keep chunks comfortably below common Plesk/PHP request limits.
    const chunkSize = 512 * 1024;
    try {
        const checksum = await sha256(file);
        const init = await axios.post("/registration/uploads/init", {
            type,
            name: file.name,
            mime: file.type,
            size: file.size,
            chunk_size: chunkSize,
            checksum,
        });
        state.id = init.data.id;
        state.token = init.data.token;
        state.status = "uploading";
        for (let index = 0; index < init.data.total_chunks; index++) {
            const data = new FormData();
            data.append("index", String(index));
            data.append(
                "chunk",
                file.slice(
                    index * chunkSize,
                    Math.min(file.size, (index + 1) * chunkSize),
                ),
                "chunk.part",
            );
            let attempt = 0;
            while (true) {
                try {
                    await axios.post(
                        `/registration/uploads/${state.id}/chunk`,
                        data,
                        { headers: { "X-Upload-Token": state.token } },
                    );
                    break;
                } catch (error) {
                    if (++attempt >= 3) throw error;
                }
            }
            state.progress = Math.round(
                ((index + 1) / init.data.total_chunks) * 100,
            );
        }
        await axios.post(
            `/registration/uploads/${state.id}/complete`,
            {},
            { headers: { "X-Upload-Token": state.token } },
        );
        form.upload_tokens = form.upload_tokens.filter(
            (item) => item.type !== type,
        );
        form.upload_tokens.push({ id: state.id!, token: state.token!, type });
        state.status = "completed";
    } catch (error: any) {
        state.status = "failed";
        state.error =
            error.response?.data?.message || "Upload gagal. Coba lagi.";
    }
}
async function cancelUpload(type: "video") {
    const state = uploads.value[type];
    if (state.id && state.token)
        await axios.delete(`/registration/uploads/${state.id}`, {
            headers: { "X-Upload-Token": state.token },
        });
    form.upload_tokens = form.upload_tokens.filter(
        (item) => item.type !== type,
    );
    uploads.value[type] = { progress: 0, status: "idle" };
    mediaMeta.value[type] = { name: "", size: 0 };
}
</script>
<template>
    <Head title="Kirim Lagu" />
    <div class="min-h-screen bg-[#080808] text-white">
        <header class="border-b border-white/10 px-5 py-5">
            <div class="mx-auto flex max-w-5xl justify-between">
                <Link href="/" class="font-display font-bold"
                    >← ORIGINAL
                    <span class="text-orange-500">SESSIONS</span></Link
                ><span class="text-sm text-neutral-500"
                    >Langkah {{ step }} dari 4</span
                >
            </div>
        </header>
        <main class="mx-auto max-w-4xl px-5 py-12">
            <div
                v-if="flash?.error"
                role="alert"
                class="mb-8 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-sm text-amber-200"
            >
                {{ flash.error }}
            </div>
            <div class="progress-shell mb-7">
                <div
                    class="h-full bg-orange-500 transition-all"
                    :style="{ width: step * 25 + '%' }"
                ></div>
            </div>
            <section class="step-card">
                <p class="eyebrow">{{ period?.name || "Original Sessions" }}</p>
                <h1 class="mt-3 font-display text-4xl font-bold">
                    {{
                        [
                            "Data diri",
                            "Data lagu & video",
                            "Deklarasi & persetujuan",
                            "Review dan kirim",
                        ][step - 1]
                    }}
                </h1>
                <form
                    class="mt-10 space-y-6"
                    @submit.prevent="step === 4 ? submit() : next()"
                >
                    <template v-if="step === 1"
                        ><label
                            >Nama lengkap sesuai KTP<input
                                v-model="form.full_name"
                                required
                        /></label>
                        <div class="grid gap-5 md:grid-cols-2">
                            <label
                                >NIK<input
                                    v-model="form.nik"
                                    inputmode="numeric"
                                    minlength="16"
                                    maxlength="16"
                                    required /></label
                            ><label
                                >Tempat lahir<input
                                    v-model="form.birth_place"
                                    required /></label
                            ><label
                                >Tanggal lahir<input
                                    v-model="form.birth_date"
                                    type="date"
                                    required /></label
                            ><label
                                >Email<input
                                    v-model="form.email"
                                    type="email"
                                    required /></label
                            ><label
                                >Nomor WhatsApp<input
                                    v-model="form.whatsapp"
                                    placeholder="0812..."
                                    pattern="(?:\+?62|0)8[1-9][0-9]{6,11}"
                                    title="Gunakan nomor WhatsApp Indonesia, misalnya 081234567890"
                                    @input="form.clearErrors('whatsapp')"
                                    required /></label
                            ><label
                                >Provinsi<select
                                    v-model="provinceId"
                                    required
                                    :disabled="regionLoading === 'provinces'"
                                    @change="changeProvince"
                                >
                                    <option value="" disabled>
                                        {{
                                            regionLoading === "provinces"
                                                ? "Memuat provinsi…"
                                                : "Pilih provinsi"
                                        }}
                                    </option>
                                    <option
                                        v-for="item in provinces"
                                        :key="item.id"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select></label
                            ><label
                                >Kota/Kabupaten<select
                                    v-model="cityId"
                                    required
                                    :disabled="
                                        !provinceId ||
                                        regionLoading === 'cities'
                                    "
                                    @change="changeCity"
                                >
                                    <option value="" disabled>
                                        {{
                                            regionLoading === "cities"
                                                ? "Memuat kota/kabupaten…"
                                                : "Pilih kota/kabupaten"
                                        }}
                                    </option>
                                    <option
                                        v-for="item in cities"
                                        :key="item.id"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select></label
                            ><label
                                >Kecamatan<select
                                    v-model="districtId"
                                    required
                                    :disabled="
                                        !cityId || regionLoading === 'districts'
                                    "
                                    @change="changeDistrict"
                                >
                                    <option value="" disabled>
                                        {{
                                            regionLoading === "districts"
                                                ? "Memuat kecamatan…"
                                                : "Pilih kecamatan"
                                        }}
                                    </option>
                                    <option
                                        v-for="item in districts"
                                        :key="item.id"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select></label
                            ><label
                                >Kelurahan/Desa<select
                                    v-model="villageId"
                                    required
                                    :disabled="
                                        !districtId ||
                                        regionLoading === 'villages'
                                    "
                                    @change="changeVillage"
                                >
                                    <option value="" disabled>
                                        {{
                                            regionLoading === "villages"
                                                ? "Memuat kelurahan…"
                                                : "Pilih kelurahan/desa"
                                        }}
                                    </option>
                                    <option
                                        v-for="item in villages"
                                        :key="item.id"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select></label
                            ><label
                                >Kode Pos<select
                                    v-model="form.postal_code"
                                    required
                                    :disabled="
                                        !villageId || regionLoading === 'postal'
                                    "
                                >
                                    <option value="" disabled>
                                        {{
                                            regionLoading === "postal"
                                                ? "Memuat kode pos…"
                                                : "Pilih kode pos"
                                        }}
                                    </option>
                                    <option
                                        v-for="item in postalCodes"
                                        :key="item.id"
                                        :value="item.name"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select></label
                            >
                        </div>
                        <p
                            v-if="regionError"
                            role="alert"
                            class="consent-error"
                        >
                            {{ regionError }}
                        </p>
                        <label
                            >Alamat lengkap<textarea
                                v-model="form.address"
                                required
                            ></textarea>
                        </label>
                        <div>
                            <p class="upload-label">Dokumen identitas</p>
                            <FileDropzone
                                accept="image/jpeg,image/png,application/pdf"
                                label="Upload KTP"
                                hint="Tarik file ke sini atau klik · JPG, PNG, PDF · Maks. 10 MB"
                                :selected-name="ktpMeta.name"
                                :selected-size="ktpMeta.size"
                                @select="selectKtp"
                                @remove="removeKtp"
                            /></div
                    ></template>
                    <template v-if="step === 2"
                        ><label
                            >Judul lagu<input v-model="form.title" required
                        /></label>
                        <div class="grid gap-5 md:grid-cols-2">
                            <label
                                >Genre utama<select
                                    v-model="form.genre"
                                    required
                                >
                                    <option value="" disabled>
                                        Pilih genre
                                    </option>
                                    <option
                                        v-for="genre in genres"
                                        :key="genre"
                                        :value="genre"
                                    >
                                        {{ genre }}
                                    </option>
                                </select></label
                            ><label
                                >Bahasa lagu<input
                                    v-model="form.language"
                                    required /></label
                            ><label
                                >Tahun penciptaan<input
                                    v-model="form.creation_year"
                                    type="number"
                                    min="1900"
                                    required
                            /></label>
                        </div>
                        <label
                            >Cerita di balik lagu<textarea
                                v-model="form.story"
                                minlength="50"
                                rows="6"
                                required
                            ></textarea></label
                        ><label
                            >Lirik lagu<textarea
                                v-model="form.lyrics"
                                rows="8"
                            ></textarea>
                        </label>
                        <div class="notice">
                            Link video dan upload file video sama-sama wajib.
                            Gunakan Google Drive, Dropbox, atau platform lain
                            selain YouTube dan pastikan tautan dapat dibuka tim.
                        </div>
                        <label
                            >Link video (wajib)<input
                                v-model="form.video_url"
                                type="url"
                                placeholder="https://drive.google.com/..."
                                required
                                @input="form.clearErrors('video_url')"
                            /><small class="field-help"
                                >YouTube tidak diperbolehkan. Pastikan tautan
                                dapat diakses oleh tim.</small
                            ></label
                        >
                        <div>
                            <p class="upload-label">
                                Upload video penampilan (wajib)
                            </p>
                            <FileDropzone
                                accept="video/mp4,video/quicktime,video/webm,.mp4,.mov,.webm"
                                label="Pilih file video"
                                hint="Tarik video ke sini atau klik · MP4, MOV, WebM · Maks. 500 MB"
                                :selected-name="mediaMeta.video.name"
                                :selected-size="mediaMeta.video.size"
                                :status="uploads.video.status"
                                :progress="uploads.video.progress"
                                :error="uploads.video.error"
                                @select="uploadLarge($event, 'video')"
                                @remove="cancelUpload('video')"
                            />
                            <p
                                v-if="form.errors.upload_tokens"
                                class="consent-error mt-3"
                                role="alert"
                            >
                                {{ form.errors.upload_tokens }}
                            </p>
                        </div></template
                    >
                    <template v-if="step === 3"
                        ><label class="check check-all"
                            ><input
                                v-model="form.terms"
                                type="checkbox"
                                required
                                @change="consentError = ''"
                            /><span
                                ><b
                                    >Saya Menyetujui Semua Ketentuan dan
                                    Persyaratan Yang berlaku</b
                                ></span
                            ></label
                        >
                        <p
                            v-if="consentError"
                            role="alert"
                            class="consent-error"
                        >
                            {{ consentError }}
                        </p>
                        ></template
                    >
                    <template v-if="step === 4"
                        ><div class="review">
                            <p><span>Nama</span>{{ form.full_name }}</p>
                            <p>
                                <span>Lagu</span>{{ form.title }} ·
                                {{ form.genre }}
                            </p>
                            <p>
                                <span>Kontak</span>{{ form.email }} ·
                                {{ form.whatsapp }}
                            </p>
                            <p>
                                <span>Video</span
                                >{{
                                    form.video_url ||
                                    (form.upload_tokens.some(
                                        (item) => item.type === "video",
                                    )
                                        ? "File video telah diupload"
                                        : "-")
                                }}
                            </p>
                        </div>
                        <p class="text-sm text-neutral-400">
                            Setelah dikirim, data menjadi snapshot dan perubahan
                            hanya dapat dilakukan melalui permintaan revisi.
                        </p></template
                    >
                    <div
                        v-if="stepErrors.length"
                        role="alert"
                        class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                    >
                        <p v-for="[field, error] in stepErrors" :key="field">
                            {{ error }}
                        </p>
                    </div>
                    <div class="form-actions flex justify-between pt-5">
                        <button
                            v-if="step > 1"
                            type="button"
                            class="back-button"
                            @click="step--"
                        >
                            Kembali</button
                        ><span v-else></span
                        ><button
                            class="cta disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="
                                form.processing || (step === 3 && !form.terms)
                            "
                        >
                            {{
                                step === 4
                                    ? form.processing
                                        ? "Mengirim…"
                                        : "Kirim Pendaftaran"
                                    : "Lanjut →"
                            }}
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</template>
<style scoped>
.progress-shell {
    height: 0.3rem;
    overflow: hidden;
    border-radius: 999px;
    background: #292929;
    box-shadow: inset 0 1px 2px #0008;
}
.step-card {
    position: relative;
    overflow: hidden;
    border: 1px solid #fff;
    background: #fff;
    padding: clamp(1.5rem, 5vw, 3.75rem);
    color: #171716;
    box-shadow:
        0 28px 80px #0008,
        0 0 0 1px #ffffff10;
    border-radius: 30px;
}
.step-card:before {
    position: absolute;
    inset: 0 0 auto;
    height: 4px;
    background: linear-gradient(90deg, #ff6a00, #ff9b4b 34%, transparent 72%);
    content: "";
}
.step-card .eyebrow {
    color: #e85f00;
}
.step-card h1 {
    color: #111110;
    letter-spacing: -0.025em;
}
label {
    display: grid;
    gap: 0.6rem;
    color: #242421;
    font-size: 0.875rem;
    font-weight: 700;
}
input,
textarea,
select {
    width: 100%;
    border: 1px solid #d8d8d2;
    background: #fff;
    border-radius: 14px;
    padding: 0.95rem 1rem;
    color: #171716;
    box-shadow: 0 1px 2px #1010100a;
    transition:
        border-color 0.18s ease,
        box-shadow 0.18s ease,
        background 0.18s ease;
}
input::placeholder,
textarea::placeholder {
    color: #999991;
}
input:hover,
textarea:hover,
select:hover {
    border-color: #b8b8b0;
}
input:focus,
textarea:focus,
select:focus {
    outline: 0;
    border-color: #ff6a00;
    box-shadow: 0 0 0 4px #ff6a0018;
}
select:disabled,
input:disabled {
    cursor: not-allowed;
    background: #f3f3f0;
    color: #8d8d86;
}
.field-help {
    color: #77776f;
    font-size: 0.78rem;
    font-weight: 400;
}
.upload-label {
    margin-bottom: 0.6rem;
    color: #242421;
    font-size: 0.875rem;
    font-weight: 700;
}
.check {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    border: 1px solid #ddddd7;
    background: #fff;
    border-radius: 16px;
    padding: 1rem;
    color: #292926;
    cursor: pointer;
    transition: 0.18s ease;
}
.check:hover {
    border-color: #bbbbaf;
    box-shadow: 0 5px 18px #1c1c160a;
}
.check input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    min-width: 1.25rem;
    margin-top: 0.05rem;
    padding: 0;
    border-radius: 0.35rem;
    accent-color: #ff6a00;
    box-shadow: none;
}
.check-all {
    border-color: #ffb47f;
    background: #fff7f1;
}
.check-all span {
    display: grid;
    gap: 0.25rem;
}
.check-all small {
    color: #6f6f68;
    font-weight: 400;
}
.consent-divider {
    height: 1px;
    background: #e7e7e2;
}
.consent-error {
    border-radius: 0.75rem;
    background: #fff1f1;
    padding: 0.8rem;
    color: #b42318;
    font-size: 0.875rem;
}
.notice {
    border: 1px solid #f1dfcf;
    border-left: 3px solid #ff6a00;
    border-radius: 14px;
    background: #fff8f2;
    padding: 1rem;
    color: #62574e;
}
.review {
    display: grid;
    gap: 1rem;
    border: 1px solid #deded8;
    background: #fafaf8;
    border-radius: 20px;
    padding: 1.5rem;
}
.review p {
    display: grid;
    gap: 0.3rem;
}
.review span {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: #e85f00;
}
.back-button {
    border: 1px solid #d7d7d1;
    border-radius: 999px;
    padding: 0.8rem 1.4rem;
    color: #343431;
    font-weight: 700;
    transition: 0.18s ease;
}
.back-button:hover {
    border-color: #a9a9a1;
    background: #f6f6f3;
}
.step-card :deep(.file-zone) {
    border-color: #d8d8d2;
    background: #fff;
}
.step-card :deep(.file-zone:hover),
.step-card :deep(.file-zone.dragging) {
    border-color: #ff6a00;
    box-shadow: 0 0 0 4px #ff6a0014;
}
.step-card :deep(.upload-icon) {
    border-color: #e1e1db;
    background: #f6f6f3;
    color: #e85f00;
}
.step-card :deep(.copy strong) {
    color: #242421;
}
.step-card :deep(.copy small),
.step-card :deep(.file-actions) {
    color: #77776f;
}
.step-card :deep(.file-actions) {
    border-top-color: #e8e8e3;
}
.step-card :deep(.change-pill) {
    border-color: #d6d6d0;
    color: #44443f;
}
.step-card :deep(.progress-track) {
    background: #e6e6e0;
}
@media (max-width: 640px) {
    main {
        padding-top: 1.75rem;
        padding-bottom: 2.5rem;
    }
    .step-card {
        border-radius: 22px;
        padding: 1.35rem;
    }
    .step-card h1 {
        font-size: 2.1rem;
    }
    .form-actions {
        align-items: center;
        gap: 1rem;
    }
    .form-actions .cta,
    .back-button {
        padding: 0.75rem 1.1rem;
        font-size: 0.86rem;
    }
}
</style>
