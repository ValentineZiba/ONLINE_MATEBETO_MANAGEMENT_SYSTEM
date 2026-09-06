<?php

namespace App\Livewire\Admin;

use App\Models\InventoryItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin', ['title' => 'Inventory'])]
class InventoryManagement extends Component
{
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $categoryFilter = '';

    public bool $showModal = false;
    public ?int $editId = null;

    public string $name = '';
    public string $unit = 'pcs';
    public string $quantity = '0';
    public string $lowStockThreshold = '10';
    public string $costPerUnit = '0';
    public string $supplier = '';
    public string $category = '';
    public bool $isActive = true;

    // Restock modal
    public bool $showRestockModal = false;
    public ?int $restockId = null;
    public string $restockAmount = '';

    public function openCreate(): void
    {
        $this->reset(['name','unit','quantity','lowStockThreshold','costPerUnit','supplier','category','isActive','editId']);
        $this->unit = 'pcs';
        $this->isActive = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $item = InventoryItem::findOrFail($id);
        $this->editId          = $id;
        $this->name            = $item->name;
        $this->unit            = $item->unit;
        $this->quantity        = (string) $item->quantity;
        $this->lowStockThreshold = (string) $item->low_stock_threshold;
        $this->costPerUnit     = (string) $item->cost_per_unit;
        $this->supplier        = $item->supplier ?? '';
        $this->category        = $item->category ?? '';
        $this->isActive        = $item->is_active;
        $this->showModal       = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'             => 'required|string|min:2',
            'unit'             => 'required|string',
            'quantity'         => 'required|numeric|min:0',
            'lowStockThreshold'=> 'required|numeric|min:0',
            'costPerUnit'      => 'required|numeric|min:0',
        ]);

        $data = [
            'name'               => $this->name,
            'unit'               => $this->unit,
            'quantity'           => $this->quantity,
            'low_stock_threshold'=> $this->lowStockThreshold,
            'cost_per_unit'      => $this->costPerUnit,
            'supplier'           => $this->supplier ?: null,
            'category'           => $this->category ?: null,
            'is_active'          => $this->isActive,
        ];

        if ($this->editId) {
            InventoryItem::findOrFail($this->editId)->update($data);
        } else {
            InventoryItem::create($data);
        }

        $this->showModal = false;
        session()->flash('success', 'Item saved.');
    }

    public function openRestock(int $id): void
    {
        $this->restockId     = $id;
        $this->restockAmount = '';
        $this->showRestockModal = true;
    }

    public function restock(): void
    {
        $this->validate(['restockAmount' => 'required|numeric|min:0.001']);
        $item = InventoryItem::findOrFail($this->restockId);
        $item->increment('quantity', (float) $this->restockAmount);
        $item->update(['last_restocked_at' => now()]);
        $this->showRestockModal = false;
        session()->flash('success', 'Stock updated.');
    }

    public function delete(int $id): void
    {
        InventoryItem::findOrFail($id)->delete();
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $query = InventoryItem::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('supplier', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))
            ->orderBy('name');

        return view('livewire.admin.inventory-management', [
            'items'      => $query->paginate(20),
            'categories' => InventoryItem::select('category')->whereNotNull('category')->distinct()->pluck('category'),
            'lowCount'   => InventoryItem::lowStock()->count(),
        ]);
    }
}
