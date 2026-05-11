<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rebate extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'quantity',
        'unit_purchase_price',
        'total_cost',
        'note',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_purchase_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Get the purchase/inventory record this rebate belongs to.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the user that created this rebate.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

