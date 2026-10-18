<?php

namespace Modules\Salary\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['created_by', 'user_id', 'school_id', 'salary', 'month', 'year', 'deduction', 'deduction_reason', 'bonus', 'bonus_reason'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Salary')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }


    protected function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            } else if ($admin->hasAnyRole('School Manager', 'Financial Director', 'Salaries Employee')) {
                $query->where('school_id', $admin->school_id);
            }
        }
    }
}
