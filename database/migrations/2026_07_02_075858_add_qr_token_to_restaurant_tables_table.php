<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->uuid('qr_token')->nullable()->unique()->after('notes');
        });

        // Backfill tokens for existing tables
        DB::table('restaurant_tables')->lazyById()->each(function ($t) {
            DB::table('restaurant_tables')
                ->where('id', $t->id)
                ->update(['qr_token' => Str::uuid()->toString()]);
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
