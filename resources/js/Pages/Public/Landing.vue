<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
defineProps<{
    faqs: { id: number; question: string; answer: string }[];
    period?: { status: string; closes_at: string };
}>();
const openFaq = ref<number | null>(null);
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
            <section class="section border-y border-white/10 bg-[#111]">
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Buat kamu yang...</p>
                    <h2>
                        Udah punya lagu di kepala. Tinggal bingung lanjutnya
                        gimana.
                    </h2>
                    <div class="cards">
                        <article>
                            <b>Nulis emang jago. Produksi masih meraba-raba.</b>
                            <p>
                                Kamu tahu lagunya harus kedengeran gimana
                                enaknya, tapi begitu masuk software rekaman,
                                semua terasa asing.
                            </p>
                        </article>
                        <article>
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
                        <article>
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
            <section id="cara" class="section">
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Cara kerjanya</p>
                    <h2>
                        Dari demo di HP kamu, sampai rilis resmi. Lima langkah.
                    </h2>
                    <ol class="mt-14 grid gap-4">
                        <li
                            v-for="(step, i) in steps"
                            :key="step[0]"
                            class="step"
                        >
                            <span>0{{ i + 1 }}</span>
                            <div>
                                <h3>{{ step[0] }}</h3>
                                <p>{{ step[1] }}</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </section>
            <section id="manfaat" class="section bg-[#111]">
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Kenapa ikut Original Sessions</p>
                    <h2>
                        Semua yang kamu butuh buat naik level, ada di satu
                        program.
                    </h2>
                    <div class="cards">
                        <article
                            v-for="(benefit, i) in benefits"
                            :key="benefit"
                        >
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
            <section id="tentang" class="section">
                <div class="mx-auto max-w-7xl">
                    <p class="eyebrow">Siapa di balik ini</p>
                    <h2>Dua nama yang saling melengkapi.</h2>
                    <div class="cards md:grid-cols-2">
                        <article>
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
                        <article>
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
            <section id="faq" class="section bg-[#111]">
                <div class="mx-auto max-w-4xl">
                    <p class="eyebrow">Yang sering ditanyain</p>
                    <h2>FAQ</h2>
                    <div class="mt-10 divide-y divide-white/10">
                        <article v-for="faq in faqs" :key="faq.id">
                            <button
                                class="flex w-full items-center justify-between py-6 text-left text-lg font-bold"
                                :aria-expanded="openFaq === faq.id"
                                @click="
                                    openFaq = openFaq === faq.id ? null : faq.id
                                "
                            >
                                {{ faq.question }}<span>+</span>
                            </button>
                            <p
                                v-show="openFaq === faq.id"
                                class="pb-6 text-neutral-400"
                            >
                                {{ faq.answer }}
                            </p>
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
@media (prefers-reduced-motion: reduce) {
    .hero-video-media {
        display: none;
    }
}
</style>
