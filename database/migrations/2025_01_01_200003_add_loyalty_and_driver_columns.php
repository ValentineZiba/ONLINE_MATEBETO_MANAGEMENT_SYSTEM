<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->after('is_active');
            $table->integer('total_points_earned')->default(0)->after('loyalty_points');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('notes');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->enum('delivery_status', ['pending', 'assigned', 'picked_up', 'delivered'])->nullable()->after('driver_phone');
            $table->timestamp('picked_up_at')->nullable()->after('delivery_status');
            $table->timestamp('delivered_at')->nullable()->after('picked_up_at');
            $table->integer('loyalty_points_earned')->default(0)->after('delivered_at');
            $table->integer('loyalty_points_redeemed')->default(0)->after('loyalty_points_earned');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'total_points_earned']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'driver_phone', 'delivery_status', 'picked_up_at', 'delivered_at', 'loyalty_points_earned', 'loyalty_points_redeemed']);
        });
    }
};
