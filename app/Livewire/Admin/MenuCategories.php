<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Menu Categories'])]
class MenuCategories extends Component
{
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $description = '';
    public string $icon = '';
    public int $sortOrder = 0;
    public bool $isActive = true;

    public function openCreate(): void
    {
        $this->reset(['name','description','icon','sortOrder','isActive','editingId']);
        $this->isActive = true;
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name = $cat->name;
        $this->description = $cat->description ?? '';
        $this->icon = $cat->icon ?? '';
        $this->sortOrder = $cat->sort_order;
        $this->isActive = $cat->is_active;
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate(['name' => 'required|string|max:100']);

        $data = ['name' => $this->name, 'description' => $this->description ?: null, 'icon' => $this->icon ?: null, 'sort_order' => $this->sortOrder, 'is_active' => $this->isActive];

        if ($this->editing && $this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Category updated.');
        } else {
            Category::create($data);
            session()->flash('success', 'Category created.');
        }
        $this->showModal = false;
    }

    public function toggle(int $id): void
    {
        $cat = Category::findOrFail($id);
        $cat->update(['is_active' => !$cat->is_active]);
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function render()
    {
        return view('livewire.admin.menu-categories', [
            'categories' => Category::withCount('menuItems')->orderBy('sort_order')->get(),
        ]);
    }
}
