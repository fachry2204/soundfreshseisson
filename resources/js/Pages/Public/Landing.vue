<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
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
                    ><a href="#tentang">Tentang</a><Link href="/faq">FAQ</Link>
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
                    <figure class="hero-poster">
                        <img
                            src="/images/poster-dmasiv-original-sessions.jpg"
                            alt="Poster D'MASIV Original Sessions oleh SoundFresh"
                        />
                    </figure>
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
                            <span class="eyebrow">Mitra Produksi</span>
                            <div class="partner-title">
                                <img
                                    src="/images/partners/dmasiv-logo.png"
                                    alt="Logo D'MASIV"
                                    class="partner-logo partner-logo-dmasiv"
                                />
                            </div>
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
                            <span class="eyebrow">Distribusi & Promosi</span>
                            <div class="partner-title">
                                <img
                                    src="/images/partners/soundfresh-logo.png"
                                    alt="Logo SoundFresh"
                                    class="partner-logo partner-logo-soundfresh"
                                />
                            </div>
                            <p>
                                Platform yang membantu musisi independen merilis
                                karya ke seluruh platform streaming musik utama
                                — cepat, transparan, tanpa hambatan teknis.
                            </p>
                        </article>
                    </div>
                </div>
            </section>
            <section class="final-cta-section section text-center">
                <div class="cta-note cta-note-left" aria-hidden="true">♪</div>
                <div class="cta-note cta-note-right" aria-hidden="true">♫</div>
                <div class="final-cta mx-auto max-w-5xl">
                    <div class="cta-vinyl" aria-hidden="true"><i></i></div>
                    <div class="cta-wave" aria-hidden="true">
                        <i v-for="n in 24" :key="n"></i>
                    </div>
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
                    <div class="cta-equalizer" aria-hidden="true">
                        <i v-for="n in 18" :key="n"></i>
                    </div>
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
.hero-poster {
    position: relative;
    width: min(100%, 31rem);
    margin-inline: auto;
    overflow: hidden;
    border: 1px solid #ff762066;
    border-radius: 1.75rem;
    background: #130c07;
    box-shadow:
        0 2rem 5rem #000a,
        0 0 4rem #ff6a0018;
    isolation: isolate;
}
.hero-poster::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 1;
    pointer-events: none;
    border-radius: inherit;
    box-shadow: inset 0 0 0 1px #ffffff12;
}
.hero-poster img {
    display: block;
    width: 100%;
    height: auto;
    object-fit: contain;
    transition: transform 0.5s ease;
}
.hero-poster:hover img {
    transform: scale(1.018);
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
    align-items: stretch;
    gap: 1rem;
    margin-top: 3.5rem;
    grid-template-columns: repeat(3, minmax(0, 1fr));
}
.pain-grid article {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
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
    align-items: stretch;
    gap: 1rem;
    margin-top: 3.5rem;
    grid-template-columns: repeat(3, minmax(0, 1fr));
}
.benefit-grid article {
    position: relative;
    display: grid;
    align-content: start;
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
.benefit-grid article > span,
.benefit-grid article > b {
    padding-right: 5.5rem;
}
.benefit-grid p {
    grid-column: 1 / -1;
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
    max-width: 30rem;
}
.partner-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1.2rem;
}
.partner-logo {
    display: block;
    flex: 0 0 auto;
    object-fit: contain;
}
.partner-logo-dmasiv {
    width: min(15rem, 72%);
    height: 3.4rem;
    border-radius: 0.35rem;
}
.partner-logo-soundfresh {
    width: min(20rem, 88%);
    height: auto;
    max-height: 4rem;
    object-position: left center;
}
.partner-deck article:hover .partner-logo,
.partner-deck article:focus-visible .partner-logo {
    filter: drop-shadow(0 0 12px #ff762055);
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
        grid-column: auto;
    }
    .partner-deck {
        grid-template-columns: 1fr;
    }
    .process-section::after {
        display: none;
    }
}
@media (max-width: 640px) {
    .hero-poster {
        width: min(100%, 24rem);
        border-radius: 1.35rem;
    }
    .pain-grid,
    .benefit-grid {
        grid-template-columns: 1fr;
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
    align-items: start;
    gap: 1rem;
    margin-top: 3rem;
    grid-template-columns: repeat(3, minmax(0, 1fr));
}
.faq-item {
    position: relative;
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
    transform: translateY(-5px);
    box-shadow: 0 18px 45px #0006;
}
.faq-item.active {
    border-color: #ff6a0088;
    background: linear-gradient(135deg, #1b1b1b, #17130f);
}
.faq-trigger {
    display: grid;
    width: 100%;
    align-content: start;
    align-items: start;
    gap: 0.85rem;
    padding: 1.35rem;
    text-align: left;
    grid-template-columns: 1fr 2.75rem;
}
.faq-number {
    grid-column: 1;
    color: #ff7620;
    font-family: monospace;
    font-size: 0.78rem;
    font-weight: 700;
}
.faq-question {
    grid-column: 1 / -1;
    font-size: 1.05rem;
    font-weight: 700;
    line-height: 1.45;
}
.faq-icon {
    position: relative;
    display: grid;
    width: 2rem;
    height: 2rem;
    place-items: center;
    border: 1px solid #ffffff18;
    border-radius: 50%;
    background: #202020;
    grid-column: 2;
    grid-row: 1;
    justify-self: end;
    transition: 0.25s ease;
}
.faq-icon i {
    position: absolute;
    width: 0.65rem;
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
    padding: 0 1.5rem;
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
    padding-bottom: 1.75rem;
    opacity: 1;
    transform: none;
}
.final-cta-section {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    background:
        radial-gradient(circle at 50% 46%, #ff762018, transparent 35%),
        linear-gradient(135deg, #070707 0%, #100b08 50%, #070707 100%);
}
.final-cta-section::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    opacity: 0.22;
    background: repeating-linear-gradient(
        155deg,
        transparent 0 42px,
        #ff7620 43px 44px
    );
    mask-image: radial-gradient(ellipse at center, #000, transparent 72%);
}
.final-cta {
    position: relative;
    padding: clamp(3.5rem, 7vw, 6.5rem) clamp(1.5rem, 5vw, 5rem) 3.5rem;
    overflow: hidden;
    border: 1px solid #ff762050;
    border-radius: 2.25rem;
    background:
        linear-gradient(145deg, #2a1609e8, #120d0ae8),
        radial-gradient(circle at 50% 0, #ff762024, transparent 50%);
    box-shadow:
        0 35px 90px #00000088,
        inset 0 1px #ffffff12;
    transition: 0.4s ease;
}
.final-cta:hover {
    border-color: #ff762099;
    transform: translateY(-4px);
    box-shadow:
        0 42px 110px #000000aa,
        0 0 60px #ff762015,
        inset 0 1px #ffffff18;
}
.final-cta > :not(.cta-vinyl, .cta-wave, .cta-equalizer) {
    position: relative;
    z-index: 2;
}
.final-cta h2 {
    max-width: 52rem;
    margin-inline: auto;
    text-wrap: balance;
}
.cta-vinyl {
    position: absolute;
    width: 19rem;
    height: 19rem;
    top: -8rem;
    right: -6rem;
    border: 1px solid #ff762025;
    border-radius: 50%;
    opacity: 0.55;
    background: repeating-radial-gradient(
        circle,
        #ffffff09 0 3px,
        #00000040 4px 10px
    );
    transition: transform 1.2s ease;
}
.cta-vinyl::before,
.cta-vinyl::after,
.cta-vinyl i {
    content: "";
    position: absolute;
    border-radius: 50%;
}
.cta-vinyl::before {
    inset: 34%;
    background: #ff7620;
    box-shadow: 0 0 35px #ff762055;
}
.cta-vinyl::after {
    inset: 47%;
    background: #17110d;
}
.final-cta:hover .cta-vinyl {
    transform: rotate(80deg);
}
.cta-wave {
    position: absolute;
    inset: auto 4% 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: clamp(0.2rem, 0.65vw, 0.65rem);
    height: 3rem;
    opacity: 0.14;
}
.cta-wave i,
.cta-equalizer i {
    display: block;
    width: 3px;
    border-radius: 99px;
    background: #ff7620;
}
.cta-wave i {
    height: calc(8px + (var(--wave, 1) * 5px));
}
.cta-wave i:nth-child(3n) {
    height: 28px;
}
.cta-wave i:nth-child(4n) {
    height: 16px;
}
.cta-wave i:nth-child(5n) {
    height: 38px;
}
.cta-equalizer {
    display: flex;
    align-items: end;
    justify-content: center;
    gap: 0.3rem;
    height: 2rem;
    margin-top: 2.25rem;
    opacity: 0.65;
}
.cta-equalizer i {
    height: 0.55rem;
    animation: equalize 1.15s ease-in-out infinite alternate;
}
.cta-equalizer i:nth-child(3n) {
    animation-delay: -0.35s;
}
.cta-equalizer i:nth-child(4n) {
    animation-delay: -0.7s;
}
.cta-equalizer i:nth-child(5n) {
    animation-delay: -0.95s;
}
.cta-note {
    position: absolute;
    z-index: -1;
    color: #ff7620;
    font-family: Georgia, serif;
    font-size: clamp(7rem, 14vw, 15rem);
    line-height: 1;
    opacity: 0.08;
    filter: drop-shadow(0 0 30px #ff7620);
    animation: note-float 6s ease-in-out infinite alternate;
}
.cta-note-left {
    left: 1%;
    top: 20%;
    transform: rotate(-15deg);
}
.cta-note-right {
    right: 1%;
    bottom: 12%;
    transform: rotate(12deg);
    animation-delay: -3s;
}
@keyframes equalize {
    from {
        height: 0.35rem;
        opacity: 0.45;
    }
    to {
        height: 1.8rem;
        opacity: 1;
    }
}
@keyframes note-float {
    to {
        translate: 0 -1.25rem;
    }
}
@media (max-width: 950px) {
    .faq-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 640px) {
    .faq-intro {
        grid-template-columns: 1fr;
    }
    .faq-trigger {
        padding: 1.25rem;
        grid-template-columns: 1fr 2rem;
    }
    .faq-icon {
        width: 2rem;
        height: 2rem;
    }
    .faq-question {
        font-size: 0.95rem;
    }
    .faq-answer-wrap p {
        padding-right: 1.25rem;
        padding-left: 1.25rem;
    }
    .faq-list {
        grid-template-columns: 1fr;
    }
}
@media (prefers-reduced-motion: reduce) {
    .hero-video-media {
        display: none;
    }
    .cta-equalizer i,
    .cta-note {
        animation: none;
    }
    .hero-poster img {
        transition: none;
    }
}
</style>
