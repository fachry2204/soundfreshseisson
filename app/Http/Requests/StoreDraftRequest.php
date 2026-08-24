<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;

class StoreDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $videoUploadDisabled = $this->videoUploadDisabled();
        $uploadTokensRule = $videoUploadDisabled
            ? 'nullable|array|max:1'
            : 'required|array|size:1';
        $videoUrlPresenceRule = $videoUploadDisabled ? 'required' : 'nullable';

        return [
            'full_name' => 'required|string|max:150', 'nik' => 'required|digits:16', 'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date|before:today', 'email' => 'required|email:rfc,dns|max:190', 'whatsapp' => ['required', 'regex:/^(?:\+?62|0)8[1-9][0-9]{6,11}$/'],
            'province' => 'required|string|max:100', 'city' => 'required|string|max:100', 'district' => 'required|string|max:100',
            'village' => 'required|string|max:100', 'postal_code' => 'required|digits:5', 'address' => 'required|string|max:1000',
            'title' => 'required|string|min:2|max:200', 'artist_name' => 'required|string|min:2|max:150',
            'artist_social_url' => 'required|url:http,https|max:2000', 'artist_spotify_url' => ['nullable', 'url:http,https', 'max:2000', function (string $attribute, mixed $value, \Closure $fail): void {
                if (filled($value) && ! str_contains(strtolower((string) parse_url((string) $value, PHP_URL_HOST)), 'spotify.com')) $fail('Link Spotify artis harus berasal dari spotify.com.');
            }],
            'songwriters' => 'required|array|min:1|max:20', 'songwriters.*.name' => 'required|string|min:2|max:150', 'songwriters.*.role' => ['required', Rule::in(['composer', 'author', 'composer_author'])],
            'genre' => ['required', 'string', Rule::in(['Alternative/Indie', 'Latin', 'Classical', 'Country', 'Blues', 'Electronic', 'Folk', 'Hip Hop/Rap', 'Jazz', 'New Age', 'Pop', 'R&B/Soul', 'Reggae', 'Rock', 'World', 'Childhood', 'Devotional/Inspirational', 'Dance', 'Soundtrack'])], 'language' => 'required|string|max:80',
            'creation_year' => 'required|integer|min:1900|max:'.date('Y'), 'story' => 'required|string|min:50|max:5000', 'lyrics' => 'nullable|string|max:20000',
            'video_url' => [$videoUrlPresenceRule, 'url:http,https', 'max:2000', function (string $attribute, mixed $value, \Closure $fail): void {
                $host = strtolower((string) parse_url((string) $value, PHP_URL_HOST));
                if ($host === 'youtu.be' || $host === 'youtube.com' || str_ends_with($host, '.youtube.com')) {
                    $fail('Link video tidak boleh berasal dari YouTube.');
                }
            }],
            'upload_tokens' => $uploadTokensRule, 'upload_tokens.*.id' => 'required|ulid', 'upload_tokens.*.token' => 'required|string|size:64', 'upload_tokens.*.type' => 'required|in:video',
            'terms' => 'accepted', 'idempotency_key' => 'required|uuid',
            'ktp' => 'required|file|max:10240|mimetypes:image/jpeg,image/png,application/pdf',
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Gunakan nomor Indonesia, misalnya 081234567890.',
            'video_url.url' => 'Link video harus berupa URL HTTP atau HTTPS yang valid.',
            'video_url.required' => 'Link video wajib diisi karena upload video sedang dinonaktifkan.',
            'artist_social_url.required' => 'Link sosial media artis wajib diisi.',
            'artist_social_url.url' => 'Link sosial media artis harus berupa URL HTTP atau HTTPS yang valid.',
            'upload_tokens.required' => 'Upload video penampilan wajib dilakukan.',
            'upload_tokens.size' => 'Upload tepat satu file video penampilan.',
        ];
    }

    public function videoUploadDisabled(): bool
    {
        return Schema::hasTable('app_settings')
            && filter_var(AppSetting::valueFor('registration.video_upload_disabled', '0'), FILTER_VALIDATE_BOOLEAN);
    }
}
