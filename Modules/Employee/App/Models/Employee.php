<?php

namespace Modules\Employee\App\Models;

use Spatie\Activitylog\LogOptions;
use Modules\School\App\Models\School;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'phone', 'email', 'image', 'password', 'school_id', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Employee')
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
                return asset('uploads/employee/' . $value);
            }
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }


    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            } else if ($admin->hasRole('School Manager')) {
                $query->where('school_id', $admin->school_id);
            }
        } else if (auth('employee')->check()) {
            $employee = auth('employee')->user();
            if ($employee->hasRole('Salaries Employee')) {
                $query->where('school_id', $employee->school_id);
            }
        }
    }

    //JWT

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
