<?php

namespace Modules\Expense\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Expense\App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseInstallment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['expense_id', 'name', 'price', 'is_optional'];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
