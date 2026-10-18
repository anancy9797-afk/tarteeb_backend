<?php

namespace Modules\Expense\Service;

use Modules\Expense\App\Models\Expense;
use Modules\Expense\App\Models\ExceptionDetail;
use Modules\Expense\App\Models\ExceptionInstallment;


class ExpenseService
{
    function findAll($data = [], $relations = [])
    {
        $expenses = Expense::query()->with($relations)->available()->latest();
        return getCaseCollection($expenses, $data);
    }
    function findExpenses($data = [], $relations = [])
    {
        $expenses = Expense::query()->with($relations)->where('school_id', $data['school_id'])->latest();
        return getCaseCollection($expenses, $data);
    }
    function findById($id)
    {
        return Expense::findOrFail($id);
    }

    function findBy($key, $value)
    {
        return Expense::where($key, $value)->get();
    }

    function create($data)
    {
        $expense = Expense::create($data);
        if (isset($data['details']) && !empty($data['details'])) {
            $expense->details()->createMany($data['details']);
        }
        if (isset($data['installments']) && !empty($data['installments'])) {
            $expense->installments()->createMany($data['installments']);
        }
        return $expense->load('details', 'installments');
    }

    function update($expense, $data)
    {
        $expense->update($data);
        if (isset($data['details'])) {
            $expense->details()->delete();
            if (!empty($data['details'])) {
                $expense->details()->createMany($data['details']);
            }
        }
        if (isset($data['installments'])) {
            $expense->installments()->delete();
            if (!empty($data['installments'])) {
                $expense->installments()->createMany($data['installments']);
            }
        }
        return $expense->load(['gradeCategory', 'grade', 'exceptions', 'details', 'installments', 'exceptionDetails', 'exceptionInstallments']);
    }

    function delete($expense)
    {
        $expense->delete();
    }

    function findExceptions($expense)
    {
        $exceptions = $expense->exceptions()->get();

        // Load exception details and installments for each student
        foreach ($exceptions as $exception) {
            $exception->exception_details = ExceptionDetail::where('expense_id', $expense->id)
                ->where('student_id', $exception->id)
                ->get();
            $exception->exception_installments = ExceptionInstallment::where('expense_id', $expense->id)
                ->where('student_id', $exception->id)
                ->get();
        }

        return $exceptions;
    }

    function saveExceptions($expense, $data)
    {
        $expense->exceptions()->attach($data['student_ids'], [
            'exception_price' => $data['exception_price'],
            'notes' => $data['notes'] ?? null
        ]);

        // Save exception details for each student
        if (isset($data['details']) && !empty($data['details'])) {
            foreach ($data['student_ids'] as $studentId) {
                foreach ($data['details'] as $detail) {
                    ExceptionDetail::create([
                        'expense_id' => $expense->id,
                        'student_id' => $studentId,
                        'name' => $detail['name'] ?? null,
                        'price' => $detail['price'],
                    ]);
                }
            }
        }

        // Save exception installments for each student
        if (isset($data['installments']) && !empty($data['installments'])) {
            foreach ($data['student_ids'] as $studentId) {
                foreach ($data['installments'] as $installment) {
                    ExceptionInstallment::create([
                        'expense_id' => $expense->id,
                        'student_id' => $studentId,
                        'name' => $installment['name'] ?? null,
                        'price' => $installment['price'],
                        'is_optional' => $installment['is_optional'] ?? 0,
                    ]);
                }
            }
        }

        return $expense->load('exceptions', 'exceptionDetails', 'exceptionInstallments');
    }

    function updateExceptions($expense, $data)
    {
        foreach ($data['student_ids'] as $studentId) {
            // Update pivot table
            $expense->exceptions()->updateExistingPivot(
                $studentId,
                [
                    'exception_price' => $data['exception_price'],
                    'notes' => $data['notes'] ?? null
                ]
            );

            // Update exception details - delete existing and create new
            if (isset($data['details'])) {
                ExceptionDetail::where('expense_id', $expense->id)
                    ->where('student_id', $studentId)
                    ->delete();

                if (!empty($data['details'])) {
                    foreach ($data['details'] as $detail) {
                        ExceptionDetail::create([
                            'expense_id' => $expense->id,
                            'student_id' => $studentId,
                            'name' => $detail['name'] ?? null,
                            'price' => $detail['price'],
                        ]);
                    }
                }
            }

            // Update exception installments - delete existing and create new
            if (isset($data['installments'])) {
                ExceptionInstallment::where('expense_id', $expense->id)
                    ->where('student_id', $studentId)
                    ->delete();

                if (!empty($data['installments'])) {
                    foreach ($data['installments'] as $installment) {
                        ExceptionInstallment::create([
                            'expense_id' => $expense->id,
                            'student_id' => $studentId,
                            'name' => $installment['name'] ?? null,
                            'price' => $installment['price'],
                            'is_optional' => $installment['is_optional'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return $expense->load('exceptions', 'exceptionDetails', 'exceptionInstallments');
    }

    function findStudentExpenses($student)
    {
        return $student->expenses()->get();
    }

    function deleteExceptions($expense, $studentIds)
    {
        foreach ($studentIds as $studentId) {
            // Delete exception details
            ExceptionDetail::where('expense_id', $expense->id)
                ->where('student_id', $studentId)
                ->delete();

            // Delete exception installments
            ExceptionInstallment::where('expense_id', $expense->id)
                ->where('student_id', $studentId)
                ->delete();

            // Detach from pivot table
            $expense->exceptions()->detach($studentId);
        }

        return true;
    }
}
