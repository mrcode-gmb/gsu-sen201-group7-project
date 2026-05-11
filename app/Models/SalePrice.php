<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePrice extends BaseModel
{
    protected $fillable = [
        'product_assignment_id',
        'customer_type',
        'sale_price',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
    ];

    public function productAssignment(): BelongsTo
    {
        return $this->belongsTo(ProductAssignment::class);
    }
}
