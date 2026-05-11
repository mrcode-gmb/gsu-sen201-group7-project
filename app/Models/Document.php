<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'path',
        'size',
        'type',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
