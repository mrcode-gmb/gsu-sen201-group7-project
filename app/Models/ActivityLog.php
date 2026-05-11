<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'action',
        'model',
        'model_id',
        'description',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the business associated with the activity
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Log an activity
     */
    public static function log($action, $description, $model = null, $modelId = null, $properties = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'business_id' => auth()->user()->business_id ?? null,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
        ]);
    }
}
