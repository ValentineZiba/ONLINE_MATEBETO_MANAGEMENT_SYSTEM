<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * bar_tabs.status previously doubled as the payment state (open/closed/paid),
     * the same conflation bug that orders.status had. This splits payment
     * concerns into their own column so status can mean purely "can items
     * still be added" (open/closed).
     */
    public function up(): void
    {
        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'processing', 'paid', 'failed', 'cancelled', 'refunded'])
                ->default('pending')->after('status');
        });

        // Only 'paid' rows are known to have been paid; 'closed' was never
        // actually written by app code (dead enum value), so it's left
        // defaulting to payment_status='pending' rather than assumed paid.
        DB::table('bar_tabs')->where('status', 'paid')->update(['payment_status' => 'paid']);
        DB::table('bar_tabs')->where('status', 'paid')->update(['status' => 'closed']);

        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->enum('status', ['open', 'closed'])->default('open')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->enum('status', ['open', 'closed', 'paid'])->default('open')->change();
        });

        DB::table('bar_tabs')->where('payment_status', 'paid')->update(['status' => 'paid']);

        Schema::table('bar_tabs', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
