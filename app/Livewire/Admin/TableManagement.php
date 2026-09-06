<?php

namespace App\Livewire\Admin;

use App\Models\RestaurantTable;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Table Management'])]
class TableManagement extends Component
{
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;
    public string $number = '';
    public int $capacity = 4;
    public string $location = 'indoor';
    public string $status = 'available';
    public string $notes = '';
    public ?RestaurantTable $selectedTable = null;

    public bool $showQrModal = false;
    public ?int $qrTableId = null;
    public ?string $qrTableNumber = null;
    public ?string $qrUrl = null;

    public function openCreate(): void
    {
        $this->reset(['number','capacity','location','status','notes','editingId']);
        $this->capacity = 4;
        $this->location = 'indoor';
        $this->status = 'available';
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $table = RestaurantTable::findOrFail($id);
        $this->editingId = $id;
        $this->number = $table->number;
        $this->capacity = $table->capacity;
        $this->location = $table->location;
        $this->status = $table->status;
        $this->notes = $table->notes ?? '';
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'number' => 'required|string|max:10|unique:restaurant_tables,number' . ($this->editingId ? ",{$this->editingId}" : ''),
            'capacity' => 'required|integer|min:1|max:50',
            'location' => 'required|in:indoor,outdoor,bar,private',
            'status' => 'required',
        ]);

        $data = ['number' => $this->number, 'capacity' => $this->capacity, 'location' => $this->location, 'status' => $this->status, 'notes' => $this->notes ?: null];

        if ($this->editing && $this->editingId) {
            RestaurantTable::findOrFail($this->editingId)->update($data);
        } else {
            RestaurantTable::create($data);
        }
        $this->showModal = false;
    }

    public function setStatus(int $id, string $status): void
    {
        RestaurantTable::findOrFail($id)->update(['status' => $status]);
    }

    public function delete(int $id): void
    {
        RestaurantTable::findOrFail($id)->delete();
    }

    public function openQr(int $id): void
    {
        $table = RestaurantTable::findOrFail($id);
        $this->qrTableId = $table->id;
        $this->qrTableNumber = $table->number;
        $this->qrUrl = $table->qrUrl();
        $this->showQrModal = true;
    }

    public function regenerateQr(int $id): void
    {
        $table = RestaurantTable::findOrFail($id);
        $table->update(['qr_token' => \Illuminate\Support\Str::uuid()->toString()]);
        $this->qrUrl = $table->fresh()->qrUrl();
    }

    #[Polling('30s')]
    public function render()
    {
        $tables = RestaurantTable::withCount(['orders as active_order' => fn ($q) => $q->whereIn('status', ['confirmed','preparing','ready','served'])])
            ->orderBy('number')
            ->get();

        $stats = [
            'total' => $tables->count(),
            'available' => $tables->where('status', 'available')->count(),
            'occupied' => $tables->where('status', 'occupied')->count(),
            'reserved' => $tables->where('status', 'reserved')->count(),
        ];

        return view('livewire.admin.table-management', compact('tables', 'stats'));
    }
}
