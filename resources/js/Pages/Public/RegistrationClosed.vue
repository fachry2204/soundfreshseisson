<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Pendaftaran sedang ditutup" />
    <main class="closed-page">
        <div class="ambient ambient-left" aria-hidden="true"></div>
        <div class="ambient ambient-right" aria-hidden="true"></div>
        <div class="music-note note-one" aria-hidden="true">♪</div>
        <div class="music-note note-two" aria-hidden="true">♫</div>

        <section class="closed-card">
            <div class="record" aria-hidden="true">
                <span></span>
            </div>
            <p class="status-label">Pendaftaran sedang ditutup</p>
            <h1>Sampai jumpa di sesi berikutnya.</h1>
            <p class="description">
                Terima kasih atas antusiasmemu untuk Original Sessions. Saat ini
                kami belum menerima pendaftaran baru. Pantau informasi terbaru
                dan siapkan karya terbaikmu untuk kesempatan berikutnya.
            </p>

            <div class="info-row">
                <span class="pulse" aria-hidden="true"></span>
                <p>Form, pengiriman data, dan upload file sedang dinonaktifkan.</p>
            </div>

            <div class="actions">
                <Link href="/" class="primary-action">Kembali ke Beranda <span>→</span></Link>
                <Link href="/faq" class="secondary-action">Lihat FAQ</Link>
            </div>

            <div class="equalizer" aria-hidden="true">
                <i v-for="n in 26" :key="n"></i>
            </div>
        </section>
    </main>
</template>

<style scoped>
.closed-page { position: relative; display: grid; min-height: 100dvh; overflow: hidden; place-items: center; padding: 1.5rem; color: #f8f7f5; background: radial-gradient(circle at 50% 38%, #ff6a0018, transparent 30%), repeating-linear-gradient(155deg, transparent 0 58px, #ff76200a 59px 60px), #080808; isolation: isolate; }
.closed-card { position: relative; z-index: 2; width: min(44rem, 100%); padding: clamp(1.6rem, 5vw, 3.3rem); overflow: hidden; border: 1px solid #ff76204d; border-radius: 2rem; text-align: center; background: linear-gradient(145deg, #1d1713f5, #111111f7); box-shadow: 0 35px 110px #000d, inset 0 1px #ffffff12; }
.record { position: relative; width: 5rem; height: 5rem; margin-inline: auto; border: 1px solid #ffffff1a; border-radius: 50%; background: repeating-radial-gradient(circle, #24211f 0 3px, #111 4px 9px); box-shadow: 0 16px 36px #0008; animation: spin 14s linear infinite; }
.record::before { content: ""; position: absolute; inset: 24%; border-radius: 50%; background: linear-gradient(135deg, #ff6414, #ff8d32); }
.record span { position: absolute; z-index: 1; inset: 44%; border-radius: 50%; background: #111; }
.status-label { margin-top: 1.45rem; color: #ff7620; font-size: .72rem; font-weight: 800; letter-spacing: .21em; text-transform: uppercase; }
h1 { max-width: 38rem; margin: .7rem auto 0; font-family: "Space Grotesk", sans-serif; font-size: clamp(2.15rem, 6vw, 4rem); font-weight: 800; line-height: .98; letter-spacing: -.055em; text-wrap: balance; }
.description { max-width: 36rem; margin: 1.15rem auto 0; color: #b7b3ae; font-size: clamp(.92rem, 2vw, 1.03rem); line-height: 1.7; }
.info-row { display: flex; align-items: center; justify-content: center; gap: .75rem; max-width: 33rem; margin: 1.5rem auto 0; padding: .9rem 1.1rem; border: 1px solid #ffffff0d; border-radius: 1rem; color: #aaa6a1; background: #ffffff07; font-size: .82rem; }
.pulse { flex: 0 0 auto; width: .55rem; height: .55rem; border-radius: 50%; background: #ff7620; box-shadow: 0 0 0 0 #ff762077; animation: pulse 1.8s infinite; }
.actions { display: flex; align-items: center; justify-content: center; gap: .8rem; margin-top: 1.55rem; }
.actions a { display: inline-flex; align-items: center; justify-content: center; min-height: 3.2rem; padding: .8rem 1.5rem; border-radius: 999px; font-size: .9rem; font-weight: 800; transition: .25s ease; }
.primary-action { gap: .75rem; color: #111; background: linear-gradient(90deg, #ff6514, #ff8a2c); box-shadow: 0 15px 35px #ff6a0030; }
.secondary-action { border: 1px solid #ffffff20; color: #eee; background: #ffffff08; }
.actions a:hover { transform: translateY(-3px); }
.equalizer { display: flex; align-items: end; justify-content: center; gap: .28rem; height: 1.8rem; margin-top: 1.5rem; opacity: .45; }
.equalizer i { width: 2px; height: .4rem; border-radius: 1rem; background: #ff7620; animation: equalize .9s ease-in-out infinite alternate; }
.equalizer i:nth-child(3n) { animation-delay: -.25s; }.equalizer i:nth-child(4n) { animation-delay: -.55s; }.equalizer i:nth-child(5n) { animation-delay: -.75s; }
.ambient { position: absolute; z-index: 0; width: 28rem; height: 28rem; border: 1px solid #ff762026; border-radius: 50%; box-shadow: inset 0 0 0 2rem #ff762006, inset 0 0 0 5rem #ff762006; }
.ambient-left { left: -16rem; bottom: -10rem; }.ambient-right { right: -17rem; top: -13rem; }
.music-note { position: absolute; z-index: 0; color: #ff7620; font-family: Georgia, serif; font-size: clamp(8rem, 18vw, 15rem); opacity: .05; }
.note-one { left: 3%; top: 8%; transform: rotate(-10deg); }.note-two { right: 4%; bottom: 5%; transform: rotate(8deg); }
@keyframes spin { to { transform: rotate(360deg); } } @keyframes equalize { to { height: 1.6rem; opacity: 1; } } @keyframes pulse { 70% { box-shadow: 0 0 0 .65rem transparent; } 100% { box-shadow: 0 0 0 0 transparent; } }
@media (max-width: 560px) { .closed-page { padding: .85rem; } .closed-card { padding: 1.65rem 1rem; border-radius: 1.5rem; } .actions { align-items: stretch; flex-direction: column; } .actions a { width: 100%; } .info-row { align-items: flex-start; text-align: left; } }
@media (prefers-reduced-motion: reduce) { .record, .equalizer i, .pulse { animation: none; } .actions a { transition: none; } }
</style>
