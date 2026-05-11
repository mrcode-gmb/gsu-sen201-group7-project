<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessCapital extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'balance',
        'currency',
        'status',
    ];

    public function credit($amount, $description = 'Capital Deposit')
    {
        return \DB::transaction(function () use ($amount, $description) {
            // Credit the capital account
            $this->balance += $amount;
            $this->save();

            // Also credit the main business wallet and create a transaction
            $this->business->wallet->credit($amount, $description, [], $this->business_id);
        });
    }

    public function debit($amount, $description = 'Capital Withdrawal')
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient capital');
        }

        $this->balance -= $amount;

        $this->business->wallet->debit($amount, $description, [], $this->business_id);
        $this->save();

        // You might want to log this transaction in a separate table in the future
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
