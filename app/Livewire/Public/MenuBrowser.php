<?php

namespace App\Livewire\Public;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Delivery\DeliveryFeeCalculator;
use App\Services\Payments\PaymentManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.public', ['title' => 'Menu'])]
class MenuBrowser extends Component
{
    #[Url]
    public string $category = '';

    #[Url]
    public string $search = '';

    public bool $cartOpen = false;
    public bool $checkoutOpen = false;
    public string $couponCode = '';
    public string $couponMessage = '';
    public float $couponDiscount = 0;
    public string $orderType = 'dine_in';
    public string $customerName = '';
    public string $customerPhone = '';
    public string $deliveryAddress = '';
    public float $deliveryFee = 0;
    public ?float $deliveryDistanceKm = null;
    public string $deliveryFeeMessage = '';
    public bool $calculatingDeliveryFee = false;
    public string $notes = '';
    public string $paymentMethod = 'cash';
    public bool $orderPlaced = false;
    public ?string $orderNumber = null;
    public bool $usePoints = false;
    public bool $fromQr = false;
    public ?int $qrTableId = null;
    public ?string $qrTableNumber = null;

    public function mount(): void
    {
        if (!session()->has('cart')) {
            session(['cart' => []]);
        }

        if (session()->has('qr_table_id')) {
            $this->qrTableId = session('qr_table_id');
            $this->qrTableNumber = session('qr_table_number');
            $this->fromQr = true;
            $this->orderType = 'dine_in';
        }
    }

    public function getCartProperty(): array
    {
        return session('cart', []);
    }

    public function getCartTotalProperty(): float
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getAvailablePointsProperty(): int
    {
        return auth()->user()?->loyalty_points ?? 0;
    }

    // 10 points = K1 discount
    public function getPointsDiscountProperty(): float
    {
        if (!$this->usePoints || $this->availablePoints <= 0) return 0.0;
        return round($this->availablePoints / 10, 2);
    }

    public function addToCart(int $itemId): void
    {
        $item = MenuItem::findOrFail($itemId);
        $cart = session('cart', []);

        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity']++;
            $cart[$itemId]['subtotal'] = $cart[$itemId]['quantity'] * $cart[$itemId]['price'];
        } else {
            $cart[$itemId] = [
                'menu_item_id' => $item->id,
                'name' => $item->name,
                'price' => $item->effective_price,
                'quantity' => 1,
                'subtotal' => $item->effective_price,
                'image' => $item->image,
            ];
        }

        session(['cart' => $cart]);
        $this->cartOpen = true;
        $this->dispatch('cart-updated');
    }

    public function removeFromCart(int $itemId): void
    {
        $cart = session('cart', []);
        unset($cart[$itemId]);
        session(['cart' => $cart]);
        $this->dispatch('cart-updated');
    }

    public function incrementItem(int $itemId): void
    {
        $cart = session('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity']++;
            $cart[$itemId]['subtotal'] = $cart[$itemId]['quantity'] * $cart[$itemId]['price'];
            session(['cart' => $cart]);
        }
    }

    public function decrementItem(int $itemId): void
    {
        $cart = session('cart', []);
        if (isset($cart[$itemId])) {
            if ($cart[$itemId]['quantity'] <= 1) {
                unset($cart[$itemId]);
            } else {
                $cart[$itemId]['quantity']--;
                $cart[$itemId]['subtotal'] = $cart[$itemId]['quantity'] * $cart[$itemId]['price'];
            }
            session(['cart' => $cart]);
        }
    }

    public function applyCoupon(): void
    {
        $coupon = Coupon::where('code', strtoupper($this->couponCode))->first();

        if (!$coupon || !$coupon->isValid()) {
            $this->couponMessage = 'Invalid or expired coupon code.';
            $this->couponDiscount = 0;
            return;
        }

        $this->couponDiscount = $coupon->calculateDiscount($this->cartTotal);
        $this->couponMessage = 'Coupon applied! You save K ' . number_format($this->couponDiscount, 2);
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

        $this->calculatingDeliveryFee = true;
        $result = app(DeliveryFeeCalculator::class)->calculateForAddress($this->deliveryAddress);
        $this->calculatingDeliveryFee = false;

        $this->deliveryFee = $result['fee'];
        $this->deliveryDistanceKm = $result['distance_km'];
        $this->deliveryFeeMessage = $result['estimated']
            ? "≈ {$result['distance_km']} km from the restaurant"
            : "Couldn't locate this address — using standard delivery fee";
    }

    public function placeOrder(): void
    {
        $this->validate([
            'customerName' => 'required|string|min:2',
            'customerPhone' => 'required|string|min:10',
            'orderType' => 'required|in:dine_in,takeaway,delivery',
            // All methods are currently staff-attested/manual (no live
            // charge or verification) until the Flutterwave integration
            // lands for card/mobile money.
            'paymentMethod' => 'required|in:cash,card,airtel_money,mtn_momo,zamtel_kwacha,zampay,bank_transfer',
            'deliveryAddress' => $this->orderType === 'delivery' ? 'required|string' : 'nullable',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) return;

        if ($this->orderType === 'delivery' && $this->deliveryDistanceKm === null && !$this->deliveryFeeMessage) {
            // Defensive: address was filled without the blur event firing
            // (e.g. browser autofill) — calculate before totals are set.
            $this->calculateDeliveryFee();
        }

        $subtotal = collect($cart)->sum('subtotal');
        $tax = $subtotal * 0.16;
        $deliveryFee = $this->orderType === 'delivery' ? $this->deliveryFee : 0;

        // Loyalty points redemption: 10 points = K1
        $loyaltyDiscount = 0.0;
        $pointsRedeemed = 0;
        if ($this->usePoints && auth()->check() && $this->availablePoints > 0) {
            $loyaltyDiscount = round($this->availablePoints / 10, 2);
            $pointsRedeemed = $this->availablePoints;
        }

        $totalDiscount = $this->couponDiscount + $loyaltyDiscount;
        $total = max(0, $subtotal + $tax + $deliveryFee - $totalDiscount);

        $order = Order::create([
            'user_id' => auth()->id(),
            'table_id' => $this->qrTableId,
            'order_type' => $this->orderType,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'customer_email' => auth()->user()?->email,
            'delivery_address' => $this->deliveryAddress,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_fee' => $deliveryFee,
            'discount' => $totalDiscount,
            'total' => $total,
            'coupon_code' => $this->couponDiscount > 0 ? strtoupper($this->couponCode) : null,
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
            'estimated_minutes' => 30,
            'loyalty_points_redeemed' => $pointsRedeemed,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        // Stock deduction, coupon usage, loyalty redemption, and the
        // confirmation email all happen in Order::onPaymentSucceeded()
        // once payment is actually confirmed — not unconditionally here.
        app(PaymentManager::class)->initiate(
            transactionable: $order,
            gatewayKey: $this->paymentMethod,
            amount: $total,
            initiatedBy: auth()->user(),
        );

        session(['cart' => []]);
        if ($this->fromQr) {
            session()->forget(['qr_table_id', 'qr_table_number']);
            $this->fromQr = false;
            $this->qrTableId = null;
            $this->qrTableNumber = null;
        }
        $this->orderNumber = $order->order_number;
        $this->orderPlaced = true;
        $this->checkoutOpen = false;
        $this->cartOpen = false;
    }

    /**
     * The hero carousel stays photo-driven for polish — only falls back to
     * featured items without a photo if there aren't enough with one.
     */
    private function getFeaturedItems()
    {
        $withPhotos = MenuItem::available()->where('is_featured', true)->whereNotNull('image')
            ->with('category')->orderBy('sort_order')->limit(8)->get();

        if ($withPhotos->count() >= 4) {
            return $withPhotos;
        }

        return MenuItem::available()->where('is_featured', true)
            ->with('category')->orderByRaw('image IS NULL')->orderBy('sort_order')->limit(8)->get();
    }

    public function render()
    {
        $query = MenuItem::available()->with('category');

        if ($this->category) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $this->category));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        $cart = session('cart', []);
        return view('livewire.public.menu-browser', [
            'categories' => Category::active()->orderBy('sort_order')->get(),
            'items' => $query->orderBy('sort_order')->get(),
            'featuredItems' => $this->search ? collect() : $this->getFeaturedItems(),
            'cart' => $cart,
            'cartCount' => collect($cart)->sum('quantity'),
            'cartTotal' => collect($cart)->sum('subtotal'),
        ]);
    }
}
