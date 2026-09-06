<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DeliveryRider;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeliveryRiderDocumentController extends Controller
{
    /**
     * Streams rider identity documents from the private disk. These are
     * never placed on the public disk / a public URL — access is gated by
     * the admin,manager role middleware on the route itself.
     */
    public function show(DeliveryRider $rider, string $type): StreamedResponse
    {
        $path = match ($type) {
            'photo' => $rider->rider_photo_path,
            'nrc_front' => $rider->nrc_front_path,
            'nrc_back' => $rider->nrc_back_path,
            default => abort(404),
        };

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        ActivityLog::record(
            'rider_document.viewed',
            "Viewed {$type} document for rider {$rider->name}",
            $rider,
            ['type' => $type]
        );

        return Storage::disk('local')->response($path);
    }
}
