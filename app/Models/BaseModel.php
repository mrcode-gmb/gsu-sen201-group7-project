<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower(class_basename(static::class)))
            ->setDescriptionForEvent(fn (string $eventName) => $eventName)
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        $properties = $activity->properties ? $activity->properties->toArray() : [];
        $businessId = $this->business_id ?? auth()->user()?->business_id;

        $activity->event = $eventName;
        $activity->properties = array_merge($properties, [
            'business_id' => $businessId,
            'ip_address' => request()->ip(),
        ]);
    }
}
