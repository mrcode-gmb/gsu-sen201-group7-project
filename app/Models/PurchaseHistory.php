<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseHistory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "product_id",
        "user_id",
        "supplier_name",
        "supplier_phone",
        "quantity",
        "purchase_price",
        "total_cost",
        "selling_price",
        "seller_profit",
        "purchase_date",
        "notes",
        "business_id",
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];


    public function addStock($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }

    public function getAverageCostPrice()
    {
        $totalCost = $this->sum('purchase_price');
        $totalQuantity = $this->sum('quantity');
        
        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
    }

    public function adjustment()
    {
    return $this->belongsTo(AdjustmentProduct::class);
    }
    /**
     * Update stock after sale
     */
    public function reduceStock($quantity)
    {
        $this->quantity -= $quantity;
        $this->save();
    }
    /**
     * Get the product that was purchased
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who recorded this purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function assignProduct()
    {
        return $this->hasMany(ProductAssignment::class);
    }


    public function expenses()
    {
        return $this->hasMany(Expenses::class);
    }

    /**
     * Get the business this purchase belongs to
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
