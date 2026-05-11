<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoftImage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "name",
        "image_path",
    ];
    // include this field in API response
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
