<?php

namespace App\Http\Controllers\Public;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDraftRequest;
use App\Jobs\ScanSubmissionFile;
use App\Models\Applicant;
use App\Models\AppSetting;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\UploadSession;
use App\Services\Submission\SubmissionStateMachine;
use App\Services\Submission\RegistrationNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    public function create(): Response
    {
        if (filter_var(AppSetting::valueFor('registration.disabled', '0'), FILTER_VALIDATE_BOOLEAN)) {
            return Inertia::render('Public/RegistrationClosed');
        }

        return Inertia::render('Public/Register', [
            'videoUploadDisabled' => filter_var(AppSetting::valueFor('registration.video_upload_disabled', '0'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function store(StoreDraftRequest $request, SubmissionStateMachine $stateMachine, RegistrationNumberGenerator $numberGenerator): RedirectResponse
    {
        $data = $request->validated();

        // A repeated browser request must return the original success page,
        // instead of trying to claim an already-consumed upload for a second time.
        if ($existing = Submission::query()->where('idempotency_key', $data['idempotency_key'])->first()) {
            return $this->redirectToSuccess($existing);
        }

        // This relation is managed internally and no longer controls whether
        // registration can be submitted.
        $period = ProgramPeriod::query()->firstOrCreate(
            ['slug' => 'original-sessions'],
            [
                'name' => 'Original Sessions',
                'opens_at' => now()->startOfYear(),
                'closes_at' => now()->addYears(20)->endOfYear(),
                'timezone' => config('app.timezone', 'Asia/Jakarta'),
                'status' => 'open',
            ],
        );
        if (! $request->videoUploadDisabled()) {
            abort_unless(collect($data['upload_tokens'])->contains('type', 'video'), 422, 'Upload video penampilan wajib dilakukan.');
        }
        $submission = DB::transaction(function () use ($data, $period, $request, $stateMachine, $numberGenerator) {
            if ($existing = Submission::where('idempotency_key', $data['idempotency_key'])->first()) {
                return $existing;
            }
            // Lock the period before any submission work so concurrent requests
            // cannot reserve the same registration number.
            $registrationNumber = $numberGenerator->next($period);
            $phone = preg_replace('/^0/', '62', preg_replace('/\D/', '', $data['whatsapp']));
            $nikHash = hash_hmac('sha256', $data['nik'], config('app.key'));
            $applicant = Applicant::create([
                'full_name' => $data['full_name'], 'nik' => $data['nik'], 'nik_blind_index' => $nikHash, 'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'], 'email' => strtolower($data['email']), 'whatsapp' => $phone, 'province' => $data['province'],
                'city' => $data['city'], 'district' => $data['district'], 'village' => $data['village'],
                'postal_code' => $data['postal_code'], 'address' => $data['address'],
            ]);
            $submission = Submission::create([
                'program_period_id' => $period->id,
                'applicant_id' => $applicant->id,
                'registration_number' => $registrationNumber,
                'status' => SubmissionStatus::Draft,
                'draft_token_hash' => hash('sha256', Str::random(64)),
                'idempotency_key' => $data['idempotency_key'],
            ]);
            $ktp = $request->file('ktp');
            $detectedMime = $ktp->getMimeType();
            abort_unless(in_array($detectedMime, ['image/jpeg', 'image/png', 'application/pdf'], true), 422, 'Format KTP tidak diizinkan.');
            $path = $ktp->storeAs('submissions/'.$submission->id, Str::uuid().'.'.$ktp->guessExtension(), 'local');
            abort_unless($path, 500, 'File gagal disimpan.');
            $storedFile = $submission->files()->create([
                'type' => 'ktp', 'disk' => 'local', 'path' => $path,
                'original_name' => Str::limit(basename($ktp->getClientOriginalName()), 200),
                'mime' => $detectedMime, 'size' => $ktp->getSize(), 'checksum' => hash_file('sha256', Storage::disk('local')->path($path)),
                'scan_status' => 'pending',
            ]);
            ScanSubmissionFile::dispatch($storedFile->id)->afterCommit();
            $submission->song()->create(['title' => $data['title'], 'artist_name' => $data['artist_name'], 'artist_social_url' => $data['artist_social_url'], 'artist_spotify_url' => $data['artist_spotify_url'] ?? null, 'songwriters' => $data['songwriters'], 'has_cowriters' => count($data['songwriters']) > 1, 'genre' => $data['genre'], 'language' => $data['language'], 'creation_year' => $data['creation_year'], 'story' => $data['story'], 'lyrics' => $data['lyrics'] ?? null]);
            foreach (['demo_url' => 'demo', 'video_url' => 'video'] as $field => $type) {
                if (! empty($data[$field])) {
                    DB::table('submission_links')->insert(['submission_id' => $submission->id, 'type' => $type, 'url' => $data[$field], 'created_at' => now(), 'updated_at' => now()]);
                }
            }
            foreach ($data['upload_tokens'] ?? [] as $capability) {
                $upload = UploadSession::whereKey($capability['id'])->lockForUpdate()->first();
                throw_if(! $upload, ValidationException::withMessages([
                    'upload_tokens' => 'Data upload sudah kedaluwarsa atau tidak ditemukan. Hapus file terpilih, lalu upload kembali.',
                ]));
                throw_if(
                    $upload->status !== 'completed' ||
                    $upload->claimed_by_submission_id !== null ||
                    $upload->type !== $capability['type'] ||
                    ! hash_equals($upload->token_hash, hash('sha256', $capability['token'])),
                    ValidationException::withMessages([
                        'upload_tokens' => 'Upload tidak valid, belum selesai, atau sudah digunakan. Silakan upload kembali.',
                    ]),
                );
                $destination = "submissions/{$submission->id}/".Str::uuid().'.'.pathinfo($upload->original_name, PATHINFO_EXTENSION);
                abort_unless(Storage::disk('local')->move($upload->path, $destination), 500, 'Upload gagal dipindahkan.');
                $storedFile = $submission->files()->create(['type' => $upload->type, 'disk' => 'local', 'path' => $destination, 'original_name' => $upload->original_name, 'mime' => $upload->detected_mime, 'size' => $upload->expected_size, 'checksum' => $upload->actual_checksum, 'scan_status' => 'pending']);
                $upload->update(['claimed_by_submission_id' => $submission->id, 'status' => 'claimed']);
                ScanSubmissionFile::dispatch($storedFile->id)->afterCommit();
            }
            DB::table('consents')->insert(['submission_id' => $submission->id, 'type' => 'terms', 'document_version' => '2026-01', 'accepted_at' => now(), 'ip_hash' => hash_hmac('sha256', (string) $request->ip(), config('app.key')), 'user_agent' => Str::limit((string) $request->userAgent(), 500), 'created_at' => now(), 'updated_at' => now()]);
            $submission->update(['snapshot' => collect($data)->except(['nik', 'idempotency_key'])->all()]);

            return $stateMachine->transition($submission, SubmissionStatus::Submitted, null, 'Dikirim oleh pendaftar');
        });

        return $this->redirectToSuccess($submission);
    }

    private function redirectToSuccess(Submission $submission): RedirectResponse
    {
        $successUrl = URL::temporarySignedRoute(
            'registration.success',
            now()->addHours(24),
            ['submission' => $submission->id],
            absolute: false,
        );

        return redirect($successUrl)->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function success(Submission $submission): Response
    {
        abort_unless($submission->status !== SubmissionStatus::Draft, 404);

        return Inertia::render('Public/Success', ['registrationNumber' => $submission->registration_number]);
    }
}
