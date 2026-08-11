<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const form=useForm({registration_number:'',email:''});
const page=usePage();
const success=computed(()=>(page.props.flash as {success?:string})?.success);
</script>
<template><Head title="Lacak Pendaftaran"/><main class="grid min-h-screen place-items-center bg-[#080808] px-5 text-white"><section class="w-full max-w-lg rounded-3xl border border-white/10 bg-[#181818] p-8"><Link href="/" class="text-sm text-neutral-400">← Beranda</Link><p class="eyebrow mt-8">Portal pendaftar</p><h1 class="mt-3 font-display text-4xl font-bold">Lacak demo kamu.</h1><p class="mt-3 text-neutral-400">Kami akan mengirim tautan aman ke email yang terdaftar.</p><div v-if="success" class="mt-6 rounded-xl bg-green-500/10 p-4 text-sm text-green-300">{{success}}</div><form class="mt-8 space-y-5" @submit.prevent="form.post('/tracking/magic-link')"><label>Nomor pendaftaran<input v-model="form.registration_number" placeholder="OS-2026-000001" required></label><label>Email terdaftar<input v-model="form.email" type="email" required></label><p v-for="error in form.errors" :key="error" class="text-sm text-red-400">{{error}}</p><button class="cta w-full justify-center" :disabled="form.processing">{{form.processing?'Mengirim…':'Kirim Magic Link'}}</button></form></section></main></template>
<style scoped>label{display:grid;gap:.5rem;font-size:.875rem;font-weight:600}input{border:1px solid #30302e;background:#111;border-radius:14px;padding:.9rem;color:white}</style>
