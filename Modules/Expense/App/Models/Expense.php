<?php

namespace Modules\Expense\App\Models;

use Modules\User\App\Models\User;
use Spatie\Activitylog\LogOptions;
use Modules\Grade\App\Models\Grade;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Modules\Student\App\Models\Student;
use Modules\Grade\App\Models\GradeCategory;
use Modules\Expense\App\Models\ExpenseDetail;
use Modules\Expense\App\Models\ExceptionDetail;
use Modules\Expense\App\Models\StudentExpense;
use Modules\Expense\App\Models\ExceptionInstallment;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'school_id', 'grade_category_id', 'grade_id', 'price', 'amount', 'amount_paid', 'payment_status'];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Expense')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    //Relation

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function gradeCategory()
    {
        return $this->belongsTo(GradeCategory::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function exceptions()
    {
        return $this->belongsToMany(Student::class, 'expense_student_exceptions')
            ->withPivot('exception_price', 'notes')
            ->withTimestamps();
    }

    public function requests()
    {
        return $this->hasMany(StudentExpense::class);
    }
    public function details()
    {
        return $this->hasMany(ExpenseDetail::class);
    }

    public function installments()
    {
        return $this->hasMany(ExpenseInstallment::class);
    }

    public function exceptionDetails()
    {
        return $this->hasMany(ExceptionDetail::class);
    }

    public function exceptionInstallments()
    {
        return $this->hasMany(ExceptionInstallment::class);
    }

    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            }
            if ($admin->hasAnyRole(['School Manager', 'Financial Director'])) {
                return $query->where('school_id', $admin->school_id);
            }
        }
    }
}
