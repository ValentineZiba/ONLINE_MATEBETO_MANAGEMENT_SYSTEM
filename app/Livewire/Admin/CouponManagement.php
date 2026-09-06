<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Coupons'])]
class CouponManagement extends Component
{
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;
    public string $code = '';
    public string $description = '';
    public string $type = 'percentage';
    public float $value = 10;
    public float $minOrderAmount = 0;
    public ?float $maxDiscount = null;
    public ?int $maxUses = null;
    public bool $isActive = true;
    public string $expiresAt = '';

    public function openCreate(): void
    {
        $this->reset(['code','description','type','value','minOrderAmount','maxDiscount','maxUses','isActive','expiresAt','editingId']);
        $this->type = 'percentage';
        $this->value = 10;
        $this->isActive = true;
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $coupon = Coupon::findOrFail($id);
        $this->editingId = $id;
        $this->code = $coupon->code;
        $this->description = $coupon->description ?? '';
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->minOrderAmount = $coupon->min_order_amount;
        $this->maxDiscount = $coupon->max_discount;
        $this->maxUses = $coupon->max_uses;
        $this->isActive = $coupon->is_active;
        $this->expiresAt = $coupon->expires_at?->format('Y-m-d') ?? '';
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'code' => 'required|string|max:20|unique:coupons,code' . ($this->editingId ? ",{$this->editingId}" : ''),
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        $data = [
            'code' => strtoupper($this->code),
            'description' => $this->description ?: null,
            'type' => $this->type,
            'value' => $this->value,
            'min_order_amount' => $this->minOrderAmount,
            'max_discount' => $this->maxDiscount ?: null,
            'max_uses' => $this->maxUses ?: null,
            'is_active' => $this->isActive,
            'expires_at' => $this->expiresAt ?: null,
        ];

        if ($this->editing && $this->editingId) {
            Coupon::findOrFail($this->editingId)->update($data);
        } else {
            Coupon::create($data);
        }
        $this->showModal = false;
    }

    public function toggle(int $id): void
    {
        $c = Coupon::findOrFail($id);
        $c->update(['is_active' => !$c->is_active]);
    }

    public function delete(int $id): void
    {
        Coupon::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.coupon-management', [
            'coupons' => Coupon::orderByDesc('created_at')->get(),
        ]);
    }
}
