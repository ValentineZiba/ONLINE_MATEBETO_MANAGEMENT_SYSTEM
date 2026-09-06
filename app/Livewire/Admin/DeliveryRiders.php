<?php

namespace App\Livewire\Admin;

use App\Models\DeliveryRider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Delivery Riders'])]
class DeliveryRiders extends Component
{
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $phone = '';
    public string $vehicleType = 'car';
    public string $plateNumber = '';
    public bool $isActive = true;

    // Base64 data URLs captured by the <x-camera-capture> component
    public ?string $riderPhotoData = null;
    public ?string $nrcFrontData = null;
    public ?string $nrcBackData = null;

    // Whether an existing stored photo is present (edit mode, not retaken)
    public bool $hasExistingPhoto = false;
    public bool $hasExistingNrcFront = false;
    public bool $hasExistingNrcBack = false;

    public function openCreate(): void
    {
        $this->reset([
            'name', 'phone', 'plateNumber', 'editingId',
            'riderPhotoData', 'nrcFrontData', 'nrcBackData',
            'hasExistingPhoto', 'hasExistingNrcFront', 'hasExistingNrcBack',
        ]);
        $this->vehicleType = 'car';
        $this->isActive = true;
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $rider = DeliveryRider::findOrFail($id);
        $this->editingId = $id;
        $this->name = $rider->name;
        $this->phone = $rider->phone;
        $this->vehicleType = $rider->vehicle_type;
        $this->plateNumber = $rider->plate_number ?? '';
        $this->isActive = $rider->is_active;
        $this->riderPhotoData = null;
        $this->nrcFrontData = null;
        $this->nrcBackData = null;
        $this->hasExistingPhoto = (bool) $rider->rider_photo_path;
        $this->hasExistingNrcFront = (bool) $rider->nrc_front_path;
        $this->hasExistingNrcBack = (bool) $rider->nrc_back_path;
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $needsPhoto = ! $this->hasExistingPhoto;
        $needsNrc = $this->vehicleType === 'bicycle' && ! $this->hasExistingNrcFront;
        $needsNrcBack = $this->vehicleType === 'bicycle' && ! $this->hasExistingNrcBack;

        $this->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'vehicleType' => 'required|in:car,motorbike,bicycle',
            'plateNumber' => $this->vehicleType === 'bicycle' ? 'nullable' : 'required|string|max:20',
            'riderPhotoData' => $needsPhoto ? 'required|starts_with:data:image' : 'nullable|starts_with:data:image',
            'nrcFrontData' => $needsNrc ? 'required|starts_with:data:image' : 'nullable|starts_with:data:image',
            'nrcBackData' => $needsNrcBack ? 'required|starts_with:data:image' : 'nullable|starts_with:data:image',
        ]);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'vehicle_type' => $this->vehicleType,
            'plate_number' => $this->vehicleType === 'bicycle' ? null : $this->plateNumber,
            'is_active' => $this->isActive,
        ];

        if ($this->riderPhotoData) {
            $data['rider_photo_path'] = $this->storeCapturedImage($this->riderPhotoData, 'delivery-riders/photos');
        }

        if ($this->vehicleType === 'bicycle') {
            if ($this->nrcFrontData) {
                $data['nrc_front_path'] = $this->storeCapturedImage($this->nrcFrontData, 'delivery-riders/nrc');
            }
            if ($this->nrcBackData) {
                $data['nrc_back_path'] = $this->storeCapturedImage($this->nrcBackData, 'delivery-riders/nrc');
            }
        } else {
            // Switching away from bicycle: NRC images are no longer relevant for this rider.
            $data['nrc_front_path'] = null;
            $data['nrc_back_path'] = null;
        }

        if ($this->editing && $this->editingId) {
            DeliveryRider::findOrFail($this->editingId)->update($data);
        } else {
            DeliveryRider::create($data);
        }

        $this->showModal = false;
        session()->flash('success', 'Delivery rider saved.');
    }

    public function toggle(int $id): void
    {
        $rider = DeliveryRider::findOrFail($id);
        $rider->update(['is_active' => ! $rider->is_active]);
    }

    public function delete(int $id): void
    {
        $rider = DeliveryRider::findOrFail($id);

        if ($rider->orders()->exists()) {
            session()->flash('error', 'Cannot delete a rider who has delivery history — deactivate instead.');
            return;
        }

        foreach ([$rider->rider_photo_path, $rider->nrc_front_path, $rider->nrc_back_path] as $path) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
        }

        $rider->delete();
    }

    private function storeCapturedImage(string $dataUrl, string $directory): ?string
    {
        if (! str_starts_with($dataUrl, 'data:image')) {
            return null;
        }

        [, $encoded] = explode(',', $dataUrl, 2);
        $binary = base64_decode($encoded);
        $path = $directory.'/'.Str::uuid().'.jpg';
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    public function render()
    {
        return view('livewire.admin.delivery-riders', [
            'riders' => DeliveryRider::withCount('orders')->latest()->get(),
        ]);
    }
}
