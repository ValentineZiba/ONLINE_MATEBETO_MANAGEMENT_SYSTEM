<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Services\Payments\PaymentManager;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'POS Terminal'])]
class Terminal extends Component
{
    public array $cart = [];
    public string $search = '';
    public string $selectedCategory = '';
    public ?int $tableId = null;
    public string $orderType = 'dine_in';
    public string $customerName = '';
    public string $paymentMethod = 'cash';
    public string $notes = '';
    public bool $orderComplete = false;
    public ?string $lastOrderNumber = null;
    public string $successMessage = '';
    public array $receiptItems = [];
    public float $receiptSubtotal = 0;
    public float $receiptTax = 0;
    public float $receiptTotal = 0;
    public string $receiptCustomer = '';
    public string $receiptPayment = '';

    public function addToCart(int $itemId): void
    {
        $item = MenuItem::findOrFail($itemId);
        $key = (string) $itemId;
        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty']++;
        } else {
            $this->cart[$key] = ['id' => $item->id, 'name' => $item->name, 'price' => $item->effective_price, 'qty' => 1];
        }
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
    }

    public function incrementItem(string $key): void
    {
        if (isset($this->cart[$key])) $this->cart[$key]['qty']++;
    }

    public function decrementItem(string $key): void
    {
        if (!isset($this->cart[$key])) return;
        if ($this->cart[$key]['qty'] <= 1) {
            unset($this->cart[$key]);
        } else {
            $this->cart[$key]['qty']--;
        }
    }

    public function removeFromCart(int $itemId): void
    {
        unset($this->cart[(string) $itemId]);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn ($i) => $i['price'] * $i['qty']);
    }

    public function getTaxProperty(): float
    {
        return $this->subtotal * 0.16;
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal + $this->tax;
    }

    public function placeOrder(): void
    {
        if (empty($this->cart)) return;

        $order = Order::create([
            'user_id' => auth()->id(),
            'table_id' => $this->tableId ?: null,
            'order_type' => $this->orderType,
            'customer_name' => $this->customerName ?: 'Walk-in Customer',
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'status' => 'confirmed',
            'accepted_at' => now(),
        ]);

        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);
        }

        if ($this->tableId) {
            RestaurantTable::find($this->tableId)?->update(['status' => 'occupied']);
        }

        app(PaymentManager::class)->initiate(
            transactionable: $order,
            gatewayKey: $this->paymentMethod,
            amount: $this->total,
            initiatedBy: auth()->user(),
        );

        $this->receiptItems    = $this->cart;
        $this->receiptSubtotal = $this->subtotal;
        $this->receiptTax      = $this->tax;
        $this->receiptTotal    = $this->total;
        $this->receiptCustomer = $this->customerName ?: 'Walk-in Customer';
        $this->receiptPayment  = $this->paymentMethod;
        $this->lastOrderNumber = $order->order_number;
        $this->cart = [];
        $this->customerName = '';
        $this->notes = '';
        $this->orderComplete = true;
        $this->successMessage = "Order #{$order->order_number} placed!";
    }

    public function render()
    {
        $query = MenuItem::available()->with('category');
        if ($this->search) $query->where('name', 'like', "%{$this->search}%");
        if ($this->selectedCategory) $query->where('category_id', (int) $this->selectedCategory);

        $subtotal = collect($this->cart)->sum(fn ($i) => $i['price'] * $i['qty']);
        $tax      = $subtotal * 0.16;
        $total    = $subtotal + $tax;

        return view('livewire.pos.terminal', [
            'menuItems'  => $query->orderBy('name')->get(),
            'categories' => Category::active()->orderBy('sort_order')->get(),
            'tables'     => RestaurantTable::where('status', 'available')->get(),
            'subtotal'   => $subtotal,
            'tax'        => $tax,
            'total'      => $total,
        ]);
    }
}
