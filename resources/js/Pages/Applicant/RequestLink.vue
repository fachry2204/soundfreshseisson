<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ submission?: any | null }>();
const form = useForm({ registration_number: props.submission?.registration_number ?? '', email: props.submission?.applicant?.email ?? '' });
const lookupError = computed(() => (form.errors as Record<string, string | undefined>).lookup);
const check = () => form.post('/tracking/check', { preserveScroll: true });
const date = (value?: string) => value ? new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(value)) : '-';
const dateTime = (value?: string) => value ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value)) : '-';
const fileSize = (bytes: number) => bytes >= 1048576 ? `${(bytes / 1048576).toFixed(1)} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`;
const role = (value: string) => ({ composer: 'Composer', author: 'Author', composer_author: 'Composer & Author' }[value] ?? value);
</script>

<template>
    <Head title="Cek Status Pendaftaran" />
    <main class="tracking-page">
        <div class="ambient one" aria-hidden="true"></div><div class="ambient two" aria-hidden="true"></div>
        <div class="music-ornaments" aria-hidden="true">
            <span class="note note-a">♪</span><span class="note note-b">♫</span>
            <div class="sound-wave"><i v-for="n in 18" :key="n"></i></div>
        </div>
        <section class="tracking-shell">
            <Link href="/" class="back-link">← Kembali ke Beranda</Link>
            <div class="intro">
                <div><p class="eyebrow">Cek status lagu</p><h1>Pantau perjalanan lagumu.</h1></div>
                <p>Masukkan nomor pendaftaran dan email yang sama seperti saat mendaftar. Hasilnya akan tampil langsung di halaman ini.</p>
            </div>

            <form class="lookup-form" @submit.prevent="check">
                <label><span>Nomor pendaftaran</span><input v-model="form.registration_number" placeholder="OS-2026-000001" autocomplete="off" required /></label>
                <label><span>Email terdaftar</span><input v-model="form.email" type="email" placeholder="nama@email.com" autocomplete="email" required /></label>
                <button :disabled="form.processing"><span>{{ form.processing ? 'Memeriksa data…' : 'Cek Status Lagu Kamu' }}</span><b aria-hidden="true">→</b></button>
            </form>

            <div v-if="lookupError" class="not-found" role="alert">
                <span class="alert-icon">!</span><div><strong>Data pendaftaran tidak ditemukan</strong><p>Pastikan nomor pendaftaran dan email yang dimasukkan sesuai dengan data saat pendaftaran.</p></div>
            </div>
            <template v-else><p v-for="(error, key) in form.errors" :key="key" class="field-error">{{ error }}</p></template>

            <section v-if="submission" class="result" aria-live="polite">
                <header class="result-head">
                    <div><p class="result-kicker">Status pendaftaran saat ini</p><h2>{{ submission.song.title }}</h2><code>{{ submission.registration_number }}</code></div>
                    <div :class="['status-badge', submission.status_value]"><i></i><span>{{ submission.status }}</span></div>
                </header>
                <div v-if="submission.reason" class="reason-box"><span>Catatan status</span><p>{{ submission.reason }}</p></div>

                <div class="result-grid">
                    <article class="data-panel">
                        <div class="panel-title"><span>01</span><div><h3>Data yang kamu submit</h3><p>Dikirim {{ dateTime(submission.submitted_at) }}</p></div></div>
                        <dl class="details">
                            <div><dt>Nama lengkap</dt><dd>{{ submission.applicant.full_name }}</dd></div>
                            <div><dt>Nama artis</dt><dd>{{ submission.song.artist_name || submission.applicant.stage_name || '-' }}</dd></div>
                            <div><dt>Tempat, tanggal lahir</dt><dd>{{ submission.applicant.birth_place }}, {{ date(submission.applicant.birth_date) }}</dd></div>
                            <div><dt>Kontak</dt><dd>{{ submission.applicant.email }}<br>{{ submission.applicant.whatsapp }}</dd></div>
                            <div class="wide"><dt>Wilayah</dt><dd>{{ [submission.applicant.village, submission.applicant.district, submission.applicant.city, submission.applicant.province, submission.applicant.postal_code].filter(Boolean).join(', ') }}</dd></div>
                            <div class="wide"><dt>Alamat</dt><dd>{{ submission.applicant.address }}</dd></div>
                        </dl>
                    </article>

                    <article class="data-panel song-panel">
                        <div class="panel-title"><span>02</span><div><h3>Data lagu</h3><p>Informasi karya terdaftar</p></div></div>
                        <dl class="details single">
                            <div><dt>Judul lagu</dt><dd>{{ submission.song.title }}</dd></div>
                            <div><dt>Genre · Bahasa · Tahun</dt><dd>{{ submission.song.genre }} · {{ submission.song.language }} · {{ submission.song.creation_year }}</dd></div>
                            <div v-if="submission.song.songwriters?.length"><dt>Songwriter</dt><dd>{{ submission.song.songwriters.map((writer:any) => `${writer.name} (${role(writer.role)})`).join(', ') }}</dd></div>
                            <div v-if="submission.song.story"><dt>Cerita lagu</dt><dd class="long-copy">{{ submission.song.story }}</dd></div>
                        </dl>
                        <div v-if="submission.links.length || submission.song.artist_social_url || submission.song.artist_spotify_url" class="link-list">
                            <a v-if="submission.song.artist_social_url" :href="submission.song.artist_social_url" target="_blank" rel="noopener">Sosial media artis <span>↗</span></a>
                            <a v-if="submission.song.artist_spotify_url" :href="submission.song.artist_spotify_url" target="_blank" rel="noopener">Spotify artis <span>↗</span></a>
                            <a v-for="item in submission.links" :key="`${item.type}-${item.url}`" :href="item.url" target="_blank" rel="noopener">{{ item.type.replaceAll('_', ' ') }} <span>↗</span></a>
                        </div>
                        <div v-if="submission.files.length" class="file-list"><p>File yang dikirim</p><div v-for="file in submission.files" :key="`${file.type}-${file.name}`"><span>{{ file.type.replaceAll('_', ' ') }}</span><strong>{{ file.name }}</strong><small>{{ fileSize(file.size) }}</small></div></div>
                    </article>
                </div>

                <article class="timeline-panel">
                    <div class="panel-title"><span>03</span><div><h3>Perjalanan status</h3><p>Riwayat proses pendaftaran</p></div></div>
                    <ol><li v-for="item in submission.timeline" :key="`${item.label}-${item.date}`"><i></i><div><strong>{{ item.label }}</strong><p v-if="item.reason">{{ item.reason }}</p><time>{{ dateTime(item.date) }}</time></div></li></ol>
                </article>
            </section>
        </section>
    </main>
</template>

<style scoped>
.tracking-page{position:relative;min-height:100dvh;overflow:hidden;padding:clamp(1rem,4vw,3rem);color:#f7f6f3;background:#080808;isolation:isolate}.tracking-page::before{content:"";position:fixed;z-index:-3;inset:0;background:url('/images/tracking-studio-bg.png') center/cover no-repeat;opacity:.42;filter:saturate(.85) contrast(1.05)}.tracking-page::after{content:"";position:fixed;z-index:-2;inset:0;background:linear-gradient(90deg,#050505e8 0%,#080808bd 48%,#090705db 100%),radial-gradient(circle at 78% 12%,#ff6a0026,transparent 32%),repeating-linear-gradient(155deg,transparent 0 70px,#ff76200a 71px 72px)}.tracking-shell{position:relative;z-index:2;width:min(1180px,100%);margin:auto}.back-link{display:inline-flex;color:#aaa6a1;font-size:.84rem;transition:.2s}.back-link:hover{color:#ff7620}.intro{display:grid;grid-template-columns:1.2fr .8fr;align-items:end;gap:2rem;margin-top:2.2rem}.eyebrow,.result-kicker{color:#ff7620;font-size:.72rem;font-weight:800;letter-spacing:.2em;text-transform:uppercase}.intro h1{margin:.65rem 0 0;font-family:"Space Grotesk",sans-serif;font-size:clamp(2.4rem,6vw,5.2rem);line-height:.94;letter-spacing:-.055em;text-shadow:0 8px 28px #000}.intro>p{max-width:31rem;color:#c2bdb6;line-height:1.65;text-shadow:0 2px 12px #000}.lookup-form{display:grid;grid-template-columns:1fr 1fr auto;gap:.8rem;margin-top:2.2rem;padding:1rem;border:1px solid #ffffff18;border-radius:1.35rem;background:#121212e8;box-shadow:0 22px 70px #0009;backdrop-filter:blur(12px)}.lookup-form label{display:grid;gap:.45rem}.lookup-form label span{padding-left:.25rem;color:#b8b4ae;font-size:.72rem;font-weight:700}.lookup-form input{width:100%;min-height:3.6rem;border:1px solid #383633;border-radius:.9rem;padding:0 1rem;color:#f8f7f5;background:#0e0e0ef2;font-weight:700;outline:none}.lookup-form input:focus{border-color:#ff7620;box-shadow:0 0 0 3px #ff762018}.lookup-form button{align-self:end;display:flex;min-height:3.6rem;align-items:center;gap:1.2rem;border-radius:.9rem;padding:0 1.25rem;color:#111;background:linear-gradient(90deg,#ff6514,#ff8d2e);font-weight:800}.lookup-form button:disabled{opacity:.6}.lookup-form button b{font-size:1.2rem}.not-found{display:flex;align-items:flex-start;gap:.9rem;margin-top:1rem;padding:1rem 1.2rem;border:1px solid #ef44444d;border-radius:1rem;color:#fecaca;background:#2a1010e8;backdrop-filter:blur(10px)}.alert-icon{display:grid;flex:0 0 auto;width:2rem;height:2rem;place-items:center;border-radius:50%;color:white;background:#ef4444;font-weight:900}.not-found p{margin-top:.2rem;color:#fca5a5;font-size:.83rem}.field-error{margin-top:.7rem;color:#fca5a5}.result{margin-top:1.4rem}.result-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:clamp(1.2rem,3vw,2rem);border:1px solid #ff76203d;border-radius:1.5rem 1.5rem .75rem .75rem;background:linear-gradient(120deg,#22150eef,#111111f2);backdrop-filter:blur(12px)}.result-head h2{margin:.35rem 0;font-family:"Space Grotesk",sans-serif;font-size:clamp(1.8rem,4vw,3rem);line-height:1}.result-head code{color:#c5c0ba;font-weight:700}.status-badge{display:flex;align-items:center;gap:.6rem;border:1px solid #ff76204d;border-radius:999px;padding:.75rem 1rem;color:#ff9a58;background:#ff762013;font-size:.85rem;font-weight:800}.status-badge i{width:.55rem;height:.55rem;border-radius:50%;background:currentColor;box-shadow:0 0 0 .35rem #ff762012}.status-badge.selected{border-color:#22c55e55;color:#4ade80;background:#22c55e12}.status-badge.not_selected,.status-badge.disqualified{border-color:#ef444455;color:#f87171;background:#ef444412}.reason-box{margin-top:.8rem;padding:1rem 1.2rem;border-left:3px solid #ff7620;border-radius:.8rem;background:#17110de8;backdrop-filter:blur(10px)}.reason-box span{color:#ff8b3d;font-size:.7rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase}.reason-box p{margin-top:.3rem;color:#d2cec8;line-height:1.55}.result-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:.8rem;margin-top:.8rem}.data-panel,.timeline-panel{border:1px solid #ffffff14;border-radius:1.35rem;padding:clamp(1.15rem,3vw,1.7rem);background:#111111ed;box-shadow:0 18px 50px #0007;backdrop-filter:blur(12px)}.panel-title{display:flex;align-items:center;gap:.8rem}.panel-title>span{display:grid;width:2.6rem;height:2.6rem;place-items:center;border-radius:.8rem;color:#ff7620;background:#ff762014;font-family:monospace;font-weight:800}.panel-title h3{font-size:1.15rem}.panel-title p{margin-top:.12rem;color:#77736f;font-size:.75rem}.details{display:grid;grid-template-columns:1fr 1fr;gap:1.15rem 1.5rem;margin-top:1.5rem}.details.single{grid-template-columns:1fr}.details .wide{grid-column:1/-1}.details dt{color:#77736f;font-size:.67rem;font-weight:800;letter-spacing:.13em;text-transform:uppercase}.details dd{margin-top:.3rem;color:#eeeae4;font-size:.88rem;font-weight:650;line-height:1.5}.long-copy{display:-webkit-box;overflow:hidden;-webkit-line-clamp:4;-webkit-box-orient:vertical;color:#bbb6af!important;font-weight:500!important}.link-list{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1.2rem}.link-list a{display:flex;gap:.5rem;border:1px solid #ff762044;border-radius:999px;padding:.55rem .75rem;color:#ff9a58;font-size:.72rem;font-weight:800;text-transform:capitalize}.file-list{display:grid;gap:.55rem;margin-top:1.3rem;padding-top:1rem;border-top:1px solid #ffffff0e}.file-list>p{color:#77736f;font-size:.68rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.file-list>div{display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:.55rem}.file-list span{border-radius:.35rem;padding:.2rem .4rem;color:#ff9a58;background:#ff762014;font-size:.62rem;text-transform:uppercase}.file-list strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.75rem}.file-list small{color:#77736f;font-size:.65rem}.timeline-panel{margin-top:.8rem}.timeline-panel ol{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-top:1.4rem}.timeline-panel li{display:grid;grid-template-columns:auto 1fr;gap:.65rem}.timeline-panel li>i{width:.7rem;height:.7rem;margin-top:.2rem;border-radius:50%;background:#ff7620;box-shadow:0 0 0 .3rem #ff762014}.timeline-panel li strong{font-size:.82rem}.timeline-panel li p{margin-top:.25rem;color:#aaa6a1;font-size:.74rem;line-height:1.4}.timeline-panel time{display:block;margin-top:.35rem;color:#68645f;font-size:.67rem}.ambient{position:fixed;z-index:-1;width:28rem;height:28rem;border:1px solid #ff762020;border-radius:50%;box-shadow:inset 0 0 0 3rem #ff762008,inset 0 0 0 6rem #ff762005}.ambient.one{right:-13rem;top:-15rem}.ambient.two{left:-18rem;bottom:-16rem}.music-ornaments{position:fixed;z-index:-1;inset:0;overflow:hidden;pointer-events:none}.note{position:absolute;color:#ff7620;font-family:Georgia,serif;font-size:clamp(7rem,14vw,13rem);line-height:1;opacity:.07;text-shadow:0 0 40px #ff762066}.note-a{right:4%;top:12%;transform:rotate(10deg)}.note-b{left:2%;bottom:5%;transform:rotate(-12deg)}.sound-wave{position:absolute;right:5%;bottom:8%;display:flex;align-items:center;gap:.4rem;height:4rem;opacity:.18}.sound-wave i{width:3px;height:35%;border-radius:99px;background:#ff7620;animation:wave 1.4s ease-in-out infinite alternate}.sound-wave i:nth-child(3n){height:90%;animation-delay:-.5s}.sound-wave i:nth-child(4n){height:60%;animation-delay:-.9s}@keyframes wave{to{height:100%}}@media(max-width:800px){.intro{grid-template-columns:1fr;gap:1rem}.lookup-form{grid-template-columns:1fr}.result-grid{grid-template-columns:1fr}.result-head{align-items:flex-start;flex-direction:column}.details{grid-template-columns:1fr}.details .wide{grid-column:auto}.tracking-page::before{opacity:.32;background-position:58% center}.note{opacity:.045}}@media(max-width:520px){.tracking-page{padding:1rem}.intro{margin-top:1.5rem}.lookup-form{padding:.8rem}.lookup-form button{justify-content:space-between}.data-panel,.timeline-panel{border-radius:1rem}.file-list>div{grid-template-columns:auto 1fr}.file-list small{grid-column:2}.timeline-panel ol{grid-template-columns:1fr}.sound-wave{display:none}}@media(prefers-reduced-motion:reduce){*{scroll-behavior:auto!important;transition:none!important}.sound-wave i{animation:none}}
</style>
