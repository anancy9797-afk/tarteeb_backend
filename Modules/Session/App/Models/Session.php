<?php

namespace Modules\Session\App\Models;

use Spatie\Activitylog\LogOptions;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Modules\Class\App\Models\Classroom;
use Modules\Subject\App\Models\Subject;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Teacher\App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Session extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['day', 'class_id', 'subject_id', 'session_number', 'semester', 'year', 'school_id', 'teacher_id', 'is_final'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Session')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Relations
    public function class()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    //Helper
    public function scopeAvailable($query){
        if(auth('user')->check()){
            $admin = auth('user')->user();
            if($admin->hasRole('Super Admin')){
            }
            if($admin->hasRole('School Manager')){
                $query->where('school_id', $admin->school_id);
            }
            if($admin->hasRole('Teacher')){
                $query->where('school_id', $admin->school_id);
            }
        }
    }

}
