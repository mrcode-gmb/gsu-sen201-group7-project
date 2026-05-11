<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expenses extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'name',
        'today_profit',
        'amount',
        'today_net_profit',
        'notes',
        'business_id',
    ];


    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the business this expense belongs to
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
