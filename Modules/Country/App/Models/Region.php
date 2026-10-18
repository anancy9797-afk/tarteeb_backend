<?php

namespace Modules\Country\App\Models;

use Modules\Country\App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Modules\Country\Database\factories\RegionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'state_id'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
