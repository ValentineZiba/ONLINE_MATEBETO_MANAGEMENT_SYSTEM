<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ZamPay is re-added as its own distinct payment method (previously
     * folded into airtel_money during the enum consolidation). All these
     * mobile-money methods are currently manual/staff-attested gateways —
     * see PaymentServiceProvider — until a real verified integration
     * (Flutterwave) is wired up for them.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mtn_momo', 'airtel_money', 'zamtel_kwacha', 'zampay', 'bank_transfer'])
                ->default('cash')->change();
        });

        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mtn_momo', 'airtel_money', 'zamtel_kwacha', 'zampay', 'bank_transfer'])
                ->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mtn_momo', 'airtel_money', 'zamtel_kwacha', 'bank_transfer'])
                ->default('cash')->change();
        });

        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mtn_momo', 'airtel_money', 'zamtel_kwacha', 'bank_transfer'])
                ->nullable()->change();
        });
    }
};
