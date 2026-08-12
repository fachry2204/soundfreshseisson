<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\UploadSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChunkUploadController extends Controller
{
    private const ALLOWED_MIMES = [
        'demo' => ['audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/mp4', 'audio/x-m4a'],
        'video' => ['video/mp4', 'video/quicktime', 'video/webm'],
    ];

    public function init(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|in:demo,video', 'name' => 'required|string|max:200',
            'mime' => 'required|string|max:100', 'size' => 'required|integer|min:1|max:524288000',
            'chunk_size' => 'required|integer|min:262144|max:10485760',
            'checksum' => ['required', 'regex:/^[a-f0-9]{64}$/'],
        ]);
        $invalidMimeMessage = $data['type'] === 'video'
            ? 'File yang diupload harus berformat video MP4, MOV, atau WebM.'
            : 'Format file audio tidak didukung.';
        abort_unless(in_array($data['mime'], self::ALLOWED_MIMES[$data['type']], true), 422, $invalidMimeMessage);
        $token = Str::random(64);
        $total = (int) ceil($data['size'] / $data['chunk_size']);
        abort_if($total > 1000, 422, 'Jumlah chunk terlalu banyak.');
        $session = UploadSession::create([
            'token_hash' => hash('sha256', $token), 'type' => $data['type'], 'original_name' => Str::limit(basename($data['name']), 200),
            'declared_mime' => $data['mime'], 'expected_size' => $data['size'], 'chunk_size' => $data['chunk_size'],
            'total_chunks' => $total, 'expected_checksum' => $data['checksum'], 'received_chunks' => [], 'expires_at' => now()->addHours(24),
        ]);

        return response()->json(['id' => $session->id, 'token' => $token, 'total_chunks' => $total, 'expires_at' => $session->expires_at], 201);
    }

    public function chunk(Request $request, UploadSession $upload): JsonResponse
    {
        $this->authorizeToken($request, $upload);
        abort_unless(in_array($upload->status, ['initialized', 'uploading'], true), 409, 'Sesi upload tidak aktif.');
        $data = $request->validate(['index' => 'required|integer|min:0|max:999']);
        abort_if($data['index'] >= $upload->total_chunks, 422, 'Index chunk tidak valid.');
        $expected = $data['index'] === $upload->total_chunks - 1 ? $upload->expected_size - ($data['index'] * $upload->chunk_size) : $upload->chunk_size;
        $binary = $request->hasFile('chunk')
            ? file_get_contents($request->file('chunk')->getRealPath())
            : $request->getContent();
        $actualSize = strlen((string) $binary);
        abort_unless($actualSize === $expected, 422, 'Potongan video tidak diterima secara utuh oleh server. Silakan coba upload kembali.');
        $path = "uploads/chunks/{$upload->id}/{$data['index']}.part";
        $stored = Storage::disk('local')->put($path, $binary);
        abort_unless($stored && Storage::disk('local')->size($path) === $expected, 500, 'Potongan video gagal disimpan utuh oleh server.');
        $received = collect($upload->received_chunks ?? [])->push((int) $data['index'])->unique()->sort()->values()->all();
        $upload->update(['received_chunks' => $received, 'status' => 'uploading']);

        return response()->json(['received' => $data['index'], 'progress' => count($received) / $upload->total_chunks]);
    }

    public function complete(Request $request, UploadSession $upload): JsonResponse
    {
        $this->authorizeToken($request, $upload);
        abort_unless(in_array($upload->status, ['initialized', 'uploading'], true), 409, 'Sesi upload tidak aktif.');
        abort_unless(count($upload->received_chunks ?? []) === $upload->total_chunks, 422, 'Chunk belum lengkap.');
        $completedPath = "uploads/completed/{$upload->id}";
        Storage::disk('local')->makeDirectory('uploads/completed');
        $output = fopen(Storage::disk('local')->path($completedPath), 'wb');
        for ($index = 0; $index < $upload->total_chunks; $index++) {
            $part = Storage::disk('local')->path("uploads/chunks/{$upload->id}/{$index}.part");
            abort_unless(is_file($part), 422, 'Chunk hilang.');
            $input = fopen($part, 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
        }
        fclose($output);
        $path = Storage::disk('local')->path($completedPath);
        $checksum = hash_file('sha256', $path);
        $mime = mime_content_type($path);
        if (filesize($path) !== $upload->expected_size || ! hash_equals($upload->expected_checksum, $checksum) || ! in_array($mime, self::ALLOWED_MIMES[$upload->type] ?? [], true)) {
            Storage::disk('local')->delete($completedPath);
            $upload->update(['status' => 'failed']);
            abort(422, 'Validasi akhir file gagal.');
        }
        Storage::disk('local')->deleteDirectory("uploads/chunks/{$upload->id}");
        $upload->update(['path' => $completedPath, 'actual_checksum' => $checksum, 'detected_mime' => $mime, 'status' => 'completed']);

        return response()->json(['id' => $upload->id, 'status' => 'completed', 'checksum' => $checksum]);
    }

    public function cancel(Request $request, UploadSession $upload): JsonResponse
    {
        $this->authorizeToken($request, $upload);
        Storage::disk('local')->deleteDirectory("uploads/chunks/{$upload->id}");
        if ($upload->path) {
            Storage::disk('local')->delete($upload->path);
        }
        $upload->update(['status' => 'cancelled']);

        return response()->json([], 204);
    }

    private function authorizeToken(Request $request, UploadSession $upload): void
    {
        $token = (string) $request->header('X-Upload-Token');
        abort_unless($token !== '' && hash_equals($upload->token_hash, hash('sha256', $token)), 403);
        abort_if($upload->expires_at->isPast(), 410, 'Sesi upload kedaluwarsa.');
    }
}
