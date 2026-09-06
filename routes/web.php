<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\AnalyticsExportController;
use App\Http\Controllers\Admin\DeliveryRiderDocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QrOrderController;

// ── Public Routes ──────────────────────────────────────────────────────────
Route::get('/', \App\Livewire\Public\Home::class)->name('home');
Route::get('/menu', \App\Livewire\Public\MenuBrowser::class)->name('menu');
Route::get('/reservations', \App\Livewire\Public\ReservationForm::class)->name('reservations');
Route::get('/track-order', \App\Livewire\Public\OrderTracker::class)->name('order.track');
Route::get('/track-order/{order}', \App\Livewire\Public\OrderTracker::class)->name('order.track.show');
Route::get('/review', \App\Livewire\Public\ReviewForm::class)->name('review');

// ── Auth Routes (from Livewire starter kit) ──────────────────────────────
require __DIR__.'/auth.php';

// ── Invoice Download ──────────────────────────────────────────────────────
Route::middleware('auth')->get('/invoice/{order}', [InvoiceController::class, 'download'])->name('invoice.download');

// ── QR Code Ordering ──────────────────────────────────────────────────────
Route::get('/order/table/{token}', [QrOrderController::class, 'scan'])->name('qr.order');
Route::middleware(['auth', 'role:admin,manager'])->get('/admin/qr/{table}', [QrOrderController::class, 'show'])->name('qr.show');

// ── Customer Portal (authenticated) ──────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isAdmin() || $user->isManager()) return redirect()->route('admin.dashboard');
        if ($user->isKitchen()) return redirect()->route('kitchen.display');
        if ($user->isBar()) return redirect()->route('bar.display');
        if ($user->isWaiter()) return redirect()->route('pos.terminal');
        return redirect()->route('customer.orders');
    })->name('dashboard');

    Route::get('/my-orders', \App\Livewire\Customer\MyOrders::class)->name('customer.orders');
    Route::get('/my-reservations', \App\Livewire\Customer\MyReservations::class)->name('customer.reservations');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// ── Admin Routes ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/orders', \App\Livewire\Admin\OrderManagement::class)->name('orders');
    Route::get('/payments', \App\Livewire\Admin\PaymentTransactions::class)->name('payments');
    Route::get('/delivery-riders', \App\Livewire\Admin\DeliveryRiders::class)->name('delivery-riders');
    Route::get('/delivery-riders/{rider}/document/{type}', [DeliveryRiderDocumentController::class, 'show'])->name('delivery-riders.document');
    Route::get('/activity-log', \App\Livewire\Admin\ActivityLogViewer::class)->name('activity-log');
    Route::get('/menu/categories', \App\Livewire\Admin\MenuCategories::class)->name('menu.categories');
    Route::get('/menu/items', \App\Livewire\Admin\MenuItems::class)->name('menu.items');
    Route::get('/tables', \App\Livewire\Admin\TableManagement::class)->name('tables');
    Route::get('/reservations', \App\Livewire\Admin\ReservationManagement::class)->name('reservations');
    Route::get('/analytics', \App\Livewire\Admin\Analytics::class)->name('analytics');
    Route::get('/analytics/export/orders', [AnalyticsExportController::class, 'orders'])->name('analytics.export.orders');
    Route::get('/analytics/export/items', [AnalyticsExportController::class, 'topItems'])->name('analytics.export.items');
    Route::get('/reviews', \App\Livewire\Admin\ReviewManagement::class)->name('reviews');
    Route::get('/coupons', \App\Livewire\Admin\CouponManagement::class)->name('coupons');
    Route::get('/settings', \App\Livewire\Admin\SettingsManager::class)->name('settings');
    Route::get('/waitlist', \App\Livewire\Admin\WaitlistManagement::class)->name('waitlist');
    Route::get('/inventory', \App\Livewire\Admin\InventoryManagement::class)->name('inventory');
    Route::get('/scheduling', \App\Livewire\Admin\StaffScheduling::class)->name('scheduling');

    // Admin-only
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', \App\Livewire\Admin\UserManagement::class)->name('users');
    });
});

// ── Waiter Routes ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,manager,waiter'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', \App\Livewire\Pos\Terminal::class)->name('terminal');
});

// ── Kitchen Display ───────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,manager,kitchen,waiter'])->group(function () {
    Route::get('/kitchen', \App\Livewire\Kitchen\Display::class)->name('kitchen.display');
});

// ── Bar Module ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,manager,waiter,bar'])->prefix('bar')->name('bar.')->group(function () {
    Route::get('/',     \App\Livewire\Bar\Display::class)->name('display');
    Route::get('/tabs', \App\Livewire\Bar\TabManager::class)->name('tabs');
});
