<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replaces the raw MySQL-only ALTER TABLE from
     * 2026_07_01_144429_expand_payment_methods_on_orders_table.php with a
     * portable Blueprint::change() call, and narrows the ambiguous legacy
     * values (mobile_money/online/zampay) down to an explicit vocabulary
     * shared by orders and bar_tabs. Existing rows are premapped first so
     * the enum narrowing never encounters a value outside the new list.
     */
    public function up(): void
    {
        DB::table('orders')->whereIn('payment_method', ['mobile_money', 'online'])->update(['payment_method' => 'card']);
        DB::table('orders')->where('payment_method', 'zampay')->update(['payment_method' => 'airtel_money']);
        DB::table('orders')->where('payment_method', 'mtn_money')->update(['payment_method' => 'mtn_momo']);
        DB::table('bar_tabs')->where('payment_method', 'mobile_money')->update(['payment_method' => 'card']);
        DB::table('bar_tabs')->where('payment_method', 'zampay')->update(['payment_method' => 'airtel_money']);
        DB::table('bar_tabs')->where('payment_method', 'mtn_money')->update(['payment_method' => 'mtn_momo']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mtn_momo', 'airtel_money', 'zamtel_kwacha', 'bank_transfer'])
                ->default('cash')->change();
        });

        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mtn_momo', 'airtel_money', 'zamtel_kwacha', 'bank_transfer'])
                ->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', [
                'cash', 'card', 'mobile_money', 'online', 'airtel_money', 'mtn_money', 'zamtel_kwacha', 'zampay', 'bank_transfer',
            ])->default('cash')->change();
        });

        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->enum('payment_method', [
                'cash', 'card', 'mobile_money', 'airtel_money', 'mtn_money', 'zamtel_kwacha', 'zampay', 'bank_transfer',
            ])->nullable()->change();
        });
    }
};
