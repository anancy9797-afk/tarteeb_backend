<?php

namespace Modules\School\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Modules\Grade\App\Models\Grade;
use Illuminate\Database\Eloquent\Model;
use Modules\Class\App\Models\Classroom;
use Modules\Session\App\Models\Session;
use Modules\Student\App\Models\Student;
use Modules\Subject\App\Models\Subject;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\School\App\Models\SchoolSetting;
use Modules\Teacher\App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory, LogsActivity;

      /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'is_active'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('School')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    //Relations
    public function manager()
    {
        return $this->hasOne(User::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function teachers()
    {
        return $this->hasManyThrough(TeacherProfile::class, User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function classes()
    {
        return $this->hasMany(Classroom::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
    public function settings()
    {
        return $this->hasOne(SchoolSetting::class);
    }
}
