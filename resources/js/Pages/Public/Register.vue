<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import axios from "axios";
import FileDropzone from "@/Components/FileDropzone.vue";
const props = defineProps<{
    flash?: { error?: string };
    videoUploadDisabled?: boolean;
}>();
const step = ref(1);
const formTop = ref<HTMLElement | null>(null);
async function scrollToFormTop() {
    await nextTick();
    const reduceMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;
    (formTop.value || document.documentElement).scrollIntoView({
        behavior: reduceMotion ? "auto" : "smooth",
        block: "start",
    });
}
function previousStep() {
    if (step.value > 1) {
        step.value--;
        scrollToFormTop();
    }
}
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
    artist_name: "",
    artist_social_url: "",
    artist_spotify_url: "",
    songwriters: [{ name: "", role: "composer_author" }],
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
    2: [
        "title",
        "artist_name",
        "artist_social_url",
        "genre",
        "language",
        "creation_year",
        "story",
    ],
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
    artist_name: 2,
    artist_social_url: 2,
    artist_spotify_url: 2,
    songwriters: 2,
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
const errorModalOpen = ref(false);
const errorModalMessages = ref<string[]>([]);
const termsModalOpen = ref(false);
function showErrorModal(messages: string | string[]) {
    const unique = [
        ...new Set(
            (Array.isArray(messages) ? messages : [messages]).filter(Boolean),
        ),
    ];
    if (!unique.length) return;
    errorModalMessages.value = unique;
    errorModalOpen.value = true;
}
function closeErrorModal() {
    errorModalOpen.value = false;
}
function handleEscape(event: KeyboardEvent) {
    if (event.key === "Escape") {
        closeErrorModal();
        termsModalOpen.value = false;
    }
}
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
watch(regionError, (message) => {
    if (message) showErrorModal(message);
});
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
onMounted(() => {
    loadProvinces();
    window.addEventListener("keydown", handleEscape);
    if (props.flash?.error) showErrorModal(props.flash.error);
});
onUnmounted(() => window.removeEventListener("keydown", handleEscape));
const consentError = ref("");
const songwriterRoles = [
    { value: "composer", label: "Composer" },
    { value: "author", label: "Author" },
    { value: "composer_author", label: "Composer & Author" },
];
function addSongwriter() {
    form.songwriters.push({ name: "", role: "composer_author" });
    form.clearErrors("songwriters");
}
function removeSongwriter(index: number) {
    if (form.songwriters.length > 1) form.songwriters.splice(index, 1);
}
function next() {
    const missing = (fields[step.value] || []).some((k) => !form[k]);
    const songwriterMissing =
        step.value === 2 &&
        form.songwriters.some((writer) => !writer.name.trim() || !writer.role);
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
    const videoUploadMissing =
        step.value === 2 &&
        !props.videoUploadDisabled &&
        !form.upload_tokens.some((item) => item.type === "video");
    const videoLinkMissing =
        step.value === 2 &&
        props.videoUploadDisabled &&
        !form.video_url.trim();
    if (step.value === 3 && !form.terms) {
        consentError.value =
            "Persetujuan ketentuan dan persyaratan wajib dicentang.";
        showErrorModal(consentError.value);
        return;
    }
    if (step.value === 2 && isYoutube) {
        form.setError(
            "video_url",
            "Link video tidak boleh berasal dari YouTube.",
        );
        showErrorModal("Link video tidak boleh berasal dari YouTube.");
        return;
    }
    if (videoLinkMissing) {
        form.setError(
            "video_url",
            "Link video wajib diisi karena upload video sedang dinonaktifkan.",
        );
        showErrorModal(
            "Upload video sedang dinonaktifkan. Masukkan link video yang dapat diakses oleh tim.",
        );
        return;
    }
    if (videoUploadMissing) {
        form.setError("upload_tokens", "Upload video penampilan wajib dilakukan.");
        showErrorModal("Upload video penampilan wajib dilakukan dan harus selesai sebelum melanjutkan.");
        return;
    }
    if (songwriterMissing) {
        form.setError(
            "songwriters",
            "Nama dan peran setiap songwriter wajib diisi.",
        );
        showErrorModal("Nama dan peran setiap songwriter wajib diisi.");
        return;
    }
    if (missing) {
        showErrorModal(
            "Masih ada data wajib yang belum diisi pada langkah ini. Periksa kembali semua kolom sebelum melanjutkan.",
        );
        return;
    }
    if (!missing) {
        form.clearErrors();
        step.value++;
        scrollToFormTop();
    }
}
function submit() {
    form.post("/registration/drafts", {
        preserveScroll: true,
        onError: (errors) => {
            const errorSteps = Object.keys(errors).map(errorStep);
            if (errorSteps.length) {
                step.value = Math.min(...errorSteps);
                scrollToFormTop();
            }
            showErrorModal(Object.values(errors));
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
function chunkToBase64(chunk: Blob): Promise<string> {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onerror = () =>
            reject(new Error("Potongan video gagal dibaca."));
        reader.onload = () => {
            const result = String(reader.result || "");
            resolve(result.slice(result.indexOf(",") + 1));
        };
        reader.readAsDataURL(chunk);
    });
}
async function uploadLarge(file: File, type: "video") {
    const state = uploads.value[type];
    const allowedVideoTypes = ["video/mp4", "video/quicktime", "video/webm"];
    if (!allowedVideoTypes.includes(file.type)) {
        mediaMeta.value[type] = { name: file.name, size: file.size };
        state.status = "failed";
        state.progress = 0;
        state.error = "File harus berformat video MP4, MOV, atau WebM.";
        showErrorModal(state.error || "Upload gagal. Coba lagi.");
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
    // Small JSON/Base64 chunks are more reliable behind Plesk, ModSecurity,
    // reverse proxies and restrictive PHP multipart/raw-body handlers.
    const chunkSize = 128 * 1024;
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
            const chunk = file.slice(
                index * chunkSize,
                Math.min(file.size, (index + 1) * chunkSize),
            );
            const encodedChunk = await chunkToBase64(chunk);
            let attempt = 0;
            while (true) {
                try {
                    await axios.post(
                        `/registration/uploads/${state.id}/chunk`,
                        { index, data: encodedChunk },
                        {
                            headers: {
                                "X-Upload-Token": state.token,
                                "Content-Type": "application/json",
                            },
                        },
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
        showErrorModal(state.error || "Upload gagal. Coba lagi.");
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
        <main ref="formTop" class="mx-auto max-w-4xl scroll-mt-6 px-5 py-12">
            <div class="progress-shell mb-7">
                <div
                    class="h-full bg-orange-500 transition-all"
                    :style="{ width: step * 25 + '%' }"
                ></div>
            </div>
            <section class="step-card">
                <p class="eyebrow">Original Sessions</p>
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
                    novalidate
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
                        <label
                            >Nama Artis<input
                                v-model="form.artist_name"
                                required
                        /></label>
                        <div class="grid gap-5 md:grid-cols-2">
                            <label
                                >Link sosial media artis<input
                                    v-model="form.artist_social_url"
                                    type="url"
                                    required
                                    placeholder="https://instagram.com/namaartis"
                                    @input="
                                        form.clearErrors('artist_social_url')
                                    "
                                /><small
                                    >Instagram, TikTok, Facebook, X, atau sosial
                                    media lainnya.</small
                                ></label
                            >
                            <label
                                ><span class="field-label"
                                    >Link Spotify artis
                                    <em>(opsional)</em></span
                                ><input
                                    v-model="form.artist_spotify_url"
                                    type="url"
                                    placeholder="https://open.spotify.com/artist/..."
                                    @input="
                                        form.clearErrors('artist_spotify_url')
                                    "
                                /><small
                                    >Masukkan link profil artis di Spotify jika
                                    tersedia.</small
                                ></label
                            >
                        </div>
                        <div class="songwriter-panel">
                            <div class="songwriter-heading">
                                <div>
                                    <p class="upload-label">Nama Songwriter</p>
                                    <small
                                        >Tambahkan seluruh penulis lagu beserta
                                        perannya.</small
                                    >
                                </div>
                                <button
                                    type="button"
                                    class="add-writer"
                                    @click="addSongwriter"
                                >
                                    + Tambah Songwriter
                                </button>
                            </div>
                            <div
                                v-for="(writer, index) in form.songwriters"
                                :key="index"
                                class="songwriter-row"
                            >
                                <label
                                    >Nama<input
                                        v-model="writer.name"
                                        required
                                        placeholder="Nama lengkap songwriter"
                                        @input="
                                            form.clearErrors('songwriters')
                                        "
                                /></label>
                                <label
                                    >Nama Sebagai<select
                                        v-model="writer.role"
                                        required
                                    >
                                        <option
                                            v-for="role in songwriterRoles"
                                            :key="role.value"
                                            :value="role.value"
                                        >
                                            {{ role.label }}
                                        </option>
                                    </select></label
                                >
                                <button
                                    v-if="form.songwriters.length > 1"
                                    type="button"
                                    class="remove-writer"
                                    :aria-label="`Hapus songwriter ${index + 1}`"
                                    @click="removeSongwriter(index)"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
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
                        <div v-if="!props.videoUploadDisabled" class="notice">
                            Link video bersifat opsional, tetapi upload file
                            video penampilan wajib dilakukan. Tautan tidak boleh
                            berasal dari YouTube.
                        </div>
                        <div v-else class="notice">
                            Upload video sedang dinonaktifkan oleh administrator.
                            Link video wajib diisi dan harus dapat diakses oleh tim.
                        </div>
                        <label
                            ><span class="field-label">Link video <em>({{ props.videoUploadDisabled ? "wajib" : "opsional" }})</em></span><input
                                v-model="form.video_url"
                                type="url"
                                :required="props.videoUploadDisabled"
                                placeholder="https://drive.google.com/..."
                                @input="form.clearErrors('video_url')"
                            /><small class="field-help"
                                >YouTube tidak diperbolehkan. Pastikan tautan
                                dapat diakses oleh tim.</small
                            ></label
                        >
                        <div v-if="!props.videoUploadDisabled">
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
                                @select="uploadLarge($event, 'video')"
                                @remove="cancelUpload('video')"
                            /></div
                    ></template>
                    <template v-if="step === 3">
                        <section class="terms-panel">
                            <div class="terms-panel-heading">
                                <div>
                                    <p>Syarat & Ketentuan</p>
                                    <small
                                        >Baca seluruh ketentuan sebelum
                                        menyetujui.</small
                                    >
                                </div>
                                <button
                                    type="button"
                                    class="terms-open-button"
                                    @click="termsModalOpen = true"
                                >
                                    Lihat Full Syarat dan Ketentuan
                                </button>
                            </div>
                        </section>
                        <label class="check check-all"
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
                    </template>
                    <template v-if="step === 4"
                        ><div class="review">
                            <p><span>Nama</span>{{ form.full_name }}</p>
                            <p>
                                <span>Lagu</span>{{ form.title }} ·
                                {{ form.genre }}
                            </p>
                            <p><span>Artis</span>{{ form.artist_name }}</p>
                            <p>
                                <span>Sosial media artis</span
                                >{{ form.artist_social_url }}
                            </p>
                            <p v-if="form.artist_spotify_url">
                                <span>Spotify artis</span
                                >{{ form.artist_spotify_url }}
                            </p>
                            <p>
                                <span>Songwriter</span
                                >{{
                                    form.songwriters
                                        .map(
                                            (writer) =>
                                                `${writer.name} (${songwriterRoles.find((role) => role.value === writer.role)?.label})`,
                                        )
                                        .join(", ")
                                }}
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
                                        : props.videoUploadDisabled
                                          ? "Upload video dinonaktifkan"
                                          : "-")
                                }}
                            </p>
                        </div>
                        <p class="text-sm text-neutral-400">
                            Setelah dikirim, data menjadi snapshot dan perubahan
                            hanya dapat dilakukan melalui permintaan revisi.
                        </p></template
                    >
                    <div class="form-actions flex justify-between pt-5">
                        <button
                            v-if="step > 1"
                            type="button"
                            class="back-button"
                            @click="previousStep"
                        >
                            Kembali</button
                        ><span v-else></span
                        ><button
                            class="cta disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="form.processing"
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
        <Teleport to="body">
            <Transition name="error-modal">
                <div
                    v-if="errorModalOpen"
                    class="error-modal-backdrop"
                    role="presentation"
                    @click.self="closeErrorModal"
                >
                    <section
                        class="error-modal"
                        role="alertdialog"
                        aria-modal="true"
                        aria-labelledby="error-modal-title"
                    >
                        <div class="error-modal-icon" aria-hidden="true">!</div>
                        <div class="error-modal-copy">
                            <p class="error-modal-kicker">Periksa pengisian</p>
                            <h2 id="error-modal-title">
                                Data belum dapat diproses
                            </h2>
                            <p class="error-modal-intro">
                                Silakan perbaiki informasi berikut sebelum
                                melanjutkan:
                            </p>
                            <ul>
                                <li
                                    v-for="message in errorModalMessages"
                                    :key="message"
                                >
                                    {{ message }}
                                </li>
                            </ul>
                        </div>
                        <button
                            type="button"
                            class="error-modal-close"
                            aria-label="Tutup pemberitahuan error"
                            @click="closeErrorModal"
                        >
                            ×
                        </button>
                        <button
                            type="button"
                            class="error-modal-action"
                            autofocus
                            @click="closeErrorModal"
                        >
                            Perbaiki Data
                        </button>
                    </section>
                </div>
            </Transition>
        </Teleport>
        <Teleport to="body">
            <Transition name="terms-modal">
                <div
                    v-if="termsModalOpen"
                    class="terms-modal-backdrop"
                    role="presentation"
                    @click.self="termsModalOpen = false"
                >
                    <section
                        class="terms-modal"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="terms-modal-title"
                    >
                        <header>
                            <div>
                                <p>Original Sessions</p>
                                <h2 id="terms-modal-title">
                                    Syarat dan Ketentuan
                                </h2>
                            </div>
                            <button
                                type="button"
                                class="terms-modal-x"
                                aria-label="Tutup syarat dan ketentuan"
                                @click="termsModalOpen = false"
                            >
                                ×
                            </button>
                        </header>
                        <div class="terms-modal-document">
                            <iframe
                                title="Dokumen lengkap Syarat dan Ketentuan Original Sessions"
                                src="https://docs.google.com/document/d/1mXJirD06OdoPK9scnCew2-Zg1pAZd9nS/preview"
                                loading="lazy"
                            ></iframe>
                        </div>
                        <footer>
                            <button
                                type="button"
                                class="terms-modal-close"
                                autofocus
                                @click="termsModalOpen = false"
                            >
                                Tutup
                            </button>
                        </footer>
                    </section>
                </div>
            </Transition>
        </Teleport>
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
.field-label {
    display: inline-flex;
    align-items: baseline;
    gap: 0.35rem;
}
.field-label em {
    color: #77776f;
    font-weight: 500;
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
.songwriter-panel {
    display: grid;
    gap: 1rem;
    border: 1px solid #e4e4de;
    border-radius: 18px;
    background: #fafaf8;
    padding: 1.25rem;
}
.songwriter-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.songwriter-heading .upload-label {
    margin: 0;
}
.songwriter-heading small {
    color: #77776f;
}
.songwriter-row {
    display: grid;
    align-items: end;
    gap: 1rem;
    border-top: 1px solid #e6e6e0;
    padding-top: 1rem;
    grid-template-columns: minmax(0, 1fr) minmax(12rem, 0.65fr) auto;
}
.add-writer {
    border: 1px solid #ff9b56;
    border-radius: 999px;
    padding: 0.65rem 1rem;
    color: #c94f00;
    font-size: 0.8rem;
    font-weight: 700;
}
.remove-writer {
    margin-bottom: 0.15rem;
    border: 1px solid #efc9c9;
    border-radius: 12px;
    padding: 0.9rem;
    color: #b42318;
    font-size: 0.8rem;
    font-weight: 700;
}
@media (max-width: 700px) {
    .songwriter-heading {
        align-items: flex-start;
        flex-direction: column;
    }
    .songwriter-row {
        grid-template-columns: 1fr;
    }
    .remove-writer {
        width: 100%;
    }
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
    accent-color: #16a34a;
    box-shadow: none;
}
.check-all {
    border-color: #ffb47f;
    background: #fff7f1;
}
.check-all:has(input:checked) {
    border-color: #22c55e;
    background: #f0fdf4;
    box-shadow: 0 0 0 3px #22c55e18;
}
.terms-panel {
    overflow: hidden;
    border: 1px solid #ddddd7;
    border-radius: 1.25rem;
    background: #f8f8f5;
}
.terms-panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #ddddd7;
    background: #fff;
}
.terms-panel-heading p {
    color: #242421;
    font-weight: 800;
}
.terms-panel-heading small {
    color: #73736d;
}
.terms-open-button {
    flex: 0 0 auto;
    color: #e85f00;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: underline;
    text-underline-offset: 0.25rem;
}
.terms-open-button:hover {
    color: #b94700;
}
@media (max-width: 600px) {
    .terms-panel-heading {
        align-items: flex-start;
        flex-direction: column;
    }
}
.terms-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1100;
    display: grid;
    place-items: center;
    padding: 1rem;
    background: #050505e6;
    backdrop-filter: blur(10px);
}
.terms-modal {
    display: grid;
    width: min(64rem, 100%);
    height: min(90vh, 54rem);
    overflow: hidden;
    border: 1px solid #ff762055;
    border-radius: 1.5rem;
    background: #fff;
    box-shadow: 0 35px 110px #000d;
    grid-template-rows: auto minmax(0, 1fr) auto;
}
.terms-modal > header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e6e3de;
}
.terms-modal > header p {
    color: #e85f00;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.terms-modal > header h2 {
    margin-top: 0.15rem;
    color: #171716;
    font-size: 1.4rem;
}
.terms-modal-x {
    display: grid;
    place-items: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    color: #686762;
    font-size: 1.7rem;
    transition: 0.2s ease;
}
.terms-modal-x:hover {
    color: #171716;
    background: #f0eeea;
}
.terms-modal-document {
    min-height: 0;
    overflow: auto;
    background: #f2f1ed;
}
.terms-modal-document iframe {
    display: block;
    width: 100%;
    height: 100%;
    min-height: 36rem;
    border: 0;
    background: #fff;
}
.terms-modal > footer {
    display: flex;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e6e3de;
    background: #fff;
}
.terms-modal-close {
    min-width: 8rem;
    padding: 0.8rem 1.4rem;
    border-radius: 999px;
    color: #fff;
    font-weight: 800;
    background: #ff6a00;
}
.terms-modal-enter-active,
.terms-modal-leave-active {
    transition: opacity 0.22s ease;
}
.terms-modal-enter-active .terms-modal,
.terms-modal-leave-active .terms-modal {
    transition: 0.22s ease;
}
.terms-modal-enter-from,
.terms-modal-leave-to {
    opacity: 0;
}
.terms-modal-enter-from .terms-modal,
.terms-modal-leave-to .terms-modal {
    opacity: 0;
    transform: translateY(1rem) scale(0.98);
}
@media (max-width: 640px) {
    .terms-modal-backdrop {
        padding: 0;
    }
    .terms-modal {
        width: 100%;
        height: 100dvh;
        border: 0;
        border-radius: 0;
    }
    .terms-modal > footer {
        justify-content: stretch;
    }
    .terms-modal-close {
        width: 100%;
    }
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
.error-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: grid;
    place-items: center;
    padding: 1.25rem;
    background: #080808d9;
    backdrop-filter: blur(10px);
}
.error-modal {
    position: relative;
    display: grid;
    width: min(34rem, 100%);
    padding: 2rem;
    border: 1px solid #ff725f66;
    border-radius: 1.75rem;
    color: #171716;
    background: linear-gradient(145deg, #fff, #fff7f5);
    box-shadow: 0 35px 100px #000b;
    grid-template-columns: 3.25rem 1fr;
    gap: 1.25rem;
}
.error-modal-icon {
    display: grid;
    place-items: center;
    width: 3.25rem;
    height: 3.25rem;
    border-radius: 50%;
    color: #fff;
    font-size: 1.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, #ff6a00, #e5352a);
    box-shadow: 0 10px 30px #e5352a44;
}
.error-modal-kicker {
    margin-bottom: 0.35rem;
    color: #e85f00;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}
.error-modal h2 {
    color: #171716;
    font-size: 1.5rem;
    line-height: 1.2;
}
.error-modal-intro {
    margin-top: 0.75rem;
    color: #66645f;
    line-height: 1.6;
}
.error-modal ul {
    display: grid;
    gap: 0.55rem;
    margin-top: 1rem;
    padding-left: 1.15rem;
    color: #a3261c;
    line-height: 1.55;
    list-style: disc;
}
.error-modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: grid;
    place-items: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 50%;
    color: #686762;
    font-size: 1.5rem;
    transition: 0.2s ease;
}
.error-modal-close:hover {
    color: #171716;
    background: #eeeae5;
}
.error-modal-action {
    grid-column: 1 / -1;
    margin-top: 0.75rem;
    padding: 0.95rem 1.25rem;
    border-radius: 999px;
    color: #fff;
    font-weight: 800;
    background: linear-gradient(90deg, #f35f18, #ff7a1a);
    box-shadow: 0 12px 28px #ff6a0033;
}
.error-modal-enter-active,
.error-modal-leave-active {
    transition: opacity 0.22s ease;
}
.error-modal-enter-active .error-modal,
.error-modal-leave-active .error-modal {
    transition: 0.22s ease;
}
.error-modal-enter-from,
.error-modal-leave-to {
    opacity: 0;
}
.error-modal-enter-from .error-modal,
.error-modal-leave-to .error-modal {
    opacity: 0;
    transform: translateY(1rem) scale(0.97);
}
@media (max-width: 520px) {
    .error-modal {
        padding: 1.5rem;
        grid-template-columns: 2.75rem 1fr;
    }
    .error-modal-icon {
        width: 2.75rem;
        height: 2.75rem;
    }
    .error-modal h2 {
        padding-right: 1.75rem;
        font-size: 1.25rem;
    }
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
