<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CollectHistory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_assignment_id',
        'collected_by',
        'collected_quantity',
        'remaining_quantity_before',
        'remaining_quantity_after',
        'notes',
        'collected_at',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'collected_quantity' => 'decimal:2',
        'remaining_quantity_before' => 'decimal:2',
        'remaining_quantity_after' => 'decimal:2',
    ];

    /**
     * Get the product assignment this collection belongs to
     */
    public function productAssignment()
    {
        return $this->belongsTo(ProductAssignment::class);
    }

    /**
     * Get the user who collected the return
     */
    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
