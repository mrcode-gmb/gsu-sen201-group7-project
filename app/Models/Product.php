<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PurchaseHistory;

class Product extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'sku',
        'storage_location',
        'unit_type',
        'current_stock',
        'selling_price',
        'seller_profit',
        'description',
        'purchase_price',
        'low_stock',
        'low_stock_threshold',
        'business_id',
    ];

    protected $casts = [
        'current_stock' => 'decimal:4',
        'low_stock' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    /**
     * Get purchases for this product
     */
    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseHistory()
    {
        return $this->hasMany(PurchaseHistory::class);
    }


    public function expensive()
    {
        return $this->hasMany(Expenses::class);
    }
    /**
     * Get sales for this product
     */
    public function sales()
    {
        return $this->hasManyThrough(Sale::class, Purchase::class);
    }
    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->effective_low_stock_threshold;
    }

    public function getEffectiveLowStockThresholdAttribute()
    {
        return (int) ($this->low_stock_threshold ?? $this->low_stock ?? 10);
    }
    /**
     * Get average cost price for profit calculation
     */
    public function getAverageCostPrice()
    {
        $totalCost = $this->purchaseHistory()->sum('total_cost');
        $totalQuantity = $this->purchaseHistory()->sum('quantity');

        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
    }

    /**
     * Update stock after purchase
     */
    public function addStock($current_stock)
    {
        $this->current_stock += $current_stock;
        $this->save();
    }

    /**
     * Update stock after sale
     */
    public function reduceStock($current_stock)
    {
        $this->current_stock -= $current_stock;
        $this->save();
    }

    /**
     * Get the business this product belongs to
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
