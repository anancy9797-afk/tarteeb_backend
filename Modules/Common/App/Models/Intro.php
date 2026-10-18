<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Intro extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['title_ar', 'title_en', 'description_ar', 'description_en', 'image', 'section', 'parent_id'];

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
                return asset('uploads/intro/' . $value);
            }
        }
    }

    //Relations

    public function details(){
        return $this->hasMany(Intro::class, 'parent_id');
    }

    public function parent(){
        return $this->belongsTo(Intro::class, 'parent_id');
    }
}
