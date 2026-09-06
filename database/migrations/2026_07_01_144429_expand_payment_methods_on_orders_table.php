<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Originally a raw MySQL-only `ALTER TABLE ... MODIFY`, which broke on
     * SQLite (the test suite's connection). Rewritten to portable Blueprint
     * syntax. Semantically identical to what already ran on the dev MySQL
     * database, so this is safe to change post-hoc: Laravel skips already-run
     * migrations by filename, it doesn't re-execute this on environments
     * where it's already applied.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', [
                'cash', 'card', 'mobile_money', 'online', 'airtel_money', 'mtn_money', 'zamtel_kwacha', 'zampay', 'bank_transfer',
            ])->default('cash')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'online'])->default('cash')->change();
        });
    }
};
