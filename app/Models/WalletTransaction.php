<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WalletTransaction extends BaseModel
{
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    protected $fillable = [
        'wallet_id',
        'business_id',
        'amount',
        'type',
        'reference',
        'description',
        'status',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function business()
    {
        return $this->hasOneThrough(
            Business::class,
            Wallet::class,
            'id',
            'id',
            'wallet_id',
            'business_id'
        );
    }

    public function getSourceLabelAttribute(): string
    {
        $metadata = $this->metadata ?? [];
        $description = Str::lower((string) $this->description);

        if (isset($metadata['creditor_id']) || Str::contains($description, 'creditor')) {
            return 'Creditor';
        }

        if (isset($metadata['purchase_history_id']) || isset($metadata['purchase_id']) || Str::contains($description, 'purchase')) {
            return 'Purchase';
        }

        if (Str::contains($description, 'capital')) {
            return 'Capital';
        }

        if (Str::contains($description, 'sale')) {
            return 'Sales';
        }

        if (Str::contains($description, 'manual deposit')) {
            return 'Manual Deposit';
        }

        if (Str::contains($description, 'manual withdrawal')) {
            return 'Manual Withdrawal';
        }

        if (Str::contains($description, 'deposit')) {
            return 'Deposit';
        }

        if (Str::contains($description, 'withdraw')) {
            return 'Withdrawal';
        }

        return 'Wallet';
    }
}
