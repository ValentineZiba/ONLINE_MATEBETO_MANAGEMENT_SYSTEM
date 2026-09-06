<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transactionable', 'payment_transactions_transactionable_index'); // Order or BarTab today
            $table->string('reference')->unique(); // our merchant reference, sent to the gateway as its idempotency/external id
            $table->string('gateway'); // 'cash' | 'card' | 'bank_transfer' | 'flutterwave'
            $table->string('type')->default('payment'); // 'payment' | 'refund'
            $table->string('status')->default('pending'); // 'pending' | 'processing' | 'succeeded' | 'failed' | 'cancelled'
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('ZMW');
            $table->string('phone_number', 20)->nullable(); // mobile money only
            $table->string('gateway_reference')->nullable(); // gateway's own transaction id
            $table->json('gateway_response')->nullable(); // last raw response payload (redacted before storage)
            $table->string('failure_reason')->nullable();
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete(); // null = self-serve customer checkout
            $table->timestamp('confirmed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'gateway_reference']);
            $table->index(['status', 'gateway']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
