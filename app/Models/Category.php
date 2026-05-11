<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'business_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
