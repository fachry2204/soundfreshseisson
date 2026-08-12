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
}
