<?php

namespace Modules\Class\App\Models;

use Spatie\Activitylog\LogOptions;
use Modules\Grade\App\Models\Grade;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Modules\Session\App\Models\Session;
use Modules\Student\App\Models\Student;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    use HasFactory, LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Class')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'grade_id', 'school_id', 'max_students', 'session_number'];
    protected $table = 'classes';
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Relations
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
    //Helper
    protected function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
                // Show All Teachers
            } else if ($admin->hasRole('School Manager')) {
                // Show Teachers Related To Current School
                $query->where('school_id', $admin->school_id);
            } else if ($admin->hasRole('Teacher')) {
                // Show Classes Related To Current Teacher
                $query->where('school_id', $admin->school_id);
            }
        }
    }

}
