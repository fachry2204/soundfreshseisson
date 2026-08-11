<script setup lang="ts">
import { computed, ref } from 'vue';

const props = withDefaults(defineProps<{
    accept: string;
    label: string;
    hint: string;
    selectedName?: string;
    selectedSize?: number;
    status?: string;
    progress?: number;
    error?: string;
    disabled?: boolean;
}>(), { selectedName: '', selectedSize: 0, status: 'idle', progress: 0, error: '', disabled: false });

const emit = defineEmits<{ select: [file: File]; remove: [] }>();
const dragging = ref(false);
const input = ref<HTMLInputElement|null>(null);
const formattedSize = computed(() => props.selectedSize ? props.selectedSize < 1048576 ? `${(props.selectedSize / 1024).toFixed(0)} KB` : `${(props.selectedSize / 1048576).toFixed(1)} MB` : '');
const busy = computed(() => ['preparing', 'uploading'].includes(props.status));

function choose(files: FileList|null) {
    const file = files?.[0];
    if (file) emit('select', file);
}
function drop(event: DragEvent) {
    dragging.value = false;
    if (!props.disabled) choose(event.dataTransfer?.files ?? null);
}
function remove() {
    if (input.value) input.value.value = '';
    emit('remove');
}
</script>

<template>
    <div class="file-zone" :class="{ dragging, error: !!error, filled: !!selectedName }">
        <input ref="input" class="sr-only" type="file" :accept="accept" :disabled="disabled" @change="choose(($event.target as HTMLInputElement).files)">
        <button type="button" class="drop-target" :disabled="disabled" @click="input?.click()" @dragenter.prevent="dragging=true" @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false" @drop.prevent="drop">
            <span class="upload-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="copy">
                <strong>{{ selectedName || label }}</strong>
                <small v-if="selectedName">{{ formattedSize }} · Klik untuk mengganti</small>
                <small v-else>{{ hint }}</small>
            </span>
            <span v-if="selectedName && !busy" class="change-pill">Ganti</span>
        </button>
        <div v-if="busy || status==='completed'" class="progress-wrap" aria-live="polite">
            <div class="progress-meta"><span>{{ status==='completed' ? 'Upload selesai' : status==='preparing' ? 'Menyiapkan file…' : 'Mengunggah…' }}</span><b>{{ progress }}%</b></div>
            <div class="progress-track"><i :style="{width: progress+'%'}"></i></div>
        </div>
        <div v-if="selectedName" class="file-actions">
            <span :class="['status-dot', status]"></span><span>{{ status==='completed' ? 'Siap dikirim' : busy ? 'Jangan tutup halaman' : 'File terpilih' }}</span>
            <button type="button" :disabled="busy" @click="remove">Hapus</button>
        </div>
        <p v-if="error" class="file-error" role="alert">{{ error }}</p>
    </div>
</template>

<style scoped>
.file-zone{overflow:hidden;border:1px solid #30302e;border-radius:18px;background:linear-gradient(145deg,#181818,#131313);transition:.2s ease}.file-zone:hover,.file-zone.dragging{border-color:#ff6a00;box-shadow:0 0 0 4px #ff6a0014}.file-zone.error{border-color:#ef4444}.drop-target{display:flex;width:100%;align-items:center;gap:1rem;padding:1.15rem;text-align:left}.drop-target:disabled{cursor:not-allowed;opacity:.65}.upload-icon{display:grid;width:3.1rem;height:3.1rem;min-width:3.1rem;place-items:center;border:1px solid #3a3a38;border-radius:14px;background:#222;color:#ff8126}.upload-icon svg{width:1.45rem}.filled .upload-icon{border-color:#ff6a0055;background:#ff6a0012}.copy{display:grid;min-width:0;flex:1;gap:.3rem}.copy strong{overflow:hidden;color:#f7f7f5;text-overflow:ellipsis;white-space:nowrap}.copy small{color:#a9a9a5;font-weight:400}.change-pill{border:1px solid #444;border-radius:999px;padding:.45rem .75rem;color:#ddd;font-size:.72rem;font-weight:700}.progress-wrap{padding:0 1.15rem 1rem}.progress-meta{display:flex;justify-content:space-between;color:#a9a9a5;font-size:.72rem}.progress-meta b{color:#ff8126}.progress-track{height:.35rem;margin-top:.5rem;overflow:hidden;border-radius:99px;background:#30302e}.progress-track i{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#ff6a00,#ff9a45);transition:width .2s}.file-actions{display:flex;align-items:center;gap:.45rem;border-top:1px solid #292927;padding:.75rem 1.15rem;color:#a9a9a5;font-size:.72rem}.file-actions button{margin-left:auto;color:#fca5a5;font-weight:700}.file-actions button:disabled{opacity:.35}.status-dot{width:.45rem;height:.45rem;border-radius:50%;background:#737373}.status-dot.uploading,.status-dot.preparing{background:#f59e0b}.status-dot.completed{background:#22c55e}.status-dot.failed{background:#ef4444}.file-error{padding:.75rem 1.15rem;color:#fca5a5;font-size:.78rem}@media(max-width:480px){.change-pill{display:none}.drop-target{padding:1rem}.upload-icon{width:2.7rem;height:2.7rem;min-width:2.7rem}}
</style>
