<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdjustmentProduct extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "purchase_id",
        "adjustment",
        "reason",
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    
}
