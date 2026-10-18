<?php

namespace Modules\School\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\School\Database\factories\SchoolSettingFactory;

class SchoolSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['school_id', 'ultramsg_token', 'ultramsg_instance_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

}
