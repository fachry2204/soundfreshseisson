<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
defineOptions({ layout: AdminLayout });
defineProps<{ assignments: any[] }>();
</script>
<template><Head title="Tugas Kurasi"/><main class="min-h-screen bg-neutral-100 p-6"><div class="mx-auto max-w-5xl"><p class="text-sm font-bold uppercase tracking-widest text-orange-600">A&R / Kurator</p><h1 class="text-4xl font-bold">Tugas Kurasi</h1><div class="mt-8 overflow-hidden rounded-2xl bg-white"><Link v-for="a in assignments" :key="a.id" :href="`/admin/reviews/${a.id}`" class="grid grid-cols-[1fr_auto] border-b p-5 hover:bg-orange-50"><div><b>{{a.title}}</b><p class="text-sm text-neutral-500">{{a.registration_number}} · {{a.genre}}</p></div><span>{{a.status}} →</span></Link></div></div></main></template>
