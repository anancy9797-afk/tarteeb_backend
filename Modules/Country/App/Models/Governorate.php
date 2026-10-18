<?php

namespace Modules\Country\App\Models;

use Modules\Country\App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Country\Database\factories\GovernorateFactory;

class Governorate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [ 'name' ];

    public function state()
    {
        return $this->hasMany(State::class);
    }
}
