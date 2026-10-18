<?php

namespace Modules\Subject\App\Models;

use Spatie\Activitylog\LogOptions;
use Modules\Grade\App\Models\Grade;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Teacher\App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'grade_id', 'school_id', 'semester'];

    protected $hidden = ['created_at', 'updated_at'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Subject')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
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

    public function teachers()
    {
        return $this->belongsToMany(TeacherProfile::class, 'teacher_subject', 'subject_id', 'teacher_id');
    }

    //Helper
    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            }
            if ($admin->hasRole('School Manager')) {
                return $query->where('school_id', $admin->school_id);
            }
        }
    }
}
