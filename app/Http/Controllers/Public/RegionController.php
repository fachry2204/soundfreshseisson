<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
    public function provinces(): JsonResponse
    {
        return $this->fetch('provinces', 'provinces', ['limit' => 100], 'provinces');
    }

    public function cities(string $province): JsonResponse
    {
        return $this->fetch("cities.{$province}", 'cities', ['provinceId' => $province, 'limit' => 100], 'cities');
    }

    public function districts(string $city): JsonResponse
    {
        return $this->fetch("districts.{$city}", 'districts', ['cityId' => $city, 'limit' => 100], 'districts');
    }

    public function villages(string $district): JsonResponse
    {
        return $this->fetch("villages.{$district}", 'villages', ['districtId' => $district, 'limit' => 100], 'villages');
    }

    public function postalCodes(string $village): JsonResponse
    {
        return $this->fetch("postal-codes.{$village}", 'postal-codes', ['villageId' => $village, 'limit' => 100], 'postalCodes', 'code');
    }

    private function fetch(string $cacheKey, string $endpoint, array $query, string $collection, string $nameField = 'name'): JsonResponse
    {
        $key = "regions.{$cacheKey}";

        try {
            $cached = $this->cacheGet($key);
            if (is_array($cached) && $cached !== []) {
                return response()->json(['data' => $cached]);
            }

            $request = Http::acceptJson()
                ->withUserAgent('OriginalSessions/1.0 (+'.config('app.url').')')
                ->connectTimeout(10)
                ->timeout(20)
                ->retry(2, 500);

            if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
                $request = $request->withOptions([
                    'curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4],
                ]);
            }

            $response = $request
                ->get(rtrim(config('services.regions.url'), '/').'/'.$endpoint, $query)
                ->throw();

            $items = collect($response->json("data.{$collection}", []))
                ->filter(fn ($item) => is_array($item) && isset($item['id'], $item[$nameField]))
                ->map(fn (array $item) => ['id' => (string) $item['id'], 'name' => (string) $item[$nameField]])
                ->unique(fn (array $item) => $item['id'].'-'.$item['name'])
                ->values()
                ->all();

            if ($items === []) {
                throw new \RuntimeException("Respons API wilayah tidak berisi {$collection}.");
            }

            $this->cachePut($key, $items);

            return response()->json(['data' => $items]);
        } catch (\Throwable $exception) {
            report($exception);

            $stale = $this->cacheGet("{$key}.stale");
            if (is_array($stale) && $stale !== []) {
                return response()->json(['data' => $stale, 'stale' => true]);
            }

            return response()->json(['message' => 'Data wilayah sedang tidak tersedia. Silakan coba lagi.'], 503);
        }
    }

    private function cacheGet(string $key): mixed
    {
        try {
            return Cache::get($key);
        } catch (\Throwable $exception) {
            Log::warning('Cache wilayah tidak tersedia; request dilanjutkan tanpa cache.', [
                'key' => $key,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function cachePut(string $key, array $items): void
    {
        try {
            Cache::put($key, $items, now()->addDay());
            Cache::put("{$key}.stale", $items, now()->addDays(30));
        } catch (\Throwable $exception) {
            Log::warning('Data wilayah berhasil dimuat tetapi gagal disimpan ke cache.', [
                'key' => $key,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
