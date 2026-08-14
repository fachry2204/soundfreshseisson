<?php

namespace Tests\Feature;

use App\Http\Requests\StoreDraftRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreDraftRequestVideoTest extends TestCase
{
    public function test_youtube_link_is_rejected(): void
    {
        $rules = (new StoreDraftRequest)->rules();
        $validator = Validator::make(['video_url' => 'https://www.youtube.com/watch?v=example'], ['video_url' => $rules['video_url']]);

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('YouTube', $validator->errors()->first('video_url'));
    }

    public function test_non_youtube_video_link_is_accepted(): void
    {
        $rules = (new StoreDraftRequest)->rules();
        $validator = Validator::make(['video_url' => 'https://drive.google.com/file/d/example/view'], ['video_url' => $rules['video_url']]);

        $this->assertFalse($validator->fails());
    }

    public function test_video_and_spotify_links_are_optional(): void
    {
        $rules = (new StoreDraftRequest)->rules();
        $validator = Validator::make([], [
            'video_url' => $rules['video_url'],
            'artist_spotify_url' => $rules['artist_spotify_url'],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_uploaded_video_is_required(): void
    {
        $rules = (new StoreDraftRequest)->rules();
        $validator = Validator::make([], [
            'upload_tokens' => $rules['upload_tokens'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('upload_tokens', $validator->errors()->toArray());
    }
}
