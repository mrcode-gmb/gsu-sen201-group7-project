<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Creditor extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'balance',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function transactions()
    {
        return $this->hasMany(CreditorTransaction::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
