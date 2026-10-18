<?php

namespace Modules\Grade\App\Models;

use Modules\Grade\App\Models\Grade;
use Modules\School\App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'school_id'];
    protected $hidden = ['created_at', 'updated_at'];
    //Relations
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    //Helper
    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            }
            if ($admin->hasRole('School Manager')) {
                return $query->where('school_id', $admin->school_id);
            }
        }
    }
}
