<?php

namespace Modules\Expense\App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Expense\App\Models\Expense;
use Modules\Student\App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentExpense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['student_id', 'expense_id', 'date', 'status', 'receipt', 'rejected_reason', 'payment_method', 'amount', 'amount_paid', 'payment_status', 'is_registration_fee'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('StudentExpense')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function getReceiptAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/expense/receipt/' . $value);
            }
        }
    }
    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            }
            if ($admin->hasAnyRole(['School Manager', 'Financial Director', 'Sales Employee'])) {
                return $query->whereHas('expense', function ($query) use ($admin) {
                    $query->where('school_id', $admin->school_id);
                });
            }
        }
    }
}
