<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RegionController extends Controller
{
    public function provinces(): JsonResponse
    {
        return $this->fetch('provinces', 'provinsi/get/');
    }

    public function cities(string $province): JsonResponse
    {
        return $this->fetch("cities.{$province}", 'kabkota/get/', ['d_provinsi_id' => $province]);
    }

    public function districts(string $city): JsonResponse
    {
        return $this->fetch("districts.{$city}", 'kecamatan/get/', ['d_kabkota_id' => $city]);
    }

    public function villages(string $district): JsonResponse
    {
        return $this->fetch("villages.{$district}", 'kelurahan/get/', ['d_kecamatan_id' => $district]);
    }

    public function postalCodes(string $city, string $district): JsonResponse
    {
        return $this->fetch("postal-codes.{$city}.{$district}", 'kodepos/get/', [
            'd_kabkota_id' => $city,
            'd_kecamatan_id' => $district,
        ]);
    }

    private function fetch(string $cacheKey, string $endpoint, array $query = []): JsonResponse
    {
        try {
            $items = Cache::remember("regions.{$cacheKey}", now()->addDay(), function () use ($endpoint, $query) {
                $response = Http::acceptJson()
                    ->timeout(10)
                    ->retry(2, 250)
                    ->get(rtrim(config('services.regions.url'), '/').'/'.$endpoint, $query)
                    ->throw();

                return collect($response->json('result', []))
                    ->map(fn (array $item) => ['id' => (string) $item['id'], 'name' => (string) $item['text']])
                    ->values()
                    ->all();
            });

            return response()->json(['data' => $items]);
        } catch (ConnectionException $exception) {
            report($exception);

            return response()->json(['message' => 'Data wilayah sedang tidak tersedia. Silakan coba lagi.'], 503);
        }
    }
}
