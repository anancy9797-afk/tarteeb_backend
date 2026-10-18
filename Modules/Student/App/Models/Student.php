<?php

namespace Modules\Student\App\Models;

use Modules\User\App\Models\User;
use Modules\Expense\App\Models\StudentExpense;
use Spatie\Activitylog\LogOptions;
use Modules\Grade\App\Models\Grade;
use Modules\Country\App\Models\State;
use Modules\School\App\Models\School;
use Modules\Country\App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Modules\Class\App\Models\Classroom;
use Modules\Session\App\Models\Attendance;
use Modules\Student\App\Models\StudentFee;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Student\App\Models\StudentParent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Student')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'email',
        'identity_number',
        'password',
        'parent_email',
        'parent_phone',
        'grade_id',
        'class_id',
        'school_id',
        'gender',
        'is_graduated',
        'is_active',
        'user_id',
        'is_fee_paid',
        'application_form',
        'is_register',
        'address',
        'state_id',
        'branch_id',
        'name_en',
        'birth_date',
        'education_level',
        'has_learning_difficulties',
        'educational_system',
        'behavioral_data',
        'pronunciation',
        'chronic_diseases',
        'food_allergies',
        'other_notes',
        'transport',
        'street_number',
        'house_number',
        'nearest_landmark',
        'home_location_url',
        'siblings_count',
        'parent_identity_card_image',
        'student_residence_card_image',
        'image',
        'student_passport_image',
        'student_birth_certificate_image',
        'student_health_card_image',
        'home_map_image',
        'register_fee_image',
        'distinguished_skills',
        'has_previous_education',
        'previous_school_data',
        'can_distinguish_letters_randomly',
        'reads_short_words',
        'reads_short_sentences',
        'memorizes_quran_surahs',
        'memorizes_quran_from',
        'memorizes_quran_to',
        'additional_educational_notes',
        'sibling_order',
        'expense_registration_fee_deducted',
        'is_register_fee_accepted',
        'region',

    ];
    protected $casts = ['behavioral_data' => 'array'];
    protected $with = ['branch', 'state.governorate', 'parent'];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function parent()
    {
        return $this->hasOne(StudentParent::class);
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
                return asset('uploads/student/' . $value);
            }
        }
    }

    public function getApplicationFormAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/application_form/' . $value);
            }
        }
    }

    public function getRegisterFeeImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/register_fee_image/' . $value);
            }
        }
    }
    public function getParentIdentityCardImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/parent_identity_card_image/' . $value);
            }
        }
    }

    public function getStudentResidenceCardImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/student_residence_card_image/' . $value);
            }
        }
    }
    public function getStudentPassportImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/student_passport_image/' . $value);
            }
        }
    }
    public function getStudentBirthCertificateImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/student_birth_certificate_image/' . $value);
            }
        }
    }
    public function getStudentHealthCardImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/student_health_card_image/' . $value);
            }
        }
    }
    public function getHomeMapImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/home_map_image/' . $value);
            }
        }
    }

    //Relations
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function class()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function studentExpenses()
    {
        return $this->hasMany(StudentExpense::class);
    }

    //Helper
    protected function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
                // Show All Students
            } else if ($admin->hasRole('School Manager')) {
                // Show Students Related To Current School
                $query->where('school_id', $admin->school_id);
            } else if ($admin->hasRole('Teacher')) {
                $query->whereHas('attendance', function ($query) use ($admin) {
                    $query->whereHas('session', function ($query) use ($admin) {
                        $query->where('teacher_id', $admin->teacherProfile->id);
                    });
                });
            }
        }
    }
    protected function scopeAvailableAll($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
                // Show All Students
            } else if ($admin->hasAnyRole(['School Manager', 'Financial Director', 'Sales Employee'])) {
                // Show Students Related To Current School
                $query->where('school_id', $admin->school_id);
            } else if ($admin->hasRole('Teacher')) {
                $query->where('school_id', $admin->school_id);
            }
        }
    }

    public function canLogin()
    {
        // Student can login if they have uploaded application form
        if (!is_null($this->application_form)) {
            return ['status' => true, 'message' => 'Student can login successfully'];
        } else {
            return ['status' => false, 'message' => 'Application form is required'];
        }
    }
}
