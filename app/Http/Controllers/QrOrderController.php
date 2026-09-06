<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\RedirectResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;

class QrOrderController extends Controller
{
    public function scan(string $token): RedirectResponse
    {
        $table = RestaurantTable::where('qr_token', $token)
            ->whereIn('status', ['available', 'occupied'])
            ->firstOrFail();

        session([
            'qr_table_id'     => $table->id,
            'qr_table_number' => $table->number,
        ]);

        return redirect()->route('menu');
    }

    public function show(RestaurantTable $table): Response
    {
        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->color(28, 25, 23)      // stone-900
            ->backgroundColor(255, 251, 235) // amber-50
            ->errorCorrection('H')
            ->generate($table->qrUrl());

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
