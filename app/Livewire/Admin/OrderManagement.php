<?php

namespace App\Livewire\Admin;

use App\Mail\OrderStatusMail;
use App\Models\ActivityLog;
use App\Models\DeliveryRider;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Services\Delivery\DeliveryFeeCalculator;
use App\Services\Payments\PaymentManager;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin', ['title' => 'Order Management'])]
class OrderManagement extends Component
{
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $statusFilter = '';
    #[Url] public string $typeFilter = '';

    public bool $showModal = false;
    public bool $showCreateModal = false;
    public bool $showDriverModal = false;
    public bool $showPaymentModal = false;
    public ?Order $selectedOrder = null;
    public ?int $driverOrderId = null;
    public ?int $selectedRiderId = null;
    public ?int $paymentOrderId = null;
    public string $recordPaymentGateway = 'cash';
    public string $recordPaymentNote = '';
    public bool $showRefundModal = false;
    public ?int $refundOrderId = null;
    public string $refundReason = '';

    // Create order fields
    public string $orderType = 'dine_in';
    public ?int $tableId = null;
    public string $customerName = '';
    public string $customerPhone = '';
    public string $deliveryAddress = '';
    public float $deliveryFee = 0;
    public ?float $deliveryDistanceKm = null;
    public string $deliveryFeeMessage = '';
    public string $paymentMethod = 'cash';
    public string $notes = '';
    public array $newItems = [];
    public ?int $addItemId = null;
    public int $addItemQty = 1;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function openDriverModal(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $this->driverOrderId = $orderId;
        $this->selectedRiderId = $order->delivery_rider_id;
        $this->showDriverModal = true;
    }

    public function assignDriver(): void
    {
        $this->validate(['selectedRiderId' => 'required|exists:delivery_riders,id']);

        $rider = DeliveryRider::findOrFail($this->selectedRiderId);

        Order::findOrFail($this->driverOrderId)->update([
            'delivery_rider_id' => $rider->id,
            'driver_name'       => $rider->name,
            'driver_phone'      => $rider->phone,
            'delivery_status'   => 'assigned',
        ]);
        $this->showDriverModal = false;
        session()->flash('success', 'Driver assigned.');
    }

    public function updateDeliveryStatus(int $orderId, string $status): void
    {
        $updates = ['delivery_status' => $status];
        if ($status === 'picked_up') $updates['picked_up_at'] = now();
        if ($status === 'delivered') $updates['delivered_at'] = now();
        Order::findOrFail($orderId)->update($updates);
    }

    public function viewOrder(int $orderId): void
    {
        $this->selectedOrder = Order::with(['items.menuItem', 'user', 'table'])->findOrFail($orderId);
        $this->showModal = true;
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $updates = ['status' => $status];

        if ($status === 'confirmed') $updates['accepted_at'] = now();
        if ($status === 'ready') $updates['ready_at'] = now();
        if ($status === 'completed') {
            $updates['completed_at'] = now();
            if ($order->table_id) {
                $order->table->update(['status' => 'available']);
            }
        }

        $order->update($updates);

        // Award loyalty points on completion (1 point per K10 spent)
        if ($status === 'completed' && $order->user_id) {
            $points = (int) floor($order->total / 10);
            if ($points > 0) {
                $order->update(['loyalty_points_earned' => $points]);
                $order->user->addLoyaltyPoints($points);
            }
        }

        // Send status notification email
        $email = $order->customer_email ?? $order->user?->email;
        if ($email && in_array($status, ['confirmed', 'preparing', 'ready', 'completed', 'cancelled'])) {
            Mail::to($email)->send(new OrderStatusMail($order));
        }

        if ($status === 'cancelled') {
            ActivityLog::record('order.cancelled', "Cancelled order #{$order->order_number}", $order, ['total' => (float) $order->total]);
        }

        if ($this->selectedOrder?->id === $orderId) {
            $this->selectedOrder = $order->fresh(['items.menuItem', 'user', 'table']);
        }

        session()->flash('success', "Order #{$order->order_number} updated to " . ucfirst($status));
    }

    public function openRecordPaymentModal(int $orderId): void
    {
        $this->paymentOrderId = $orderId;
        $this->recordPaymentGateway = 'cash';
        $this->recordPaymentNote = '';
        $this->showPaymentModal = true;
    }

    public function recordPayment(): void
    {
        $this->validate([
            'recordPaymentGateway' => 'required|in:cash,card,airtel_money,mtn_momo,zamtel_kwacha,zampay,bank_transfer',
        ]);

        $order = Order::findOrFail($this->paymentOrderId);

        app(PaymentManager::class)->initiate(
            transactionable: $order,
            gatewayKey: $this->recordPaymentGateway,
            amount: (float) $order->total,
            payload: ['note' => $this->recordPaymentNote],
            initiatedBy: auth()->user(),
        );

        ActivityLog::record(
            'payment.recorded',
            "Recorded {$this->recordPaymentGateway} payment for order #{$order->order_number}",
            $order,
            ['gateway' => $this->recordPaymentGateway, 'amount' => (float) $order->total, 'note' => $this->recordPaymentNote]
        );

        $this->showPaymentModal = false;

        if ($this->selectedOrder?->id === $order->id) {
            $this->selectedOrder = $order->fresh(['items.menuItem', 'user', 'table']);
        }

        session()->flash('success', "Payment recorded for order #{$order->order_number}.");
    }

    public function openRefundModal(int $orderId): void
    {
        $this->refundOrderId = $orderId;
        $this->refundReason = '';
        $this->showRefundModal = true;
    }

    public function refundOrder(): void
    {
        $order = Order::findOrFail($this->refundOrderId);
        $payment = $order->paymentTransactions()
            ->where('type', 'payment')->where('status', 'succeeded')
            ->latest('id')->first();

        if (! $payment) {
            $this->showRefundModal = false;
            session()->flash('error', 'This order has no successful payment to refund.');
            return;
        }

        app(PaymentManager::class)->refund(
            original: $payment,
            amount: null,
            reason: $this->refundReason ?: null,
            initiatedBy: auth()->user(),
        );

        ActivityLog::record(
            'payment.refunded',
            "Refunded order #{$order->order_number}",
            $order,
            ['amount' => (float) $payment->amount, 'reason' => $this->refundReason ?: null]
        );

        $this->showRefundModal = false;

        if ($this->selectedOrder?->id === $order->id) {
            $this->selectedOrder = $order->fresh(['items.menuItem', 'user', 'table']);
        }

        session()->flash('success', "Refund recorded for order #{$order->order_number}.");
    }

    public function updatedOrderType(): void
    {
        if ($this->orderType !== 'delivery') {
            $this->deliveryFee = 0;
            $this->deliveryDistanceKm = null;
            $this->deliveryFeeMessage = '';
        } elseif (trim($this->deliveryAddress)) {
            $this->calculateDeliveryFee();
        }
    }

    public function updatedDeliveryAddress(): void
    {
        if ($this->orderType === 'delivery') {
            $this->calculateDeliveryFee();
        }
    }

    public function calculateDeliveryFee(): void
    {
        if (!trim($this->deliveryAddress)) {
            $this->deliveryFee = 0;
            $this->deliveryDistanceKm = null;
            $this->deliveryFeeMessage = '';
            return;
        }

        $result = app(DeliveryFeeCalculator::class)->calculateForAddress($this->deliveryAddress);
        $this->deliveryFee = $result['fee'];
        $this->deliveryDistanceKm = $result['distance_km'];
        $this->deliveryFeeMessage = $result['estimated']
            ? "≈ {$result['distance_km']} km from the restaurant"
            : "Couldn't locate this address — using standard delivery fee";
    }

    public function createOrder(): void
    {
        $this->validate([
            'customerName'    => 'required|string',
            'orderType'       => 'required',
            'paymentMethod'   => 'required',
            'deliveryAddress' => $this->orderType === 'delivery' ? 'required|string' : 'nullable',
        ]);

        if ($this->orderType === 'delivery' && $this->deliveryDistanceKm === null && !$this->deliveryFeeMessage) {
            $this->calculateDeliveryFee();
        }

        $subtotal    = collect($this->newItems)->sum('subtotal');
        $tax         = $subtotal * 0.16;
        $deliveryFee = $this->orderType === 'delivery' ? $this->deliveryFee : 0;

        $order = Order::create([
            'order_type'       => $this->orderType,
            'table_id'         => $this->tableId ?: null,
            'customer_name'    => $this->customerName,
            'customer_phone'   => $this->customerPhone,
            'delivery_address' => $this->deliveryAddress ?: null,
            'payment_method'   => $this->paymentMethod,
            'notes'            => $this->notes,
            'subtotal'         => $subtotal,
            'tax'              => $tax,
            'delivery_fee'     => $deliveryFee,
            'total'            => $subtotal + $tax + $deliveryFee,
            'user_id'          => auth()->id(),
        ]);

        foreach ($this->newItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        if ($this->tableId) {
            RestaurantTable::find($this->tableId)?->update(['status' => 'occupied']);
        }

        $this->reset(['newItems', 'customerName', 'customerPhone', 'deliveryAddress', 'notes', 'tableId', 'deliveryFee', 'deliveryDistanceKm', 'deliveryFeeMessage']);
        $this->showCreateModal = false;
        session()->flash('success', "Order #{$order->order_number} created successfully.");
    }

    public function addItemToNew(): void
    {
        if (!$this->addItemId) return;
        $item = MenuItem::find($this->addItemId);
        if (!$item) return;

        $this->newItems[] = [
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->effective_price,
            'qty' => $this->addItemQty,
            'subtotal' => $item->effective_price * $this->addItemQty,
        ];
        $this->addItemId = null;
        $this->addItemQty = 1;
    }

    public function removeNewItem(int $index): void
    {
        array_splice($this->newItems, $index, 1);
    }

    public function render()
    {
        $orders = Order::with(['user', 'table', 'items'])
            ->when($this->search, fn ($q) => $q->where('order_number', 'like', "%{$this->search}%")
                ->orWhere('customer_name', 'like', "%{$this->search}%")
                ->orWhere('customer_phone', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter, fn ($q) => $q->where('order_type', $this->typeFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.order-management', [
            'orders' => $orders,
            'menuItems' => MenuItem::available()->get(),
            'tables' => RestaurantTable::where('status', 'available')->get(),
            'statuses' => ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'],
            'activeRiders' => DeliveryRider::active()->get(),
        ]);
    }
}
