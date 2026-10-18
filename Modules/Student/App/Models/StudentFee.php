<?php

namespace Modules\Student\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Student\App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Student\Database\factories\StudentFeeFactory;

class StudentFee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['student_id', 'payment_method', 'status', 'receipt', 'reason'];


    public function getReceiptAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/student/receipt/' . $value);
            }
        }
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
