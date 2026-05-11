<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoftNews extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name_title',
        'new_content',
    ];
}
