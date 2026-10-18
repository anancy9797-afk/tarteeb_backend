<?php

namespace Modules\Expense\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Expense\App\Models\Expense;
use Modules\Student\App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExceptionDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['expense_id', 'student_id', 'name', 'price'];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
