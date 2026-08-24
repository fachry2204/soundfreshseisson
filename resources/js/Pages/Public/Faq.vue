<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";

type Faq = { id: number; question: string; answer: string };

const props = defineProps<{ faqs: Faq[] }>();
const openFaq = ref<number | null>(null);
const query = ref("");

const filteredFaqs = computed(() => {
    const keyword = query.value.trim().toLocaleLowerCase("id-ID");
    if (!keyword) return props.faqs;

    return props.faqs.filter((faq) =>
        `${faq.question} ${faq.answer}`.toLocaleLowerCase("id-ID").includes(keyword),
    );
});

function toggle(id: number) {
    openFaq.value = openFaq.value === id ? null : id;
}
</script>

<template>
    <Head title="FAQ — Original Sessions">
        <meta
            head-key="description"
            name="description"
            content="Jawaban lengkap seputar pendaftaran, seleksi, produksi, hak cipta, dan promosi Original Sessions."
        />
    </Head>

    <div class="faq-page">
        <a href="#faq-content" class="skip-link">Lewati ke konten</a>

        <header class="site-header">
            <nav>
                <Link href="/" class="brand">
                    ORIGINAL <span>SESSIONS</span>
                    <small>SOUNDFRESH × D'MASIV</small>
                </Link>
                <div class="nav-links">
                    <Link href="/#cara">Cara Kerja</Link>
                    <Link href="/#manfaat">Manfaat</Link>
                    <Link href="/#tentang">Tentang</Link>
                    <Link href="/faq" class="active">FAQ</Link>
                </div>
                <Link href="/daftar" class="nav-cta">Kirim Lagu</Link>
            </nav>
        </header>

        <main id="faq-content">
            <section class="faq-hero">
                <div class="vinyl vinyl-left" aria-hidden="true"><i></i></div>
                <div class="vinyl vinyl-right" aria-hidden="true"><i></i></div>
                <div class="sound-lines" aria-hidden="true">
                    <i v-for="n in 22" :key="n"></i>
                </div>
                <div class="hero-inner">
                    <Link href="/" class="back-link">← Kembali ke beranda</Link>
                    <div class="hero-copy">
                        <div>
                            <p class="eyebrow">Pusat informasi peserta</p>
                            <h1>Pertanyaan yang<br /><em>sering ditanyakan.</em></h1>
                        </div>
                        <p>
                            Temukan jawaban lengkap seputar pendaftaran, proses
                            seleksi, produksi bersama D'MASIV, hak cipta, hingga
                            perilisan lagu.
                        </p>
                    </div>
                    <label class="search-box">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>
                        <span class="sr-only">Cari pertanyaan</span>
                        <input
                            v-model="query"
                            type="search"
                            placeholder="Cari topik atau pertanyaan…"
                        />
                        <b>{{ filteredFaqs.length }} jawaban</b>
                    </label>
                </div>
            </section>

            <section class="faq-section">
                <div class="section-heading">
                    <p>Semua pertanyaan</p>
                    <span>Klik kartu untuk membuka jawaban</span>
                </div>

                <div v-if="filteredFaqs.length" class="faq-list">
                    <article
                        v-for="(faq, index) in filteredFaqs"
                        :key="faq.id"
                        :class="['faq-card', { open: openFaq === faq.id }]"
                    >
                        <button
                            type="button"
                            :aria-expanded="openFaq === faq.id"
                            :aria-controls="`answer-${faq.id}`"
                            @click="toggle(faq.id)"
                        >
                            <span class="number">{{
                                String(index + 1).padStart(2, "0")
                            }}</span>
                            <span class="question">{{ faq.question }}</span>
                            <span class="toggle-icon" aria-hidden="true">
                                <i></i><i></i>
                            </span>
                        </button>
                        <div :id="`answer-${faq.id}`" class="answer-shell">
                            <div>
                                <p>{{ faq.answer }}</p>
                            </div>
                        </div>
                    </article>
                </div>

                <div v-else class="empty-state">
                    <span>♪</span>
                    <h2>Pertanyaan belum ditemukan</h2>
                    <p>Coba gunakan kata kunci yang lebih singkat.</p>
                    <button type="button" @click="query = ''">Tampilkan semua</button>
                </div>
            </section>

            <section class="help-cta">
                <div class="mini-wave" aria-hidden="true">
                    <i v-for="n in 18" :key="n"></i>
                </div>
                <p>Sudah siap memperdengarkan lagumu?</p>
                <h2>Kirim karya terbaikmu sekarang.</h2>
                <Link href="/daftar">Kirim Lagu →</Link>
            </section>
        </main>

        <footer>
            <span>© 2026 Original Sessions</span>
            <div><Link href="/legal/terms">Syarat</Link><Link href="/legal/privacy">Privasi</Link></div>
        </footer>
    </div>
</template>

<style scoped>
.faq-page{min-height:100vh;background:#090909;color:#f8f7f4;font-family:Inter,ui-sans-serif,system-ui,sans-serif}.skip-link{position:fixed;left:1rem;top:-5rem;z-index:100;background:#ff6b16;color:#080808;padding:.75rem 1rem;font-weight:800}.skip-link:focus{top:1rem}.site-header{position:sticky;top:0;z-index:40;border-bottom:1px solid #ffffff14;background:#080808e8;backdrop-filter:blur(18px)}nav{display:flex;max-width:1280px;margin:auto;align-items:center;justify-content:space-between;padding:1rem 1.5rem}.brand{font-size:1.05rem;font-weight:900;line-height:1.05;letter-spacing:-.03em}.brand span,.eyebrow{color:#ff6b16}.brand small{display:block;margin-top:.42rem;color:#898989;font-size:.58rem;letter-spacing:.25em}.nav-links{display:flex;gap:2rem;color:#bbb;font-size:.9rem}.nav-links a{position:relative;padding:.5rem 0;transition:.2s}.nav-links a:hover,.nav-links .active{color:#fff}.nav-links .active:after{content:"";position:absolute;right:0;bottom:0;left:0;height:2px;background:#ff6b16}.nav-cta,.help-cta>a{border-radius:999px;background:#ff6b16;color:#080808;font-weight:800;padding:.85rem 1.4rem;transition:.2s}.nav-cta:hover,.help-cta>a:hover{background:#ff853f;transform:translateY(-2px)}.faq-hero{position:relative;overflow:hidden;border-bottom:1px solid #ffffff12;background:radial-gradient(circle at 82% 20%,#57210e55,transparent 28%),linear-gradient(125deg,#0a0a0a 35%,#160e09)}.hero-inner{position:relative;z-index:2;max-width:1120px;margin:auto;padding:5rem 1.5rem 4rem}.back-link{display:inline-flex;color:#aaa;font-size:.86rem;transition:.2s}.back-link:hover{color:#ff6b16}.hero-copy{display:grid;grid-template-columns:1.35fr .65fr;gap:4rem;align-items:end;margin-top:3.5rem}.eyebrow{text-transform:uppercase;letter-spacing:.22em;font-size:.7rem;font-weight:900}.hero-copy h1{margin-top:1rem;font-size:clamp(3rem,7vw,6.4rem);font-weight:800;line-height:.9;letter-spacing:-.065em}.hero-copy h1 em{color:#ff6b16;font-style:normal}.hero-copy>p{max-width:29rem;color:#aaa;font-size:1.05rem;line-height:1.8}.search-box{display:grid;grid-template-columns:1.25rem 1fr auto;align-items:center;gap:1rem;margin-top:3.4rem;padding:1rem 1.15rem;border:1px solid #ffffff20;border-radius:16px;background:#171717cc;box-shadow:0 24px 70px #0008}.search-box:focus-within{border-color:#ff6b16;box-shadow:0 0 0 4px #ff6b1615}.search-box svg{fill:none;stroke:#ff6b16;stroke-width:2}.search-box input{min-width:0;border:0;outline:0;background:transparent;color:#fff;font-size:1rem}.search-box input::placeholder{color:#777}.search-box b{color:#8e8e8e;font-size:.72rem;text-transform:uppercase;letter-spacing:.12em}.vinyl{position:absolute;width:280px;height:280px;border-radius:50%;border:1px solid #ff6b1640;background:repeating-radial-gradient(circle,#17120f 0 8px,#0b0b0b 9px 12px);opacity:.32}.vinyl i{position:absolute;inset:42%;border-radius:50%;background:#ff6b16}.vinyl-left{left:-170px;bottom:-90px}.vinyl-right{right:-130px;top:-160px}.sound-lines{position:absolute;right:8%;top:15%;display:flex;height:100px;align-items:center;gap:7px;opacity:.16;transform:rotate(-10deg)}.sound-lines i{width:4px;height:calc(22px + (var(--n, 1) * 2px));border-radius:4px;background:#ff6b16}.sound-lines i:nth-child(3n){height:82px}.sound-lines i:nth-child(2n){height:46px}.faq-section{max-width:980px;margin:auto;padding:5rem 1.5rem 6rem}.section-heading{display:flex;align-items:end;justify-content:space-between;margin-bottom:1.5rem}.section-heading p{font-size:1.4rem;font-weight:800}.section-heading span{color:#777;font-size:.85rem}.faq-list{display:flex;flex-direction:column;gap:.85rem}.faq-card{overflow:hidden;border:1px solid #ffffff15;border-radius:18px;background:linear-gradient(110deg,#151515,#111);transition:border-color .25s,transform .25s,box-shadow .25s}.faq-card:hover{border-color:#ff6b1666;transform:translateX(5px);box-shadow:-8px 0 0 #ff6b16}.faq-card.open{border-color:#ff6b1690;background:linear-gradient(110deg,#1b1511,#121212)}.faq-card>button{display:grid;width:100%;grid-template-columns:3.2rem 1fr 2.5rem;align-items:center;gap:1rem;padding:1.25rem 1.4rem;text-align:left}.number{color:#ff6b16;font-family:ui-monospace,monospace;font-size:.78rem;font-weight:900}.question{font-size:1.02rem;font-weight:750;line-height:1.45}.toggle-icon{position:relative;width:2.15rem;height:2.15rem;border:1px solid #ffffff1c;border-radius:50%;background:#1e1e1e}.toggle-icon i{position:absolute;left:50%;top:50%;width:.7rem;height:2px;border-radius:2px;background:#ff6b16;transform:translate(-50%,-50%);transition:.25s}.toggle-icon i:last-child{transform:translate(-50%,-50%) rotate(90deg)}.open .toggle-icon{background:#ff6b16}.open .toggle-icon i{background:#090909}.open .toggle-icon i:last-child{transform:translate(-50%,-50%)}.answer-shell{display:grid;grid-template-rows:0fr;transition:grid-template-rows .32s ease}.answer-shell>div{overflow:hidden}.answer-shell p{margin:0 5.9rem 0 5.6rem;padding:0;color:#aaa;line-height:1.8;opacity:0;transform:translateY(-6px);transition:.3s}.open .answer-shell{grid-template-rows:1fr}.open .answer-shell p{padding-bottom:1.5rem;opacity:1;transform:none}.empty-state{padding:5rem 2rem;border:1px dashed #ffffff20;border-radius:20px;text-align:center;background:#121212}.empty-state>span{color:#ff6b16;font-size:2rem}.empty-state h2{margin-top:1rem;font-size:1.5rem}.empty-state p{margin-top:.5rem;color:#888}.empty-state button{margin-top:1.5rem;color:#ff6b16;font-weight:800}.help-cta{position:relative;overflow:hidden;max-width:1120px;margin:0 auto 6rem;padding:4rem 2rem;border:1px solid #ff6b1645;border-radius:28px;background:radial-gradient(circle at 70% 50%,#ff6b1620,transparent 30%),#18110d;text-align:center}.help-cta p{color:#ff8a47;text-transform:uppercase;letter-spacing:.18em;font-size:.7rem;font-weight:900}.help-cta h2{margin:1rem 0 2.2rem;font-size:clamp(2rem,4vw,3.6rem);line-height:1.05;letter-spacing:-.04em}.mini-wave{display:flex;position:absolute;left:4%;bottom:-5px;height:60px;align-items:end;gap:6px;opacity:.14}.mini-wave i{width:4px;height:28px;background:#ff6b16}.mini-wave i:nth-child(3n){height:55px}.mini-wave i:nth-child(2n){height:40px}footer{display:flex;max-width:1280px;margin:auto;align-items:center;justify-content:space-between;border-top:1px solid #ffffff12;padding:2rem 1.5rem;color:#666;font-size:.78rem}footer div{display:flex;gap:1.5rem}footer a:hover{color:#ff6b16}@media(max-width:760px){.nav-links{display:none}.nav-cta{padding:.7rem 1rem}.hero-inner{padding-top:3.5rem}.hero-copy{grid-template-columns:1fr;gap:1.5rem;margin-top:2.5rem}.hero-copy h1{font-size:clamp(3rem,14vw,4.5rem)}.search-box{grid-template-columns:1.2rem 1fr}.search-box b{display:none}.faq-section{padding-top:3.5rem}.section-heading{display:block}.section-heading span{display:block;margin-top:.4rem}.faq-card>button{grid-template-columns:2rem 1fr 2.2rem;gap:.65rem;padding:1.1rem}.answer-shell p{margin:0 1.1rem 0 3.75rem}.help-cta{margin-right:1rem;margin-left:1rem;padding:3.5rem 1.25rem}footer{align-items:flex-start;gap:1rem;flex-direction:column}}@media(prefers-reduced-motion:reduce){*{scroll-behavior:auto!important;transition:none!important}}
/* Two-column FAQ layout: compact on wide screens, readable on mobile. */
@media (min-width: 761px) {
    .faq-section {
        max-width: 1120px;
    }

    .faq-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
        gap: 1rem;
    }

    .faq-card {
        min-width: 0;
    }

    .faq-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px #0006;
    }

    .faq-card > button {
        grid-template-columns: 2.5rem minmax(0, 1fr) 2.5rem;
        min-height: 88px;
        padding: 1.15rem 1.25rem;
    }

    .answer-shell p {
        margin: 0 1.25rem 0 4.75rem;
    }
}
</style>
