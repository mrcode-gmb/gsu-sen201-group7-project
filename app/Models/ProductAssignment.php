<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAssignment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'purchase_id',
        'assigned_quantity',
        'sold_quantity',
        'returned_quantity',
        'expected_selling_price',
        'commission_rate',
        'commission_amount',
        'actual_total_sales',
        'profit_collected',
        'status',
        'assigned_date',
        'due_date',
        'returned_date',
        'notes',
        'business_id',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'due_date' => 'date',
        'returned_date' => 'date',
    ];

    /**
     * Get the staff member assigned to this product
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchase/product assigned
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get all sales made from this assignment
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'assignment_id');
    }


    /**
     * Calculate expected profit
     */
    public function getExpectedProfitAttribute()
    {
        $costPrice = $this->purchase->cost_price_per_unit ?? 0;
        return ($this->expected_selling_price - $costPrice) * $this->assigned_quantity;
    }

    /**
     * Calculate actual profit
     */
    public function getActualProfitAttribute()
    {
        $costPrice = $this->purchase->cost_price_per_unit ?? 0;
        return $this->actual_total_sales - ($costPrice * $this->sold_quantity);
    }

    /**
     * Check if assignment is overdue
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Get the business this assignment belongs to
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get collection histories for this assignment
     */
    public function collectionHistories()
    {
        return $this->hasMany(CollectHistory::class);
    }

    /**
     * Get the sale prices for this assignment
     */
    public function salePrices()
    {
        return $this->hasMany(SalePrice::class);
    }

    /**
     * Get total collected quantity from all collections
     */
    public function getTotalCollectedQuantityAttribute()
    {
        return $this->collectionHistories()->sum('collected_quantity');
    }

    /**
     * Calculate remaining quantity after all collections
     */
    public function getRemainingQuantityAttribute()
    {
        $totalCollected = $this->total_collected_quantity ?? 0;
        return $this->assigned_quantity - $this->sold_quantity - $totalCollected;
    }
}
