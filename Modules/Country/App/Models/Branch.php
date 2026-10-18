<?php

namespace Modules\Country\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Country\Database\factories\BranchFactory;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
protected $fillable = [
        'name',
        'school_id',
    ];    
    protected static function newFactory(): BranchFactory
    {
        //return BranchFactory::new();
    }
}
