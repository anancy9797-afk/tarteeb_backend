<?php

namespace Modules\Country\App\Models;

use Modules\Country\App\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Modules\Country\App\Models\Governorate;
use Modules\Country\Database\factories\StateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'governorate_id'];

    public function region()
    {
        return $this->hasMany(Region::class);
    }
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
