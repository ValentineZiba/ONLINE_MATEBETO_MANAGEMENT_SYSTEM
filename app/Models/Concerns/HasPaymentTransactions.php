<?php

namespace App\Models\Concerns;

use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPaymentTransactions
{
    public function paymentTransactions(): MorphMany
    {
        return $this->morphMany(PaymentTransaction::class, 'transactionable');
    }

    public function latestPaymentTransaction(): ?PaymentTransaction
    {
        return $this->paymentTransactions()->latest('id')->first();
    }

    /**
     * Called once by PaymentManager the first time a payment transaction
     * for this model transitions to 'succeeded'. Override per-model for
     * side effects that must only happen once payment is confirmed.
     */
    public function onPaymentSucceeded(PaymentTransaction $transaction): void
    {
        //
    }
}
