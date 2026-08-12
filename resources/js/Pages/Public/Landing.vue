<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
defineProps<{
    faqs: { id: number; question: string; answer: string }[];
    period?: { status: string; closes_at: string };
}>();
const openFaq = ref<number | null>(null);
const activeStep = ref(0);
const steps = [
    [
        "Kirim Demo",
        "Rekam video kamu nyanyiin lagu kamu + sedikit cerita di baliknya. Nggak perlu produksi bagus-bagus pakai AI, yang penting jujur.",
    ],
    [
        "Dikurasi",
        "Tim A&R SoundFresh & personel D'MASIV dengerin satu per satu submission yang masuk.",
    ],
    [
        "Diumumin",
        "Kalau lagumu kepilih, kami hubungi langsung buat lanjut ke tahap produksi.",
    ],
    [
        "Produksi Bareng D'MASIV",
        "Workshop, aransemen, mixing & mastering dengan standar internasional.",
    ],
    [
        "Rilis & Dipromosiin",
        "Lagu resmi rilis ke semua platform musik digital & dipromosikan penuh sama SoundFresh.id.",
    ],
];
const benefits = [
    "Produksi Profesional, Gratis",
    "Rilis ke Semua Platform",
    "Mentoring Langsung",
    "Dipromosikan Serius",
    "Konten Buat Portofolio",
    "Masuk Circle yang Tepat",
];
</script>

<template>
    <Head title="Original Sessions — SoundFresh × D'MASIV"
        ><meta
            head-key="description"
            name="description"
            content="Program gratis untuk singer-songwriter independen Indonesia."
    /></Head>
    <div
        class="min-h-screen bg-[#080808] text-[#F7F7F5] selection:bg-orange-500/40"
    >
        <a
            href="#main"
            class="sr-only focus:not-sr-only focus:fixed focus:z-50 focus:bg-orange-500 focus:p-3"
            >Lewati ke konten</a
        >
        <header
            class="sticky top-0 z-40 border-b border-white/10 bg-black/75 backdrop-blur-xl"
        >
            <nav
                class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4"
            >
                <a href="#" class="font-display text-lg font-bold"
                    >ORIGINAL <span class="text-orange-500">SESSIONS</span
                    ><small
                        class="block text-[10px] tracking-[.2em] text-neutral-400"
                        >SOUNDFRESH × D'MASIV</small
                    ></a
                >
                <div class="hidden gap-7 text-sm text-neutral-300 md:flex">
                    <a href="#cara">Cara Kerja</a><a href="#manfaat">Manfaat</a
                    ><a href="#tentang">Tentang</a><a href="#faq">FAQ</a>
                </div>
                <Link
                    href="/daftar"
                    class="rounded-full bg-orange-500 px-5 py-3 text-sm font-bold text-black hover:bg-orange-400"
                    >Kirim Lagu</Link
                >
            </nav>
        </header>
        <main id="main">
            <section
                class="hero-video relative overflow-hidden px-5 py-24 md:py-32"
            >
                <div class="hero-video-media" aria-hidden="true">
                    <iframe
                        src="https://www.youtube.com/embed/CZ6wd4lp-S4?autoplay=1&mute=1&controls=0&loop=1&playlist=CZ6wd4lp-S4&playsinline=1&rel=0&modestbranding=1&disablekb=1"
                        title=""
                        tabindex="-1"
                        allow="autoplay; encrypted-media"
                    ></iframe>
                </div>
                <div class="hero-video-overlay" aria-hidden="true"></div>
                <div class="orb"></div>
                <div
                    class="relative z-10 mx-auto grid max-w-7xl items-center gap-16 lg:grid-cols-[1.1fr_.9fr]"
                >
                    <div>
                        <p class="eyebrow">
                            SoundFresh × D'MASIV — Open submission gratis
                        </p>
                        <h1
                            class="mt-5 max-w-4xl font-display text-5xl font-bold leading-[.98] tracking-tight md:text-7xl"
                        >
                            Kesempatan agar lagu kamu
                            <span class="text-orange-500"
                                >siap didengar dunia.</span
                            >
                        </h1>
                        <p
                            class="mt-7 max-w-2xl text-lg leading-8 text-neutral-300"
                        >
                            Original Sessions ngajak singer-songwriter
                            independen buat kirim lagu ciptaan sendiri. Yang
                            terpilih diproduksi langsung bareng personel
                            D'MASIV, lalu dirilis & dipromosikan penuh lewat
                            SoundFresh.id — dari awal sampai akhir, tanpa biaya.
                        </p>
                        <div class="mt-9 flex flex-wrap gap-4">
                            <Link href="/daftar" class="cta"
                                >Kirim Lagu Kamu <span>→</span></Link
                            ><a
                                href="#cara"
                                class="rounded-full border border-white/20 px-6 py-4 font-semibold"
                                >Lihat cara kerjanya ↓</a
                            >
                        </div>
                        <p class="mt-8 text-sm text-neutral-400">
                            Rp0 biaya submission · Diproduksi D'MASIV · Rilis ke
                            semua platform
                        </p>
                    </div>
                    <div class="player-card">
                        <p
                            class="text-xs uppercase tracking-[.25em] text-orange-400"
                        >
                            Demo → Master
                        </p>
                        <div
                            class="my-8 flex h-40 items-center gap-1"
                            aria-hidden="true"
                        >
                            <i
                                v-for="n in 38"
                                :key="n"
                                class="wave"
                                :style="{ height: `${22 + ((n * 37) % 78)}%` }"
                            ></i>
                        </div>
                        <div class="flex items-center justify-between">
                            <button
                                class="grid size-14 place-items-center rounded-full bg-orange-500 text-black"
                                aria-label="Putar audio demo"
                            >
                                ▶
                            </button>
                            <div>
                                <b>01 — Lagu Kamu</b>
                                <p class="text-sm text-neutral-500">
                                    Demo / Master
                                </p>
                            </div>
                            <span class="font-mono text-sm">00:00 / 03:42</span>
                        </div>
                    </div>
                </div>
            </section>
            <section class="music-section pain-section section">
                <div class="music-staff" aria-hidden="true"></div>
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Buat kamu yang...</p>
                    <h2>
                        Udah punya lagu di kepala. Tinggal bingung lanjutnya
                        gimana.
                    </h2>
                    <div class="pain-grid">
                        <article tabindex="0">
                            <span class="track-number">TRACK 01</span>
                            <b>Nulis emang jago. Produksi masih meraba-raba.</b>
                            <p>
                                Kamu tahu lagunya harus kedengeran gimana
                                enaknya, tapi begitu masuk software rekaman,
                                semua terasa asing.
                            </p>
                        </article>
                        <article tabindex="0">
                            <span class="track-number">TRACK 02</span>
                            <b
                                >Alat udah lengkap, hasil masih jauh dari
                                standar rilis.</b
                            >
                            <p>
                                Interface, mic, plugin — semuanya ada. Tapi kok
                                hasil akhirnya nggak semulus lagu-lagu favorit
                                kamu.
                            </p>
                        </article>
                        <article tabindex="0">
                            <span class="track-number">TRACK 03</span>
                            <b
                                >Udah rilis sendiri, tapi nggak ada yang
                                dengar.</b
                            >
                            <p>
                                Upload ke Spotify gampang. Yang berat: bikin
                                orang lain nemuin lagumu di antara jutaan
                                rilisan lain.
                            </p>
                        </article>
                    </div>
                </div>
            </section>
            <section id="cara" class="music-section process-section section">
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Cara kerjanya</p>
                    <h2>
                        Dari demo di HP kamu, sampai rilis resmi. Lima langkah.
                    </h2>
                    <ol class="signal-chain">
                        <li
                            v-for="(step, i) in steps"
                            :key="step[0]"
                            :class="[
                                'signal-step',
                                { active: activeStep === i },
                            ]"
                            role="button"
                            tabindex="0"
                            :aria-pressed="activeStep === i"
                            @click="activeStep = i"
                            @focus="activeStep = i"
                            @keydown.enter="activeStep = i"
                        >
                            <span class="step-index">0{{ i + 1 }}</span>
                            <div>
                                <h3>{{ step[0] }}</h3>
                                <p>{{ step[1] }}</p>
                            </div>
                            <span class="step-meter" aria-hidden="true"
                                ><i v-for="bar in 5" :key="bar"></i
                            ></span>
                        </li>
                    </ol>
                </div>
            </section>
            <section
                id="manfaat"
                class="music-section benefits-section section"
            >
                <div class="vinyl-orbit" aria-hidden="true"></div>
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Kenapa ikut Original Sessions</p>
                    <h2>
                        Semua yang kamu butuh buat naik level, ada di satu
                        program.
                    </h2>
                    <div class="benefit-grid">
                        <article
                            v-for="(benefit, i) in benefits"
                            :key="benefit"
                            tabindex="0"
                        >
                            <div class="album-mark" aria-hidden="true">
                                <i></i>
                            </div>
                            <span class="text-orange-500">✦ 0{{ i + 1 }}</span
                            ><b>{{ benefit }}</b>
                            <p>
                                {{
                                    [
                                        "Rekaman, aransemen, mixing & mastering bareng D'MASIV, tanpa biaya sepeser pun.",
                                        "Lagu kamu tayang di Spotify, Apple Music, YouTube Music, dan platform lain lewat SoundFresh.id.",
                                        "Belajar langsung dari musisi yang udah lebih dulu ada di posisi kamu sekarang.",
                                        "Kampanye promosi lintas kanal — bukan cuma diunggah terus didoain viral sendiri.",
                                        "Dokumentasi proses produksi & behind-the-scenes yang bisa kamu pakai sendiri nanti.",
                                        "Kenalan sama tim A&R, produser, dan sesama singer-songwriter independen lainnya.",
                                    ][i]
                                }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>
            <section
                id="tentang"
                class="music-section partners-section section"
            >
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Siapa di balik ini</p>
                    <h2>Dua nama yang saling melengkapi.</h2>
                    <div class="partner-deck">
                        <article tabindex="0">
                            <span class="eyebrow">Mitra Produksi</span
                            ><b class="text-3xl">D'MASIV</b>
                            <p>
                                Salah satu band rock paling berpengaruh di
                                Indonesia, dengan jam terbang menulis lagu,
                                mengaransemen, dan memproduksi karya yang
                                diterima luas oleh publik. Di Original Sessions,
                                mereka turun langsung sebagai produser & mentor
                                bagi lagu-lagu terpilih.
                            </p>
                        </article>
                        <article tabindex="0">
                            <span class="eyebrow">Distribusi & Promosi</span
                            ><b class="text-3xl">SoundFresh.id</b>
                            <p>
                                Platform yang membantu musisi independen merilis
                                karya ke seluruh platform streaming musik utama
                                — cepat, transparan, tanpa hambatan teknis.
                            </p>
                        </article>
                    </div>
                </div>
            </section>
            <section id="faq" class="faq-section section">
                <div class="mx-auto max-w-5xl">
                    <div class="faq-intro">
                        <div>
                            <p class="eyebrow">Yang sering ditanyain</p>
                            <h2>FAQ</h2>
                        </div>
                        <p>
                            Punya pertanyaan? Klik salah satu topik di bawah
                            untuk melihat jawabannya.
                        </p>
                    </div>
                    <div class="faq-list">
                        <article
                            v-for="(faq, index) in faqs"
                            :key="faq.id"
                            :class="[
                                'faq-item',
                                { active: openFaq === faq.id },
                            ]"
                        >
                            <button
                                class="faq-trigger"
                                :aria-expanded="openFaq === faq.id"
                                :aria-controls="`faq-answer-${faq.id}`"
                                @click="
                                    openFaq = openFaq === faq.id ? null : faq.id
                                "
                            >
                                <span class="faq-number">{{
                                    String(index + 1).padStart(2, "0")
                                }}</span>
                                <span class="faq-question">{{
                                    faq.question
                                }}</span>
                                <span class="faq-icon" aria-hidden="true"
                                    ><i></i><i></i
                                ></span>
                            </button>
                            <div
                                :id="`faq-answer-${faq.id}`"
                                class="faq-answer-wrap"
                            >
                                <div>
                                    <p>{{ faq.answer }}</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>
            <section class="section text-center">
                <div
                    class="mx-auto max-w-4xl rounded-[2rem] border border-orange-500/30 bg-orange-500/10 p-10 md:p-16"
                >
                    <p class="eyebrow">Gelombang pendaftaran dibuka sekarang</p>
                    <h2>
                        Lagu kamu udah nunggu buat didengar lebih banyak orang.
                    </h2>
                    <p class="mx-auto mt-5 max-w-xl text-neutral-300">
                        Isi form, kasih tautan demo kamu, dan biarkan kami yang
                        urus sisanya.
                    </p>
                    <Link href="/daftar" class="cta mt-8 inline-flex"
                        >Kirim Demo Sekarang →</Link
                    >
                </div>
            </section>
        </main>
        <footer
            class="border-t border-white/10 px-5 py-10 text-center text-sm text-neutral-500"
        >
            <div class="mb-4 flex justify-center gap-5">
                <Link
                    href="/tracking"
                    class="text-neutral-300 hover:text-orange-400"
                    >Lacak pendaftaran</Link
                ><Link
                    href="/legal/terms"
                    class="text-neutral-300 hover:text-orange-400"
                    >Syarat</Link
                ><Link
                    href="/legal/privacy"
                    class="text-neutral-300 hover:text-orange-400"
                    >Privasi</Link
                >
            </div>
            <p>© 2026 SoundFresh.id — sebuah kolaborasi bersama D'MASIV</p>
        </footer>
    </div>
</template>

<style scoped>
.hero-video-media,
.hero-video-overlay {
    position: absolute;
    inset: 0;
    pointer-events: none;
}
.music-section {
    position: relative;
    overflow: hidden;
    isolation: isolate;
}
.music-section > * {
    position: relative;
    z-index: 1;
}
.pain-section {
    border-block: 1px solid #ffffff12;
    background:
        radial-gradient(circle at 85% 25%, #ff6a0015, transparent 28%), #101010;
}
.music-staff {
    position: absolute;
    inset: 8% -5% auto 48%;
    height: 18rem;
    opacity: 0.16;
    transform: rotate(-7deg);
    background: repeating-linear-gradient(
        to bottom,
        transparent 0 34px,
        #ff7620 35px 36px
    );
    mask-image: linear-gradient(
        90deg,
        transparent,
        #000 20%,
        #000 75%,
        transparent
    );
}
.pain-grid {
    display: grid;
    gap: 1rem;
    margin-top: 3.5rem;
    grid-template-columns: 1.15fr 0.85fr 0.85fr;
}
.pain-grid article {
    position: relative;
    min-height: 19rem;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    gap: 1rem;
    padding: 2rem;
    overflow: hidden;
    border: 1px solid #ffffff14;
    border-radius: 1.75rem;
    background: linear-gradient(145deg, #1c1c1c, #141414);
    transition: 0.35s ease;
}
.pain-grid article::before {
    content: "";
    position: absolute;
    width: 12rem;
    height: 12rem;
    right: -4rem;
    top: -4rem;
    border: 1px solid #ffffff12;
    border-radius: 50%;
    box-shadow:
        0 0 0 20px #ffffff05,
        0 0 0 42px #ffffff04;
    transition: 0.5s ease;
}
.pain-grid article:hover,
.pain-grid article:focus-visible {
    outline: 0;
    border-color: #ff762088;
    transform: translateY(-7px);
    box-shadow: 0 24px 60px #0008;
}
.pain-grid article:hover::before,
.pain-grid article:focus-visible::before {
    transform: rotate(40deg) scale(1.1);
    border-color: #ff762066;
}
.pain-grid b {
    max-width: 24rem;
    font-size: 1.25rem;
    line-height: 1.35;
}
.pain-grid p {
    color: #a3a3a0;
    line-height: 1.75;
}
.track-number {
    color: #ff7620;
    font-family: monospace;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.18em;
}
.process-section {
    background: linear-gradient(180deg, #080808, #0d0b09);
}
.process-section::after {
    content: "";
    position: absolute;
    left: 9%;
    right: 9%;
    top: 53%;
    height: 1px;
    background: linear-gradient(90deg, transparent, #ff762055, transparent);
    box-shadow: 0 0 30px #ff762055;
}
.signal-chain {
    display: grid;
    gap: 0.8rem;
    margin-top: 3.5rem;
}
.signal-step {
    display: grid;
    grid-template-columns: 5rem 1fr 6rem;
    align-items: center;
    gap: 1.25rem;
    min-height: 7.5rem;
    padding: 1.5rem 1.75rem;
    border: 1px solid #ffffff12;
    border-radius: 1.25rem;
    background: #101010ee;
    cursor: pointer;
    transition: 0.3s ease;
}
.signal-step:hover,
.signal-step:focus-visible,
.signal-step.active {
    outline: 0;
    border-color: #ff762066;
    background: linear-gradient(90deg, #201309, #151515 55%);
    transform: translateX(0.5rem);
}
.step-index {
    color: #ff7620;
    font-family: "Space Grotesk", sans-serif;
    font-size: 2rem;
    font-weight: 700;
}
.signal-step h3 {
    font-size: 1.25rem;
    font-weight: 700;
}
.signal-step p {
    margin-top: 0.3rem;
    color: #9f9f9b;
    line-height: 1.6;
}
.step-meter {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 5px;
    height: 2rem;
}
.step-meter i {
    width: 4px;
    height: 25%;
    border-radius: 9px;
    background: #474747;
    transition: 0.3s ease;
}
.signal-step.active .step-meter i {
    background: #ff7620;
    animation: meter 0.8s ease-in-out infinite alternate;
}
.signal-step.active .step-meter i:nth-child(2n) {
    animation-delay: 0.15s;
    height: 70%;
}
.signal-step.active .step-meter i:nth-child(3n) {
    animation-delay: 0.3s;
    height: 100%;
}
@keyframes meter {
    to {
        height: 100%;
    }
}
.benefits-section {
    background:
        radial-gradient(circle at 0 55%, #ff6a0018, transparent 24%), #111;
}
.vinyl-orbit {
    position: absolute;
    width: 28rem;
    height: 28rem;
    left: -18rem;
    top: 30%;
    border: 1px solid #ff762044;
    border-radius: 50%;
    box-shadow:
        0 0 0 28px #ffffff05,
        0 0 0 55px #ff762018,
        0 0 90px #ff6a0025;
}
.benefit-grid {
    display: grid;
    gap: 1rem;
    margin-top: 3.5rem;
    grid-template-columns: repeat(3, 1fr);
}
.benefit-grid article {
    position: relative;
    display: grid;
    min-height: 18rem;
    align-content: end;
    gap: 0.85rem;
    padding: 1.75rem;
    overflow: hidden;
    border: 1px solid #ffffff12;
    border-radius: 1.5rem;
    background: #181818;
    transition: 0.35s ease;
}
.benefit-grid article:hover,
.benefit-grid article:focus-visible {
    outline: 0;
    border-color: #ff762077;
    transform: translateY(-6px);
    background: #1c1815;
}
.benefit-grid b {
    font-size: 1.2rem;
}
.benefit-grid p {
    color: #a6a5a1;
    line-height: 1.65;
}
.album-mark {
    position: absolute;
    top: 1.6rem;
    right: 1.6rem;
    width: 4.5rem;
    height: 4.5rem;
    border: 1px solid #ffffff18;
    border-radius: 50%;
    background: repeating-radial-gradient(
        circle,
        #272727 0 3px,
        #1b1b1b 4px 7px
    );
    transition: 0.7s ease;
}
.album-mark i {
    position: absolute;
    inset: 42%;
    border-radius: 50%;
    background: #ff7620;
}
.benefit-grid article:hover .album-mark,
.benefit-grid article:focus-visible .album-mark {
    transform: rotate(150deg);
    box-shadow: 0 0 35px #ff762033;
}
.partners-section {
    background: linear-gradient(120deg, #080808 55%, #110d09);
}
.partner-deck {
    display: grid;
    gap: 1rem;
    margin-top: 3.5rem;
    grid-template-columns: 1fr 1fr;
}
.partner-deck article {
    position: relative;
    min-height: 22rem;
    padding: 2.25rem;
    overflow: hidden;
    border: 1px solid #ffffff14;
    border-radius: 1.75rem;
    background: #171717;
    transition: 0.35s ease;
}
.partner-deck article::after {
    content: "";
    position: absolute;
    width: 13rem;
    height: 13rem;
    right: -3rem;
    bottom: -4rem;
    border: 1px solid #ff762055;
    border-radius: 50%;
    box-shadow:
        0 0 0 20px #ffffff05,
        0 0 0 42px #ff762012;
}
.partner-deck article:hover,
.partner-deck article:focus-visible {
    outline: 0;
    border-color: #ff762077;
    transform: translateY(-5px);
    box-shadow: 0 25px 60px #0008;
}
.partner-deck article > * {
    position: relative;
    z-index: 1;
    display: block;
    max-width: 30rem;
}
.partner-deck article b {
    margin-top: 1.2rem;
}
.partner-deck article p {
    margin-top: 1.2rem;
    color: #aaa9a5;
    line-height: 1.8;
}
@media (max-width: 900px) {
    .pain-grid,
    .benefit-grid {
        grid-template-columns: 1fr 1fr;
    }
    .pain-grid article:first-child {
        grid-column: 1/-1;
    }
    .partner-deck {
        grid-template-columns: 1fr;
    }
    .process-section::after {
        display: none;
    }
}
@media (max-width: 640px) {
    .pain-grid,
    .benefit-grid {
        grid-template-columns: 1fr;
    }
    .pain-grid article:first-child {
        grid-column: auto;
    }
    .pain-grid article {
        min-height: 16rem;
    }
    .signal-step {
        grid-template-columns: 3rem 1fr;
        padding: 1.25rem;
        transform: none !important;
    }
    .step-meter {
        display: none;
    }
    .partner-deck article {
        min-height: 19rem;
        padding: 1.5rem;
    }
}
.hero-video-media {
    opacity: 0.6;
    overflow: hidden;
}
.hero-video-media iframe {
    position: absolute;
    top: 50%;
    left: 50%;
    width: max(100vw, 177.78vh);
    height: max(56.25vw, 100vh);
    border: 0;
    transform: translate(-50%, -50%);
}
.hero-video-overlay {
    background:
        linear-gradient(
            90deg,
            rgba(8, 8, 8, 0.92) 0%,
            rgba(8, 8, 8, 0.62) 55%,
            rgba(8, 8, 8, 0.76) 100%
        ),
        linear-gradient(180deg, rgba(8, 8, 8, 0.3), rgba(8, 8, 8, 0.8));
}
.faq-section {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(circle at 88% 10%, #ff6a0015, transparent 30%), #101010;
}
.faq-intro {
    display: grid;
    align-items: end;
    gap: 2rem;
    grid-template-columns: 1fr minmax(16rem, 0.65fr);
}
.faq-intro h2 {
    margin-top: 0.75rem;
}
.faq-intro > p {
    color: #a3a3a0;
    line-height: 1.7;
}
.faq-list {
    display: grid;
    gap: 0.85rem;
    margin-top: 3rem;
}
.faq-item {
    overflow: hidden;
    border: 1px solid #ffffff12;
    border-radius: 20px;
    background: #171717;
    transition:
        border-color 0.25s ease,
        background 0.25s ease,
        transform 0.25s ease;
}
.faq-item:hover {
    border-color: #ff6a0055;
    transform: translateY(-2px);
}
.faq-item.active {
    border-color: #ff6a0088;
    background: linear-gradient(135deg, #1b1b1b, #17130f);
}
.faq-trigger {
    display: grid;
    width: 100%;
    align-items: center;
    gap: 1.25rem;
    padding: 1.35rem 1.5rem;
    text-align: left;
    grid-template-columns: 2.5rem 1fr 2.75rem;
}
.faq-number {
    color: #ff7620;
    font-family: monospace;
    font-size: 0.78rem;
    font-weight: 700;
}
.faq-question {
    font-size: 1.05rem;
    font-weight: 700;
}
.faq-icon {
    position: relative;
    display: grid;
    width: 2.75rem;
    height: 2.75rem;
    place-items: center;
    border: 1px solid #ffffff18;
    border-radius: 50%;
    background: #202020;
    transition: 0.25s ease;
}
.faq-icon i {
    position: absolute;
    width: 0.85rem;
    height: 2px;
    border-radius: 99px;
    background: #ff7620;
    transition: transform 0.25s ease;
}
.faq-icon i:last-child {
    transform: rotate(90deg);
}
.faq-item.active .faq-icon {
    border-color: #ff7620;
    background: #ff7620;
}
.faq-item.active .faq-icon i {
    background: #0b0b0b;
}
.faq-item.active .faq-icon i:last-child {
    transform: rotate(0);
}
.faq-answer-wrap {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 0.32s ease;
}
.faq-answer-wrap > div {
    overflow: hidden;
}
.faq-answer-wrap p {
    max-width: 48rem;
    padding: 0 5.75rem 0;
    color: #aaa9a5;
    line-height: 1.8;
    opacity: 0;
    transform: translateY(-0.35rem);
    transition:
        opacity 0.25s ease,
        transform 0.25s ease,
        padding 0.32s ease;
}
.faq-item.active .faq-answer-wrap {
    grid-template-rows: 1fr;
}
.faq-item.active .faq-answer-wrap p {
    padding-bottom: 1.5rem;
    opacity: 1;
    transform: none;
}
@media (max-width: 640px) {
    .faq-intro {
        grid-template-columns: 1fr;
    }
    .faq-trigger {
        gap: 0.75rem;
        padding: 1rem;
        grid-template-columns: 2rem 1fr 2.5rem;
    }
    .faq-icon {
        width: 2.5rem;
        height: 2.5rem;
    }
    .faq-question {
        font-size: 0.95rem;
    }
    .faq-answer-wrap p {
        padding-right: 1rem;
        padding-left: 3.75rem;
    }
}
@media (prefers-reduced-motion: reduce) {
    .hero-video-media {
        display: none;
    }
}
</style>
