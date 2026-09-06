<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->integer('party_size');
            $table->enum('status', ['waiting', 'notified', 'seated', 'cancelled'])->default('waiting');
            $table->text('notes')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('seated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('waitlist'); }
};
