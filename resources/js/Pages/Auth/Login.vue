<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{ canResetPassword?: boolean; status?: string }>();
const showPassword = ref(false);
const form = useForm({ username: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="Login Admin" />
    <main class="login-page">
        <div class="glow"></div>
        <Link href="/" class="identity">
            <span class="logo">OS</span>
            <h1>Original Sessions</h1>
            <p>Admin Panel · 2026</p>
        </Link>
        <section class="login-card">
            <div class="card-heading"><span>AREA TERBATAS</span><h2>Masuk ke Dashboard</h2><p>Kelola submission dan proses kurasi dalam satu ruang.</p></div>
            <p v-if="status" class="status">{{ status }}</p>
            <form @submit.prevent="submit">
                <label for="username">User ID</label>
                <input id="username" v-model="form.username" type="text" required autofocus autocomplete="username" placeholder="Masukkan user ID">
                <InputError class="error" :message="form.errors.username" />
                <label for="password">Password</label>
                <div class="password-field">
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password" placeholder="Masukkan password">
                    <button type="button" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'" @click="showPassword=!showPassword">
                        <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    </button>
                </div>
                <InputError class="error" :message="form.errors.password" />
                <div class="options"><label class="remember"><Checkbox name="remember" v-model:checked="form.remember"/><span>Ingat saya</span></label><Link v-if="canResetPassword" href="/forgot-password">Lupa password?</Link></div>
                <button class="submit" :disabled="form.processing">{{ form.processing ? 'Memeriksa…' : 'Masuk' }} <span>→</span></button>
            </form>
        </section>
        <Link href="/" class="back">← Kembali ke halaman utama</Link>
        <small class="copyright">© 2026 Original Sessions. Secure admin access.</small>
    </main>
</template>

<style scoped>
.login-page{position:relative;display:flex;min-height:100vh;overflow:hidden;flex-direction:column;align-items:center;justify-content:center;background:#080d19;padding:36px 20px;color:#eef2f7}.glow{position:absolute;top:-260px;width:650px;height:480px;border-radius:50%;background:#ff6a0014;filter:blur(90px)}.identity{position:relative;text-align:center}.logo{display:grid;width:72px;height:72px;margin:auto;place-items:center;border:2px solid #ff7a24;border-radius:22px;background:#111827;box-shadow:0 0 35px #ff6a0033;color:#ff8133;font-family:'Space Grotesk';font-size:23px;font-weight:800;transform:rotate(-5deg)}.identity h1{margin-top:18px;font-size:30px;text-transform:uppercase;letter-spacing:-.03em}.identity p{margin-top:4px;color:#737e91;font-weight:600}.login-card{position:relative;width:100%;max-width:560px;margin-top:34px;border:1px solid #2a3446;border-radius:22px;background:#111827dd;padding:38px;box-shadow:0 24px 70px #0008;backdrop-filter:blur(16px)}.card-heading span{color:#ff7d2d;font-size:10px;font-weight:800;letter-spacing:.2em}.card-heading h2{margin-top:9px;font-size:26px}.card-heading p{margin-top:7px;color:#7e899a;font-size:14px}.login-card form{display:grid;margin-top:28px}.login-card label{margin-bottom:8px;color:#b7c0ce;font-size:13px;font-weight:700}.login-card input[type=text],.login-card input[type=password]{width:100%;border:1px solid #354156;border-radius:13px;background:#e9eff9;padding:15px 16px;color:#111827;outline:none}.login-card input:focus{border-color:#ff7a24;box-shadow:0 0 0 4px #ff6a001c}.password-field{position:relative}.password-field button{position:absolute;right:12px;top:50%;color:#657186;transform:translateY(-50%)}.password-field svg{width:20px;fill:none;stroke:currentColor;stroke-width:1.7}.login-card label[for=password]{margin-top:20px}.options{display:flex;align-items:center;justify-content:space-between;margin-top:20px}.remember{display:flex;align-items:center;gap:9px;margin:0!important}.options a{color:#929daf;font-size:12px}.options a:hover{color:#ff8a42}.submit{display:flex;justify-content:center;gap:12px;margin-top:22px;border-radius:13px;background:linear-gradient(90deg,#ef5c20,#ff7a24);padding:15px;color:white;font-weight:800;box-shadow:0 12px 30px #ff6a0026;transition:.2s}.submit:hover{transform:translateY(-1px);filter:brightness(1.08)}.submit:disabled{opacity:.5}.error{margin-top:6px}.status{margin-top:20px;color:#4ade80}.back{position:relative;margin-top:26px;color:#697589;font-size:13px}.back:hover{color:#f2f5f9}.copyright{position:relative;margin-top:22px;color:#404a5c}@media(max-width:600px){.login-page{justify-content:flex-start;padding-top:42px}.logo{width:60px;height:60px}.identity h1{font-size:23px}.login-card{margin-top:26px;padding:26px 20px}.card-heading h2{font-size:22px}.options{align-items:flex-start;gap:10px}.copyright{text-align:center}}
</style>
