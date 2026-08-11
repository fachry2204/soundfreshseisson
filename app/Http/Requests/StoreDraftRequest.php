<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:150', 'nik' => 'required|digits:16', 'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date|before:today', 'email' => 'required|email:rfc,dns|max:190', 'whatsapp' => ['required', 'regex:/^(?:\+?62|0)8[1-9][0-9]{6,11}$/'],
            'province' => 'required|string|max:100', 'city' => 'required|string|max:100', 'address' => 'required|string|max:1000',
            'title' => 'required|string|min:2|max:200', 'genre' => 'required|string|max:80', 'language' => 'required|string|max:80',
            'creation_year' => 'required|integer|min:1900|max:'.date('Y'), 'story' => 'required|string|min:50|max:5000', 'lyrics' => 'nullable|string|max:20000',
            'demo_url' => 'nullable|url:https|max:2000|required_without:upload_tokens', 'video_url' => 'nullable|url:https|max:2000',
            'upload_tokens' => 'nullable|array|max:2', 'upload_tokens.*.id' => 'required|ulid', 'upload_tokens.*.token' => 'required|string|size:64', 'upload_tokens.*.type' => 'required|in:demo,video',
            'original_work' => 'accepted', 'rights_approved' => 'accepted', 'data_correct' => 'accepted', 'terms' => 'accepted', 'communication' => 'accepted',
            'signature_name' => 'required|string|max:150', 'idempotency_key' => 'required|uuid',
            'ktp' => 'required|file|max:10240|mimetypes:image/jpeg,image/png,application/pdf',
        ];
    }
}
