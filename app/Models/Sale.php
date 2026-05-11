<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Sale extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'user_id',
        'quantity',
        'selling_price_per_unit',
        'cost_price_per_unit',
        'total_amount',
        'total_cost',
        'profit',
        'seller_profit_per_unit',
        'net_profit_per_unit',
        'customer_name',
        'customer_phone',
        'payment_type',
        'assignment_id',
        'sale_status',
        'sale_date',
        'notes',
        'unique_id',
        'business_id',
        'creditor_id',
        'amount_paid',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    /**
     * Get the product that was sold
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    /**
     * Get the user who made this sale
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            $sale->unique_id = 'SALE-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
        });
    }
    /**
     * Get the product assignment this sale belongs to
     */
    public function assignment()
    {
        return $this->belongsTo(ProductAssignment::class, 'assignment_id');
    }

    /**
     * Get the business this sale belongs to
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the creditor this sale belongs to
     */
    public function creditor()
    {
        return $this->belongsTo(Creditor::class);
    }
}
