<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

defineOptions({ layout: AdminLayout });
const props = defineProps<{ settings: any; admins: any[] }>();
const activeTab = ref<"logo" | "smtp" | "drive" | "registration" | "admins">("logo");
const preview = ref<string | null>(props.settings.logo_url);
const flash = computed(() => (usePage().props.flash as any)?.success);
const flashError = computed(() => (usePage().props.flash as any)?.error);
const logo = useForm<{ logo: File | null }>({ logo: null });
const smtp = useForm({
    mail_host: props.settings.mail_host || "smtp.gmail.com",
    mail_port: Number(props.settings.mail_port || 587),
    mail_username: props.settings.mail_username || "",
    mail_password: "",
    mail_encryption: props.settings.mail_encryption || "tls",
    mail_from_address: props.settings.mail_from_address || "",
    mail_from_name: props.settings.mail_from_name || "Original Sessions",
});
const smtpTest = useForm({ test_email: "" });
const drive = useForm({
    drive_enabled: Boolean(props.settings.drive_enabled),
    drive_binary: props.settings.drive_binary || "rclone",
    drive_config_path: props.settings.drive_config_path || "",
    drive_remote: props.settings.drive_remote || "gdrive",
    drive_base_path: props.settings.drive_base_path || "Original Sessions",
});
const driveTesting = ref(false);
const registration = useForm({
    registration_disabled: Boolean(props.settings.registration_disabled),
    video_upload_disabled: Boolean(props.settings.video_upload_disabled),
});
const admin = useForm({
    name: "",
    username: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "admin",
});
function chooseLogo(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    logo.logo = file;
    if (file) preview.value = URL.createObjectURL(file);
}
function saveLogo() {
    logo.post("/admin/settings/logo", { forceFormData: true });
}
function saveAdmin() {
    admin.post("/admin/settings/admins", { onSuccess: () => admin.reset() });
}
function sendTestEmail() {
    smtpTest.post("/admin/settings/smtp/test", {
        preserveScroll: true,
        onSuccess: () => smtpTest.clearErrors(),
    });
}
function saveRegistrationSettings() {
    registration.put("/admin/settings/registration", {
        preserveScroll: true,
    });
}
function testGoogleDrive() {
    driveTesting.value = true;
    router.post("/admin/settings/google-drive/test", {}, {
        preserveScroll: true,
        onFinish: () => (driveTesting.value = false),
    });
}
function toggleAdmin(id: number) {
    router.patch(
        `/admin/settings/admins/${id}/toggle`,
        {},
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Setting Admin" />
    <div class="settings-page">
        <header class="page-heading">
            <div>
                <p>PENGATURAN SISTEM</p>
                <h1>Setting</h1>
                <span
                    >Kelola identitas, pengiriman email, dan akses
                    administrator.</span
                >
            </div>
            <div class="pulse"><i></i> Sistem aktif</div>
        </header>
        <div v-if="flash" class="flash">{{ flash }}</div>
        <div v-if="flashError" class="flash flash-error">{{ flashError }}</div>
        <nav class="tabs" aria-label="Kategori setting">
            <button
                v-for="tab in [
                    { id: 'logo', label: 'Logo & Branding', icon: '◈' },
                    { id: 'smtp', label: 'SMTP Gmail', icon: '✉' },
                    { id: 'drive', label: 'Google Drive', icon: '△' },
                    { id: 'registration', label: 'Form Pendaftaran', icon: '▣' },
                    { id: 'admins', label: 'Akun Admin', icon: '♙' },
                ]"
                :key="tab.id"
                :class="{ active: activeTab === tab.id }"
                @click="activeTab = tab.id as any"
            >
                <span>{{ tab.icon }}</span
                >{{ tab.label }}
            </button>
        </nav>

        <section v-if="activeTab === 'logo'" class="setting-grid">
            <article class="panel form-panel">
                <div class="panel-title">
                    <span>01</span>
                    <div>
                        <h2>Logo Website</h2>
                        <p>
                            Logo ditampilkan sebagai identitas Original
                            Sessions.
                        </p>
                    </div>
                </div>
                <form @submit.prevent="saveLogo">
                    <label class="upload-zone"
                        ><input
                            type="file"
                            accept="image/png,image/jpeg,image/webp,image/svg+xml"
                            @change="chooseLogo"
                        /><span class="upload-icon">↑</span
                        ><b>Pilih atau tarik logo ke sini</b
                        ><small
                            >PNG, JPG, WebP, atau SVG · Maks. 2 MB</small
                        ></label
                    >
                    <p v-if="logo.errors.logo" class="error">
                        {{ logo.errors.logo }}
                    </p>
                    <button
                        class="save"
                        :disabled="logo.processing || !logo.logo"
                    >
                        Simpan Logo
                    </button>
                </form>
            </article>
            <article class="panel preview-panel">
                <span>PREVIEW</span>
                <div class="logo-preview">
                    <img v-if="preview" :src="preview" alt="Preview logo" />
                    <div v-else class="logo-placeholder">OS</div>
                </div>
                <p>
                    Logo akan ditampilkan dengan proporsi asli dan latar
                    transparan.
                </p>
            </article>
        </section>

        <section v-else-if="activeTab === 'smtp'" class="panel smtp-panel">
            <div class="panel-title">
                <span>02</span>
                <div>
                    <h2>SMTP Gateway Gmail</h2>
                    <p>
                        Gunakan Google App Password agar email notifikasi dapat
                        dikirim dengan aman.
                    </p>
                </div>
            </div>
            <form
                class="form-grid"
                @submit.prevent="smtp.put('/admin/settings/smtp')"
            >
                <label
                    >SMTP Host<input
                        v-model="smtp.mail_host"
                        required
                        placeholder="smtp.gmail.com"
                    /><small v-if="smtp.errors.mail_host">{{
                        smtp.errors.mail_host
                    }}</small></label
                >
                <label
                    >Port<input v-model="smtp.mail_port" type="number" required
                /></label>
                <label
                    >Email / Username<input
                        v-model="smtp.mail_username"
                        required
                        autocomplete="off"
                        placeholder="nama@gmail.com"
                /></label>
                <label
                    >App Password<input
                        v-model="smtp.mail_password"
                        type="password"
                        autocomplete="new-password"
                        :placeholder="
                            settings.mail_password_set
                                ? 'Tersimpan · isi untuk mengganti'
                                : '16 digit App Password'
                        "
                    /><small>Password disimpan terenkripsi.</small></label
                >
                <label
                    >Enkripsi<select v-model="smtp.mail_encryption">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                    </select></label
                >
                <label
                    >Email Pengirim<input
                        v-model="smtp.mail_from_address"
                        type="email"
                        required
                /></label>
                <label class="wide"
                    >Nama Pengirim<input v-model="smtp.mail_from_name" required
                /></label>
                <div class="wide form-footer">
                    <p v-if="Object.keys(smtp.errors).length" class="error">
                        Periksa kembali data SMTP.
                    </p>
                    <button class="save" :disabled="smtp.processing">
                        Simpan SMTP
                    </button>
                </div>
            </form>
            <form class="smtp-test" @submit.prevent="sendTestEmail">
                <div>
                    <strong>Test pengiriman email</strong>
                    <p>Kirim email percobaan untuk memastikan SMTP yang tersimpan berfungsi.</p>
                </div>
                <label>
                    Email tujuan test
                    <input
                        v-model="smtpTest.test_email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="contoh@gmail.com"
                    />
                    <small v-if="smtpTest.errors.test_email" class="error">{{ smtpTest.errors.test_email }}</small>
                </label>
                <button class="test-button" :disabled="smtpTest.processing">
                    {{ smtpTest.processing ? "Mengirim..." : "Kirim Test Email" }}
                </button>
            </form>
        </section>

        <section v-else-if="activeTab === 'drive'" class="panel drive-panel">
            <div class="panel-title">
                <span>03</span>
                <div>
                    <h2>Penyimpanan Video Google Drive</h2>
                    <p>Video final dipindahkan otomatis melalui rclone. KTP tetap tersimpan privat di server.</p>
                </div>
            </div>
            <form class="form-grid" @submit.prevent="drive.put('/admin/settings/google-drive', { preserveScroll: true })">
                <label class="wide setting-switch-card">
                    <span><b>Aktifkan Google Drive</b><small>Setelah aktif, video baru dipindahkan ke Drive dan salinan lokal dihapus setelah ukuran file terverifikasi.</small></span>
                    <input v-model="drive.drive_enabled" type="checkbox" /><i aria-hidden="true"></i>
                </label>
                <label>Lokasi rclone<input v-model="drive.drive_binary" required placeholder="rclone atau /usr/bin/rclone" /></label>
                <label>Nama remote<input v-model="drive.drive_remote" required placeholder="gdrive" /></label>
                <label class="wide">Lokasi config rclone <small>(opsional jika memakai lokasi default)</small><input v-model="drive.drive_config_path" placeholder="/home/USER/.config/rclone/rclone.conf" /></label>
                <label class="wide">Folder utama Google Drive<input v-model="drive.drive_base_path" required placeholder="Original Sessions" /></label>
                <div class="wide form-footer drive-actions">
                    <button type="button" class="test-button" :disabled="driveTesting" @click="testGoogleDrive">{{ driveTesting ? 'Memeriksa...' : 'Tes Koneksi Drive' }}</button>
                    <button class="save" :disabled="drive.processing">{{ drive.processing ? 'Menyimpan...' : 'Simpan Google Drive' }}</button>
                </div>
            </form>
            <div class="drive-guide">
                <strong>Login Google satu kali di Plesk</strong>
                <ol>
                    <li>Pasang rclone pada server, lalu buka SSH/Terminal Plesk.</li>
                    <li>Jalankan <code>rclone config</code>, pilih <b>New remote</b>, beri nama <code>{{ drive.drive_remote || 'gdrive' }}</code>, lalu pilih Google Drive.</li>
                    <li>Ikuti tautan login Google yang diberikan rclone dan izinkan akses satu kali.</li>
                    <li>Simpan pengaturan di atas, aktifkan Google Drive, kemudian klik <b>Tes Koneksi Drive</b>.</li>
                    <li>Pindahkan seluruh video lama dengan <code>php artisan videos:migrate-to-drive --retry-failed</code>.</li>
                </ol>
                <p>Jangan menghapus video lokal secara manual. Sistem hanya menghapusnya setelah file Drive terverifikasi.</p>
            </div>
        </section>

        <section v-else-if="activeTab === 'registration'" class="panel registration-panel">
            <div class="panel-title">
                <span>03</span>
                <div>
                    <h2>Pengaturan Form Pendaftaran</h2>
                    <p>Atur kewajiban media yang harus dikirim oleh pendaftar.</p>
                </div>
            </div>
            <form class="registration-form" @submit.prevent="saveRegistrationSettings">
                <label class="setting-switch-card danger-setting">
                    <span>
                        <b>Nonaktifkan Pendaftaran</b>
                        <small>Jika aktif, halaman formulir diganti dengan informasi pendaftaran ditutup dan seluruh proses submit serta upload dikunci.</small>
                    </span>
                    <input v-model="registration.registration_disabled" type="checkbox" />
                    <i aria-hidden="true"></i>
                </label>
                <div :class="['setting-state', registration.registration_disabled ? 'disabled' : 'enabled']">
                    <strong>{{ registration.registration_disabled ? "Pendaftaran ditutup" : "Pendaftaran dibuka" }}</strong>
                    <span>{{ registration.registration_disabled ? "Pengunjung tidak dapat mengisi atau mengirim pendaftaran baru." : "Pengunjung dapat membuka dan mengirim form pendaftaran." }}</span>
                </div>
                <label class="setting-switch-card">
                    <span>
                        <b>Nonaktifkan Upload Video</b>
                        <small>Jika aktif, kolom upload video disembunyikan dan Link Video otomatis menjadi wajib.</small>
                    </span>
                    <input v-model="registration.video_upload_disabled" type="checkbox" />
                    <i aria-hidden="true"></i>
                </label>
                <div :class="['setting-state', registration.video_upload_disabled ? 'disabled' : 'enabled']">
                    <strong>{{ registration.video_upload_disabled ? "Upload video tidak wajib" : "Upload video wajib" }}</strong>
                    <span>{{ registration.video_upload_disabled ? "Pendaftar wajib mengisi Link Video yang dapat diakses tim." : "Upload video wajib, sedangkan Link Video bersifat opsional." }}</span>
                </div>
                <button class="save" :disabled="registration.processing">
                    {{ registration.processing ? "Menyimpan..." : "Simpan Pengaturan" }}
                </button>
            </form>
        </section>

        <section v-else class="admin-grid">
            <article class="panel">
                <div class="panel-title">
                    <span>04</span>
                    <div>
                        <h2>Tambah Admin</h2>
                        <p>Buat akun baru yang dapat masuk ke panel admin.</p>
                    </div>
                </div>
                <form class="admin-form" @submit.prevent="saveAdmin">
                    <label
                        >Nama lengkap<input
                            v-model="admin.name"
                            required /></label
                    ><label
                        >Username<input
                            v-model="admin.username"
                            required
                            autocomplete="off" /></label
                    ><label
                        >Email<input
                            v-model="admin.email"
                            type="email"
                            required /></label
                    ><label
                        >Peran<select v-model="admin.role">
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select></label
                    ><label
                        >Password<input
                            v-model="admin.password"
                            type="password"
                            minlength="8"
                            required
                            autocomplete="new-password" /></label
                    ><label
                        >Konfirmasi password<input
                            v-model="admin.password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                    /></label>
                    <div v-if="Object.keys(admin.errors).length" class="error">
                        <p v-for="error in admin.errors" :key="error">
                            {{ error }}
                        </p>
                    </div>
                    <button class="save" :disabled="admin.processing">
                        Tambahkan Admin
                    </button>
                </form>
            </article>
            <article class="panel">
                <div class="list-title">
                    <div>
                        <h2>Administrator</h2>
                        <p>{{ admins.length }} akun terdaftar</p>
                    </div>
                </div>
                <div class="admin-list">
                    <div v-for="item in admins" :key="item.id">
                        <span class="admin-avatar">{{
                            item.name.charAt(0)
                        }}</span
                        ><span
                            ><b>{{ item.name }}</b
                            ><small
                                >@{{ item.username }} ·
                                {{ item.role.replaceAll("_", " ") }}</small
                            ></span
                        ><button
                            type="button"
                            :class="['status', item.is_active ? 'on' : 'off']"
                            @click="toggleAdmin(item.id)"
                        >
                            {{ item.is_active ? "Aktif" : "Nonaktif" }}
                        </button>
                    </div>
                </div>
            </article>
        </section>
    </div>
</template>

<style scoped>
.settings-page {
    max-width: 1320px;
    margin: auto;
}
.page-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
}
.page-heading > div > p {
    color: #ff7c2e;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.2em;
}
.page-heading h1 {
    margin-top: 7px;
    font-size: 36px;
}
.page-heading > div > span,
.panel-title p,
.list-title p {
    display: block;
    margin-top: 5px;
    color: #748196;
    font-size: 13px;
}
.pulse {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #748196;
    font-size: 11px;
}
.pulse i {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #36d399;
    box-shadow: 0 0 0 5px #36d39917;
}
.flash {
    margin-top: 22px;
    border: 1px solid #36d39944;
    border-radius: 13px;
    background: #123126;
    padding: 13px 16px;
    color: #6ee7b7;
    font-size: 12px;
}
.flash-error {
    border-color: #fb718544;
    background: #3c2025;
    color: #fda4af;
}
.tabs {
    display: flex;
    gap: 8px;
    margin-top: 28px;
    padding: 6px;
    border: 1px solid #202b3e;
    border-radius: 16px;
    background: #0d1422;
}
.tabs button {
    display: flex;
    align-items: center;
    gap: 9px;
    border-radius: 11px;
    padding: 11px 16px;
    color: #7f8ba0;
    font-size: 12px;
    font-weight: 700;
}
.tabs button span {
    color: #ff7c2e;
}
.tabs button.active {
    background: #252033;
    color: #fff;
    box-shadow: inset 0 0 0 1px #ff6a0033;
}
.panel {
    border: 1px solid #202b3e;
    border-radius: 20px;
    background: #111827;
    padding: 26px;
}
.setting-grid,
.admin-grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 20px;
    margin-top: 20px;
}
.panel-title {
    display: flex;
    gap: 15px;
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
.panel-title h2,
.list-title h2 {
    font-size: 18px;
}
.upload-zone {
    display: flex;
    align-items: center;
    flex-direction: column;
    margin-top: 26px;
    border: 1px dashed #364258;
    border-radius: 16px;
    padding: 40px 20px;
    background: #0d1421;
    text-align: center;
    cursor: pointer;
    transition: 0.2s;
}
.upload-zone:hover {
    border-color: #ff7c2e;
    background: #161720;
}
.upload-zone input {
    display: none;
}
.upload-icon {
    display: grid;
    width: 46px;
    height: 46px;
    place-items: center;
    border-radius: 50%;
    background: #2e1c1c;
    color: #ff7c2e;
    font-size: 23px;
}
.upload-zone b {
    margin-top: 14px;
}
.upload-zone small,
.preview-panel p,
label small {
    margin-top: 7px;
    color: #6f7c90;
    font-size: 10px;
}
.save {
    margin-top: 20px;
    border-radius: 12px;
    background: #ff6a00;
    padding: 12px 18px;
    color: #0b101c;
    font-size: 12px;
    font-weight: 800;
}
.save:disabled {
    cursor: not-allowed;
    opacity: 0.45;
}
.preview-panel > span {
    color: #ff7c2e;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.18em;
}
.logo-preview {
    display: grid;
    min-height: 240px;
    place-items: center;
    margin-top: 20px;
    border-radius: 15px;
    background: repeating-linear-gradient(
        45deg,
        #0d1421,
        #0d1421 12px,
        #111a29 12px,
        #111a29 24px
    );
}
.logo-preview img {
    max-width: 70%;
    max-height: 180px;
}
.logo-placeholder {
    display: grid;
    width: 90px;
    height: 90px;
    place-items: center;
    border-radius: 26px;
    background: linear-gradient(145deg, #ff822e, #e44816);
    color: #111827;
    font: 800 30px "Space Grotesk";
}
.smtp-panel {
    margin-top: 20px;
}
.registration-panel { margin-top: 20px; }
.registration-form { margin-top: 27px; }
.setting-switch-card { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 18px; margin-top: 14px; border: 1px solid #2a3548; border-radius: 16px; padding: 20px; background: #0b1220; cursor: pointer; }
.setting-switch-card:first-child { margin-top: 0; }
.setting-switch-card.danger-setting { border-color: #4a2a30; background: linear-gradient(135deg, #1a1117, #0b1220); }
.setting-switch-card > span b, .setting-switch-card > span small { display: block; }
.setting-switch-card > span b { color: #edf2f7; font-size: 14px; }
.setting-switch-card > span small { max-width: 680px; margin-top: 7px; color: #748196; font-size: 11px; line-height: 1.6; }
.setting-switch-card input { position: absolute; opacity: 0; pointer-events: none; }
.setting-switch-card i { position: relative; width: 50px; height: 28px; border-radius: 99px; background: #334155; transition: .2s ease; }
.setting-switch-card i::after { content: ""; position: absolute; top: 4px; left: 4px; width: 20px; height: 20px; border-radius: 50%; background: white; transition: .2s ease; }
.setting-switch-card input:checked + i { background: #ef4444; }
.setting-switch-card input:checked + i::after { transform: translateX(22px); }
.setting-switch-card:focus-within { border-color: #ff7c2e; box-shadow: 0 0 0 3px #ff6a0017; }
.setting-state { display: grid; gap: 4px; margin-top: 14px; border-radius: 12px; padding: 13px 15px; font-size: 11px; }
.setting-state strong { font-size: 12px; }
.setting-state.enabled { background: #123126; color: #6ee7b7; }
.setting-state.disabled { background: #3c2025; color: #fda4af; }
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 17px;
    margin-top: 27px;
}
.form-grid label,
.admin-form label {
    display: grid;
    gap: 8px;
    color: #aeb8c8;
    font-size: 11px;
    font-weight: 600;
}
.wide {
    grid-column: 1/-1;
}
input,
select {
    width: 100%;
    border: 1px solid #2a3548;
    border-radius: 11px;
    background: #0b1220;
    padding: 12px 13px;
    color: #edf2f7;
    font-size: 12px;
}
input:focus,
select:focus {
    border-color: #ff7c2e;
    outline: 0;
    box-shadow: 0 0 0 3px #ff6a0017;
}
.form-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}
.smtp-test {
    display: grid;
    grid-template-columns: minmax(220px, 1fr) minmax(280px, 1.2fr) auto;
    align-items: end;
    gap: 18px;
    margin-top: 28px;
    border-top: 1px solid #263044;
    padding-top: 25px;
}
.smtp-test strong {
    font-size: 14px;
}
.smtp-test > div p {
    margin-top: 6px;
    color: #748196;
    font-size: 11px;
    line-height: 1.6;
}
.smtp-test label {
    display: grid;
    gap: 8px;
    color: #aeb8c8;
    font-size: 11px;
    font-weight: 600;
}
.smtp-test .error {
    margin-top: 0;
}
.test-button {
    min-height: 42px;
    border: 1px solid #ff7c2e;
    border-radius: 12px;
    padding: 11px 17px;
    color: #ff9a5b;
    font-size: 12px;
    font-weight: 800;
    transition: .2s;
}
.test-button:hover {
    background: #ff6a00;
    color: #0b101c;
}
.test-button:disabled {
    cursor: wait;
    opacity: .5;
}
.error {
    margin-top: 10px;
    color: #fb7185;
    font-size: 11px;
}
.admin-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 25px;
}
.admin-form > .error,
.admin-form > .save {
    grid-column: 1/-1;
}
.admin-list {
    display: grid;
    margin-top: 16px;
}
.admin-list > div {
    display: grid;
    grid-template-columns: 40px 1fr auto;
    align-items: center;
    gap: 11px;
    border-top: 1px solid #202a3b;
    padding: 15px 0;
}
.admin-list > div:first-child {
    border: 0;
}
.admin-avatar {
    display: grid;
    width: 38px;
    height: 38px;
    place-items: center;
    border-radius: 50%;
    background: #2d2032;
    color: #ff9a5b;
    font-weight: 800;
}
.admin-list b,
.admin-list small {
    display: block;
    text-transform: capitalize;
}
.admin-list b {
    font-size: 12px;
}
.admin-list small {
    margin-top: 4px;
    color: #718096;
    font-size: 9px;
}
.status {
    border-radius: 99px;
    padding: 6px 10px;
    font-size: 9px;
    font-weight: 800;
}
.status.on {
    background: #173a31;
    color: #4ade80;
}
.status.off {
    background: #3c2025;
    color: #fb7185;
}
.drive-actions { display:flex;justify-content:flex-end;gap:12px; }
.drive-guide { margin-top:24px;border:1px solid #28354b;border-radius:16px;background:#0a1220;padding:20px;color:#9aa8bd; }
.drive-guide strong { color:#f4f7fb;font-size:15px; }
.drive-guide ol { display:grid;gap:9px;margin:14px 0 12px;padding-left:20px;font-size:12px;line-height:1.6; }
.drive-guide code { border:1px solid #344159;border-radius:6px;background:#111b2c;padding:2px 6px;color:#ff9a5b;overflow-wrap:anywhere; }
.drive-guide p { margin:0;color:#ffbd91;font-size:11px; }
@media (max-width: 950px) {
    .setting-grid,
    .admin-grid {
        grid-template-columns: 1fr;
    }
    .smtp-test {
        grid-template-columns: 1fr;
        align-items: stretch;
    }
}
@media (max-width: 600px) {
    .page-heading {
        align-items: flex-start;
        flex-direction: column;
    }
    .tabs {
        overflow-x: auto;
    }
    .tabs button {
        flex: none;
    }
    .form-grid,
    .admin-form {
        grid-template-columns: 1fr;
    }
    .wide,
    .admin-form > .error,
    .admin-form > .save {
        grid-column: auto;
    }
    .panel {
        padding: 20px;
    }
}
</style>
