<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Settings'])]
class SettingsManager extends Component
{
    public string $restaurantName = '';
    public string $restaurantTagline = '';
    public string $restaurantEmail = '';
    public string $restaurantPhone = '';
    public string $restaurantAddress = '';
    public string $openingHours = '';
    public string $currency = '';
    public string $currencySymbol = '';
    public string $taxRate = '';
    public string $deliveryFee = '';
    public string $deliveryBaseFee = '';
    public string $deliveryRatePerKm = '';
    public string $restaurantLatitude = '';
    public string $restaurantLongitude = '';
    public string $minOrderAmount = '';
    public string $facebookUrl = '';
    public string $instagramUrl = '';
    public string $twitterUrl = '';

    public function mount(): void
    {
        $this->restaurantName = Setting::get('restaurant_name', 'Matebeto Restaurant');
        $this->restaurantTagline = Setting::get('restaurant_tagline', '');
        $this->restaurantEmail = Setting::get('restaurant_email', '');
        $this->restaurantPhone = Setting::get('restaurant_phone', '');
        $this->restaurantAddress = Setting::get('restaurant_address', '');
        $this->openingHours = Setting::get('opening_hours', '');
        $this->currency = Setting::get('currency', 'K');
        $this->currencySymbol = Setting::get('currency_symbol', 'K');
        $this->taxRate = Setting::get('tax_rate', '16');
        $this->deliveryFee = Setting::get('delivery_fee', '150');
        $this->deliveryBaseFee = Setting::get('delivery_base_fee', '30');
        $this->deliveryRatePerKm = Setting::get('delivery_rate_per_km', '15');
        $this->restaurantLatitude = Setting::get('restaurant_latitude', '-15.3875');
        $this->restaurantLongitude = Setting::get('restaurant_longitude', '28.3228');
        $this->minOrderAmount = Setting::get('min_order_amount', '500');
        $this->facebookUrl = Setting::get('facebook_url', '');
        $this->instagramUrl = Setting::get('instagram_url', '');
        $this->twitterUrl = Setting::get('twitter_url', '');
    }

    public function save(): void
    {
        $this->validate([
            'restaurantName' => 'required|string|max:100',
            'restaurantEmail' => 'nullable|email',
            'taxRate' => 'required|numeric|min:0|max:100',
            'deliveryBaseFee' => 'required|numeric|min:0',
            'deliveryRatePerKm' => 'required|numeric|min:0',
            'restaurantLatitude' => 'required|numeric|between:-90,90',
            'restaurantLongitude' => 'required|numeric|between:-180,180',
        ]);

        Setting::set('restaurant_name', $this->restaurantName);
        Setting::set('restaurant_tagline', $this->restaurantTagline);
        Setting::set('restaurant_email', $this->restaurantEmail);
        Setting::set('restaurant_phone', $this->restaurantPhone);
        Setting::set('restaurant_address', $this->restaurantAddress);
        Setting::set('opening_hours', $this->openingHours);
        Setting::set('currency', $this->currency);
        Setting::set('currency_symbol', $this->currencySymbol);
        Setting::set('tax_rate', $this->taxRate, 'billing');
        Setting::set('delivery_fee', $this->deliveryFee, 'billing');
        Setting::set('delivery_base_fee', $this->deliveryBaseFee, 'billing');
        Setting::set('delivery_rate_per_km', $this->deliveryRatePerKm, 'billing');
        Setting::set('restaurant_latitude', $this->restaurantLatitude, 'general');
        Setting::set('restaurant_longitude', $this->restaurantLongitude, 'general');
        Setting::set('min_order_amount', $this->minOrderAmount, 'billing');
        Setting::set('facebook_url', $this->facebookUrl, 'social');
        Setting::set('instagram_url', $this->instagramUrl, 'social');
        Setting::set('twitter_url', $this->twitterUrl, 'social');

        session()->flash('success', 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings-manager');
    }
}
