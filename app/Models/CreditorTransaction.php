<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditorTransaction extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'creditor_id',
        'type',
        'amount',
        'description',
        'running_balance',
    ];

    public function creditor()
    {
        return $this->belongsTo(Creditor::class);
    }
}
