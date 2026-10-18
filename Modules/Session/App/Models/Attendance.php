<?php

namespace Modules\Session\App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Session\App\Models\Session;
use Modules\Student\App\Models\Student;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['student_id', 'session_id', 'is_present', 'teacher_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Attendance')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

       protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    //Relations
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    //Helper
    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $user = auth('user')->user();
            if ($user->hasRole('Super Admin')) {
            }
            if ($user->hasRole('School Manager')) {
                return $query->whereHas('session', function ($query) use ($user) {
                    $query->where('school_id', $user->school_id);
                });
            } elseif ($user->hasRole('Teacher')) {
                return $query->whereHas('session', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacherProfile->id);
                });
            }
        }
    }
}
