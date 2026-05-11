<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Business extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'business_type',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($business) {
            if (empty($business->slug)) {
                $business->slug = Str::slug($business->name);
            }
        });
    }

    /**
     * Get all users belonging to this business.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the wallet associated with the business.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get all of the wallet transactions for the business.
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get the admin user for this business.
     */
    public function admin()
    {
        return $this->hasOne(User::class)->where('role', 'admin');
    }

    /**
     * Get all salespeople for this business.
     */
    public function salespeople()
    {
        return $this->hasMany(User::class)->where('role', 'salesperson');
    }

    /**
     * Get all products for this business.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all sales for this business.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all purchases for this business.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseHistory()
    {
        return $this->hasMany(PurchaseHistory::class);
    }

    /**
     * Get all expenses for this business.
     */
    public function expenses()
    {
        return $this->hasMany(Expenses::class);
    }

    /**
     * Get all product assignments for this business.
     */
    public function productAssignments()
    {
        return $this->hasMany(ProductAssignment::class);
    }

    /**
     * Get all creditors for this business.
     */
    public function creditors()
    {
        return $this->hasMany(Creditor::class);
    }

    /**
     * Get all of the creditor transactions for the business.
     */
    public function creditorTransactions()
    {
        return $this->hasManyThrough(CreditorTransaction::class, Creditor::class);
    }

    /**
     * Get all of the documents for the business.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function businessCapital()
    {
        return $this->hasOne(BusinessCapital::class);
    }

    /**
     * Check if business is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Scope to get only active businesses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
