<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps<{ registrationNumber: string }>();
const copied = ref(false);

async function copyNumber() {
    await navigator.clipboard.writeText(props.registrationNumber);
    copied.value = true;
    window.setTimeout(() => (copied.value = false), 2200);
}
</script>

<template>
    <Head title="Pendaftaran berhasil dikirim" />
    <main class="success-page">
        <div class="music-note note-one" aria-hidden="true">♪</div>
        <div class="music-note note-two" aria-hidden="true">♫</div>
        <div class="vinyl" aria-hidden="true"><i></i></div>
        <section class="success-card">
            <div class="success-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="m5 12.5 4.2 4.2L19 7" />
                </svg>
            </div>
            <p class="eyebrow">Pendaftaran berhasil</p>
            <h1>Data kamu berhasil dikirim!</h1>
            <p class="intro">
                Terima kasih sudah mengirimkan karya terbaikmu. Tim Original
                Sessions akan melakukan verifikasi dan kurasi terhadap data yang
                masuk.
            </p>

            <div class="registration-ticket">
                <div>
                    <small>Nomor pendaftaran</small>
                    <strong>{{ registrationNumber }}</strong>
                </div>
                <button type="button" @click="copyNumber">
                    {{ copied ? "Tersalin ✓" : "Salin nomor" }}
                </button>
            </div>

            <div class="next-info">
                <span aria-hidden="true">✦</span>
                <p>
                    Simpan nomor pendaftaran ini untuk melacak status. Jika
                    lolos ke tahap berikutnya, tim akan menghubungi melalui
                    email atau WhatsApp yang didaftarkan.
                </p>
            </div>

            <Link href="/" class="home-button">
                <span>←</span> Kembali ke Beranda
            </Link>
            <div class="equalizer" aria-hidden="true">
                <i v-for="n in 20" :key="n"></i>
            </div>
        </section>
    </main>
</template>

<style scoped>
.success-page {
    position: relative;
    display: grid;
    min-height: 100vh;
    overflow: hidden;
    place-items: center;
    padding: 2rem 1.25rem;
    color: #fff;
    background:
        radial-gradient(circle at 50% 45%, #ff6a0022, transparent 32%),
        repeating-linear-gradient(
            155deg,
            transparent 0 48px,
            #ff76200c 49px 50px
        ),
        #080808;
    isolation: isolate;
}
.success-card {
    position: relative;
    z-index: 2;
    width: min(44rem, 100%);
    padding: clamp(2rem, 6vw, 4rem);
    overflow: hidden;
    border: 1px solid #ff76204d;
    border-radius: 2rem;
    text-align: center;
    background: linear-gradient(145deg, #1c1714f2, #111111f5);
    box-shadow:
        0 35px 110px #000c,
        inset 0 1px #ffffff12;
}
.success-icon {
    display: grid;
    width: 5.25rem;
    height: 5.25rem;
    margin-inline: auto;
    border-radius: 50%;
    place-items: center;
    background: linear-gradient(135deg, #22c55e, #0c9b44);
    box-shadow:
        0 0 0 12px #22c55e13,
        0 18px 45px #16a34a44;
    animation: arrive 0.55s cubic-bezier(0.2, 0.8, 0.2, 1) both;
}
.success-icon svg {
    width: 2.8rem;
    fill: none;
    stroke: white;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.eyebrow {
    margin-top: 2rem;
    color: #ff7620;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}
h1 {
    max-width: 35rem;
    margin: 0.65rem auto 0;
    font-family: "Space Grotesk", sans-serif;
    font-size: clamp(2.2rem, 6vw, 4rem);
    font-weight: 800;
    line-height: 1.04;
    letter-spacing: -0.045em;
    text-wrap: balance;
}
.intro {
    max-width: 34rem;
    margin: 1.25rem auto 0;
    color: #b6b3ae;
    line-height: 1.75;
}
.registration-ticket {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 2rem;
    padding: 1.25rem 1.4rem;
    border: 1px dashed #ff762077;
    border-radius: 1.25rem;
    text-align: left;
    background: #ff76200d;
}
.registration-ticket small {
    display: block;
    color: #8e8b86;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.registration-ticket strong {
    display: block;
    margin-top: 0.35rem;
    color: #ff8a3d;
    font-family: ui-monospace, monospace;
    font-size: clamp(1.1rem, 4vw, 1.55rem);
}
.registration-ticket button {
    flex: 0 0 auto;
    padding: 0.7rem 1rem;
    border: 1px solid #ffffff1f;
    border-radius: 999px;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 700;
    background: #ffffff0b;
    transition: 0.2s ease;
}
.registration-ticket button:hover {
    border-color: #ff762066;
    background: #ff762018;
}
.next-info {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    text-align: left;
    color: #aaa7a2;
    background: #ffffff08;
    line-height: 1.6;
}
.next-info span {
    color: #ff7620;
}
.home-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.7rem;
    min-width: 15rem;
    margin-top: 2rem;
    padding: 1rem 1.5rem;
    border-radius: 999px;
    color: #111;
    font-weight: 800;
    background: linear-gradient(90deg, #ff6514, #ff8a2c);
    box-shadow: 0 15px 35px #ff6a0033;
    transition: 0.25s ease;
}
.home-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 45px #ff6a0044;
}
.equalizer {
    display: flex;
    align-items: end;
    justify-content: center;
    gap: 0.25rem;
    height: 1.5rem;
    margin-top: 1.75rem;
    opacity: 0.45;
}
.equalizer i {
    width: 2px;
    height: 0.35rem;
    border-radius: 9px;
    background: #ff7620;
    animation: equalize 0.9s ease-in-out infinite alternate;
}
.equalizer i:nth-child(3n) {
    animation-delay: -0.3s;
}
.equalizer i:nth-child(4n) {
    animation-delay: -0.6s;
}
.equalizer i:nth-child(5n) {
    animation-delay: -0.8s;
}
.vinyl {
    position: absolute;
    z-index: 0;
    width: 27rem;
    height: 27rem;
    right: -12rem;
    bottom: -12rem;
    border-radius: 50%;
    opacity: 0.18;
    background: repeating-radial-gradient(circle, #fff 0 2px, #111 3px 10px);
    animation: spin 18s linear infinite;
}
.vinyl i {
    position: absolute;
    inset: 38%;
    border-radius: 50%;
    background: #ff7620;
}
.music-note {
    position: absolute;
    z-index: 0;
    color: #ff7620;
    font-family: Georgia, serif;
    font-size: clamp(8rem, 18vw, 16rem);
    opacity: 0.08;
    animation: float 5s ease-in-out infinite alternate;
}
.note-one {
    left: 2%;
    top: 12%;
}
.note-two {
    right: 3%;
    top: 10%;
    animation-delay: -2.5s;
}
@keyframes arrive {
    from {
        opacity: 0;
        transform: scale(0.55) rotate(-12deg);
    }
    to {
        opacity: 1;
        transform: none;
    }
}
@keyframes equalize {
    to {
        height: 1.4rem;
        opacity: 1;
    }
}
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
@keyframes float {
    to {
        transform: translateY(-1.5rem) rotate(5deg);
    }
}
@media (max-width: 560px) {
    .success-card {
        padding: 2rem 1.25rem;
        border-radius: 1.5rem;
    }
    .registration-ticket {
        align-items: stretch;
        flex-direction: column;
    }
    .registration-ticket button {
        width: 100%;
    }
    .next-info {
        font-size: 0.85rem;
    }
}
@media (prefers-reduced-motion: reduce) {
    .success-icon,
    .equalizer i,
    .vinyl,
    .music-note {
        animation: none;
    }
}
</style>
