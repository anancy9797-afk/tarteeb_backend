<?php

namespace Modules\Purchase\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    const STATUS_PENDING = "pending";
    const STATUS_ACCEPTED = "accepted";
    const STATUS_REJECTED = "rejected";
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['description', 'user_id', 'school_id', 'image', 'date', 'price', 'reject_reason', 'status'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Purchase')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    public function getImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/purchase/' . $value);
            }
        }
    }
    protected function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            } else if ($admin->hasAnyRole('School Manager', 'Financial Director')) {
                $query->where('school_id', $admin->school_id);
            }
        }
    }
    //Relations
    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
