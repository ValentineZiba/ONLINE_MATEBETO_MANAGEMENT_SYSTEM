<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Free OpenStreetMap geocoder. Nominatim's usage policy requires a proper
 * identifying User-Agent and forbids hammering the endpoint — results are
 * cached (successes for 30 days, failures for 1 hour so a bad address
 * doesn't get retried on every keystroke but can recover once OSM data
 * improves) and each lookup is capped at a short timeout so a slow
 * response never blocks checkout.
 */
class NominatimGeocoder
{
    public function geocode(string $address): ?array
    {
        $address = trim($address);
        if ($address === '') {
            return null;
        }

        $cacheKey = 'geocode:'.md5(strtolower($address));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $result = $this->lookup($address);
        Cache::put($cacheKey, $result, $result ? now()->addDays(30) : now()->addHour());

        return $result;
    }

    private function lookup(string $address): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'MatebetoRestaurant/1.0 ('.config('mail.from.address', 'contact@matebeto.com').')',
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address.', Zambia',
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'zm',
            ]);

            if (! $response->successful()) {
                return null;
            }

            $results = $response->json();
            if (empty($results)) {
                return null;
            }

            return [
                'lat' => (float) $results[0]['lat'],
                'lng' => (float) $results[0]['lon'],
            ];
        } catch (\Throwable $e) {
            Log::warning('Nominatim geocoding failed', ['address' => $address, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
