<?php

namespace App\Livewire\Bar;

use App\Models\BarTab;
use App\Models\BarTabItem;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Services\Payments\PaymentManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;

#[Layout('components.layouts.bar', ['title' => 'Bar Tabs'])]
class TabManager extends Component
{
    // Open-tab modal
    public bool $showNewTab  = false;
    public string $tabName   = '';
    public ?int $tabTableId  = null;

    // Active tab being worked on
    public ?int $activeTabId = null;

    // Add-drink search within an active tab
    public string $drinkSearch = '';

    // Close-tab modal
    public bool $showCloseModal    = false;
    public ?int $closingTabId      = null;
    public string $closePayment    = 'cash';

    // ── Open a new tab ────────────────────────────────────────────────────
    public function openNewTabModal(): void
    {
        $this->reset(['tabName', 'tabTableId']);
        $this->showNewTab = true;
    }

    public function createTab(): void
    {
        $this->validate([
            'tabName'    => 'required|string|max:80',
            'tabTableId' => 'nullable|exists:restaurant_tables,id',
        ]);

        $tab = BarTab::create([
            'opened_by'     => auth()->id(),
            'customer_name' => $this->tabName,
            'table_id'      => $this->tabTableId ?: null,
            'status'        => 'open',
        ]);

        $this->showNewTab  = false;
        $this->activeTabId = $tab->id;
    }

    // ── Select an existing tab ────────────────────────────────────────────
    public function selectTab(int $id): void
    {
        $this->activeTabId  = $id;
        $this->drinkSearch  = '';
    }

    // ── Add a drink to the active tab ────────────────────────────────────
    public function addDrink(int $menuItemId): void
    {
        if (!$this->activeTabId) return;

        $item = MenuItem::findOrFail($menuItemId);
        $tab  = BarTab::findOrFail($this->activeTabId);

        $existing = $tab->items()->where('menu_item_id', $menuItemId)->first();
        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + 1,
                'subtotal' => ($existing->quantity + 1) * $existing->unit_price,
            ]);
        } else {
            $tab->items()->create([
                'menu_item_id' => $menuItemId,
                'quantity'     => 1,
                'unit_price'   => $item->effective_price,
                'subtotal'     => $item->effective_price,
            ]);
        }

        $tab->recalculate();
        $this->drinkSearch = '';
    }

    // ── Remove item from active tab ───────────────────────────────────────
    public function removeItem(int $itemId): void
    {
        $tabItem = BarTabItem::findOrFail($itemId);
        $tab     = $tabItem->tab;
        $tabItem->delete();
        $tab->recalculate();
    }

    // ── Decrement qty or remove ───────────────────────────────────────────
    public function decrementItem(int $itemId): void
    {
        $tabItem = BarTabItem::findOrFail($itemId);
        if ($tabItem->quantity <= 1) {
            $tab = $tabItem->tab;
            $tabItem->delete();
            $tab->recalculate();
        } else {
            $tabItem->update([
                'quantity' => $tabItem->quantity - 1,
                'subtotal' => ($tabItem->quantity - 1) * $tabItem->unit_price,
            ]);
            $tabItem->tab->recalculate();
        }
    }

    // ── Close tab modal ───────────────────────────────────────────────────
    public function openCloseModal(int $tabId): void
    {
        $this->closingTabId = $tabId;
        $this->closePayment = 'cash';
        $this->showCloseModal = true;
    }

    public function closeTab(): void
    {
        $tab = BarTab::with('items')->findOrFail($this->closingTabId);

        $tab->update(['payment_method' => $this->closePayment]);

        app(PaymentManager::class)->initiate(
            transactionable: $tab,
            gatewayKey: $this->closePayment,
            amount: (float) $tab->total,
            initiatedBy: auth()->user(),
        );

        $tab->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        if ($this->activeTabId === $this->closingTabId) {
            $this->activeTabId = null;
        }
        $this->showCloseModal = false;
    }

    // ── Delete (void) tab ─────────────────────────────────────────────────
    public function voidTab(int $tabId): void
    {
        $tab = BarTab::findOrFail($tabId);

        if ($tab->paymentTransactions()->where('status', 'succeeded')->exists()) {
            session()->flash('error', 'Cannot void a tab that has already been paid.');
            return;
        }

        if ($this->activeTabId === $tabId) $this->activeTabId = null;
        $tab->delete();
    }

    #[Computed]
    public function openTabs()
    {
        return BarTab::open()->with(['table', 'items.menuItem', 'opener'])->latest()->get();
    }

    #[Computed]
    public function activeTab(): ?BarTab
    {
        if (!$this->activeTabId) return null;
        return BarTab::with(['items.menuItem', 'table'])->find($this->activeTabId);
    }

    #[Computed]
    public function drinkMenu()
    {
        $q = MenuItem::whereIn('station', ['bar', 'both'])->where('is_available', true);
        if ($this->drinkSearch) {
            $q->where('name', 'like', "%{$this->drinkSearch}%");
        }
        return $q->orderBy('name')->get();
    }

    #[Computed]
    public function barTables()
    {
        return RestaurantTable::where('location', 'bar')->orderBy('number')->get();
    }

    public function render()
    {
        return view('livewire.bar.tab-manager');
    }
}
