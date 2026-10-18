<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Salary\App\Models\Salary;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SalariesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                Salary::create([
                    'created_by' => auth('user')->id(),
                    'school_id' => auth('user')->user()->school_id,
                    'user_id' => $row['employee_id'],
                    'salary' => $row['salary'],
                    'month' => $row['month'],
                    'year' => $row['year'],
                    'deduction' => $row['deduction'] ?? 0,
                    'deduction_reason' => $row['deduction_reason'] ?? null,
                    'bonus' => $row['bonus'] ?? 0,
                    'bonus_reason' => $row['bonus_reason'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Get all failures
     */
    public function getFailures(): array
    {
        $failures = [];
        foreach ($this->failures() as $failure) {
            $rowKey = "row_{$failure->row()}";
            if (!isset($failures[$rowKey])) {
                $failures[$rowKey] = [];
            }
            foreach ($failure->errors() as $error) {
                $failures[$rowKey][] = $error;
            }
        }

        return $failures;
    }
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:users,id'],
            'salary' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'max:65535'],
            'deduction' => ['nullable', 'sometimes', 'numeric', 'min:0'],
            'deduction_reason' => ['nullable', 'sometimes', 'string', 'max:255'],
            'bonus' => ['nullable', 'sometimes', 'numeric', 'min:0'],
            'bonus_reason' => ['nullable', 'sometimes', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_id' => 'Employee ID',
            'salary' => 'Salary',
            'month' => 'Month',
            'year' => 'Year',
            'deduction' => 'Deduction',
            'deduction_reason' => 'Deduction Reason',
            'bonus' => 'Bonus',
            'bonus_reason' => 'Bonus Reason',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required',
            'employee_id.exists' => 'Employee ID is not valid',
            'salary.required' => 'Salary is required',
            'salary.numeric' => 'Salary must be a number',
            'salary.min' => 'Salary must be greater than 0',
            'month.required' => 'Month is required',
            'month.integer' => 'Month must be an integer',
            'month.min' => 'Month must be greater than 0',
            'month.max' => 'Month must be less than 13',
            'year.required' => 'Year is required',
            'year.integer' => 'Year must be an integer',
            'year.max' => 'Year must be less than 65536',
            'deduction.numeric' => 'Deduction must be a number',
            'deduction.min' => 'Deduction must be greater than 0',
            'deduction_reason.string' => 'Deduction Reason must be a string',
            'deduction_reason.max' => 'Deduction Reason must be less than 256 characters',
            'bonus.numeric' => 'Bonus must be a number',
            'bonus.min' => 'Bonus must be greater than 0',
            'bonus_reason.string' => 'Bonus Reason must be a string',
            'bonus_reason.max' => 'Bonus Reason must be less than 256 characters',
        ];
    }
}
