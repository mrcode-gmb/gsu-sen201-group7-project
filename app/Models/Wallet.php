<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends BaseModel
{
    protected $fillable = [
        'business_id',
        'balance',
        'currency',
        'status',
        'last_transaction_at'
    ];
    
    protected $dates = [
        'last_transaction_at'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_transaction_at' => 'datetime',
    ];
    
    protected static function booted()
    {
        static::creating(function ($wallet) {
            $wallet->last_transaction_at = now();
        });
    }
    
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
    
    public function credit($amount, $description = 'Deposit', $metadata = [])
    {
        return $this->processTransaction($amount, WalletTransaction::TYPE_CREDIT, $description, $metadata, $this->business_id);
    }
    
    public function debit($amount, $description = 'Withdrawal', $metadata = [])
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient funds in wallet');
        }
        
        return $this->processTransaction($amount, WalletTransaction::TYPE_DEBIT, $description, $metadata, $this->business_id);
    }
    
    protected function processTransaction($amount, $type, $description, $metadata, $business_id)
    {
        return \DB::transaction(function () use ($amount, $type, $description, $metadata, $business_id) {
            $transaction = $this->transactions()->create([
                'wallet_id' => $this->id,
                'business_id'=>$business_id,
                'amount' => $amount,
                'type' => $type,
                'reference' => 'WALLET-' . strtoupper(\Str::random(10)),
                'description' => $description,
                'status' => 'completed',
                'metadata' => $metadata
            ]);
            if ($type === WalletTransaction::TYPE_CREDIT) {
                $this->balance += $amount;
            } else {
                $this->balance -= $amount;
            }
            
            $this->last_transaction_at = now();
            $this->save();
            
            return $transaction;
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
