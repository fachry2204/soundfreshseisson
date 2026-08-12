<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegionApiTest extends TestCase
{
    public function test_provinces_are_returned_through_backend_proxy(): void
    {
        Cache::forget('regions.provinces');
        Cache::forget('regions.provinces.stale');
        Http::fake([
            '*/provinces*' => Http::response([
                'data' => ['provinces' => [
                    ['id' => '31', 'name' => 'DKI JAKARTA'],
                ]],
            ]),
        ]);

        $this->getJson('/api/regions/provinces')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'DKI JAKARTA');
    }

    public function test_stale_region_data_is_used_when_upstream_is_unavailable(): void
    {
        Cache::forget('regions.provinces');
        Cache::put('regions.provinces.stale', [['id' => '31', 'name' => 'DKI JAKARTA']], now()->addDay());
        Http::fake(fn () => Http::response([], 503));

        $this->getJson('/api/regions/provinces')
            ->assertOk()
            ->assertJsonPath('stale', true)
            ->assertJsonPath('data.0.name', 'DKI JAKARTA');
    }
}
