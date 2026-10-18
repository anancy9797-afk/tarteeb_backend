<?php

namespace Modules\Common\App\Models;

use Modules\Grade\App\Models\Grade;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Modules\Class\App\Models\Classroom;
use Modules\Student\App\Models\Student;
use Modules\Subject\App\Models\Subject;
use Modules\Teacher\App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class History extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['day', 'session_number', 'semester', 'year', 'teacher_id', 'attendance_taken_by', 'student_id', 'subject_id', 'session_id', 'class_id', 'school_id', 'is_present', 'teacher_name', 'attendance_taken_by_name', 'student_name', 'subject_name', 'class_name', 'school_name'];

    //Relations
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function attendanceTakenBy()
    {
        return $this->belongsTo(TeacherProfile::class, 'attendance_taken_by');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function class()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    //Helper
    //Helper
    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            }
            if ($admin->hasRole('School Manager')) {
                // استخدام العلاقة لأن جدول histories ليس به عمود school_id مباشرة
                return $query->whereHas('school', function ($q) use ($admin) {
                    $q->where('id', $admin->school_id);
                });
            }
            if ($admin->hasRole('Teacher')) {
                return $query->where('teacher_id', optional($admin->teacherProfile)->id);
            }
        }
    }
    // public function scopeAvailable($query)
    // {
    //     if (auth('user')->check()) {
    //         $admin = auth('user')->user();
    //         if ($admin->hasRole('Super Admin')) {
    //         }
    //         if ($admin->hasRole('School Manager')) {

    //             return $query->where('school_id', $admin->school_id);
    //         }
    //         if ($admin->hasRole('Teacher')) {
    //             return $query->where('teacher_id', $admin->teacherProfile->id);
    //         }
    //     }
    // }
}

