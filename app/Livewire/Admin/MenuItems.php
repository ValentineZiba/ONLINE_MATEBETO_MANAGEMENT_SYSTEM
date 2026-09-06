<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin', ['title' => 'Menu Items'])]
class MenuItems extends Component
{
    use WithPagination, WithFileUploads;

    #[Url] public string $search = '';
    #[Url] public string $categoryFilter = '';

    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    public string $name = '';
    public int $categoryId = 0;
    public string $description = '';
    public float $price = 0;
    public ?float $discountPrice = null;
    public int $preparationTime = 15;
    public ?int $calories = null;
    public bool $isAvailable = true;
    public bool $isFeatured = false;
    public bool $isVegetarian = false;
    public bool $isVegan = false;
    public bool $isGlutenFree = false;
    public bool $isSpicy = false;
    public $imageFile = null;
    public ?string $existingImage = null;
    public ?int $stockQuantity = null;
    public string $station = 'kitchen';

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['name','categoryId','description','price','discountPrice','preparationTime','calories','isAvailable','isFeatured','isVegetarian','isVegan','isGlutenFree','isSpicy','imageFile','existingImage','stockQuantity','station']);
        $this->station = 'kitchen';
        $this->isAvailable = true;
        $this->editing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $this->editingId = $id;
        $this->name = $item->name;
        $this->categoryId = $item->category_id;
        $this->description = $item->description ?? '';
        $this->price = $item->price;
        $this->discountPrice = $item->discount_price;
        $this->preparationTime = $item->preparation_time;
        $this->calories = $item->calories;
        $this->isAvailable = $item->is_available;
        $this->isFeatured = $item->is_featured;
        $this->isVegetarian = $item->is_vegetarian;
        $this->isVegan = $item->is_vegan;
        $this->isGlutenFree = $item->is_gluten_free;
        $this->isSpicy = $item->is_spicy;
        $this->existingImage = $item->image;
        $this->imageFile = null;
        $this->stockQuantity = $item->stock_quantity;
        $this->station = $item->station ?? 'kitchen';
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'categoryId' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'preparationTime' => 'required|integer|min:1',
            'imageFile' => 'nullable|image|max:2048',
            'stockQuantity' => 'nullable|integer|min:0',
        ]);

        $data = [
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'price' => $this->price,
            'discount_price' => $this->discountPrice ?: null,
            'preparation_time' => $this->preparationTime,
            'calories' => $this->calories ?: null,
            'is_available' => $this->isAvailable,
            'is_featured' => $this->isFeatured,
            'is_vegetarian' => $this->isVegetarian,
            'is_vegan' => $this->isVegan,
            'is_gluten_free' => $this->isGlutenFree,
            'is_spicy' => $this->isSpicy,
            'stock_quantity' => $this->stockQuantity !== '' ? $this->stockQuantity : null,
            'station' => $this->station,
        ];

        if ($this->imageFile) {
            $data['image'] = $this->imageFile->store('menu-items', 'public');
        }

        if ($this->editing && $this->editingId) {
            MenuItem::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Menu item updated successfully.');
        } else {
            MenuItem::create($data);
            session()->flash('success', 'Menu item created successfully.');
        }

        $this->showModal = false;
    }

    public function toggleAvailable(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $item->update(['is_available' => !$item->is_available]);
    }

    public function toggleFeatured(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $item->update(['is_featured' => !$item->is_featured]);
    }

    public function delete(int $id): void
    {
        MenuItem::findOrFail($id)->delete();
        session()->flash('success', 'Menu item deleted.');
    }

    public function render()
    {
        $items = MenuItem::with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->orderBy('category_id')->orderBy('sort_order')
            ->paginate(20);

        return view('livewire.admin.menu-items', [
            'items' => $items,
            'categories' => Category::active()->orderBy('sort_order')->get(),
        ]);
    }
}
