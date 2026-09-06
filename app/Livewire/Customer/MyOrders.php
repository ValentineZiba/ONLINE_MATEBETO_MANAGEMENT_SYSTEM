<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public', ['title' => 'My Orders'])]
class MyOrders extends Component
{
    use WithPagination;

    public ?Order $selectedOrder = null;
    public bool $showModal = false;

    public function viewOrder(int $id): void
    {
        $this->selectedOrder = Order::where('user_id', auth()->id())
            ->with('items.menuItem')
            ->findOrFail($id);
        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.customer.my-orders', [
            'orders' => Order::where('user_id', auth()->id())
                ->with('items')
                ->latest()
                ->paginate(10),
        ]);
    }
}
