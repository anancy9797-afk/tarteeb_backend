<?php

namespace Modules\Student\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentParent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['parent_name', 'parent_nationality', 'parent_identity_number', 'parent_job', 'parent_job_address', 'parent_education_level', 'mother_name', 'mother_nationality', 'mother_identity_number', 'mother_job', 'mother_job_address', 'mother_education_level', 'mother_phone', 'parents_status', 'relative_name', 'relative_relation', 'relative_phone'];
    protected $table = 'student_parent';
    protected $primaryKey = 'student_id';
    public $incrementing = false;
    protected $keyType = 'int';
}
