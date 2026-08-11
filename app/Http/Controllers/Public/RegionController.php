<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
        try {
            $items = Cache::remember("regions.{$cacheKey}", now()->addDay(), function () use ($endpoint, $query, $collection, $nameField) {
                $response = Http::acceptJson()
                    ->timeout(15)
                    ->retry(2, 250)
                    ->get(rtrim(config('services.regions.url'), '/').'/'.$endpoint, $query)
                    ->throw();

                return collect($response->json("data.{$collection}", []))
                    ->map(fn (array $item) => ['id' => (string) $item['id'], 'name' => (string) $item[$nameField]])
                    ->unique(fn (array $item) => $item['id'].'-'.$item['name'])
                    ->values()
                    ->all();
            });

            return response()->json(['data' => $items]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Data wilayah sedang tidak tersedia. Silakan coba lagi.'], 503);
        }
    }
}
