<?php

namespace App\Services\Delivery;

use App\Models\Setting;
use App\Services\Geocoding\NominatimGeocoder;

/**
 * Straight-line (haversine) distance from the restaurant to a geocoded
 * delivery address, priced as base fee + rate per km. Falls back to the
 * flat "delivery_fee" setting when the address can't be located, rather
 * than blocking checkout — Nominatim's Zambia coverage isn't guaranteed
 * to resolve every address.
 */
class DeliveryFeeCalculator
{
    public function __construct(private readonly NominatimGeocoder $geocoder) {}

    public function calculateForAddress(string $address): array
    {
        $coords = $this->geocoder->geocode($address);

        if (! $coords) {
            return [
                'fee' => (float) Setting::get('delivery_fee', 150),
                'distance_km' => null,
                'estimated' => false,
            ];
        }

        $distanceKm = $this->haversineKm(
            (float) Setting::get('restaurant_latitude', -15.3875),
            (float) Setting::get('restaurant_longitude', 28.3228),
            $coords['lat'],
            $coords['lng'],
        );

        $baseFee = (float) Setting::get('delivery_base_fee', 30);
        $ratePerKm = (float) Setting::get('delivery_rate_per_km', 15);

        return [
            'fee' => round($baseFee + ($ratePerKm * $distanceKm), 2),
            'distance_km' => round($distanceKm, 1),
            'estimated' => true,
        ];
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
