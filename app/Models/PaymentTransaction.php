<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transactionable_type', 'transactionable_id', 'reference', 'gateway', 'type', 'status',
        'amount', 'currency', 'phone_number', 'gateway_reference', 'gateway_response',
        'failure_reason', 'initiated_by', 'confirmed_at', 'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'meta' => 'array',
        'confirmed_at' => 'datetime',
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['succeeded', 'failed', 'cancelled'], true);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }
}
