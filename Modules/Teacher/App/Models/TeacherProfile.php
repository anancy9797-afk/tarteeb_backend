<?php

namespace Modules\Teacher\App\Models;

use Modules\User\App\Models\User;
use Modules\Grade\App\Models\Grade;
use Modules\Subject\App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TeacherProfile extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'gender'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Teacher')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Relations
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // public function grade()
    // {
    //     return $this->belongsTo(Grade::class);
    // }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'teacher_id', 'subject_id');
    }

    //Helper
    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            }
            if ($admin->hasRole('School Manager')) {
                return $query->whereHas('teacher', function ($query) use ($admin) {
                    $query->where('school_id', $admin->school_id);
                });
            }
        }
    }
}
