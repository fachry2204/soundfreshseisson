<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import FileDropzone from '@/Components/FileDropzone.vue';

defineProps<{ submission: any; revisionUrl: string }>();

const revisionForm = useForm({ demo_url: '', video_url: '', file: null as File|null, note: '' });
const revisionFile = ref({ name: '', size: 0 });

function selectRevision(file: File) {
    revisionForm.file = file;
    revisionFile.value = { name: file.name, size: file.size };
}

function removeRevision() {
    revisionForm.file = null;
    revisionFile.value = { name: '', size: 0 };
}
</script>

<template>
    <Head title="Portal Pendaftar" />
    <div class="min-h-screen bg-[#080808] px-5 py-10 text-white">
        <main class="mx-auto max-w-4xl">
            <p class="eyebrow">Portal pendaftar</p>
            <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="font-display text-4xl font-bold">{{ submission.song.title }}</h1>
                    <p class="mt-2 font-mono text-orange-500">{{ submission.registration_number }}</p>
                </div>
                <span class="rounded-full bg-orange-500/15 px-5 py-2 text-sm font-bold text-orange-400">{{ submission.status }}</span>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-[1fr_.8fr]">
                <section class="rounded-3xl border border-white/10 bg-[#181818] p-7">
                    <h2 class="text-2xl font-bold">Perjalanan submission</h2>
                    <ol class="mt-7 space-y-6">
                        <li v-for="item in submission.timeline" :key="item.date" class="grid grid-cols-[1rem_1fr] gap-4">
                            <i class="mt-1 size-3 rounded-full bg-orange-500"></i>
                            <div><b>{{ item.label }}</b><p class="text-sm text-neutral-500">{{ new Date(item.date).toLocaleString('id-ID') }}</p></div>
                        </li>
                    </ol>
                </section>
                <aside class="space-y-6">
                    <section class="rounded-3xl border border-white/10 bg-[#181818] p-7">
                        <h2 class="text-xl font-bold">Data lagu</h2>
                        <dl class="mt-5 space-y-3 text-sm">
                            <div><dt>Genre</dt><dd>{{ submission.song.genre }}</dd></div>
                            <div><dt>Bahasa</dt><dd>{{ submission.song.language }}</dd></div>
                            <div><dt>Dikirim</dt><dd>{{ new Date(submission.submitted_at).toLocaleDateString('id-ID') }}</dd></div>
                        </dl>
                    </section>
                    <section v-if="submission.revisions.length" class="rounded-3xl border border-amber-500/30 bg-amber-500/10 p-7">
                        <h2 class="text-xl font-bold text-amber-300">Permintaan revisi</h2>
                        <article v-for="revision in submission.revisions" :key="revision.id" class="mt-4">
                            <p>{{ revision.message }}</p>
                            <p class="mt-2 text-sm text-amber-200">Field: {{ revision.fields.join(', ') }} · Batas {{ new Date(revision.deadline_at).toLocaleString('id-ID') }}</p>
                        </article>
                        <form v-if="submission.status === 'Perlu Perbaikan'" class="mt-6 space-y-3" @submit.prevent="revisionForm.post(revisionUrl)">
                            <input v-model="revisionForm.demo_url" type="url" placeholder="Tautan demo baru (opsional)">
                            <input v-model="revisionForm.video_url" type="url" placeholder="Tautan video baru (opsional)">
                            <FileDropzone
                                accept="image/jpeg,image/png,application/pdf,audio/*,video/mp4,video/quicktime"
                                label="Upload file revisi"
                                hint="Tarik file ke sini atau klik · Dokumen, audio, atau video · Maks. 10 MB"
                                :selected-name="revisionFile.name"
                                :selected-size="revisionFile.size"
                                :disabled="revisionForm.processing"
                                @select="selectRevision"
                                @remove="removeRevision"
                            />
                            <textarea v-model="revisionForm.note" placeholder="Jelaskan revisi yang kamu kirim" required></textarea>
                            <p v-for="error in revisionForm.errors" :key="error" class="text-xs text-red-300">{{ error }}</p>
                            <button class="rounded-xl bg-amber-400 px-4 py-3 font-bold text-black" :disabled="revisionForm.processing">Kirim revisi</button>
                        </form>
                    </section>
                </aside>
            </div>
        </main>
    </div>
</template>

<style scoped>
dt{color:#737373}dd{margin-top:.2rem;font-weight:600}input,textarea{width:100%;border:1px solid #78350f;background:#181818;border-radius:.75rem;padding:.75rem;color:white}
</style>
